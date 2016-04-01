<?

/**
* MySQL BACKUP PHP SCRIPT
*
* php 5
*
*
* LICENSE: Use of this library is governed by the Creative Commons License Attribution 2.5.
* You can check it out at: http://creativecommons.org/licenses/by/2.5/legalcode
*
* - You may copy, distribute, and eat this code as you wish. You must give me credit for
* writing it. You may not misrepresent yourself as the author of this code.
* - You may not use this code in a commercial setting without prior consent from me.
* - If you make changes to this code, you must make the changes available under a license like
* this one.
*
* @category   DB Managment
* @author     R. Fritz Washabaugh <general@nucleusdevelopment.com>
* @copyright  2012 Nucleus Development - R. Fritz Washabaugh
* @license    http://creativecommons.org/licenses/by/2.5/legalcode CC 2.5
* @link       http://www.nucleusdevelopment.com.com/code/do/sql-backup
*
**/
/**
* MySQL BACKUP PHP SCRIPT
*
* @version    Release: 1.0
* @link        http://www.nucleusdevelopment.com.com/code/do/sql-backup
* @ModDate    2012-07-26
*
**/
/**
* MySQL BACKUP PHP CLASS
*
* Automatically backs up MySQL database to file and saves it.
* Don't use that shit php code  that is all over the internet.
* 50,000 sql inserts is not cool.
*
* This script output mimics what the actual MYSQL export output would be, with out
* calling any system() commands. Groups 499 inserts together in to a gang, making importing MUCH faster.
* Don't risk the security of your system just to backup your mysql database.
*
*/

// mySQL connection information
$SECURE_CONF['MYSQL_HOST'] = 					  'localhost';
$SECURE_CONF['MYSQL_USER'] = 					  '';
$SECURE_CONF['MYSQL_PASSWORD'] = 				'';
$SECURE_CONF['MYSQL_DATABASE'] = 				'';
$SECURE_CONF['MYSQL_PREFIX'] =           '';


error_reporting(2047);
ini_set("display_errors",1);// report all errors!

// Set localized time
date_default_timezone_set('America/New_York');

// connect to sql
sql_connect();
// makes sure database connection gets closed on script termination
register_shutdown_function('sql_disconnect');

/**
* run backup
*/
$bkup = new SQLBKUP($MYSQL_DATABASE);

// DONE
//   :]






/**
* Connects to mysql server
*/
function sql_connect() {
  global $SECURE_CONF;

  $SECURE_CONF['MYSQL_CONNECT'] = mysql_connect(
        $SECURE_CONF['MYSQL_HOST'],
        $SECURE_CONF['MYSQL_USER'],
        $SECURE_CONF['MYSQL_PASSWORD'] ) or startUpError('<p>Could not connect to MySQL database.</p>', 'Connect Error');

  mysql_select_db(
        $SECURE_CONF['MYSQL_DATABASE'],
        $SECURE_CONF['MYSQL_CONNECT'] ) or startUpError('<p>Could not select database: ' . mysql_error() . '</p>', 'Connect Error');

  return $SECURE_CONF['MYSQL_CONNECT'];
}

/**
* disconnects from SQL server
*/
function sql_disconnect() {
    @mysql_close();
}

/**
* executes an SQL query
*/
function sql_query($query) {
    global $MYSQL_CONNECT;
    $res = mysql_query($query, $MYSQL_CONNECT) or
    print("mySQL error with query $query: " .
    mysql_error() . '<p />' );
    return $res;
}

class SQLBKUP
{
    // vars
    private $sql_resource = null;
    private $field_data = array();
    public function __construct( $name, $tables = '*') {
        //get all of the tables
        if($tables == '*') {
            $tables = array();
            $result = sql_query('SHOW TABLES');
            // commit tables names to array
            while($row = mysql_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            // or if tables is an array move on
            $tables = is_array( $tables ) ? $tables : explode(',', $tables );
        }
        $out = null;
        //cycle through each table
        foreach( $tables as $table ) {
            $out .= $this->processTable( $table );
        }
        return $this->toFile( $name, $tables, $out );
    }

    private function processTable( $table ) {
        // get sql resource
        $resource = $this->sqlTableRequest( $table );
        // get all table data
        $field_count = $this->getTableFields( $resource );
        $cell_data = $this->getTableCellData( $resource, $field_count );
        // prep all statements
        $create_table = $this->createTable( $table );
        $insert_statement = $this->getInsertStatement( $table, $field_count );
        // table create statement
        $out = "\n" . trim($create_table) . ";\n\n";
        //init loop vars
        $count = 0;
        $out .= "--\n";
        $out .= "-- Dumping data for table `" . $table . "`\n";
        $out .= "--\n\n";
        // loop through data
        while( $count < count( $cell_data ) ) {
            // start count
            $iteration_count = $count + 498;
            // table insert statement
            $out .= $insert_statement;

            // only allow insert statement to be 499 row tall
             while ( $count < $iteration_count && $count < count( $cell_data ) ) {
                $row = "\n(";
                $row .= implode( ", " , $cell_data[ $count ] );
                $row = rtrim( $row, ", ");
                $row .= "),";
                $out .= $row;
                $count++;
            };
            $out = rtrim( $out, "," );
            $out .= ";\n";
        }

        $out .= "\n";
        $out .= "-- --------------------------------------------------------\n";
        return $out;
    }

    private function getTableFields($sql_res) {
        // sql call
        $field = mysql_num_fields( $sql_res );
        $this->field_data = array();
        // loop through fields
        for($count = 0; $count < $field; $count++) {

            // 2d array
            $this->field_data[$count] = array();
            // store name and attributes
            $this->field_data[$count]['name'] = mysql_field_name( $sql_res, $count );
            $this->field_data[$count]['type'] = mysql_field_type( $sql_res, $count );
            $this->field_data[$count]['length'] = mysql_field_len( $sql_res, $count );
            $this->field_data[$count]['flags'] = mysql_field_flags( $sql_res, $count );
        }

        return $field;
    }

    private function getTableCellData($sql_res, $field_count) {
        // call all data from table
        // init loop vars
        $data = array();
        $row_count = -1;
        // loop through rows
        while( false != ($row = mysql_fetch_row( $sql_res ) ) ) {
            // init 2d array
            $data[ ++$row_count ] = array();
            // loop through all data for each row, commit to array
            for ( $i = 0; $i < $field_count; $i++ ) {
                // sanitize
                $cell = addslashes( $row[$i] );
                  // ecape linebreaks
                  $cell = str_replace( "\n", "\\n", $cell );
                // if cell is empty insert place holder
                $data[ $row_count ][ $i ] = $this->field_data[$i]['type'] == 'int'
                  ? $cell
                  : "'". $cell ."'";
            }
        }
        return $data;
    }

    private function sqlTableRequest( $table ) {
        $this->sql_resource = sql_query('SELECT * FROM ' . $table );
        return $this->sql_resource;
    }

    private function createTable($table) {
        $create_table = mysql_fetch_row( sql_query( 'SHOW CREATE TABLE ' . $table  ) );
        $out = "--\n";
        $out .= "-- Table structure for table `".$table."`\n";
        $out .= "--\n\n";
        $out .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $create_table[1]);
        return $out;
    }
    private function getInsertStatement( $table_name ) {
        // init loop vars
        $data = null;
        $field_str = null;
        // loooooooooooop
        foreach($this->field_data as $label) {
            $field_str .= '`' . $label['name'] . '`, ';
        }
        // trim following ','
        $field_str = rtrim( $field_str, ', ' );
        // tada
        return 'INSERT INTO `' . $table_name . '` (' . $field_str . ') VALUES ';
    }

    private function toFile( $name, $tables, $data ) {
        $header = "--\n\n";
        $header .= "-- Database: $name \n";
        $header .= "--\n\n";
        $header .= "CREATE DATABASE $name DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\n";
        $header .= "--\n\n";
        $header .= "USE `$name`;\n\n";
        $header .= "-- --------------------------------------------------------\n\n";
        $data = $header . $data;
        //save file
        $file_op = fopen('./bkup/db-bkup-' . date("Ymd") . '-' .
        ( md5( implode( ',', $tables ) ) ) . '.sql', 'w+' );
        fwrite( $file_op, $data );
        return fclose( $file_op );
    }
}
?>
