<?php
/*
 * package csv
 */

class csv {
	/*
	 * create csv file
	 * @return true
	 * TODO checking error
	 */
	public function createFile($rows, $filename) {
		$delimiter = ';';
		$enclosure = '"';
		//open or create csv file
		$filename = 'csv' . DS . $filename . '.csv';
		$handle = fopen($filename, "w");
		foreach ($rows as $row) {
			fputcsv($handle, $row, $delimiter, $enclosure);
		}
		return true;
	}
	/*
	 * read
	 * return csv content
	 * @return array with object
	 */
	 //TODO CHECKING nb lines with the 1000 limitation
	public function read($path) {
		if (($handle = fopen('csv' . DS . $path, "r")) !== FALSE) {
			$header = true;
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				$countField = count($data);
				$fieldNames = array();
				if ($header ==  true) {
					for ($field = 0; $field < $countField; $field++) {
						$csvHeader[] = trim($data[$field]);
					}
					$header = false;
					//echo('<pre>'.print_r($csvHeader, true).'</pre>');

				} else {
					$i = 0;
					$line = new stdClass;
					foreach ($csvHeader as $key => $fieldName) {
						$content = utf8_decode($data[$i]);
						$line->$csvHeader[$key] = $content;
						$i++;
					}
					$lines[$data[0]] = $line;
				}

			}
			fclose($handle);
			
		}
		return $lines;
	}

}
?>
