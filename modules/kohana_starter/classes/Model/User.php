<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends ORM{

    protected $_primary_key = 'user_id';
    protected $_table_name	= 'users';

    protected $_has_one = array(
    );

    protected $_has_many = array(
    );

    public function getID(){
        return $this->user_id;
    }

    public function getCompleteName(){
        return $this->fullname;
    }

    public function getEmailAddress(){
        return $this->email_address;
    }

    public function profilePhotoFileName()
    {
        return  $this->user_id.'_'. md5($this->user_id.$this->username);
    }

    public function deleteProfilePhoto()
    {

        if( $this->profile_photo == '' )return;
        $dir_profile_photo = Config::yipyy()->get('dir_profile_photo');
        $photo = $this->profile_photo;
        $this->profile_photo = '';
        $this->save();
        if( $this->saved() and file_exists($dir_profile_photo.$photo) ){
            unlink($dir_profile_photo.$photo);
        }

    }

    public function updateRating()
    {

        //get all my ratings
        $ratings = $this->reviews->find_all();
        $total_ratings = 0;
        $total_each = array(1=>0,2=>0,3=>0,4=>0,5=>0);
        foreach ($ratings as $rating) {
            $total_each[$rating->reliability]++;
            $total_ratings++;
        }

        $multiplied_total=0;
        for($i=1;$i<=5;$i++){
            $multiplied_total += $i*$total_each[$i];
        }

        $average = 0;
        if($total_ratings>0){
            $average = $multiplied_total/$total_ratings;

        }

        $this->rating = $average;

    }

    static public function updateProfile($data)
    {
        $user = self::current();
        if(isset($data['birthdate_day'])){
            $birthday = date('Y-m-d', mktime(1,1,1,$data['birthdate_month'],$data['birthdate_day'],$data['birthdate_year']));
            $user->date_birthday = $birthday;
        }

        // 'db_field' no mapping  - means  'db_field' = 'form_field'
        // mapping 'db_field' => 'form_field'
        $secured_fields =array('firstname','lastname','gender','country_id'=>'country','email_address'=>'email', 'contactno_prefix', 'contactno', 'about_me','store_name', 'country_id'=>'country' );
        foreach ($secured_fields as $db_field => $form_field){
            if(!is_string($db_field)){
                $db_field = $form_field;
            }
            if( isset($data[$form_field]) ){
                $user->$db_field = $data[$form_field];
            }
        }

        if( isset($data['password']) ){
            if($data['password']!=''){
                $user->password      = Lib_Tools::encrypt($data['password']);
            }
        }

        $user->save();
        if($user->saved()){
            return true;
        }
        return false;

    }

    public static function register($data){

        $user = ORM::factory('User');
        $user->date_created = date('Y-m-d');
        $user->fullname      = $data['fullname'];
        $user->username      = $data['username'];
        $user->password      = Lib_Tools::encrypt($data['password']);
        $user->status        = Model_User::STATUS_PENDING;
        $user->nric          = $data['nric'];
        $user->matrix        = $data['matrix'];
        $user->about     = '';
        $user->save();
        return $user;

    }

    public function updateData($data)
    {
        $this->fullname      = $data['fullname'];
        if( !empty($data['password']) ){
            $this->password      = Lib_Tools::encrypt($data['password']);
        }
        $this->course       = $data['course'];
        $this->nric         = $data['nric'];
        $this->contactno    = $data['contactno'];
        $this->matrix       = $data['matrix'];
        $this->about        = $data['about'];
        $this->bank_account = Lib_HtmlLawed::clean($data['bank_account']);
        $this->save();
    }

    public function getPhotoUrl($options='&s=profile')
    {
        if( empty($this->photo) ){
            return URL::base()  .'images/profile_pic.jpg';
        }
        return Lib_App::config()->get('url_profile_photo').$this->photo.$options;
    }

    public function fullname(){
        return htmlentities($this->fullname);
    }

    public function username()
    {
        return $this->username;
    }


    public function getOnlineIdeas()
    {
        return $this->ideas
        ->where('is_draft','=',0);
    }
}
