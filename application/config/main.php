<?php

return array(

	'dir_profile_photo'     => MODPATH . '../uploads/profile/',
	'dir_idea_photo'        => MODPATH . '../uploads/idea/',
	'dir_idea_presentation' => MODPATH . '../uploads/presentation/',
	'dir_fundedby_images'   => MODPATH . '../uploads/fundedby/',

	'dir_idea_video'        => DOCROOT . 'video/',
	'dir_ads'               => DOCROOT . 'images/ads/',

	'url_profile_photo'     => URL::site() . 'file/profile?img=',
	'url_fundedby_images'   => URL::site() . 'file/fundedby?img=',
	'url_idea_video'        => URL::base() . 'video/',
	'url_idea_photo'        => URL::site() . 'file/image?img=:img',
	'url_ads'               => URL::base() . 'file/ad?img=',

	// MB
	'video_max_size'        => 300,
	);
