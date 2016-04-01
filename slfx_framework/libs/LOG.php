<?php


class LOG
{

	//vars
	var $log_dir = 'logs/';
	var $delimiter = '|';

	// logs
	var $access_log = 'accesslog.log';
	var $action_log = 'actionlog.log';
	var $error_log = 'errorlog.log';
	var $system_log = 'systemlog.log';

	//constructor
	public function __construct() {}


	// (static)
	static function staticWriteBasic($type, $data) {
		$log = new LOG();
		$log->writeBasic($type, $data);
		return $log;
	}

	public function get($type = '') {

		$log_path = $this->getLogPath($type);

		// target log file
		$file = new FILE( $log_path );

		// get the file content
		$fileOP = $file->get();

		// rip file contents lines in to array
		return array_reverse(explode ("\n", $fileOP), true);

	}


	public function write($type = '', $data='') {

		$log_path = $this->getLogPath( $type );

		// target log file
		$file = new FILE( $log_path );

		// get current file content
		$file->get();

		// append log file
		$fileOP = $file->cat( $data . "\n" );

		// write to file
		return $file->put();

	}


	public function writeBasic($type, $data) {
		return $this->write( $type, getTimeStamp() . $this->delimiter . getIp(). $this->delimiter . getenv("HTTP_USER_AGENT") . $this->delimiter . $data );
	}


	private function getLogPath( $type ) {
		global $DIR_ROOT;

		switch( $type ) {
			case 'access':
				return $DIR_ROOT . $this->log_dir.$this->access_log;
				break;
			case 'error':
				return $DIR_ROOT . $this->log_dir.$this->error_log;
				break;
			case 'system':
				return $DIR_ROOT . $this->log_dir.$this->system_log;
				break;
			case 'action':
				return $DIR_ROOT . $this->log_dir.$this->action_log;
				break;
			default:
				return $DIR_ROOT . $this->log_dir.$this->error_log;
		}

	}


	public function outToTable( $file_lines ) {

		$table = null;
		foreach ($file_lines as $line ) {

			$table_line = null;
			$items = explode($this->delimiter, $line);

			foreach($items  as $item )
				$table_line .= $item . '</td><td>';

			$table .='<tr><td>' . $table_line . '</td></tr>';
		}

		unset($file_lines);

		$table = '<table border=0 cellpadding=0 cellspacing=0 class="log_table">'. $table .'</table>';
		return $table . $file->getSize() . $file->sizeUnit;

	}

}

?>