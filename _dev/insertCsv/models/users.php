<?php
class usersModel {
	
	
	public function getUsers() {
		$db = new database;
		$query = "SELECT mat_etud, nom as lastname, prenom as firstname, sexe, date_naiss as birthdate, username ";
		$query .= "FROM students  ORDER BY `username` ASC  limit 0,10 ";
		//$query .= "inner join inscriptions i on e.mat_etud = i.mat_etud ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadAssocList();
				echo count($rows);
		//echo '<pre>'.print_r($rows,true).'</pre>';
		//die();
		foreach ($rows as $row) {
			$row['lastname'] = $this->cleanAccent($row['lastname']);
			$row['firstname'] = $this->cleanAccent($row['firstname']);
			$row['username'] = $this->createUsername($row);
			$this->updateUser($row);
			$users[] = $row;
		}
		
		return $users;
	}
	protected function updateUser($row){
		
		//echo '<pre>'.print_r($row,true).'</pre>';
		//die();
		$query = null;
		$db = new database;
		$query .= "UPDATE students SET ";
		$query .= "username = '".$row['username']."' ";
		$query .= "WHERE mat_etud = '".$row['mat_etud']."' ; ";
		
		//echo $query;
		//die();
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
			return false;
		}
		//echo $db->getAffectedRows();
		return true;
		//die(__LINE__.' model / users'.'<br />');
	}
	
	public function cleanAccent($source) {
        //$source = utf8_encode($source);
		$trans = array("\""=>"","."=>"","'"=>"","-"=>""," "=>"","À"=>"a", "Â"=>"a", "Ã"=>"a", "Ä"=>"a", "Å"=>"a", "à"=>"a", "á"=>"a", "â"=>"a", "ã"=>"a", "ä"=>"a", "å"=>"a", "Ò"=>"o", "Ó"=>"o", "Ô"=>"o", "Õ"=>"o", "Ö"=>"o", "Ø"=>"o", "ò"=>"o", "ó"=>"o", "ô"=>"o", "õ"=>"o", "ö"=>"o", "Ø"=>"o", "ø"=>"o", "È"=>"e", "É"=>"e", "Ê"=>"e", "Ë"=>"e", "è"=>"e", "é"=>"e", "ê"=>"e", "ë"=>"e", "Ç"=>"c", "ç"=>"c", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ì"=>"i", "í"=>"i", "î"=>"i", "ï"=>"i", "ù"=>"u", "ú"=>"u", "û"=>"u", "ü"=>"u", "Ü"=>"u","ý"=>"y", "ÿ"=>"y", "Ñ"=>"n", "ñ"=>"n");
        $cleanSource = strtr($source, $trans);
        return $cleanSource;
    }	
	
	public function createUsername($user){
		$birthdate = null;
		
		//echo $user['firstname'].' '.$user['lastname'].'<br />';
		$firstname = $this->cleanAccent($user['firstname']);
		$lastname = $this->cleanAccent($user['lastname']);
		
		$lastname = substr($lastname, 0 ,14);
	    $firstname = substr($firstname, 0 ,2);
		$username = strtolower($firstname.$lastname);
		
		//if($user['type'] == 'student'){
			$date = new DateTime($user['birthdate']);
			$birthdate = $date->format('dm');	
			$username = $birthdate.$username;
			$user['username'] = $username;
			
			//update database
			//$this->updateUser($user);
			return $username;	
		//}
	            
		//echo $birthdate.$firstname.$lastname.'<br />';
	    //return $username;
	}

	/*
	 * updateMoodle
	 * we compare users in moodle with epfc et do update, insert or delete
	 */
	public function updateMoodle($usersMoodle, $usersEpfc) {
		echo 'records in csv file '.count($usersEpfc).'<br />';
		
		//checking date;
		$usersToUpdate = array_intersect_key($usersMoodle, $usersEpfc);
		if($usersToUpdate){
			$this->usersToUpdate($usersToUpdate, $usersMoodle, $usersEpfc);
			echo '$usersToUpdate '.count($usersToUpdate).'<br />';
			//echo 'UPDATE <pre>' . print_r($usersToUpdate, true) . '</pre>';
		}
		
		$usersToAdd = array_diff_key($usersEpfc, $usersMoodle);
		if($usersToAdd){
			$this->usersToAdd($usersToAdd);
			echo '$usersToAdd '.count($usersToAdd).'<br />';
			//echo 'add <pre>' . print_r($usersToAdd, true) . '</pre>';
		}
		
		$usersToDelete = array_diff_key($usersMoodle, $usersEpfc);
		if($usersToDelete){
			$this->usersToDelete($usersToDelete);
			echo '$usersToDelete '.count($usersToDelete).'<br />';
			//echo 'delete <pre>' . print_r($usersToDelete, true) . '</pre>';
		}
	}
	/*
	 * usersToUpdate
	 * wich database must i update ?
	 * @return array
	 * TODO create csv file to epfc database
	 */
	protected function usersToUpdate($usersToUpdate, $usersMoodle, $usersEpfc){
		$db = new database;
		foreach ($usersToUpdate as $value) {
			//echo 'UPDATE <pre>' . print_r($usersEpfc, true) . '</pre>';
			$userEpfc = $usersEpfc[$value->username];
			$userMoodle = $usersMoodle[$value->username];
			
			//echo 'update <pre>' . print_r($userMoodle, true) . '</pre>';
			//echo $userEpfc->modified.' ** '.$userMoodle->modified;
			if(strtotime($userEpfc->modified) > strtotime($userMoodle->modified)){
				$updateDatabase[] = $userEpfc;
			}else if(strtotime($userMoodle->modified) > strtotime($userEpfc->modified)){
				//do nothing
			}
		}
		echo 'user updated '.count($updateDatabase).'<br />';
		if($updateDatabase){
			$this->updateDatabase($updateDatabase);
		}
	}
	/*
	 * updateDatabase
	 * TODO escape ?
	 */
	 //UPDATE `formatiormoodle`.`epfc_user` SET `created` = '',`modified` = '' 
	protected function updateDatabase($rows){
		$query = null;
		$db = new database;
		
		foreach ($rows as $row) {
			$query .= "UPDATE `epfc_user` SET ";
			$query .= "`lastname` = ".$db->quote($row->lastname).", ";
			$query .= "`firstname` = ".$db->quote($row->firstname).", ";
			$query .= "`email` = ".$db->quote($row->email).", ";
			$query .= "`modified` = ".$db->quote($row->modified)." ";
			$query .= "WHERE `username` = ".$db->quote($row->username)."; ";
		}
		$db -> sql = $query;
		if (!$db -> queryBatch()) {
			echo $db -> errorMsg;
			return false;
		}
		return $db->getAffectedRows();
	
	}
	/*
	 * usersToAdd
	 * insert record into epfc_user table
	 */
	protected function usersToAdd($rows){
		$db = new database;
		$fields = $db->getHeader($rows);
		$fields = implode(', ', $fields);
		$values = $db->prepareInsertValues($rows);
		
		$query = "INSERT INTO epfc_user ";
		
		//$now = date('Y-m-d H:i:s');
		$query .= "(".$fields.") ";
		$query .= "VALUES ".$values." ";
		
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
			return false;
		}
		echo 'usersToAdd '.$db->getAffectedRows().'<br />';
		return $db->getAffectedRows();
	}
	/*
	 * usersToDelete
	 * @return affected rows
	 * TODO checking that i have a return
	 */
	protected function usersToDelete($rows){
		$query = null;
		$db = new database;
		
		foreach ($rows as $row) {
			$query .= "DELETE FROM `epfc_user` WHERE `username` = ".$db->quote($row->username)."; ";
		}
		
		//die($query);
		$db -> sql = $query;
		if (!$db -> queryBatch()) {
			echo $db -> errorMsg;
			return false;
		}
		echo 'usersToDelete '.$db->getAffectedRows().'<br />';
		return $db->getAffectedRows();
	}

}
?>