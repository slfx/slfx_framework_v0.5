<?php

/**
  *
  *	$source = 'media/_trash/';
  *
  *	include_once( $DIR_LIBS . 'SYSOPS.php' );
  *	$sys = new SYSOPS();
  *	echo $user = $sys->getSystemUsr();
  *	echo '<br /> ' . $sys->getFileOwner('crontab.php');
  *	echo '<br /> ' . $sys->checkPerm($source);
  *
  */

class SYSOPS
{
	private $benchmark = 0;
	private $sys_usr = null;



	public function  __construct() {
		//$this->sys_usr = $this->_getSystemUsr();

	}


	public function getSystemUsr() {
		return $this->sys_usr;

	}


	public function getFileOwner($file) {
		return fileowner( $file );
	}


	public function checkPerm($source) {
		return substr( decoct( fileperms( $source ) ), 2 );
	}


	/* Depreaceated
	private function _getSystemUsr() {
		return posix_getuid();
	}
	*/

	public function changeOwner($source, $user) {

		//log the action
		LOG::staticWriteBasic( 'system', getIp() . ' - system changed owner - "' . $source . '"');


		return chown($source, $user);
	}


	function _changePerm($file, $perm) {
		return chmod($file, '0' . $perm );
	}


	function makeDir( $dir ) {
		global $DIR_LIBS;

		if ( file_exists( $dir ) )
			return true;

		//echo $dir . '<br />';

		// Creating Folder 644
		$oldumask = umask( 022 );
		$ok = mkdir( $dir, 0777 );
		umask( $oldumask );

		if ( !file_exists( $dir ) )
			return false;

		return true;

	}


	function removeDir( $dir ) {

		if ( !file_exists( $dir ) )
			return false;

		rmdir( $dir );

		//log the action
		LOG::staticWriteBasic( 'system', getIp() . ' - system removed directory "' . $dir . '"');


		if ( file_exists( $dir ) )
			return false;

		return $dir;

	}


	function stopClock() {
		$time = round(microtime('get_as_float') - $this->start, 7);
		$this->benchmark += $time;
	}


	function startClock() {
		$this->start = microtime('get_as_float');
	}


	function getRunTime() {
		return $this->benchmark;

	}



}


?>