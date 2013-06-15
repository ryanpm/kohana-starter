<?php

class Lib_PHPinit{

	static function getUploadMaxFilesizeInByte(){
		return self::inBytes(self::getUploadMaxFilesize());
	}

	static function getPostMaxSizeInByte(){ 
        return self::inBytes(self::getPostMaxSize());
	}

	static function getUploadMaxFilesize(){
		return ini_get('upload_max_filesize');
	}

	static function getPostMaxSize(){
		return ini_get('post_max_size');
	}

    static function inBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        } 
        return $val;
    }
    
}

?>