<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Lib_Controller {

	public function getTemplate(){
		return new View('_login');
	}

	/**
	 * Controller_Login::before()
	 *
	 * @return
	 */
	public function before()
	{
		parent::before();

		if( $this->request->query('logout') !== NULL ){
			Session::instance()->destroy();
			Cookie::delete('_encrypted');
			$this->redirect('login');
			exit;
		}

		if( Cookie::get('_encrypted') != NULL ){

			$username_password = Encrypt::instance()->decode(Cookie::get('_encrypted'));
			list($username,$password) = explode(':',$username_password);
			if( Lib_User::authenticate($username,$password) ){
				$this->redirect('home');
			}

		}

		View::set_global('remembered',false);
		View::set_global('username',Cookie::get('username'));
		if( Cookie::get('remembered') != NULL ){
			View::set_global('remembered',true);
		}

		$this->template->title = "Login Page - ".$this->template->title;

	}

	/**
	 * Controller_Login::action_index()
	 *
	 * @return
	 */
	public function action_index()
	{
		$this->view('common/blank');

	}

	/**
	 * Controller_Login::action_auth()
	 *
	 * @return
	 */
	public function action_auth(){

 
		$username = $this->request->post('username');
		$password = $this->request->post('password');
		$remember = $this->request->post('remember');
 
		if( Lib_User::authenticate($username,$password) ){

			$response['success'] = true;
			$response['redirect'] = URL::site('home/');

			if($remember!=NULL){
				Cookie::set('remembered', 1, time()+60*60*24*30);
				Cookie::set('username', $username, time()+60*60*24*30);
				Cookie::set('_encrypted', Encrypt::instance()->encode($username.':'.$password), time()+60*60*24*30);
			}else{
				Cookie::delete('username');
				Cookie::delete('remembered');
				Cookie::delete('_encrypted');
			}

		}else{

			$response['success'] = '';
			$response['message'] = 'Invalid account.';

		}

		if( $this->request->query('reload') !== NULL  ){


			$this->redirect('home/');


		}else{
			echo json_encode($response);
			exit;
		}
		exit;


	}


} // End Welcome
