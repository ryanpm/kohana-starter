<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Image extends Lib_Controller{

	function get() {

		$file_name = $this->request->query('im');
		$path_info = pathinfo($file_name);
		$ext = $path_info['extension'];

		$first_level 	= '';
		$second_level 	= '';

		$upload_path       = DOCROOT . "/uploads/";
		$cache_path        = DOCROOT . "/cache/";

		if( isset($_GET['path']) ){

			$image_src = $upload_path.$file_name;

		}else{

			$first_level  = $upload_path.$file_name[0]."/";
			$second_level = $first_level.$file_name[1]."/";
			$image_src = $second_level.$file_name;

		}


		if( isset($_GET['size']) and trim($_GET['size'])!=""  ){

			if(in_array($_GET['size'],array("L","S","T"))){
				$size = (isset($wgUserVar['articleImageMaxSize'][$_GET['size']]))?$wgUserVar['articleImageMaxSize'][$_GET['size']]:$wgUserVar['articleImageMaxSize']["L"];
			}else{
				$size = $_GET['size'];
			}

			list($x,$y) = explode("x",$size);

			//abort impractical image size
			if($x>3000 or $y>3000)exit;

			$append_name = str_replace("x","_", $size );
			if(isset($_GET['crop'])){
				$append_name .= '_c';
			}
			if(isset($_GET['round'])){
				$append_name .= '_r';
			}
			$cache_file_name = $append_name."_".$file_name;

			if( isset($_GET['path']) ){

				$cache_src = $cache_path.$cache_file_name;

			}else{

				$first_level_c  = $cache_path.$file_name[0]."/";
				$second_level_c = $first_level_c.$file_name[1]."/";
				$cache_file_name = $append_name."_".$file_name;

				$cache_src = $second_level_c.$cache_file_name;

			}

			// let see if the image has not yet been resized before
			if( !file_exists($cache_src) ){

				if( !isset($_GET['path']) ){
					//create tree folder
					if( !is_dir($first_level_c) ){
						mkdir($first_level_c,0777) or die("err1");
					}
					if( !is_dir($second_level_c) ){
						mkdir($second_level_c,0777) or die("err2");
					}
				}

				if(isset($_GET['crop'])){
					$wgSH->shImages->crop  = true;
					$wgSH->shImages->crop_pos = array('center','top');

				}
				if(isset($_GET['round'])){
					$wgSH->shImages->round  = true;
				}

				$wgSH->shImages->resize($image_src,$cache_src,$size);

				@chmod($cache_src,0777);

			}

			$image_src = $cache_src;

		}

		$im_info = getimagesize($image_src);

		$size   =   filesize($image_src);
		$etag   =   md5($time.$file_name);
		$time   =   gmdate('r', filemtime($image_src));

		header('Last-Modified: '.$time);
		header("Cache-Control: must-revalidate, max-age=10800, pre-check=10800");
		header('Expires: '.$time);
		header("Etag: ".$etag);

		$test1 = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $time;
		$test2 = isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag;
		if($test1 || $test2){
			header('HTTP/1.1 304 Not Modified');
			exit();
		}

		header("Content-Length: ".$size);
		header("Content-type: {$im_info['mime']}");
		echo file_get_contents($image_src);
		exit;



	}


}

?>

