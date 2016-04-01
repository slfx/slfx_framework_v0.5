<?php



/**
 *
 */
class CUSTOMIZE
{
  //vars

  //constructor
  public function __construct( $parent=null ) {
   global $conf_obj;
   
   $this->parent = $parent;

    // read language file, only after user has been initialized
    $language = CUSTOMIZE::getLanguageName();
    include( $conf_obj->page_conf['dir_lang'] . ereg_replace( '[\\|/]', '', $language) . '.php');

  }

  //public

  //private

  //(public)
 
  public function getBrowserType() {

  	$ua = $_SERVER["HTTP_USER_AGENT"];
  	$type = false;
  	//if( strpos($ua, 'Linux') )				$type = 'linux';
  	//if( strpos($ua, 'Macintosh') ) 			$type = 'mac';
  	//if( strpos($ua, 'Windows') )			$type = 'win';

  	//if( strpos($ua, 'Safari') ) 			$type = 'safari'; // All Safari
  	if( strpos($ua, 'Safari/419') )			$type = 'safari 2'; // Safari 2
  	if( strpos($ua, 'Safari/525') )			$type = 'sarari 3'; // Safari 3
  	if( strpos($ua, 'Safari/528') )			$type = 'safari 3-1'; // Safari 3.1
  	if( strpos($ua, 'Safari/531') )			$type = 'safari 4'; // Safari 4

  	if( strpos($ua, 'Chrome') ) 			$type = 'chrome'; // Google Chrome

  	if( strpos($ua, 'Firefox') ) 			$type = 'firefox'; // All Firefox
  	if( strpos($ua, 'Firefox/2.0') )	 	$type = 'firefox 2'; // Firefox 2
  	if( strpos($ua, 'Firefox/3.0') )		$type = 'firefox 3'; // Firefox 3
  	if( strpos($ua, 'Firefox/3.6') )		$type = 'firefox 3-6'; // Firefox 3.6

  	if( strpos($ua, 'MSIE') ) 				$type = 'ie'; // All Internet Explorer
  	if( strpos($ua, 'MSIE 7.0') )			$type = 'ie 7'; // Internet Explorer 7
  	if( strpos($ua, 'MSIE 8.0') )			$type = 'ie 8'; // Internet Explorer 8
  	if( strpos($ua, 'MSIE 9.0') )			$type = 'ie 9'; // Internet Explorer 8
  	if( strpos($ua, 'MSIE 10.0') )			$type = 'ie 10'; // Internet Explorer 8

  	//$type = preg_match("/\bOpera\b/i", $ua); // All Opera

  	return $type;
  }

  public function isIe() {
      if (isset($_SERVER['HTTP_USER_AGENT']) &&
      (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
          return true;
      else
          return false;
  }

  public function detectBot() {
  	$botlist = array(
                  "Teoma",
                  "alexa",
                  "froogle",
                  "inktomi",
                  "looksmart",
                  "URL_Spider_SQL",
                  "Firefly",
                  "NationalDirectory",
                  "Ask Jeeves",
                  "TECNOSEEK",
                  "InfoSeek",
                  "WebFindBot",
                  "girafabot",
                  "crawler",
                  "www.galaxy.com",
                  "Googlebot",
                  "Scooter",
                  "Slurp",
                  "appie",
                  "FAST",
                  "WebBug",
                  "Spade",
                  "ZyBorg",
                  "rabaz");

      foreach($botlist as $bot) {
        if(ereg($bot, $_SERVER["HTTP_USER_AGENT"])) {
  		/*
            if($bot == "Googlebot") {
              if (substr($REMOTE_HOST, 0, 11) == "216.239.46.") $bot = "Googlebot Deep Crawl";
              elseif (substr($REMOTE_HOST, 0,7) == "64.68.8") $bot = "Google Freshbot";
            }
            if ($QUERY_STRING != "") {
              $url = "http://" . $SERVER_NAME . $PHP_SELF . "?" . $QUERY_STRING . "";
            } else {
              $url = "http://" . $SERVER_NAME . $PHP_SELF . "";
            }

  		// settings
  		$to = "w.fritz@gmail.com";
  		$subject = "Detected: $bot on $url";
  		$body = "$bot was deteched on $url\n\n
  		Date.............: " . date("F j, Y, g:i a") . "
  		Page.............: " . $url . "
  		Robot Name.......: " . $HTTP_USER_AGENT . "
  		Robot Address....: " . $REMOTE_ADDR . "
  		Robot Host.......: " . $REMOTE_HOST . "
  		";

  		mail($to, $subject, $body);
  		*/
  		return true;
        }
      }
  	return false;
  }


  function isMobile() {

  	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

  	if( stristr($ua, "windows ce") or
   	stristr($ua, "avantgo") or
   	stristr($ua,"mazingo") or
   	stristr($ua, "mobile") or
   	stristr($ua, "t68") or
   	stristr($ua,"syncalot") or
   	stristr($ua, "blazer") )
  	 	return true;

   	return false;
  }


  /**
  * Checks if a certain language/plugin exists
  */
  public function checkLanguage($lang) {
  	return file_exists( $this->parent->page_status['dir_lang'] . ereg_replace( '[\\|/]', '', $lang) . '.php');
  }

  /**
  * Returns the name of the language to use
  * preference priority: member - site
  * defaults to english when no good language found
  *
  * checks if file exists, etc...
  */
  public function getLanguageName() {

  	// use default language
  	if ( CUSTOMIZE::checkLanguage( $this->parent->page_status['dir_lang'] ) )
  		return $this->parent->page_status['dir_lang'];
  	else
  		return 'english';
  }

}

 ?>
