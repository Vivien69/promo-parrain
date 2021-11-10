#!/usr/bin/php
<?php
require_once 'function.php';
require_once 'config.php';

$time = time() - 86400;
//BDD annonces de parrainages à remettre à zéro
$sql = "DELETE FROM an_deleted WHERE date < $time";
$sql = $pdo->prepare($sql);
$sql->execute();
$sql->closeCursor();

?>