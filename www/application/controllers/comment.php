<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default Kohana controller. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Comment_Controller extends Template_Controller {

	// Disable this controller when Kohana is set to production mode.
	// See http://docs.kohanaphp.com/installation/deployment for more details.
	const ALLOW_PRODUCTION = FALSE;

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
    public function obfuscateUserName(){
        echo json_encode(array("username"=>base64_encode($this->input->post("username","unknown"))));
        exit;
    }
    public function create(){
        $errors = 0;
        $sessToken = $this->session->get("token");
        // reset token       
        if($sessToken == $this->input->post("token")){
            $userName = base64_decode($this->input->post("author"));
            $commentObj = new Comment_Model();
            $commentObj->author = $userName;
            $commentObj->body = $this->input->post("body");
            $commentObj->timeLineRef = $this->input->post("time_line_ref");
            if(!$commentObj->create()){
                $errors++;
            }
        }else{
            $errors++;
        }
        if($errors >0){
            kohana::log("debug","Invalid token detected in comment submission");
            $this->failForComment();
        }else{
            $this->thankForComment();
        }
    }

    public function thankForComment(){        
		$this->template->title = "Chris";//var_dump($this->pagination);
        $siteDesc = new Post_Model();        
        $this->template->description = $siteDesc->getSiteDescription();
        $this->template->content = new View('user_message');
        $this->template->content->title = "Thanks for commenting!";
        $this->template->content->body = "I consider your feedback to be extremely important, many thanks for reading.";
    }
    public function failForComment(){
		$this->template->title = "Chris";//var_dump($this->pagination);
        $siteDesc = new Post_Model();
        $this->template->description = $siteDesc->getSiteDescription();
        $this->template->content = new View('user_message');
        $this->template->content->title = "Whoops, something went wrong :(";
        $this->template->content->body = "It may not be your fault, but a problem was detected with your submission. Please try again later.";
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
