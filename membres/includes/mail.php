<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
actualiser_session();
$title = 'Renvoi du mail de confirmation';
require_once '../../elements/header2.php';

$_SESSION['erreurs'] = 0;
if(isset($_GET['email'])) {
      if (isset($_GET['email'])) {
		$email_addr = trim($_GET['email']);
		$mail_result = checkifmail($email_addr);
		if ($mail_result == 'isnt') {
			$_SESSION['info']['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'est pas valide.</div>';
			$_SESSION['form']['mail'] = '';
			$_SESSION['erreurs']++;
		}  else if ($mail_result == 'ok') {
			$_SESSION['info']['mail_info'] = '';
			$_SESSION['form']['mail'] = $email_addr;
		} else if ($mail_result == 'empty') {
			$_SESSION['info']['mail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
			$_SESSION['form']['mail'] = '';
			$_SESSION['erreurs']++;
		}
	}

      $sql = $pdo->prepare('SELECT * FROM user WHERE membre_email = :email');
      $sql->bindParam(':email', $_SESSION['form']['mail'], PDO::PARAM_STR);
      $sql->execute();
      if($sql->rowcount() === 1) {
            $sql = $sql->fetch();
            if($sql['membre_etat'] == 0) {
                  $sujet = ' Activer votre compte sur '.TITRESITE.'';
                  $message = '<table bgcolor="#F2F2F2" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
                  $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Activer votre compte</h2></td></tr>';
                  $message.= '<tr bgcolor="#FFF"><td style="padding:4px 20px;"><p style="color:#181B1F;">Bienvenue sur Promo-parrain.com, <br />Pour activer votre compte, veuillez cliquer sur le lien ci dessous : </p><br />';
                  $message.= '<a style="color:#701818;font-weight:bold;font-size:16px;" href="https://www.promo-parrain.com/valider/'.$sql['membre_utilisateur'].'/'.$sql['membre_cle'].'/">Activez-votre compte</a></td></tr>';
                  $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
                  $message.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;"><p style="color:#181B1F;">Ou copier collez le lien suivant dans votre navigateur internet :<br /> https://www.promo-parrain.com/valider/'.$sql['membre_utilisateur'].'/'.$sql['membre_cle'].'/</p></td></tr>';
                  $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Fonctionnalités débloqués</h2></td></tr>';
                  $message.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;"><p style="color:#181B1F;">En activant votre comptez, vous aurez accès a une panoplie de fonctinnalités sur Promo-parrain.com, en commençant par la possibilité de contacter un parrain ou un filleul avec la messagerie, <br /> - Publier une offre de parrainage pour trouver des filleuls, gérer vos annonces publiées et les actualiser chaque jours pour les faire remonter en tête de liste et obtenir davantage de filleuls. <br /> - Gérer votre profil pour le rendre attractif pour vos filleuls, votre parrain, ou les utilisateurs du site et consulter vos notes et avis. </p></td></tr>';
                  $message.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
                  $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
                  $message.= '</table></table>';
                  $headers = "From: Promo-Parrain.com <admin@promo-parrain.com>\r\n".
                        "Reply-To: Promo-Parrain.com <no-reply@promo-parrain.com>\r\n".
                        "MIME-Version: 1.0" . "\r\n" .
                        "Content-type: text/html; charset=UTF-8" . "\r\n";
                  if(mail($_SESSION['form']['mail'],$sujet,$message,$headers))
                        $reponse = 'Bonjour <b>'.$sql['membre_utilisateur'].'</b>, un nouvel email de confirmation vous a été envoyé. Veuillez vérifiez vos courriers indésirables !';
                  else
                        $reponse = 'Erreur. Mail non envoyé. Contactez-nous';
            } else 
            $reponse = 'Votre compte est déja activé';
      }
} 



?>

<div class="block_inside">
	<h2> Renvoi du mail de validation</h2>
	<?php if(isset($_SESSION['erreurs'])) 
      $_SESSION['info']['mail_info']
      ?>
      <?= $reponse ?? '' ?>

	<br />
</div>

<?php require_once '../../elements/footer.php'; ?>