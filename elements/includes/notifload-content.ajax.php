<?php
    require_once '../../includes/config.php';
    require_once '../../includes/function.php';

if(isset($_POST['json'])) {

    $json =  json_decode($_POST['json']);
    $idmembre = (int) $json->{'idmembre'};
    $limit =  (int) $json->{'limit'};
    $startn =  (int) $json->{'startn'};

    $sql = "SELECT * FROM notifications 
    WHERE idmembre =  :idmembre 
    ORDER BY date DESC
    LIMIT :startn,:limit";
    $sql = $pdo->prepare($sql);
    $sql->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
    $sql->bindParam(':startn', $startn, PDO::PARAM_INT);
    $sql->bindParam(':limit', $limit, PDO::PARAM_INT);
    $sql->execute();
    $fetch = $sql->fetchAll();

    $nb = count($fetch);
    for ($i=0; $i < $nb ; $i++) { 
        $fetch[$i]['date'] = mepd($fetch[$i]['date']);
    }
    echo json_encode($fetch);
}
/*
action 1 : Copie du code d'une annonce
action 2 : Copie du lien d'une annonce
action 3 : Nouveauté dans les parrainages idsender = id filleul ou parrain & annonce = lien
action 4 : Nouveau message
action 5 : Nouvel avis sur son profil
action 6 : Nouveau badge
action 7 : Refus points*/