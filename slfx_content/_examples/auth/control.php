<?php
$page_class = 'main home';


/**
* setOutputDependancies(page_class,page_title,head_include,foot_include)
* Is nessessary to render a page.
* IMPORTANT: it calls two methods required to inhibit crashing
* browsers with unrelenting ajax calls.
*/
$this->output->setOutputDependancies( $page_class /* $page_title*/ /*head_include*/ /*foot_include*/ );


/**
* Some basic methods in the database api
*/
// setup the user type db class
$member_db = new MEMBER();

// table selector 'null' will set table to default
$member_db->useTable();

// makes the default userbased typed table
//$str = '<br /> create table: ' . $member_db->makeMemberTable();

// choice member or time
$str .= '<br /> store data ' . time() . ': ' . $member_db->storeMemberData( 'time', time() );

// lookup 5 records
$member_db->setOrderBy( 'data' );
$member_db->setLimit( 5 );

$times_arry = $member_db->getMemberBulkData( 'time' );

$str .= '<br /> query 5 times: <br />' . implode( $times_arry, '<br />' );

//$str .= '<br /> remove all "time": '. $member_db->removeMember('time'); 

// commit string to template array
$PAGE_ZONES['code-block'] = $str;





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
