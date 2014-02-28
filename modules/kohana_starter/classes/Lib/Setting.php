<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Setting{

	public static $data;
	public static function data($reload=false)
	{
		if( self::$data == null or $reload ){
			$settings = ORM::factory('Setting')->find_all();
			foreach ($settings as $s) {
				self::$data[$s->key] = $s->value;
			}
		}
		return self::$data;
	}

	public static function set($key, $value='')
	{
		if( is_array($key) ){
			$settings = ORM::factory('Setting');
			foreach ($key as $key => $val) {
				DB::query(Database::UPDATE,"UPDATE settings SET
					`value` = :value WHERE `key` = :key " )
					->param(':key',$key)
					->param(':value',$val)
				->execute();
			}
		}else{
			$s = DB::query(Database::UPDATE,"UPDATE settings SET
					`value` = :value WHERE `key` = :key " )
					->param(':key',$key)
					->param(':value',$value)
				->execute();

		}
		self::data(true);
	}

	public static function get($key, $default=null)
	{
		$data = self::data();
		return isset($data[$key])?$data[$key]:$default;
	}

}