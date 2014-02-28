<?php defined('SYSPATH') or die('No direct script access.');

class Model_Idea extends ORM{

    protected $_primary_key = 'idea_id';
    protected $_table_name	= 'ideas';

    protected $_belongs_to = array(
            'user' => array('model'=>'User', 'foreign_id'=>'ideas.user_id'),
    		'category' => array('model'=>'IdeaToCategory', 'foreign_key' => 'idea_id')
    );

    protected $_has_many = array(
        'images' => array('model'=>'IdeaImage', 'foreign_id'=>'images.idea_id'),
        'ratings' => array('model'=>'IdeaRating', 'foreign_id'=>'idea_ratings.idea_id'),
        'comments' => array('model'=>'IdeaComment', 'foreign_id'=>'idea_comments.idea_id'),
        'likes' => array('model'=>'IdeaLike', 'foreign_id'=>'idea_likes.idea_id'),
        'categories' => array('model'=>'IdeaToCategory', 'foreign_id'=>'idea_to_categories.idea_id'),
        'benefits' => array('model'=>'IdeaPledgeBenefit', 'foreign_id'=>'idea_pledge_benefits.idea_id'),
        'fund_txn' => array('model'=>'FundTxn', 'foreign_id'=>'fund_transactions.idea_id')
    );

    protected $_has_one = array(
            'model_request_funding' => array('model' => 'IdeaRequestFunding')
        );


    public function getVideoUrl()
    {
        return Lib_App::config()->get('url_idea_video'). $this->video;
    }

    public function updateRating()
    {
        //get all my ratings
        $ratings = $this->ratings->find_all();
        $total_ratings = 0;
        $total_each = array('1.0'=>0,'1.5'=>0,'2.0'=>0,'2.5'=>0,'3.0'=>0,'3.5'=>0,'4.0'=>0,'4.5'=>0,'5.0'=>0);
        foreach ($ratings as $rating) {
            $total_each[$rating->value]++;
            $total_ratings++;
        }

        $multiplied_total=0;
        for($i=1;$i<=5;$i++){
            $ii = number_format($i,1);
            $multiplied_total += $i*$total_each[$ii];
        }
        $average = 0;
        if($total_ratings>0){
            $average = $multiplied_total/$total_ratings;

        }
        $this->ave_rating = $average;
    }

    public function getComments(){
        return  $this->comments
        ->order_by('comment_id','DESC')
        ->limit(10)
        ->find_all();
    }

    public function committedFunds()
    {
        $res = DB::query(Database::SELECT,"SELECT SUM(ft.amount) AS total FROM fund_transactions  ft WHERE idea_id = '". $this->idea_id  ."' AND paypal_status = '". Lib_Paypal::STATUS_SUCCESS ."' ")->execute();
        if($res->get('total')>0){
            return $res->get('total');
        }
        return 0;
    }

    public function getCategoryIDs()
    {
       $categories = $this->categories->find_all();
       $c = array();
       foreach ($categories as $category) {
           $c[] = $category->category_id;
       }
       return $c;
    }

    public function isFundable()
    {
        if( $this->request_funding == 0 ){
            return false;
        }else{
            // can fund but check the expiration
            if( $this->model_request_funding->loaded() ){
                if( $this->model_request_funding->date_expired > date('Y-m-d') ){
                    return true;
                }
            }
        }
        return false;

    }

    public function itemPage($protocol=false)
    {
        $site = '';
        if($protocol){
            $site = URL::site(NULL, TRUE);
        }
        return $site .'idea/'. $this->idea_id. '/'. Lib_Tools::slugify($this->idea_name);
    }

    public function whereStatus($status)
    {
        $this->with('model_request_funding');
        $this->where('request_funding','=',1);
        if( $status == '' )return;
        if($status == Model_IdeaRequestFunding::STATUS_SUCCESS){

             $this->where_open();
             $this->where('current_funding','>=', DB::expr('goal_amount')  );
             $this->where_close();

        }elseif($status == Model_IdeaRequestFunding::STATUS_FAILED){

             $this->where_open();
             $this->where('date_expired','<', date('Y-m-d') );
             $this->where('current_funding','<', DB::expr('goal_amount') );
             $this->where_close();

        }elseif($status == Model_IdeaRequestFunding::STATUS_PENDING){

             $this->where_open();
             $this->where('date_expired','>', date('Y-m-d') );
             $this->where_close();

        }

    }

    public function getPrimaryPhoto($size='262x180')
    {
        $image = $this->images->limit(1)->find();
        if($image->loaded()){
            return $image->getUrl($size);
        }else{
            return '';
        }
    }
}
