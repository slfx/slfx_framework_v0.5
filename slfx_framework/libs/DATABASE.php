<?php

class DATABASE
{

	//vars
	// Load Stat Vars
	public $query_count = 0;
	public $query_log = array();
	
	protected $target_table = null;
	protected $default_table_name = 'quicktable';
	protected $connection = null; 
	
	protected $query_order_by = null;
	protected $query_limit = null;
	
	
	//contructor
	public function __construct($parent = null) {
		
		// default framework table name
		$this->useTable();
		
		$this->parent = $parent;
		
		$this->connect();
				
		return;
	
	}
	
	public function setOrderBy( $attr ) {
		return $this->query_order_by = ' ORDER BY ' . addslashes( $attr ) . ' ';
	}
	
	public function setLimit( $limit = 5, $reverse = false ) {
		
		$order = 'DESC';
		if( $reverse == true )
			$order = 'ASC';
		
		return $this->query_limit = $order . ' LIMIT ' . (int)$limit;		
	}
	
	public function setParent($parent = null ) {
		$this->parent = $parent;
	}
	
	//public
	/**
	* Connects to mysql server
	*/
	protected function connect() {
		global $SECURE_CONF;
		
		if( isset( $this->parent->page_status['enable_db'] ) && $this->parent->page_status['enable_db'] === false )
			return false;
		
		$this->connection = mysql_connect( $SECURE_CONF['MYSQL_HOST'], $SECURE_CONF['MYSQL_USER'], $SECURE_CONF['MYSQL_PASSWORD'] ) 
			or startUpError( '<p>Could not connect to MySQL database.</p>', 'Connect Error');
		
		mysql_select_db( $SECURE_CONF['MYSQL_DATABASE'], $this->connection ) 
			or startUpError( '<p>Could not select database: ' . mysql_error() . '</p>', 'Connect Error');
	}
	
	/**
	* returns a prefixed table name
	*/
	protected function registerTableName( $name ) {
		global $SECURE_CONF;
		
		if( $SECURE_CONF['MYSQL_PREFIX'] )
		  return $SECURE_CONF['MYSQL_PREFIX'] . '_' . $name;
		
		return $name;
	}
	
	public function useTable( $name = null ) {
		
		if( $name == null )
			$name = $this->default_table_name;
			
		return $this->target_table = $this->registerTableName( $name );
	}
	
	
	// depreciated
	private function setTargetTable( $table_name = null ) {
		
		if( $table_name === null )
		    throw new RuntimeException('No DB table set');
		
		$this->target_table = $table_name;
		
		return true;
	}

	
	/**
	* disconnects from SQL server
	*/
	public function disconnect() {
		@mysql_close();
	}
	
	/**
	* executes an SQL query
	*/
	public function query( $query, $comment=null ) {
		global $SECURE_CONF;
	
		if( isset( $this->parent->page_status['enable_db'] ) && $this->parent->page_status['enable_db'] === false )
			return false;
		
		$query_time = microtime('get_as_float');
		$res = mysql_query( $query, $this->connection ) or print("mySQL error with query $query: " . mysql_error() . '<p />'  );
		++$this->query_count;
		
		if( isset( $this->parent->page_status['enable_debug'] ) && $this->parent->page_status['enable_debug'] === true ) {
		  $this->query_log[] = "\nquery " . $this->query_count . " runtime = " . round( microtime('get_as_float') - $query_time, 4 ) . " sec =--= " . $query . "\n\tcomment: #### " . $comment ." ####" ;
		}
		
		return $res;
	}
	
	public function quickQuery( $q, $note = null ) {
		global $CONF;
		
		if( isset( $this->parent->page_status['enable_db'] ) && $this->parent->page_status['enable_db'] === false )
			return false;
		
		$res = $this->query($q, $note);
		$obj = mysql_fetch_object($res);
		
		if( is_object( $obj ) )
		  return $obj->result;
		
		return false;
	}
	
	protected function updateDataByKey( $key, $data ) {
	
		$time = getTimestamp();
		
		$query = 'UPDATE ' . $this->target_table
		    . " SET	"
		    . "    	data='". addslashes( (string)$data ) . "',"
		    . "    	time='". addslashes( $time ) . "'"
		    . " WHERE key='" . addslashes( (int)$key ) . "'";
		
		return $this->query( $query, 'update data by key' );
	}
		
	protected function tableExists( $target_table ) {
		
		$result = $this->query("SHOW TABLES LIKE '" . $target_table . "'");
		return mysql_num_rows($result) > 0;
		
	}

}




class MEMBER extends DATABASE 
{
	
	protected $default_table_name = 'membertable';
	
	
	//contructor
	public function __construct() {
		
		// default framework table name
		$this->useTable();
		
		$this->connect();
				
		return;
	
	}

	/**
	* member => data write methods
	*
	* key - value scheme
	* 
	* overwrite or create data for a member id
	* limited to non-type databases
	*
	*/
	public function rewriteMemberData( $member, $data, $overwrite = false ) {
		
		if( true == ( $r = $this->memberExists( $member ) ) ) {
			if( $overwrite === true ) 
				return $this->updateMemberData( $r, $data );

			return false; 
		}
		
		// just create a new one
		return $this->createMemberData( $member, $data );
	}
	
	// will make duplicate stores without checking
	public function storeMemberData( $member, $data ) {
		return $this->createMemberData( $member, $data );
	}
	 
	// get member data limit 1
	public function getMemberData( $member ) {
		return $this->quickQuery('SELECT data as result FROM ' . $this->target_table . ' WHERE member="' . fnvhash( $member ) . '"  LIMIT 1', 'get data');
	}
	
	public function getMemberBulkData( $member ) {
		
		//vars
		$members = array(); 

		$res = $this->query('SELECT * FROM ' . $this->target_table . 
			' WHERE member=' . fnvhash( $member ) . 
			$this->query_order_by . $this->query_limit, 'get member bulk data'
		);
		
		while( true == ( $obj = mysql_fetch_object( $res ) ) ) {

			if( !is_object( $obj ) )
				return false;

			$members[] = $obj->data;
		}
		
		return $members;
	}
	
	
	private function memberExists( $member ) {
		return $this->quickQuery('SELECT member as result FROM ' . $this->target_table . ' WHERE member="' . fnvhash( $member ) . '"  LIMIT 1','data exists');
	}
	
	private function updateMemberData($member, $data) {
		
		$time = getTimestamp();
		$query = 'UPDATE ' . $this->target_table
		    . " SET	"
		    . " data='". addslashes( (string)$data ) . "'"
		    . " WHERE member='" . fnvhash( $member ) . "'";
		
		return query($query, 'update data');
	}
	
	private function createMemberData( $member, $data ) {
		
		$query = 'INSERT INTO ' . $this->target_table . ' (member, data) VALUES ';
		$query .= '("' . fnvhash( $member ) . '","'. addslashes($data) . '")';
		return $this->query($query, 'create data');
	}
	
	/**
	* data removal methods
	* User based
	*/
	public function removeMemberData($member, $data) {
		$query = 'DELETE FROM '. $this->target_table . 
		' WHERE member=' . fnvhash( $member ) . 
		' AND data="' . addslashes( $data ) . '"';
		
		return $this->query( $query, 'remove by member and data' );
	}
	
	/**
	* data removal methods
	* User based
	*/
	public function removeMember($member) {
		$query = 'DELETE FROM ' . $this->target_table . 
		' WHERE member=' . fnvhash( $member ); 
		
		return $this->query( $query, 'remove member' );
	}

	/**
	* Creates a basic nonrational userbased journaled table 
	* to store any data up to 512 chars.
	*
	* 999 million record limit
	*
	*/
	public function makeMemberTable() {
		if( false == ( $this->tableExists( $this->target_table ) ) ) {
			return ( $this->query( 'CREATE TABLE `' . $this->target_table . '` (
			  `key` int(12) NOT NULL AUTO_INCREMENT,
			  `member` int(32) NOT NULL DEFAULT "0",
			  `data` varchar(512) NOT NULL DEFAULT "",
			  `time` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			  PRIMARY KEY (`key`),
			  KEY `member` (`member`)
			  ) ENGINE=MyISAM AUTO_INCREMENT=696356 DEFAULT CHARSET=utf8;') 
			  	? 'Table ' . $this->target_table . ' created ' 
			  	: 'Unable to create ' . $this->target_table . ' due to unknown error' );
		}
		
		return 'Table '. $this->target_table . ' already exists'; 
	}
}



class USERTYPE extends DATABASE 
{

	protected $default_table_name = 'typedtable';
		
	//contructor
	public function __construct() {
		
		// default framework table name
		$this->useTable();
		
		$this->connect();
				
		return;
	}
	
	public function getIDbyEmail( $email ) {
		return $this->quickQuery( 'SELECT member as result FROM ' . $this->target_table . ' WHERE data = "' . addslashes( $email ) . '"  LIMIT 1' );
	}

	
	/**
	* Typed write methods
	*
	* key -> type -> value scheme
	*
	* overwrite or create typed data for a user id
	* Good for storing all kinds of different data types
	*
	*/
	
	// only use for data that have a single instance.
	// ( overwrites existing data )
	public function rewriteUserTypeData( $uid, $type, $data, $overwrite = false ) {
		
		if( true == ( $r = USERTYPE::isUserType( $uid, $type ) ) ) {
			if( $overwrite === true )
		    	return $this->updateDataByKey( $r, $data );
			
			return false;
		}
			
		return $this->createUserTypeData( $uid, $type, $data );
	}
	
	// will make duplicate stores without checking
	public function storeUserTypeData( $uid, $type, $data ) {

		return $this->createUserTypeData( $uid, $type, $data );
	}
		
	// private
	private function isUserType($uid, $type) {
		return $this->quickQuery('SELECT key as result FROM ' . $this->target_table . 
		' WHERE member=' . (int)$uid . 
		' AND type="' . fnvhash( $type ) . '" LIMIT 1', 'check if is profile param');
	}

	public function isUserData($uid=null, $data) {

		if($uid != null)
			$where = 'member="' . addslashes( (int)$uid ) . '" AND';

		return $this->quickQuery('SELECT key as result FROM ' . $this->target_table . 
		' WHERE ' . $where . ' data="' . addslashes($data) . '"  LIMIT 1', 'check if is profile data');
	}

	
	private function createUserTypeData( $uid, $type, $data ) {
	
		$time = getTimestamp();
		
		$query = 'INSERT INTO ' . $this->target_table . ' (member, type, data, time) VALUES ';
		$query .= '("' . addslashes( (int)$uid ) . '","' . fnvhash( $type ) . '","'. addslashes( $data ) . '", "' . addslashes( $time ) .'")';
		return $this->query($query, 'create new user type data');
	}
	
	/**
	* Typed data removal methods
	* User based
	*/
	// nuclear bomb method - worst
	public function removeTypeData( $type, $data ) {
	
		$query = 'DELETE FROM ' . $this->target_table .
		' WHERE type=' . fnvhash( $type ) . 
		' AND data="' . addslashes( $data ) . '"';
		
		return $this->query( $query, 'remove all type data' );
	}

	// cluster bomb - better
	public function removeUserType($uid, $type) {
		$query = 'DELETE FROM '. $this->target_table .
		' WHERE member=' . (int)$uid . 
		' AND type=' . fnvhash( $type );
		return $this->query( $query, 'remove date by user and type' );
	}
	
	// bunker buster - good
	public function removeUserData($uid, $data) {
		$query = 'DELETE FROM '. $this->target_table . 
		' WHERE member=' . (int)$uid . 
		' AND data="' . addslashes( $data ) . '"';
		return $this->query( $query, 'remove by user and data' );
	}
	
	// rail gun - best
	public function removeUserTypeData($uid, $type, $data) {
		$query = 'DELETE FROM ' . $this->target_table .
		' WHERE member=' . (int)$uid . 
		' AND type=' . fnvhash( $type ) . 
		' AND data="' . addslashes( $data ) . '"';
		return $this->query( $query, 'remove by user type and data' );
	}
	
	
	public function countType($type) {
		return $this->quickQuery( 'SELECT COUNT(*) as result FROM ' . $this->target_table . ' WHERE type = '. fnvhash($type) );
	}


	public function getUsers($min=null, $max=null) {
		
		//vars
		$where = null;
		$data = array();
		
		if( $min !== null && $max !== null ) {
			$where = 'member BETWEEN ' . (int)$min . ' AND ' . (int)$max;
		}
		
		$res = $this->query("SELECT DISTINCT member FROM ". $this->target_table . $where . $this->query_order_by . $this->query_limit, 'get all users online' );
		while( true == ( $obj = mysql_fetch_object($res) ) ) {
			
			if( !is_object( $obj ) )
				return false;
				
			$data[] = $obj->member;
		}
		return $data; 
		
	}
	
	
	// read from table
	// best for one user
	public function getUserTypeBulk($where) {
		//fnvhash( $member );		
		
		// read info
		$query =  'SELECT ptype, pdata FROM ' . $this->target_table . 
			' WHERE ' . $where .  
			$this->query_order_by . $this->query_limit;

		// set default settings to be overwritten by db data
		$res = $this->query( $query, 'full profile lookup' );
		
		while( true == ( $obj = mysql_fetch_object($res) ) ) {
			
			if( !is_object( $obj ) )
				return false;
			
			// second entry of same type needs multiarray
			if( in_array( $obj->type, $this->profile_items ) ) {
				
				// create buffer for first entry type
				$temp_buffer = $this->profile_items[ $obj->type ]; 
				
				// check if 2D array exists, if not create it.
				if( is_array( $this->profile_items[ $obj->type ] ) == false ) {
					$this->profile_items[ $obj->type ] = array();
					$this->profile_items[ $obj->type ][] = $temp_buffer;
				}
				
				// proceed as normal and add item to 2D array
				$this->profile_items[ $obj->type ][] = is_numeric($obj->data) ? (int)$obj->data : DATABASE::encodeBody( $obj->data );
			
			} else {
				
				// add item to 1D array
				$this->profile_items[ $obj->type ] = is_numeric($obj->data) ? (int)$obj->data : DATABASE::encodeBody( $obj->data );
			}
		}
		
		return mysql_num_rows($res);
	}
	
	
	// read from table
	public function getMultiUserTypeBulk($uid_set, $type_set ) {
	
		// vars
		$type_query = null; 
		$member_query = null;
		$query = null;
		$data = array();

		if( !is_array( $uid_set ) || count( $uid_set ) < 1 )
			return false;
			
		// where members
		foreach($uid_set as $uid) {
			$member_query .= 'member=' . (int)$uid . ' OR ';
			$data[ (int)$uid ] =  array( 'uid' => (int)$uid );
		}
		$member_query = rtrim( $member_query, ' OR ' );
				
		// where type 
		if( is_array( $type_set ) ) {
			foreach( $type_set as $data_type ) {
				$type_query .= 'type=' . fnvhash( $data_type ) . ' OR ';
			}
			$type_query = rtrim($type_query, ' OR ');
		
		} else $type_query .= 'type="' . fnvhash( $type_set ) . '"';
		
		// form query
		$query = 'SELECT member,type,data FROM ' . $this->target_table . ' WHERE (' . $member_query . ') AND (' . $type_query . ')';

		$res = $this->query( $query, 'uid set ' . implode( $uid_set, '|') .' multi-member ' .  implode( $type_set, '|' ) . ' lookup');

		while( true == ( $obj = mysql_fetch_object( $res ) ) ) {

			if( !is_object( $obj ) )
				return false;

			if( !is_array( $type_set ) )
				$data[] = $this->packStr( $obj->data );
			else {
				// add item to 2D array
				if( !isset( $data[ $obj->member ] ) || !is_array( $data[ $obj->member ] ) )
					$data[ $obj->member ] = array( 'uid' => $obj->member );

				$data[ (int)$obj->member ][ (int)$obj->type ] = $this->packStr( $obj->data );
			}
		}

		return $data;
	}
	
	
	function unpackStr( $str ) {

		$str = removeBreaks($str); // remove any <br />  and replace with \r\n

		// remove whitespace before or after entry
		$str = trim($str);

		// escape everything encluding quotes
		$str = htmlentities($str, ENT_QUOTES, 'UTF-8');

		// replace whitespace
		$str = str_replace( '  ', ' &nbsp;', $str );
		$str = addslashes($str);
		
		return $str;
	}


	/**
	* Prepares the body to display as HTML
	*
	* leaves everything excaped.
	* formerly 'encodeBody'
	*/
	// prepares the body of a comment (static)
	function packStr( $str ) {

		// trim away whitespace and newlines at beginning and end
		$str = trim($str);
		$str = stripslashes($str);
		$str = htmlentities($str, ENT_QUOTES, 'UTF-8');
		$str = str_replace(array('\"',"\'", "&"), array('&quot;','&#039;','&amp;'), $str);
		
		return $str;
	}
	
	/**
	* Creates a basic nonrational typed userbased journaled table 
	* to store any data up to 512 chars.
	*
	* 999 million record limit
	*
	*/
	public function makeUserTypeTable() {
		if( false == ( $this->tableExists( $this->target_table ) ) ) {
			return ( $this->query( 'CREATE TABLE `' . $this->target_table . '` (
			  `key` int(12) NOT NULL AUTO_INCREMENT,
			  `member` int(10) NOT NULL DEFAULT "0",
			  `type` int(32) NOT NULL DEFAULT "0",
			  `data` varchar(512) NOT NULL DEFAULT "",
			  `time` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			  PRIMARY KEY (`key`),
			  KEY `member` (`member`),
			  KEY `type` (`type`)
			  ) ENGINE=MyISAM AUTO_INCREMENT=696356 DEFAULT CHARSET=utf8;') 
			  	? 'Table ' . $this->target_table . ' created ' 
			  	: 'Unable to create ' . $this->target_table . ' due to unknown error' );
		}
		
		return 'Table '. $this->target_table . ' already exists'; 
	}

	
	
	
}

?>
