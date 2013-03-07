<?php
class JController {
	/*
	 * getEnrolments
	 * TODO respPeda + secretary -> create group acces ?
	 */
	public function getEnrolments (){
		$model = new modelEnrolments;
		
		$teachers = $model->getTeachers();
		$students = $model->getStudents();
		$enrolments = array_merge($teachers, $students);
		//die(__LINE__.' model/getEnrolments'.'<br />');
		
		return $enrolments;
	}
	
	/*
	 * get students et teachers
	 * @return array
	 * TODO update user
	 * TODO The password must have at least 8 characters, at least 1 digit(s), at least 1 lower case letter(s), at least 1 upper case letter(s), at least 1 non-alphanumeric character(s) 
	 * 		table adding modified + trigger on email adress ?
	 * TODO add excludeSection / exclude id_pers
	 * TODO adding id pers et student ?
	 * TODO delete user Test in the table
	 * suspend user in moodle if not exist in external database
	 */
	public function getUsers (){
		$db = new database;
		//get student
		$query = "SELECT mat_etud \"id\", e.username \"username\", '' \"password\" ,e.prenom \"firstname\" , e.nom \"lastname\", e.email \"email\" , e.date_naiss \"birthdate\", e.date_created \"created\", e.date_modified \"modified\", 'student' \"type\" ";
		$query .= "FROM etudiants e ";
		$query .= "UNION  ";
		//getting teacher
		$query .= "SELECT DISTINCT p.id_extranet \"id\", weu.login \"username\", '' \"password\" ,p.prenom \"firstname\" , p.nom \"lastname\", p.email \"email\" , p.date_naiss \"birthdate\", p.date_created \"created\", p.date_modified \"modified\", 'teacher' \"type\" ";
		$query .= "FROM horaires h ";
		$query .= "INNER JOIN personne p on p.id_Pers = h.id_Pers ";
		$query .= "INNER JOIN w_ext_users weu on weu.id = p.id_extranet ";
		$query .= "INNER JOIN classes c on h.no_classe = c.no_classe ";
		$query .= "WHERE c.id_Section not in (188, 189, 190) and p.id_Pers <> '1TESTT1' ";
      	//not working with union
      	//$query .= "ORDER BY nom, prenom ASC ";
		//$query .= "ROWS 1 TO 10000 ";
		
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
			
			//user have not username
			if(!$row['username']){$row['username'] = $model->createUsername($row);}
			$row['password'] = $model->createPassword($row);
			
			unset ($row['id']);
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
		 //TODO ucfirst
		 //TODO summary ?
		$db = new database;
		$where = null;
		$config = new config;
		if(count($config->enrolmentsUfExclude) >= 1){$where[] = ' u.id_uf NOT IN ('.implode(', ', $config->enrolmentsUfExclude).') ';}
		if(count($config->enrolmentsSectionsExclude) >= 1){$where[] = ' s.id_section NOT IN ('.implode(', ', $config->enrolmentsSectionsExclude).') ';}
		if(count($config->enrolmentsClassesExclude) >= 1){$where[] = ' c.no_classe NOT IN ('.implode(', ', $config->enrolmentsClassesExclude).') ';}
		
		
		$query = "SELECT c.no_classe \"id\", '1' \"category\", lower(c.no_classe||' - '||u.denom) \"fullname\", lower(u.denom_crt) \"shortname\", 'summary' \"summary\", '0' \"visible\", c.date_modification \"modified\" ";
		$query .= "FROM classes c ";
		$query .= "inner join uf u on u.id_uf = c.id_uf ";
		$query .= "inner join sections s on s.id_section = c.id_section ";
		if($where){
			$query .= "WHERE ".implode(' AND ',$where)." ";
		}
		
      	//$query .= "ORDER BY e.nom, e.prenom ASC ";
		//$query .= "ROWS 1 TO 3 ";
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