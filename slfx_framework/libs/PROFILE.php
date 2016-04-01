<?php

/**
  * We store all data needed for profiles here.
  * Primary data is also stored in member table
  * for easy searching.
  * There is redundancy between these to tables.

  CREATE TABLE `silverfox_profile` (
  `pkey` int(10) NOT NULL auto_increment,
  `pmember` int(8) NOT NULL default '0',
  `ptype` int(5) NOT NULL default '0',
  `pdata` varchar(512) NOT NULL default '',
  `ptime` datetime NOT NULL default '0000-00-00 00:00:00',
  `pcomment` varchar(128) NOT NULL,
  PRIMARY KEY  (`pkey`)
) ENGINE=MyISAM AUTO_INCREMENT=638 DEFAULT CHARSET=utf8 AUTO_INCREMENT=638 ;

  *
  *
  */


class PROFILE
{

	// vars
	private $db_dup_keys;
	private $db_single_keys;
	//private $limit_by_violators = false;
	private $profile_items = array();
	public $benchmark;
	public $profile_id = -1;

	public $pro_blocks = array();
	public $pro_friends = array();
	public $pro_views = array();
	public $pro_invite = array();

	public $pro_friends_data = array();


	private $member_friend_limit = 999;
	private $nonmember_friend_limit = 27;

	private $db_keys = array(
			'flag' => 			 	9000, // profile reported
			'friend' => 			1000, // member friends
			'block' => 				2000, // blocked members
			'profile' => 			3000, // null
				'profile_label' => 			3100, // profile label
				'profile_subtitle' => 		3200, // profile subtitle
				'profile_city' => 			3300, // profile city
				'profile_state' => 			3400, // profile state
				'profile_zip' => 			3500, // profile zip
				'profile_country' =>		3540, // profile country
				'profile_longlat' =>		3550, // long and lat
				'profile_introduction' => 	3600, // profile introduction
				'profile_birthday' => 		3700, // profile age
				'profile_height' => 		3800, // profile height
				'profile_weight' => 		3900, // profile weight
				'profile_prefer' => 		4000, // null
					'prefer_rolemin' => 			4010, // top or bottom min
					'prefer_rolemax' => 			4015, // top or bottom max
					'prefer_agemin' => 			4020, // minimum age limit
					'prefer_agemax' =>			4030, // maxium age limit
					'prefer_weightmin' => 		4040, // minium age
					'prefer_weightmax' => 		4050, // maximum age
				'profile_lookingfor' => 	4100, // null
					//'lookingfor_justsex' => 		4110, // ""
					//'lookingfor_friends' => 		4120, // ""
					//'lookingfor_dating' => 			4130, // ""
					//'lookingfor_relationship' =>	4140, // ""
				'profile_pictures' => 		4200,	// "null"
					'pictures_dir' => 			4210,	// member root img dir
					'pictures_main' => 			4220, // main picture face pic
					'pictures_main_cropped' =>		4221, // main picture face pic CROPPED!!
					'pictures_aux' =>			4230, // additional aux pics
					'pictures_xxx' => 			4240, // any dirty pictures
					'pictures_misc' => 			4250, // misc pictures used in stream
					'pictures_original' => 			4260, // any dirty pictures
				'profile_settings' => 		4300, // null
					'settings_visibilityby' =>	4310, // null
						'visibilityby_location' => 	4311, // location visibility state 1|2|3|4|5
						'visibilityby_relation' => 	4312, // relation visibility state 1|2|3|4 1 = no one , 4 = everyone
					'settings_photo_blur' => 	4320, // null
					'settings_notification' =>	4330, //
						'notification_post' =>		4331, // notify if post
						'notification_message' =>	4332, // notify if message
						'notification_friend' =>	4333, // notify if message
						'notification_comment-reply' => 4334, // notify if comments
					'settings_stream-source' =>	4340, // stream posts from 'all' or 'keepers'
				'profile_datecreated' => 	4400, // datetime of creation
				'profile_lastlogin' => 		4450, // datetime of lastlogin
				'profile_timesviewed' => 	4500, // times viewed
				'profile_whoviewedme' => 	4600,
				'profile_email' =>			4700,
				'profile_hwratio' =>		4800,
				'profile_position' =>		4900,
				'profile_text' =>			5000,
				'profile_score' =>			5100,
				'profile_posts' =>			5110,
			'invite' =>				6000, // email address of the invited
			'banned' =>				7000,
			'status' =>				8000,
			'lastip' =>		 		8100, //
			'lastfingerprint' =>	8200 //

		);


	// constructor
	public function __construct() {

		$this->setdbKeys();

		//$this->deleteAllMemberImages(129,'1/129/');
		//$this->_removeDupProfile( 135,300 );
		//$this->cleanupDb();
	}






	/**
	*	Profile Avatar
	*
	*/
	public function generateAvatarString($pro, $size = 48) {
		$item_class = 'profile_avatar avatar_'.$size;
		$str = '<img class="' . $item_class . '" src="/img.php?w='.$size.'&h='.$size.'&c=1&f=profile/photo.png" />';
		if( $pro->getProfile('pictures_main') != '' ) {
			$profile_image_name = $pro->getProfile('pictures_main');
			if( $pro->getProfile('pictures_main_cropped') != '' )
				$profile_image_name = $pro->getProfile('pictures_main_cropped');
			$str = '<img class="' . $item_class . '" src="/img.php?w='.$size.'&h='.$size.'&c=1&a=1&f=/' . $pro->getProfile('pictures_dir') . 'thumbs/'. $profile_image_name . '" />';
		}

		return $str;
	}
	/**
	*	End Profile Avatar
	*/







	/*
	// (static)
	function createFromName($username) {
		$prof =& new PROFILE();
		$prof->readFromName($username);
		return $prof;
	}


	// (static)
	function createFromID($id) {
		$prof =& new PROFILE();
		$prof->readFromID($id);
		return $prof;
	}
	*/


	private function stopClock() {
		$time = round(microtime('get_as_float') - $this->start, 7);
		$this->benchmark += $time;
	}


	private function startClock() {
		$this->start = microtime('get_as_float');
	}


	public function getRunTime() {
		return $this->benchmark;

	}


	// public
	public function checkCurrentMemStorageDir( $base_dir ) {
		global $DIR_LIBS;

		// recursion is turned off
		$dirlist = new DIRLIST( $base_dir, 1, false, false );

		// get the list and count directories in directories
  		// we only need to check the last directory
  		$arry = $dirlist->getList();

  		// get last directory
  		end($arry);
  		$last_dir = trim( key($arry), '/' );

  		// get count in last dir
  		$dir_list = new DIRLIST( $base_dir . $last_dir, 1, false, false );
  		$dir_list_arry = $dir_list->getList();

  		// if there are more than 1000 member dirs in
  		// storage dir. create a new one.
  		if( is_array( $dir_list_arry ) && count( $dir_list_arry ) > 500 ) {

  			// dirs are numbers so create the
  			// next dir in the list
  			$new_dir = (int)$last_dir + 1;

  			include_once( $DIR_LIBS . 'SYSOPS.php' );
  			$sys = new SYSOPS();

  			//echo $base_dir . $new_dir .'/' ;

  			// return new dir
  			if( true == ( $sys->makeDir( $base_dir . $new_dir ) ) ) {
  				return  $base_dir . $new_dir .'/';
  			}
  		}

  		// return existing dir
  		return $base_dir . $last_dir .'/';
  	}


	// create standard dir set
	public function checkMemberImageDir($dir) {
		global $DIR_LIBS;

		include_once( $DIR_LIBS . 'SYSOPS.php' );
  		$sys = new SYSOPS();

  	 	$cwd = $dir;

  		// make member dir
		if( !is_dir( $cwd ) ) {

  			// creating dir failed check dir path
  			if( false == ( $sys->makeDir( $cwd ) ) )
  				exit('0|Can\'t make "' . $cwd . '". Contact administrator.');

  		}

  		// make subdirectory for originals
		if( !is_dir(  $cwd . 'orig' ) )
  			$sys->makeDir(  $cwd . 'orig' );

  		// make subdirectory for img
		if( !is_dir( $cwd . 'img' ) )
  			$sys->makeDir( $cwd . 'img' );

  		// make subdirectory for originals
		if( !is_dir(  $cwd . 'thumbs' ) )
  			$sys->makeDir( $cwd . 'thumbs' );

		return $dir;
	}


	public function deleteAllMemberImages($uid, $img_dir) {
		global $DIR_ROOT, $DIR_LIBS;

		$img_dir = str_replace($DIR_ROOT, '', $img_dir);
		$img_dir = $DIR_ROOT . $img_dir;

		$dirs = array('orig/', 'thumbs/', 'img/');

		// error reporting deleted row
		//log the action
		LOG::staticWriteBasic( 'action', ' delete all member images - "'.$uid.'". directory = '  . $img_dir . '.');

		foreach( $dirs as $dir ) {
			// remove old thumbs
			$imgList = new FILELIST(
	  								$img_dir . '/' . $dir,
									$sort=1,
									$shuffle=false,
								 	$fileTypeLimit=false,
								 	$limit=false,
								 	$offset=false,
								 	$invisibles=true
		 							);

		 	// use get list method in loop
		  	while( false != ( $image = $imgList->getNext() ) ) {
			  	//echo 'found a file!! ' . $image;

				$file = new FILE( $image );
		  		$file->erase();
			}

			include_once( $DIR_LIBS . 'SYSOPS.php' );
	  		$sys = new SYSOPS();

			$sys->removeDir($img_dir .'/' . $dir);

		}

		$sys->removeDir($img_dir);

		$r = sql_query('DELETE FROM '. sql_table( 'profile' ) .' WHERE pmember=' . (int)$uid . ' AND ( pcomment="pictures_main" OR pcomment="pictures_aux" OR pcomment="pictures_xxx" )', 'delete all member images' );

		return $r;
	}

	/*

	public function readFromEmail($email) {
		return $this->readProfile("ptype='4700' AND pdata='".addslashes($email)."'");
	}
	*/


	public function readFromId($id, $active = true) {

		// banned is the oposite of active
		$banned = ( $active ? 0 : 1 );

		$this->setId( (int)$id );
		return $this->readProfile( "pmember=" . (int)$id, $banned );
	}


	public function getIDbyEmail($email) {
		return quickQuery('SELECT pmember as result FROM ' . sql_table('profile') . ' WHERE ptype="4700" AND pdata="' . addslashes($email) . '"  LIMIT 1', 'get id by email address');
	}


	// count number of email address in db to see how many accounts we have
	public function getTotalMembers() {
		return quickQuery('SELECT COUNT(*) as result FROM ' . sql_table('profile') . ' WHERE ptype="4700"');
	}


	private function getCurrentId() {
		return quickQuery('SELECT MAX(pmember) AS result FROM '. sql_table('profile') );
	}

	public function getEmailHash() {
		return md5( $this->getEmail() );
	}


	public function getId() {
		return $this->profile_id;
	}


	private function setId($id) {
		$this->profile_id = $id;
	}


	public function dbEnglish2Key($english) {
		return $this->db_keys[$english]; // returns number
	}


	public function dbKey2English( $key ) {

		reset($this->db_keys);

	 	for($i = 0; $i < count( $this->db_keys ); $i++) {
	   		if( $key == ( $r = next( $this->db_keys ) ) ) {

	   			return key( $this->db_keys );
	   		}
	   	}

	 	return false;
	}



	// private
	public function setdbKeys() {

		// Users can have multiple of these keys stored in the database.
		// If they are dup don't allow overwriting by uid and type… must overwrite
		// by data
		$this->db_dup_keys = array(
			'flag', // reported profiles
			'friend', // member friends
			'block', // blocked members
			'pictures_aux', // additional aux pics
			'pictures_xxx', // any dirty pictures
			'pictures_misc', // main picture waiting for approval
			'profile_whoviewedme', //
			'invite'
		);

		$this->db_single_keys = array(
			'profile_label' => null, // profile label
			'profile_subtitle' => null, // profile subtitle
			'profile_city' => null, // profile city
			'profile_state' => null, // profile state
			'profile_zip' => null, // profile zip
			'profile_country' => null, // profile country
			'profile_longlat' => null, // profile long and lat
			'profile_introduction' => null, // profile introduction
			'profile_birthday' => null, // profile age
			'profile_height' => null, // profile height
			'profile_weight' => null, // profile weight
			'profile_prefer' => null, // null
				'prefer_rolemin' => null, // top or bottom min
				'prefer_rolemax' => null, // top or bottom max
				'prefer_agemin' => null, // minimum age limit
				'prefer_agemax' => null, // maxium age limit
				'prefer_weightmin' => null, // minium age
				'prefer_weightmax' => null, // maximum age
			'profile_lookingfor' => null, // null
				//'lookingfor_justsex', // ""
				//'lookingfor_friends', // ""
				//'lookingfor_dating', // ""
				//'lookingfor_relationship', // ""
			'profile_pictures' => null,	// "null"
				'pictures_dir' => null,	// "null"
				'pictures_main' => null, // main picture face pic
				'pictures_main_cropped' => null, // main picture face pic
				'pictures_original' => null, // any dirty pictures
			'profile_settings' => null, // null
				'settings_visibilityby' => null, // null
					'visibilityby_location' => null, // location visibility state 1|2|3|4|5
					'visibilityby_relation' => 4, // relation visibility state 1|2|3|4 4==internet
				'settings_photo_blur' => null, // null
				'settings_notification' =>	null, //
					'notification_post' => 1, // notify if post
					'notification_message' => 1, // notify if message
					'notification_friend' => 0, // notify if friended
					'notification_comment-reply' => 0, // notify when someone comments on a comment
				'settings_stream-source' => 'all', // stream posts from 'all' or 'keepers'

			'profile_datecreated' => 0, // datetime of creation
			'profile_lastlogin' => 0, // datetime of creation
			'profile_timesviewed' => 0, // times viewed
			'profile_hwratio' => 0, // height weight ratio
			'profile_position' => 0, // bot bot/ver ver ver/top top
			'profile_email' => null,
			'profile_score' => 0,
			'profile_posts' => 0,
			'banned' => 0

		);

	}



	/**
	* Profile Scoring
	*
	* Used to quantify how much content
	* of a profile has been filled out.
	*
	*
	*
	*/

	private function writeScore($score) {
		if($this->profile_id > 0)
			return $this->writeparam( $this->profile_id, 5100, (int)$score ); // score
	}


	public function getScore() {

		if( !isset( $this->profile_items['profile_score'] ) )
			$this->profile_items['profile_score'] = 0;

		if( $this->profile_items['profile_score'] == 0 )
			$this->profile_items['profile_score'] = $this->verifyScore();

		return $this->profile_items['profile_score'];
	}

	public function verifyScore() {


		/**
		* Scoring
		*
		* profile visibility = 80
		* face picture = 40
		* location = 20
		* height = 10
		* weight = 10
		* position = 10
		* main text = 15
		* profile intro = 10
		* gallery pictures = 5 (each)
		* photo+ pictures = 5 (each)
		* preferences = 5 (each)
		*
		* New System
		*
		* Scoring
		*
		* profile visibility = 100000
		* face picture = 10000
		* cropped face picture = 10000
		* location = 1000
		* height = 10
		* weight = 10
		* position = 10
		* main text = 15
		* profile intro = 10
		* gallery pictures = 5 (each)
		* photo+ pictures = 5 (each)
		* preferences = 5 (each)
		*
		*/

		// vars
		$score = 0;

		foreach( $this->profile_items as $label => $item ) {

			switch($label) {
				case 'visibilityby_relation':
					if($item == 0 || $item == 3 || $item == 4)
						$score += 400000;

						//die('done!');

					break;
				case 'pictures_main':
					if($item != '')
					 	$score += 10000;
					break;

				case 'pictures_main_cropped':
					if($item != '')
					 	$score += 10000;
					break;

				case 'profile_longlat':
					if($item != '')
					 	$score += 1000;
					break;
				// 411,000
				case 'profile_subtitle':
					if($item != '')
						$score += 10;
					break;
				case 'profile_introduction':
					if($item != '')
						$score += 10;
					break;
				case 'profile_height':
					if($item != '')
						$score += 10;
					break;
				case 'profile_weight':
					if($item != '')
						$score += 10;
					break;
				case 'profile_position':
					if($item != '')
						$score += 10;
					break;

				// 411,050
				case 'prefer_role':
					if($item != '')
						$score += 5;
					break;
				case 'prefer_agemin':
					if($item != '')
						$score += 5;
					break;
				case 'prefer_agemax':
					if($item != '')
						$score += 5;
					break;
				case 'prefer_weightmin':
					if($item != '')
						$score += 5;
					break;
				case 'prefer_weightmax':
					if($item != '')
						$score += 5;
					break;
				case 'profile_lookingfor':
					if($item === 0 || $item > 0)
						$score += 5;
					break;
				// 411,080
				default:
			}
		}

		// update score - if there is a deviation of more than 20 points
		if( isset( $this->profile_items['profile_score'] ) == true && ( $this->profile_items['profile_score'] > $score + 20 || $this->profile_items['profile_score'] < $score - 20 ) )
			$this->writeScore($score);

		// if no score exists - create it.
		if( isset( $this->profile_items['profile_score'] ) == false )
			$this->writeScore($score);

		return $score;

	}


	// read from table
	private function readProfile($where, $banned = false ) {
		global $CONF;
		// read info
		$query =  'SELECT ptype,pdata FROM '. sql_table('profile') . ' WHERE ' . $where;
		// always put the newest data at the beginning...
		$query .= ' ORDER BY ptime DESC';
		// set default settings to be overwritten by db data
		$this->profile_items = $this->db_single_keys;
		$res = sql_query($query, 'full profile lookup');
		while( true == ( $obj = mysql_fetch_object($res) ) ) {
			if( !is_object( $obj ) )
				return false;
			// check if item type is multiple per member
			if( in_array( $this->dbKey2English( $obj->ptype ), $this->db_dup_keys ) ) {
				// check if 2D array exists, if not create it.
				if( !isset( $this->profile_items[ $this->dbKey2English( $obj->ptype ) ] ) ) {
					$this->profile_items[ $this->dbKey2English( $obj->ptype ) ] = array();
				}
				// add item to 2D array
				$this->profile_items[ $this->dbKey2English( $obj->ptype ) ][] = is_numeric($obj->pdata) ? (int)$obj->pdata : PROFILE::encodeBody( $obj->pdata );
			} else {
				// add item to 1D array
				$this->profile_items[ $this->dbKey2English( $obj->ptype ) ] = is_numeric($obj->pdata) ? (int)$obj->pdata : PROFILE::encodeBody( $obj->pdata );
			}
		}
		if( $banned == true ) {
			$this->profile_items[ 'banned' ] = 1;
		}
		// set aside blocks and friends because we will use them a lot later
		if( array_key_exists( 'friend', $this->profile_items ) )
			$this->pro_friends = $this->profile_items['friend'];
		if( array_key_exists( 'block', $this->profile_items ) )
			$this->pro_blocks = $this->profile_items['block'];
		if( array_key_exists( 'profile_whoviewedme', $this->profile_items ) )
			$this->pro_views = $this->profile_items['profile_whoviewedme'];
		if( array_key_exists( 'invite', $this->profile_items ) )
			$this->pro_invite = $this->profile_items['invite'];
		//$this->stopClock();
		return mysql_num_rows($res);
	}


	// read from member table, get single value
	public function lookupProfileValue($uid, $type) {
		global $CONF;

		// if no db then exit
		if (!$CONF['UseDB']) return false;

		// read info
		$query = 'SELECT pdata FROM '. sql_table('profile') . ' WHERE pmember="' . (int)$uid . '" AND ptype="' . addslashes( $type ) . '" LIMIT 1';

		$res = sql_query($query, 'get a single piece of data about a user');
		$obj = mysql_fetch_object($res);

		if( !is_object( $obj ) )
			return false;

		return  PROFILE::encodeBody( $obj->pdata );
	}


	// read from profile table, get single type value
	public function lookupProfileArry($uid, $type) {

		// read info
		$query = 'SELECT ptype,pdata FROM '. sql_table('profile') . ' WHERE pmember="' . (int)$uid . '" AND ptype="' . addslashes( $type ) . '" LIMIT 1000';
		$data = array();
		$res = sql_query($query,'get a single type of data about a user');
		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			// check if 2D array exists, if not create it.
			if( !isset( $data[ $this->dbKey2English( $obj->ptype ) ] ) ) {
				$data[ $this->dbKey2English( $obj->ptype ) ] = array();
			}

			// add item to 2D array
			$data[ $this->dbKey2English( $obj->ptype ) ][] = PROFILE::encodeBody( $obj->pdata );

		}

		//$this->stopClock();
		return $data;
	}


	// read for multiple members, multiple info
	public function multiMemberlookup($uid_set, $type, $nice_labels = true ) {
		global $CONF;

		$data = array();

		if( !is_array( $uid_set ) || count( $uid_set ) < 1 )
			return false;

		// form query
		$query = 'SELECT pmember,ptype,pdata FROM '. sql_table('profile') . ' WHERE (';

		foreach($uid_set as $uid) {
			$query .= 'pmember=' . (int)$uid . ' OR ';
			$data[ (int)$uid ] =  array( 'uid' => (int)$uid )   ;
		}
		$query = rtrim($query, ' OR ');
		$query .= ') AND (';
		if( is_array( $type ) ) {
			foreach($type as $data_type) {
				$query .= 'ptype=' . addslashes( $data_type ) . ' OR ';
			}

			// trim last OR
			$query = rtrim($query, ' OR ');
		} else $query .= 'ptype="' . addslashes( $type ) . '"';
		 $query .= ')';

		$res = sql_query($query, 'uid set ' . implode($uid_set, '|') .' multi-member ' . implode( $type, '|' ) . ' lookup');

		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			if( !is_array( $type ) )
				$data[] = PROFILE::encodeBody( $obj->pdata );

			else {

				// add item to 2D array
				if( !isset($data[$obj->pmember]) || !is_array( $data[$obj->pmember] ) )
					$data[$obj->pmember] = array( 'uid' => $obj->pmember );

				$data[ (int)$obj->pmember ][ ( $nice_labels ? PROFILE::dbKey2English( (int)$obj->ptype ) : (int)$obj->ptype ) ] = PROFILE::encodeBody( $obj->pdata );
			}
		}

		return $data;
	}


	// type is in english, for easier requests
	public function getProfile($type=false) {
		// no type set, return entire data set
		if($type == false)
			return $this->profile_items;

		// if item should be array but there are no entries yet, return blank array
		if( in_array( $type, $this->db_dup_keys ) && ( !isset( $this->profile_items[ $type ] ) || !is_array( $this->profile_items[ $type ] ) ) ) {
			return $this->profile_items[ $type ] = array();
		}
		// if array item isn't set, create it
		if( !isset( $this->profile_items[ $type ] ) )
			$this->profile_items[ $type ] = '';

		// It's set, and filled, return item and data
		return $this->profile_items[ $type ];
	}



	// type is in english, for easier requests
	public function tempSetProfile($type=false, $value = null) {
		if($type == false )
			return false;
		// if item should be array but there are no entries yet, set blank array
		if( in_array( $type, $this->db_dup_keys ) ) {
			if( !isset( $this->profile_items[ $type ] ) || !is_array( $this->profile_items[ $type ] ) ) {
				return $this->profile_items[ $type ] = array($value);
			} else {
				return $this->profile_items[ $type ][] = $value;
			}
		}
		// if array item isn't set
		if( !isset( $this->profile_items[ $type ] ) )
			$this->profile_items[ $type ] = $value;
		// else it's set, return item
		return $this->profile_items[ $type ] = $value;
	}


	// only use for items that have a single instance.
	// ( overwrites existing data )
	public function writeParam($uid, $type, $data) {

		// type is not regognized
		if( !in_array( $type, $this->db_keys ) )
			return false;

		if($type == 3800 || $type == 3900)
			PROFILE::registerHWRatio($uid, $type, $data);

		if($type == 5000)
			return PROFILE::writeProfileText($uid, $data);

		// we are duplicating some information in member table
		// for quicker searching
		// so fill member table first
		if( $type == 3100 ) {

			$mem = new MEMBER();
			$mem->readFromId($uid);

			// if label
			if($type == 3100)
				$mem->setClientLabel( $data );

			$mem->write();
		}


		// check if type is not singular
		// and does exist, then allow update
		if( !in_array( $this->dbKey2English( $type ), $this->db_dup_keys ) ) {

			if( true == ( $r = PROFILE::isParam( $uid, $type ) ) )
				return $this->updateParam( $r, $data );

			// error ??
			//return $r;
		}

		// if it isn't singular just create a new one
		return $this->createParam( $uid, $type, $data );
	}


	public function switchImgType($uid, $img, $new_type ) {

		$time = ctime();

		if( true == ($key = $this->isData( $uid, $img ) ) ) {

			$query = 'UPDATE ' . sql_table( 'profile' )
		 			. " SET	"
		 			. "    	ptype='". addslashes( (int)$new_type ) . "',"
			 		. "    	pcomment='". addslashes( (string)$this->dbKey2English( $new_type ) ) . "',"
			 		. "    	ptime='". addslashes( $time ) . "'"
			 		. " WHERE pmember='" . addslashes( (int)$uid ) . "' AND pkey='" . addslashes( (int)$key ) . "'";

			return sql_query($query);

		}

		return false;

	}


	// we are only allowing this function to be called
	// during member self editing
	public function registerLocation( $uid, $zip = null, $long = null, $lat = null ) {
		//global $member;
		global $DIR_LIBS;

		$mem = new MEMBER();
		$mem->readFromId( (int)$uid );

		include_once($DIR_LIBS . 'ZIPCODE.php');
		$zipcode = new ZIPCODE();

		// if long ant lat aren't registered
		// zip is valid
		// use zip to get long and lat
		if( (float)$long == null && (float)$lat == null ) {

			// zip code lookup failed
			if((int)$zip != null && !$zipcode->readByZip( (int)$zip ) ) {
				return false;
			}

			// lookup sucess
			$long = $zipcode->getZipData(0,'long');
			$lat = $zipcode->getZipData(0,'lat');

			$mem->setZip( (int)$zip );

		}

		// Either by zipcode or GEO LOC we have long and lat
		$this->writeparam( $uid, 3550, $long . '|' . $lat ); // long and lat
		include_once($DIR_LIBS . 'GOOLOC.php');
		$gooloc = new GOOLOC();

		// if long lat is provided
		if( (float)$long != null && (float)$lat != null && $gooloc->lookupByLongLat( (float)$long, (float)$lat ) ) {

			// process json and prep city state and country.
			$gooloc->registerLocality();

			$this->writeparam( $uid, 3300, ucwords( PROFILE::encodeBody( $gooloc->getCity() ) ) ); // city
			$this->writeparam( $uid, 3400, PROFILE::encodeBody( $gooloc->getState() ) ); // state
			$this->writeparam( $uid, 3540, ucwords( PROFILE::encodeBody( $gooloc->getCountry() ) ) ); // city

			$mem->setCity( ucwords( $gooloc->getCity() )  );
			$mem->setState( $gooloc->getState() );
			return $mem->write();

		}

		return false;
	}


	private function registerHWRatio( $uid, $type, $data) {

		// if height, get weight
		if( $type == 3800 ) {
			$weight = $this->lookupProfileValue($uid, 3900);
			$hw_ratio = $this->calcHW( $data, $weight );
		}

		// if weight, get height
		if( $type == 3900 ) {
			$height = $this->lookupProfileValue($uid, 3800);
			$hw_ratio = $this->calcHW( $height, $data );
		}

		if( isset( $hw_ratio ) )
			return $this->writeparam( $uid, 4800, $hw_ratio ); // state

		return false;
	}


	public function calcHeightInches($height) {
		list($feet, $inches) = explode( '-', $height );

		// get height in inches
		$inches += $feet * 12;

		return $inches;
	}


	public function calcHeightFeet($inches) {

		// get height in feet
		$feet = floor($inches / 12);
		$inches = $inches - ($feet * 12);

		return $feet . '-' . $inches;
	}


	private function calcHW( $inches=null, $weight=null ) {

		// return hw ratio, don't allow div by 0
		if($weight > 0 && $inches > 0)
			return round( $weight / $inches * 1000, 0);

		return false;
	}


	/*
	*
	* I can't wait to get rid of this entire method
	* I know it sucks but I'm really pressed for time
	* Sorry.
	*
	*
	$query = 'SELECT * FROM ' . sql_table('profile') . ' T JOIN (SELECT CEIL(MAX(pmember) * RAND() ) AS ID FROM '. sql_table('profile') . ') AS x ON T.pmember  ';
	*
	*/
	public function getRandomProfile( $area=null, $selection=null, $limit=2, $uids=null, $ids_only=false ) {
		global $CONF;

		/**
		*	'profile_city' => 			3300, // profile city
		*	'profile_state' => 			3400, // profile state
		*	'profile_zip' => 			3500, // profile zip
		*
		*/

		switch($area) {

			case "state":
				$where = 'ptype="3400" AND pdata="' . addslashes( $selection ) . '" AND ';
				break;

			case "city":
				$where = 'ptype="3300" AND pdata="' . addslashes( $selection ) . '" AND ';
				break;

			case "zip":
				$where = 'ptype="3500" AND pdata="' . addslashes( $selection ) . '" AND ';
				break;

			default:
				$where = null;

		}

		// custom lookup query
		// read info
		$query = 'SELECT pmember FROM '. sql_table('profile') . ' WHERE ptype = 5100 AND pdata > 421000';
		$query .= $where;
		$query .= ' AND pmember > 1 LIMIT 10000';

		$ids = array();
		$res = sql_query( $query, 'get random profiles' );
		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			$ids[] = $obj->pmember;
		}

		// then return their images
		if( count( $ids ) <= 0 )
			return false;

		$data = array();

		for( $i = 0; $i < $limit; $i++ ) {
			$rand = rand( 0, ( count( $ids ) - 1 ) );
			$data[] = $ids[$rand];
		}

		if( $ids_only == true )
			return $data;

		/*
		'profile' => 				3000,
		'profile_label' => 			3100, // profile label
		'pictures_dir' => 			4210, // member root img dir
		'pictures_main' => 			4220, // main picture face pic
		'profile_city' => 			3300, // profile city
		'profile_state' => 			3400, // profile state
		'profile_birthday' => 		3700
		*/

		// get all members images, labels, age, and state
		$r = $this->multiMemberlookup( $data, array( 3000, 3100, 4210, 4220, 4221, 3300, 3400, 3540, 3700 ), false );
		//var_dump( $r );

		return $r;

	}


	public function getlatestProfiles( $limit=5, $ids_only=false ) {

		$daysOld = 20;

		$date = date( "Y-m-d H:i:s", mktime( date('H'), date('i'), 0, date('m'), date('d') - (int)$daysOld, date('y') ) );

		$query_limit_multiplier = $limit;
		$query_lead_table = 'date_tbl';
		$query_select = ',  date_tbl.ptime AS date ';
		$query_having = ' date >= "' . $date .'"';
		$query_order = 'date';
		$query_score = ' AND score_tbl.pdata >= 421000 ';
		$query_from = ' INNER JOIN ' . sql_table('profile') . ' score_tbl ON ' . $query_lead_table . '.pmember = score_tbl.pmember';
		$query_where = ' AND ( score_tbl.ptype = 5100 ' . $query_score . ' )';
		$query = 'SELECT SQL_CALC_FOUND_ROWS date_tbl.pmember AS id' . $query_select . ' FROM ' . sql_table('profile') . ' date_tbl ' . $query_from . '  WHERE date_tbl.ptype = 4210 ' . $query_where;

		if($query_having != '')
			$query .= ' HAVING ' . $query_having;

		// set limit and offset
		$query .= ' ORDER BY ' . $query_order;
		$query .= ' DESC LIMIT 10';

		$ids = array();
		$res = sql_query($query, 'get random profiles');
		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			$ids[] = $obj->id;
		}

		// then return their images
		if( count($ids) <= 0 )
			return false;

		$data = array();
		$count = 0;
		do {

			$rand = rand( 0, ( count( $ids ) - 1 ) );
			$data[] = $ids[$rand];

		} while ( count($data) < $limit && $count++ < 100 );

		if( $ids_only == true )
			return $data;
		/*
		'profile' => 				3000,
		'profile_label' => 			3100, // profile label
		'pictures_dir' => 			4210, // member root img dir
		'pictures_main' => 			4220, // main picture face pic
		'profile_city' => 			3300, // profile city
		'profile_state' => 			3400, // profile state
		'profile_birthday' => 		3700
		*/

		// get all members images, labels, age, and state
		//$profile_data =  PROFILE::multiMemberlookup( $data, array( 3000, 3100, 4210, 4220, 4221, 3300, 3400, 3700, 3540, 5100 ), false );

		return PROFILE::multiMemberlookup( $data, array( 3000, 3100, 3550, 4210, 4220, 4221, 3300, 3400, 3540, 3500, 3600, 3700, 3800, 3900, 4800, 4900, 5100 ) );

	}


	public function getlastlogins( $limit=5 ) {

		//vars
		$ids = array();
		$query = null;
		$res = null;

		$query = 'SELECT date_tbl.pmember AS id, date_tbl.ptime AS date FROM ' . sql_table('profile') . ' date_tbl INNER JOIN ' . sql_table('profile') . ' status_tbl ON date_tbl.pmember = status_tbl.pmember WHERE date_tbl.ptype = 4450 AND status_tbl.ptype = 8000 AND status_tbl.pdata >= 1 ';

		// set limit and offset
		$query .= 'ORDER BY date ';
		$query .= 'DESC LIMIT ' . $limit;

		$res = sql_query($query, 'last login list');
		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			$ids[] = $obj->id;
		}
		return $ids;
	}


	// requires a seperate profile look up for friends… Costly…
	// has this person -> friended this person?
	public function isFriend($mid) {

		if( !is_array ( $this->pro_friends ) || 1 > count( $this->pro_friends) )
			$this->getFriends();

		if( isset( $this->pro_friends ) && is_array( $this->pro_friends ) )
			foreach( $this->pro_friends as $friend ) {
				if($mid == $friend) {
					return true;
				}
			}

		return false;

	}


	public function getFriends($self=false, $limit=null, $offset=null ) {

		// check if array is populated
		if( count( $this->pro_friends ) == 0 || !isMulti( $this->pro_friends_data ) ) {
			// if not then populate
			$this->queryFriends();
		}

		$friends = $this->pro_friends_data;

		$c = 0;
		$limit_count = 0;
		$unsetted_friends = array();

		if( $self == false )
			$unsetted_friends[] = $this->profile_id;

		if( count( $this->pro_friends ) > 0 && ( (int)$limit || (int)$offset ) )
			foreach( $friends as $friend ) {



				// if offset is set we need to advance to it
				if( (int)$offset != null && $c <= $offset ) {
					// we will have to unset these later
					$unsetted_friends[] = $friend[3000];
				}


				// if we are over the limit skip the rest
				if( (int)$limit != null &&  ($offset + $limit) < $c ) {

					// we will have to unset these later
					$unsetted_friends[] = $friend[3000];

				}

				$c++;

			}

		if( count( $unsetted_friends ) > 0 )
			foreach( $unsetted_friends as $unset_id )
				unset( $friends[ $unset_id ] );

		//echo '<br />offset = ' . $offset . ',limit is = ' . $limit . ', stop limit = ' . ($offset + $limit) . ', unseatted count = '. count( $unsetted_friends ). ',total = '.$c.', array count = ' . count( $friends )  ;

		return $friends;
	}


	public function getFriendsCount() {

		if( 0 == count( $this->pro_friends ) )
			$this->pro_friends = $this->lookupProfileArry($this->profile_id, 1000);

		return count( $this->pro_friends );

	}


	private function queryFriends() {

		// friend data is multi array set up
		if( isMulti( $this->pro_friends_data ) )
			return true;

		// no friends listed in array, get them
		if( 1 > count( $this->pro_friends ) ) {
			$this->pro_friends = $this->lookupProfileArry($this->profile_id, 1000);
		}

		// if they still don't have friends; exit
		if( 1 > count( $this->pro_friends)  )
			return false;

		// include self
		$this->pro_friends_data[ (int)$this->profile_id ] = array( 3000 => (int)$this->profile_id);

		foreach( $this->pro_friends as $friend ) {
			$this->pro_friends_data[ $friend ] = array( 3000 => (int)$friend );
		}

		// read info
		$query = 'SELECT pmember,ptype,pdata,ptime FROM '. sql_table('profile') . ' WHERE (';

		foreach( $this->pro_friends as $friend ) {
			$query .= 'pmember=' . $friend . ' OR ';
		}

		$query .= ' pmember=' . $this->profile_id;
		$query .= ') AND (ptype=3100 OR ptype=4220 OR ptype=4210 OR ptype=4221 OR ptype=4230 OR ptype=4240) ORDER BY pmember';

		$res = sql_query($query, 'get friends for '. $this->profile_id);

		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			// Check if item is a singular instance or multiple
			if( in_array( $this->dbKey2English( $obj->ptype ), $this->db_dup_keys ) ) {

				// check if 3D array exists, if not, create it.
				if( !isset( $this->pro_friends_data[ $obj->pmember ][ $obj->ptype ] ) ) {
					$this->pro_friends_data[$obj->pmember][ $obj->ptype ] = array();
				}

				// add item to 2D array
				$this->pro_friends_data[$obj->pmember][ $obj->ptype ][ $obj->ptime ] = is_numeric($obj->pdata) ? (int)$obj->pdata : PROFILE::encodeBody( $obj->pdata );

			} else {

				// add item to 1D array
				$this->pro_friends_data[$obj->pmember][ $obj->ptype ] = is_numeric($obj->pdata) ? (int)$obj->pdata : PROFILE::encodeBody( $obj->pdata );

			}

		}

		return true;
	}


	public function getTotalPages() {
		$query = 'SELECT FOUND_ROWS()';
		$r = sql_query($query);
		$res = mysql_fetch_array($r);

		return  $res['FOUND_ROWS()'];

	}


	public function hasMemberAsFriend($member, $limit=100, $offset=0) {

		// read info
		$query = 'SELECT SQL_CALC_FOUND_ROWS pmember, ptime FROM '. sql_table('profile') . ' WHERE ';
		$query .= ' pdata = ' . $member;
		$query .= ' AND ptype = 1000 ORDER BY ptime DESC ';

		if($limit != 0 || $offset) {
			$query .= ' LIMIT ';

			if($offset)
				$query .= $offset . ', ';

			$query .= $limit;
		}

		$res = sql_query($query, 'has member as friend');
		$as_friend = array();

		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			// add item to 2D array
			$as_friend[] = $obj->pmember;

		}

		return $as_friend;
	}


	public function canAddFriends() {
		global $member;

		if( 1 < count( $this->pro_friends) )
			$this->getFriends();

		// something is wrong but let them add anyway
		//if( !isset( $this->pro_friends ) || !is_array( $this->pro_friends ) )
		//	return true;

		if( $member->isPayingMember() )
			return ( count( $this->pro_friends ) < $this->member_friend_limit );

		return ( count( $this->pro_friends ) < $this->nonmember_friend_limit );
	}


	public function addFriend($uid) {

		// check if is friend
		if( false == ( $r = $this->isFriend($uid) ) )
			// add friend
			return $this->writeParam($this->profile_id, 1000, $uid);

		return false;
	}


	public function removeFriend($uid) {

		// check if is friend
		if( true == ( $r = $this->isFriend( (int)$uid) ) )
			// add friend
			return $this->removeProfileParamData($this->profile_id, (int)$uid);
		die('this failed');
		return false;
	}


	public function getViews() {

		// arrays are filled with data, return data.
		if( 1 < count( $this->pro_views ) && isMulti( $this->pro_views ) )
			return $this->pro_views;

		//reset views and prepare for dataset
		$this->pro_views = array();

		// read info
		$query = 'SELECT pkey,pdata,ptime FROM '. sql_table('profile') . ' WHERE ';
		$query .= 'pmember=' . (int)$this->profile_id;
		$query .= ' AND ptype=4600 ORDER BY pkey DESC LIMIT 200';

		$res = sql_query($query, 'get views');

		$visitor = array();
		$last_visitor = 0;
		while( true == ( $obj = mysql_fetch_object($res) ) ) {

			if( !is_object( $obj ) )
				return false;

			if($obj->pdata <= 1)
				continue;

			if( $last_visitor == $obj->pdata)
				continue;

			if( !isset( $visitor[$obj->ptime] ) || !is_array( $visitor[$obj->ptime] ) )
				$visitor[$obj->ptime] = array( 3000 => $obj->pdata );

			// add item to 2D array
			$visitor[$obj->ptime]['time'] =  $obj->ptime;

			$last_visitor = $obj->pdata;

		}

		return $visitor;
	}


	/**
	* Prepares body to be stored to database
	* in a form that is stable and nontoxic
	* the system
	* remove <br />  leave \n
	*
	* Excapses everything Leaving it intact to encode later
	*/

	function decodeBody($data) {

		$data = removeBreaks($data); // remove any <br />  and replace with \r\n

		// remove whitespace before or after entry
		$data = trim($data);



		// escape everything encluding quotes
		$data = htmlentities($data, ENT_QUOTES, 'UTF-8');

		// replace whitespace
		$data = str_replace( '  ', ' &nbsp;', $data );

		$data = addslashes($data);
		return $data;
	}


	/**
	* Prepares the body to display as HTML
	*
	* leaves everything excaped.
	*/
	// prepares the body of a comment (static)
	function encodeBody( $body, $replace_links = true, $insert_elements=false ) {

		// trim away whitespace and newlines at beginning and end
		$body = trim($body);
		$body = stripslashes($body);
		$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
		$body = str_replace('\"', '&quot;', $body);
		$body = str_replace("\'", '&#039;', $body);
		$body = str_replace("&amp;", '&', $body);

		// create hyperlinks for http:// addresses
		$replaceFrom = array(
			'/([^:\/\/\w]|^)((https:\/\/)([\w\.-]+)([\/\w+\.~%&?@=_:;#,-]+))/ie',
			'/([^:\/\/\w]|^)((http:\/\/|www\.)([\w\.-]+)([\/\w+\.~%&?@=_:;#,-]+))/ie',
			'/([^:\/\/\w]|^)((img:\/\/|www\.)([\w\.-]+)([\/\w+\.~%&?@=_:;#,-]+))/ie',
			'/([^:\/\/\w]|^)((ftp:\/\/|ftp\.)([\w\.-]+)([\/\w+\.~%&?@=_:;#,-]+))/ie',
			'/([^:\/\/\w]|^)(mailto:(([a-zA-Z0-9\@\%\.\-\+_])+))/ie'
		);

		$replaceTo = array(
			'PROFILE::createLinkCode("\\1", "\\2","https","'.$insert_elements.'")',
			'PROFILE::createLinkCode("\\1", "\\2","http","'.$insert_elements.'")',
			'PROFILE::createLinkCode("\\1", "\\2","html_img","'.$insert_elements.'")',
			'PROFILE::createLinkCode("\\1", "\\2","ftp","'.$insert_elements.'")',
			'PROFILE::createLinkCode("\\1", "\\3","mailto","'.$insert_elements.'")'
		);

		if( $replace_links == true ) {
			$body = preg_replace($replaceFrom, $replaceTo, $body);
			$body = addBreaks($body);
		}

		return $body;
	}


	function createLinkCode($pre, $url, $protocol = 'http', $insert_elements=false) {
		$post = '';

		// it's possible that $url ends contains entities we don't want,
		// since htmlspecialchars is applied _before_ URL linking
		// move the part of URL, starting from the disallowed entity to the 'post' link part
		$aBadEntities = array('&quot;', '&gt;', '&lt;');
		foreach ($aBadEntities as $entity) {
			$pos = strpos( $url, $entity );
			if ( $pos ) {
				$post = substr( $url, $pos ) . $post;
				$url = substr( $url, 0, $pos );
			}
		}

		// remove entities at end (&&&&)
		if (preg_match('/(&\w+;)+$/i', $url, $matches)) {
			$post = $matches[0] . $post;	// found entities (1 or more)
			$url = substr($url, 0, strlen($url) - strlen($post)); }

		// move ending comma from url to 'post' part
		if (substr($url, strlen($url) - 1) == ',') {
			$url = substr($url, 0, strlen($url) - 1);
			$post = ',' . $post;
		}


		if( ($protocol == 'http' || $protocol == 'https') && $insert_elements== true ) {

			$yt_key = preg_replace('~
		        # Match non-linked youtube URL in the wild. (Rev:20130823)
		        https?://         # Required scheme. Either http or https.
		        (?:[0-9A-Z-]+\.)? # Optional subdomain.
		        (?:               # Group host alternatives.
		          youtu\.be/      # Either youtu.be,
		        | youtube\.com    # or youtube.com followed by
		          \S*             # Allow anything up to VIDEO_ID,
		          [^\w\-\s]       # but char before ID is non-ID char.
		        )                 # End host alternatives.
		        ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
		        (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
		        (?!               # Assert URL is not pre-linked.
		          [?=&+%\w.-]*    # Allow URL (query) remainder.
		          (?:             # Group pre-linked alternatives.
		            [\'"][^<>]*>  # Either inside a start tag,
		          | </a>          # or inside <a> element text contents.
		          )               # End recognized pre-linked alts.
		        )                 # End negative lookahead assertion.
		        [?=&+%\w.-]*      # Consume any URL (query) remainder.
		        ~ix',
		        '$1',
			$url);

			if( strlen( $yt_key ) == 11 && strstr($yt_key, '.') == false ) {
				return '<div class="image_holder"><iframe width="420" height="315" src="//www.youtube.com/embed/' . $yt_key . '" frameborder="0" allowfullscreen></iframe></div>';
			}
		}

		if( $protocol == 'html_img' )
			return '<div class="image_holder"><a class="model-image protect" href="/img.php?f=' .str_replace( 'img://', '/', $url ) . '&w=700&h=700"><img src="/img.php?f=' .str_replace( 'img://', '/', $url ) . '&w=430&h=250" class="img_attached" /><span class="hide">' . $url . '</span></a></div>';

		else if ( !ereg( '^'.$protocol.'://',$url ) )
			$linkedUrl = $protocol . (($protocol == 'mailto') ? ':' : '://') . $url;
		else
			$linkedUrl = $url;

		if ($protocol != 'mailto')
			$displayedUrl = $linkedUrl;
		else
			$displayedUrl = $url;
		return $pre . '<a href="'.$linkedUrl.'" target="_blank" rel="nofollow" class="protect">' . shorten( $displayedUrl, 60, '...' ) . '</a>' . $post;
	}


	public function createProfile( $profile, $zip, $uid, $email, $time ) {
		global $DIR_LIBS, $DIR_MEDIA, $DIR_ROOT;

		//check if profile exists already… some weird fluke
		if( true == ( $r = $this->getIDbyEmail( $email ) ) )
			// remove it
			$this->removeProfile($r);

		$arry = array(
			'profile_subtitle',
			'profile_introduction',
			'profile_birthday',
			'profile_height',
			'profile_weight',
			'profile_prefer',
				'prefer_role_min',
				'prefer_role_max',
				'prefer_agemin',
				'prefer_agemax',
				'prefer_weightmin',
				'prefer_weightmax',
			'profile_lookingfor',
			//	'lookingfor_justsex',
			//	'lookingfor_friends',
			//	'lookingfor_dating',
			//	'lookingfor_relationship',
			'profile_pictures',
			//	'pictures_dir',
			//	'pictures_main',
			//	'pictures_aux',
			//	'pictures_xxx',
			'profile_settings',
				'settings_visibilityby',
					'visibilityby_location',
					'visibilityby_relation',
			'profile_timesviewed'
		);

		/**
		include_once( $DIR_LIBS . 'SYSOPS.php' );
  		$sys = new SYSOPS();
		$sys->removeDir('media/profile/2/ already exists120');
		*/

		// full path
		$img_dir = $this->checkCurrentMemStorageDir( $DIR_MEDIA .'profile/' );
		$img_dir = $img_dir . (int)$uid .'/';
		$this->checkMemberImageDir( $img_dir );
		// good for db to remove root
		$img_dir = str_replace( $DIR_ROOT, '', $img_dir );

		include_once($DIR_LIBS . 'ZIPCODE.php');
		$zipcode = new ZIPCODE();

		$city = '';
		$state = '';
		$zip = addslashes( $zip );
		$profile = addslashes( $profile );

		$time = addslashes( $time );
		$email = addslashes( $email );
		$img_dir = addslashes( $img_dir );

		$query = 'INSERT INTO ' . sql_table( 'profile' ) . ' (pkey, pmember, ptype, pdata, ptime, pcomment) VALUES ';

		// create the rest as null
		foreach ( $arry as $key ) {
			$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key( $key ) .'","", "'. $time .'", "' . $key . '"), ';
		}

		// enter the basic information we have to start the profile
		$query .= '("0","' . (int)$uid . '", "' . (int)$this->dbEnglish2Key("profile_label") . '", "' . $profile . '", "' . $time . '", "profile_label"), ';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_city") .'","'. $city .'", "'. $time  .'", "profile_city"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_state") .'","'. $state .'", "'. $time  .'", "profile_state"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_zip") .'","'. $zip .'", "'. $time  .'", "profile_zip"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_datecreated") .'","'. $time .'", "'. $time .'", "profile_datecreated"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_email") .'","'. $email .'", "'. $time .'", "profile_email"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("pictures_dir") .'","'. $img_dir .'", "'. $time .'", "pictures_dir"),';
		$query .= '("0","' . (int)$uid . '","'. (int)$this->dbEnglish2Key("profile_position") .'","0", "'. $time .'", "profile_position")';

		$query .= 'ON DUPLICATE KEY UPDATE pkey=VALUES(pkey),pmember=VALUES(pmember),ptype=VALUES(ptype),pdata=VALUES(pdata),ptime=VALUES(ptime),pcomment=VALUES(pcomment);';

		$r = sql_query( $query, 'create profile' );

		//log the action
		LOG::staticWriteBasic( 'action', 'create profile = "'.$uid.'".');

		return $r;

	}

	// returns true or false, Perm Deletion, No messing around here.
	public function removeProfile($id, $option = null ) {
		global $DIR_LIBS;

		if($option === 'ALL') {
			// remove all posts and messages and everything!!!
			include_once( $DIR_LIBS . 'MESSAGE.php');
			$message = new MESSAGE();
			$message->deleteAllMessagesFromSender( $id );

			// remove all posts and messages and everything!!!
			include_once( $DIR_LIBS . 'POSTS.php');
			$posts = new POSTS();
			$posts->deleteAllPostsFromPoster( $id );

		}


		$pro_del = new PROFILE();
		$pro_del->readFromId( (int)$id );
		$pro_del->deleteAllMemberImages( (int)$id, $pro_del->getProfile('pictures_dir') );

		$r = sql_query('DELETE FROM ' . sql_table('profile') . ' WHERE pmember =' . intval( (int)$id ),'remove all profile information');

		$pro_del->removeText($id);
		// error reporting deleted row
		// so just check for it after deletion

		// error reporting deleted row
		//log the action
		LOG::staticWriteBasic( 'action', 'delete profile = "'.$id.'".');

		return !$pro_del->readFromId($id);

	}


	// private
	private function isParam($uid, $type) {
		return quickQuery('SELECT pkey as result FROM ' . sql_table('profile') . ' WHERE pmember="' . (int)$uid . '" AND ptype="' . (int)$type . '"  LIMIT 1','check if is profile param');
	}

	public function isData($uid=null, $data) {

		$where = null;

		if($uid != null)
			$where = 'pmember="' . addslashes( (int)$uid ) . '" AND';

		return quickQuery('SELECT pkey as result FROM ' . sql_table('profile') . ' WHERE ' . $where . ' pdata="' . addslashes($data) . '"  LIMIT 1', 'check if is profile data');
	}


	public function writeProfileText( $uid, $data ) {

		if( true == ( $r = $this->textExists( $uid ) ) )
			return $this->updateText( $r, $data );

		// if it isn't singular just create a new one
		return $this->createText( $uid,  $data );
	}


	public function getProfileText($uid) {
		return quickQuery('SELECT pdata as result FROM ' . sql_table('profile_text') . ' WHERE pmember="' . (int)$uid . '"  LIMIT 1', 'get profile text');
	}


	private function textExists($uid) {
		return quickQuery('SELECT pmember as result FROM ' . sql_table('profile_text') . ' WHERE pmember="' . (int)$uid . '"  LIMIT 1','profile text exists');
	}


	private function updateText($uid, $data) {

		$time = ctime();

		$query = 'UPDATE ' . sql_table( 'profile_text' )
	 			. " SET	"
	 			. "    	pdata='". addslashes( (string)$data ) . "',"
		 		. "    	ptime='". addslashes( $time ) . "'"
		 		. " WHERE pmember='" . addslashes( (int)$uid ) . "'";

		return sql_query($query, 'update profile text');
	}


	private function createText( $uid, $data ) {

		$time = ctime();

		$query = 'INSERT INTO ' . sql_table( 'profile_text' ) . ' (pmember, pdata, ptime) VALUES ';
		$query .= '("' . addslashes( (int)$uid ) . '","'. addslashes( $data ) . '","' . addslashes( $time ) .'")';
		return sql_query($query, 'create profile text');
	}

	public function removeText($uid) {
		$query = 'DELETE FROM '. sql_table( 'profile_text' ) .' WHERE pmember=' . (int)$uid ;

		//log the action
		LOG::staticWriteBasic( 'action', 'profile text deleted - "'.$uid.'".');

		return sql_query( $query, 'remove profile param data' );
	}


	private function updateParam($key, $data) {

		$time = ctime();

		$query = 'UPDATE ' . sql_table( 'profile' )
	 			. " SET	"
	 			. "    	pdata='". addslashes( (string)$data ) . "',"
		 		. "    	ptime='". addslashes( $time ) . "'"
		 		. " WHERE pkey='" . addslashes( (int)$key ) . "'";

		return sql_query($query, 'update profile param');
	}


	private function createParam( $uid, $type, $data ) {

		$time = ctime();

		$query = 'INSERT INTO ' . sql_table( 'profile' ) . ' (pmember, ptype, pdata, ptime, pcomment) VALUES ';
		$query .= '("' . addslashes( (int)$uid ) . '","'. addslashes( (int)$type ) . '","'. addslashes($data) . '", "' . addslashes( $time ) .'", "' . addslashes( $this->dbKey2English( $type ) ) . '")';
		return sql_query($query, 'create new profile param');
	}


	public function removeParam($uid, $type) {
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE pmember=' . (int)$uid . ' AND ptype="' . addslashes( $type ) . '"';
		return sql_query( $query, 'remove profile param' );
	}


	public function removeProfileParamData($uid, $data) {
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE pmember=' . (int)$uid . ' AND pdata="' . addslashes( $data ) . '"';
		return sql_query( $query, 'remove profile param data' );
	}

	public function removeProfileTypeParamData($uid, $type, $data) {
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE pmember=' . (int)$uid . ' AND ptype="' . addslashes( $type ) . '" AND pdata="' . addslashes( $data ) . '"';
		return sql_query( $query, 'remove profile param data' );
	}

	private function removeTypeParamData($type, $data) {
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE ptype=' . (int)$type . ' AND pdata="' . addslashes( $data ) . '"';
		return sql_query( $query, 'remove all type param data' );
	}


	private function _dupProfile( $type, $data ) {

		if( $this->_dup_pro_uid == null )
			$this->_dup_pro_uid = 1 + $this->getCurrentId();

		$uid = $this->_dup_pro_uid;

		/**
			'profile_pictures' => 		4200,	// "null"
			'pictures_dir' => 			4210,	// member root img dir
			'pictures_main' => 			4220, // main picture face pic
			'pictures_aux' =>			4230, // additional aux pics
			'pictures_xxx' => 			4240, // any dirty pictures
			'pictures_maintemp' => 		4250, // main picture waiting for approval
			'pictures_original' => 		4260, // any dirty pictures

		*/

		$this->createParam( $uid, $type, $data );

	}

	// counldn't find profile so remove any reference to it
	Public function profileGone($id) {

		// remove as friend
		$this->removeTypeParamData(1000, $id);

		return true;
	}

	private function _removeDupProfile( $min,$max ) {

		$r = sql_query('DELETE FROM ' . sql_table('profile') . ' WHERE pmember BETWEEN '.$min.' AND '.$max,'remove all profile between id='.$min .' AND '.$max);

		// error reporting deleted row
		// so just check for it after deletion

		return $r;
	}


	public function getPosition($entry, $short=false) {

		switch($entry){
			case(0):
				$entry = "Not defined";
				break;
			case(1):
				$entry = 'Bottom';
				if($short) $entry = 'Btm';
				break;
			case(2):
				$entry = 'Versatile/Bottom';
				if($short) $entry = 'Ver/Btm';
				break;
			case(3):
				$entry = 'Versatile';
				if($short) $entry = 'Ver';
				break;
			case(4):
				$entry = 'Versatile/Top';
				if($short) $entry = 'Ver/Top';
				break;
			case(5):
				$entry = 'Top';
				if($short) $entry = 'Top';
				break;
			default:
				$entry = null;
		}

		return $entry;
	}


	public function getHWRatio( $num, $short=false ) {

		$num = (int)$num;

		if($num < 1500)
			return false;

		// 5'11" 135 lb
		$entry = 'x-Thin';
		if($short) $entry = 'xThin';

		// 5'11" 150 lb
		if($num > 2110 ) {
			$entry = 'Thin';
			if($short) $entry = 'Thin';
		}
		// 5'11" 165 lb
		if($num > 2320 ) {
			$entry = 'Medium';
			if($short) $entry = 'Med';
		}
		// 5'11" 200 lb
		if($num > 2820 ) {
			$entry = 'Large';
			if($short) $entry = 'Lg';
		}
		// 5'11" 250 lb
		if($num > 3520 ) {
			$entry = 'xLarge';
			if($short) $entry = 'xLg';
		}
		// 5'11" 290 lb
		if($num > 4080 ) {
			$entry = 'xxLarge';
			if($short) $entry = 'xxLg';
		}

		return $entry;
	}


	public function convertRatioToClass( $num ) {

		$num = (int)$num;

		if($num < 1500)
			return false;

		// 5'11" 135 lb
		$entry = 1;

		// 5'11" 150 lb
		if($num > 2110 )
			$entry = 2;

		// 5'11" 165 lb
		if($num > 2320 )
			$entry = 3;

		// 5'11" 200 lb
		if($num > 2820 )
			$entry = 4;

		// 5'11" 250 lb
		if($num > 3520 )
			$entry = 5;

		// 5'11" 290 lb
		if($num > 4080 )
			$entry = 6;

		return $entry;
	}



	public function getWeightTerm($entry,$short=false) {

		switch($entry){
			case(0):
				$entry = 'Any';
				if($short) $entry = 'Any';
				break;
			case(1):
				$entry = 'xThin';
				if($short) $entry = 'xThin';
				break;
			case(2):
				$entry = 'Thin';
				if($short) $entry = 'Thin';
				break;
			case(3):
				$entry = 'Medium';
				if($short) $entry = 'Med';
				break;
			case(4):
				$entry = 'Large';
				if($short) $entry = 'Lg';
				break;
			case(5):
				$entry = 'xLarge';
				if($short) $entry = 'xLg';
				break;
			case(6):
				$entry = 'xxLarge';
				if($short) $entry = 'xxLg';
				break;
			default:
				$entry = null;
		}

		return $entry;
	}


	public function getAge( $item_data=null ) {

		// age number set is less than 6 digits or is emtpy
		if($item_data == null || $item_data == '--' || strlen( $item_data ) < 6 )
			return false;

		// day or month but no year
		if( strpos( $item_data, '-' ) == 0 ) {
			return false;
		}

		// year and day or month but not both
		// year is available use it
		// falsify birthdate
		if( substr( $item_data, 0, 4 ) > 1900 && strlen( $item_data ) < 10 ) {
			$item_data = substr( $item_data, 0, 4 ) . '-' . date("m-d");
		}

		// age number set is 10 digits and we are happy
		list($m, $d, $Y) = explode("-", substr( $item_data, 5, 2 ) . '-' . substr( $item_data, 8, 2 ) . '-' . substr( $item_data, 0, 4 ) );

		return ( date("md") < $m.$d ? date("Y") - $Y - 1 : date("Y") - $Y );
	}


	public function getVisibility($entry) {

		switch($entry){
			case(1):
				$entry = 'Inactive';
				break;
			case(2):
				$entry = 'My Keepers List';
				break;
			case(3):
				$entry = 'Active members';
				break;
			case(4):
				$entry = 'Public';
				break;
			default:
				$entry = null;
		}

		return $entry;
	}


	static function cleanupDb() {

		// remove any entries that have no type
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE ptype=""';
		sql_query( $query, 'remove profile param' );

		// remove any entries that have no data
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE pdata=""';
		sql_query( $query, 'remove profile param' );

		// remove any entries that have no data
		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE pmember < 1';
		sql_query( $query, 'remove profile param' );

		// remove any viewed parameters older than 30 days
		$daysAgo = date( "Y-m-d H:i:s", mktime( date('H'), date('i'), 0, date('m'), date('d') - 30, date('y') ) );

		$query = 'DELETE FROM '. sql_table( 'profile' ) .' WHERE ptype=4600 AND pmember < 2 AND ptime < "' . $daysAgo . '"';

		sql_query( $query, 'remove profile param' );

		//log the action
		LOG::staticWriteBasic( 'system', 'cleaned up profile database.');

		return true;
	}

}


?>
