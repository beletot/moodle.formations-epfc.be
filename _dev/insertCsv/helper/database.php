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
	protected $connection = '';

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
		global $CFG;
		echo $CFG->dbhost;
		die();
		$user 		= 'sysdba';
		$password 	= 'epfccfpe';
		$database 	= 'isis:c:\epfc1213.fdb';
		//$this -> _connection = @ibase_connect($database, $user, $password, 'UTF8');
		$this -> connection = @ibase_connect($database, $user, $password, 'UTF8');
		
		if (!$this -> connection) {
			$this -> errorMsg = "Impossible de se connecter : " . ibase_errmsg();
			echo $this -> errorMsg;
			exit;
		} 
	}
}
	
?>