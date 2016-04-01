<?php

/**
  * File Get and Mod Class
  *
  * php 4,5
  *
  *
  * LICENSE: Use of this library is governed by the Creative Commons License Attribution 2.5.
  * You can check it out at: http://creativecommons.org/licenses/by/2.5/legalcode
  *
  * - You may copy, distribute, and eat this code as you wish. You must give me credit for
  * writing it. You may not misrepresent yourself as the author of this code.
  * - You may not use this code in a commercial setting without prior consent from me.
  * - If you make changes to this code, you must make the changes available under a license like
  * this one.
  *
  * @category   File Managment
  * @package    FILE
  * @author     R. Fritz Washabaugh <general@nucleusdevelopment.com>
  * @copyright  2009 Nucleus Development - R. Fritz Washabaugh
  * @license    http://creativecommons.org/licenses/by/2.5/legalcode CC 2.5
  * @link       http://www.nucleusdevelopment.com.com/code/do/file
  *
  **/

/**
  * File Get and Mod Class
  *
  * @version	Release: 1.11
  * @link		http://www.nucleusdevelopment.com.com/code/do/file
  * @ModDate	2011-05-23
  *
  **/

// {{{ FILE
/**
  * File Get and Mod Class
  *
  * Return file contents
  * Read and write to files easily.
  *
  *
  * Usage example:
  * <code>
  *
  * include_once('FILE.php');
  *
  *
  * echo $file->getSize() . $file->sizeUnit; // 20 MB
  * echo $file->get() // file contents
  * echo $file->cat('test') // file contentstest
  * echo $file->put() // writes file contentstest
  * echo $file->erase() // deletes file
  *	echo $file->mv(new loc); moves file
  *
  * // just get a file contents
  * $file = new FILE('test.html');
  * return  $file->get();
  *
  * or
  *
  * if(false != ($contents = $file->get())
  *		do something with $contents
  *
  * or
  *
  *
  * $file = new FILE($ipSrc);
  *
  *	// get the file content
  *	$fileOP = $file->get();
  *
  *	// append the file contents
  *	$fileOP = $file->cat(ipaddress().
  * '|' . date("Y-m-d - G:i:s") .
  * '|'. getenv("HTTP_USER_AGENT"). "\n");
  *
  *
  *
  *	// write the file to disk
  *	$file->put();
  *
  * // create and write a new file
  * $file = new FILE( $new_file_path );
  * $file->create('file content');
  *
  *
  * </code>
  *
  *
  *
  *
  * Version Changes:
  *
  * v1.1 - 2010/11/28
  *	Features Added: Get file size, file extension, etc.
  *
  *
  * v1.11 - 2011/05/23
  *	Bug fix: better error handling for file size method.
  *
  *
  * @author R. Fritz Washabaugh
  * @package FILE
  * @access public
  **/

// CONSTANTS
define('_FILE_NONEXISTENT',		'File doesn\'t exist. Process Failed.');

class FILE
{
	//vars
	var $sizeUnit;
	var $_fileLocation = null;
	var $status;
	var $_contents;
	var $_opFile;

	//constructor
	function FILE($file) {
		$this->_fileLocation = $file;
	}

	//public medthods
	function mv($new_path=null, $file=null) {

		if($file == null)
			$file = $this->_fileLocation;

		if($new_path == null) return false;

		return rename($file, $new_path);
	}


	function erase() {

		if ( !is_file( $this->_fileLocation ) )
			return _FILE_NONEXISTENT;
		else
			@unlink($this->_fileLocation);

		//log the action
		LOG::staticWriteBasic( 'system', getIp() . ' - system removed file "' . $this->_fileLocation . '"');


		return !is_file( $this->_fileLocation );
	}


	function create( $content = null ) {
		
		// check if file exists don't create just put
		if( $this->_exists( $this->_fileLocation ) )
			return $this->put($content);

		if($content == null)
			$content = $this->_contents;

		// create the file. Then close
		if( false == ( file_put_contents ( $this->_fileLocation, $content ) ) )
			return false;
		
		return true;
	}


	function put($content=false) {

		if(false == ($this->_opFile = $this->_open('w+') ) )
			return $this->_opFile;

		if(!$content)
			$content = $this->_contents;

		return $this->_write($content);

	}


	function get($permission='r') {
		// open file with read permissions
		$this->_opFile = $this->_open($permission);

		// no file = return false
		if(!$this->_opFile)
			return false;

		// read and pass to var
		$this->_read();
		// this need some work, does some trimming
		$this->_prepareGet();

		$this->_close();

		return $this->_contents;
	}


	// concatenate current files contents
	function cat($add) {
		return $this->_contents .= $add;
	}


	function stripExt() {
		return eregi_replace("\.(.+)$", "", $this->_fileLocation);
	}


	function getExt($file=null) {

		if($file == null)
			$file = $this->_fileLocation;

		return end(explode('.', $file) );

	}


	function getPath($file=null) {

		if($file == null)
			$file = $this->_fileLocation;

		$paths = explode('/', $file);

		//pop off file name
		array_pop($paths);

		// return path string
		return implode( '/', $paths );
	}


	// get file name from file path
	function getFileName($strip_ext=false, $file=null) {
		
		if($file == null) {
			// check if this is being called as static function
			if( get_class($this) != 'FILE' )
				return false;
				
			$file = $this->_fileLocation;
		}
			
		$path = explode('/', $file);
		
		if($strip_ext)
			return preg_replace( '/\.[^.]*$/', '', $path[count($path) - 1] );
					 
  		return $path[count($path) - 1]; 
	}
	
	static function getFileNameStatic($strip_ext=false, $file=null) {
		
			
		$path = explode('/', $file);
		
		if($strip_ext)
			return preg_replace( '/\.[^.]*$/', '', $path[count($path) - 1] );
					 
  		return $path[count($path) - 1]; 
	}
	


	function getSize($size = null) {

		// get file size
		if( $size == null && $this->_exists( $this->_fileLocation ) )
			$size = filesize($this->_fileLocation);

		//GB
		if($size > 1000000000) {
			//$this->sizeUnit = 'BG';
			return round($size/1000000000,2) .'GB';
		}

		//MB
		if($size > 100000) {
			//$this->sizeUnit = 'MB';
			return round($size/1000000,2) .'MB';
		}

		//KB
		if($size > 1000) {
			//$this->sizeUnit = 'KB';
			return round($size/1000,1) .'KB';
		}

		return round($size/1,1) .'B';


	}


	function getFileDate() {
		if( $this->_exists( $this->_fileLocation ) )
			return date( "Y-m-d H:i:s", filemtime( $this->_fileLocation ) );

		return false;
	}


	//(private methods)
	function _close() {
		@fclose($this->_opFile);
	}


	// file path to var
	function _getLocation() {
		return $this->_fileLocation;
	}


	function _open($permission='r') {
		return @fopen($this->_fileLocation, $permission);
	}


	function _exists() {
		return file_exists($this->_fileLocation);
	}


	/*function flattenContents() {

		$fileLines = array();
		$fileLines = explode("\r", $this->contents);

		for($i = 0; $i <= count($fileLines); $i++) {
			$this->contents .= $fileLines[$i] . "\r\n";
		}
	}*/


	function _prepareGet() {
		//$content = htmlentities($this->contents);
		//$this->contents = removeBreaks($content);

		$this->_trimContents();
	}


	// needs a lot of work.
	function _preparePut() {
		/*
		$fileContent = undoMagic($fileContent);
		$fileContent = str_replace("\n", '', $fileContent);
		*/
	}


	function _trimContents() {
		return trim($this->_contents);
	}


	function _read() {
		$this->_contents = @fread($this->_opFile, filesize($this->_fileLocation));
	}


	function _write($content) {
		return @fwrite($this->_opFile, $content);
	}

}

?>