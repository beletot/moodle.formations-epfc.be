<?php
class curl {
	
	public $error_code = null;
	public $error_msg = null;
	
	public function get($url){
		$ch = curl_init();
	
	  	// set URL and other appropriate options
	  	curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// grab URL and pass it to the browser
	  	if( ! $result = curl_exec($ch)) {
	  		$this->error_code = 404;
	  		$this->error_msg = 'fail curl';
	  		throw new Exception();
	  	}
		
		// Check if any error occured
		if(curl_errno($ch))
		{
		    /*$subject = '[Get users] error';
			$message .= 'Curl error: ' . curl_error($ch);
			mail($this->email, $subject, $message);*/
			$this->error_code = 404;
	  		$this->error_msg = curl_error($ch);
	  		throw new Exception();
		}
		if($result){
			/*$message .= $result;
			//die();
			$subject = '[Get users] done';
			$message .= 'Curl okay: ';
			mail($this->email, $subject, $message);
			echo $message;*/
			echo $result;
		}else{
			/*$subject = '[Get users] error';
			$message .= 'Execute Cron';
			mail($this->email, $subject, $message);
			$this->error->state = 1;
	  		$this->error->comment = $message;*/
	  		throw new Exception(__LINE__.' error');
		}
	  	// close cURL resource, and free up system resources
	  	curl_close($ch);
	}
}
?>