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
	public function createFile($rows, $filename){
		$delimiter = ';';
		$enclosure = '"';
		//open or create csv file
		$filename = 'csv'.DS. $filename.'.csv';
		$handle = fopen($filename, "w");
		
		//put header
		$header = $this->getHeader($rows);
		fputcsv($handle, $header, $delimiter, $enclosure);
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
}
?>
