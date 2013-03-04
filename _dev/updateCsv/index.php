<?php
/*
 * update moodle.epfc.eu
 * TODO utf8 encode ?
 * TODO db escape query ?
 * TODO delete limit record into sql
 */
 echo 'using dev database <br />';
define('_JEXEC',true);
define('DS', DIRECTORY_SEPARATOR);
$return = null;

//require files
require('config.php');
require('controller.php');
require ('helper'.DS.'database.php');
require ('helper'.DS.'csv.php');
require ('helper'.DS.'ftp.php');

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
	echo '<pre>'.print_r($users,true).'</pre>';
	//$csv->createFile($users, 'users');
	//echo '<pre>'.print_r($rows,true).'</pre>';
	
	//getEnrolments		
	//$enrolments = $controller->getEnrolments();
	//echo '<pre>'.print_r($enrolments,true).'</pre>';
	//$csv->createFile($enrolments, 'enrolments');
	// store know on the extranet/calendar/ ftp folder
	//$return = $ftp->store('csv','enrolments.csv');
	
	//getCourses
	//http://docs.moodle.org/22/en/Bulk_course_upload
	/*$courses = $controller->getCourses();
	echo 'courses '.count($courses).'<br />';
	echo '<pre>'.print_r($courses,true).'</pre>';
	$csv->createFile($courses, 'courses');*/
	
	//$controller->ftpPush('directory.csv');
} catch (Exception $e) {
    echo 'Error : ',  $e->getMessage(), '<br />';
    //$error = $e->getMessage();
    echo $controller->error->comment;
    //echo '<pre>'.print_r($error,true).'</pre>';
}

echo '<pre>'.print_r($return,true).'</pre>';
?>