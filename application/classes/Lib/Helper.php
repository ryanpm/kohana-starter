<?php

class Lib_Helper{

	static function reQuery($mixed,$app='?'){
		$vars = array();
		if(is_string($mixed)){
			$vars = array($mixed);
		}else{
			$vars = $mixed;
		}

		$url = array();
		foreach($vars as $r){
			if(($val =  Request::current()->query($r))!=null){
				$url[] = $r.'='.$val;
			}
		}

		if(count($url)==0)return '';
		return $app.implode('&',$url);

	}


}

?>