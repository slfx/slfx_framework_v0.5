<?php


class CACHING {

	// public vars
	public $module = null;

	// private vars
	private $cache_dir = 'slfx_framework/cache/blobs/';
	private $cache_file_type = 'cb';

	private $recall = false;
	private $store = false;

	private $public = true;
	private $module_type = null;
	private $mobule_url = null;

	private $blob_name = null;
	private $blob_name_short = null; // no mentions
	private $blob_name_full = null; // name with ext
	private $blob_exists = false; // prove it exists
	private $blob_content = null;
	private $properties = array();
	private $cache_size = 0;

	public $protected_vars = array();

	private $f = null; // file object
	/**
	* things that need caching
	*
	* notifications menu
	*/

	// constructor
	public function __construct( $parent ) {
		global $DIR_ROOT;

		// init vars
		$this->cache_dir = $DIR_ROOT . $this->cache_dir;

		 $this->parent = $parent;
	}
	
	public function setParent($parent) {
		$this->parent = $parent;
	}

	public function setStore( $bool ) {
		if( $this->parent->page_status['enable_caching'] === false )
			return false;
		
		$this->store = $bool;

	}

	public function setRecall( $bool ) {
		if( $this->parent->page_status['enable_caching'] === false )
			return false;
		
		$this->recall = $bool;
	}

	public function getRecall() {
			return $this->recall;
	}

	public function getStore() {
			return $this->store;
	}

	public function getCacheSize() {
			return $this->cache_size;
	}

	/**
	* does initial check on name and properties to make sure they are good
	* must be called before anything else.
	*/
	public function registerName( $module_type, $module_url='*' ) {
		if( $this->parent->page_status['enable_caching'] === false )
			return false;
		
		// vars
		$this->blob_name = null;
		$this->blob_name_short = null;
		$this->blob_name_full = null;
		$this->blob_public = true;

		$module_url = str_replace('/', ';', $module_url);
		$this->blob_name .= 't:' . $module_type . '|p:' . $module_url . '|';

		$this->blob_name_short = $this->blob_name;

		// add file type extention
		$this->blob_name_full .=  $this->blob_name . '.' . $this->cache_file_type;

		return $this->blob_name;
	}

	/**
	* Finds module by name
	*
	* Will look for name fragements if option is active. This
	* is usful if you don't know what users are mentioned in blob
	* file name.
	*
	* Name_fragments will cause search to only look for part of the file name 
	* not an exact file name match.
	*/
	public function moduleExists( $name_fragment = false ) {

		if( $this->parent->page_status['enable_caching'] === false )
			return false;

		if( $this->blob_exists == true )
			return true;

		$this->flist = new FILELIST( $this->cache_dir,false,false,$this->cache_file_type,false,0,false);

		/**
		* search cache directory
		* will only return the first match
		* it nothing is found we are done here.
		*/		
		if( false == ( $r = $this->flist->search( $this->blob_name_short ) ) )
			return false;

		/**
		* check file creation date
		* If cache blob is older than orginal content
		* file dates, blob is out of date
		* and should be replaced. Exit cache recall.
		*/
		$blob_date = filemtime( $this->cache_dir . $this->blob_name_full );
		if( $blob_date < $this->parent->output->getTime() ) {
		//	echo date('Y m d h:m:s ', $blob_date) . ' < ' .  date('Y m d h:m:s ', $this->parent->output->getTime());
		
			$this->blob_exists = false;
			$this->recall = false;
			$this->store = true;

			return false;
		}
		
		//echo date('Y m d h:m:s ', $blob_date) . ' > ' .  date('Y m d h:m:s ', $this->parent->output->getTime());
		
		// if blobname is a fragment
		if($name_fragment == true) {			
			$this->blob_name_full = $this->flist->getFileName($r);
			$this->blob_exists = true;
			return true;
		}
		
		// Only full name blobs accepted 
		if( $this->flist->getByName( $this->blob_name_full ) === false )
			return false;

		// set this to true so we don't have to check next time
		$this->blob_exists = true;

		return true;
	}


	public function getModule() {
		$this->f = new FILE( $this->cache_dir . $this->blob_name_full );
		$this->cache_size = $this->f->GetSize();
		return $this->f->get();
	}


	public function writeModule( $template, $content_fragments ) {
		
		if( $this->parent->page_status['enable_caching'] === false )
			return false;
			
		if( $this->parent->page_status['status'] !== 200 )
			return false; 
			
		$blob_contents = $this->populateTemplate( $template, $content_fragments );

		// prep file object
		$this->f = new FILE( $this->cache_dir . $this->blob_name_full);
		
		// compress content
		$blob_contents = $this->compress( $blob_contents );

		if( $this->moduleExists() == false ) {
			return $this->createModule( $blob_contents );
		}

		return $this->updateModule( $blob_contents );
	}


	private function updateModule( $blob_contents ) {
		// write content to blob and save
		return $this->f->put( $blob_contents );
	}


	private function createModule( $blob_contents ) {
		// write content to blob and save
		return $this->f->create( $blob_contents );
	}


	public function compress( $output_str ) {
		return preg_replace( array("/\s\s+/", "/\n/"), array(" "," "), $output_str );
	}


	public function calcDiff( $arry ) {
		// return only the non matching array keys
		return array_diff_key( $arry, $this->protected_vars );
	}


	private function populateTemplate( $template, $content_fragments ) {
		// get cachable content fragments
		$public_fragments = $this->calcDiff( $content_fragments );

		return TEMPLATE::place( $template, $public_fragments );
	}



	/**
	*
	* following methods used for manipulating properties contained in file name
	*
	*
	*/

	public function getProperty( $type ) {

		if( !isset( $this->properties[$type] ) )
			return false;

		return $this->properties[$type];
	}


	public function explodeProperties( $str=null ) {

		//clear any existing data
		$this->properties = array();

		// get rid of any blanks
		$str = str_replace('|:|','|', $str);

		$arry = explode( '|', $str );

		//var_dump($arry);

		if( count( $arry ) == 0 )
			return false;

		foreach($arry as $item) {
			if($item == ':' || $item == '')
				continue;

			list($key, $data) = explode( ':', $item);
			$this->properties[$key] = $data;
		}

		return true;
	}


	public function addProperty( $type, $data ) {

		if( isset( $this->properties[$type] ) )
			return false;

		$this->properties[$type] = $data;

		return true;
	}


	public function implodeProperties( $arry ) {

		if( count( $arry ) <= 0 )
			return false;

		$str = null;

		foreach($arry as $key => $item ) {
			$str .= $key . ':' . $item . '|';
		}

		// clear blanks
		$str = str_replace('|:|','|', $str);

		return $str;
	}




}

?>
