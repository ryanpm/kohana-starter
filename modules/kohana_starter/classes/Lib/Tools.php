<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Tools{


    static function encrypt($password){
        return  hash("sha256", $password);
    }

	/**
	 * Generate Message & Thread
	**/
	static function getIpAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
			return $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Date
	**/
	static function diff_date($ori_date,$total_days,$format='Y-m-d'){
		$ori_date = strtotime($ori_date);
		$new_date = strtotime($total_days, $ori_date); // or, strtotime( '-1 days' );
		$y = date( $format, $new_date );
		return $y;
	}


	public static function getDateDiffByDay($date1, $date2)
	{
		$time1 = strtotime($date1); // start
		$time2 = strtotime($date2); // end
		$diff = abs($time1 - $time2);
		return (int)( $diff / (60*60*24) );

	}

	static function prepare_year_list($start_year = 2013){
		$current_year = date('Y');
		$years = array();
		do{
			$years[$start_year] = $start_year;
			$start_year ++;
		}while($start_year <= $current_year);

		krsort($years); //reverse sort

		return $years;
	}

	public static function safe_urlencode($str)
	{
		return urlencode(preg_replace('/[^A-Za-z0-9\s]/', ' ', $str));
	}

	public static function guid($namespace = '') {
	    $uid = uniqid("", true);
	    $data = $namespace;
	    $data .= $_SERVER['REQUEST_TIME'];
	    $data .= $_SERVER['HTTP_USER_AGENT'];
	    $data .= $_SERVER['REMOTE_ADDR'];
	    $data .= $_SERVER['REMOTE_PORT'];
	    $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
	    $guid =  substr($hash,  0,  8) .
	            '_' .
	            substr($hash,  8,  4) .
	            '_' .
	            substr($hash, 12,  4) .
	            '_' .
	            substr($hash, 16,  4) .
	            '_' .
	            substr($hash, 20, 12);
	    return strtolower($guid);
  	}

	public static function getExtentionVideoType($file){

        $types = array(
            'video/mpeg' => 'm1v,m2v,mjpg,moov,mp3,mpeg,mpa',
            'video/x-mpeg' => 'mp3',
            'video/quicktime' => 'moov,mov',
            'video/webm' => 'webm',
            'video/ogg' => 'ogg',
            'video/x-fl' => 'flv',
            'video/avi' => 'avi',
            'video/mp4' => 'mp4'
        );

        $pathinfo  = pathinfo($file);
        $mimetype  = '';
        foreach ($types as $mime => $ext){
            $parts = explode(',',$ext);
            if(in_array($pathinfo['extension'], $parts)){
                $mimetype = $mime;
            }
        }
        return $mimetype;
	}

	public static function convertDate($string)
	{

		$date_parts = explode('/', $string);
		if( count($date_parts) == 3 ){
			list($m,$d,$y) = $date_parts;
			return $y.'-'.$m.'-'.$d;
		}
		return 0;

	}

    public static function isVideoFlashPlayable($file)
    {
        $pathinfo = pathinfo($file);
        if($pathinfo['extension']=='mp4' or $pathinfo['extension']=='flv'){
            return true;
        }
        return false;
    }

	public static function base64UrlEncode($input) {
	 	return strtr(base64_encode($input), '+/=', '-_,');
	}

	public static function base64UrlDecode($input) {
	 	return base64_decode(strtr($input, '-_,', '+/='));
	}

	public static function pluralize($number, $singular)
	{
		if( $number < 2 ){
			return $singular;
		}
		$rules=array(
			'/(m)ove$/i' => '\1oves',
			'/(f)oot$/i' => '\1eet',
			'/(c)hild$/i' => '\1hildren',
			'/(h)uman$/i' => '\1umans',
			'/(m)an$/i' => '\1en',
			'/(s)taff$/i' => '\1taff',
			'/(t)ooth$/i' => '\1eeth',
			'/(p)erson$/i' => '\1eople',
			'/([m|l])ouse$/i' => '\1ice',
			'/(x|ch|ss|sh|us|as|is|os)$/i' => '\1es',
			'/([^aeiouy]|qu)y$/i' => '\1ies',
			'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
			'/(shea|lea|loa|thie)f$/i' => '\1ves',
			'/([ti])um$/i' => '\1a',
			'/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
			'/(bu)s$/i' => '\1ses',
			'/(ax|test)is$/i' => '\1es',
			'/s$/' => 's',
		);
		foreach($rules as $rule=>$replacement)
		{
			if(preg_match($rule,$singular))
				return preg_replace($rule,$replacement,$singular);
		}
		return $singular.'s';
	}


	public static function str_cut($string, $limit, $concat='...')
	{
		return ( strlen($string) > $limit )? substr($string, 0, $limit). $concat : $string;
	}

	public static function slugify($text)
	{
	    // Swap out Non "Letters" with a -
	    $text = preg_replace('/[^\\pL\d]+/u', '-', $text);

	    // Trim out extra -'s
	    $text = trim($text, '-');

	    // Convert letters that we have left to the closest ASCII representation
	    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	    // Make text lowercase
	    $text = strtolower($text);

	    // Strip out anything we haven't been able to convert
	    $text = preg_replace('/[^-\w]+/', '', $text);

	    return $text;
	}

	public static function secure_html($html)
	{
		return preg_replace('/<([a-z]+)[^>]*>/i', '<\1>', strip_tags($html,'<a><b><img><i><br><p>'));
	}

}

