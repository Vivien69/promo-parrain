<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
?>
<meta name="description" content="Récupérer votre mot de passe perdu sur promo-parrain.com">
<meta name="keywords" content="mot, passe, perdu, promo, parrain">
<link rel="canonical" href="https://www.promo-parrain.com/passe_perdu" />
<meta name="robots" content="noodp,noydir" />
<?php
$title = 'Testmail';
require_once '../elements/header2.php';

$_SESSION['erreurs'] = 0;
 

$mail = new PHPMailer(true);

try{
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
    $mail->DKIM_private = '../includes/PHPMailer/DKIM/private.key';
    $mail->DKIM_selector = '1632130975.parrain';
    $mail->DKIM_passphrase = '1632130975';
    $mail->DKIM_identity = $mail->From;

    //Recipients
    $mail->setFrom('admin@promo-parrain.com', 'Promo-Parrain');
    $mail->addAddress('proviv@gmail.com');     //Add a recipient
    $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');

                    $sujet = ' Promo-parrain - Réponse à une conversation';
                    $message = '<!DOCTYPE html>';
    $message.= '<html lang="en">';
    $message.= '<head>';
    $message.= '<meta charset="UTF-8">';
    $message.= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    $message.= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $message.= '<title>Mot de passe généré</title>';
    $message.= '</head>';
    $message.= '<body style="background-color:#EEE;padding: 0 20px 20px ;font-family:Arial, Times New Roman, Verdana, Courier;font-size:14px;">';
    $message.= '<table bgcolor="#F5F5F5" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" height="100px" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:10px 40px;text-transform:uppercase"><h1 style="color:#181B1F;font-size:24px;">Votre nouveau mot de passe !</h1></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td style="padding:4px 40px;"><p style="color:#181B1F;font-size:14px;"> Bonjour ,<br /><br />Voici le nouveau mot de passe que vous avez demandé :<br />';
    $message.= 'Il s\'agit de : <strong>password235fs</strong><br /></td></tr>';
    $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;"> Vous pouvez dès à présent vous connecter à votre espace personnel en <a href="' . ROOTPATH . '/connexion.html">cliquant ici</a><br /></td></tr>';
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
    echo 'Mail envoyé';

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


?>

<div class="block_inside">
	<h1>Test mail</h1>
	
<?= (isset($send) ? $send : ''); ?>

</div>

<?php require_once '../elements/footer.php'; ?>