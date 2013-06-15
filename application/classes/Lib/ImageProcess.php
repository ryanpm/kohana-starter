<?php

class Lib_ImageProcess{

	public $round    = false;
	public $crop     = false;
	public $crop_pos = array('left','top');

	function copy($src,$dst){
		$this->resize($src,$dst);
	}

	public function resize($src,$dst,$size=""){

		if( !file_exists($src) ){
			echo $src;
			echo " file not exist.";
			exit;
		}
		$path_info = pathinfo($src);
		$ext = strtolower($path_info['extension']);
		$image_info = getimagesize($src);
		$sw = $image_info[0];
		$sh = $image_info[1];

		if( $size != "" ){

			list($rw,$rh)     = explode("x",$size);

			// CROP: adjust to fill all the dimension
			// applicable when dimension is square
			if( $this->crop and $rw==$rh ){

				$nw = $rw;
				$nh = $rh;
				if( $sw > $rw and $sw > $sh ){
					$nh = $rh;
					$nw = floor(($rh*$sw)/$sh);
				}elseif( $sh > $rh and $sh > $sw ){
					$nw = $rw;
					$nh = floor(($rw*$sh)/$sw);
				}

			}else{

				//adjust image ratio to fit the given dimensions
				if( $sw > $rw and $sw > $sh ){
					$nw = $rw;
					$nh = floor(($rw*$sh)/$sw);
				}elseif( $sh > $rh and $sh > $sw ){
					$nh = $rh;
					$nw = floor(($rh*$sw)/$sh);
				}elseif( $sh > $rh and $sh == $sw ){
					$nw = $rw;
					$nh = $rh;
				}else{
					$nw = $sw;
					$nh = $sh;
				}
				//make sure crop is false
				$this->crop = false;

			}

		}else{
			$nw=$sw;
			$nh=$sh;
		}

		if($ext=="jpg"){
			$im_src = imagecreatefromjpeg($src);
		}elseif($ext=="gif"){
			$im_src = imagecreatefromgif($src);
		}elseif($ext=="png"){
			$im_src = imagecreatefrompng($src);
		}

		$im_dst = imagecreatetruecolor($nw,$nh);
		$white  = imagecolorallocate($im_dst,255,255,255);
		imagefill($im_dst, 0, 0, $white);
		//imagecopyresized($im_dst, $im_src, 0, 0, 0, 0, $nw, $nh, $sw, $sh); //imagecopyresampled
		imagecopyresampled($im_dst, $im_src, 0, 0, 0, 0, $nw, $nh, $sw, $sh);

		if( $this->crop ){ //$crop_pos

			$im_src = $im_dst;
			$im_dst = imagecreatetruecolor($rw, $rh);
			$white = imagecolorallocate($im_dst, 255, 255, 255);
			imagefill( $im_dst, 0, 0, $white);

			$sx = 0;
			$sy = 0;
			if( $this->crop_pos[0] == 'left' and $this->crop_pos[1] == 'top'  ){

				$sx = 0;
				$sy = 0;

			}elseif(  $this->crop_pos[0] == 'center' and $this->crop_pos[1] == 'top' ){

				$sx =  (($nw - $rw)>0)?($nw - $rw)/2:0;
				$sy =  0;
			}

			//imagecopyresized($im_dst, $im_src, 0, 0, $sx, $sy, $rw, $rh,  $rw, $rh);
			imagecopyresampled($im_dst, $im_src, 0, 0, $sx, $sy, $rw, $rh,  $rw, $rh);
		}

		if($this->round){
			$this->makeRounded($im_dst);
		}


		if($ext=="jpg"){
			imagejpeg($im_dst,$dst,100);
		}elseif($ext=="gif"){
			imagegif($im_dst,$dst,100);
		}elseif($ext=="png"){
			imagepng($im_dst,$dst);
		}

		return;

	}

	public function makeRounded(&$source_image,$radius=20){

		$source_width = imagesx($source_image);
		$source_height = imagesy($source_image);
		$colour = "FFFFFF";

		//
		// 2) create mask for top-left corner in memory
		//
		$corner_image = imagecreatetruecolor(
		    $radius,
		    $radius
		);

		$clear_colour = imagecolorallocate(
		    $corner_image,
		    0,
		    0,
		    0
		);

		$solid_colour = imagecolorallocate(
		    $corner_image,
		    hexdec( substr( $colour, 0, 2 ) ),
		    hexdec( substr( $colour, 2, 2 ) ),
		    hexdec( substr( $colour, 4, 2 ) )
		);

		imagecolortransparent(
		    $corner_image,
		    $clear_colour
		);

		imagefill(
		    $corner_image,
		    0,
		    0,
		    $solid_colour
		);

		imagefilledellipse(
		    $corner_image,
		    $radius,
		    $radius,
		    $radius * 2,
		    $radius * 2,
		    $clear_colour
		);

		//
		// 3) render the top-left, bottom-left, bottom-right, top-right corners by rotating and copying the mask
		//

		imagecopymerge(
		    $source_image,
		    $corner_image,
		    0,
		    0,
		    0,
		    0,
		    $radius,
		    $radius,
		    100
		);

		$corner_image = imagerotate( $corner_image, 90, 0 );

		imagecopymerge(
		    $source_image,
		    $corner_image,
		    0,
		    $source_height - $radius,
		    0,
		    0,
		    $radius,
		    $radius,
		    100
		);

		$corner_image = imagerotate( $corner_image, 90, 0 );

		imagecopymerge(
		    $source_image,
		    $corner_image,
		    $source_width - $radius,
		    $source_height - $radius,
		    0,
		    0,
		    $radius,
		    $radius,
		    100
		);

		$corner_image = imagerotate( $corner_image, 90, 0 );

		imagecopymerge(
		    $source_image,
		    $corner_image,
		    $source_width - $radius,
		    0,
		    0,
		    0,
		    $radius,
		    $radius,
		    100
		);

	}


}