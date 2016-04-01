<?php


/**
  * Directory Listing Class
  *
    * php 4
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
  * @category   HTML Navigation
  * @package    DIRLIST
  * @author     R. Fritz Washabaugh <w.fritz@gmail.com>
  * @copyright  2009 R. Fritz Washabaugh
  * @license    http://creativecommons.org/licenses/by/2.5/legalcode CC 2.5
  * @link       http://fritzw.com/Programming/do/dirlist
  *
  **/

/**
  * Directory Listing Class
  *
  * @version	Release: 1.01
  * @link		http://fritzw.com/Programming/do/dirlist
  * @ModDate	2010-01-28
  *
  **/

// {{{ DIRLIST
/**
  * Directory Listing Class
  *
  * Return all the directories in a
  * directory contained in an array.
  *
  * You can list directories, list files along side directories, sort, get
  * the entire list, get one item at a time, and more. Pretty easy to use and
  * won't create a bunch of nasty errors if the root directory doesn't exist.
  *
  * Recurse perameter is still in alpha so I recommend you keep
  * it set to false.
  *
  *
  *
  *	Usage example:
  <code>
  		include_once('DIRLIST.php');
  		$dirlist = new DIRLIST($baseDir, true, false, true);

		//call the parser
  		echo '<ul>', $this->parseArry($dirlist->getList()) , '</ul>';

  		// output: string
  		function parseArry($arry, $class=NULL) {

		//vars
		$navStr = NULL;
		$label = NULL;

		if(!is_array($arry))
			return false;

		if($class)
				$class = " class=\"$class\"";

		foreach($arry as $label => $item) {

			//vars
			$current = NULL;
			$labelNew = NULL;
			$itemNew = NULL;

			if(rtrim($label,'/') == $URL_PATH) $current = ' id="current"';

			$itemNew = str_replace($DOCUMENT_ROOT, '/',$item);
			$labelNew = str_replace('_', ' ' , rtrim($label,'/'));

			// if this is subdirectory treat it like such.
			if(is_array($itemNew)) {

				$navStr .= '<li class="open"'.$current.'><a href="/' . $label . '">'. $labelNew.'</a>' ."\n";
				$navStr .= '<ul class="sublist">'."\n";
				$navStr .= $this->parseArry($itemNew);
				$navStr .= '</ul></li>'."\n";
				continue;

			}

			$navStr .= '<li><a href="'.$itemNew.'"'.$class.'>'.$labelNew.'</a></li>' . "\n";


		}

		return $navStr;

	}

  </code>
  *
  *
  *
  *
  * Version Changes:
  *
  * v1.01 - 2010/01/28
  * Bug fix: logic error when listing invisible files caused only invisible files to list. Fixed.
  *
  *
  *
  * @author R. Fritz Washabaugh
  * @package DIRLIST
  * @access public
  **/

class DIRLIST
{
	//{{{ members

   /**
     * Renderer object.
     *
     * @access private
     * @var array
     */
	var $_dirArry; // final array


	var $_directory; // dir path
	var $_opDir; // open dir reference

	var $_files; // include files in list
	var $_recurse;
	var $_dirSort = false;

	//}}}

	//(constructor)
	function DIRLIST($directory, $sort=false, $files=false, $recursion=false, $invisibles=false) {

		$this->_files = $files;
		$this->_recurse = $recursion;
		$this->_invisibles = $invisibles;

		// wish I knew eregi better
		$this->_directory = rtrim($directory, '/') .'/';
		$this->_isDir = $this->_buildList();
		$this->_dirSort = $sort;

		if($sort)
			$this->_dirSort();

	}

	//(public)
	function getList() {
		if(!$this->_isDir)
			return false;

		return $this->_dirArry;
	}

	//(private)
	function _validPath() {
		return is_dir($this->_directory);
	}

	function _buildList() {
		// open - if fail return false
		if(!$this->_open())
			return false;

		// pass file content to var for use
		$this->_getContents($this->_opDir);
		$this->_close();

		return true;
	}

	// Do not include 'this' and 'parent' directory aliases
	function _checkDir($dir) {
		if($this->_invisibles)
			return ('.' != substr($dir, 0,1));

		return ('.' != substr($dir, 0,1) && '_' != substr($dir, 0,1));


	}

	function _open() {
		// real dir?
		if(!is_dir($this->_directory))
			return false;
		// does it open?
		$this->_opDir = opendir($this->_directory);
		return $this->_opDir;
	}

	// all done close dir
	function _close() {
		closedir($this->_opDir);
	}

	// read each item of dir and pass it to add to array func
	function _getContents($opDir) {

		while(false != ($item = readdir($opDir))) {

			$this->_addDir($this->_directory,$item);

		}
	}

	// if dir has more then one dir - sort
	function _dirSort() {
		if($this->_dirCount() < 1)
			return false;

		if($this->_dirSort == 2)
			return krsort($this->_dirArry);

		return ksort($this->_dirArry);


	}

	function _dirCount() {
		return count($this->_dirArry);
	}

	// pass the parent dir name/ and each item from open dir
	function _addDir($dir,$item) {

		// check valid dir and file?
		if(!is_dir($dir.$item) && !is_file($dir.$item))
			return false;

		// if it is a file and we aren't including files
		if(is_file($dir.$item) && !$this->_files)
			return false;

		// if dir is self or parent don't include
		if(!$this->_checkDir($item))
			return false;

		// passed tests so -> array
		$this->_dirArry[$item] = $dir.$item;


		// this might get heavy -- be careful
		if( $this->_recurse == 1 ) {

			$_dirlist = new DIRLIST($this->_dirArry[$item], $this->_dirSort, $this->_files, false );

			if($_dirlist->_dirCount() > 0)
				$this->_dirArry[$item] =  $_dirlist->getList();

			/**
			  * destroy the list after we are done to
			  * preserve resources during recursion.
			  **/
			unset ($_dirlist);

		}
	}
}



?>