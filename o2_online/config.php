<?php
session_start();
$hostname = "localhost";
$user = "hlbf1825";
$password = "hdg@(qf-kCcC";
$nbd = "hlbf1825_reduc";
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fra', 'fr_FR');
ini_set("display_errors", 1);
try {
	$pdo = new PDO('mysql:host=' . $hostname . ';dbname=' . $nbd, $user , $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $th) {
    echo 'Échec lors de la connexion : ' . $th->getMessage();
}

define('TITRESITE', 'Promo-Parrain');
define('ROOTPATH', 'http://hlbf1825.odns.fr');

?>