<?php
session_start();
$hostname = "81.88.52.164";
$user = "uv49z9yx";
$password = "t8a,y-657?q:";
$nbd = "uv49z9yx_reduc";
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fra', 'fr_FR');
try {
	$pdo = new PDO('mysql:host=' . $hostname . ';dbname=' . $nbd, $user , $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $th) {
    echo 'Échec lors de la connexion : ' . $th->getMessage();
}

define('TITRESITE', 'Promo-Parrain');
define('ROOTPATH', 'https://www.promo-parrain.com');

?>