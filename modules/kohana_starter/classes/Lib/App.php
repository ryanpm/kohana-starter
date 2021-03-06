<?php defined('SYSPATH') or die('No direct script access.');

class Lib_App {

	static $cache;
	public static function user(){
		if( isset(self::$cache['user']) ) return self::$cache['user'];
		self::$cache['user'] =  Lib_AppUser::getInstance();
		return self::$cache['user'];
	}


	// configuration comming  from config/folder
	public static function config($file='main')
	{
        if( isset(static::$cache['config'][$file]) )return static::$cache['config'][$file];
        $cnf = Kohana::$config->load($file);
        static::$cache['config'][$file] = $cnf;
        return $cnf;
	}

	// settings coming from database but can also overwrite by config
	public static function setting($key)
	{
		$value = self::config()->get($key);
		return ($value!=null)?$value:Lib_Setting::get($key);
	}



}