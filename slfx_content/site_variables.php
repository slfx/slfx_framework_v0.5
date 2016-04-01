<?php

/**
* SITE VARIABLES
*/

// vars
$page_title = 'My Company Name';

$SITE_VARS['meta'] = '<meta content="Web Develpment Studio professional with decades of experience &amp; hundreds of clients, ready to build your web projects; large or small." name="description" />
<meta content="New York, NYC, New York City, Web Development, Photography, Hosting, Content Management Systems, CMS, Search Engine Optimization, SEO, Brooklyn, Manhattan, E-Commerce, Programming, Site Maintainance" name="keywords" />
<link rel="icon" type="image/png" href="/favicon.png" />';

$this->output->setPageHead('<link rel="stylesheet" href="' . $this->page_status['dir_exec'] . '/slfx_css/' . $this->page_status['asset_style_global'] . '" media="screen" />' . "\r\n", 'global_lib_css');

$this->output->setPageHead('<link href="https://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700,400italic" rel="stylesheet" type="text/css">', 'titles-font');

$this->output->setPageHead('<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic" rel="stylesheet" type="text/css">', 'text-bodies');

$this->output->setPageHead('<script src="' . $this->page_status['dir_exec'] . '/slfx_framework/scripts/jquery2.min.js"></script>' . "\r\n",'jquery2.min');

$this->output->setPageHead('<script src="' . $this->page_status['dir_exec'] . '/slfx_framework/scripts/' . $this->page_status['asset_script_global'] . '"></script>' . "\r\n",'global_lib_js');

$this->output->setPageFoot('<script src="' . $this->page_status['dir_exec'] . '/slfx_framework/scripts/' . $this->page_status['asset_script_modules'] . '"></script>' . "\r\n", 'global_modules');

if( $this->page_status['enable_ajax'] === true )
	$this->output->setPageFoot('<script src="' . $this->page_status['dir_exec'] . '/slfx_framework/scripts/' . $this->page_status['asset_script_ajax'] . '"></script>' . "\r\n", 'global_ajax');

// Dynamic Variables
$SITE_VARS['year'] = date("Y");

// Javascript vars passed
$this->output->setPageHead('<script>
	/* global vars */
	var useAjax = ' . bool2str( $this->page_status['enable_ajax'] ) . ';
	var useServerCaching = ' . bool2str( $this->page_status['enable_caching'] ) . ';
	var useShortUrl = ' . bool2str( $this->page_status['enable_short_urls'] ) . ';
	var useDataBase = ' . bool2str( $this->page_status['enable_db'] ) . ';
	var dbReadOnly = ' . bool2str( $this->page_status['db_readonly'] ) . ';
	var useSite = "' . $this->page_status['use_site'] . '";
	var urlPrefix = "' . $this->page_status['use_prefix'] . '";
	var isMobile = ' . bool2str( CUSTOMIZE::isMobile() ) . ';
	var pageuri = "' . $this->page_status['uri'] . '";
	var pageStatus = ' . $this->page_status['status'] . ';
	var useDebug = ' . bool2str( $this->page_status['enable_debug'] ) . ';
</script>', 'js_header_globals');

//Detect mobile devices
if( CUSTOMIZE::isMobile() )
	$this->output->setPageHead( '<link rel="stylesheet" href="' . $this->page_status['dir_exec'] . '/sflx_css/mobile.css" media="screen" />' );

// Site Variable Constants
$this->output->setPageTitle( $page_title );

// global site modules
if(count( $this->page_status['paths'] ) > 0) {
	$page_name = $this->page_status['paths'][ count( $this->page_status['paths'] ) - 1 ];
	$str_lan = strlen($page_name) > 17 ? 17 : strlen($page_name) ;

	$PAGE_ZONES['parallax-image'] = 'url(/img.php?w=1400&h=600&a=4&c=1&f=/media/parallax/' . $str_lan . '.jpg);';
}
?>
