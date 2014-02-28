<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaComment extends ORM{

    protected $_primary_key = 'comment_id';
    protected $_table_name	= 'idea_comments';

    protected $_belongs_to = array(
    		'idea' => array('model'=>'Idea', 'foreign_id'=>'idea_comments.idea_id'),
    		'user' => array('model'=>'User', 'foreign_id'=>'idea_comments.user_id')
    );

}
