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
	 * TODO add excludeSection / exclude id_pers
	 * TODO adding id pers et student ?
	 * suspend user in moodle if not exist in external database
	 */
	public function getUsers (){
		$db = new database;
		//get student
		$query = "SELECT '' \"username\", '' \"password\" ,e.prenom \"firstname\" , e.nom \"lastname\", e.email \"email\" , e.date_naiss \"birthdate\", e.date_created \"created\", e.date_modified \"modified\" ";
		$query .= "FROM etudiants e ";
		$query .= "UNION  ";
		//getting teacher
		$query .= "SELECT DISTINCT weu.login \"username\", '' \"password\" ,p.prenom \"firstname\" , p.nom \"lastname\", p.email \"email\" , p.date_naiss \"birthdate\", p.date_created \"created\", p.date_modified \"modified\" ";
		$query .= "FROM horaires h ";
		$query .= "INNER JOIN personne p on h.id_Pers = p.id_Pers ";
		$query .= "INNER JOIN w_ext_users weu on weu.id = p.id_extranet ";
		$query .= "INNER JOIN classes c on h.no_classe = c.no_classe ";
		$query .= "WHERE c.id_Section not in (188, 189, 190) and p.id_Pers <> '1TESTT1' ";
      	//not working with union
      	//$query .= "ORDER BY nom, prenom ASC ";
		//$query .= "ROWS 1 TO 5 ";
		
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
			
			//teacher have user have already a username for the extranet 
			if(!$row['username']){$row['username'] = $model->createUsername($row);}
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
		/*
		 * SELECT c.no_classe "id",
       c.id_uf,
       u.denom "fullname",
       u.denom_crt "shortname",
       'summary' "summary",
       s.denom,
       s.SECTION_COURT,
       ds.nom_domaine,
       ds.groupe,
       rp.nom,
       rp.prenom
FROM classes c
     inner join uf u on u.id_uf = c.id_uf
     inner join sections s on s.id_section = c.id_section
     inner join domaine_section ds on ds.id_domaine = s.id_domaine_section
     right join resp_peda rp on rp.id_resp_peda = s.id_resp_peda
ROWS 1 TO 10
		 * 
		 * SELECT c.no_classe "id",
       u.denom "fullname",
       u.denom_crt "shortname",
FROM classes c
     inner join uf u on u.id_uf = c.id_uf
     inner join sections s on s.id_section = c.id_section
WHERE c.no_classe is not null AND s.id_section not in (188, 189, 190)
		 */
		 //TODO date de début et date de fin
		 //TODO section et uf alternative / adding if in the query
		$db = new database;
		$query = "SELECT c.no_classe \"id\", c.id_uf, u.denom \"fullname\", u.denom_crt \"shortname\", 'summary' \"summary\" ";
		$query .= "FROM classes c ";
		$query .= "inner join uf u on u.id_uf = c.id_uf ";
		//$query .= "WHERE i.code_motif_transf is Null ";
      	//$query .= "ORDER BY e.nom, e.prenom ASC ";
		$query .= "ROWS 1 TO 10 ";
		echo $query;
		$db->sql = $query;
		if(!$db->query()){
			echo $db->errorMsg;
		}
		
		$rows = $db->loadAssocList();
		return $rows;
	}
}
?>