<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Validator {

    public static function checkIfExist($username)
    {
        // Check if the username already exists in the database
        $user = ORM::factory('User')->where('username','=',$username)->find();
        return (bool)$user->loaded();
    }

    public static function checkIfEmailExist($email,$user_id=0)
    {
        // Check if the username already exists in the database
        $user = ORM::factory('User')->where('username','=',$email)->find();
        $pass  =  (bool)$user->loaded();
        if( $user_id != 0  ){
            if( $user->user_id == $user_id ){
                $pass = false;
            }
        }
        return $pass;
    }


    /**
     * This is use by validation in registration
     * @param type $username
     * @return PASSED - true or false
     */
    public static function uniqueUsername($username)
    {
        return !self::checkIfExist($username);
    }

    public static function uniqueEmailAddress($email)
    {
        $user_id = 0;
        if( !Lib_App::user()->isGuest() ){
            $user_id = Lib_App::user()->getID();
        }
        return !self::checkIfEmailExist($email,$user_id);
    }

    public static function validUsername($username)
    {
        $pass = preg_match('/^[a-z\_][a-z0-9\_]+$/i',$username);
        return (bool)$pass;
    }


}