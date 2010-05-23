<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default controller for my website
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 * @version	   2.3.4
 * See http://docs.kohanaphp.com/installation/deployment for more details.
 */
class Welcome_Controller extends Template_Controller {

	// Set the name of the (master) template to use
	public $template = 'template';
    public $siteDesc = "";
	public $pagination = "";	
	public $links = array();
	protected $db;	
	protected $siteObj;

	public function __construct(){		
		parent::__construct(); // This must be included	
		$env = Kohana::config("config.environment");	
		$this->db = new Database($env);
		$this->itemsPerPage = Kohana::config("config.number_of_items");
		$this->pagination = new Pagination(array(
		    'base_url'    => '/welcome/page/', // base_url will default to current uri
		    'uri_segment'    => 'page', // pass a string as uri_segment to trigger former 'label' functionality
		    'total_items'    => $this->db->count_records("kh_timeline"), // use db count query here of course
		    'items_per_page' => $this->itemsPerPage, 
		    'style'          => 'classic' // pick one from: classic (default), digg, extended, punbb, or add your own!		
		));
        $this->siteObj = new Post_Model();
        $this->siteDesc = $this->siteObj->getSiteDescription();
	}
	
	public function index()
	{
		/*
		 * Useful snippets
		 * 	$this->template->content->test .= Kohana::debug($xml);
		 *  kohana::log("debug",Kohana::debug($post));	
		 */
	
		// Load template
		$this->template->content = new View('welcome_content');		
				
		$this->template->title = 'Chris Hardcastle ('.Kohana::config("config.environment").')';
        $this->template->description = $this->siteObj->getSiteDescription();
		$this->template->content->hotlinks = $this->pagination->render("digg");
		// Post timeline data
		$this->template->content->posts = $this->db->select("*")
		->from("kh_timeline")		
		->limit($this->itemsPerPage)
		->orderby("id","asc")
		->get()
		->result_array(true);
		
		/* Just this once, load next posts, required for JS scrolling idea */
		$this->template->content->nextPosts = $this->db->select("*")
		->from("kh_timeline")		
		->limit($this->itemsPerPage,($this->itemsPerPage*2))
		->orderby("id","asc")
		->get()
		->result_array(true);
		//
		
		
		
	}
	/*
	 * Provide data to static pages
	 * */
	public function page($pageId,$ajax=false){
		if($ajax){
			echo $this->pageAsJson($pageId);
			exit;			
		}else{
			$this->template->content = new View('welcome_content');
			
			$this->template->content->hotlinks = $this->pagination->render();
			$end = $this->getPageSqlEnd($pageId);
			$this->template->content->posts = $this->db->select("*")
			->from("kh_timeline")		
			->limit($this->itemsPerPage,$end)
			->orderby("id","asc")
			->get()
			->result_array(true);
			//

			$this->template->title = "Chris";//var_dump($this->pagination);
            $this->template->description = $this->siteDesc;
		}
	}
	private function getPageSqlEnd($pageId){
		return ($pageId * $this->itemsPerPage)-$this->itemsPerPage;		
	}
	/*
	 * Provide data as JSON
	 * */
	public function pageAsJson($pageId){				
		$end = $this->getPageSqlEnd($pageId);
		$data = $this->db->select("*")
		->from("kh_timeline")		
		->limit($this->itemsPerPage,$end)
		->orderby("id","asc")
		->get()
		->result_array(true);		
		$x = 0;
		$returned = array();
		kohana::log("debug","load page as JSON");
		foreach($data as $key => $value){
			$x++;		
			$contents = unserialize($value->content);
			$html = "";
			if(is_array($contents)){
				foreach($contents as $str){
					$html .= $str;
				}
			}			
			$returned[$key] = array(
				"index"=>"{$x}",
				"body"=>$html,
				"id"=>$value->id,	
				"title"=>$value->date);			 			
		}
		echo json_encode($returned);		
		exit;		
	}	

	public function saveNewPosts(){		
		if (PHP_SAPI === 'cgi-fcgi' || PHP_SAPI === 'cli'){
			$postObj = new Post_Model;		
			$postObj->searchForNewPosts();
			kohana::log("debug","The function 'saveNewPosts' has run.");
		}else{
			throw new Kohana_User_Exception('Cannot call over the web', 'This is a function that can only be called via the command line.');
		}		
	}

	public function digestNewPosts(){
		if (PHP_SAPI === 'cgi-fcgi' || PHP_SAPI === 'cli'){
			$postObj = new Post_Model;
			$postObj->digestNewPosts();
			kohana::log("debug","The system has digested the posts tabel. HTML is now refreshed.");
		}else{
			throw new Kohana_User_Exception('Cannot call over the web', 'This is a function that can only be called via the command line.');
		}
	}
	/*
	 * Show about page
	*/
	public function about()
	{		
		$content = new View('about');
		$this->template->title = "About";
		$this->template->description = $this->siteDesc;
		$this->template->content = $content->render();	
	}

	public function __call($method, $arguments)
	{
		// Disable auto-rendering
		$this->auto_render = FALSE;

		// By defining a __call method, all pages routed to this controller
		// that result in 404 errors will be handled by this method, instead of
		// being displayed as "Page Not Found" errors.
		echo 'This text is generated by __call. If you expected the index page, you need to use: welcome/index/'.substr(Router::$current_uri, 8);
	}


} // End Welcome Controller
