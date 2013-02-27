<?php 
// initialisation de la session
$ch = curl_init();

// configuration des options
curl_setopt($ch, CURLOPT_URL, "http://www.example.com/");
curl_setopt($ch, CURLOPT_HEADER, 0);

// exécution de la session
curl_exec($ch);

// fermeture des ressources
curl_close($ch);
?>