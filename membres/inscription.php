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
$title = 'Inscription';
?>
<meta name="description" content="Inscrivez vous sur promo-parrain pour accéder a une multitude de services de parrainage.">
<meta name="keywords" content="inscription, membre, promo, parrain">
<link rel="canonical" href="https://www.promo-parrain.com/inscription" />
<meta name="robots" content="noodp,noydir" />
<?php
require_once '../elements/header2.php';
$_SESSION['erreurs'] = 0;

if (isset($_POST["inscription"])) {

			// Pseudo
    if (isset($_POST['nom_utilis'])) {
        $nom_utilis = trim($_POST['nom_utilis']);
        $pseudo_result = checkpseudo($nom_utilis);
        if ($pseudo_result == 'tooshort') {
            $_SESSION['info']['pseudo_info'] = '<div class="erreurform">Le pseudo ' . htmlspecialchars($nom_utilis, ENT_QUOTES) . ' est trop court (minimum 5 caract&egrave;res).</div>';
            $_SESSION['form']['pseudo'] = '';
            $_SESSION['erreurs']++;
        } else if ($pseudo_result == 'toolong') {
            $_SESSION['info']['pseudo_info'] = '<div class="erreurform">Le pseudo ' . htmlspecialchars($nom_utilis, ENT_QUOTES) . ' est trop long (maximum 30 caract&egrave;res).</div>';
            $_SESSION['form']['pseudo'] = '';
            $_SESSION['erreurs']++;
        } else if ($pseudo_result == 'exists') {
            $_SESSION['info']['pseudo_info'] = '<div class="erreurform">Le pseudo ' . htmlspecialchars($nom_utilis, ENT_QUOTES) . ' est d&eacute;j&agrave;  pris, choisissez-en un autre.</div>';
            $_SESSION['form']['pseudo'] = '';
            $_SESSION['erreurs']++;
        } else if ($pseudo_result == 'ok') {
            $_SESSION['info']['pseudo_info'] = '';
            $_SESSION['form']['pseudo'] = htmlspecialchars($nom_utilis);
        } else if ($pseudo_result == 'empty') {
            $_SESSION['info']['pseudo_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de pseudo.</div>';
            $_SESSION['form']['pseudo'] = '';
            $_SESSION['erreurs']++;
        }
    }
    // Mot de passe
    if (isset($_POST['mot_pass'])) {
        $mot_pass = trim($_POST['mot_pass']);
        $mdp_result = checkmdp($mot_pass, '');
        if ($mdp_result == 'tooshort') {
            $_SESSION['info']['mdp_info'] = '<div class="erreurform">Le mot de passe entr&eacute; est trop court. (minimum 8 caract&egrave;res).</div>';
            $_SESSION['form']['mdp'] = '';
            $_SESSION['erreurs']++;
        } else if ($mdp_result == 'toolong') {
            $_SESSION['info']['mdp_info'] = '<div class="erreurform">Le mot de passe entr&eacute; est trop long. (maximum 30 caract&egrave;res)</div>';
            $_SESSION['form']['mdp'] = '';
            $_SESSION['erreurs']++;
        } else if ($mdp_result == 'ok') {
            $_SESSION['info']['mdp_info'] = '';
            $_SESSION['form']['mdp'] = $mot_pass;
        } else if ($mdp_result == 'empty') {
            $_SESSION['info']['mdp_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de mot de passe.</div>';
            $_SESSION['form']['mdp'] = '';
            $_SESSION['erreurs']++;
        }
    }
    // Adresse e-mail
    if (isset($_POST['email_addr'])) {
        $email_addr = trim($_POST['email_addr']);
        $mail_result = checkmail($email_addr);
        if ($mail_result == 'isnt') {
            $_SESSION['info']['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'est pas valide.</div>';
            $_SESSION['form']['mail'] = '';
            $_SESSION['erreurs']++;
        } else if ($mail_result == 'exists') {
            $_SESSION['info']['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' est d&eacute;j&agrave; pris !</div>';
            $_SESSION['form']['mail'] = '';
            $_SESSION['erreurs']++;
        } else if ($mail_result == 'ok') {
            $_SESSION['form']['mail'] = htmlspecialchars($email_addr);
            $_SESSION['info']['mail_info'] = '';
        } else if ($mail_result == 'empty') {
            $_SESSION['info']['mail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
            $_SESSION['form']['mail'] = '';
            $_SESSION['erreurs']++;
        }
    }
            $sql = $pdo->prepare("SELECT COUNT(membre_id) as present, membre_id, membre_utilisateur, membre_pass, membre_email, membre_etat, membre_IP FROM user WHERE membre_IP = :membre_ip");
            $sql->execute(array(":membre_ip" => $_SERVER['REMOTE_ADDR']));
            $retour = $sql->fetch();
            $GLOBALS['nb_req']++;
            if($retour['present'] >= 1)
            echo '<div class="erreur">Vous êtes déja membre, les doubles comptes sont interdits. </div>';
            else {
            $ladate = time();
            if ($_SESSION['erreurs'] == 0 && isset($_POST['mot_pass']) && isset($_POST['email_addr'])) {
            $sql = "INSERT INTO user (membre_utilisateur,membre_pass,membre_email,membre_date,membre_IP,membre_etat,membre_cle) 
			VALUES(:membre_utilisateur,:membre_pass,:membre_email,:membre_date,:membre_IP,0,:membre_cle)";
			$mdp = password_hash($_SESSION['form']['mdp'], PASSWORD_ARGON2ID);
			$cle = md5(microtime(TRUE)*100000);
			
			$sqlbind=$pdo->prepare($sql);
			$sqlbind->bindParam('membre_utilisateur', $_SESSION['form']['pseudo']);
			$sqlbind->bindParam('membre_pass', $mdp);
			$sqlbind->bindParam('membre_email', $_SESSION['form']['mail']);
			$sqlbind->bindParam('membre_date', $ladate);
			$sqlbind->bindParam('membre_IP', $_SERVER['REMOTE_ADDR']);
			$sqlbind->bindParam('membre_cle', $cle);
			
			if ($sqlbind->execute()) {
					$GLOBALS['nb_req']++;
					$_SESSION['inscrit']['pseudo'] = $_SESSION['form']['pseudo'];
                    $_SESSION['inscrit']['mail'] = $_SESSION['form']['mail'];

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
                    $mail->addAddress($_SESSION['inscrit']['mail']);     //Add a recipient
                    $mail->addReplyTo('no-reply@promo-parrain.com', 'No-Reply');
                
                    $sujet = ' Activer votre compte sur '.TITRESITE;
                    $message = '<!DOCTYPE html>';
                    $message.= '<html lang="en">';
                    $message.= '<head>';
                    $message.= '<meta charset="UTF-8">';
                    $message.= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
                    $message.= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
                    $message.= '<title>Activer votre compte sur '.TITRESITE.'</title>';
                    $message.= '</head>';
                    $message.= '<body style="background-color:#EEE;padding: 0 20px 20px;font-family:Arial, Times New Roman, Verdana, Courier;font-size:14px;">';
					$message.= '<table bgcolor="#F2F2F2" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
					$message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Activer votre compte</h2></td></tr>';
					$message.= '<tr bgcolor="#FFF"><td style="padding:4px 40px;"><p style="color:#181B1F;">Bienvenue sur Promo-parrain.com, <br />Pour activer votre compte, veuillez cliquer sur le lien ci dessous : </p><br />';
					$message.= '<a style="color:#701818;font-weight:bold;font-size:16px;" href="https://www.promo-parrain.com/valider/'.$_SESSION['inscrit']['pseudo'].'/'.$cle.'/">Activez-votre compte</a></td></tr>';
					$message.= '<tr><td bgcolor="#FFF"></td></tr>';
                    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;">Ou copier collez le lien suivant dans votre navigateur internet :<br /> https://www.promo-parrain.com/valider/'.$_SESSION['inscrit']['pseudo'].'/'.$cle.'/</p><br /></td></tr>';
                    $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Fonctionnalités débloqués</h2></td></tr>';
                    $message.= '<tr><td bgcolor="#FFF" style="padding:4px 40px;"><p style="color:#181B1F;">En activant votre comptez, vous aurez accès a une panoplie de fonctinnalités sur Promo-parrain.com, en commençant par la possibilité de contacter un parrain ou un filleul avec la messagerie, <br /> - Publier une offre de parrainage pour trouver des filleuls, gérer vos annonces publiées et les actualiser chaque jours pour les faire remonter en tête de liste et obtenir davantage de filleuls. <br /> - Gérer votre profil pour le rendre attractif pour vos filleuls, votre parrain, ou les utilisateurs du site et consulter vos notes et avis. </p></td></tr>';
					$message.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
					$message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
                    $message.= '</table></table>';
					$message.= '</body>';
                    $message.= '</html>';
                
                
                    $mail->isHTML(true);                                  //Set email format to HTML
                    $mail->Subject = $sujet;
                    $mail->Body    = $message;

					if($mail->send())
					{
						$reponse = 'ok';
					}else{
						$reponse = 'Erreur. Mail non envoyé. ';
					}
					unset($_SESSION['form']);
                    header ("Refresh: 10;URL=".ROOTPATH."/connexion");
                
                    ?>
            <div class="valider">Un email de confirmation vous à été envoyé à votre adresse <strong><?php echo $_SESSION['inscrit']['mail']; ?></strong> !<br/>
            Veuillez valider votre adresse email avant de vous connecter.
            </div>
<?php
                }
            } elseif ($_SESSION['erreurs'] > 0) {
                if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, veuillez vérifier les champs.</div>';
                else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreurs'] . ' erreurs dans votre formulaire, veuillez vérifier les champs.</div>';

				echo $_SESSION['nb_erreurs'];
            }
        
    
		}
    }



?>

<section class="block_inside">

	<h1>Inscription</h1>

	<form action="<?= ROOTPATH ?>/inscription" method="post" id="inscription">

    <div id="form_ajouter">

			<div id="d_nom_utilis">
				<label for="nom_utilis">Nom d'utilisateur :</label>
				
					<input type="text" class="<?=$class ?>" name="nom_utilis" id="nom_utilis" placeholder="Inscrivez un pseudo" value="<?php if (isset($_SESSION['form']['pseudo'])) echo $_SESSION['form']['pseudo']; ?>" />
					<br /><?php if (isset($_SESSION['info']['pseudo_info'])) echo $_SESSION['info']['pseudo_info']; ?>
			</div>	
			
			
			<h2> Informations de connexion</h2>
			
			<div id="d_email_addr">
				<label for="email_addr">Adresse email :</label>
					<input type="email" class="<?=$class ?>" name="email_addr" id="email_addr" placeholder="Ajoutez votre adresse em@il" value="<?php if (isset($_SESSION['form']['mail'])) echo $_SESSION['form']['mail']; ?>" required />
					<br /><?php if (isset($_SESSION['info']['mail_info']))echo $_SESSION['info']['mail_info']; ?>
			</div>
			<div id="d_mot_pass">
				<label for="mot_pass">Mot de passe :</label>
					<input type="password" class="<?=$class ?>" name="mot_pass" id="mot_pass" placeholder="Votre mot de passe"  value="<?php if (isset($_SESSION['form']['mdp'])) echo $_SESSION['form']['mdp']; ?>" required />
					<br /><?php if (isset($_SESSION['info']['mdp_info']))echo $_SESSION['info']['mdp_info']; ?>
			</div>
			
			<div class="QapTcha" style="margin:40px 50px 10px 300px;"></div>
		
		<label></label>
			<input class="bouton" type="submit" name="inscription" value="Envoyer" /><br /><br />
	 </form>
	 

	</div>

<br />

<p>
Vous avez déja un compte ? <a style="font-weight:bold" title="Connexion" href="connexion">Connexion</a></p>
</p>

</section>

<?php require_once '../elements/footer.php'; ?>