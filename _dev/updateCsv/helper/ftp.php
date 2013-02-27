<?php
defined('_JEXEC') or die('Restricted access');
//TODO create lib file on the dev server

/*
 * ftp class
 * !! ne peut pas se connecter sir ftp expert est déjà connecté
 */
// supprimer le fichier sur le ftp après un bug
class ftp {
	/*
	 * ftp_fput take a string
	 */
	function store($localFolder, $fileName) {
		$config = new config;
		$ftp_server = $config->ftp_server;
		$ftp_user_name = $config->ftp_user_name;
		$ftp_user_pass = $config->ftp_user_pass;
		//si le mot de passe est mauvais il ne donne pas d'erreur

		$return = new stdclass;
		$return -> error = 0;
		$return -> comment = '';

		//$fp = fopen('sql/'.$fileName, 'r');

		// Mise en place d'une connexion basique
		$conn_id = @ftp_connect($ftp_server);
		if (!$conn_id) {
			$return -> error = 1;
			$return -> comment = "Couldn't connect as $ftp_user_name\n";
			return $return;
		}

		// Identification avec un nom d'utilisateur et un mot de passe
		//$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

		if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
			$return -> error = 0;
			ftp_pasv($conn_id, true);
			ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 5);
		} else {
			$return -> error = 1;
			$return -> comment = "Couldn't connect as $ftp_user_name\n";
			return $return;
		}
		//die();
		//echo 'pre>'.print_r($return, true).'<pre />';
		//die(__LINE__.' - ');
		// Tente de charger le fichier $file
		// Ouverture de quelques fichiers pour lecture
		$fp = fopen($localFolder . DS . $fileName, 'r');
		//echo fread($fp, filesize($localFolder.DS.$fileName));
		//die();
		if (!$fp) {
			$return -> error = 1;
			$return -> comment = 'Erreur ouverture fichier sql';
			return $return;
		}

		if (ftp_fput($conn_id, $fileName, $fp, FTP_ASCII)) {
			//if (ftp_put($conn_id, $fileName, $localFolder.DS.$fileName, FTP_ASCII)) {
			//crash if (ftp_fput($conn_id, $fileName, $fp, FTP_BINARY)) {
			//ftp_put($connexionFTP, CHEMIN_REPERTOIRE_FTP.$nomFichier, $cheminFichier, FTP_BINARY);
			$return -> error = 0;
		} else {
			$return -> error = 1;
			$return -> comment = "There was a problem while uploading $fileName\n";
			ftp_close($conn_id);
			fclose($fp);
			return $return;
		}
		// Fermeture de la connexion et du pointeur de fichier
		ftp_close($conn_id);
		fclose($fp);
		$return -> comment = "Upload okay \n";
		return $return;
	}

}
?>