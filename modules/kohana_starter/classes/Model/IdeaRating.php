<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaRating extends ORM{

    protected $_primary_key = 'rating_id';
    protected $_table_name	= 'idea_ratings';

    protected $_belongs_to = array(
    		'idea' => array('model'=>'Idea', 'foreign_id'=>'idea_ratings.idea_id'),
    		'user' => array('model'=>'User', 'foreign_id'=>'idea_ratings.user_id')
    );


}
