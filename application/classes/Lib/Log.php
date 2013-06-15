<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User class handles all user information, registration, and login authentication
 * */

class Lib_Log{

   const TYPE_REGISTER = 1;
   const TYPE_LOGIN = 2;

   /**
    * Lib_Log::getLogs()
    *
    * @param int $user_id
    * @return array
    */
   static function getLogs($user_id, $page=1, $perpage=10){

        $user = ORM::factory('user',$user_id);
        $logs = array();
        $total = $totalpage = 0;
        if( $user->loaded() ){

            $start = ($page-1)*$perpage;
            $total = $user->logs->count_all();
            $totalpage = ceil($total/$perpage);
            $userlogs = $user->logs->limit($start.','.$perpage)->order_by('log_id','desc')->find_all();

            foreach( $userlogs->next() as $d ){
                $l = array(date('M d, Y h:ia',strtotime($d->logged_date)) ,$d->ip_address,$d->user_agent);
                $logs[]= $l;
            }
        }
        return array('logs'=>$logs,'total'=>$total,'totalpage'=>$totalpage);

    }

    /**
     * Lib_Log::addLog()
     *
     * @param int $user_id
     * @param int $type
     * @return void
     */
    static function addLog($user_id, $type){
        $log = ORM::factory('log');
        $log->user_id = $user_id;
        $log->logged_date = date('Y-m-d H:i:s');
        $log->ip_address = $_SERVER['REMOTE_ADDR'];
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log->type = $type;
        $log->save();
    }


}