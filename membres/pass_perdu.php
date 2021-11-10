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
$title = 'Mot de passe oublié';
require_once '../elements/header2.php';

$_SESSION['erreurs'] = 0;

if (isset($_POST["pass_perdu"])) { // Si l'utilisateur est bien passé par le post du formulaire

	// Adresse e-mail
	if (isset($_POST['email_addr'])) {
		$email_addr = trim($_POST['email_addr']);
		$mail_result = passperdu($email_addr);
		if ($mail_result == 'isnt') {
			$_SESSION['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'est pas valide.</div>';
			$email_addr = '';
			$_SESSION['erreurs']++;
		} else if ($mail_result == 'existpas') {
			$_SESSION['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'existe pas dans notre base de donnée.</div>';
			$email_addr = '';
			$_SESSION['erreurs']++;
		} else if ($mail_result == 'plusieurs') {
			$_SESSION['mail_info'] = '<div class="erreurform">Plusieurs adresses email correspondant à ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' existe, contactez un admin !</div>';
			$email_addr = '';
			$_SESSION['erreurs']++;
		} else if ($mail_result == 'ok') {
			$_SESSION['mail_info'] = '';
			$_SESSION['form_mail'] = htmlspecialchars($email_addr);
		} else if ($mail_result == 'empty') {
			$_SESSION['mail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
			$email_addr = '';
			$_SESSION['erreurs']++;
		}
	}


	if ($mail_result == 'ok') {

		$prep = $pdo->prepare('SELECT * FROM user WHERE membre_email = :email');
		$GLOBALS['nb_req']++;
		$prep->bindValue(':email', $_SESSION['form_mail'], PDO::PARAM_STR);
		$prep->execute();
		$result = $prep->fetch(PDO::FETCH_ASSOC);
		if($prep->rowcount() == 1) {
			$key = md5(microtime(TRUE)*100001);
			$prep = $pdo->prepare('UPDATE user 
			SET membre_cle = :key
			WHERE membre_email = :email');
			$GLOBALS['nb_req']++;
			$prep->bindValue(':email', $_SESSION['form_mail'], PDO::PARAM_STR);
			$prep->bindValue(':key', $key, PDO::PARAM_STR);
			$prep->execute();
		}

		$to = '' . $email_addr . '';
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
    $mail->DKIM_private = '../includes/PHPMailer/DKIM/private.key';
    $mail->DKIM_selector = '1632130975.parrain';
    $mail->DKIM_passphrase = '1632130975';
    $mail->DKIM_identity = $mail->From;

    //Recipients
    $mail->setFrom('admin@promo-parrain.com', 'Promo-Parrain');
    $mail->addAddress($to);     //Add a recipient
    $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');

		$sujet = $result['membre_utilisateur'] . ' votre mot de passe perdu sur ' . TITRESITE;

	$message = '<!DOCTYPE html>';
    $message.= '<html lang="en">';
    $message.= '<head>';
    $message.= '<meta charset="UTF-8">';
    $message.= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    $message.= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $message.= '<title>Mot de passe perdu</title>';
    $message.= '</head>';
    $message.= '<body style="background-color:#EEE;padding: 0 20px 20px ;font-family:Arial, Times New Roman, Verdana, Courier;font-size:14px;">';
    $message.= '<table bgcolor="#F5F5F5" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" height="100px" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:10px 40px;text-transform:uppercase"><h1 style="color:#181B1F;font-size:24px;">Votre nouveau mot de passe</h1></td></tr>';
    $message.= '<tr bgcolor="#FFF"><td style="padding:4px 40px;"><p style="color:#181B1F;font-size:14px;"> Bonjour ' . $result['membre_utilisateur']  . ',<br /><br />Vous avez effectuer une demande de mot de passe perdu. Dans un soucis de sécurité, et afin de confirmer que vous êtes bien le propriétaire de cette adresse email, je vous invite à générer un nouveau mot de passe en cliquant sur ce lien :<br />';
    $message.= '<a style="color:#701818;font-weight:bold;font-size:15px;" href="' . ROOTPATH . '/generate_pass/'.$email_addr.'/'.$key.'>"Générer un nouveau mot de passe</a></strong></td></tr>';
    $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;">Ou copier collez le lien suivant dans votre navigateur internet :<br /><br /> ' . ROOTPATH . '/generate_pass/'.$email_addr.'/'.$key.'</p><br /><br />Pensez à modifier votre mot de passe dans votre espace personnel pour vous en rappeler !</td></tr>';
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

			if ($mail->send()) echo '<div class="valider">Un email vous à été envoyé à votre adresse ' . htmlspecialchars($email_addr, ENT_QUOTES) . '.<br />Veuillez vérifier vos nouveaux emails afin de générer un nouveau mot de passe.</div>';
			else echo '<div class="erreur">Un mail de confirmation devait être envoyé, mais son envoi a échoué, réessayez plus tard.</div>';
		} else echo '<div class="erreur">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'existe pas !</div>';
	} elseif ($_SESSION['erreurs'] > 0) {
		if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
		else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreurs'] . ' erreurs dans votre formulaire, merci de les corriger !</div>';

		echo $_SESSION['nb_erreurs'];
	}

vidersession();
?>

<div class="block_inside">
	<h1>Mot de passe perdu</h1>
	<p style="padding:0 20px;">Si vous avez perdu votre mot de passe, indiquez votre adresse email ci-dessous, un email vous sera envoyé afin d'indiquer un nouveau mot de passe.</p><br />
	<form action="passe_perdu" method="post" id="pass_perdu">
		<label for="email_addr" style="font-weight:bold;">Adresse email :</label> <input type="email" name="email_addr" id="email_addr" placeholder="Inscrivez votre adresse email" required="required" /><br />
		<?php if (isset($_SESSION['mail_info'])) echo $_SESSION['mail_info']; ?>
		<br /><input class="bouton" style="margin-left:200px;" type="submit" name="pass_perdu" value="Envoyer" />
	</form>

</div>

<?php require_once '../elements/footer.php'; ?>