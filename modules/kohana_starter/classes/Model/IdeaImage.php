<?php defined('SYSPATH') or die('No direct script access.');

class Model_IdeaImage extends ORM{

    protected $_primary_key = 'image_id';
    protected $_table_name	= 'idea_images';

    public function getUrl($size='100x100')
    {
    	return str_replace(':img', $this->filename, Lib_App::config()->get('url_idea_photo')).'&s='.$size;
    }
}
