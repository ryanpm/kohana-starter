<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User class handles all user information, registration, and login authentication
 * */

class Lib_User{

    /**
     * Lib_User::isLogin()
     *
     * @return
     */
    static function isLogin(){
        if( Session::instance()->get('user_id') !== NULL ){
            return true;
        }
        return false;
    }

    /**
     * Lib_User::setLoginStatus()
     *
     * @return
     */
    static function setLoginStatus($status){
        self::$isLogin = $status;
    }

    /**
     * Lib_User::authenticate()
     * Check database for existing username and password
     * @return
     */
    static function authenticate($username,$password){

        if( trim($username) != '' and trim($password) != '' ){
            $user = ORM::factory('user')->where('username','=',$username)->where('password','=',$password)->find();
            if( $user->loaded() ){

                self::setUserSession(array(
                    'user_id' => $user->user_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'username' => $user->username,
                ));

                Lib_Log::addLog($user->user_id, Lib_Log::TYPE_LOGIN);

                return true;
            }
        }

        return false;
    }

    /**
     * Lib_User::add()
     *
     * @return
     */
    static function add($userdata){

        $user = ORM::factory('user');
        $user->firstname = $userdata['firstname'];
        $user->lastname = $userdata['lastname'];
        $user->username = $userdata['username'];
        $user->password = $userdata['password'];
        $user->registration_date = date('Y-m-d');
        $user->save();
        return $user->pk();



    }

    /**
     * Lib_User::setUserSession()
     *
     * @return
     */
    static function setUserSession($user){

        Session::instance()->set('user_id', $user['user_id'] );
        Session::instance()->set('firstname', $user['firstname'] );
        Session::instance()->set('lastname', $user['lastname'] );
        Session::instance()->set('username', $user['username'] );

    }

    /**
     * Lib_User::checkExistingUsername()
     *
     * @return
     */
    static function checkExistingUsername($username){
        $user = ORM::factory('user')->where('username','=',$username)->find();
        if( $user->loaded() ){
            return true;
        }
        return false;
    }

}