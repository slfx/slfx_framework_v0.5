<?php

class SHORTEN
{

	// vars
	private $id = null;
	private $url_long = null;
	private $url_short = null;
	private $base = null;
	private $limit_ip = null; // set to world
	private $tracking = true;
	public $check_url = null;
	private $caching = true;
	private $cache_dir = './slfx_framework/cache/shortlinks/';

	private $allowed_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


	// contructor
	public function __construct( $mode = 'lookup', $url = null, $check_url = false, $parent ) {
		global $DIR_ROOT, $CONF;
		/**
		  * uncomment this and reload site
		  * to clear db.
		  **/
		//$this->clearDb();
		$this->parent = $parent;
		$this->base = ''; // . $_SERVER['HTTP_HOST'] . '/';
		$this->limit_ip = $_SERVER['REMOTE_ADDR'];
		//$this->cache_dir = $DIR_ROOT . 'cache/shortlinks';
		$this->check_url = $check_url;


		// gets long url for redirecting
		if($mode == 'redirect')
			return $this->load($url);

		// short circuit to create
		if($mode == 'create')
			return $this->store($url);

		// gets long url for redirecting
		if($mode == 'lookup')
			return true;	
  	}

  	public function setParent($parent) {
    	$this->parent = $parent;
  	}

	public function getShortUrl() {
	
		if( !empty( $this->url_short ) )
			return 'http://'. $this->parent->page_status['short_url'] . '/' . $this->url_short;

		return false;
	}


	public function redirect() {

		header('HTTP/1.1 301 Moved Permanently');

		if( empty($this->url_long) ) {
			header('Location: ' . $this->parent->page_status['url_index'] );
			exit;
		}

		header('Location: ' .  $this->url_long);
		exit;

	}


	public function lookupSurl($url_long) {

		$this->url_long = $url_long;

		// check if record exists and if so offer it
 		if( true == ( $r = $this->recordExists() ) )
 			return $this->url_short = $this->getSurlFromId( (int)$r );

		return false;
	}


	private function load($url) {

		if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $url))
			return false;

		$this->id = $this->getIdFromSurl($url);

		if($this->caching)
			$this->cache();

		$this->url_long = $this->parent->db->quickQuery( 'SELECT surl_long as result FROM ' .
		$this->parent->db->useTable('shorturls') . ' WHERE sid = "' . $this->id . '"', 'Load long URL');

		if($this->tracking)
			$this->track();

		return true;
	}


	private function store( $url_long ) {
		global $CONF;

		// if class var limit_ip is limiting short linking
		if($_SERVER['REMOTE_ADDR'] != $this->limit_ip)
			return false;

		// if url is empty
		if( empty( $url_long ) )
			$url_long = $this->parent->page_status['url_index'];

		/**
		* if the url contains the shortener's
		* url don't allow recursive linking
		*/
		if( strstr( $url_long, $this->parent->page_status['short_url'] ) )
			return false;

		// don't allow just http:// to be a link
		if($url_long == 'http://')
			return false;

 		$this->url_long = $url_long;

 		// check if record exists and if so offer it
 		if( true == ( $r = $this->recordExists() ) )
 			return $this->url_short = $this->getSurlFromId( (int)$r );
 		
 		// if we are checking url for validity
 		if( true != ( $r = $this->checkUrl() ) )
 			die( 'Link validity could not be verified' );


 		// if not create it
 		$this->url_short = $this->createRecord();
 		return true;

	}


	private function getSurlFromId( $integer, $base = null ) {
		
		if( empty( $base ) )
			$base = $this->allowed_chars;

		$length = strlen( $base );

		$out = null;
		while( $integer > $length - 1 ) {
			$out = @$base[ fmod( $integer, $length ) ] . $out;
			$integer = floor( $integer / $length );
		}

		return @$base[ $integer ] . $out;
	}


	function getIdFromSurl($string, $base = null ) {

		if( empty($base) )
			$base = $this->allowed_chars;

		$length = strlen($base);
		$size = strlen($string) - 1;
		$string = str_split($string);
		$out = strpos($base, array_pop($string));

		foreach($string as $i => $char) {
			$out += strpos($base, $char) * pow($length, $size - $i);
		}

		return $out;
	}


	private function track() {
		$r = $this->parent->db->query('UPDATE ' . $this->parent->db->useTable('shorturls') .
		' SET sreferrals=sreferrals+1 WHERE sid="' . addslashes($this->id) .
		'"', 'track short URL usage');
		return $r;
	}


	// everything else if private
	private function checkUrl() {

		if(!$this->check_url)
			return true;

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->url_long );
		curl_setopt( $ch,  CURLOPT_RETURNTRANSFER, true );

		$r = curl_exec($ch);


		$flag = true;
		if( !curl_getinfo( $ch, CURLINFO_HTTP_CODE ) == '404' )
			$flag = false;

		curl_close($ch);

		return $flag;

	}



	private function recordExists() {

		/*if( !table_exists('shorturls') ) {
			sql_query( "CREATE TABLE `nuke_shorturls` (
 				`sid` int(10) unsigned NOT NULL auto_increment,
				`surl_long` varchar(255) NOT NULL,
		 		`screated` int(10) unsigned NOT NULL,
		  		`screator` char(15) NOT NULL,
		  		`sreferrals` int(10) unsigned NOT NULL default '0',
		  		PRIMARY KEY  (`sid`),
		  		UNIQUE KEY `long` (`surl_long`),
		  		KEY `referrals` (`sreferrals`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			);
		}*/

		$r = $this->parent->db->quickQuery( 'SELECT sid as result FROM ' . $this->parent->db->useTable('shorturls') .
		' WHERE surl_long ="' . $this->url_long . '"', 'check if long URL exists');

		if( $r )
			return $r;

		return false;
	}


	private function createRecord() {

		// URL not in database, insert
		$this->parent->db->query('LOCK TABLES ' . $this->parent->db->useTable('shorturls')  . ' WRITE;', 'lock short url table');
		
		$r = $this->parent->db->query('INSERT INTO ' . $this->parent->db->useTable('shorturls') .
		' (surl_long, screated, screator) VALUES ("' . addslashes($this->url_long) .
		'", "' . time() . '", "' . addslashes( $_SERVER['REMOTE_ADDR'] ) . '")', 'insert into short URL table');
		
		$this->url_short = $this->getSurlFromID( mysql_insert_id() );

		$this->parent->db->query('UNLOCK TABLES', 'unlock');

		return $this->url_short;
	}


	private function cache() {
		if(is_file($this->cache_dir . $this->id))
			$url_long = file_get_contents($this->cache_dir . $this->id);

		if( empty( $url_long ) || !preg_match( '|^https?://|', $url_long ) ) {

			$r = $this->parent->db->quickQuery( 'SELECT surl_long as result FROM ' .
			$this->parent->db->useTable('shorturls') . ' WHERE sid = "' . $this->id .
			'"', 'no cache for link, lookup long URL');
			//@mkdir(CACHE_DIR, 0777);

			$handle = fopen($this->cache_dir .'/'. $this->id, 'w+');

			fwrite($handle, $r);
			fclose($handle);
		}

		return true;

	}


	private function clearDb() {

		$query = 'TRUNCATE TABLE '. $this->parent->db->useTable('shorturls');

		if(!$this->parent->db->query($query))
			echo 'Error emptying database<br />';

		$this->url_long = $this->parent->page_status['url_index'];

 		// check if record exists and if so offer it
 		if(true == ( $r = $this->recordExists() ) )
 			return $this->url_short = $this->getSurlFromId($r);

 		// if not create it
 		$this->url_short =  $this->createRecord();

		return true;

	}


}

?>
