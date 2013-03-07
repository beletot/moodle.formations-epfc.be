<?php
/*
 * https://tracker.moodle.org/browse/MDL-13114
 */
 //mdl_course
 //TODO mdl_course_categories
 class coursesModel{
 	/*
	 * getCourses
	 * getting all courses from moodle
	 * TODO getting prefix from moodle config
	 */
 	public function getCourses() {
		$db = new database;
		$query = "SELECT c.idnumber as id, c.fullname, c.shortname, c.summary, c.category, c.visible, c.timecreated, c.timemodified ";
		$query .= "FROM mdl_course ";
		$query .= "ORDER BY c.idnumber ASC ";
		$db -> sql = $query;
		if (!$db -> query()) {
			echo $db -> errorMsg;
		}

		$rows = $db -> loadObjectList('idnumber');
		echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}
 }
?>