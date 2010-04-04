<?php defined('SYSPATH') or die('No direct script access.');
 
class Post_Model extends Model {
	 	
 	private $urls = array();
 	protected $db; // database instance
 	public $posts = array();
 	
	public function __construct()
	{
		// load database library into $this->db (can be omitted if not required)
		parent::__construct();
		$this->db = new Database('local');
		$this->urls = array(
			"tweets" => 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20twitter.user.timeline%20where%20id%3D%22hardcastle%22&format=xml&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys',
			"tumblr" => "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20tumblr.posts%20where%20username%3D'hardcastle'&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys",
			"github" => array("jquery"=>"http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20github.repo.commits%20where%20id%3D'jquery'%20and%20repo%3D'jquery'&format=xml&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys")
		);
	}
	
	/*
	 * Look for posts newer than those we already have 
	 */	
	public function searchForNewPosts(){
		/* */
		kohana::log("debug","Request for new posts detected");
		//$this->db = new Database('local');
		//Get the most recent post
		$this->db->query("TRUNCATE TABLE kh_posts");
		$mostRecentPost = $this->db->select("*")
		->from("kh_posts")		
		->limit(1)
		->orderby("created_dt","desc")
		->get()
		->result_array(true);				
		
		$mostRecentPost = (isset($mostRecentPost[0]))?$mostRecentPost[0]->created_dt:false;
				
		$info = "Looking for posts newer than ".date("d-m-y",$mostRecentPost);
		// return nothing, just capture feeds
		// Parse an external atom feed
		
		$obj = new Tumblr_Model;		
		//$tumNo = $obj->captureFeed($this->urls["tumblr"],$mostRecentPost);
		$info .= "<p>".(isset($tumNo)?$tumNo:"Didn't request any")." new Tumblr items</p>";
		
		$obj = new Tweet_Model;
		$tweNo = $obj->captureFeed($this->urls["tweets"],$mostRecentPost);
		$info .= "<p>".(isset($tweNo)?$tweNo:"Didn't request any")." new Tweet items</p>";
		
		$obj = new Git_Model;
		//$gitNo = $obj->captureFeed($this->urls["github"]["jquery"],$mostRecentPost);
		$info .= "<p>".(isset($gitNo)?$gitNo:"Didn't request any")." new Git items</p>";
		echo $info;
		/*				
		$jQueryfeed = file_get_contents($feedUrl);		
		$xml = new SimpleXMLElement($jQueryfeed);		
		// jQuery github activity	
		foreach($xml->results->commits as $post){			
			foreach($post as $commit){				
				$created = strtotime($commit->{"committed-date"});
				// Unless override is on, only include if new				
				if($created > $mostRecentPost || !$mostRecentPost){					
					// Yay, a new post, save it!
					$content = serialize((array) $commit);				
					$this->db->insert("kh_posts",array(
						"title"=>"jQuery",
						"content"=>"{$content}",
						"created_dt"=>"{$created}",					
						"modified_dt"=>time(),
						"type" => "gitcommit"
					));
					$numberOfNewPosts++;
					kohana::log("debug",Kohana::debug("Found a new jquery commit ... saved"));
				}
			}						
		}
		/* Tweets
		$xml = new SimpleXMLElement($myTweets);		
		foreach($xml->results->entry as $post){			
			$created = strtotime($post->published);
			// Unless override is on, only include if new	
			if($created > $mostRecentPost || !$mostRecentPost){
				// Yay, a new post, save it!				
				$attributes = array();
				Kohana::debug("Attributes data:".$post->{"@attributes"});				
				foreach($post->{"@attributes"} as $key => $value){
					kohana::log("debug",Kohana::debug("Attributes serial: key:".$key." val:".$value));					
					$attributes[$key] = $value;						
				}				
				$content = serialize($attributes);
				
				$this->db->insert("kh_posts",array(
					"title"=>"tweet",
					"content"=>"{$content}",
					"created_dt"=>"{$created}",					
					"modified_dt"=>time(),
					"type" => "tweet"
				));
				$numberOfNewPosts++;
				kohana::log("debug",Kohana::debug("Found a new Tweet ... saved"));
			}			
		}
		return $numberOfNewPosts;
		/* */		
	}
	
	/*
	 * Remove all records from timeline
	 * create new ones based on post source table 
	 * */
	public function digestNewPosts(){
		$this->posts = array();
		// remove old records
		$this->db->query("TRUNCATE TABLE kh_timeline");
		//$this->db->update("kh_timeline",array("id"=>0));	
		$sql = <<<SQL
			SELECT posts.type,
				from_unixtime(posts.created_dt,'%d-%m-%y') AS dateKey,
				posts.content, 
				posts.* 
				FROM (select * from kh_posts) AS posts 
				INNER JOIN kh_posts AS dates ON posts.created_dt = dates.created_dt 
				GROUP BY posts.content ORDER BY posts.created_dt DESC
SQL;

		$posts = $this->db->query($sql)->result_array(true);		
		foreach($posts as $key => $value){
			$content = $value->content;
			if($value->type == "tumblr"){				
				$contentSerial = unserialize($content);				
				/* */
				if(!$contentSerial){					
					kohana::log("debug","failed to decode");				
				}else{					
					kohana::log("debug","Found json");
					$json = $contentSerial["@attributes"];
					if(is_array($json) && isset($json->type)){
						switch($json["type"]){
							case "photo":
								$obj = new Photo_Model;
								$content = $obj->loadFromLocalSource($json);
								break;						
							case "regular":		
								$obj = new Regular_Model;				
								$content = $obj->loadFromLocalSource($content);
								break;
							case "link":	
								$obj = new Link_Model;					
								$content = $obj->loadFromLocalSource($content);
								break;
							case "video":		
								$obj = new Video_Model;													
								$content = $obj->loadFromLocalSource($content);	
								break;							
						}
					}
				}
			}else if($value->type == "gitcommit"){
				$obj = new Git_Model;
				$obj->repoName = $value->title;
				$obj->dateTime = $value->created_dt;
				$obj->message = $value->content;
				// slight difference in parameter for this object 
				$content = $obj->loadFromLocalSource($obj);			
			}else if($value->type == "tweet"){
				$tweet = explode(":",$value->content);
				$content = (isset($tweet[1]))?$tweet[1]:"";
				//preg_replace("#\[(([a-zA-Z]+://)([a-zA-Z0-9?&%.;:/=+_-]*))\]#e", "'<a href=\"$1\" target=\"_blank\">$3</a>'", $content);			
				preg_replace("/#[a-zA-Z]+/i", "<a href='http://twitter.com/search?q=urlencode($1)' target='_blank'>$1</a>", $content);
				$obj = new Tweet_Model;
				$obj->tweet = $value->content; 
				$obj->author = (isset($tweet[0]))?$tweet[0]:"";
				$obj->tweetWithLinks = $content;
				$obj->pubDateTime = $value->created_dt;				
				// slight difference in parameter for this object 
				$content = $obj->loadFromLocalSource($obj);				
			}				
			// load into result array			
			$key = date("dS M y",$value->created_dt);
			if(!array_key_exists($key,$this->posts)){
				$this->posts[$key] = array($content);				
			}else{
				$this->posts[$key][] = $content;				
			}	
		}	
		// traverse and stick into table
		//kohana::log("debug",print_r($this->posts,true));
		$x=0;
		foreach($this->posts as $key => $value){
			$x++;
			$content = serialize($value);
			$this->db->insert("kh_timeline", array(
				"date" => $key,
				"content" => "{$content}",
				"squence_id" => $x
			));				
		}
	}
	public function getPosts($page){
		$this->db->select("*")
		->from("kh_posts")
		->limit();
	
	}
	public function getDataSourceUrls(){
		return $this->urls;	
	}
	/*
	 * Get part of the data, for fun
	 
	public function getDataSourceDataSchemea($key){		
		$data = file_get_contents($this->urls[$key]);
		return substr($data,0,500);		
	}
	/*
	 * Get part of the data, for fun
	 */
	public function getDataSourceDataSchemea($key){		
		return (Kohana::config("config.pop"))?"Pop is on":"Pop is off";
				
	}	
 
}