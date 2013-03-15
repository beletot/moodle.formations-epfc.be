<?php
class usersModel {
	public function getUsers() {
		$db = new database;
		$query = "SELECT username, password, lastname, firstname, email, created, modified ";
		$query .= "FROM epfc_user ";
		//$query .= "inner join inscriptions i on e.mat_etud = i.mat_etud ";
		$query .= "ORDER BY username ASC ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadObjectList('username');
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
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