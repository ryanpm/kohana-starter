<?php defined('SYSPATH') or die('No direct script access.');

class Lib_AppUser {

    public $model;
    public static function getInstance()
    {
        $user_id = Lib_Session::get('user_id');
        $user = new self();
        if( (int)$user_id > 0 ){
            $user->model = ORM::factory('User',$user_id);
        }
        return $user;
    }

    public function login($username, $password, $remember = null)
    {
        if( trim($username) != '' and trim($password) != '' ){


            $user = ORM::factory('User')
                ->where('username','=',$username)
                ->where('password','=', Lib_Tools::encrypt($password) )
            ->find();
            if( $user->loaded() ){

                if( $user->status == Model_User::STATUS_PENDING ){
                    throw new Exception_Login("Account is not yet verified");
                }elseif( $user->status == Model_User::STATUS_INVALID ){
                    throw new Exception_Login("Account failed the verification process");
                }elseif( $user->status == Model_User::STATUS_VERIFIED ){

                    Lib_Session::set(array(
                        'user_id'   => $user->user_id,
                        ));
                    if($remember!=NULL){
                        Cookie::set('remembered', 1, time()+60*60*24*30);
                        Cookie::set('_encrypted', Encrypt::instance()->encode($username.':'.$password), time()+60*60*24*30);
                    }else{
                        Cookie::delete('user_username');
                        Cookie::delete('remembered');
                        Cookie::delete('_encrypted');
                    }
                    $this->model = $user;
                    return true;
                }
            }

        }
        throw new Exception_Login("Invalid account");
    }

    public function logout()
    {
            Lib_Session::delete('user_id');
    }

    public function getID()
    {
      if($this->model==null) return null;
      return $this->model->user_id;
    }

    public function welcomeName(){
        if(!isset($this->model)) return '';
        return $this->model->fullname();
    }

    public function isGuest(){
        if( $this->model === NULL ) return true;
        return false;
    }

    public function __call($property, $args)
    {
        return call_user_func_array(array($this->model, $property), $args);
    }

}