<?php

$page_title = 'Privacy Policy';
$page_class = 'main welcome';

/**
* setOutputDependancies(page_class,page_title,head_include,foot_include)
* Is nessessary to render a page.
* IMPORTANT: it calls two methods required to inhibit crashing
* browsers with unrelenting ajax calls.
*/
$this->output->setOutputDependancies( $page_class, $page_title /*head_include*/ /*foot_include*/ );

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

?>
