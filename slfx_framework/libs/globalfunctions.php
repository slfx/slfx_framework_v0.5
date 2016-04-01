<?php


if ( $conf_obj->page_conf['enable_debug'] === true ) {
	
	error_reporting(2047);
	ini_set( "display_errors", 1 );// report all errors!

} else {

	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	ini_set( "display_errors", 1 );

}


/**
* includes
*/
// basics framework dependancies
include_once( $conf_obj->page_conf['dir_libs'] . 'vars5.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'DIRLIST.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'TEMPLATE.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'FILE.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'FILELIST.php' );

// routing
include_once( $conf_obj->page_conf['dir_libs'] . 'ROUTER.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'OUTPUT.php' );

// logging and storing data
include_once( $conf_obj->page_conf['dir_libs'] . 'LOG.php' );
include_once( $conf_obj->page_conf['dir_libs'] . 'DATABASE.php' );

// login and accounts
include_once( $conf_obj->page_conf['dir_libs'] . 'AUTH.php' );


function intPostVar( $name ) { return intval( postVar( $name ) ); }
function intGetVar( $name ) { return intval( getVar( $name ) ); }
function intRequestVar( $name ) { return intval( requestVar( $name ) ); }
function intCookieVar( $name ) { return intval( cookieVar( $name ) ); }
function bool2str( $str = false ) { return ( $str ? 'true' : 'false' ); }


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

function selector( $mode = 'loader' ) {
	global $conf_obj;
	global $router;

	$router = new ROUTER();
	
	// allow some modes to return string errors if nessassary
	if( is_string($r = $router->doAction( $mode ) ) ) 
		return $r;

	// show error when headers already sent out
	if (headers_sent() && $conf_obj->page_conf['alertOnSentHeader'] ) {

		// try to get line number/filename (extra headers_sent params only exists in PHP 4.3+)
		if (function_exists('version_compare') && version_compare('4.3.0', phpversion(), '<=')) {
			headers_sent($hsFile, $hsLine);
			$extraInfo = ' in <code>'.$hsFile.'</code> line <code>'.$hsLine.'</code>';
		} else {
			$extraInfo = '';
		}

		startUpError(
			'<p>The page headers have already been sent out'.$extraInfo.'. This could cause this site not to work in the expected way.</p><p>Usually, this is caused by spaces or newlines at the end of the <code>config.php</code> file, at the end of the language file or at the end of a plugin file. Please check this and try again.</p><p>If you don\'t want to see this error message again, without solving the problem, set <code>$CONF[\'alertOnHeadersSent\']</code> in <code>globalfunctions.php</code> to <code>0</code></p>',
			'Page headers already sent'
		);
		exit;
	}

	// prints the page
	$router->output->completePage();

	return true;
}

function getConfig() {
	global $conf_obj;
	
	$query = 'SELECT * FROM site_config';
	$res = sql_query($query);
	while ($obj = mysql_fetch_object($res)) {

		$conf_obj->page_conf[$obj->name] = $obj->value;
	}
}

function getIp() {
	
	$ip = null;
	/*
	This function will try to find out if user is coming behind
	proxy server. Why is this important?
	If you have high traffic web site, it might happen
	that you receive lot of traffic
	from the same proxy server (like AOL). In that case,
	the script would count them all as 1 user.
	This function tryes to get real IP address.
	Note that getenv() function doesn't work when PHP
	is running as ISAPI module
	*/
	if(getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	}
	elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	}
	elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	}
	elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	}
	else {
		if( isset( $_SERVER['REMOTE_ADDR'] ))
			$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}


// add and remove linebreaks
function addBreaks( $var ) { 		return nl2br($var); }
function removeBreaks( $var ) {		return preg_replace("/<br \/>([\r\n])/","$1",$var); }

// shortens a text string to maxlength ($toadd) is what needs to be added
// at the end (end length is <= $maxlength)
function shorten($text, $maxlength, $toadd='...') {
	// 1. remove entities...
	$text = preg_replace(array("/\s\s+/", "/\n/"), array(" "," "), $text);

	// mark put entities in
	$text = unhtmlspecialchars( $text );
	// 2. the actual shortening
	if( strlen( $text ) > $maxlength )
		$text = substr( $text, 0, $maxlength - strlen( $toadd ) ) . $toadd;
	$text = encodehtmlspecialchars( $text );

	return $text;
}

function unhtmlspecialchars( $string ) {
    $string = str_replace( '&amp;', '&', $string );
    $string = str_replace( '&#039;', "'", $string );
    $string = str_replace( '&quot;', '"', $string );
    $string = str_replace( '&lt;', '<', $string );
    $string = str_replace( '&gt;', '>', $string );
    $string = str_replace( '&#8230;', '...', $string );

    return $string;
}

function encodehtmlspecialchars( $string ) {
    $string = str_replace( '&', '&amp;' , $string );
    $string = str_replace( "'", '&#039;', $string );
    $string = str_replace( '"', '&quot;', $string );
    $string = str_replace( '<', '&lt;', $string );
    $string = str_replace( '>', '&gt;', $string );
    $string = str_replace( '...', '&#8230;', $string );

    return $string;
}


function isMultiArray($a) {
    foreach ($a as $v) {
        if( is_array($v) ) 
        	return true;
    }
    return false;
}



function formatDuration($seconds) {

	$periods = array(
		//'centuries' => 3155692600,
		//'decades' => 315569260,
		'years' => 31556926,
		'months' => 2629743,
		//'weeks' => 604800,
		'days' => 86400,
		'hours' => 3600,
		'minutes' => 60,
		'seconds' => 1
	);

	$durations = array();

	foreach( $periods as $period => $seconds_in_period ) {

		$durations[$period] = 0;

		if ( $seconds >= $seconds_in_period ) {
			$durations[$period] = (int)floor( $seconds / $seconds_in_period );
			$seconds -= $durations[$period] * $seconds_in_period;
		}
	}

	return $durations;
}


function sendEMessage( $from, $to, $email_obj ) {
	global $CONF;

	//unique boundary
	$boundary = uniqid("_----------=_");

	//add From: header
	$headers = "Return-Path: <". $from .">\n";

	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative;\n\t";
	$headers .= "boundary=\"$boundary\"\n";
	$headers .= "From:$from";
	
	// if it's not set, set it to null so it is defined...
	if( !isset( $email_obj->css_add) )
		$email_obj->css_add = null;
		
	// if it's not set, set it to null so it is defined...
	if( !isset( $email_obj->unsubscribe ) )
		$email_obj->unsubscribe = null;

	$aVars = array(
			'css_add' => $email_obj->css_add,
			'boundary' => $boundary,
			'title' => $email_obj->title,
			'subject' => $email_obj->subject,
			'message' => strip_tags($email_obj->message),
			'html_message' => $email_obj->message,
			'unsubscribe' => $email_obj->unsubscribe
			);

	$comp = 'emessage';

	//if( strpos( $to, 'aol.com' ) || strpos( $to, 'yahoo.com' ) )
	//	$comp = 'emessage_txtonly';


	$file = new FILE( $CONF['DIR_SKIN'] . $comp . '.inc' );

	$body = TEMPLATE::fill( $file->get(), $aVars );

	return mail("<". $to . ">", $email_obj->subject, $body, $headers);
}



/**
* Time methods
*
*/

function getTimestamp( $time = null ) {

	if($time == null)
		$time = time();

	$date = date("Y-m-d H:i:s",$time);
	return $date;
}


//format time
function relativeTimePast( $t ) {

	$time_past = time() - strtotime( $t );
	$times = formatDuration( $time_past );

	if($times['months'] > 1)
		$ago = date( 'M d, Y', strtotime( $t ) );
	else if($times['months'] == 1)
		$ago = $times['months'] . ' ' . _MONTH . ' ' . _AGO;
	else if($times['days'] > 1)
		$ago = $times['days'] . ' ' . _DAYS . ' ' . _AGO;
	else if($times['days'] == 1)
		$ago = $times['days'] . ' ' . _DAY . ' ' . _AGO;
	else if($times['hours'] > 1)
		$ago = $times['hours'] . ' ' . _HOURS . ' ' . _AGO;
	else if($times['hours'] == 1)
		$ago = $times['hours'] . ' ' . _HOUR . ' ' . _AGO;
	else if($times['minutes'] > 1)
		$ago = $times['minutes'] . ' ' . _MINUTES . ' ' . _AGO;
	else if($times['minutes'] == 1)
		$ago = $times['minutes'] . ' ' . _MINUTE . ' ' . _AGO;
	else if($times['seconds'] > 0)
		$ago = $times['seconds'] . ' ' . _SECONDS . ' ' . _AGO;
	else { $ago = 1 . ' ' . _SECOND . ' ' . _AGO; }

	return $ago;
}


function formatTimeToStr($times) {

	if($times['months'] > 1)
		$time = $times['months'] . ' ' . _MONTHS . ' ';
	if($times['months'] == 1)
		$time = $times['months'] . ' ' . _MONTH . ' ';
	if($times['days'] > 1)
		$time = $times['days'] . ' ' . _DAYS . ' ';
	if($times['days'] == 1)
		$time .= $times['days'] . ' ' . _DAY  . ' ';
	if($times['hours'] > 1)
		$time .= $times['hours'] . ' ' . _HOURS . ' ';
	if($times['hours'] == 1)
		$time .= $times['hours'] . ' ' . _HOUR . ' ';
	if($times['minutes'] > 1)
		$time .= $times['minutes'] . ' ' . _MINUTES;
	if($times['minutes'] == 1)
		$time .= $times['minutes'] . ' ' . _MINUTE;

	return $time;
}


/**
* Validate an email address.
*
* Returns true if address format is correct and the domain exists.
*/
function isValidAddress($email) {
	$is_valid = true;
	$atIndex = strrpos($email, "@");

	if( is_bool( $atIndex ) && !$atIndex ) {
		$is_valid = false;
   } else {
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);

		if( $localLen < 1 || $localLen > 64 ) {
		 // local part length exceeded
		 $is_valid = false;
		}
		else if( $domainLen < 1 || $domainLen > 255 ) {
		 // domain part length exceeded
		 $is_valid = false;
		}
		else if( $local[0] == '.' || $local[$localLen-1] == '.' ) {
		 // local part starts or ends with '.'
		 $is_valid = false;
		}
		else if( preg_match( '/\\.\\./', $local ) ) {
		 // local part has two consecutive dots
		 $is_valid = false;
		}
		else if( !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) ) {
		 // character not valid in domain part
		 $is_valid = false;
		}
		else if( preg_match('/\\.\\./', $domain) ) {
		 // domain part has two consecutive dots
		 $is_valid = false;
		}
		else if( !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace( "\\\\", "", $local ) ) ) {

		// character not valid in local part unless
		// local part is quoted
		if( !preg_match( '/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local) ) ) {
			$is_valid = false;
		 }
		}

		if( $is_valid && !( checkdnsrr( $domain, "MX" ) || checkdnsrr( $domain, "A" ) ) ) {
		// domain not found in DNS
		$is_valid = false;
		}
   }

   return $is_valid;
}



/**
*	FNV Hash
*
*  Author: Neven Boyanov
*  Copyright (c) 2009 by Neven Boyanov (Boyanov.Org)
*  Licensed under GNU/GPLv2 - http://www.gnu.org/licenses/
*
*  This program is distributed under the terms of the License,
*  but WITHOUT ANY WARRANTY; without even the implied warranty
*  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
*  the License for more details.
*
*
*	Constants
*
*	FNV_PRIME:
*	32 bit FNV_prime = 2^24 + 2^8 + 0x93 = 16777619	... 1000000000000000110010011
*	64 bit FNV_prime = 2^40 + 2^8 + 0xb3 = 1099511628211	... 10000000000000000000000000000000110110011
*	128 bit FNV_prime = 2^88 + 2^8 + 0x3b = 309485009821345068724781371	...
*	OFFSET_BASIS:
*	32 bit offset_basis = 2166136261
*	64 bit offset_basis = 14695981039346656037
*	128 bit offset_basis = 144066263297769815596495629667062367629	
*
*	Source: http://www.isthe.com/chongo/tech/comp/fnv/
*/

define ("FNV_prime_32", 16777619);
define ("FNV_prime_64", 1099511628211);
define ("FNV_prime_128", 309485009821345068724781371);

define ("FNV_offset_basis_32", 2166136261);
define ("FNV_offset_basis_64", 14695981039346656037);
define ("FNV_offset_basis_128", 144066263297769815596495629667062367629);

function fnvhash( $str ) {
	
	$buf = str_split( $str );
	$hash = FNV_offset_basis_32;
	
	foreach( $buf as $chr ) {
		$hash += ($hash << 1) + ($hash << 4) + ($hash << 7) + ($hash << 8) + ($hash << 24);
		$hash = $hash ^ ord($chr);
	}
	
	$hash = $hash & 0x0ffffffff;
	
	return $hash;
}



function createKey() {
	return $this->randStr(24, 32, true, true);
}


function getRandStr($minlength, $maxlength, $useupper=false, $usenumbers=false, $usespecial=false) {
	/*
	Author: Peter Mugane Kionga-Kamau
	http://www.pmkmedia.com

	*/
	$charset = "abcdefghijklmnopqrstuvwxyz";
	if ($useupper)   $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($usenumbers) $charset .= "0123456789";
	if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";   // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
	if ($minlength > $maxlength)
		$length = mt_rand($maxlength, $minlength);
	else
		$length = mt_rand($minlength, $maxlength);

	$key = null;

	for ($i=0; $i<$length; $i++)
		$key .= $charset[ (mt_rand( 0,( strlen( $charset )-1 ) ) ) ];

	return $key;
}



?>