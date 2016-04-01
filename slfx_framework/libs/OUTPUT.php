<?php

class OUTPUT
{
	//vars
	private $page_title = null; //
	private $page_subtitle = null; //
	private $head_include = array(); //
	private $foot_include = array(); //
	private $page_class; //
	private $page_breadcrumb;
	private $file_time; // last edit time of current files to be output
	private $page_content; // current output's contents
	private $head_content = array();
	private $header_code = false;
	private $parent;

	// contructor
  public function __construct($parent) {
      $this->parent = $parent;
  }

  public function setParent($parent) {
      $this->parent = $parent;
  }

	//public methods
	public function setPageClass($class) { $this->page_class = $class; }

	public function setPageTitle( $str = null ) {
		
		if( $str == null ) {
			return false;
		}
		
		return $this->page_title = $str;
	}

	public function setPageSubtitle( $str = null ) {
		if( $str == null ) {
			return false;
		}
		$this->page_subtitle = $str;
	}


	public function setPageHead( $str = null, $key = null ) {
		if($key === null && $str != null)
			$key = FILE::getFileName(false, $str);

		$this->head_include[$key] = $str;
	}

	public function setPageFoot( $str = null, $key = null ) {
		if($key === null)
			$key = FILE::getFileNameStatic(false, $str);

		$this->foot_include[$key] = $str;
	}

	public function getPageFoot() {
		return $this->foot_include;
	}

	/**
	*
	* setOutputDependancies is a quick way of calling these
	* 4 nessessary methods to render a page
	*
	* $this->output->setPageClass( $page_class );
	* $this->output->setPageHead();
	* $this->output->setPageFoot();
	* $this->output->setPageTitle();
	*
	*/
	public function setOutputDependancies( $page_class = null, $page_subtitle = null, $head_include = null, $foot_include = null ) {
		
		if( $page_class !== null )
			$this->setPageClass( $page_class );

		
				
		
		if( $page_subtitle !== null )
			$this->setPagesubtitle( $page_subtitle  );

		$this->setPageHead($head_include);
		$this->setPageFoot($foot_include);

		return true;
	}


	public function setPageContent( $str = null ) {

		if($str == null)
			return false;

		$this->page_content = $str;
		return true;
	}

	public function getPageContent() {

		return $this->page_content;
	}

	public function setPageBreadcrumb($label,$link) {
		$this->page_breadcrumb .= '<li class="top deletable"><a href="'. $link .'" class="top">' . $label . '</a></li>';
		return true;
	}

	public function setTime($time) {
		if($time > $this->file_time)
			return $this->file_time = $time;
	}

	public function getFileTime($file) {
		return filemtime($file);
	}

	public function getTime() {
		return $this->file_time;
	}

	public function completePage( $doc_content = null, $template = 'main' ) {

		$output_str = $doc_content ? $doc_content : '';

		// check that component exists
		if( !$this->checkForComponent( $template ) ) {
			throw new RuntimeException( $template . ' skin include is missing' );
			return false;
		}

		$this->confirmPageStatus();

		// dump output
		header('Cache-Control: max-age');
		$output_str = $this->fillGlobalVars( $this->getComponent( $template ) );
		$this->out( $output_str );

		return true;
	}


	public function ajaxCompleteBlob( $doc_content=null ) {
		
		// dump output
		header('Content-type: text/xml');

		$this->confirmPageStatus();

		$doc_content = $doc_content ? $doc_content : $this->page_content;
		$output_str = TEMPLATE::fill( $this->getComponent( 'ajax_content' ),
				array(
					  'DOC_TITLE' => rtrim( $this->page_title . ' - ' . $this->page_subtitle ),
					  'DOC_CLASS' => $this->page_class,
					  'DOC_HEAD' => str_replace( '+', '%20',urlencode( end($this->head_include) ) ),
					  'DOC_BREAD' => str_replace( '+', '%20',urlencode( $this->page_breadcrumb ) ),
					  'DOC_CONTENT' =>  str_replace( '+', '%20',urlencode( $doc_content ) ),
					  'DOC_FOOT' => str_replace( '+', '%20',urlencode( end($this->foot_include) ) )
					  )
				);


		$this->out( trim( $output_str ) );

		exit;

	}

	public function confirmPageStatus() {
		$this->parent->page_status['status'] = (int)$this->parent->page_status['status'];
		if($this->parent->page_status['status'] == 0) $this->parent->page_status['status'] = 200;
		return $this->serveHeader( $this->parent->page_status['status'] );
	}

	public function ajaxFragment( $doc_content ) {
		
		// dump output
		header('Content-type: text/xml');

		$this->confirmPageStatus();

		$output_str = TEMPLATE::fill( $this->getComponent( 'ajax_fragment_content' ),
				array(
					  'DOC_TITLE' => rtrim( $this->page_title  . ' - ' . $this->page_subtitle ),
					  'DOC_CLASS' => $this->page_class,
					  'DOC_CONTENT' =>  str_replace( '+', '%20', urlencode( $doc_content ) ),
					  'DOC_FOOT' => str_replace( '+', '%20', urlencode( end( $this->foot_include ) ) )
					  )
				);

		$this->out( trim( $output_str ) );

		exit;

	}


	/**
	*
	* Runs all the checks and
	* includes the inc and dumps content in to the inc
	* then includes it in the content for the site.
	*
	*/

	public function parseComponent($template, $content = null) {

		// check that component exists
		if(!$this->checkForComponent($template)) {
			  throw new RuntimeException( $template . ' skin include is missing' );
		}

		// fill template with custom data from content var/
		return $this->page_content .= TEMPLATE::fill( $this->getComponent( $template ), $content );

	}


	// load attached component
	private function getComponent($comp) {
		
		$file = new FILE( $this->parent->page_status['dir_skin'] . $comp . '.inc');
		return $file->get();
	}


	// look in skin dir for specific includes
	private function checkForComponent($comp) {

		// setup list - (filelist attributes must be bool words not numeric)
		$fList = new FILELIST( $this->parent->page_status['dir_skin'], true, false, 'inc', false );

		//compare list
		foreach( $fList->getArry() as $label => $path ) {
			if( $comp == eregi_replace("\.(.+)$", "", $label) )
				return true;
		}

		return false;
	}


	private function out( $output_str ) {
		$expires = 6000*60*24*14;
		header("Pragma: public");
		header("Cache-Control: maxage=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

		echo $output_str;

		return true;
	}


	private function fillGlobalVars( $template_str ) {
		global $SITE_VARS;

		$db_stats = null;
		$query_tag = null;
		$cache_tag = null;
		$class = null;
		$load_tag = null;
		$queries = null;

		$done_time = round(microtime('get_as_float') - $this->parent->page_status['loadtime_start'], 7);

		// db queries text
		if( $this->parent->page_status['enable_debug'] === true && $this->parent->page_status['enable_db'] === true ) {
			
			foreach( $this->parent->db->query_log as $item ) {
				$queries .= $item . "\n";
			}
			
			$queries = "\n\n----\n\n" . $queries . "\n\n----\n\n";
			$db_stats = $this->parent->db->query_count . " DB calls made. ";
		}

		// caching text
		if(	$this->parent->cache->getRecall() == true )
			$cache_tag = 'Loaded from ' . $this->parent->cache->getCacheSize() . ' cached file ';

		$load_tag = "<!--// Framework " . $this->parent->page_status['public_version'] . ". This page took " . $done_time . " sec to load. " . $db_stats . $queries . $cache_tag ." //-->";

		if( $this->parent->page_status['enable_debug'] === true )
			$class_debug = ' debug';

		$SITE_VARS['page_title_complete'] = rtrim( $this->page_title . ' - ' . $this->page_subtitle );
		$SITE_VARS['page_title'] = rtrim( $this->page_title );
		$SITE_VARS['head_include'] = implode( $this->head_include );
		$SITE_VARS['page_class'] = 'class="' . $this->page_class . $class_debug . '"';
		$SITE_VARS['foot_include'] = implode( $this->foot_include );
		$SITE_VARS['content'] = $this->page_content;
		//$SITE_VARS['url'] = $_SERVER["REQUEST_URI"] . $_SERVER['REDIRECT_URL'];
		$SITE_VARS['machine_time'] = date("Y-m-d" . '\T' . "H:i:s", $this->getTime() );
		$SITE_VARS['time'] =  gmdate("M d, Y",$this->getTime());
		$SITE_VARS['load_time'] = $load_tag;
		$SITE_VARS['version'] = $this->parent->page_status['public_version'];
		$SITE_VARS['page_breadcrumb'] = $this->page_breadcrumb;

		//print_r($SITE_VARS);
		return TEMPLATE::fill( $template_str, $SITE_VARS );
	}


	private function serveHeader( $code = 200 ) {

		// only run serveHeader once.
		if( $this->header_code === true )
			return false;

		$this->header_code = true;

		$errorCodes = array(
			200 => 'OK',
			301 => 'Moved permanently',
			302 => 'Found',
			303 => 'See Other',
			403 => 'Forbidden',
			404 => 'Not Found'
		);

		if( $code > 200 ) {
			//$this->setPageClass('error-' . $code);
		}

	 	header( "HTTP/1.1 $code " . $errorCodes[$code] );

	 	return true;
	}

}

?>
