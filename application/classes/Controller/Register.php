<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Register extends Chope_Template {

	/**
	 * Controller_Register::action_index()
	 *
	 * @return
	 */
	public function action_index()
	{
		$this->template->body = View::factory('common/registration');

	}

	/**
	 * Controller_Register::before()
	 *
	 * @return
	 */
	public function before()
	{
		parent::before();
		$this->template->title = "Registration Page - ".$this->template->title;

	}


	/**
	 * Controller_Register::action_submit()
	 *
	 * @return
	 */
	public function action_submit(){

		$user = array();
		$error = '';
		if( $this->_validate_request($user,$error)  ){

			$user['user_id'] = Lib_User::add($user);
			$response['status'] = 'success';
			$response['redirect'] = URL::site('home/');
			Lib_Log::addLog($user['user_id'], Lib_Log::TYPE_REGISTER);
			Lib_User::setUserSession($user);

		}else{

			$response['status'] = 'failed';
			$response['message'] = $error;

		}
		echo json_encode($response);
		exit;

	}

	/**
	 * Controller_Register::_validate_request()
	 *
	 * @param mixed $user
	 * @param mixed $error
	 * @return
	 */
	function _validate_request(&$user,&$error){

		$isvalid = true;
		$fields = array('firstname','lastname','username','password','confirmpassword');
		foreach($fields as $f){
			$user[$f] = trim($this->request->post($f));
			if( $user[$f] == '' ){
				$isvalid = false;
			}
		}

		if( $isvalid ){
			if( Lib_User::checkExistingUsername( $user['username'] ) ){
				$isvalid = false;
				$error = 'Username already exist.';
			}
		}else{
			$error = 'Please complete all fields.';
		}

		return $isvalid;

	}

} // End Welcome
