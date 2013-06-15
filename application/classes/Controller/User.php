<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Lib_Controller{

	function action_index(){

		$this->action_list();

	}

	function action_list(){

		$this->add_navigation_path(array('User','user/'));

		$data['grid_id'] = "user_list";
		$data['header'] = json_encode( array('ID','First Name','Last Name','User Name','') );
		$data['caption'] = '';

		$colModel[] =array('name'=>'user_id','index'=>'user_id','width'=>45,'align'=>'center');
		$colModel[] =array('name'=>'firstname','index'=>'firstname','width'=>150,'editable'=>true);
		$colModel[] =array('name'=>'lastname','index'=>'lastname','width'=>150,'editable'=>true);
		$colModel[] =array('name'=>'username','index'=>'username','width'=>100,'editable'=>true);
		$colModel[] =array('name'=>'controls','index'=>'controls','width'=>53);

		$data['colModel'] = json_encode( $colModel );
		$data['url'] = Url::site('rest/user/get');
		$data['update_url'] = Url::site('rest/user/update');

		$data['navGrid']['add'] = true;
		$data['navGrid']['edit'] = true;
		$data['navGrid']['search'] = true;
		$data['navGrid']['del'] = true;
		$data['navGrid']['view'] = true;

		$this->view('common/list',$data);

	}

}

?>