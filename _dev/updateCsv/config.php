<?php
/*
 * need max execution time > 30
 */
//helper/ftp
class config {
	/*public $db_user = 'sysdba' ;
	public $db_password = 'epfccfpe';
	public $db_database = 'isis:c:\epfc1213.fdb';*/
	
	public $db_user = 'sysdba' ;
	public $db_password = 'epfccfpe';
	public $db_database = 'EPFC01DEV01:c:\EPFC1213Test.fdb';
	
	
	public $ftp_server = '5.39.89.162';
	public $ftp_user_name = 'moodleupdate';
	public $ftp_user_pass = 'M7dx6apnz';
	
	//courses - exclude
	//string must have quote
	public $enrolmentsSectionsExclude = array();
	// XPSS et co -> ;: expertise pas d'étudiants 
	public $enrolmentsUfExclude = array("'XPSS'","'XPSI'","'XPSU'");
	public $enrolmentsClassesExclude = array();
}
?>