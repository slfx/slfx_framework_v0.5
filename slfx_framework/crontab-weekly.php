<?php

ignore_user_abort(true);
if ( !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') )
	die();

/**
 * Tell us we are doing the CRON task.
 *
 * @var bool
 */
define('DOING_CRON', true);

include_once( '/var/www/vhosts/silverfoxie.com/httpdocs/' . "config.php" );

// report all errors!
error_reporting(2047);
ini_set("display_errors",1);

include_once( $DIR_LIBS . 'CRON.php' );

// call cron
$cron = new CRON('weekly'); // weekly or daily


?>