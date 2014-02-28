<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Base extends Kohana_Controller_Template {

    //put your code here
    public $template = 'layout/main';
    public $layout_column = '1';
    public static $data;
    public $breadcrumbs;
    public $public_actions = array();

    public function __construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->checkPermission();
    }

    protected function checkPermission() {
        // all actions for the controller are secure except for declared public actions
        if (in_array('*', $this->public_actions)) {
        }elseif (!in_array($this->request->action(), $this->public_actions)) {
            if ( !Lib_App::user()->isGuest() ) {
                $this->permissionSuccess();
            } else {
                $this->permissionFailed();
            }
        }
    }

    public function permissionSuccess()
    {
    }

    public function permissionFailed()
    {
        $this->redirect('login?a='.strtolower($this->request->controller().'/'.$this->request->action()));
    }

    public function view($view = '', $data = array()) {

        $this->template->body = Lib_View::factory($view, $data)->render();

    }


    // override
    public function before() {

        if ($this->auto_render === TRUE) {
            // Load the template
            $this->template = Lib_View::factory($this->template);
        }

        $this->setTemplateData('body', '');
        if (!Lib_App::user()->isGuest()) {
            $this->setTemplateData('username',  Lib_App::user()->username() );
        }

        $this->addBreadcrumb(array('Home', 'home'));
        $this->setTemplateData('title', 'Idea Bank');
        $this->setTemplateData('is_guest', Lib_App::user()->isGuest());
        $this->setTemplateData('welcome_name', Lib_App::user()->welcomeName() );
        $this->setTemplateData('urlbase', URL::base());
        $this->setTemplateData('urlsite', URL::site());
        $this->setTemplateData('category_id', 0);
        $this->setTemplateData('urlaction', $this->request->action());
        $this->setTemplateData('url_route', strtolower($this->request->controller().'/'.$this->request->action()));
        $this->setTemplateData('urlcontroller', $this->request->action());

    }

    public function setTemplateData($var, $val) {
        if ( is_object($this->template) and $this->auto_render) {
            $this->template->$var = $val;
        }
        View::set_global($var, $val);
    }

    public function getTemplateData($var) {
        if ($this->template != '') {
            return (isset($this->template->$var)) ? $this->template->$var : null;
        }
        return null;
    }

    public function setActiveMenu($id) {
        View::set_global('active_menu', $id);
    }

    public function addBreadcrumb($path) {
        if (isset($this->breadcrumbs)) {
            $this->breadcrumbs[] = $path;
        } else {
            $this->breadcrumbs = array($path);
        }
        View::set_global('breadcrumbs', $this->breadcrumbs);
    }

}

?>
