<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaRequestFunding extends ORM{

    const STATUS_PENDING = 0;
    const STATUS_FAILED  = 1;
    const STATUS_SUCCESS = 2;

    protected $_primary_key = 'idea_funding_id';
    protected $_table_name	= 'idea_request_fundings';

    public static function getStatusArray()
    {
    	return array(
				self::STATUS_PENDING => 'Pending',
				self::STATUS_FAILED  => 'Failed',
				self::STATUS_SUCCESS => 'Success',
    		);
    }

    public function daysLeft()
    {
    	return Lib_Tools::getDateDiffByDay( date('Y-m-d').' 00:00:00', $this->date_expired.' 23:59:59');
    }


    public function getStatusId()
    {
        if( $this->date_expired >  date('Y-m-d') ){
            return self::STATUS_PENDING;
        }else{

            if( $this->current_funding >= $this->goal_amount ){
                return self::STATUS_SUCCESS ;
            }else{
                return self::STATUS_FAILED;
            }
        }
    }

    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[self::getStatusId()];
    }

}