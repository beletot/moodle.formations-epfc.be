<?php
//loading moodle config
//require('../../config.php');
define('_JEXEC',true);
define('DS', DIRECTORY_SEPARATOR);

require('controller.php');
require('helper'.DS.'database.php');
require('helper'.DS.'csv.php');
require('models'.DS.'user.php');

$model = new userModel;
$csv = new csv;
try {
	//getEnrolments		
	$usersMoodle = $model->getUsers();
	//echo '<pre>' . print_r($usersMoodle, true) . '</pre>';
	$usersEpfc = $csv->read('users.csv');
	//echo '<pre>' . print_r($usersEpfc, true) . '</pre>';
	//die();
	$model->updateMoodle($usersMoodle, $usersEpfc);
	
	
	
	
} catch (Exception $e) {
    echo 'Error : ',  $e->getMessage(), '<br />';
    //$error = $e->getMessage();
    echo $controller->error->comment;
    //echo '<pre>'.print_r($error,true).'</pre>';
}
?>