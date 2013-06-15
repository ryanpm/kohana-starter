<?php

class Controller_Rest extends Lib_Controller{

	function action_index(){

		$class =  $class = $this->request->param('object');
		$action = $this->request->param('verb');

		if( is_numeric($action) ){
			$action = '';
		}
		if($action==''){
			$action = 'index';
		}

		$class = "Rest_".ucfirst($class);
		$rest = new $class();

		$rest->$action();

	}

}

?>