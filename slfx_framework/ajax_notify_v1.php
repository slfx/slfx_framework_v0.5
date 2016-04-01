<?php
	
$DIR_ROOT =	'../';	
$conf_obj = new stdClass();
$conf_obj->page_conf = array(); 

// include config 
include('../slfx_config.php');
	
/**
*
* Nofify API takes post and get in a special format.
*
* /ajax_notify_v1.php?mode/action/option
*
*/	
if( isset( $_SERVER["REQUEST_URI"] ) ) {
	
	$uris = explode( "?", $_SERVER["REQUEST_URI"] );
	$uris = explode( "/", $uris[1] );
	
	foreach( $uris as $part ) {

		// don't allow standard gets, or direct file requests
		if( $part == '' || strstr($part, '?') )
			continue;

		$conf_obj->page_conf['paths'][] = undoMagic($part);
	}
}

// get mode
$mode = $conf_obj->page_conf['paths'][0];

switch( $mode )
{
	case 'login':
	
		// load framework
		echo selector( 'login' );
		
				
		break;
	case 'create':
		
		// load framework
		echo selector( 'create' );
		
				
		break;
	

}

?>