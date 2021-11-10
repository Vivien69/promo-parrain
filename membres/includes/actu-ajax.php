<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';

if (isset($_POST["idannonce"]) && is_numeric($_POST["idmembre"])) {
    $id = intval($_POST["idannonce"]);
    $idmembre = intval($_POST["idmembre"]);
    $ladate = time();
    $sql = "SELECT * FROM annonces_parrainage WHERE id = " . $id . " AND idmembre=" . $idmembre;
    $prepa = $pdo->prepare($sql);
    $GLOBALS['nb_req']++;
    $prepa->execute();
    if ($prepa->rowcount() == 1) {
        $row = $prepa->fetch();
        $actu = $row['actualisation'];
        if ($actu < 2) {
            $actu++;
            $reponse["actu"] = $actu;
            $sql = $pdo->prepare("UPDATE annonces_parrainage SET dateajout = :ladate, actualisation = :actualisation WHERE id = :id AND idmembre= :idmembre");
            $sql->bindParam(":ladate", $ladate);
            $sql->bindParam(":actualisation", $actu);
            $sql->bindParam(":id", $id);
            $sql->bindParam(":idmembre", $idmembre);

            $GLOBALS['nb_req']++;
            if ($sql->execute()) {
                $reponse["etat"] = 'ok';
            }
        } else {
            $reponse["etat"] = 'limit';
        }
    } else {
        $reponse["etat"] = 'nofound';
    }
    echo json_encode($reponse);
}
