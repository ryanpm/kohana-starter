<?php defined('SYSPATH') OR die('No direct script access.');

class Lib_View extends Kohana_View {

    public static $main_dir = '';
    /**
     * This function helps to get file from view with reference to controller name
     * @param type $file - String for view or Array if Data
     * @param type $data
     * @return View
     *
     */
    public static function factory($file = NULL, array $data = NULL){

        $kotal = false;
        if( strpos($file, 'tal:') === 0 ){
            $file = str_replace('tal:', '', $file);
            $kotal = true;
        }

        if( is_array($file) ){
                $data = $file;
                $file ='';
        }
        if( $file == NULL ){
                $file = Request::current()->controller().'/'.Request::current()->action();
        }else{
            if( strstr($file,'.') !== FALSE ){
                $file = str_replace('.', Request::current()->controller(), $file);
            }
        }

        if( static::$main_dir!=''){
            static::$main_dir =  trim(strtolower(static::$main_dir),'/').'/';
        }
        $file =  static::$main_dir.trim(strtolower($file),'/');

        if( $kotal ){

            $talview = Kotal_View::factory($file, $data);
            $talview->use_tal(TRUE);

            PHPTAL_Dom_Defs::getInstance()->registerNamespace(new Lib_TALCode());

            $talview->set_output_mode(PHPTAL::HTML5);
            $talview->set_encoding('utf-8');

            return $talview;

        }else{

            return Kohana_View::factory($file,$data);

        }

    }

    static $sections=array();
    static $section_nest = array();
    public static function section($section_name)
    {
        self::$section_nest[] = $section_name;
        ob_start();
    }

    public static function end()
    {
        if( count(self::$section_nest) == 0 ) throw new  Exception("Error section end pair");
        $current_nest = self::$section_nest[ count(self::$section_nest)-1 ];
        unset( self::$section_nest[ count(self::$section_nest)-1 ] );
        self::$section_nest = array_merge(array(),self::$section_nest);
        self::$sections[$current_nest] = ob_get_contents();
        ob_get_clean();
    }

    public static function yield($section_name)
    {
        return isset(self::$sections[$section_name])?self::$sections[$section_name]:'';
    }

}


function phptal_tales_slugify($src, $nothrow )
{
    return 'Lib_Tools::slugify('.phptal_tales($src, $nothrow).')';
}

function phptal_tales_number0($src, $nothrow )
{

    return 'number_format('.phptal_tales($src, $nothrow).')';
}

function phptal_tales_number2($src, $nothrow )
{
    return 'number_format('.phptal_tales($src, $nothrow).',2)';
}


function phptal_tales_section($src, $nothrow )
{
    return 'Lib_View::section(\''. $src .'\')';
}


function phptal_tales_section_end($src, $nothrow )
{
    return 'Lib_View::end()';
}


