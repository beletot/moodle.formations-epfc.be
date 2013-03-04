<?php

class modelUsers {
	/*
	 * createUsername 
	 * adding birtday if the user is a student
	 * @return string
	 */
	public function createUsername($user){
		$birthdate = null;
		
		//echo $user['firstname'].' '.$user['lastname'].'<br />';
		$firstname = $this->cleanAccent($user['firstname']);
		$lastname = $this->cleanAccent($user['lastname']);
		
		$lastname = substr($lastname, 0 ,14);
	    $firstname = substr($firstname, 0 ,2);
		$username = strtolower($firstname.$lastname);
		
		if($user['type'] == 'student'){
			$date = new DateTime($user['birthdate']);
			$birthdate = $date->format('dm');	
			$username = $birthdate.$username;
			$user['username'] = $username;
			
			//update database
			$this->updateUser($user);
			return $username;	
		}
	            
		//echo $birthdate.$firstname.$lastname.'<br />';
	    return $username;
	}
	
	/*
	 * updateUser
	 * push username into etudiants table
	 * TODO getAffectedRows not returning
	 */
	protected function updateUser($row){
		
		//echo '<pre>'.print_r($row,true).'</pre>';
		//die();
		$query = null;
		$db = new database;
		$query .= "UPDATE ETUDIANTS SET ";
		$query .= "username = '".$row['username']."' ";
		$query .= "WHERE mat_etud = '".$row['id']."' ; ";
		
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
	
	/*
	 * createPassword
	 * @return string 8 characters
	 */
	public function createPassword($user){
		$password = md5($user['username']);
		$password = substr($password, 0 ,8);
		
		return $password;
	}
    public function cleanAccent($source) {
        //$source = utf8_encode($source);
		$trans = array("."=>"","'"=>"","-"=>""," "=>"","À"=>"a", "Â"=>"a", "Ã"=>"a", "Ä"=>"a", "Å"=>"a", "à"=>"a", "á"=>"a", "â"=>"a", "ã"=>"a", "ä"=>"a", "å"=>"a", "Ò"=>"o", "Ó"=>"o", "Ô"=>"o", "Õ"=>"o", "Ö"=>"o", "Ø"=>"o", "ò"=>"o", "ó"=>"o", "ô"=>"o", "õ"=>"o", "ö"=>"o", "Ø"=>"o", "ø"=>"o", "È"=>"e", "É"=>"e", "Ê"=>"e", "Ë"=>"e", "è"=>"e", "é"=>"e", "ê"=>"e", "ë"=>"e", "Ç"=>"c", "ç"=>"c", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ì"=>"i", "í"=>"i", "î"=>"i", "ï"=>"i", "ù"=>"u", "ú"=>"u", "û"=>"u", "ü"=>"u", "Ü"=>"u","ý"=>"y", "ÿ"=>"y", "Ñ"=>"n", "ñ"=>"n");
        $cleanSource = strtr($source, $trans);
        return $cleanSource;
    }	
	
}
?>