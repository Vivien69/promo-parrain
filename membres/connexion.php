<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
actualiser_session();
$title = 'Connexion';
?>
<meta name="description" content="Connectez-vous sur promo-parrain pour accéder a votre espace membre personnalisé et trouver de nombreux filleuls.">
<meta name="keywords" content="connexion, membre, filleuls, promo, parrain">
<link rel="canonical" href="https://www.promo-parrain.com/connexion" />
<meta name="robots" content="noodp,noydir" />
<?php
require_once '../elements/header2.php';

if (isset($_SESSION['membre_id'])) {
	$informations = array(/*Membre qui essaie de se connecter alors qu'il l'est déjà*/
		true,
		'Vous êtes déjà connecté',
		'Vous êtes déjà connecté avec le pseudo <span class="infotexte">' . htmlspecialchars($_SESSION['membre_utilisateur'], ENT_QUOTES) . '</span>.',
		' - <a href="' . ROOTPATH . '/deconnexion">Se déconnecter</a>',
		ROOTPATH . '/membres',
		3
	);

	require_once('../information.php');
	exit();
}

$_SESSION['erreurs'] = 0;

if (isset($_POST['connexion'])) {
	// Pseudo
	if (isset($_POST['email_addr'])) {
		$email_addr = trim($_POST['email_addr']);
		$mail_result = checkmail($email_addr);
		if ($mail_result == 'isnt') {
			$_SESSION['info']['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'est pas valide.</div>';
			$_SESSION['form']['mail'] = '';
			$_SESSION['erreurs']++;
		} else if ($mail_result == 'exists') {
			$_SESSION['form']['mail'] = htmlspecialchars($email_addr);
			$_SESSION['info']['mail_info'] = '';
		} else if ($mail_result == 'ok') {
			$_SESSION['info']['mail_info'] = '<div class="erreurform">Cette adresse email ' . htmlspecialchars($email_addr, ENT_QUOTES) . ' n\'existe pas dans nos données !</div>';
			$_SESSION['form']['mail'] = '';
			$_SESSION['erreurs']++;
		} else if ($mail_result == 'empty') {
			$_SESSION['info']['mail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
			$_SESSION['form']['mail'] = '';
			$_SESSION['erreurs']++;
		}
	}
if($_SESSION['erreurs'] === 0) {
	$sql = $pdo->prepare("SELECT * FROM user WHERE membre_email = :email_addr ");
	$sql->bindValue(':email_addr', $email_addr, PDO::PARAM_STR);
	if ($sql->execute() && $sql->rowcount() == 1) {
		$row = $sql->fetch();
		$GLOBALS['nb_req']++;
		$isadmin = $row['membre_etat'];
		$hash = $row['membre_pass'];
	} else {
		$informations = array(/*Compte non activé*/
			true,
			'Connexion impossible',
			'Votre adresse n\'existe pas dans notre base de donnée">cliquez ici. </a>',
			'',
			'',
			60
		);
		require_once('../information.php');
		exit();
	}
		
	if ($isadmin == 1 or $isadmin == 2) {

		if (isset($_POST['mot_pass'])) {
			$mot_pass = trim($_POST['mot_pass']);
			if (password_verify($mot_pass, $hash)) {
				$_SESSION['membre_id'] = $row['membre_id'];
				$_SESSION['membre_utilisateur'] = $row['membre_utilisateur'];
				$_SESSION['membre_email'] = $row['membre_email'];
				$_SESSION['membre_pass'] = $row['membre_pass'];

					//On actualise la date (timestamp) de dernière connexion du membre.
					$sql = $pdo->prepare('UPDATE user SET membre_lastco = ? WHERE membre_id = ?');
					$sql->execute(array(time(), $row['membre_id']));
					$GLOBALS['nb_req']++;


				unset($_SESSION['mail_info'], $_SESSION['erreurs'], $_SESSION['form'], $_SESSION['info']);

				if ($row['membre_etat'] == 2) {
					$_SESSION['admin_co'] = 'connecter';
				}

				if (isset($_POST['cookie']) && $_POST['cookie'] == 'on') {
					setcookie('membre_id', $row['membre_id'], time() + 365 * 24 * 3600, "/");
					setcookie('membre_pass', $row['membre_pass'], time() + 365 * 24 * 3600, "/");
				}

				$informations = array(/*Vous êtes bien connecté*/
					false,
					'Connexion réussie',
					'Vous êtes désormais connecté avec le pseudo <span class="infotexte">' . htmlspecialchars($_SESSION['membre_utilisateur'], ENT_QUOTES) . '</span>.',
					'',
					ROOTPATH . '/membres',
					1
				);
				require_once('../information.php');
				exit();
			} else {
				$_SESSION['info']['connexion_info'] = '<div class="erreurform">Mot de passe incorrect.</div>';
			}
		} else {
			$_SESSION['info']['connexion_info'] = '<div class="erreurform">Veuillez compléter votre mot de passe.</div>';
		}
	} else if($isadmin == 0) {

		$informations = array(/*Compte non activé*/
			true,
			'Connexion impossible',
			'Votre compte n\'est pas activé, vérifiez vos emails indésirables pour activer votre compte.<br /><br />Si vous souhaitrez que l\'on vous renvoi un email de confirmation, <a href="'.ROOTPATH.'/email/'.$_SESSION['form']['mail'].'">cliquez ici. </a>',
			'',
			'',
			60
		);
		require_once('../information.php');
		exit();
	}else if($isadmin == 4) {

		$informations = array(/*Compte supprimé*/
			true,
			'Compte supprimé',
			'Votre compte a été supprimé. Vous pouvez toujours le reactiver en contactant promo-parrain',
			'',
			ROOTPATH . '/contact',
			15
		);
		require_once('../information.php');
		exit();
	}

}
}
?>
<?php

if (isset($_SESSION['inscrit'])) { ?>

	<div class="valider">Nous vous remercions de votre inscription <strong><?php echo $_SESSION['inscrit']['pseudo']; ?></strong> !<br />
	Veuillez confirmer votre compte en cliquant sur le lien de validation dans l'email qui viens de vous être expédié.<br />
		Vous pourrez ensuite vous connecter avec vos identifiants ci-dessous. 
	</div>
<?php } ?>

<div class="block_inside">
	<h2> Connexion à votre espace membre</h2>
	
	
	<form action="<?= ROOTPATH . '/connexion'; ?>" method="post" id="connexion">
		<label for="nom_utilis" class="iconic2 nom_utilis">Votre adresse email :</label><input type="text" name="email_addr" id="email_addr" placeholder="Inscrivez votre email" value="<?php if (isset($_SESSION['form']['mail']) && $_SESSION['form']['mail'] != '') echo $_SESSION['form']['mail'];
																																														elseif (isset($_SESSION['inscrit']['mail'])) echo $_SESSION['inscrit']['mail']; ?>" required="required" /><br />
		<?php if (isset($_SESSION['info']['mail_info'])) echo $_SESSION['info']['mail_info']; ?>
		<label for="mot_pass" class="iconic2 mot_pass">Mot de passe :</label><input type="password" name="mot_pass" id="mot_pass" placeholder="Votre mot de passe" required="required" /><br />
		<?php if (isset($_SESSION['info']['connexion_info'])) echo $_SESSION['info']['connexion_info']; ?>
		<br />
		<label for="cookie" class="iconic2 cookie">Connexion automatique :</label><input type="checkbox" name="cookie" id="cookie" value="on" checked />
		<p style="display:inline;margin-left:80px;"><a title="Mot de passe oublié ?" href="passe_perdu">Mot de passe oubli&eacute; ?</a> </p>
		<br /><br />
		<label></label>
		<input class="bouton" type="submit" name="connexion" value="Envoyer" />
	</form>

	<br />
	<p>
		Vous n'avez pas de compte ? <a style="font-weight:bold" title="Inscription" href="inscription">Inscrivez-vous</a></p>
	</p><br />
</div>

<?php require_once '../elements/footer.php'; ?>