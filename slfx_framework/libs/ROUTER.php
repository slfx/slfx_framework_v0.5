<?php

class ROUTER
{
	//vars
	private $filelist = null;

	public $action = null;
	public $short = null;
	public $output = null;
	public $db = null;
	public $cache = null;
	public $page_status = array();

	private	$view_file_path = null;		
	private $control_file_path = null;

	//constructor
	public function __construct() {
		global $conf_obj;

		$this->page_status['ajax'] = false;
		$this->page_status['paths'] = array();
		$this->page_status['uri'] = null;
		$this->page_status['getcom'] = array();
		$this->page_status['status'] = 200;

		// dump CONF to router object array
		$this->page_status = array_merge( $conf_obj->page_conf, $this->page_status );
		// parser used to render html output
		$this->output = new OUTPUT( $this );

		// db for all needed db actions
		$this->db = new DATABASE( $this );
	}


	private function checkShorten() {
		
		include_once( $this->page_status['dir_libs'] . 'SHORTEN.php' );
		$this->short = new SHORTEN( null, null, false, $this );
		
		if( $this->page_status['short_url'] == str_replace( 'www.', '', $_SERVER["HTTP_HOST"] ) ) {
			$this->short = new SHORTEN( 'redirect', $this->page_status['uri'], false, $this );

			// check if short link,
			// redirect if available
			$this->short->redirect();
		} 
		
		return true;		
	}


	/**
	* .htaccess is set to forward errors to
	* single page. Every request starts here..
	* Look at uri for path.
	*
	*/
	public function registerPath() {
		if( isset( $_SERVER["REQUEST_URI"] ) ) {
			$uris =  explode( "/", $_SERVER["REQUEST_URI"] );
			foreach( $uris as $part ) {

				if($part == '!') {
					$this->page_status['ajax'] = true;
					continue;
				}

				// don't allow standard gets, or direct file requests
				if($part == '' || strstr($part, '?') || strstr($part, '.inc') )
					continue;

				$this->page_status['paths'][] = undoMagic($part);
			}
		}
		return true;
	}


	/**
	* Build path vars and get vars --
	* parse get vars for any action
	* that wants to use them.
	*/
	private function registerPathVars() {

		$temp = array();
		$do = false;

		foreach( $this->page_status['paths'] as $part ) {
			$part = undoMagic( $part );

			if($part == '')
				continue;

			if( $part == $this->page_status['dir_exec'] )
				continue;

			// this will allow us to pass
			// get vars to the load Extensions
			if( $part === 'do' || $do === true ) {
				$do = true;
				$this->page_status['getcom'] = $part;
				continue;
			}

			// remove anything nasty
			$this->page_status['uri'] .= $part . '/';
			$temp[] = undoMagic( $part );
		}

		$this->page_status['paths'] = $temp;

		// used to load content later
		$this->page_status['uri'] = trim( $this->page_status['uri'], '/');

		return true;
	}


	public function doAction( $action ) {
		global $conf_obj, $PAGE_ZONES;

		// vars
		$this->action = $action;

		$this->registerPath(); // cut path into array
		$this->registerPathVars(); // use path array to form GETCOM, URI, etc.

		// check if loading a profile number
		//if( isset( $this->page_status['paths'][0] ) && is_numeric( $this->page_status['paths'][0] ) )
		//	return $this->profile();

		// The action controller
		switch( $action ) {
			case 'create':
				$auth = new AUTH( $this ); 
				
				// if user or password missing error out
				if( postVar('create_email') == false )
					return '0|' . _ERROR_ADDRESSMISSING;
				
				// if user or password missing error out
				if( isValidAddress( postVar('create_email') ) == false )
					return '0|' . _ERROR_ADDRESSMISSING;
					
				// if user or password missing error out
				if( postVar('create_password') == false )
					return '0|' . _ERROR_PASSWORDMISSING;
				
				// setup the user type db class
				$member_db = new USERTYPE();
				$member_db->useTable();
				
				// make sure account doesn't already exist
				if ( true == ( $r = $member_db->getIDbyEmail( postVar('create_email') ) ) )
					return TEMPLATE::fill( '0|' . _ERROR_ADDRESSINUSE, array( 'uid' => md5( postVar('create_email') . $auth->getSalt() ) ) );
				
				// check login and password
				if( $auth->create( trim( postVar('create_email') ), postVar('create_password') ) ) {
			
					// set new cookies 
					$auth->newCookieKey();
					$auth->setCookies( postVar('private') );
					
					// log access
					LOG::staticWriteBasic('access',  ' - signin page login' );
					
					return '1|Account created';
				
				}
				
				return '0|Can\'t create account';
				
				
	
				break;
			
			case 'login':
				
				$auth = new AUTH( $this ); 
				
				// check login and password
				if( $auth->login( trim( postVar('signin_email') ), postVar('signin_password') ) ) {
			
					// set new cookies 
					$auth->newCookieKey();
					$auth->setCookies( postVar('private') );
					
					// log access
					LOG::staticWriteBasic('access', $member->getId() . ' - signin page login' );
				}

	
				break;
		
			case 'loader':
							
				if( $this->page_status['enable_short_urls'] === true ) {
					$this->checkShorten();
				}
					
				include_once( $this->page_status['dir_libs'] . 'CUSTOMIZE.php' );
				$customize = new CUSTOMIZE( $this ); // for browsers 

				// we'll be loading content from here
				$this->page_status['dir_content'] . $this->page_status['uri'];

				// Load file directory contents to array for later use
				$this->filelist = new FILELIST( $this->page_status['dir_content'] . $this->page_status['uri'], true, false,'php|inc', false );

				// if files are not located
				if( count( $this->filelist->getArry() ) < 1) {
					// File not found
					$this->page_status['status'] = 404;
					$this->page_status['uri'] = '.error/404/';
					$this->filelist = new FILELIST( $this->page_status['dir_content'] . $this->page_status['uri'], true, false,'php|inc', false );
				}

				include_once( $this->page_status['dir_libs'] . 'CACHING.php');
				$this->cache = new CACHING( $this );

				
				// loads all required dependents and preps content
				if( $this->loadAssets() ) {
					// source did load properly
				}
				
				// prepare to load content template
				$content_file_obj = $this->registerTemplate();


			 	//get cached version and get out!
				if( $this->cache->getRecall() == true ) {
					
					$this->output->setPageContent( TEMPLATE::place( $this->cache->getModule(), $PAGE_ZONES ) );

					// if we are just requesting a part of page via AJAX
					// we can just dump it out here
					if( $this->page_status['ajax'] === true ) {
						$this->page_status['status'] = 200;
						$this->output->ajaxFragment( TEMPLATE::removeTags( $this->output->getPageContent() ) );
					}
					return true;
				}

				// load template
				$content_template = $content_file_obj->get();
				
				// store all approprate info in blob
				if( $this->cache->getStore() == true ) {
					$this->cache->writeModule( $content_template . end( $this->output->getPageFoot() ), $PAGE_ZONES );
				}

				// caching operations are complete.
				// Finish out normally
				$content = TEMPLATE::fill( $content_template, $PAGE_ZONES );

				if( $this->page_status['ajax'] === true ) {
					$this->page_status['status'] = 200;
					$this->output->ajaxFragment( $content );
				}

				$this->output->setPageContent( $content );
				break;

			default:
				//error.
				NULL;

			return true;
		}
	}

	
	private function loadAssets() {
		global $PAGE_ZONES;

		if( $this->page_status['enable_debug'] === true )
			$this->output->parseComponent( ('general_error'), array( 'contents' => _DEBUG_ACTIVE ) );

		// Make these vars available to CMS
		$PAGE_ZONES['INFO_PAGE_URI'] = 	$this->page_status['uri'];

		if( count( $this->page_status['paths'] ) > 1 ) {
			$PAGE_ZONES['INFO_PAGE_NAME'] = $this->page_status['paths'][ count( $this->page_status['paths'] )-1 ];			
			$PAGE_ZONES['INFO_PAGE_PARENT'] = $this->page_status['paths'][ count( $this->page_status['paths'] )-2 ];
		}

		// get any assocated source code to support content
		if( $this->page_status['enable_short_urls'] == true ) {
			$PAGE_ZONES['INFO_PAGE_SHORTNAME'] = $this->short->lookupSurl( $this->page_status['url_index'] . $this->page_status['uri'] );
		}

		return $this->registerSource();	
	}

	/**
	* Load file source to processing
	*/
	private function registerSource() {
		global $conf_obj, $PAGE_ZONES, $SITE_VARS;

		// vars
		$this->view_file_path = null;		
		$this->control_file_path = null;


		// Check for any inherited source files
		if(file_exists( $this->page_status['dir_content'] . '/site_variables.php'))
			include_once( $this->page_status['dir_content'] . '/site_variables.php');

		if( count( $this->page_status['paths'] ) >= 1 ) {
			// first upper inherit
			if( file_exists( $this->page_status['dir_content'] . $this->page_status['uri'] . '/../../inherit.php') )
				include_once( $this->page_status['dir_content'] . $this->page_status['uri'] . '/../../inherit.php' );
		}

		// first upper inherit
		if( file_exists( $this->page_status['dir_content'] . $this->page_status['uri'] . '/../inherit.php') )
			include_once( $this->page_status['dir_content'] . $this->page_status['uri'] . '/../inherit.php');

		// then sibling inherit
		if(file_exists( $this->page_status['dir_content'] . $this->page_status['uri'] . '/inherit.php'))
			include_once( $this->page_status['dir_content'] . $this->page_status['uri'] . '/inherit.php');

		// check for CMS source code assets
		$this->control_file_path = $this->filelist->search('control', 'php');
		$this->view_file_path = $this->filelist->search( 'view', 'inc' );

		// More has been loaded -- Update time
		$this->output->setTime( $this->output->getFileTime( $this->view_file_path ) );

		// error if not found
		if( $this->control_file_path == false ) {


			// source.php files not required so we are not going to error out
			// But enter place holders for parser-> head include and parser->foot include.
			$this->output->setPageHead();
			$this->output->setPageFoot();

			return false;
		}

		require_once($this->control_file_path);

		// More has been loaded -- Update time
		//$parser->setTime( $parser->getFileTime( $file_path ) );

		// Check for any ERROR flags after loading source.
		// If something threw an ERROR then offer the error page.
		/*
		if( $this->page_status['status'] !== 200 ) {
		  $this->filelist = new FILELIST( $this->page_status['dir_content'] . '.error/' . $this->page_status['status'], true, false, 'php|inc', false );
		  $file_path = $this->filelist->search('control', 'php');
		  require_once( $file_path );
		}
		*/
		return true;
	}

	/**
	* Load file content to processing and parsing
	*/
	private function registerTemplate() {

		$file = new FILE( $this->view_file_path );
		return $file;
	}

	// used for breadcrumb
	private function registerBreadcrumb() {

		// form full url if they passed get vars
		$c = 0;
		$full_uri = $this->page_status['uri'];
		$uri_str = null;

		if( count( $this->page_status['getcom'] ) > 0 ) {
			do {
				$uri_str .= '/'. $this->page_status['getcom'][$c];
				$c++;
			} while ($c < count($this->page_status['getcom']) );
			$full_uri = $this->page_status['uri'] . $uri_str;
		}

		// Sets breadcrumb
		if( $full_uri != '' && $full_uri != '/' ) {
			$parts = explode( '/' , $full_uri);
			$link = '/';
			foreach ( $parts as $label ) {
				if(
					strstr($label, '/.') ||
					strstr($label, 'do') ||
					strstr($label, '?')
				) continue;

				$link .= $label . '/';
				$this->output->setPageBreadcrumb( $label, $link );
			}
		}
		return true;
	}

}

?>
