<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';

if (isset($_POST["idsender"]) && isset($_POST["idreceiver"]) && is_numeric($_POST["idreceiver"]) && isset($_POST["idannonce"]) && is_numeric($_POST["idannonce"]) && isset($_POST["idmarchand"]) && is_numeric($_POST["idmarchand"]) && isset($_POST["action"]) && is_numeric($_POST["action"])) {
   
    (is_numeric($_POST['idsender']) ? $registered_filleul = true : $registered_filleul = false);
    ($registered_filleul ? $id_filleul = trim(intval($_POST['idsender'])) : $id_filleul = strip_tags('Filleul'.substr($_POST['idsender'],0,15)));
    $id_parrain = trim(intval($_POST['idreceiver']));
    $id_annonce = trim(intval($_POST['idannonce']));
    $id_marchand = trim(intval($_POST['idmarchand']));
    $action = trim(intval($_POST['action']));

    //Impossible de s'envoyer un auto parrainage
    if($id_parrain === $id_filleul) 
    { 
        $reponse["etat"] = 'error';
        $reponse["type"] = 'autoparrainage';
        echo json_encode($reponse);
        die();
    }

    // On vérifie si le code de parrainage à déja été copié et qu'un parrainage est en cours pour ce marchand. 
    $sql = $pdo->prepare("SELECT COUNT(*) as nombre, id, date, statut_parrainage FROM execparrainages WHERE id_parrain = :id_parrain AND id_filleul = :id_filleul AND id_annonce = :id_annonce AND id_marchand = :id_marchand");
    $sql->bindParam(":id_parrain", $id_parrain, PDO::PARAM_INT);
    $sql->bindParam(":id_filleul", $id_filleul, PDO::PARAM_INT);
    $sql->bindParam(":id_annonce", $id_annonce, PDO::PARAM_INT);
    $sql->bindParam(":id_marchand", $id_marchand, PDO::PARAM_INT);
    $sql->execute();
    $nb = $sql->fetch();
    $time = time() - $nb['date'];


    //Au moin 1 copie de code détectée entre les 2 utilisateurs pour cette annonce ALORS on compte le délai (300sec = 5mn) et on STOP (die() ) tout car le parrainage est déja lancé !
    
    if($nb['nombre'] > 0) {
        if($time < 300) {
            $reponse["etat"] = 'delai';
            echo json_encode($reponse);
            die();
        } else {
            //SINON ON REACTUALISE LA DATE DE COPIAGE et on enleve la suppression POUR RELANCER LE PARRAINAGE et die();
            $sql = $pdo->prepare("UPDATE execparrainages SET date = :date, deleted_parrain = :deleted_parrain, deleted_filleul = :deleted_filleul, statut_parrainage = :statut_parrainage WHERE id = :id");
            $sql->bindParam(":id", $nb['id'], PDO::PARAM_INT);
            $sql->bindValue(":deleted_parrain", 0, PDO::PARAM_INT);
            $sql->bindValue(":deleted_filleul", 0, PDO::PARAM_INT);
            $sql->bindValue(":statut_parrainage", 0, PDO::PARAM_INT);
            $sql->bindValue(":date", time(), PDO::PARAM_INT);
            $sql->execute();
            $reponse["etat"] = 'ok';
            $reponse["type"] = 'reactualiseparrainage';
            echo json_encode($reponse);
            die();
        }
        
      
    }
    if($registered_filleul == false) {
        $sql = $pdo->prepare("INSERT INTO execparrainages SET id_parrain = :id_parrain, id_filleul = :id_filleul, id_marchand = :id_marchand, id_annonce = :id_annonce, date = :date, action = :action, notif=:notif");
        $sql->bindParam(":id_parrain", $id_parrain, PDO::PARAM_INT);
        $sql->bindParam(":id_filleul", $id_filleul, PDO::PARAM_STR);
        $sql->bindParam(":id_marchand", $id_marchand, PDO::PARAM_INT);
        $sql->bindParam(":id_annonce", $id_annonce, PDO::PARAM_INT);
        $sql->bindValue(":date", time(), PDO::PARAM_INT);
        $sql->bindParam(':action', $action, PDO::PARAM_INT);
        $sql->bindValue(':notif', 1, PDO::PARAM_INT);
        $sql->execute();
        $reponse["etat"] = 'oknotregistred';

        echo json_encode($reponse);
        $GLOBALS['nb_req']++;
        die();
    }
            // On vérifie si une conversation existe déja entre les 2 utilisateurs

            $sql = $pdo->prepare("SELECT id, COUNT(*) as nombre FROM conversations_users WHERE user_id = :user_id AND read_at = :read_at OR (user_id = :read_at AND read_at = :user_id)");
            $sql->bindParam(":user_id", $id_parrain, PDO::PARAM_INT);
            $sql->bindParam(":read_at", $id_filleul, PDO::PARAM_INT);
            $sql->execute();
            $fetch = $sql->fetch();

            //Si il n'y a pas de conversation, alors on en crée une.

            if($fetch['nombre'] == 0) {
                $prep1 = $pdo->prepare("INSERT INTO conversations_users (user_id, read_at, date_last)
                VALUES (:id1, :id2, :date_last)");
                $prep1->bindParam(':id1', $id_filleul, PDO::PARAM_INT);
                $prep1->bindParam(':id2', $id_parrain, PDO::PARAM_INT);
                $prep1->bindValue(':date_last', time(), PDO::PARAM_INT);
                $prep1->execute();
                $GLOBALS['nb_req']++;
                $id_conversation = $pdo->lastInsertId();
            } else {

                $id_conversation = $fetch['id'];

                $prep1 = $pdo->query("UPDATE conversations_users SET read_at_delete = 0, user_delete = 0
                WHERE id = $id_conversation");
                $prep1->execute();
                $GLOBALS['nb_req']++;

                
            }

            //On rajoute le nouveau parrainage dans la bdd.
            $sql = $pdo->prepare("SELECT bonus FROM annonces_parrainage WHERE idmembre = :idmembre AND idmarchand = :idmarchand");
            $sql->bindParam(":idmembre", $id_parrain, PDO::PARAM_INT);
            $sql->bindParam(":idmarchand", $id_marchand, PDO::PARAM_INT);
            $sql->execute();
            $GLOBALS['nb_req']++;
            $fetch = $sql->fetch();
            $bonus = $fetch['bonus'];

            $sql = $pdo->prepare("INSERT INTO execparrainages SET id_parrain = :id_parrain, id_filleul = :id_filleul, id_marchand = :id_marchand, id_annonce = :id_annonce, id_conversation = :id_conversation, date = :date, bonus = :bonus, action = :action");
            $sql->bindParam(":id_parrain", $id_parrain, PDO::PARAM_INT);
            $sql->bindParam(":id_filleul", $id_filleul, PDO::PARAM_INT);
            $sql->bindParam(":id_marchand", $id_marchand, PDO::PARAM_INT);
            $sql->bindParam(":id_annonce", $id_annonce, PDO::PARAM_INT);
            $sql->bindParam(":id_conversation", $id_conversation, PDO::PARAM_INT);
            $sql->bindValue(":date", time(), PDO::PARAM_INT);
            $sql->bindValue(":bonus", $bonus, PDO::PARAM_INT);
            $sql->bindParam(':action', $action, PDO::PARAM_INT);
 
            $sql->execute();
            $GLOBALS['nb_req']++;
            $id_parrainage = $pdo->lastInsertId();
            

        //On envoi le message signalant le début d'un parrainage entre les 2 utilisateurs :

        $url = ROOTPATH.'/membres/parrainages#';
        $message = '<a href="'.$url.$id_parrainage.'">Un nouveau parrainage a été lancé !</a>';

        $prep = $pdo->prepare("INSERT INTO messagerie (message, date, id1, id2, lu1, lu2, conversation_id, ip, info_message)
        VALUES (:message, :date, :id1, :id2, :lu1, :lu2, :conversation_id, :ip, :info_message)");
        $prep->bindValue(':message', $message, PDO::PARAM_STR);
        $prep->bindValue(':date', time(), PDO::PARAM_INT);
        $prep->bindParam(':id1', $id_filleul, PDO::PARAM_INT); // Si 0 c'est l'admin / robot qui envoi le message
        $prep->bindParam(':id2', $id_parrain, PDO::PARAM_INT);
        $prep->bindValue(':lu1', 0, PDO::PARAM_INT);
        $prep->bindValue(':lu2', 0, PDO::PARAM_INT);
        $prep->bindValue(':conversation_id', $id_conversation, PDO::PARAM_INT);
        $prep->bindValue(':ip', 0, PDO::PARAM_STR);
        $prep->bindValue(':info_message', 1, PDO::PARAM_INT);
        $prep->execute();
        $GLOBALS['nb_req']++;
        $reponse["etat"] = 'ok';
        $reponse["type"] = 'parrainagecree';
} else
    $reponse["etat"] = 'error';
    $reponse["type"] = 'manquedeschamps';

echo json_encode($reponse);