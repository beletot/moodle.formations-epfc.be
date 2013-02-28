<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @subpackage  Database
 */

class database {
	/**
	 * The database link identifier.
	 *
	 * @var mixed
	 */
	protected $_connection = '';

	/**
	 * The query sql string
	 *
	 * @var string
	 **/
	public $sql = '';

	/**
	 * The last query cursor
	 * @var resource
	 */
	protected $cursor = null;
	
	/**
	 * The fields that are to be quote
	 *
	 * @var array
	 * @since	1.5
	 */
	protected $_quoted = null;
	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $_nameQuote = '`';

	public $errorNum = '';
	public $errorMsg = '';
	public $debug = true;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 * @since   11.1
	 */
	public function __construct() {
		// Get some basic values from the options.
		$options['host'] = 'mysql51-47.pro';
		$options['user'] = 'formatiormoodle';
		$options['password'] = 'V8zpnsxq';
		$options['database'] = 'formatiormoodle';
		$options['select'] = (isset($options['select'])) ? (bool)$options['select'] : true;
		$options['port'] = null;
		$options['socket'] = null;

		/*
		 * Unlike mysql_connect(), mysqli_connect() takes the port and socket as separate arguments. Therefore, we
		 * have to extract them from the host string.
		 */
		$tmp = substr(strstr($options['host'], ':'), 1);
		if (!empty($tmp)) {
			// Get the port number or socket name
			if (is_numeric($tmp)) {
				$options['port'] = $tmp;
			} else {
				$options['socket'] = $tmp;
			}

			// Extract the host name only
			$options['host'] = substr($options['host'], 0, strlen($options['host']) - (strlen($tmp) + 1));
		}

		// Make sure the MySQLi extension for PHP is installed and enabled.
		if (!function_exists('mysqli_connect')) {

		}

		$this -> _connection = @mysqli_connect($options['host'], $options['user'], $options['password'], null, $options['port'], $options['socket']);

		// Attempt to connect to the server.
		if (!$this -> _connection) {
			$this -> errorNum = 2;
			$this -> errorMsg = 'JLIB_DATABASE_ERROR_CONNECT_MYSQL';
			return;
		}

		// Set sql_mode to non_strict mode
		mysqli_query($this -> _connection, "SET @@SESSION.sql_mode = '';");

		// If auto-select is enabled select the given database.
		if ($options['select'] && !empty($options['database'])) {
			$this -> select($options['database']);
		}
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.1
	 */
	public function escape($text, $extra = false) {
		$result = mysqli_real_escape_string($this -> getConnection(), $text);

		if ($extra) {
			$result = addcslashes($result, '%_');
		}

		return $result;
	}
	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 */
	public function getEscaped($text, $extra = false)
	{
		$result = mysqli_real_escape_string($this->_connection, $text);
		if ($extra) {
			$result = addcslashes($result, '%_');
		}
		return $result;
	}
	/**
	 * Description
	 *
	 * @return	int	The number of affected rows in the previous operation
	 * @since	1.0.5
	 */
	public function getAffectedRows()
	{
		return mysqli_affected_rows($this->_connection);
	}

	/**
	 * Get a quoted database escaped string
	 *
	 * @param	string	A string
	 * @param	boolean	Default true to escape string, false to leave the string unchanged
	 * @return	string
	 */
	public function quote($text, $escaped = true)
	{
		return '\''.($escaped ? $this->getEscaped($text) : $text).'\'';
	}
	/**
	 * Quote an identifier name (field, table, etc).
	 *
	 * @param	string	$s	The identifier to quote.
	 *
	 * @return	string	The quoted identifier.
	 * @since	1.5
	 */
	public function nameQuote($s)
	{
		$q = $this->_nameQuote;

		if (strlen($q) == 1) {
			return $q.$s.$q;
		} else {
			return $q{0}.$s.$q{1};
		}
	}
	
	/**
	 * Get the connection
	 *
	 * Provides access to the underlying database connection. Useful for when
	 * you need to call a proprietary method such as postgresql's lo_* methods
	 *
	 * @return resource
	 */
	public function getConnection()
	{
		return $this->_connection;
	}
	/*
	 * getHeader
	 * get key of a $rows
	 * TODO checking if we have a row
	 * @return $header
	 */
	public function getHeader($rows){
		$line = array_pop($rows);
		foreach($line as $key=>$value){
			$header[] = $this->nameQuote($key);
		}
		//echo '<pre>'.print_r($header,true).'</pre>';	
		return $header;
	}
	/*
	 * prepareValues
	 * receive a array and format it to make an insert
	 * @return string
	 */
	public function prepareInsertValues ($rows){
		foreach ($rows as $row) {
			//echo '<pre>'.print_r($row,true).'</pre>';	
			$line = null;
			foreach($row as $key => $value){

				if($key == 'modified' && $value == null){
					//$value = date('Y-m-d H:i:s');
				}
				//echo $value.'<br />';
				//$value = $this->escape($value);
				$value = $this->quote($value);
				//echo $value.'<br />';
				$line[] = $value;
			}
			//echo '<pre>'.print_r($line,true).'</pre>';	
			//die();
			$string = '('.implode(', ', $line).')';
			//$string = implode(', ', $line);
			$lines[] = $string;
			
			
		}
		$values = implode(', ', $lines);
		return $values;
	}

	/**
	 * Test to see if the MySQL connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public static function test() {
		return (function_exists('mysqli_connect'));
	}

	public function query() {
		return $this -> execute();
	}
	/**
	 * Execute a batch query
	 *
	 * @return	mixed	A database resource if successful, FALSE if not.
	 */
	public function queryBatch($abort_on_error=true, $p_transaction_safe = false)
	{
		$sql = $this->sql;
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$sql = rtrim($sql, "; \t\r\n\0");
			$si = $this->getVersion();
			preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", $si, $m);
			if ($m[1] >= 4) {
				$sql = 'START TRANSACTION;' . $sql . '; COMMIT;';
			}
			else if ($m[2] >= 23 && $m[3] >= 19) {
				$sql = 'BEGIN WORK;' . $sql . '; COMMIT;';
			}
			else if ($m[2] >= 23 && $m[3] >= 17) {
				$sql = 'BEGIN;' . $sql . '; COMMIT;';
			}
		}
		$query_split = $this->splitSql($sql);
		$error = 0;

		foreach ($query_split as $command_line) {
			$command_line = trim($command_line);
			if ($command_line != '') {
				$this->_cursor = mysqli_query($this->_connection, $command_line);
				if ($this->_debug) {
					$this->_ticker++;
					$this->_log[] = $command_line;
				}

				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysqli_errno($this->_connection) . ' ';
					$this->_errorMsg .= mysqli_error($this->_connection)." SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function execute() {
		if (!is_object($this -> _connection)) {
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1

			if ($this -> debug) {
				echo 'JDatabaseMySQLi::query: ' . $this -> errorNum . ' - ' . $this -> errorMsg;
			}
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->sql;
		//$sql = $this->replacePrefix((string) $this->sql);
		if ($this -> limit > 0 || $this -> offset > 0) {
			$sql .= ' LIMIT ' . $this -> offset . ', ' . $this -> limit;
		}

		// If debugging is enabled then let's log the query.
		if ($this -> debug) {
			// Increment the query counter and add the query to the object queue.
			$this -> count++;
			$this -> log[] = $sql;

			//JLog::add($sql, JLog::DEBUG, 'databasequery');
		}

		// Reset the error values.
		$this -> errorNum = 0;
		$this -> errorMsg = '';

		// Execute the query.
		$this -> cursor = mysqli_query($this -> _connection, $sql);

		// If an error occurred handle it.
		if (!$this -> cursor) {
			$this -> errorNum = (int) mysqli_errno($this -> _connection);
			$this -> errorMsg = (string) mysqli_error($this -> _connection) . ' SQL=' . $sql;

			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1

			if ($this -> debug) {
				echo 'JDatabaseMySQLi::query: ' . $this -> errorNum . ' - ' . $this -> errorMsg;
			}
			return false;
		}

		return $this -> cursor;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function select($database) {
		if (!$database) {
			return false;
		}

		if (!mysqli_select_db($this -> _connection, $database)) {
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy) {
				$this -> errorNum = 3;
				$this -> errorMsg = 'JLIB_DATABASE_ERROR_DATABASE_CONNECT';
				return false;
			}
		}

		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setUTF() {
		mysqli_query($this -> _connection, "SET NAMES 'utf8'");
	}
	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @param   string  $sql  Input SQL string with which to split into individual queries.
	 *
	 * @return  array  The queries from the input string separated into an array.
	 *
	 * @since   11.1
	 */
	public static function splitSql($sql)
	{
		$start = 0;
		$open = false;
		$char = '';
		$end = strlen($sql);
		$queries = array();

		for ($i = 0; $i < $end; $i++)
		{
			$current = substr($sql, $i, 1);
			if (($current == '"' || $current == '\''))
			{
				$n = 2;

				while (substr($sql, $i - $n + 1, 1) == '\\' && $n < $i)
				{
					$n++;
				}

				if ($n % 2 == 0)
				{
					if ($open)
					{
						if ($current == $char)
						{
							$open = false;
							$char = '';
						}
					}
					else
					{
						$open = true;
						$char = $current;
					}
				}
			}

			if (($current == ';' && !$open) || $i == $end - 1)
			{
				$queries[] = substr($sql, $start, ($i - $start + 1));
				$start = $i + 1;
			}
		}

		return $queries;
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchArray($cursor = null) {
		return mysqli_fetch_row($cursor ? $cursor : $this -> cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchAssoc($cursor = null) {
		return mysqli_fetch_assoc($cursor ? $cursor : $this -> cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass') {
		return mysqli_fetch_object($cursor ? $cursor : $this -> cursor, $class);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function freeResult($cursor = null) {
		mysqli_free_result($cursor ? $cursor : $this -> cursor);
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an array.  The array
	 * of objects can optionally be keyed by a field offset, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string  $key  The name of a field on which to key the result array.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadRowList($key = null) {
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this -> execute())) {
			return null;
		}

		// Get all of the rows from the result set as arrays.
		while ($row = $this -> fetchArray($cursor)) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this -> freeResult($cursor);

		return $array;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an object.  The array
	 * of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string  $key    The name of a field on which to key the result array.
	 * @param   string  $class  The class name to use for the returned row objects.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadObjectList($key = '', $class = 'stdClass') {
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this -> execute())) {
			return null;
		}

		// Get all of the rows from the result set as objects of type $class.
		while ($row = $this -> fetchObject($cursor, $class)) {
			if ($key) {
				$array[$row -> $key] = $row;
			} else {
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this -> freeResult($cursor);

		return $array;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an associative array
	 * of ['field_name' => 'row_value'].  The array of rows can optionally be keyed by a field name, but defaults to
	 * a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string  $key     The name of a field on which to key the result array.
	 * @param   string  $column  An optional column name. Instead of the whole row, only this column value will be in
	 * the result array.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadAssocList($key = null, $column = null) {
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this -> execute())) {
			return null;
		}

		// Get all of the rows from the result set.
		while ($row = $this -> fetchAssoc($cursor)) {
			$value = ($column) ? (isset($row[$column]) ? $row[$column] : $row) : $row;
			if ($key) {
				$array[$row[$key]] = $value;
			} else {
				$array[] = $value;
			}
		}

		// Free up system resources and return.
		$this -> freeResult($cursor);

		return $array;
	}

}
?>