<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaPledgeBenefit extends ORM{

    protected $_primary_key = 'pledge_benefits_id';
    protected $_table_name	= 'idea_pledge_benefits';

    protected $_belongs_to = array(
    		'idea' => array('model'=>'Idea', 'foreign_id'=>'idea_pledge_benefits.idea_id')
    );

}
