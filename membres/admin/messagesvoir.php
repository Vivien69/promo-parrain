<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$titre = 'Admin :: Voir message';
require_once '../../elements/header2.php';
/**
 * *******Gestion avant affichage...**********
 */
    if(isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
	} else {
		$informations = Array(/*L'id de cookie est incorrect*/
        true,
        'Vous n\'&ecirc;tes pas connect&eacute;',
        'Impossible d\'accéder à cette page membre.',
        ' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
        ''. ROOTPATH . '/index.html',
        3
        );
    require_once('../../information.php');
    exit(); 
	}

$sql = "SELECT * FROM user WHERE membre_id=".$id.' AND membre_etat = 2';
$prep = $pdo->prepare($sql);
$prep->execute();

if($prep->rowCount() == 0) {
	$informations = Array(/*L'id de cookie est incorrect*/
            true,
            'Accès interdit',
            'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
            '',
            ''. ROOTPATH . '/index.html',
            3
            );
        require_once('../../information.php');
        exit();
	
}
while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
	$current = 'admin_messages' ;
	 require_once '../includes/menu_membres.php';
    /**
     * *******FIN Gestion avant affichage...**********
     */

	if (isset($_GET["message"]) && is_numeric($_GET["message"])) {
			$num = intval($_GET["message"]);
			$sql= 'SELECT * FROM messages WHERE id = '.$num;
			$GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
			$sql2 = $pdo->prepare('UPDATE messages SET vu_par = '.$_SESSION['membre_id'].' WHERE id = '.$num.'');
			$sql2->execute();
			if ($prep->rowcount() == 1)
				$result = $prep->fetch(PDO::FETCH_ASSOC);
	}

	if(isset($_POST['sendresponse']) && !empty($_POST['message'])) {
		$message = $_POST['message'];
		$to = $result['email']; 
		$subject = ' Réponse à votre message sur '.TITRESITE.'';
                  $mailis= '<table bgcolor="#F2F2F2" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
                  $mailis.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Réponse à votre message sur Promo-parrain.com</h2></td></tr>';
                  $mailis.= '<tr bgcolor="#FFF"><td style="padding:4px 20px;"><p style="color:#181B1F;">Bonjour '.$result['nom_utilisateur'].', <br />Merci de nous avoir contacté, votre message à bien été pris en compte.</p>';
                  $mailis.= '</td></tr>';
                  $mailis.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;">	'.nl2br($message).'</td></tr>';
				  $mailis.= '<tr><td bgcolor="#FFF" style="padding:4px 20px;"><br />	A bientôt sur <a href="https://www.promo-parrain.com">Promo-parrain.com</a><br /></td></tr>';
                  $mailis.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
                  $mailis.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
                  $mailis.= '</table></table>';
                  $headers = "From: Promo-Parrain.com <admin@promo-parrain.com>\r\n".
                        "Reply-To: Promo-Parrain.com <admin@promo-parrain.com>\r\n".
                        "MIME-Version: 1.0" . "\r\n" .
                        "Content-type: text/html; charset=UTF-8" . "\r\n";
	
	if(mail($to, $subject, $mailis, $headers)) echo '<div class="valider">Un mail à été envoyé à '.htmlspecialchars($result['email'], ENT_QUOTES).'</div>';
	else echo '<div class="erreur">Erreur lors de l\'envoi de l\'email.</div>';
		
	}
    ?>

  <section class="block_inside">


	<div id="aff_liste" style="margin-bottom:10px;"><a href="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>">Retour</a></div>
  	<?php
	  echo '
	 	<div class="list-item" style="height:auto;display:block;text-align:left;">
			<p class="title" style="font-size:25px;font-family:BebasNeueRegular,Verdana,Arial,Sans-serif;margin-bottom:5px;font-weight:bold;display:inline-block;">Message n°'.$result['id'].'</p>
			'.mepd($result['date']).'
			<p>par <b>'.$result['nom_utilisateur'].'</b></p>
			<p> <b>E-mail : </b>'.$result['email'].'</p> 
			
			<a class="pboutonr" style="text-decoration:none;color:#FFF;float:right;margin:-30px 20px 0 0;" href="'.ROOTPATH.'/membres/admin/messages.php?message='.$result['id'].'&delete=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer ce message ?\')) return false;">Supprimer</a>
			<p style="margin-top:20px;"> <b>Sujet : </b>'.($result['sujet'] == 1 ? 'Demande de Renseignement':'').($result['sujet'] == 2 ? 'L\'utilisateur à un problème ?':'').($result['sujet'] == 3 ? 'L\'utilisateur signale un abus':'').($result['sujet'] == 4 ? 'Autre':'').($result['sujet'] == 5 ? 'Offre '.$result['offre'] : '').'</p>
			<p style="width:70%x;display:inline-block;vertical-align:top;margin-top:5px;"> <b>Message : </b>'.$result['message'].'</p>';	
  	
  	
	 ?>
	<form action="" method="POST" name="sendresponse" id="sendresponse" style="text-align:center;width:70%;margin-top:40px;">
	<p style="margin: 10px 0 5px 20px;font-weight:bold;font-size:20px;">REPONDRE :</p>
		<textarea style="max-width:90%;height:100px;" id="message" name="message" placeholder="Inscrivez ici votre réponse"></textarea>
		<input class="bouton" style="margin-top:10px;" name="sendresponse" type="submit" value="Envoyer" />
	</form>
	</div>

</section>

<?php } require_once '../../elements/footer.php'; ?>