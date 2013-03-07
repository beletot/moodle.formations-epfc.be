<?php
/*
 * https://tracker.moodle.org/browse/MDL-13114
 */
//mdl_course
//TODO mdl_course_categories
class coursesModel {
	/*
	 * getCourses
	 * getting all courses from moodle
	 * TODO getting prefix from moodle config
	 */
	public function getCourses() {
		$db = new database;
		$query = "SELECT c.idnumber as id, c.fullname, c.shortname, c.summary, c.category, c.visible, c.timecreated as created, c.timemodified as modified ";
		$query .= "FROM mdl_course as c ";
		$query .= "WHERE c.idnumber > 0 ";
		$query .= "ORDER BY c.idnumber ASC ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadObjectList('id');
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}

	/*
	 * updateMoodle
	 * we compare courses in moodle do update, insert or delete
	 */
	public function updateMoodle($coursesMoodle, $coursesEpfc) {
		echo 'records in csv file ' . count($coursesEpfc) . '<br />';

		//checking date;
		$coursesToUpdate = array_intersect_key($coursesMoodle, $coursesEpfc);
		if ($coursesToUpdate) {
			$this -> coursesToUpdate($coursesToUpdate, $coursesMoodle, $coursesEpfc);
			echo '$coursesToUpdate ' . count($coursesToUpdate) . '<br />';
			//echo 'UPDATE <pre>' . print_r($coursesToUpdate, true) . '</pre>';
		}

		$coursesToAdd = array_diff_key($coursesEpfc, $coursesMoodle);
		if ($coursesToAdd) {
			$this -> coursesToAdd($coursesToAdd);
			echo '$coursesToAdd ' . count($coursesToAdd) . '<br />';
			//echo 'add <pre>' . print_r($coursesToAdd, true) . '</pre>';
		}

		$coursesToDelete = array_diff_key($coursesMoodle, $coursesEpfc);
		if ($coursesToDelete) {
			//$this -> coursesToDelete($coursesToDelete);
			echo '$coursesToDelete ' . count($coursesToDelete) . '<br />';
			//echo 'delete <pre>' . print_r($coursesToDelete, true) . '</pre>';
		}
	}

	/*
	 * coursesToUpdate
	 * wich database must i update ?
	 * @return array
	 * TODO create csv file to epfc database
	 */
	protected function coursesToUpdate($coursesToUpdate, $coursesMoodle, $coursesEpfc) {
		$db = new database;
		foreach ($coursesToUpdate as $value) {
			$courseEpfc = $coursesEpfc[$value -> id];
			$courseMoodle = $coursesMoodle[$value -> id];
			
			$courseEpfc->modified = strtotime($courseEpfc -> modified);

			//echo 'update <pre>' . print_r($userMoodle, true) . '</pre>';
			if ($courseEpfc -> modified > $courseMoodle -> modified) {
				//echo 'moodle need to be updated <br />';
				$updateDatabase[] = $courseEpfc;
			} else if ($courseMoodle -> modified > $courseEpfc -> modified) {
				//do nothing
			}
		}
		echo 'user updated ' . count($updateDatabase) . '<br />';
		if ($updateDatabase) {
			$this -> updateDatabase($updateDatabase);
		}
	}

	/*
	 * updateDatabase
	 * TODO escape ?
	 */
	//UPDATE `formatiormoodle`.`epfc_user` SET `created` = '',`modified` = ''
	protected function updateDatabase($rows) {
		$query = null;
		$db = new database;

		foreach ($rows as $row) {
			$query .= "UPDATE `mdl_course` SET ";
			$query .= "`fullname` = " . $db -> quote($row -> fullname) . ", ";
			$query .= "`shortname` = " . $db -> quote($row -> shortname) . ", ";
			$query .= "`category` = 1 , ";
			$query .= "`visible` = 1 , ";
			$query .= "`timemodified` = " . $db -> quote($row -> modified) . " ";
			$query .= "WHERE `idnumber` = " . $db -> quote($row -> id) . "; ";
		}
		$db -> sql = $query;
		if (!$db -> queryBatch()) {
			echo $db -> errorMsg;
			return false;
		}
		return $db -> getAffectedRows();

	}

	/*
	 * coursesToAdd
	 * insert record into courses table
	 * The course must be edit to complet all data
	 * TODO get header id must become idnumber
	 */
	protected function coursesToAdd($rows) {
		/*
		 * course_format_options
		 * course_modules
		 * course sections
		 */
		/*require_once('../../../config.php');
    	require_once("../../../course/lib.php");
    	require_once("$CFG->libdir/blocklib.php");
		 $required = array(  'fullname' => false, // Mandatory fields
                            'shortname' => false);*/                  
        
        foreach ($rows as $row) {
			$row->category = 1;         
			$line = null;
			foreach($row as $key => $value){        

				if($key == 'id' && $value == null){
					$value = date('Y-m-d H:i:s');
				}           
				
				//echo $value.'<br />';
				//$value = $this->quote($value);
				//echo $value.'<br />';
				$line[] = $value;           
			}
			//echo '<pre>'.print_r($line,true).'</pre>';	
			//die();
			$string = '('.implode(', ', $line).')';
			$lines[] = $string;
		}          
		$values = implode(', ', $lines);
		                        
		
		$db = new database;
		$fields = $db -> getHeader($rows);
		//change header from csv to moodle table
		$headerCsv = array("id", "modified");
		$headerMoodle  = array("idnumber", "timemodified");
		$fields = str_replace($headerCsv, $headerMoodle, $fields);
		
		//echo '<pre>'.print_r($fields,true).'</pre>';	
		$fields = implode(', ', $fields);
		$values = $db -> prepareInsertValues($rows);

		$query = "INSERT INTO mdl_course ";

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
		echo 'coursesToAdd ' . $db -> getAffectedRows() . '<br />';
		return $db -> getAffectedRows();
	}

	/*
	 * coursesToDelete
	 * @return affected rows
	 * TODO checking that i have a return
	 */
	protected function coursesToDelete($rows) {
		$query = null;
		$db = new database;

		foreach ($rows as $row) {
			$query .= "DELETE FROM `epfc_user` WHERE `username` = " . $db -> quote($row -> username) . "; ";
		}

		//die($query);
		$db -> sql = $query;
		if (!$db -> queryBatch()) {
			echo $db -> errorMsg;
			return false;
		}
		echo 'coursesToDelete ' . $db -> getAffectedRows() . '<br />';
		return $db -> getAffectedRows();
	}

}
?>