<?php

class modelEnrolments {
	/*
	 * getTeachers
	 * adding and delete enrolments
	 * @return array / object
	 */
	public function getTeachers(){
		$db = new database;

		$query = "SELECT DISTINCT 'add' \"operation\", 'teacher' \"role\", weu.login \"username\", h.NO_CLASSE \"idcourse\" ";
		$query .= "FROM horaires h ";
		$query .= "INNER JOIN personne p on p.id_Pers = h.id_Pers ";
		$query .= "INNER JOIN w_ext_users weu on weu.id = p.id_extranet ";
      	$query .= "ORDER BY weu.login, h.no_classe asc ";
		//$query .= "ROWS 1 TO 5 ";
		echo $query;
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
			return false;
		}
		$rows = $db->loadAssocList();
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}
	/*
	 * getStudents
	 * getting usernama from the csv file
	 * TODO 2011004307 mat_etdu no course ?
	 */
	public function getStudents(){
		$db = new database;
		
		//$csv = new csv;
		//$users = $csv->read('users.csv');
		//echo '<pre>'.print_r($users,true).'</pre>';

		$query = "SELECT 'add' \"operation\", 'teacher' \"role\", e.username \"username\", i.NO_CLASSE \"idcourse\"";
		$query .= "FROM INSCRIPTIONS i ";
		$query .= "INNER JOIN etudiants e on e.mat_etud = i.mat_etud ";
		$query .= "WHERE i.CODE_MOTIF_TRANSF is Null AND e.username is not null ";
      	$query .= "ORDER BY e.username, i.NO_CLASSE ASC ";
		//$query .= "ROWS 1 TO 5 ";
		
		echo $query;
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
			return false;
		}
		$rows = $db->loadAssocList();
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}
}
?>