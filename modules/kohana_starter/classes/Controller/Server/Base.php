<?php defined('SYSPATH') or die('No direct script access.');

 /**
 *  This class serves the ajax functionality of javascript for complex purpose
 *  like server validation of data, adding data, deleting data so on..
 */
class Controller_Server_Base extends Controller_Base {

	public $template = '';
	public $auto_render = FALSE;
	public $public_actions = array();

    public function permissionFailed()
    {
         $this->response(array(
            'success' => false,
            'session_expired' => true
        ));
    }

    public function response($view,$data=null) {
        if(!is_array($data)){
            echo json_encode($view);
        }else{
            echo Lib_View::factory($view, $data);
        }
        exit;
    }

}