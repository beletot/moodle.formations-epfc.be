<?php
class JController {
	//TODO merged courses
	public function getEnrolments (){
		$db = new database;
		$sql = '';
		$query = "SELECT 'add', 'student', MAT_ETUD, NO_CLASSE ";
		$query .= "FROM INSCRIPTIONS ";
		$query .= "WHERE CODE_MOTIF_TRANSF is Null ";
      	$query .= "ORDER BY NO_CLASSE ASC ";
		$query .= "ROWS 1 TO 10 ";
		$db->sql = $query;
		$db->query();
		$rows = $db->loadRowList();
		return $rows;
	}
	
	/*
	 * get students et teachers
	 * @return array
	 * TODO update user
	 * TODO The password must have at least 8 characters, at least 1 digit(s), at least 1 lower case letter(s), at least 1 upper case letter(s), at least 1 non-alphanumeric character(s) 
	 * 		table adding modified + trigger on email adress ?
	 * suspend user in moodle if not exist in external database
	 */
	public function getUsers (){
		$db = new database;
		$query = "SELECT '' \"username\", '' \"password\" ,e.prenom \"firstname\" , e.nom \"lastname\", e.email \"email\" , e.date_naiss \"birthdate\", e.date_created \"created\", e.date_modified \"modified\" ";
		$query .= "FROM etudiants e ";
		//$query .= "inner join inscriptions i on e.mat_etud = i.mat_etud ";
		//$query .= "WHERE i.code_motif_transf is Null ";
      	$query .= "ORDER BY e.nom, e.prenom ASC ";
		//$query .= "ROWS 1 TO 10 ";
		
		//echo $query;
		//die();
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
		}
		
		$rows = $db->loadAssocList();
		//echo '<pre>'.print_r($rows,true).'</pre>';
		//die();
		
		$model = new modelUsers;
		foreach ($rows as $row ) {
			$row['firstname'] = utf8_encode(strtolower($row['firstname']));
			$row['lastname'] = utf8_encode(strtolower($row['lastname']));
			
			$row['firstname'] = ucfirst($row['firstname']);
			$row['lastname'] = ucfirst($row['lastname']);
			
			$row['username'] = $model->createUsername($row);
			$row['password'] = $model->createPassword($row);
			
			unset ($row['birthdate']);
			
			$users[$row['username']] = $row;
		}
		//key sort
		ksort($users);
		//echo '<pre>'.print_r($users,true).'</pre>';
		return $users;
	}
	
	/*
	 * get courses
	 * @return array
	 */
	public function getCourses(){
		//timecreated
		//timemodified
		//format
		//idnumber ?
		//category
		$db = new database;
		$query = "SELECT c.no_classe \"idCourses\", c.id_uf, u.denom \"fullname\", u.denom_crt \"shortname\", 'summary' \"summary\" ";
		$query .= "FROM classes c ";
		$query .= "inner join uf u on u.id_uf = c.id_uf ";
		//$query .= "WHERE i.code_motif_transf is Null ";
      	//$query .= "ORDER BY e.nom, e.prenom ASC ";
		$query .= "ROWS 1 TO 10 ";
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
		}
		
		$rows = $db->loadAssocList();
		return $rows;
	}
}
?>