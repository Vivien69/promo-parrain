<?php 

require_once '../../includes/config.php';
$payload = file_get_contents("php://input");
$object = json_decode($payload);

if(isset($object->changetat)) {
    $val = intval($object->changetat);
    $id = intval($object->id);


    ($val == 1 ? $etat = 'actif' : $etat = 'desactif');

    // ON DESACTIVE LE MARCHAND ET LES ANNONCES
    $sql = "UPDATE marchands SET actif = :actif WHERE id=$id";
    $sql2 = "UPDATE annonces_parrainage SET etatvalidation = 2 WHERE idmarchand = $id";

    $sql = $pdo->prepare($sql);
    $sql2 = $pdo->prepare($sql2);
    $sql->bindParam(':actif', $val, PDO::PARAM_INT);
    if($sql->execute() && $sql2->execute()) {
        $reponse['erreur'] = 'ok';
    }


    // ON ENVOI UNE NOTIFICATION A TOUS LES MEMBRES COMME QUOI UNE DE LEUR ANNONCE A ETE DESACTIVEE

   
        $sql="SELECT id, idmembre FROM annonces_parrainage WHERE idmarchand = ".$_GET['idm'];
        $sql = $pdo->prepare($sql);
        $sql->execute();
        $rows = $sql->fetchAll();
        echo print_r($rows);
        $values = '';
        foreach($rows as $rowit) {
            $values.= '('.$rowit['idmembre'].', 0, 4, $id, '.$rowit['id'].', '.time().').'($rowit == end($rowit) ? '' : ', ');
            
        }
        $sql = "INSERT INTO notifications (idmembre, idsender, action, annonce, date)
        VALUES $values";
        $sql = $pdo->prepare($sql);
        $sql->execute();
        



} else {
    $reponse['erreur'] = 'error';
}

echo json_encode($reponse);