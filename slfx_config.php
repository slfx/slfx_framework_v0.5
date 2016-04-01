<?php

$conf_obj->page_conf['loadtime_start'] = microtime('get_as_float');

// Set localized time
date_default_timezone_set('America/New_York');

// mySQL connection information
$SECURE_CONF['MYSQL_HOST'] = 			'localhost';
$SECURE_CONF['MYSQL_USER'] = 		 	'';
$SECURE_CONF['MYSQL_PASSWORD'] =	'';
$SECURE_CONF['MYSQL_DATABASE'] = 	'';
$SECURE_CONF['MYSQL_PREFIX'] =    'slfx';

// these dirs are normally sub dirs of root dir
$conf_obj->page_conf['dir_libs'] = 					$DIR_ROOT .'slfx_framework/libs/';
$conf_obj->page_conf['dir_lang'] = 					$DIR_ROOT .'slfx_framework/language/';
$conf_obj->page_conf['dir_skin'] = 					$DIR_ROOT . 'slfx_skin/';
$conf_obj->page_conf['dir_content'] = 			$DIR_ROOT . 'slfx_content/';
$conf_obj->page_conf['dir_media'] = 				$conf_obj->page_conf['dir_content'] . 'media/';
$conf_obj->page_conf['dir_exec'] = 					'';

$conf_obj->page_conf['cookie_prefix'] = 		'';
$conf_obj->page_conf['cookie_domain'] = 		'';
$conf_obj->page_conf['cookie_secure'] = 		false;
$conf_obj->page_conf['cookie_path'] = 			'/';
$conf_obj->page_conf['cookie_salt'] =			'Put something else here';

$conf_obj->page_conf['alertOnSentHeader'] = 	true;

// Global Settings
/**
* Debugging
* Very helpful to find errors.
* Adds debuggin id to page to make
* creating markup for testing and debugging
* easy. All so adds a "debug mode" banner to page
* content.
*
* Not strongly supported in combination with
* CONF['ENABLE_AJAX']
*/
$conf_obj->page_conf['enable_debug'] = 		false;

/**
* Database
*
*/
$conf_obj->page_conf['enable_db'] = 		false;
$conf_obj->page_conf['db_readonly'] =	 	false;

/**
* Caching
* server side page caching
*/
$conf_obj->page_conf['enable_caching'] =	false;

/**
* Ajax Page Loading
* This feature is fully supported,
* but can make page loading situations
* complicated. Only set this if you know
* this framework and the concepts behind it.
*/
$conf_obj->page_conf['enable_ajax'] =		false;
$conf_obj->page_conf['page_ttl'] =			4; // minutes
$conf_obj->page_conf['ajax_api'] =			'/slfx_framework/ajax_notify_v1.php';

/**
* URL Shortener
* Activate URL shortener
*/
$conf_obj->page_conf['enable_short_urls'] = false;

//$CONF['UrlPrefix'] = 'http';
//if( isset( $_SERVER['REDIRECT_HTTPS'] ) && $_SERVER['REDIRECT_HTTPS'] == 'on'  )
//	$CONF['UrlPrefix'] = 'https';

/* Site Root */
$conf_obj->page_conf['url_index'] = 'http://www.domain.com/';
$conf_obj->page_conf['use_site'] = 'www.domain.com';
$conf_obj->page_conf['short_url'] = 'dman.co';
$conf_obj->page_conf['use_prefix'] = 'http';

/* User and site global preferences */
$conf_obj->page_conf['language'] = 			'english';

// dependancies
$conf_obj->page_conf['asset_style_global'] = 'global.20150709.css';
$conf_obj->page_conf['asset_script_global'] = 'global_lib.20150709.js';
$conf_obj->page_conf['asset_script_ajax'] = 'global_ajax_lib.20150709.js';
$conf_obj->page_conf['asset_script_modules'] = 'global_modules_lib.20160225.js';

//
$conf_obj->page_conf['public_version'] = 		   'v0.5b';
$conf_obj->page_conf['public_version_date'] = 	'2016-02-15';

// include libs
include( $conf_obj->page_conf['dir_libs'] . 'globalfunctions.php' );

?>
