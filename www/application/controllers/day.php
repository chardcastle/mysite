<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default Kohana controller. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 */
class Day_Controller extends Template_Controller {

	// Set the name of the template to use
	public $template = 'template';
	protected $db;
    public $session = false;
	public function __construct(){
		parent::__construct(); // This must be included
        $env = Kohana::config("config.environment");
		$this->db = new Database($env);
        $this->session = Session::instance();
	}

	public function index()
	{
		/*
		 * Useful snippets
		 * 	$this->template->content->test .= Kohana::debug($xml);
		 *  kohana::log("debug",Kohana::debug($post));
		 */
	}
    
    public function view($postId){
        if($postId>0){
			$env = Kohana::config("config.environment");
            $postObj = new Post_Model();
            $this->template->content = new View('full_width');
            $this->template->description = $postObj->getSiteDescription();
            $this->template->content->paginationLimit = $postObj->totalTimeLineItems;
            // set comment token
            $this->session->delete("token");
            $token = md5(rand(0,20));
            $this->session->set("token",$token);
            $this->template->content->token = $token;
            // Post timeline data
            $post = $postObj->getPost($postId);
			$comments = new Comment_Model($postId);		
		    $commentHtml = "";
			if(count($comments)>0){
				foreach($comments->collection as $key => $comment){
					$html = new View("comment/single");
					$commentObj = new Comment_Model();
					$html->set("comment",$comment);
				    kohana::log("debug",Kohana::debug($comment));
					$commentHtml .= $html->render();
				}
			}	
			$this->template->content->comments = $commentHtml;
            $this->template->content->date = $post[0]["date"];
            $this->template->title = ($env != 'production')?"{$post[0]["date"]} ({$env})":$post[0]["date"];
            
            $this->template->content->id = $post[0]["id"];            
            $next = false;
            $prev = false;
            if($postId > 0){
				$slugs = $postObj->getNextAndPrevUrls($postId,"day");
				//var_dump($slugs).die();
				if($postId > 2){
					$next = $slugs['next'];
				}
				if($postId < ($postObj->totalTimeLineItems)-1){
					$prev = $slugs['prev'];
				}
			}
            $this->template->content->nextUrl = $next;
            $this->template->content->prevUrl = $prev;
            $this->template->content->post = unserialize($post[0]["content"]);
        }

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
