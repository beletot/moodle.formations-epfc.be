<?php

class modelEnrolments {
	/*
	 * getTeachers
	 * adding and delete enrolments
	 * @return array / object
	 */
	public function getTeachers(){
		$db = new database;

		$query = "SELECT DISTINCT 'add', 'teacher', weu.login \"username\", h.NO_CLASSE ";
		$query .= "FROM horaires h ";
		$query .= "INNER JOIN personne p on p.id_Pers = h.id_Pers ";
		$query .= "INNER JOIN w_ext_users weu on weu.id = p.id_extranet ";
      	$query .= "ORDER BY h.no_classe asc ";
		$query .= "ROWS 1 TO 10 ";
		echo $query;
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
			return false;
		}
		$rows = $db->loadRowList();
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}
	/*
	 * getStudents
	 * getting usernama from the csv file
	 */
	public function getStudents(){
		$db = new database;
		
		$csv = new csv;
		$users = $csv->read('users.csv');
		echo '<pre>'.print_r($users,true).'</pre>';
		die();

		$query = "SELECT 'add', 'student', '' \"username\", NO_CLASSE";
		$query .= "FROM INSCRIPTIONS ";
		$query .= "WHERE CODE_MOTIF_TRANSF is Null ";
      	$query .= "ORDER BY NO_CLASSE ASC ";
		$query .= "ROWS 1 TO 10 ";
		echo $query;
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
			return false;
		}
		$rows = $db->loadRowList();
		echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}
}
?>