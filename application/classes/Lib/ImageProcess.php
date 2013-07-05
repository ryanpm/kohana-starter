<?php defined('SYSPATH') or die('No direct script access.');

class Lib_ImageProcess{

	public $imagestats;
	public $pathinfo;
	public $quality    		= 100;
	public $round    		= false;
	public $crop     		= false;
	public $crop_position = array('left','top');
	public $extension;
	public $source;
	public $alternative_source;
	public $size;
	public $cache_path;
	public $recache = false;
	public $destination;

	public $frame 			 = false;
	public $background_color = array(255,255,50); // applicable if frame is true
	public $border_size 	 = 0;
	public $border_color 	 = array(50,50,50);

	public $is_image = false;
	public $is_existing = false;
	public $init = false;

	public static function getImageFile($image_src,$size=null)
	{
		$img = new Lib_ImageProcess($image_src,$size);
		$img->source = $image_src;
		$img->size = $size;
		return $img;

	}

	function __construct($source=null,$size=null)
	{
		$this->cache_path = APPPATH . 'cache/';
		$this->source 		= $source;
		$this->size 		= $size;
	}

	public function init(){

		if($this->init) return;
		$this->analyze();
        $this->init = true;

	}

	public function analyze()
	{

        if( isset($this->source) ){

        	$found = false;
        	// source not found
        	if(file_exists($this->source)){
        		$found = true;
        	}elseif(file_exists($this->alternative_source)){
        		$found = true;
        		$this->source = $this->alternative_source;
        	}

        	if($found){
	        	$this->is_existing = true;
	        	if( !isset($this->pathinfo) ){
			        $this->pathinfo    =pathinfo($this->source);
	        	}
	        	if (!isset($this->imagestats)) {
			        $this->imagestats  = getimagesize($this->source);
	        	}
		        if(count($this->imagestats)>3 and isset($this->imagestats['mime'])){
	                $this->is_image = true;
		        }
		        $this->extension = isset($this->pathinfo)?$this->pathinfo:'';
        	}
        }

	}


	public function clear()
	{
		$this->is_existing = false;
		$this->is_image = false;
		$this->pathinfo    = null;
		$this->imagestats  = null;
		$this->extension = null;

	}

	public function getCacheName()
	{
        $append_name = str_replace("x","_", $this->size );
        $file_name = $this->source;
        if(!$this->is_existing){
                $file_name = 'no_file_found';
        }else{
            if( !$this->is_image ){
                    $file_name = 'file_ext_'.$this->extension;
            }
        }
        if($this->crop){
                $append_name .= '_c';
        }
        if($this->round){
                $append_name .= '_r';
        }
		return $append_name."_".md5($append_name.$file_name);

	}

	public  function render()
	{

		$this->init();
        $cache_src = $this->cache_path.$this->getCacheName();
        // let see if the image has not yet been resized before
        try{
        	//if failed file is not yet in the cache so catch it to resize the file
        	if( $this->recache ){
        		@unlink($cache_src);
        	}
	        $time   	=   gmdate('r', filemtime($cache_src));

        }catch(Exception $e){


            if( !$this->is_image or !$this->is_existing ){

        		list($x,$y) = explode("x",$this->size);
                $im = imagecreatetruecolor($x,$y);

                $white = imagecolorallocate($im, 20, 255, 255 );
                $black = imagecolorallocate($im, 0, 0, 0 );

                imagefill($im, 0, 0, $white );
                $word = ($this->is_existing)?'['.$this->extension.']':'[!FOUND]';

                $font = 12;
                $xc = ($x/2)-( strlen($word)*4.5 );
                $yc = ($y/2)-8;

                imagestring($im, 12, $xc, $yc, $word, $black);
                imagejpeg($im,$cache_src);

            }else{
            	$this->destination = $cache_src;
                $this->resize();

            }

	        $time   	=   gmdate('r', filemtime($cache_src));

        }

        $etag   	=   md5($time.$cache_src);
        $image_info = 	getimagesize($cache_src);
        $filesize  	=   filesize($cache_src);
        // See if the browser already has the image
		$lastModifiedString	= gmdate('D, d M Y H:i:s', filemtime($cache_src)) . ' GMT';

		$this->doConditionalGet($etag, $lastModifiedString);
		$data = file_get_contents($cache_src);

		// Send the image to the browser with some delicious headers
		header("Content-type: ".$this->getMimeType());
        header("Content-Length: ".$filesize);
        // header("Cache-Control: must-revalidate, max-age=10800, pre-check=10800");
        // header('Expires: '.$time);
        // header("Etag: ".$etag);

        echo $data;
        exit;

	}


	public function resize(){

		$this->init();

		$sw 		= $this->imagestats[0];
		$sh 		= $this->imagestats[1];

		if( $this->size != "" ){

			list($rw,$rh)     = explode("x",$this->size);

			//adjust image ratio to fit the given dimensions
			if( $sw > $rw and $sw > $sh){//
				$nw = $rw;
				$nh = floor(($rw*$sh)/$sw);
				if( $nh > $rh ){
					$nh = $rh;
					$nw = floor(($rh*$sw)/$sh);
				}
			}elseif( $sh > $rh and $sh > $sw ){//
				$nh = $rh;
				$nw = floor(($rh*$sw)/$sh);
				if( $nw > $rw ){
					$nw = $rw;
					$nh = floor(($rw*$sh)/$sw);
				}
			}elseif( $sh > $rh and $sh == $sw ){
				if( $rh > $rw ){
					$nw = $rw;
					$nh = $rw;
				}else{
					$nw = $rh;
					$nh = $rh;
				}
			}else{
				$nw = $sw;
				$nh = $sh;
			}
			//make sure crop is false

		}else{
			$nw=$sw;
			$nh=$sh;
		}

		$quality = $this->quality;
		switch ($this->imagestats['mime'])
		{
			case 'image/gif':
				// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
				// This is maybe not the ideal solution, but IE6 can suck it
				$creationFunction	= 'ImageCreateFromGif';
				$outputFunction		= 'ImagePng';
				$mime				= 'image/png'; // We need to convert GIFs to PNGs
				$doSharpen			= FALSE;
				$quality			= round(10 - ($this->quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
			break;

			case 'image/x-png':
			case 'image/png':
				$creationFunction	= 'ImageCreateFromPng';
				$outputFunction		= 'ImagePng';
				$doSharpen			= FALSE;
				$quality			= round(10 - ($this->quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;

			default:
				$creationFunction	= 'ImageCreateFromJpeg';
				$outputFunction	 	= 'ImageJpeg';
				$doSharpen			= TRUE;
			break;
		}

		$im_src = $creationFunction($this->source);

		$offsetX = 0;
		$offsetY = 0;
		if( $this->crop ){

			$nw = $rw;
			$nh = $rh;

			$ratioComputed		= $sw / $sh;
			$cropRatioComputed	= (float) $rw / (float) $rh;

			if ($ratioComputed < $cropRatioComputed)
			{ // Image is too tall so we will crop the top and bottom
				$origHeight	= $sh;
				$sh		= $sw / $cropRatioComputed;

				if($this->crop_position[1] == 'top'){
					$offsetY	= 0;
				}else{
					$offsetY	= ($origHeight - $sh) / 2;
				}

			}
			else if ($ratioComputed > $cropRatioComputed)
			{ // Image is too wide so we will crop off the left and right sides
				$origWidth	= $sw;
				$sw		= $sh * $cropRatioComputed;

				if($this->crop_position[0] == 'left'){
					$offsetY	= 0;
				}else{
					$offsetX	= ($origWidth - $sw) / 2;
				}

			}

		}

		$dest_x = $dest_y = 0;
		if( $this->frame ){

			$im_dst = imagecreatetruecolor($rw,$rh);
			$dest_x = ceil(($rw-$nw)/2);
			$dest_y = ceil(($rh-$nh)/2);
			$border_x  = $rw;
			$border_y  = $rh;

		}else{

			$im_dst = imagecreatetruecolor($nw,$nh);
			$border_x  = $nw;
			$border_y  = $nh;

		}

		if( $this->border_size > 0 ){
			$cc = $this->parseColor($this->border_color);
		}else{
			$cc = $this->parseColor($this->background_color);
		}

		$background_color  = imagecolorallocate($im_dst,$cc[0],$cc[1],$cc[2]);
		imagefill($im_dst, 0, 0, $background_color);

		if( $this->border_size > 0 ){

			// fill with background to offset position
			$bc = $this->parseColor($this->background_color);
			$background_color  = imagecolorallocate($im_dst,$bc[0],$bc[1],$bc[2]);

			imagefilledrectangle($im_dst, $this->border_size, $this->border_size, $border_x-$this->border_size-1, $border_y-$this->border_size-1, $background_color);

		}

		ImageCopyResampled($im_dst, $im_src, $dest_x, $dest_y, $offsetX, $offsetY, $nw, $nh, $sw, $sh);

		if ($doSharpen)
		{
			// Sharpen the image based on two things:
			//	(1) the difference between the original size and the final size
			//	(2) the final size
			$sharpness	= $this->findSharp($sw, $nw);

			$sharpenMatrix	= array(
				array(-1, -2, -1),
				array(-2, $sharpness + 12, -2),
				array(-1, -2, -1)
			);
			$divisor		= $sharpness;
			$offset			= 0;
			imageconvolution($im_dst, $sharpenMatrix, $divisor, $offset);
		}

		if($this->round){
			$this->makeRounded($im_dst);
		}

		// Determine the quality of the output image
		$outputFunction($im_dst,$this->destination,$quality);
		// Clean up the memory
		ImageDestroy($im_dst);
		ImageDestroy($im_src);

		return;

	}

	public function makeRounded(&$source_image,$radius=10){

		$source_width = imagesx($source_image);
		$source_height = imagesy($source_image);
		$colour = "FFFFFF";

		//
		// 2) create mask for top-left corner in memory
		//
		$corner_image = imagecreatetruecolor($radius, $radius );
		$clear_colour = imagecolorallocate($corner_image, 0, 0, 0 );

		$solid_colour = imagecolorallocate($corner_image, hexdec( substr( $colour, 0, 2 ) ), hexdec( substr( $colour, 2, 2 ) ), hexdec( substr( $colour, 4, 2 ) ) );

		imagecolortransparent($corner_image, $clear_colour );
		imagefill($corner_image, 0, 0, $solid_colour );
		imagefilledellipse($corner_image, $radius, $radius, $radius * 2, $radius * 2, $clear_colour );

		//
		// 3) render the top-left, bottom-left, bottom-right, top-right corners by rotating and copying the mask
		//
		imagecopymerge($source_image, $corner_image, 0, 0, 0, 0, $radius, $radius, 100 );
		$corner_image = imagerotate( $corner_image, 90, 0 );

		imagecopymerge($source_image, $corner_image, 0, $source_height - $radius, 0, 0, $radius, $radius, 100 );
		$corner_image = imagerotate( $corner_image, 90, 0 );
		imagecopymerge($source_image, $corner_image, $source_width - $radius, $source_height - $radius, 0, 0, $radius, $radius, 100 );
		$corner_image = imagerotate( $corner_image, 90, 0 );
		imagecopymerge($source_image, $corner_image, $source_width - $radius, 0, 0, 0, $radius, $radius, 100 );


	}

	public function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
	{
		$final	= $final * (750.0 / $orig);
		$a		= 52;
		$b		= -0.27810650887573124;
		$c		= .00047337278106508946;

		$result = $a + $b * $final + $c * $final * $final;

		return max(round($result), 0);
	}

	public function doConditionalGet($etag, $lastModified)
	{
		header("Last-Modified: $lastModified");
		header("ETag: \"{$etag}\"");

		$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
			stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
			false;

		$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
			stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
			false;

		if (!$if_modified_since && !$if_none_match)
			return;

		if ($if_none_match && $if_none_match != $etag && $if_none_match != '"' . $etag . '"')
			return; // etag is there but doesn't match

		if ($if_modified_since && $if_modified_since != $lastModified)
			return; // if-modified-since is there but doesn't match

		// Nothing has changed since their last request - serve a 304 and exit
		header('HTTP/1.1 304 Not Modified');
		exit();
	}

	public function getMimeType()
	{
		if( !isset($this->imagestats['mime']) )return null;
		$mime	= $this->imagestats['mime'];
		if( $this->imagestats['mime'] == 'image/gif'){
			$mime				= 'image/png'; // We need to convert GIFs to PNGs
		}
		return $mime;

	}

	public function getExtesion()
	{
		if( isset($this->pathinfo['extension']) ){
			return $this->pathinfo['extension'];
		}else{
			$m = $this->getMimeType();
			if( $m != null ){
				list($t,$x) = explode('/',$this->getMimeType());
				return $x;
			}
		}
		return null;
	}



	public function parseColor($color)
	{
		if( is_string($color) ){
			if( $color[0] == '#' ){ //hex
				$r = hexdec(substr($color, 1,2));
				$g = hexdec(substr($color, 3,2));
				$b = hexdec(substr($color, 5,2));
			}
			return array($r,$g,$b);
		}elseif( is_array($color) ){
			return $color;
		}

	}

}
