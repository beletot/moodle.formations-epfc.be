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
	
	protected $errorNum = '';
	protected $errorMsg = '';

	/*
	 * constructor
	 * @return  _connection
	 */
	public function __construct() {
		$config = new config;
		$user = $config->db_user;
		$password = $config->db_password;
		$database = $config->db_database;
		
		/*$user 		= 'sysdba';
		$password 	= 'epfccfpe';
		$database 	= 'isis:c:\epfc1213.fdb';*/
		
		//$this -> _connection = @ibase_connect($database, $user, $password, 'UTF8');
		$this->_connection = @ibase_connect($database, $user, $password, 'ISO8859_1');
		
		if (!$this->_connection) {
			$this -> errorMsg = "Impossible de se connecter : " . ibase_errmsg();
			echo $this -> errorMsg;
			exit;
		} 
	}
	
	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string  $text   The string to be escaped.
	 * @param   boolean $extra  not use with ibase
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.1
	 */
	public function escape($text, $extra = false)
	{
		//º working with utf8_decode	
		//Burešová Kristýna not working with utf8_decode	
		//return $text;	
		$text = str_replace("'", "''",$text);
		//$text = utf8_decode($text);
		return $text;	
	}
	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * TODO not working
	 */
	public function __getEscaped($text, $extra = false)
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
		return ibase_affected_rows($this->_connection);
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
	
	public function query (){
		return $this->execute();
	}
	
	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	 //TODO retun ibase error
	public function execute()
	{
	 		
		//TODO prepare query
			
	 	// Execute the query.
		$this->cursor = ibase_query($this->_connection, $this->sql);

		// If an error occurred handle it.
		if (!$this->cursor) {
			//echo 'error query <br />';
			//die(__LINE__.' pas de retour ibase');
			$this->errorNum = (int) ibase_errcode($this->_connection);
			$this->errorMsg = (string) ibase_errmsg($this->_connection).' SQL='.$sql;
		}
		return $this->cursor;	
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
	public function fetchArray($cursor = null)
	{
		return ibase_fetch_row($cursor ? $cursor : $this->cursor);
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
	protected function fetchAssoc($cursor = null)
	{
		return ibase_fetch_assoc($cursor ? $cursor : $this->cursor);
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
	public function fetchObject($cursor = null, $class = 'stdClass')
	{
			
		return ibase_fetch_object($cursor ? $cursor : $this->cursor);
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
	public function loadRowList($key = null)
	{
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this->execute()))
		{
			return null;
		}

		// Get all of the rows from the result set as arrays.
		while ($row = $this->fetchArray($cursor))
		{
			if ($key !== null)
			{
				$array[$row[$key]] = $row;
			}
			else
			{
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this->freeResult($cursor);

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
	public function loadObjectList($key = '', $class = 'stdClass')
	{
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this->execute()))
		{
			return null;
		}

		// Get all of the rows from the result set as objects of type $class.
		while ($row = $this->fetchObject($cursor, $class))
		{
			if ($key)
			{
				$array[$row->$key] = $row;
			}
			else
			{
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this->freeResult($cursor);

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
	public function loadAssocList($key = null, $column = null)
	{
		// Initialise variables.
		$array = array();

		// Execute the query and get the result set cursor.
		if (!($cursor = $this->execute()))
		{
			return null;
		}

		// Get all of the rows from the result set.
		while ($row = $this->fetchAssoc($cursor))
		{
			$value = ($column) ? (isset($row[$column]) ? $row[$column] : $row) : $row;
			if ($key)
			{
				$array[$row[$key]] = $value;
			}
			else
			{
				$array[] = $value;
			}
		}

		// Free up system resources and return.
		$this->freeResult($cursor);

		return $array;
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
	protected function freeResult($cursor = null)
	{
		ibase_free_result($cursor ? $cursor : $this->cursor);
	}

}
