<?php

include(MODAPP.'/vendor/htmllawed/htmllawed/HtmlLawed.php');
class Lib_HtmlLawed{

	public static function clean($html)
	{
		return htmLawed($html, array('safe'=>1, 'elements'=>'a, b, strong, i, em, li, ol, ul, img, br, p, div'));
	}

}