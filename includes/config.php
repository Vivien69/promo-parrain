<?php
session_start();
$hostname = "localhost";
$user = "root";
$password = "";
$nbd = "reduc";
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fra', 'fr_FR');
try {
	$pdo = new PDO('mysql:host=' . $hostname . ';dbname=' . $nbd, $user , $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Throwable $th) {
    echo 'Échec lors de la connexion : ' . $th->getMessage();
}

define('TITRESITE', 'Promo-Parrain');
define('ROOTPATH', 'http://'.$_SERVER['HTTP_HOST']);
define('GOOGLEID', '565427318129-tamce087db0fk0ua66i6433rpcr8r58r.apps.googleusercontent.com');
define('GOOGLESECRET', 'VQqdmSJiXjc8nHESsfPEEiHu');

?>