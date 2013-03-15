<?php
//loading moodle config
//require('../../config.php');
/*
 * TODO must came from epfc server
 *
 */
//ini_set('error_reporting', E_ALL);
define('_JEXEC', true);
define('DS', DIRECTORY_SEPARATOR);

require ('config.php');
//require('controller.php');
require ('helper' . DS . 'database.php');
require ('helper' . DS . 'csv.php');
require ('models' . DS . 'users.php');
require ('models' . DS . 'courses.php');
require ('models' . DS . 'enrolments.php');

$usersModel = new usersModel;
$coursesModel = new coursesModel;
$enrolmentsModel = new enrolmentsModel;
$csv = new csv;

/* insert	*/
try {
	//push users / auth/db/cli/sync_users.php       
	$usersMoodle = $usersModel->getUsers();
	echo '<pre>' . print_r($usersMoodle, true) . '</pre>';
	//$usersEpfc = $csv->read('users.csv');
	//echo '<pre>' . print_r($usersEpfc, true) . '</pre>';
	//$usersModel->updateMoodle($usersMoodle, $usersEpfc);

	//courses
	//$coursesMoodle = $coursesModel->getCourses();
	//$coursesEpfc = $csv->read('courses.csv');
	//echo '<pre>' . print_r($coursesEpfc, true) . '</pre>';
	//$coursesModel->updateMoodle($coursesMoodle, $coursesEpfc);

	//getEnrolments - just pushing all records
	/*$enrolmentsMoodle = $enrolmentsModel -> getEnrolments();
	echo '<pre>' . print_r($enrolmentsMoodle, true) . '</pre>';
	$enrolmentsEpfc = $csv -> read('enrolments.csv');
	echo '<pre>' . print_r($enrolmentsEpfc, true) . '</pre>';*/
	//$enrolmentsModel -> updateMoodle();

	//http://docs.moodle.org/22/en/External_database_enrolment
	/*# 5 minutes past 4am
	 5 4 * * * /usr/bin/php -c /path/to/php.ini /path/to/moodle/enrol/database/cli/sync.php
	 * php -f  /home/epfc/sd/moodle/www/enrol/database/cli/sync.php
	 */

} catch (Exception $e) {
	echo 'Error : ', $e -> getMessage(), '<br />';
	//$error = $e->getMessage();
	echo $controller -> error -> comment;
	//echo '<pre>'.print_r($error,true).'</pre>';
}
?>