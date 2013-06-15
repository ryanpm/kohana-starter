<?php defined('SYSPATH') or die('No direct script access.');

header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/**
 * This class will be the main Template for all controller
 **/

class Lib_Controller extends Controller{

    // located at view/_site.php
    public $template;

    /**
     * Chope_Template::__construct()
     *
     * @return
     */
    public function __construct(Request $request, Response $response){
        parent::__construct($request,$response);

    	$this->template = $this->getTemplate();

        $public_controllers = array(
            'Login','Register'
        );

        //All controllers are secure except for login
        if( !in_array($this->request->controller(),$public_controllers) ){
            if( !Lib_User::isLogin() ){
               	$this->redirect('login');
            }else{
             	View::set_global('username','test');
            }
        }


    	$this->add_navigation_path(array('Home','home/'));

    }

	public function getTemplate(){
		return new View('_site');
	}

	public function view($view='',$data=array()){

		if( is_array($view) ){
			$data = $view;
			$view ='';
		}
		if( $view == '' ){
			$view = $this->request->controller().'/'.$this->request->action();
		}else{
			$x = explode('/',$view);
			if($x[0]=='.'){
				$view = $this->request->controller().'/'.$x[1];
			}
		}
		$view = strtolower($view);

		$view = View::factory($view,$data);

		$this->template->body = $view;

		$this->response->body($this->template);

	}

	function setSubmenus($view){
		$this->template->submenus  = View::factory($view);
	}

    public function before()
	{
		parent::before();
        // Default title for all the controllers
    	$this->template->title = "Document Keeper";
    	View::set_global('firstname',Session::instance()->get('firstname'));
    	View::set_global('baseurl',Url::base());
    	View::set_global('siteurl',Url::site());

	}

	public function add_navigation_path($path){
		if( isset($this->template->navigation_path) ){
			$this->template->navigation_path[] = array(' / ');
			$this->template->navigation_path[] = $path;
		}else{
			$this->template->navigation_path = array($path);
		}
	}

	public function sendErrorStatus($msg,$h=''){
		if($h=='')$h='Error message';
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' 500 '.$h);
		exit( $msg );

	}

} // End Welcome
