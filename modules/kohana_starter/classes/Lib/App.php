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

	public static function paypal()
	{
		$paypal_config          = self::config('paypal');
		$credentials            = $paypal_config[$paypal_config['use']];

		$paypal                 = new Lib_Paypal();
		$paypal->sandbox        = $credentials['sandbox'];
		$paypal->paypal_url     = $credentials['url'];
		$paypal->paypal_email   = $credentials['receiver_email'];
		$paypal->api_un         = $credentials['api_un'];
		$paypal->api_pw         = $credentials['api_pw'];
		$paypal->api_sig        = $credentials['api_sig'];
		$paypal->paypal_api_url = $credentials['api_url'];
		$paypal->currency       = $credentials['currency'];

		$paypal->return_url     = $credentials['return_url'];
		$paypal->cancel_url     = $credentials['cancel_url'];

		return $paypal;
	}

}