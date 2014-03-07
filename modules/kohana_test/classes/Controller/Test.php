<?php

if( !LOCAL ) exit;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author edifice
 */
class Controller_Test extends Controller{

    public function after()
    {
        echo 'ok';
        exit;
    }

    public function action_paypal()
    {

        // $postdata = array();
        // $opts = array('https' =>
        //     array(
        //         'method'  => 'POST',
        //         'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
        //                       "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n".
        //                       "Accept-Encoding: gzip,deflate,sdch\r\n".
        //                       "Cache-Control: max-age=0\r\n".
        //                       "Connection: keep-alive\r\n".
        //                       "Host: www.api-3t.sandbox.paypal.com/nvp\r\n".
        //                       "User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.3\r\n".
        //                       'Accept-Language: en-US,en;q=0.8',
        //         'content' => $postdata
        //     )
        // );

        // $context                 = stream_context_create($opts);
        // var_dump($context);
        // echo file_get_contents('https://api-3t.sandbox.paypal.com/nvp', false, $context );

        $data = '{}';
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_CAINFO, "C:/Users/edifice/Desktop/Projects/008_IdeaBank/cacert.pem");
        curl_setopt($ch, CURLOPT_URL, "https://api-3t.sandbox.paypal.com/nvp");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-type: application/x-www-form-urlencoded",
          "Content-length: ".strlen($data))
        );
        try {
            $result = curl_exec($ch);
            if (FALSE === $result)  throw new Exception(curl_error($ch), curl_errno($ch));
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
        var_dump($result);

    }

    public function action_phpini()
    {
        echo ini_get('allow_url_fopen');
    }

    public function directories()
    {
        $directories = array();
        $main_configs = Lib_App::config();
        foreach ($main_configs as $key => $conf) {
            if( strpos($key, 'dir_') === 0 ){
                $directories[$key] = $conf;
            }
        }
        return $directories;

    }

    public function action_list()
    {
        $directories = $this->directories();
        $folder = $this->request->query('folder');
        if(isset($directories[$folder])){
            print_r(File::getFiles($directories[$folder]));
        }else{
            echo "directory not exist";
        }
    }

    public function action_clear()
    {
        $directories = $this->directories();
        if( $this->request->query('folders') == 'all' ){
            foreach ($directories as $d) {
               File::clear($d,false);
            }
        }else{
            $folders = explode(',', $this->request->query('folders'));
            foreach ($folders as $d) {
                File::clear( $directories[$d] ,false);
            }
        }
    }

    public function action_delete()
    {
        $table = explode(',', $this->request->query('table'));
        foreach ($table as $t) {
           DB::query( Database::DELETE, "DELETE FROM $t")->execute();
        }
    }

    public function action_update()
    {
        $table = $this->request->query('table');
        DB::query( Database::UPDATE, "UPDATE ".$table)->execute();
    }
    public function action_select()
    {
        $table = $this->request->query('table');
        if( strpos(strtolower($table), '->') !== false ){
            $table = str_replace('->', 'from', $table);
            $rs = DB::query( Database::SELECT, "SELECT $table")->execute();
        }else{
            $rs = DB::query( Database::SELECT, "SELECT * FROM $table")->execute();
        }
        foreach ( $rs as $row) {
            print_r($row);
        }
    }

    public function action_truncate()
    {
        $truncate_excluded = array('ads','admin','settings','users','categories');
        if( $this->request->query('tables') == 'all' ){
            $datbase = Lib_App::config('database');
            $tables_array = DB::query(Database::SELECT, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '". $datbase['default']['connection']['database'] ."'")->execute();
            foreach ($tables_array as $table_row) {
                $t = $table_row['TABLE_NAME'];
                if( !in_array($t, $truncate_excluded) ){
                    DB::query(NULL, "TRUNCATE $t")->execute();
                }
            }
        }else{
            $tables = explode(',', $this->request->query('tables'));
            foreach ($tables as $t) {
                    DB::query(NULL, "TRUNCATE $t")->execute();
            }
        }
    }

}

?>
