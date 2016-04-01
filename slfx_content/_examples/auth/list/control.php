<?php
$page_class = 'main home';


/**
* setOutputDependancies(page_class,page_title,head_include,foot_include)
* Is nessessary to render a page.
* IMPORTANT: it calls two methods required to inhibit crashing
* browsers with unrelenting ajax calls.
*/
$this->output->setOutputDependancies( $page_class /* $page_title*/ /*head_include*/ /*foot_include*/ );

// vars
$str = null;
$rows = null;

/**
* Some basic methods in the database api
*/
// setup the user type db class
$db = new USERTYPE();

// table selector 'null' will set table to default
$db->useTable();

// lookup max 5 records
$db->setOrderBy( 'member' );
$db->setLimit( /*limit*/ 5, /*reverse*/ true );

// get all user ids
$member_arry = $db->getUsers();

//$times_arry = $db->getUserTypeBulk( 'time' );
foreach($member_arry as $data) {
	$str .= '<tr><td> member: </td><td>' . $data . '</td></tr>';
}


// commit string to template array
$PAGE_ZONES['code-block-member']  = '<table class="data_table"><tr><th> Label </th><th>ID</th></tr>' . $str . '</table>'; 




// lookup max 5 records
$db->setOrderBy( 'member' );
$db->setLimit( /*limit*/ 5, /*reverse*/ true );

// get all user ids
$data_arry = $db->getMultiUserTypeBulk($member_arry, array('email','password') );



if( is_array( $data_arry ) ) {
	foreach( $member_arry as $member_data ) {

		if( isset( $data_arry[$member_data][fnvhash('email')] )
			&& $data_arry[$member_data][fnvhash('email')] != '' ) {
			$email = $data_arry[$member_data][fnvhash('email')]; 
		}
		
		if( isset( $data_arry[$member_data][fnvhash('password')] )
			&& $data_arry[$member_data][fnvhash('password')] != '' ) {
			$password = $data_arry[$member_data][fnvhash('password')]; 
		}

		if( isset(  $data_arry[$member_data]['uid']) &&  $data_arry[$member_data]['uid'] > 0 )
			$rows .= '<tr><td><a href="/' .  $data_arry[$member_data]['uid'] . '">' . $email . '</a></td><td>' . $password . '</tr>';
	}
}


// commit string to template array
$PAGE_ZONES['code-block-data'] =  '<table class="data_table"><tr><th> email </th><th> Password </th></tr>' . $rows . '</table>'; 


// form post submit api 
$PAGE_ZONES['ajax_api'] = $conf_obj->page_conf['ajax_api'] . '?create';


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
