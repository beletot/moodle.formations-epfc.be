<?php
//loading moodle config
//require('../../config.php');
/*
 * TODO must came from epfc server
 * 
 */
define('_JEXEC',true);
define('DS', DIRECTORY_SEPARATOR);

require('config.php');
require('controller.php');
require('helper'.DS.'database.php');
require('helper'.DS.'csv.php');
require('models'.DS.'users.php');
require('models'.DS.'course.php');

$model = new usersModel;
$csv = new csv;
try {
	//getEnrolments		
	//$usersMoodle = $model->getUsers();
	//echo '<pre>' . print_r($usersMoodle, true) . '</pre>';
	//$usersEpfc = $csv->read('users.csv');
	//echo '<pre>' . print_r($usersEpfc, true) . '</pre>';
	//$model->updateMoodle($usersMoodle, $usersEpfc);
	
	$courses = $model->getCourses();
	
	
	
	
} catch (Exception $e) {
    echo 'Error : ',  $e->getMessage(), '<br />';
    //$error = $e->getMessage();
    echo $controller->error->comment;
    //echo '<pre>'.print_r($error,true).'</pre>';
}
?>