<?php
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../../includes/PHPMailer/src/Exception.php';
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';

include_once '../../includes/config.php';
include_once '../../includes/function.php';
//Anti spam
if(isset($_POST['name']) && $_POST['name'] == '') {

//textarea qui nous interesse
    if(isset($_POST['inputadd_comment']) && isset($_POST['idreceiver']) && isset($_POST['idsender'])) {
        if(isset($_POST['note']) && $_POST['note'] == '') {
            $response['error'] = '<div class="erreur">Vous devez donner une note grâce aux étoiles. </div>';
            echo json_encode($response);
            exit();
        }

        if ($_POST['inputadd_comment'] != '') {

            $commentaire = nl2br(htmlspecialchars($_POST['inputadd_comment']));
            $id_sender = intval($_POST['idsender']); //(id_filleul)
            $id_receiver = intval($_POST['idreceiver']); //id_parrain
            $note = intval($_POST['note']);
            $id_marchand = intval($_POST['id_marchand']);

            
            $bdd = $pdo->prepare("SELECT id as idparrainage, id_filleul, id_parrain, statut_parrainage FROM execparrainages
						WHERE ((id_parrain = :id_parrain AND id_filleul = ".$_SESSION['membre_id'].") OR (id_filleul = :id_parrain AND id_parrain = ".$_SESSION['membre_id'].")) AND ((bonus IS NULL AND (statut_parrainage = 2 OR statut_parrainage = 3)) OR (bonus IS NOT NULL AND (statut_parrainage = 4 OR statut_parrainage = 5)))");
						$bdd->bindParam(':id_parrain', $id_receiver, PDO::PARAM_INT);
						$bdd->execute(); 
						$fetchall = $bdd->fetchAll();
						$nb = $bdd->rowcount();

            if($nb <= 0) {
                $response['error'] = '<div class="erreur">Impossible de laisser un commentaire. </div>';
                echo json_encode($response);
                exit();
            }
            

             //Si un avis a déja été posté par ce membre sur ce parrain !
            $sql = "SELECT COUNT(*) as nb FROM comments WHERE id_sender = :id_sender AND id_receiver = :id_receiver ";
            $prep = $pdo->prepare($sql);
            $prep->bindParam(":id_sender", $id_sender, PDO::PARAM_INT);
            $prep->bindParam(":id_receiver", $id_receiver, PDO::PARAM_INT);
            $prep->execute();
            $result = $prep->fetch();
            $nbcomment = $result['nb'];
            if($nbcomment > 0 ) {
                    $response['error'] = '<div class="erreur">Vous avez déja laissé un avis à ce parrain </div>';
                    echo json_encode($response);
                    exit();
            }

            //INSERTION DANS BDD
            $sql = "INSERT INTO comments (id_receiver, id_sender, date, commentaire, id_marchand, note) VALUES (:id_receiver, :id_sender, :date, :commentaire, :id_marchand, :note)";
            $prep = $pdo->prepare($sql);
            $prep->bindParam(":id_receiver", $id_receiver, PDO::PARAM_STR);
            $prep->bindParam(":id_sender", $id_sender,  PDO::PARAM_INT);
            $prep->bindValue(":date", time(),  PDO::PARAM_INT);
            $prep->bindParam(":commentaire", $commentaire,  PDO::PARAM_STR);
            $prep->bindParam(":id_marchand", $id_marchand,  PDO::PARAM_INT);
            $prep->bindParam(":note", $note,  PDO::PARAM_INT);
            $prep->execute();
            $GLOBALS['nb_req']++;
            $idc = $pdo->lastinsertid();
            /*
            // BADGES GESTION CHECK
            */
            checkHowManyEntry($id_receiver, 3, 'comments', 'id_receiver');
            checkHowManyEntry($id_receiver, 2, 'execparrainages', 'id_filleul', ' OR id_parrain = ');
            checkHowManyEntry($id_sender, 2, 'execparrainages', 'id_filleul', ' OR id_parrain = ');

            $sql = "SELECT membre_email, membre_utilisateur FROM user
            WHERE membre_id = $id_receiver 
            UNION ALL
            SELECT membre_email, membre_utilisateur FROM user
            WHERE membre_id = $id_sender";
            $prep = $pdo->prepare($sql);
            $prep->execute();
            $fetchemail = $prep->fetchAll();
            $mail = new PHPMailer(true);
    
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'authsmtp.securemail.pro';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'admin@promo-parrain.com';                     //SMTP username
    $mail->Password   = 'Kckk2k5k69.';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->CharSet = 'UTF-8';
    $mail->DKIM_domain = 'promo-parrain.com';
    $mail->DKIM_private = '../../includes/PHPMailer/DKIM/private.key';
    $mail->DKIM_selector = '1632130975.parrain';
    $mail->DKIM_passphrase = '1632130975';
    $mail->DKIM_identity = $mail->From;

    //Recipients
    $mail->setFrom('admin@promo-parrain.com', 'Promo-Parrain');
    $mail->addAddress($fetchemail[0]['membre_email']);     //Add a recipient
    $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');

    $sujet = ' Promo-parrain - Vous avez un nouvel avis sur votre profil';
    $message = '<!DOCTYPE html>';
    $message.= '<html lang="en">';
    $message.= '<head>';
    $message.= '<meta charset="UTF-8">';
    $message.= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    $message.= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $message.= '<title>Réponse à une conversation</title>';
    $message.= '</head>';
    $message.= '<body style="background-color:#EEE;padding: 0 20px 20px ;font-family:Arial, Times New Roman, Verdana, Courier;font-size:14px;">';
    $message.= '<table bgcolor="#F5F5F5" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" height="100px" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:10px 40px;text-transform:uppercase"><h1 style="color:#181B1F;font-size:24px;">Vous avez un nouvel avis de '.$fetchemail[1]['membre_utilisateur'].'</h1></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td style="padding:4px 40px;"><p style="color:#181B1F;font-size:14px;">Bonjour, <br /><br />Le membre '.$fetchemail[1]['membre_utilisateur'].' vous à laissé un avis sur votre profil. Pour consulter son avis et voir la note obtenue, cliquez sur ce lien :  </p><br />';
    $message.= '<a style="color:#701818;font-weight:bold;font-size:15px;" href="https://www.promo-parrain.com/profil/'.$id_receiver.'#'.$idc.'">Voir l\'avis de '.$fetchemail[1]['membre_utilisateur'].'</a></td></tr>';
    $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;">Ou copier collez le lien suivant dans votre navigateur internet :<br /><br /> https://www.promo-parrain.com/profil/'.$id_receiver.'#'.$idc.'</p></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"></td></tr>';
    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;"></p></td></tr>';
    $message.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;" height="80px" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
    $message.= '<tr><td bgcolor="#FFF"></td></tr>';
    $message.= '</table></table>';
    $message.= '</body>';
    $message.= '</html>';

            
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $sujet;
    $mail->Body    = $message;

    $mail->send();
            
            foreach($fetchall as $fetch) {
            // On actualise execparrainages pour mettre l'ID de l'avis
            $update = ($_SESSION['membre_id'] == $fetch['id_filleul'] ? 'id_avis_filleul' : 'id_avis_parrain');
            $query = $pdo->prepare('UPDATE execparrainages SET '.$update.' = :'.$update.' '.($update == 'id_avis_filleul' ? ', statut_parrainage = statut_parrainage+1' : '').'
            WHERE id = :id');
            $query->bindParam(':'.$update, $idc, PDO::PARAM_INT);
            $query->bindParam(':id', $fetch['idparrainage'], PDO::PARAM_INT);
            $GLOBALS['nb_req']++; 
            $query->execute();
            
        }
            //Récupération des informlations pour apparation de l'avis directement en JS. query nécessaire pour avatar ... 
            $query = $pdo->query('SELECT C.id, C.id_receiver, C.id_sender, C.commentaire, C.date, C.id_marchand, C.note, U.membre_id, U.membre_utilisateur, I.image, I.type FROM comments C
                                                    LEFT JOIN images I ON C.id_sender = I.id_membre AND I.type = "avatar"
                                                    LEFT JOIN user U ON C.id_sender = U.membre_id
                                                WHERE C.id = '.$idc);
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
}