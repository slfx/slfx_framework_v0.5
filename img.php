<?php

$debug = 0;

/**
  * Image Resizer Crop and Cache Class
  *
  *
  **/

/**
  * Image / Icon Resize, Crop, and Cache Class
  *
  * @version	Release: 1.14
  * @link		http://www.nucleusdevelopment.com.com/code/do/resize
  * @ModDate	2010-05-20
  *
  **/

// {{{ RESIZER
/**
  * Image Resize, Crop and Cache Class
  *
  * Resizes the image to fit width and height, while maintaining proportion.
  * will also crop from image center to specified size while keeping proportion. (Very cool)
  * Creates cached versions of files on the fly, to reduce server load.
  *
  *
  * NOTE: to utilize caching, make a directory "cache" in the same path directory as this
  * class (preferablily document-root), make directory permission
  * writable by web server process (apache).
  *
  *
  * EXAMPLE:
  *
  * <img src="resizer.php?f=imagePath&w=int&h=int&c=boolean&a=int" />
  *
  * Crop: var 'c' (boolean) - default: false
  * If possible this will cause the image to fit your percise dimensions
  *
  *
  * Crop Alignment: var 'a' (int) - default: 0
  *   1   2
  *     0
  *   4   3
  *
  * Choosing a crop alignment will cause the image to favor one side or corner over another when
  * cropping the image.
  *
  *
  *
  *
  *
  * Version Changes:
  * v1.1.4 - 2010/05/20
  * Checked and debugged in php 5.1. Better error reporting, cleaned up code. If only
  * one dimension is giving program will automatically size to that that number.
  *
  * v1.13 - 2010/02/08
  * Obfuscated media directory. Must now be specified in resize.php.
  *
  * v1.12 - 2010/02/04
  * Hole Patch: Not forcing image source URL root created hole allowed XSS Exploit. Fixed.
  * Update: Switched cached file label parameter order
  *
  *
  * @author R. Fritz Washabaugh
  * @package RESIZER
  * @access public
  **/

if ($debug) {
	error_reporting(2047);
	ini_set("display_errors",1);// report all errors!
} else {
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	ini_set("display_errors",1);
}


// removes magic quotes if that option is enabled
function undoMagic($data) {
	return get_magic_quotes_gpc() ? stripslashes_array($data) : $data;
}

function stripslashes_array($data) {
	return is_array($data) ? array_map('stripslashes', $data) : stripslashes($data);
}

function getVar($name) {
	return undoMagic(isset($_GET[$name])? $_GET[$name]:'');
}




$resize = new RESIZE();

class RESIZE
{

	//(vars)objects

	/**
	  * User Editable Vars
	  **/
	var $_siteName = 'localhost';
	var $_contentDir = 'media';

	var $_cacheDir = 'slfx_framework/cache/images/';
	var $_quality = 75; // jpeg quality

	var $_maxHeight = 1000; //  any image output will never be larger than this
	var $_maxWidth = 2000; //

	/**
	  * End user editable Vars
	  * Do not edit below this line
	  **/

	var $_crop = 0;
	var $_cropAlign = 0;
	var $_imgType = NULL;
	var $_srcX = 0;
	var $_srcY = 0;
	var $_dstX = 0;
	var $_dstY = 0;
	var $_orgWidth = 0;
	var $_orgHeight = 0;
	var $_thumbWidth = 200; // images will never be smaller than this - prevents division by 0
	var $_thumbHeight = 100;
	var $_finalWidth = 0;
	var $_finalHeight = 0;

	var $_resizedPath = NULL;
	var $checkDocType = 'jpg|jpeg|png|gif'; // allowed file types
	var $errorListener = false;
	var $_errorImgPath = 'images/logos/fox.png';

	var $watermark = '';
	var $uid = null;

	// (constructor)
	function RESIZE () {

		if((bool)getVar('c'))
			$this->_crop = (bool)getVar('c');

		$this->_cropAlign = (int)getVar('a');

		// get final width
		if(isset($_GET['w']) && (int)getVar('w') < $this->_maxWidth)
			$this->_thumbWidth = $this->_maxWidth = (int)getVar('w');
		if(isset($_GET['h']) && (int)getVar('h') < $this->_maxHeight)
			$this->_thumbHeight = $this->_maxHeight = (int)getVar('h');


		if((string)getVar('f') == '' )
			$this->error(1);

		if((int)getVar('u') != '' )
			$this->uid = getVar('u');


		$this->_orgImgPath =
			$this->_contentDir.'/'. str_replace(
												$this->_contentDir,
												'',
												(string)getVar('f')
												);

		$this->_fileName = $this->_getFileName($this->_orgImgPath);

		if(!$this->_fileExists($this->_orgImgPath))
			$this->error(1);

		if(!$this->_checkType($this->_fileName))
	  		$this->error(3);

		// get org img size
		if(!$this->_getOrgDimension())
			$this->error(4);

	  	// set thumb as max and we will reduce later if needed.
		$this->_thumbWidth = $this->_orgWidth;
		$this->_thumbHeight = $this->_orgHeight;
	   	// also set final deminsions
		$this->_finalWidth = $this->_orgWidth;
		$this->_finalHeight = $this->_orgHeight;

	  	// set new size ratio
		$this->_getReductionRatio();
		$this->_setDimensions($this->_crop);

		// check cache
		if($this->_checkCache())
			exit;

		$this->_imgType = $this->_getExt($this->_orgImgPath); // Set image type

		// get source data
		$srcImg = $this->_getImgSrc($this->_imgType);
		$finalImg = $this->_createImg($srcImg);	//

		// output image
		$this->_outputImg($finalImg);
		$this->_saveImgToCache($finalImg);

		// destory allocated resources
		$this->_destory($src);
		$this->_destory($finalImg);

	}

	// methods
	//(public)


	//(private)
	function _fileExists($file) {
		return file_exists($file);
	}

	function _getFileName($imagePath) {
		// if this is a path strip the path from filename
		if(strrchr($imagePath, '/'))
			return substr( strrchr($imagePath, '/'), 1); // remove folder references

		return $imagePath;
	}

	function _getOrgDimension() {

		// lookup original images dimensions
		$size = @getimagesize($this->_orgImgPath);
		$this->_orgWidth = $size[0];
		$this->_orgHeight = $size[1];

		return 1;

	}

	function _getReductionRatio() {

		if( $this->_orgWidth == 0 || $this->_orgHeight == 0 )
			return 0;

		// get the ratio needed for resizing
		$this->_reductionX = $this->_maxWidth / $this->_orgWidth;
		$this->_reductionY = $this->_maxHeight / $this->_orgHeight;

		return 1;
	}

	function _setDimensions($crop) {

		if($crop)
			return $this->_getCropSize();

		else if (	($this->_orgWidth < $this->_maxWidth) &&
					($this->_orgHeight < $this->_maxHeight) )
		{

			$this->_thumbWidth = $this->_finalWidth = $this->_orgWidth;
			$this->_thumbHeight = $this->_finalHeight = $this->_orgHeight;

		} else if (($this->_reductionX * $this->_orgHeight) < $this->_maxHeight) {

			$this->_thumbHeight =
			$this->_finalHeight =
			ceil($this->_reductionX * $this->_orgHeight);

			$this->_thumbWidth = $this->_finalWidth = $this->_maxWidth;

		} else {

			$this->_thumbWidth =
			$this->_finalWidth =
			ceil($this->_reductionY * $this->_orgWidth);

			$this->_thumbHeight = $this->_finalHeight = $this->_maxHeight;

		}

		return;
	}


	function _getCropSize() {

		$this->_thumbWidth = $this->_maxWidth;
		$this->_thumbHeight = $this->_maxHeight;

		// image is too small and should be resized but not cropped
		if($this->_orgWidth < $this->_maxWidth || $this->_orgHeight < $this->_maxHeight)
			$this->error(2);

		if($this->_reductionY > $this->_reductionX)
		{
			// changing final dimensions to new size
			$this->_finalWidth *= $this->_reductionY;
			$this->_finalHeight *= $this->_reductionY;
			// get thumbnail sizes and adjust alignment
			$this->_setCropArea('w');

		} else {

			$this->_finalWidth *= $this->_reductionX;
			$this->_finalHeight *= $this->_reductionX;
			$this->_setCropArea('h');

		}

		return 1;
	}

	function _setCropArea($default) {

		// Lets reset our vars because its polite
		$this->_dstX = 0;
		$this->_dstY = 0;

		switch($this->_cropAlign)
		{
			case 1:
				// all zeros
				break;

			case 2:
				$this->_dstX = ($this->_finalWidth - $this->_thumbWidth) * -1;
				break;
			case 4:
				$this->_dstY = ($this->_finalHeight - $this->_thumbHeight) / -1;
				break;
			case 3:
				$this->_dstX = ($this->_finalWidth - $this->_thumbWidth) / -1;
				$this->_dstY = ($this->_finalHeight - $this->_thumbHeight) / -1;
				break;
			default:

				if($default == 'w')
					$this->_dstX = ($this->_finalWidth - $this->_thumbWidth) / -2;

				if($default == 'h')
					$this->_dstY = ($this->_finalHeight - $this->_thumbHeight) / -2;

		}

	}

	function _checkCache() {

		$dir = str_replace('/', '', $this->_orgImgPath);

		$this->_resizedPath = 	$this->_cacheDir.
								$dir . '_' .
								$this->_thumbWidth.'x'.
								$this->_thumbHeight.'-'.
								$this->_crop.'-'.
								$this->_cropAlign; // path to cached file

		// first check cache
		$orgImgModDate = @filemtime($this->_orgImgPath); // org file mod date
		$resizedModDate = @filemtime($this->_resizedPath); // cached file mod date

		// if thumbnail is newer than image then output cached thumbnail and exit
		if($orgImgModDate < $resizedModDate) {
			// Output image
			$this->headerCheck();

			header("Content-type: image/jpeg");
			header("Cache-Control: max-age=63072000");
			header("Accept-Ranges: bytes");

			// set header mod date for browser cache
			$resizedModDate = $resizedModDate - 86400 * 7;
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $resizedModDate ) . " GMT");
			// calc expiration date // add a year
			//$ts =  strtotime( $resizedModDate );
			// add a year
			$ts = time() + 86400 * 365 * 2;
			header("Expires: " . gmdate( "D, d M Y H:i:s", $ts ) . " GMT");

			readfile($this->_resizedPath); // read and output

			// if found return true
			return true;
		}
		// not found = false
		return false;

	}

	function _createImg($src) {

		// set up canvas
		$dst = imagecreatetruecolor($this->_thumbWidth,$this->_thumbHeight);

		imageantialias ($dst, true);

		// copy resized image to new canvas
		imagecopyresampled (
								$dst,
								$src,
								$this->_dstX,
								$this->_dstY,
								$this->_srcX,
								$this->_srcY,
								$this->_finalWidth,
								$this->_finalHeight,
								$this->_orgWidth,
								$this->_orgHeight
							);

		//$color = imagecolorallocate($dst, 255, 255, 255);

		// add watermark
		if( $this->_thumbWidth > 199 ) {

			$x =  $this->_finalHeight - 17;
			$y = 20; //$this->_finalWidth - 120;

			$mark = $this->watermark . $this->uid;
			$text_color1 = imagecolorallocatealpha($dst, 250, 250, 250, 30);
			$text_color2 = imagecolorallocatealpha($dst, 0, 0, 0, 50);
			imagestring($dst, 2, ($y + 1), ($x - 1), $mark,$text_color2);
			imagestring($dst, 2, $y, $x, $mark,$text_color1);

		}
		return $dst;

	}

	function _getExt($file) {
		return strtolower(substr(strrchr($file, '.'), 1)); // get the file extension
	}

	function _getImgSrc($ext) {

		switch ($ext)
		{
			case 'jpg': // jpg
				$src = @imagecreatefromjpeg($this->_orgImgPath) or $this->error(1);
				break;
			case 'jpeg': // jpg
				$src = @imagecreatefromjpeg($this->_orgImgPath) or $this->error(1);
				break;
			case 'png': // png
				$src = @imagecreatefrompng($this->_orgImgPath) or $this->error(1);
				break;
			case 'gif': // gif
				$src = @imagecreatefromgif($this->_orgImgPath) or $this->error(1);
				break;
			default:
				$this->error(3);
		}

		return $src;
	}

	function _outputImg($dst) {

		// check that headers have not been sent yet
		$this->headerCheck();


		// header output
		if($this->_errorReport() && $debug == 1) {
			echo "An error occurred";
		} else {

			// send the header and new image
			header("Content-type: image/jpeg");
			header("Cache-Control: max-age=63072000");
			header("Accept-Ranges: bytes");

			// set header mod date for browser cache


			$orgImgModDate = @filemtime($this->_orgImgPath);
			$orgImgModDate = $orgImgModDate - 86400 * 7;
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $orgImgModDate ) . " GMT");
			// calc expiration date // add a year
			//$ts =  strtotime( $resizedModDate );
			// add a year
			$ts = $orgImgModDate + 86400 * 365 * 2;
			header("Expires: " . gmdate( "D, d M Y H:i:s", $ts ) . " GMT");

		}

		return imagejpeg($dst, NULL, $this->_quality);
	}

	// write the file to cache
	function _saveImgToCache($dst) {
		return imagejpeg($dst, $this->_resizedPath, $this->_quality);
	}

	function _destory($src) {
		return imagedestroy($src); // clear out the allocated resource
	}

	function _checkType($file) {
		return eregi(".($this->checkDocType)$", $file);
	}

	function headerCheck() {
		// show error when headers already sent out
		if (!headers_sent())
			return true;

		// try to get line number/filename (extra headers_sent params only exists in PHP 4.3+)
		$extraInfo = '';

		if (function_exists('version_compare') && version_compare('4.3.0', phpversion(), '<='))
		{
			headers_sent($hsFile, $hsLine);
			$extraInfo = ' in <code>'.$hsFile.'</code> line <code>'.$hsLine.'</code>';
		}

		$this->startUpError(
			'<p>The page headers have already been sent out'.$extraInfo.
			'. This could cause Image Resizer
			not to work in the expected way.</p>
			<p>Usually, this is caused by spaces or newlines at the end
			of the resizer.php file, at the end of the language file or
			at the end of a plugin file. Please check this and try again.</p>',
			'Page headers already sent'
		);
		exit;

	}

	function _errorListen() {
		$this->_errorListener = true;
	}

	function _errorReport() {
		return $this->_errorListener;
	}

	/**
	 * Errors before the database connection has been made
	 */
	function startUpError($msg, $title) {
		?>
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head><title><?php echo htmlspecialchars($title)?></title></head>
			<body>
				<h1><?php echo htmlspecialchars($title)?></h1>
				<?php echo $msg?>
			</body>
		</html>
		<?php	exit;
	}

	function error($code=0) {

		$this->_errorListen();

		switch($code)
		{
			// couldn't locate image
			case 1:
				$error = "Couldn't find";
				$error2 = "image";
				//$error2 = "We'll fix it later";
				break;
			// cropping an image that is too small
			case 2:
				$error = " Image is";
				$error2 = ' too small';
				//$error2 = "Should be $this->_maxWidth x $this->_maxHeight, but is $this->_orgWidth x $this->_orgHeight";
				break;
			// unknown doc type
			case 3:
				$error = " Don't know '$this->_imgType' ";
				break;
			// permission read issue, results in devision by 0, so we exit with error
			case 4:
				$error = " Reading images";
				$error2 = " is hard";
			// default
			default:
				$error = " Something went wrong";
				$error2 = " That's all we know";
		}

		$this->headerCheck();
		$dst = imagecreatetruecolor($this->_thumbWidth,$this->_thumbHeight);
		$src = imagecreatefrompng($this->_errorImgPath);

		$grey = imagecolorallocate($dst, 102, 102, 102);

		imagefilledrectangle($dst, 0, 0, $this->_thumbWidth, $this->_thumbHeight, $grey);


		imageantialias($dst, true);


		imagecopyresampled (
								$dst,
								$src,
								( 0 ),
								( $this->_thumbHeight - 216 ),
								0,
								0,
								216,
								216,
								216,
								216
							);


		//$trans_colour = imagecolorallocatealpha($dst, 200,200,200,254);
		//imagefill($dst, 10, 10, $trans_colour);
		$fontSize = 0;
		if($this->_thumbWidth > 100)
			$fontSize = 1;
		if($this->_thumbWidth > 200) {
			$fontSize = 2;
			$error .= ' ' . $error2;
			$error2 = null;
		}

		$text_color = imagecolorallocate($dst, 220, 220, 220);
		imagestring($dst, $fontSize, 10, 5, $error, $text_color);
		imagestring($dst, $fontSize, 10, 20, $error2, $text_color);
		$this->_outputImg($dst);

		exit;
	}

}

?>
