<?php defined('SYSPATH') or die('No direct script access.');

 class File extends Kohana_File
 {
    static function read($file_name){

        if( !file_exists($file_name) )return null;

        $size = filesize($file_name);
        if($size==0)return '';
        $fp = fopen($file_name,"r+");
        $data = fread($fp,$size);
        fclose($fp);
        return $data;

    }

    static function write($file_name,$data){
        $fp = fopen($file_name,"w+");
        fwrite($fp,$data,strlen($data));
        fclose($fp);
    }

    static function append($file_name,$data){
        $fp = fopen($file_name,"a+");
        fwrite($fp,$data,strlen($data));
        fclose($fp);
    }

    static function clear($folder){

        $folder = trim($folder,"/")."/";
    	if(is_dir($folder)){
            $d = opendir($folder);
            while( $f = readdir($d) ){
                if($f=='.' or $f=='..')continue;
                if(is_dir($folder.$f)){
                    self::rmFolder($folder.$f);
                }else{
                    unlink($folder.$f);
                }
            }
            closedir($d);
    	}

    }

    static function getFolders($folder){
        $d = opendir($folder) or die("cant open dir $folder");
        $folders = array();
        while( $f = readdir($d) ){
            if($f=='.' or $f=='..')continue;
            if( is_dir($folder.$f) ){
                $folders[] = $f;
            }
        }
         closedir($d);
        return $folders;
    }

    static function getFiles($folder){

        $folder = trim($folder,"/")."/";
        $d = opendir($folder) or die("cant open dir $folder");
        $files = array();
        while( $f = readdir($d) ){
            if($f=='.' or $f=='..')continue;
            if( is_file($folder.$f) ){
                $files[] = $f;
            }
        }
        closedir($d);
        return $files;
    }

    static function isWritable($folder){

        $folder = trim($folder,"/")."/";
        if( is_dir($folder) ){
            $file = $folder.'testwrite'. md5(time()).'.txt';
            touch($file);
            if( file_exists($file) ){
                unlink($file);
                return true;
            }
        }
        return false;

    }

    static function rmFolder($folder){

        $folder = trim($folder,"/")."/";
        self::clear($folder);
        if(is_dir($folder)){
            rmdir($folder);
        }

    }


    static function mkFolder($folder){
        if(!is_dir($folder)){
            mkdir($folder,0777);
        }

    }

    static function mvFile($from,$to){
        if(file_exists($from)){
            copy($from, $to);
            unlink($from);
        }

    }


 }

