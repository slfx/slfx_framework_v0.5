<?php

$page_class = 'works coding url-shortener';
$page_title = ' [Nd] URL Shortener';


include_once($this->page_status['dir_libs'] . 'SHORTEN.php');

if(isset($_REQUEST['url'])){
	$url = get_magic_quotes_gpc() ? stripslashes( trim( $_REQUEST['url'] ) ) : trim( $_REQUEST['url'] );
	$url = str_replace("Enter a URL starting with ", '', urldecode($url) );
}

if( isset( $url ) && !empty( $url ) ) {
	
	// first 'create' the short link, then look it up
	// if post request, check link validity
	if( isset( $_POST['url'] ) )
		echo new SHORTEN( 'create', $url, true, $this );
	else
		new SHORTEN( 'create', $url, false, $this );

	$short = new SHORTEN( 'lookup', null, false, $this );
	
	if( true == ( $r = $short->lookupSurl( $url ) ) )
		die( $this->page_status['short_url'] . '/' . $r );

	die( 'Enter a URL starting with http://' );
}


/**
* setOutputDependancies(page_class,page_title,head_include,foot_include)
* Is nessessary to render a page.
* IMPORTANT: it calls two methods required to inhibit crashing
* browsers with unrelenting ajax calls.
*/
$this->output->setOutputDependancies( $page_class, $page_title /*$head_include*/ /*$foot_include*/ );

/**
*
* CACHE
* version 1.0
*	date 2016-02-23
*
*  // how to use this
*  Place this code block where ever we can
*  exit the current script, and switch to cache
*
*  // what this does
*  1) register name
*  2) check for existing or activate 'storage' mode
*  3) if existing, activate 'recall' mode, and choose exit point and return true
*
*/
// check cache
if( $this->cache->registerName( 'page_public', implode('/', $this->page_status['paths'] ) ) == true ) {
	if( $this->cache->moduleExists( /* if mentions dont matter*/ true ) ) {
		// found cached module
		$this->cache->setRecall( true );
		// exiting
		return true;
	}
	// found nothing, engage storage mode
	$this->cache->setStore( true );
	/**
	* template_vars
	*
	* vars in this array will not be hardcoded to the
	* blob, and will be filled with fresh data on each
	* query.
	*/
	$this->cache->protected_vars = $PAGE_ZONES;
}
/**
*
* END CACHE
*
*/






/**
* markup this code for color highlighting and output to template
*/

//get the file
$include = $this->page_status['dir_libs'] . 'SHORTEN.php';
$code_title = 'URL Shorten lib';

$file = new FILE( $include );
$code_blob = str_replace( $find_arry, $replace_arry, highlight_string( $file->get(), true) );
$aVars = array(
	'title' => $code_title,
	'code_blob' => $code_blob
);
$comp = 'template.code.markup';
$file = new FILE($this->page_status['dir_skin'] . $comp . '.inc');
$PAGE_ZONES['code-block'] = TEMPLATE::fill($file->get(), $aVars);

?>
