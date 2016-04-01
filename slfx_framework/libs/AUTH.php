<?php


define( '_ERROR_PASSWORDMISSING', 'password missing');
define( '_ERROR_ADDRESSMISSING', 'address missing');
define( '_ERROR_ADDRESSNOTVALID', 'address not valid');
define( '_ERROR_ADDRESSINUSE', 'Address is already in our system. <a href="/signin/do/<%uid%>">Retrieve account with "no password login"</a>');

class AUTH
{
	// auth works with usertype database api -- use only usertype db. 
	
	
	
	//var
	private $mode = null;
	private $salt = null; 
	private $cookieSeed = null; 
	
	//
	public function __construct( $parent, $mode = null ){
		$this->parent = $parent; 
		$this->mode = $mode; 
		$this->salt = $this->parent->page_status['cookie_salt'];
	}

	function setPassword( $pwd ) { return $this->password = md5( $pwd . $this->salt );	}
	function setCookieKey( $val ) {	$this->cookieKey = $val; }
	
	function getPassword () {}
	function getId() {}
	function getCookieKey() {}
	
	function getHashedEmail() {	return $this->hashedEmail; }
	function getSalt() { return $this->salt; }
	function getPrivate() { $private = cookieVar('privatepc'); return $private; }

	function checkPassword($pw) {
		// for admin access allow crypt to be passed
		if( $pw === $this->getPassword() )
			return true;

		return md5( $pw . $this->salt ) == $this->getPassword();
	}

	function newCookieKey() {
		mt_srand( (double)microtime() * 1000000 );
		$this->cookieSeed = md5( uniqid( mt_rand() ) );
		//$this->write();
		return $this->cookieSeed;
	}
	
	function checkCookieKey($key) {
		return $key != '' && $key == $this->getCookieSeed();
	}

	private function getRandomKey() {
		return md5( time() );
	}

	// adds a new member (static)
	public function create( $email, $password = null ) {

		// return salt-hashed email address if in use
		
		if ( empty( $password ) )
			return _ERROR_PASSWORDMISSING;
		
		$ut = new USERTYPE();
		$uid = $ut->countType('email') + 1;

		// sterilize EVERYTHING
		$password = md5( $password . $this->getSalt() );
		
		$ut->rewriteUserTypeData( $uid, 'email', $email );
		$ut->rewriteUserTypeData( $uid, 'password', $password );

		return $uid;
	}


	/**
	* Tries to login as a given user. Returns true when succeeded,
	* returns false when failed
	*/
	public function login( $login, $password ) {
		/*$this->loggedin = 0;

		if( !$this->readFromEmail( $login ) )
			return 0;

		//$this->setPassword('1');
		//$this->write();

		if( !$this->checkPassword( $password ) )
			return 0;

		if( !$this->canLogin() )
			return 0;

		$this->loggedin = 1;

		//$this->setLastLogin($this->ctime());
		$this->write();

		return $this->isLoggedIn();
*/	}


	// login using cookie key
	public function cookieLogin($login, $cookiekey) {
/*
		$this->loggedin = 0;

		if( !$this->readFromId($login) )
			return 0;

		if ( !$this->checkCookieKey($cookiekey) )
			return 0;
		// set new login time

		$this->loggedin = 1;
		return $this->isLoggedIn();
*/	}




	/**
	* Sets the cookies for the member
	*
	* @param shared
	*		set this to 1 when using a shared computer. Cookies will expire
	*		at the end of the session in this case.
	*/
	public function setCookies( $private = false ) {
		global $conf_obj;

		if( $conf_obj->page_conf['cookie_secure'] == true || $private == false )
			$lifetime = 0;
		else
			$lifetime = time() + 2592000;
		// used to id the computer and browser
		setcookie(
			$conf_obj->page_conf['cookie_prefix'] . '_id',
			$this->getRandomKey(),
			$lifetime,
			$conf_obj->page_conf['cookie_path'],
			$conf_obj->page_conf['cookie_domain'],
			$conf_obj->page_conf['cookie_secure'] );
		// used to authenticate
		setcookie(
			$conf_obj->page_conf['cookie_prefix'] . '_key',
			$this->getCookieKey(),
			$lifetime,
			$conf_obj->page_conf['cookie_path'],
			$conf_obj->page_conf['cookie_domain'],
			$conf_obj->page_conf['cookie_secure'] );
		// public or private computer?
		setcookie(
			$conf_obj->page_conf['cookie_prefix'] . '_stay',
			'0',
			$lifetime,
			$conf_obj->page_conf['cookie_path'],
			$conf_obj->page_conf['cookie_domain'],
			$conf_obj->page_conf['cookie_secure'] );

		// make sure cookies on shared pcs don't get renewed
		if( $private === true ) {
			setcookie(
				$conf_obj->page_conf['cookie_prefix'] . '_stay',
				'1',
				$lifetime,
				$conf_obj->page_conf['cookie_path'],
				$conf_obj->page_conf['cookie_domain'],
				$conf_obj->page_conf['cookie_secure'] );
		}

	}
	
	
	public function logout() {
		global $conf_obj;

		// login if cookies set
		if ( $mode == 'logout' && headers_sent() == false && cookieVar( $conf_obj->page_conf['cookie_prefix'] . '_id' ) ) {

			// remove cookies on logout
			setcookie(
				$conf_obj->page_conf['cookie_prefix'] . '_id',
				'',
				( time() - 2592000 ),
				$conf_obj->page_conf['cookie_path'],
				$conf_obj->page_conf['cookie_domain'],
				$conf_obj->page_conf['cookie_secure'] );

			setcookie(
				$conf_obj->page_conf['cookie_prefix'] . '_key',
				'',
				( time() - 2592000 ),
				$conf_obj->page_conf['cookie_path'],
				$conf_obj->page_conf['cookie_domain'],
				$conf_obj->page_conf['cookie_secure'] );
				
			setcookie(
				$conf_obj->page_conf['cookie_prefix'] . '_stay',
				'',
				( time() - 2592000 ),
				$conf_obj->page_conf['cookie_path'],
				$conf_obj->page_conf['cookie_domain'],
				$conf_obj->page_conf['cookie_secure'] );

			header("location: /");
			exit();

		} 
	}
	
	public function renewCookie() {	
		
		if( cookieVar( $conf_obj->page_conf['cookie_prefix'] . '_id' ) ) {
			$res = $this->cookieLogin( cookieVar( $conf_obj->page_conf['cookie_prefix'] .'_id' ), cookieVar( $conf_obj->page_conf['cookie_prefix'] .'_key' ) );

			if( $res == true ) {

				// renew cookies when not on a shared computer
				if( cookieVar( $conf_obj->page_conf['cookie_prefix'] .'_stay' ) == true && !headers_sent() )
				{}	//$member->setCookies( true );

			}
		}
		return true;
	}


	function getActivationLink($type, $extra=null) {
		global $CONF;

		// generate key and URL
		$key = $this->generateActivationEntry($type, $extra);
		//$url = $CONF['IndexURL'] .'?action=activate&key=' . $key;

		// choose text to use in mail
		switch ($type) {
			case 'nopw':
				$message = _VERIFY_NOPW_MAILMESSAGE;
				$title = _VERIFY_NOPW_MAILTITLE;
				$subject = _VERIFY_NOPW_MAILSUBJECT;
				$url = $CONF['IndexURL'] . '/verify/do/nopw/' . $key . '/'. $this->getId() ;
				break;
			case 'addresschange':
				$message = _ACTIVATE_CHANGE_MAILMESSAGE;
				$title = _ACTIVATE_CHANGE_MAILTITLE;
				$subject = _ACTIVATE_CHANGE_MAILSUBJECT;
				$key = $this->generateActivationEntry($type, $extra);
				$url = $CONF['IndexURL'] .'/verify/do/change/' . $key. '/' . $this->getId();
				break;
			default:
				// generate key and URL
				$message = _VERIFY_ACTIVATE_MAILMESSAGE;
				$title = _VERIFY_ACTIVATE_MAILTITLE;
				$subject = _VERIFY_ACTIVATE_MAILSUBJECT;
				$key = $this->generateActivationEntry($type, $extra);
				$url = $CONF['IndexURL'] .'/verify/do/activate/' . $key . '/' . $this->getId();
		}

		// fill out variables in text
		$aVars = array(
			'siteName' => $CONF['SiteName'],
			'siteUrl' => $CONF['IndexURL'],
			'siteRoot' => $CONF['SiteRoot'],
			'memberName' => $this->getClientLabel(),
			'activationUrl' => $url
		);

		$data = (object) array(
			'title' => TEMPLATE::fill($title, $aVars),
			'subject' => TEMPLATE::fill($subject, $aVars),
			'message' => TEMPLATE::fill($message, $aVars),
			'link' => $url,
			'activation_code' => $key
		);
		return $data;
	}


	/**
	* Returns activation info for a certain key (an object with properties vkey, vmember, ...)
	* (static)
	*
	*/
	function getActivationInfo($key) {

		$query = 'SELECT * FROM ' . sql_table('activation') . ' WHERE vkey=\'' . addslashes($key). '\'';
		$res = sql_query($query);

		if (!$res || (mysql_num_rows($res) == 0))
			return 0;
		else
			return mysql_fetch_object($res);
	}


	/**
	* Creates an account activation key
	*
	* @param $type one of the following values (determines what to do when activation expires)
	*                'register' (new member registration)
	*                'forgot' (forgotton password)
	*                'addresschange' (member address has changed)
	* @param $extra extra info (needed when validation link expires)
	*				  addresschange -> old email address
	*/
	function generateActivationEntry($type, $extra=null) {

		// clean up old entries
		$this->cleanupActivationTable();

		// kill any existing entries for the current member (delete is ok)
		// (only one outstanding activation key can be present for a member)
		sql_query('DELETE FROM ' . sql_table('activation') . ' WHERE vmember=' . intval($this->getID()));

		$cant_login_verify = false; // indicates if the member can log in while the link is active

		switch ($type) {
			case 'nopw':
				$cant_login_verify = false;
				break;
			case 'register':
				break;
			case 'addresschange':
				//$extra = $extra . '/' . ($this->canLogin() ? '1' : '0');
				$cant_login_verify = false;
				break;
		}

		// generate a random key
		srand((double)microtime()*1000000);
		$key = md5(uniqid(rand(), true));

		// attempt to add entry in database
		// add in database as non-active
		$query = 'INSERT INTO '. sql_table('activation') . ' (vkey, vtime, vmember, vtype, vextra) ';
		$query .= 'VALUES (\'' . addslashes($key). '\', \'' . date('Y-m-d H:i:s',time()) . '\', \'' . intval($this->getID()). '\', \'' . addslashes($type). '\', \'' . addslashes($extra). '\')';

		if (!sql_query($query)) 	return false;

		// mark member as not allowed to log in
		if ($cant_login_verify){
			$this->setCanLogin(0);
			$this->write();
		}

		// return the key
		return $key;
	}


	/**
	* Inidicates that an activation link has been clicked and any forms displayed
	* there have been successfully filled out.
	* @author dekarma
	*/
	function activate($key) {
		// get activate info
		$info = MEMBER::getActivationInfo($key);

		// no active key
		if (!$info) 	return false;

		switch ($info->vtype) {
			case 'nopw':
				// nothing to do
				break;
			case 'register':
				// set canlogin value
				global $CONF;
				sql_query('UPDATE '. sql_table('member') . ' SET mcanlogin=' . intval($CONF['NewMemberCanLogon']). ' WHERE mnumber=' . intval($info->vmember));
				break;
			case 'addresschange':
				// reset old 'canlogin' value
				$new_email = $info->vextra;

				if ( true == ( $r = MEMBER::getIDbyEmail( $new_email ) ) )
					return TEMPLATE::fill( _ERROR_MAILADDRESSINUSE, array( 'uid' => $r ) );

				// write new address to member and profile tables
				sql_query('UPDATE '. sql_table('member') . ' SET memail="' . addslashes($new_email). '" WHERE mnumber=' . intval($info->vmember));

				global $DIR_LIBS;
				include_once($DIR_LIBS . 'PROFILE.php' );
				PROFILE::writeParam( (int)$r, 4700, addslashes( $new_email ) );


				break;
		}

		// delete from activation table
		sql_query('DELETE FROM '. sql_table('activation') . ' WHERE vkey=\'' . addslashes($key) . '\'');

		// after confirmation, elevate status to 1
		$mem = new MEMBER();
		$mem->readFromId( $info->vmember );
		if( $mem->getStatus() < 1 )
			$mem->setStatus(1);
		$mem->write();

		include_once( $DIR_LIBS . 'PROFILE.php' );
		$pro_self = new PROFILE();
		if( $mem->getStatus() < 1 )
			$pro_self->writeparam( $info->vmember, 8000, 1 );


		// success!
		return $info->vmember;
	}


	/**
	* Cleans up entries in the activation table. All entries older than 2 days are removed.
	* (static)
	*
	*/
	function cleanupActivationTable() {
		$boundary = time() - (60 * 60 * 24 * 1);

		// 1. walk over all entries, and see if special actions need to be performed
		$res = sql_query('SELECT * FROM '. sql_table('activation') . ' WHERE vtime < \'' . date('Y-m-d H:i:s',$boundary) . '\'');

		while ($o = mysql_fetch_object($res)) {
			switch ($o->vtype) {
				case 'register':
					// delete all information about this site member. registration is undone because there was
					// no timely activation
					//include_once($DIR_LIBS . 'ADMIN.php');

					// status must be 0 or account can't be deleted for safety reasons.
					/**
					* we are no longer deleting users for not confirming
					*/
					//ADMIN::deleteOneMember(intval($o->vmember), 0);
					break;
				case 'addresschange':
					// revert the e-mail address of the member back to old address
					//list($oldEmail, $oldCanLogin) = explode('/', $o->vextra);
					//sql_query('UPDATE '. sql_table('member') . ' SET mcanlogin=' . intval($oldCanLogin). ', memail=\'' . addslashes($oldEmail). '\' WHERE mnumber=' . intval($o->vmember));
					break;
				case 'nopw':
					// delete the activation link and ignore. member can request a new password using the
					// forgot password link
					break;
			}
		}

		// 2. delete activation entries for real
		sql_query('DELETE FROM '. sql_table('activation') . ' WHERE vtime < \'' . date('Y-m-d H:i:s',$boundary) . '\'');
	}


}

?>