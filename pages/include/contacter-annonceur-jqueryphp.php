
<?php
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../../includes/PHPMailer/src/Exception.php';
require '../../includes/PHPMailer/src/PHPMailer.php';
require '../../includes/PHPMailer/src/SMTP.php';

include_once '../../includes/function.php';

    $erreur = 0;
    if($_POST['ca_nom'] == '' && !isset($_POST['validerform'])) {

    if (isset($_POST['ca_pseudo'])) {
        $pseudo = trim($_POST['ca_pseudo']);
        $pseudo_result = checkvide($pseudo);
         if ($pseudo_result == 'ok') {
            $form['ca_pseudo'] = $pseudo;
        } else if ($pseudo_result == 'empty') {
            $reponse['info'] = 'empty';
            $erreur++;
        }
    }
    if (isset($_POST['ca_idm'])) {
        $idmembreexpediteur = trim(intval($_POST['ca_idm']));
        }
    if (isset($_POST['ca_dest'])) {
        $idmembredestinataire = trim(intval($_POST['ca_dest']));
    }
    if (isset($_POST['ca_marchand'])) {
        $ca_marchand = trim($_POST['ca_marchand']);
    }
// Message a envoyer :
		if (isset ($_POST['ca_message'])) {
			$message = trim(nl2br($_POST['ca_message']));
			$message_result = checkvide($message);
			 if ($message_result == 'ok') {
                 $form['ca_message'] = strip_tags(nl2br($message), '<strong><img><a>');
		}else  if ($message_result == 'empty') {
            $reponse['info'] = 'emptymessage';
            $form['ca_message'] = '';
            $erreur++;
        }
		}

    if ($erreur == 0) {

                    // On vérifie si une conversation existe déja entre les 2 utilisateurs et on récupère l'id de celle-ci si elle existe et on met à jour la date, et si elle a été supprimée on al réactive. 
                    $prep = $pdo->prepare("SELECT COUNT(*) as nb FROM conversations_users WHERE (user_id = :id1 AND read_at = :id2) OR (read_at = :id1 AND user_id = :id2)");
                    $prep->execute(array("id1" => $idmembreexpediteur, "id2" => $idmembredestinataire, "id1" => $idmembreexpediteur, "id2" => $idmembredestinataire));    
                    $result = $prep->fetch();
                    $GLOBALS['nb_req']++;
                    if($result['nb'] == 1) {
                        $prep = $pdo->prepare("SELECT id FROM conversations_users WHERE (user_id = :id1 AND read_at = :id2) OR (read_at = :id1 AND user_id = :id2)");
                        $prep->bindParam(':id1', $idmembreexpediteur, PDO::PARAM_INT);
                        $prep->bindParam(':id2', $idmembredestinataire, PDO::PARAM_INT);
                        $prep->execute();
                        $result = $prep->fetch();
                        $GLOBALS['nb_req']++;
                        $idconvers = $result['id'];
                        $pdo->query('UPDATE conversations_users SET date_last = '.time().', user_delete = 0, read_at_delete = 0');
                    } else {
                        
                        // Si il n'y pas de conversation alors on là crée en inserant une ligne dans la table conversations_users
                        $prep1 = $pdo->prepare("INSERT INTO conversations_users (user_id, read_at, date_last, marchand)
                        VALUES (:id1, :id2, :date_last, :marchand)");
                        $prep1->bindParam(':id1', $idmembreexpediteur, PDO::PARAM_INT);
                        $prep1->bindParam(':id2', $idmembredestinataire, PDO::PARAM_INT);
                        $prep1->bindValue(':date_last', time(), PDO::PARAM_INT);
                        $prep1->bindValue(':marchand', $ca_marchand, PDO::PARAM_INT);
                        $prep1->execute();
                        $GLOBALS['nb_req']++;
                        $idconvers = $pdo->lastInsertId();
                        
                    }
                    // On insert le message dans la table messagerie après avoir déclaré l'ID de la conversation ($idconvers)
                    $prep = $pdo->prepare("INSERT INTO messagerie (message, date, id1, id2, lu1, lu2, conversation_id, ip)
                    VALUES (:message, :date, :id1, :id2, :lu1, :lu2, :conversation_id, :ip)");
                    $prep->bindValue(':message', $message, PDO::PARAM_STR);
                    $prep->bindValue(':date', time(), PDO::PARAM_INT);
                    $prep->bindParam(':id1', $idmembreexpediteur, PDO::PARAM_INT);
                    $prep->bindParam(':id2', $idmembredestinataire, PDO::PARAM_INT);
                    $prep->bindValue(':lu1', 0, PDO::PARAM_INT);
                    $prep->bindValue(':lu2', 1, PDO::PARAM_INT);
                    $prep->bindValue(':conversation_id', $idconvers, PDO::PARAM_STR);
                    $prep->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);

                    //email destinataire
                        $sql = $pdo->prepare("SELECT membre_email FROM user where membre_id = $idmembredestinataire");
                        $sql->execute();
                        $result = $sql->fetch(PDO::FETCH_ASSOC);
                        $emaildesti = $result['membre_email'];

                    if ($prep->execute()) {
                        $reponse['erreurs'] = 'no';
                        $GLOBALS['nb_req']++;

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
                        $mail->addAddress($emaildesti);     //Add a recipient
                        $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');
                    
                        $sujet = 'Nouveau message privé';
                        $mailtosend = '<table bgcolor="#F2F2F2" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:120px" width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
                        $mailtosend.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Nouveau message privé de '.$form['ca_pseudo'].'</h2></td></tr>';
                        $mailtosend.= '<tr bgcolor="#FFF"><td style="padding:4px 20px;"><p style="color:#181B1F;">Vous venez de recevoir un message privé sur Promo-parrain.com de la part de <b>'.$form['ca_pseudo'].'</b></p><br /></td></tr>';
                        $mailtosend.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;">Pour consulter ce message, rendez-vous dans votre espace personnel, dans la rubrique "Messagerie". <br /></td></tr>';
                        $mailtosend.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;"><p style="color:#181B1F;">Ou suivez le lien suivant : <br /> https://www.promo-parrain.com/membres/messagerie.html</p><br /><br /></td></tr>';
                        $mailtosend.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;height:100px" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
                        $mailtosend.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
                        $mailtosend.= '</table></table>';
                        $headers = "From: Promo-Parrain.com <admin@promo-parrain.com>\r\n".
                                    "Reply-To: Promo-Parrain.com <no-reply@promo-parrain.com>\r\n".
                                    "MIME-Version: 1.0" . "\r\n" .
                                    "Content-type: text/html; charset=UTF-8" . "\r\n";
                    
                    
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = $sujet;
                        $mail->Body    = $mailtosend;
                    
                        if($mail->send())
                        $reponse['erreurs'] = 'no';
                    }
	}
	elseif ($erreur > 0) {
        if ($erreur == 1) {
			$reponse['erreurs'] = 'one'; }
		else {
            $reponse['erreurs'] = 'plusieurs';
        }
    }
} else {
    $reponse['fvalidation'] = "yes";
}
    echo json_encode($reponse);
?>