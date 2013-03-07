<?php
class enrolmentsModel {
	public function getEnrolments() {
		$db = new database;
		$query = "SELECT concat(idnumber,idcourse) as id, operation, role, idnumber, idcourse ";
		$query .= "FROM epfc_enrol ";
		//$query .= "inner join inscriptions i on e.mat_etud = i.mat_etud ";
		$query .= "ORDER BY idnumber ASC ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadObjectList('id');
		return $rows;
	}
	/*
	 * updateMoodle
	 * we compare enrolments in db moodle and epfc
	 */
	public function updateMoodle($enrolmentsMoodle, $enrolmentsEpfc) {
		echo 'records in csv file '.count($enrolmentsEpfc).'<br />';
		
		$enrolmentsToAdd = array_diff_key($enrolmentsEpfc, $enrolmentsMoodle);
		if($enrolmentsToAdd){
			$this->enrolmentsToAdd($enrolmentsToAdd);
			echo '$enrolmentsToAdd '.count($enrolmentsToAdd).'<br />';
			//echo 'add <pre>' . print_r($usersToAdd, true) . '</pre>';
		}
		
		$enrolmentsToDelete = array_diff_key($enrolmentsMoodle, $enrolmentsEpfc);
		if($enrolmentsToDelete){
			//$this->enrolmentsToDelete($enrolmentsToDelete);
			echo '$enrolmentsToDelete '.count($enrolmentsToDelete).'<br />';
			//echo 'delete <pre>' . print_r($usersToDelete, true) . '</pre>';
		}
	}
	
	/*
	 * enrolmentToAdd
	 * insert record into epfc_enrol table
	 */
	protected function enrolmentsToAdd($rows){
		foreach ($rows as $row) {
			//echo 'row <pre>' . print_r($row, true) . '</pre>';
			//die();
			unset($row->id);   
			$line = null;
			foreach($row as $value){
				$line[] = $value;            
			}
			//echo '<pre>'.print_r($line,true).'</pre>';	
			//die();
			$string = '('.implode(', ', $line).')';
			$lines[] = $string;
		}          
		$values = implode(', ', $lines);
		//die(__LINE__.' enrolmentsToAdd'.'<br />');
		                        
		
		$db = new database;
		$fields = $db -> getHeader($rows);
		//echo 'row <pre>' . print_r($fields, true) . '</pre>';
		//delete id column
		//unset($fields[0]);
		//change header from csv to moodle table
		//$headerCsv = array("id", "modified");
		//$headerMoodle  = array("idnumber", "timemodified");
		//$fields = str_replace($headerCsv, $headerMoodle, $fields);
		
		//echo '<pre>'.print_r($fields,true).'</pre>';	
		$fields = implode(', ', $fields);
		$values = $db -> prepareInsertValues($rows);

		$query = "INSERT INTO epfc_enrol ";

		//$now = date('Y-m-d H:i:s');
		$query .= "(" . $fields . ") ";
		$query .= "VALUES " . $values . " ";

		$db -> sql = $query;
		//echo $query;
		//die();
		if (!$db -> query()) {
			echo $db -> errorMsg;
			return false;
		}
		echo 'enrolmentsToAdd ' . $db -> getAffectedRows() . '<br />';
		return $db -> getAffectedRows();
	}
}