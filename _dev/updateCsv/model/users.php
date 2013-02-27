<?php

class modelUsers {
	/*
	 * createUsername
	 * @return string
	 * TODO can't format date.birthday
	 */
	public function createUsername($user){
		$firstname = self::cleanAccent($user['firstname']);
		$name = self::cleanAccent($user['lastname']);
		$date = new DateTime($user['birthdate']);
		$birthdate = $date->format('dm');
	            
	    $name = substr($name, 0 ,14);
	    $firstname = substr($firstname, 0 ,2);
	    return strtolower($birthdate.$firstname.$name);	
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
    function cleanAccent($source) {
        $source = utf8_encode($source);
		$trans = array("\'"=>"","-"=>""," "=>"","À"=>"a", "Â"=>"a", "Ã"=>"a", "Ä"=>"a", "Å"=>"a", "à"=>"a", "á"=>"a", "â"=>"a", "ã"=>"a", "ä"=>"a", "å"=>"a", "Ò"=>"o", "Ó"=>"o", "Ô"=>"o", "Õ"=>"o", "Ö"=>"o", "Ø"=>"o", "ò"=>"o", "ó"=>"o", "ô"=>"o", "õ"=>"o", "ö"=>"o", "Ø"=>"o", "ø"=>"o", "È"=>"e", "É"=>"e", "Ê"=>"e", "Ë"=>"e", "è"=>"e", "é"=>"e", "ê"=>"e", "ë"=>"e", "Ç"=>"c", "ç"=>"c", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ê"=>"e", "ì"=>"i", "í"=>"i", "î"=>"i", "ï"=>"i", "ù"=>"u", "ú"=>"u", "û"=>"u", "ü"=>"u", "Ü"=>"u", "ÿ"=>"y", "Ñ"=>"n", "ñ"=>"n");
        $cleanSource = strtr($source, $trans);
        return $cleanSource;
    }	
	
}
?>