<?php defined('SYSPATH') or die('No direct script access.');
 
class Tumblr_Model extends App_Model {
	 	
 	public $title = "";
 	public $teaser = "";
 	
	public function __construct()
	{
		// load database library into $this->db (can be omitted if not required)
		parent::__construct();
		$this->title = "";
		$this->teaser = "";
	}
	public function captureFeed($feedUrl,$mostRecentPost){
		$numberOfNewPosts = 0;		 
		$myTummy = file_get_contents($feedUrl);
		$xml = new SimpleXMLElement($myTummy);	
		foreach($xml->results->posts->post as $post){			
			$created = strtotime($post["date"]);
			kohana::log("debug","Tumblr post detected of type ".$post["type"]." on:".$post["date"] );
			// Unless override is on, only include if new			
			if($created > $mostRecentPost || !$mostRecentPost){
				// Yay, a new post, save it!
				$title = $post["type"];				
				if($title == "photo"){
					foreach($post->{"photo-url"} as $photo){
						if(strpos($photo,"500") !== false){
							$post["large"] = $photo;						
						}else if(strpos($photo,"250") !== false){
							$post["small"] = $photo;							
						}else if(strpos($photo,"75") !== false){
							$post["tiny"] = $photo;							
						}					
					}
				}
				$content = serialize((array) $post);
								
				if($content && $title){
					$query = $this->db->insert("kh_posts",array(
						"title"=>"{$title}",
						"content"=>"{$content}",
						"created_dt"=>"{$created}",										
						"modified_dt"=>time(),
						"type" => "tumblr"
					));
					kohana::log("debug","Tumblr last query was ".$this->db->last_query());
					// Now make a teaser
					$breakStart = strpos($content,"<!-- more -->");
					$breakEnd = strlen($content);
					$insertId = $query->insert_id();
					if($breakStart !== false){					
						$teaser = substr($content,$breakStart,$breakEnd);
						$this->db->from('kh_posts')
						->set(array("teaser" => "{$teaser}"))
						->where(array('post_id' => $insertId))
						->update();
					}
					$numberOfNewPosts++;
					kohana::log("debug","Found a new Tumblr post ... saved");
				}
			}
		}
		return $numberOfNewPosts;		
	}	
}