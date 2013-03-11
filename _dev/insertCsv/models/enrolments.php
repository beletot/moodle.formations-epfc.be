<?php
class enrolmentsModel {
	//not use
	public function __getEnrolments() {
		$db = new database;
		$query = "SELECT operation, role, idnumber, idcourse ";
		$query .= "FROM epfc_enrol ";
		//$query .= "inner join inscriptions i on e.mat_etud = i.mat_etud ";
		$query .= "ORDER BY idnumber ASC ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadObjectList();
		return $rows;
	}

	/*
	 * updateMoodle
	 * we don't need to compare data, we just make a truncate on the table the push all recors to the table.
	 */
	public function updateMoodle() {
		$db = new database;
		$query = 'TRUNCATE TABLE epfc_enrol';
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}
		$query = "LOAD DATA INFILE '/home/epfc/sd/moodle/www/_dev/insertCsv/csv/enrolments.csv' 
			INTO TABLE epfc_enrol
			FIELDS TERMINATED BY ';' 
		";
		$db -> sql = $query;
		if (!$db -> query()) {
			throw new Exception('enrolmentsUpdateMoodle # '.$db -> errorMsg);
		}
	}
	
	//not use
	public function __updateMoodle($enrolmentsMoodle, $enrolmentsEpfc) {
		echo 'records in csv file ' . count($enrolmentsEpfc) . '<br />';

		$enrolmentsToAdd = array_diff_key($enrolmentsEpfc, $enrolmentsMoodle);
		if ($enrolmentsToAdd) {
			$this -> enrolmentsToAdd($enrolmentsToAdd);
			echo '$enrolmentsToAdd ' . count($enrolmentsToAdd) . '<br />';
			//echo 'add <pre>' . print_r($usersToAdd, true) . '</pre>';
		}

		$enrolmentsToDelete = array_diff_key($enrolmentsMoodle, $enrolmentsEpfc);
		if ($enrolmentsToDelete) {
			//$this->enrolmentsToDelete($enrolmentsToDelete);
			echo '$enrolmentsToDelete ' . count($enrolmentsToDelete) . '<br />';
			//echo 'delete <pre>' . print_r($usersToDelete, true) . '</pre>';
		}
	}

	/*
	 * enrolmentToAdd
	 * insert record into epfc_enrol table
	 */
	 //not use
	protected function enrolmentsToAdd($rows) {
		foreach ($rows as $row) {
			//echo 'row <pre>' . print_r($row, true) . '</pre>';
			//die();
			$line = null;
			foreach ($row as $value) {
				$line[] = $value;
			}
			//echo '<pre>'.print_r($line,true).'</pre>';
			//die();
			$string = '(' . implode(', ', $line) . ')';
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
