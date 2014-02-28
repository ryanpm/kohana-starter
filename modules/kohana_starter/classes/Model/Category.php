<?php defined('SYSPATH') or die('No direct script access.');

class Model_Category extends ORM{

    protected $_primary_key = 'category_id';
    protected $_table_name	= 'categories';

    public function getWithItemsOnly()
    {
    	$this->join(array('idea_to_categories','i2c'))
	    		 ->on('category.category_id','=','i2c.category_id')
    		 ->join(array('ideas','i'))
	    		 ->on('i.idea_id','=','i2c.idea_id')
	    		 ->on('i.is_draft','=',DB::expr('0'))
    		 ->group_by('category.category_id');
		return $this;
    }

}
