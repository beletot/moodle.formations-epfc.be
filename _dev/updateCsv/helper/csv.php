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
	public function createFile($rows, $filename, $putHeader = true){
		$delimiter = ';';
		$enclosure = '"';
		//open or create csv file
		$filename = 'csv'.DS. $filename.'.csv';
		$handle = fopen($filename, "w");
		
		//put header
		if($putHeader === true){
			$header = $this->getHeader($rows);
			fputcsv($handle, $header, $delimiter, $enclosure);
		}
		
		foreach($rows as $row){
			fputcsv($handle, $row, $delimiter, $enclosure);
		}
		return true;
	}
	protected function getHeader($rows){
		$line = array_pop($rows);
		foreach($line as $key=>$value){
			$header[] = $key;
		}
		//echo '<pre>'.print_r($header,true).'</pre>';	
		return $header;
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
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
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
