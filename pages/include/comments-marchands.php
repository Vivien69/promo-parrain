<?php
include_once '../includes/config.php';
include_once '../includes/function.php';
//Anti spam
if(isset($_POST['name']) && $_POST['name'] == '') {

//textarea qui nous interesse
    if(isset($_POST['inputadd_comment']) && isset($_POST['idsender']) && isset($_POST['id_marchand'])) {
        if(isset($_POST['note']) && $_POST['note'] == '') {
            $response['error'] = '<div class="erreur">Vous devez donner une note grâce aux étoiles. </div>';
            echo json_encode($response);
            exit();
        }
        if(isset($_POST['inputadd_comment']) && strlen($_POST['inputadd_comment']) < 250) {
            $response['error'] = '<div class="erreur">Votre commentaire doit faire 250 caractères minimum. Décrivez votre expérience sur le site marchand : le choix de produits, les produits commandés, les délais de livraison... </div>';
            echo json_encode($response);
            exit();
        }

        if ($_POST['inputadd_comment'] != '') {

            $commentaire = htmlspecialchars($_POST['inputadd_comment']);
            $id_sender = intval($_POST['idsender']); //(id_filleul)
            $note = intval($_POST['note']);
            $id_marchand = intval($_POST['id_marchand']);
            

             //Si un avis a déja été posté par ce membre sur ce marchand !
            $sql = "SELECT COUNT(*) as nb FROM comments_marchands WHERE id_sender = :id_sender AND id_marchand = :id_marchand ";
            $prep = $pdo->prepare($sql);
            $prep->bindParam(":id_sender", $id_sender, PDO::PARAM_INT);
            $prep->bindParam(":id_marchand", $id_marchand, PDO::PARAM_INT);
            $prep->execute();
            $result = $prep->fetch();
            $nbcomment = $result['nb'];
            if($nbcomment > 0 ) {
                    $response['error'] = '<div class="erreur">Vous avez déja laissé un avis à ce marchand</div>';
                    echo json_encode($response);
                    exit();
            }

            //INSERTION DANS BDD
            $sql = "INSERT INTO comments_marchands (id_sender, id_marchand, commentaire, date, note) VALUES (:id_sender, :id_marchand, :commentaire, :date,:note)";
            $prep = $pdo->prepare($sql);
            $prep->bindParam(":id_sender", $id_sender,  PDO::PARAM_INT);
            $prep->bindParam(":id_marchand", $id_marchand,  PDO::PARAM_INT);
            $prep->bindParam(":commentaire", $commentaire,  PDO::PARAM_STR);
            $prep->bindValue(":date", time(),  PDO::PARAM_INT);
            $prep->bindParam(":note", $note,  PDO::PARAM_INT);
            $prep->execute();
            $GLOBALS['nb_req']++;
            $idc = $pdo->lastinsertid();
            checkHowManyEntry($_SESSION['membre_id'], 4, 'comments_marchands', 'id_sender');
            //Récupération des informlations pour apparation de l'avis directement en JS. query nécessaire pour avatar ... 
            $query = $pdo->query('SELECT CM.id, CM.id_sender, CM.commentaire, CM.date, CM.note, CM.id_marchand, U.membre_id, U.membre_utilisateur, I.image, I.type FROM comments_marchands CM
                                                    LEFT JOIN images I ON CM.id_sender = I.id_membre
                                                    LEFT JOIN user U ON CM.id_sender = U.membre_id
                                                WHERE CM.id = '.$idc.' AND (I.type = "avatar" OR I.type IS NULL)');
                            $query->execute();
                            $GLOBALS['nb_req']++;
                            $result = $query->fetch();
                            $response['ok'] = "";
            $response['ok'] .= '<div id="'.$result['id'].'" class="viewcomment">
            <img style="align-items:flex-start;width:60px;height:60px;border-radius:50%;margin-right:10px;" src="'. ROOTPATH .'/membres/images/'. (isset($result['image']) && $result['image'] != '' ? $result['image'] : 'default_avatar.png') .'" />
            <div>
                <p style="margin-top:10px;margin-bottom:0;"><a style="font-weight:bold" href="'.ROOTPATH .'/profil/'.$result['id_sender'] .'">'. $result['membre_utilisateur'] .'</a> <span style="color:#999999;margin-left:15px;font-size:11px">'.strtolower(mepd($result['date'])) .'</span></p>
                <p style="margin-left:0px;font-size:15px;"><span style="font-size:12px;">Note attribuée : </span>';
                for($i=0;$i < 5; $i++) {
                    if($i < $result['note'])
                        $type = 'fas';
                    else
                        $type = 'far';
                        $response['ok'] .= "<i class='$type fa-star fa-1x' style='color:#701818;'></i>";
                }
                $response['ok'] .='</p>
            </div>
            <br /><br />
            <p>'.$result['commentaire'] .'</p>
        </div><hr>';
            echo json_encode($response);
           
        } else {
            $response['error'] = '<div class="erreur" style="grid-area: 2 / 2 / 1 / 3;">Vous avez oublié d\'écrire un commentaire <button type="button" class="vpb-close" onclick="closeit();"><i class="fas fa-times"></i></button></div>';
            echo json_encode($response);
    }
    }
} else {
    $response['error'] = '<div class="erreur" style="grid-area: 2 / 2 / 1 / 3;">Etes vous un bot ? <button type="button" class="vpb-close" onclick="closeit();"><i class="fas fa-times"></i></button></div>';
echo json_encode($response);
}