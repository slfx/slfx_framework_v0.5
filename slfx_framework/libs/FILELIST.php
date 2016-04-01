<?php

/**
  * File Listing Class
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
  * @package    FILELIST
  * @author     R. Fritz Washabaugh <general@nucleusdevelopment.com>
  * @copyright  2009 Nucleus Development - R. Fritz Washabaugh
  * @license    http://creativecommons.org/licenses/by/2.5/legalcode CC 2.5
  * @link       http://www.nucleusdevelopment.com/code/do/filelist
  *
  **/

/**
  * File  Listing Class
  *
  * @version	Release: 1.1
  * @link		http://www.nucleusdevelopment.com/code/do/filelist
  * @ModDate	2010-11-30
  *
  **/

// {{{ FILELIST
/**
  * File Listing Class
  *
  * Return file list from a choosen
  * directory in array, as individual file names/paths, sorted,
  * mix file types, file search, etc.
  *
  *
  *	Usage example:
  * <code>
  * include_once('FILELIST.php');
  *
  * $imgList = new FILELIST(
  *								$dir,
								$sort=false,
								$shuffle=false,
							 	$fileTypeLimit=false,
							 	$limit=false,
							 	$offset=0,
							 	$invisibles = true
  *							);
  *
  *
  *	// use get list method in loop
  * while( false != ($image = $imgList->getNext()) ) {
  *
  *  $size = getimagesize($image);
  *  $width = $size[0];
  *  $height = $size[1];
  *
  *  $imgString .= "\n<img src=\"resizer.php?w=180&h=180&c=1&f=$image\"
  ¥							alt=\"\" width=\"$width\" height=\"$height\" />";
  *
  * }
  *
  * // you can request individual files one at a time
  * and you can traverse the array
  * echo $imgList->getNext(); 		// image1
  * echo $imgList->getNext();		// image2
  * echo $imgList->getCurrent(2);   // /path/to/current/image2
  * echo $imgList->getIndex();		// 5
  *	echo $imgList->getFileName();   // filename
  * echo $imgList->getLast();		// count($imgList array) -1
  *	echo $imgList->getCurrent();	// return $imgList array key
  * echo $imgList->getArry();		// array('1', '2','3');
  * echo $imgList->search('test'); // /path/to/file/test.php
  * echo $imgList->getFilesTotal(); 		// 12303
  *
  * // by default the file array will be populated with png|jpg|jpeg|gif|pdf doc types
  * how ever you can force your own limit: (this will only list file of the 'inc|doc' type)
  *
  * $imgList = new FILELIST($dir, 1, 0,'inc|doc', 5 );
  *
  *
  *	// just check if a file exists (filelist attributes must
  * be bool words not numeric)
  *	$fList = new FILELIST($dir,true,false,false,'jpg');
  *
  *
  * </code>
  *
  *
  *
  *
  * Version Changes:
  *
  * v1.1 - 2010/11/30
  * Feature Add: remove invisible files from list.
  *
  *
  *
  * @author R. Fritz Washabaugh
  * @package DIRLIST
  * @access public
  **/


class FILELIST
{
	//vars
	var $fileDir; // dir path
	var $_fileCount;
	var $_opDir;
	var $_fileArry; // array of files
	var $_invisibles; // bool
	var $_sortArry;
	var $_shuffleArry;
	var $_limit = false;
	var $_offset = -1;
	var $_counter = -1;
	// allowed file types
	var $_checkDocType = 'png|jpg|jpeg|gif|pdf|html|xls|rtf|doc|txt';


	//constructor
	function FILELIST(	$dir,
						$sort=false,
						$shuffle=false,
					 	$fileTypeLimit=false,
					 	$limit=false,
					 	$offset=0,
					 	$invisibles = true)
	{
		// trim following slash if present
		$this->fileDir = rtrim($dir, '/');
		$this->_sortArry = $sort;
		$this->_shuffleArry = $shuffle;

			$this->_counter = $offset - 1;
			$this->_offset = $offset - 1;

		if($limit)
			$this->_limit = $limit + 1;

		$this->_invisibles = $invisibles;

		// if this class is called to retreive specific file types
		if($fileTypeLimit)
			$this->_checkDocType = $fileTypeLimit;

		return $this->_getFileList();
	}

	//static
	function arryIndex($arry,$pos) {

	   if ( ($pos < 0) || ($pos >= count($arry)) )
			 return false;

	   reset($arry);

	   for($i = 0; $i < $pos; $i++) next($arry);
	   return key($arry);

	}

	//public methods

	function getNextFile() {
		return $this->getNext();
	}

	function getCurrentFile() {
		return $this->getCurrent( $this->getIndex() );
	}

	function getTotal() {
		return $this->getFilesTotal();
	}

	function getNext() {
		global $DIR_CONTENT;

		//incrument to next
		$this->_counter++;
		// check if we are going to overload the array
		if($this->_counter >= $this->_fileCount())
			return false;

		$this->_fileArry[$this->_byIndex($this->_counter)];
		// get array by custom key as number
		return $this->_fileArry[$this->_byIndex($this->_counter)];
	}

	// Search the arry for file and return with path or false
	function search($name, $fType=null) {
		
		if(count($this->_fileArry) > 0)
			foreach($this->_fileArry as $label => $path) {
				if( stristr( $label, $name . ($fType ? '.'. $fType : '' ) ) )
				 	return $this->fileDir.'/'.$label;
			}

		return false;
	}


	function getByName($name=null) {
		
		if( count( $this->_fileArry ) == 0 )
			return false;		

		
		reset($this->_fileArry);
			
		for($i = 0; $i < $this->getFilesTotal(); $i++) {
				
				if($name == ($r = key($this->_fileArry) ) ) {
					$this->getCurrent($i);
					return $r;
				}
				next($this->_fileArry);
				
			}
		
			return false;
		}


	function getFilesTotal() {
			return count($this->_fileArry);
	}

	function stripExt($file) {
		return eregi_replace("\.(.+)$", "", $file);
	}

	// get file name from file path
	function getFileName($file = null) {

		if($file == null)
			$file =	$this->_fileArry[ $this->_byIndex( $this->_counter ) ];

		$path = explode('/', $file);
  		return $path[count($path) - 1];
	}

	// get current file path
	function getCurrent($current=NULL) {

		if($current >= $this->_fileCount())
			return false;

		$this->_counter = $current;
		return  $this->_fileArry[$this->_byIndex($current)];
	}

	// miss titled - really 'get current' but that is taken...
	function getIndex() {
		return $this->_counter;
	}

	function getLast() {
		// get last file in array
		return end($this->_fileArry);

	}

	// export array outside of class
	function getArry() {
		return $this->_fileArry;
	}

	// sort array by key with out re keying
	function sortFiles() {
		if($this->_fileCount() < 1)
			return false;

		if($this->sortArry == 2)
			return asort($this->_fileArry);

		arsort($this->_fileArry);

	}

	// private methods

	/**
	  * fileCount will figure to values
	  * first: offset + limit which will likely cause overflow
	  * second: the array max index
	  * then choose the smaller of the two
	  **/

	function _fileCount() {
		$arryMax = $this->getFilesTotal();

		if($this->_limit) {
			$ilogicalMax = $this->_offset+$this->_limit;
			return $arryMax < $ilogicalMax ? $arryMax : $ilogicalMax;
		}

		return $arryMax;
	}

	function _checkVisible($file) {
		$file_array = explode( "/", $file );
		$file_name = $file_array[count($file_array)-1];

		if(!$this->_invisibles)
			return ('.' != substr($file_name, 0,1));

		return true;
	}


	function _getfileList() {
		$this->_opDir = $this->_open();

		if(!$this->_opDir)
			return false;

		$this->_getfiles();
		$this->_fileCount();
		$this->_sortFiles();
		$this->_shuffleFiles();
		$this->_close();
		return true;
	}


	function _shuffleFiles() {
		if($this->_fileCount() <= 1 )
			return false;

		if($this->_shuffleArry)
			shuffle($this->_fileArry);
	}


	// currently sorts newiest to oldest
	function _sortFiles() {
		if(!$this->_sortArry)
			return false;

		if($this->_fileCount() <= 1)
			return false;

		if($this->_sortArry == 2)
			return krsort($this->_fileArry);

		ksort($this->_fileArry);
	}


	// limiter in case you only want 4-5 files
	function _limitFiles() {
		while($this->checkLimit()) {
			array_pop($this->fileArry);
		}
	}


	function _open() {
		return @opendir($this->fileDir);
	}


	function _close() {
		closedir($this->_opDir);
	}


	// get files
	function _getFiles() {
		while ($file = readdir($this->_opDir)) {
			$this->_addFile($this->fileDir.'/'.$file);
		}
	}


	// add files to array
	function _addFile($file) {
		if(!$this->_isFile($file))
		 	return false;

		if(!$this->_checkVisible($file))
			return false;

		$this->_fileArry[$this->getFileName($file)] = $file;
	}


	function _checkLimit() {
		// is limit a far value
		if(!is_numeric($this->_limit))
			return false;

		// if we go over limit then limit is exceeded
		if($this->_fileCount() > $this->_limit)
			return true;
	}


	function _isFile($file) {
		if($file == '')
			return false;
				
		return @eregi(".($this->_checkDocType)$", $file);
	}


	function _byIndex($pos) {

	   //$pos--;
	   /* uncomment the above line if you */
	   /* prefer position to start from 1 */

	   if ( ($pos < 0) || ($pos >= $this->_fileCount($this->_fileArry)) )
			 return false;

	   reset($this->_fileArry);

	   for($i = 0; $i < $pos; ++$i) next($this->_fileArry);
	   return key($this->_fileArry);

	}


}


?>