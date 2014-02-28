<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Session {

	static $prefix = 'ideabank_';
    static function get($var){
        return Session::instance()->get(self::$prefix.$var);
    }

    static function set($data,$value=null){
        if( !is_array($data) ){
            $data = array($data=>$value);
        }
        foreach($data as $k => $v){
            $kk = self::$prefix.$k;
            Session::instance()->set($kk, $v );
        }
    }
    static function delete($key){
        Session::instance()->delete(self::$prefix.$key);
    }

}