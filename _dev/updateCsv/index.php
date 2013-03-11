<?php
/*
 * update moodle.epfc.eu
 * TODO utf8 encode ?
 * TODO db escape query ?
 * TODO delete limit record into sql
 * TODO checking index in firebird table
 * TODO récupérer les students et prof de l'année dernnière
 */
 echo '<div style="background-color:red;color:white;padding:5px;margin:50px;">using dev database / checking query limit</div>';
define('_JEXEC',true);
define('DS', DIRECTORY_SEPARATOR);
$return = null;

//require files
require('config.php');
require('controller.php');
require('helper'.DS.'database.php');
require('helper'.DS.'csv.php');
require('helper'.DS.'ftp.php');
require('helper'.DS.'curl.php');

require('model'.DS.'enrolments.php');
require('model'.DS.'users.php');

/*
 * create users , courses, enrolments
 */
$controller = new JController;
$csv = new csv;
$ftp =  new ftp;
try {
	
	//getUSers
	$users = $controller->getUsers();
	//echo '<pre>'.print_r($users,true).'</pre>';
	$csv->createFile($users, 'users');
	//echo '<pre>'.print_r($rows,true).'</pre>';
	$return[] = $ftp->store('csv','users.csv');
	
	//getCourses
	//http://docs.moodle.org/22/en/Bulk_course_upload
	$courses = $controller->getCourses();
	//echo 'courses '.count($courses).'<br />';
	//echo '<pre>'.print_r($courses,true).'</pre>';
	$csv->createFile($courses, 'courses');
	$return[] = $ftp->store('csv','courses.csv');
	
	//getEnrolments	
	//So slow -> check student	
	$enrolments = $controller->getEnrolments();
	//echo '<pre>'.print_r($enrolments,true).'</pre>';
	// i use mysql load data file / don't need header
	$csv->createFile($enrolments, 'enrolments', false);
	$return[] = $ftp->store('csv','enrolments.csv');
	
	//inserting data in moodle.epfc.eu database
	$curl = new curl;
	$curl->get('http://moodle.epfc.eu/_dev/insertCsv/index.php');
	
} catch (Exception $e) {
    echo 'Error : ',  $e->getMessage(), '<br />';
    //$error = $e->getMessage();
    //echo $controller->error->comment;
    //echo '<pre>'.print_r($error,true).'</pre>';
}
//echo $curl->error_msg;
//echo '<pre>'.print_r($return,true).'</pre>';
?>