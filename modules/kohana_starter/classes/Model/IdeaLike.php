<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaLike extends ORM{

    protected $_primary_key = 'like_id';
    protected $_table_name	= 'idea_likes';

    public static function getTotal($idea_id)
    {
    	return $this->where('idea_id','=',$idea_id)->count_all();
    }

}