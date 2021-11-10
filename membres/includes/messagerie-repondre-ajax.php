
<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../../includes/PHPMailer/src/Exception.php';
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';

include_once '../../includes/function.php';
include_once '../../includes/config.php';

    $erreur = 0;
//Message

    if (isset($_POST['ca_dest'])) {
        $idmembredestinataire = trim(intval($_POST['ca_dest']));
    }
    if (isset($_POST['ca_idconvers'])) {
        $idconvers = trim(intval($_POST['ca_idconvers']));
    }
    if (isset($_POST['ca_idm'])) {
        $idmembreexpediteur = trim(intval($_POST['ca_idm']));
        }

		if (isset ($_POST['msgrep'])) {
			$message = trim(nl2br($_POST['msgrep']));
			$message_result = checkvide($message);
			 if ($message_result == 'ok') {
                 $msgrep = strip_tags(nl2br($message), '<strong><img><a><br>');
		}else  if ($message_result == 'empty') {
            $reponse['erreur'] = 'emptymessage';
            $msgrep = '';
            $erreur++;
        }
		}
if ($erreur == 0) {
                    // On vérifie si la conversation correspond
                    $prep = $pdo->prepare("SELECT COUNT(*) AS nb FROM conversations_users WHERE id = $idconvers AND (user_id = :id1 AND read_at = :id2) OR (read_at = :id1 AND user_id = :id2)");
                    $prep->execute(array("id1" => $idmembreexpediteur, "id2" => $idmembredestinataire));
                    $result = $prep->fetch();
                    $GLOBALS['nb_req']++;
                    if($result['nb'] == 1) {
                        $sql = "SELECT U.membre_utilisateur, U.membre_email, CU.user_id, CU.read_at FROM user U
                        LEFT JOIN conversations_users CU ON (CU.user_id = U.membre_id OR CU.read_at = U.membre_id)
                        WHERE U.membre_id = $idmembreexpediteur";
                        $prep = $pdo->prepare($sql);
                        $prep->execute();
                        $form = $prep->fetch();
                        if($idmembreexpediteur == $_SESSION['membre_id']) {
                            $lu1 = 0;
                            $lu2 = 1;
                        }
                            else {
                                $lu1 = 1;
                                $lu2 = 0;
                            }
                        
                        
                        // On insert le message dans la table messagerie après avoir déclaré l'ID de la conversation ($idconvers)
                        $prep = $pdo->prepare("INSERT INTO messagerie (message, date, id1, id2, lu1, lu2, conversation_id, ip)
                        VALUES (:message, :date, :id1, :id2, :lu1, :lu2, :conversation_id, :ip)");
                        $prep->bindValue(':message', $msgrep, PDO::PARAM_STR);
                        $prep->bindValue(':date', time(), PDO::PARAM_INT);
                        $prep->bindParam(':id1', $idmembreexpediteur, PDO::PARAM_INT);
                        $prep->bindParam(':id2', $idmembredestinataire, PDO::PARAM_INT);
                        $prep->bindValue(':lu1', $lu1, PDO::PARAM_INT);
                        $prep->bindValue(':lu2', $lu2, PDO::PARAM_INT);
                        $prep->bindValue(':conversation_id', $idconvers, PDO::PARAM_STR);
                        $prep->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                       
                        $pdo->query("UPDATE conversations_users SET date_last = ".time());
                         $GLOBALS['nb_req']++;
                       
                        if ($prep->execute()) {
                            $sql = "SELECT image FROM images WHERE id_membre = ".$idmembreexpediteur." AND type='avatar'";
                            $prep = $pdo->prepare($sql);
                            $prep->execute();
                            $GLOBALS['nb_req']++;
                            if($prep->rowcount() === 0)
                            $image = 'default_avatar.png';
                            else {
                                $resultat = $prep->fetch();
                                $image = $resultat['image'];
                            }
                        
                            $reponse['erreurs'] = 'no';
                            $reponse['info'] = [
                            "message" => $msgrep,
                            "date" => time(),
                            "pseudo_ecris" => $form['membre_utilisateur'],
                            "from_avat" => $image,
                            "convid" => $idconvers,
                        ];
                            $GLOBALS['nb_req']++;
                        }

                    } else {
                        $reponse['erreurs'] = 'notfound';
                    }
                     // On envoi un email de notification pour signaler la réception d'un nouveau message privé. 
                     $sql2 = $pdo->query("SELECT membre_email FROM user WHERE membre_id = $idmembredestinataire");
                     $form2 = $sql2->fetch();


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
    $mail->addAddress($form2['membre_email']);     //Add a recipient
    $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');

    $sujet = ' Promo-parrain - Réponse à une conversation';
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
    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:10px 40px;text-transform:uppercase"><h1 style="color:#181B1F;font-size:24px;">Réponse de '.$form['membre_utilisateur'].'</h1></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td style="padding:4px 40px;"><p style="color:#181B1F;font-size:14px;">Bonjour, <br /><br />Le membre '.$form['membre_utilisateur'].' vous à répondu sur une conversation en cours. Pour voir son message, cliquez sur ce lien :  </p><br />';
    $message.= '<a style="color:#701818;font-weight:bold;font-size:15px;" href="https://www.promo-parrain.com/membres/messagerie.html">Voir le message de '.$form['membre_utilisateur'].'</a></td></tr>';
    $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;">Ou copier collez le lien suivant dans votre navigateur internet :<br /><br /> https://www.promo-parrain.com/membres/messagerie.html</p></td></tr>';
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
                    

	}
	elseif ($erreur > 0) {
        if ($erreur == 1) {
			$reponse['erreurs'] = 'one'; }
    } 
    echo json_encode($reponse);
?>