#!/usr/bin/php
<?php
require_once 'function.php';
require_once 'config.php';
//BDD annonces de parrainages à remettre à zéro
$sql = "UPDATE annonces_parrainage SET actualisation = 0";
$sql = $pdo->prepare($sql);
$sql->execute();
$sql->closeCursor();

?>