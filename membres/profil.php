<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */
if (isset($_GET['idm']))
	$id = intval($_GET['idm']);
else {
	if (isset($_SESSION['membre_id']))
		$id = intval($_SESSION['membre_id']);
	else
		$id = 0;
}

$sql = "SELECT * FROM user WHERE membre_id= :membre_id AND membre_etat != 3 AND membre_etat != 4";
$prep = $pdo->prepare($sql);

$prep->execute(array(":membre_id" => $id));

if ($prep->rowCount() == 0) {
	require_once '../elements/header2.php';
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Accès interdit',
		'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
		'',
		'../index.php',
		3
	);
	require_once('../information.php');
	exit();
} else {
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
		/**
		 * *******FIN Gestion avant affichage...**********
		 */

		$title = 'Profil de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';
		require_once '../elements/header2.php';

			$current = 'monprofil';
			(isset($_SESSION['membre_id']) && ($_SESSION['membre_id'] == $row['membre_id']) ? require_once 'includes/menu_membres.php' : '');
?>

<link rel="stylesheet" href="<?= ROOTPATH; ?>/css/imgPicker.css">
<script src="<?= ROOTPATH; ?>/script/jquery.Jcrop.min.js"></script>
<script src="<?= ROOTPATH; ?>/script/jquery.imgpicker.js"></script>


<section class="block_inside" style="padding:0;">

	<!-- HEADER ET AVATAR -->
	<?php
	$sqla = "SELECT * FROM images WHERE id_membre = " . $row['membre_id'] . " AND type='header'";
	$prepa = $pdo->prepare($sqla);
	$prepa->execute();
	if ($prepa->rowcount() == 1) {
		$resulta = $prepa->fetch(PDO::FETCH_ASSOC);
		$cover = ROOTPATH . '/membres/images/' . $resulta['image'];
	} else {
		$cover = ROOTPATH . '/membres/images/default_cover.jpg';
	} ?>

	<div class="cover" style="background-image: url('<?= $cover ?>');">
		<?= (isset($_SESSION['membre_id']) && ($_SESSION['membre_id'] == $row['membre_id']) ? '<button type="button" data-ip-modal="#headerModal"><i class="fas fa-pen"></i></button>' : ''); ?>
	</div>
	<div class="avatar_container">
		<?php
		$sqla = "SELECT * FROM images WHERE id_membre = " . $row['membre_id'] . " AND type='avatar'";
		$prepa = $pdo->prepare($sqla);
		$prepa->execute();
		if ($prepa->rowcount() == 1) {
			$resulta = $prepa->fetch(PDO::FETCH_ASSOC);
			$avatar = ROOTPATH . '/membres/images/' . $resulta['image'];
		} else {
			$avatar = ROOTPATH . '/membres/images/default_avatar.png';
		} ?>
		<img src="<?= $avatar ?>" class="avatar" style="width:110px;height:110px;">
		<?= (isset($_SESSION['membre_id']) && ($_SESSION['membre_id'] == $row['membre_id']) ? '<button type="button" id="edit-avatar" class="edit-avatar btn btn-info" data-ip-modal="#avatarModal"><i class="fas fa-pen"></i></button>' : ''); ?>
		

	</div>

	<!-- PROFIL DE ... et DESCRIPTION  -->

	<article style="display:grid;grid-template-columns: 70% 30%;margin-top:-50px;">
		<div>
			<h1 style="margin-left:130px;margin-top:0;">Profil de <?= $row['membre_utilisateur']; ?></h1>
		</div>
		<div>
			<a href="" rel="popup_sendmessage" class="poplight pboutonr"><i class="fas fa-envelope"></i> Envoyer un MP</a>
		</div>
		
	</article>

	<!-- ASIDE VILLE INSCRIPTION -->
	<br /><br />

	<aside style="display:grid;grid-template-columns: 65% 35%;">
		<div>
			<?php
			if (isset($row['membre_plusinfos']))
				echo '<p>' . $row['membre_plusinfos'];
			else
				echo '<p><b>' . $row['membre_utilisateur'] . '</b> votre description de profil est vide, rendez-vous dans <b>vos paramètres</b> pour compléter votre fiche.<br />La modification de votre logo et du fond de votre profil se fait sur cette page.';
			$nbavis = nombrecom($row['membre_id']);
			$moyenne = notemoyenne($row['membre_id']);
			
				
			?></p>
		</div>
		<div style="text-align:left;">
			<p style="margin-bottom: 5px;"><i style="margin-right:5px;" class="fas fa-user fa-lg bred"></i> Inscrit <?php echo mepd($row['membre_date']); ?></p>
			<p style="margin-bottom: 3px;"><i style="margin-right:5px;" class="fas fa-comment fa-lg bred"></i> <b>Avis : </b><a href="#comment"><?= $nbavis.' '.$moyenne ?></a></p>
			<?= ($row['conf_online'] == 1 ? '<p style="margin-bottom: 5px;"><i style="margin-right:5px;" class="far fa-clock fa-lg bred"></i> <b>Dernière connexion : </b>'.is_online($row['membre_lastco']).'</p>' : ''); ?>
			
			
		</div>
	</aside>

	

	<article style="padding:5px 10px 50px;">
		<h2>Offres de parrainage</h2>
		<?php
			$sql = "SELECT AP.id, AP.idmarchand, AP.idmembre, M.montantremise, M.montantdevise, M.montantachatminimal, AP.description, AP.dateajout, AP.actualisation, M.img, M.nom_marchand
			FROM annonces_parrainage AP
			LEFT JOIN marchands M ON AP.idmarchand = M.id
			WHERE AP.idmembre = " . $row['membre_id'];
			$prepare = $pdo->prepare($sql);
			$prepare->execute();
			$GLOBALS['nb_req']++;
			if ($prepare->rowcount() > 0) {
				while ($result = $prepare->fetch(PDO::FETCH_ASSOC)) {
					echo '<a href="' . ROOTPATH . '/parrain/' . format_url($result['nom_marchand']) . '-' . $result['id'] . '"><div class="presentation-categories" style="padding:20px 20px 10px 20px;display:inline-block;background-color:#eee;height:140px;margin-right:10px;vertical-align: top;">
								<span style="color: #701818;">' . $result['nom_marchand'] . '</span>
								<div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $result['img'] . '\'); background-size: 140px;background-repeat: no-repeat;"></div>
						</div></a>';
				}
			} else {
				echo '<div class="box_annonces"><p>Vous n\'avez actuellement aucune annonce de parrainage.</p></div>';
			}
		
			?>
	</article>
			<br />
</section>
<br />
<section class="block_inside" style="padding:0;background:unset;">
	<div id="flex" style="align-items:flex-start">
		<!-- COMMENTAIRES  -->
		<article class="columnflex" style="background:#FFF;padding:10px;">

			<article id="comment">
				
				<div class="comment">
					<p><i style="margin-right:10px;font-size:22px;" class="fas fa-comment"></i><?= $row['membre_utilisateur'].' à '.nombrecom($row['membre_id']) ?> avis </p>
				</div>
				<?php
					if(isset($_SESSION['membre_id'])) :
						$dateac = time();
						// On vérifie qu'un parrainage a bien été exécutée entre parrain et filleul et qu'il est a l'étape 5-4 ou 3-2 sans bonus ou qu'il date de plus de 30 jours. 
						$bdd = $pdo->prepare("SELECT EP.id_filleul, EP.id_parrain, EP.statut_parrainage, EP.bonus, M.img, M.id, M.nom_marchand, EP.date FROM execparrainages EP
						LEFT JOIN marchands M ON EP.id_marchand = M.id
						WHERE (EP.id_parrain = :id_parrain AND EP.id_filleul = ".$_SESSION['membre_id'].") OR (EP.id_parrain = ".$_SESSION['membre_id']." 
						AND EP.id_filleul = :id_parrain)
						AND ((EP.bonus is NULL AND (EP.statut_parrainage = 2 OR EP.statut_parrainage = 3)) OR (EP.bonus is NOT NULL AND (EP.statut_parrainage = 4 OR EP.statut_parrainage = 5)) OR (($dateac  - EP.date) > 2592000))
						AND EP.deleted_filleul = 0 AND EP.deleted_parrain = 0
						");
						$bdd->bindParam(':id_parrain', $row['membre_id'], PDO::PARAM_INT);
						$bdd->execute();
						$fetch = $bdd->fetch();
						$nb = $bdd->rowcount();
					

						if($nb > 0)  :
							
							//Un parrainage a été exécuté, on vérifie qu'il n'y ai pas déja un avis du parrain sur ce profil 

							$bdd = $pdo->prepare("SELECT C.id_sender, C.id_receiver, C.id, EP.statut_parrainage, M.img, M.id, M.nom_marchand FROM comments C
							LEFT JOIN execparrainages EP ON EP.id_marchand = C.id_marchand
							LEFT JOIN marchands M ON M.id = C.id_marchand
							WHERE C.id_sender = ".$_SESSION['membre_id']." and C.id_receiver = :id_parrain");
							$bdd->bindParam(':id_parrain', $row['membre_id'], PDO::PARAM_INT);
							$bdd->execute();
							$fetch2 = $bdd->fetch();
							$nb = $bdd->rowcount();
							
							if($nb == 0)  :
								//echo print_r($fetch);
						?>
				<p style="padding:10px">Laisser un avis à <strong><?= $row['membre_utilisateur'] ?></strong> pour le parrainage <strong><?= $fetch['nom_marchand'] ?></strong></p>
				<div id="reponseaddcom"></div>
				
				<div class="add_comment"><br/>
										
					<div class="note-star">
						<i class="far fa-star changestar fa-2x" data-value="1"></i><i class="far fa-star changestar fa-2x" data-value="2"></i><i class="far fa-star changestar fa-2x" data-value="3"></i><i class="far fa-star changestar fa-2x" data-value="4"></i><i class="far fa-star changestar fa-2x" data-value="5"></i>
						<div class="item-img-solo" style="background-image: url('<?= ROOTPATH ?>/membres/includes/uploads-img/120-<?= $fetch['img'] ?>');background-size:140px;justify-self:end;margin-top:15px;margin-right:0;"></div>
					</div>
					
					<div style="grid-area: 1 / 2 / 2 / 2;">
						<form method="POST" id="comments" name="comments">
							<textarea id="inputadd_comment" name="inputadd_comment" placeholder="Ajoutez un commentaire ..."></textarea>
							<input style="display:none" type="text" name="note" id="note">
							<input style="display:none" type="text" name="idsender" value="<?= $_SESSION['membre_id']; ?>">
							<input style="display:none" type="text" name="idreceiver" value="<?= $row['membre_id']; ?>">
							<input style="display:none" type="text" name="id_marchand" value="<?= $fetch['id']; ?>">
							<input style="display:none" type="text" name="name" id="name" value="">
							<button class="bouton" id="send" name="submit" style="float:right;margin-right:10px;">Envoyer</button>
						</form>
					</div>
				</div>
					
				<?php 		endif;
						endif; 
					endif;
				?>
				<div id="listcomment">

				</div>

			</article>
		</article>

		<article class="rightcontext">
			<aside class="aff_droit">
				<span>Badges</span>

				<?php
				$sql = $pdo->query("SELECT UB.date, B.palier, B.type, MAX(B.level) as level FROM userbadges UB
				LEFT JOIN badges B ON UB.idbadge = B.id
				WHERE UB.idmembre = ".$row['membre_id']."
				GROUP BY B.type");
				$fetch = $sql->fetchAll();
				//pr($fetch);
				
				foreach($fetch as $f) {

					switch ($f['type']) {
						case '1':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icoprez"></div></div>';
						break;
						case '1':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icooffre"></div></div>';
						break;
						case '2':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icoparrainage"></div></div>';
						break;
						case '3':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icoavis"></div></div>';
						break;
						case '4':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icoavis"></div></div>';
						break;
						case '5':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icomarchands"></div></div>';
						break;
						case '6':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icomarchands"></div></div>';
						break;
						case '8':
							echo ' <div class="crown'.$f['level'].'" style="opacity:1;"><div class="icoparrainage"></div></div>';
						break;
						
					}
				
				}


				?>
			</aside>
		</article>
	</div>
</section>


<!-- Avatar Modal -->
<div class="ip-modal" id="avatarModal">
	<div class="ip-modal-dialog">
		<div class="ip-modal-content">
			<div class="ip-modal-header">
				<a class="ip-close" title="Close">&times;</a>
				<h4 class="ip-modal-title">Changez votre avatar</h4>
			</div>
			<div class="ip-modal-body">
				<div class="pboutonr ip-upload">SELECTIONNEZ UNE IMAGE <input type="file" name="file" class="ip-file"></div>
				<button type="button" class="btn btn-info ip-edit">Modifier</button>
				<button type="button" class="btn btn-danger ip-delete">Supprimer</button>

				<div class="alert ip-alert"></div>
				<div class="ip-info"><b>Cliquez sur une region pour recadrer l'image</b></div>
				<div class="ip-preview"></div>
				<div class="ip-rotate">
					<button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="fas fa-undo"></i></button>
					<button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="fas fa-redo"></i></button>
				</div>
				<div class="ip-progress">
					<div class="text">Uploading</div>
					<div class="progress progress-striped active">
						<div class="progress-bar"></div>
					</div>
				</div>
			</div>
			<div class="ip-modal-footer">
				<div class="ip-actions">
					<button type="button" class="btn btn-success ip-save">Enregistrer l'image</button>
					<button type="button" class="btn btn-primary ip-capture">Capture</button>
					<button type="button" class="btn btn-default ip-cancel">Annuler</button>
				</div>
				<button type="button" class="btn btn-default ip-close">Fermer</button>
			</div>
		</div>
	</div>
</div>
<!-- end Modal -->

<!-- Header Modal -->
<div class="ip-modal" id="headerModal">
	<div class="ip-modal-dialog">
		<div class="ip-modal-content">
			<div class="ip-modal-header">
				<a class="ip-close" title="Close">&times;</a>
				<h4 class="ip-modal-title">Changez votre fond</h4>
			</div>
			<div class="ip-modal-body">
				<div class="pboutonr ip-upload">Upload <input type="file" name="file" class="ip-file"></div>
				<!-- <button type="button" class="btn btn-primary ip-webcam">Webcam</button> -->
				<button type="button" class="btn btn-info ip-edit">Modifier</button>
				<button type="button" class="btn btn-danger ip-delete">Supprimer</button>

				<div class="alert ip-alert"></div>
				<div class="ip-info"><b>Cliquez sur une region pour recadrer l'image</b></div>
				<div class="ip-preview"></div>
				<div class="ip-rotate">
					<button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="fas fa-undo"></i></button>
					<button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="fas fa-redo"></i></button>
				</div>
				<div class="ip-progress">
					<div class="text">Uploading</div>
					<div class="progress progress-striped active">
						<div class="progress-bar"></div>
					</div>
				</div>
			</div>
			<div class="ip-modal-footer">
				<div class="ip-actions">
					<button type="button" class="btn btn-success ip-save">Enregistrer l'image</button>
					<button type="button" class="btn btn-primary ip-capture">Capture</button>
					<button type="button" class="btn btn-default ip-cancel">Annuler</button>
				</div>
				<button type="button" class="btn btn-default ip-close">Fermer</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal ENVOYER MP -->

<div id="popup_sendmessage" class="popup_block">
	<h2><i class="fas fa-pen fa-flip-horizontal"></i>  Envoyer un MP</h2><br />
	<p>Pour envoyer un message à <?= $row['membre_utilisateur']; ?> complétez le formulaire : </p>

	<!-- FORMULAIRE d'envoi MP : -->
	<div id="result" style="padding:5px;"></div>
	<form action="" method="post" id="contactmail" name="contactmail">
		<textarea name="ca_message" id="ca_message" style="min-width:200px;height:170px;max-width:550px;" required="required" placeholder="Salut, voici mon message"></textarea>
		<br /><?php if (isset($_SESSION['message_info']))echo $_SESSION['message_info']; ?>
		<input type="text" <?php if(isset($_SESSION['membre_utilisateur'])) echo 'style="display:none;"'; ?> name="ca_pseudo" id="ca_pseudo" placeholder="Votre nom" style="width:110px;min-width:100px;" value="<?php if (isset($_SESSION['membre_utilisateur'])) echo $_SESSION['membre_utilisateur']; ?>" required="required" />
		<br /><?php if (isset($_SESSION['nom_info']))echo $_SESSION['nom_info']; ?> <?php if (isset($_SESSION['telephone_info']))echo $_SESSION['telephone_info']; ?>
		<input type="text" style="display:none;" name="ca_idm" id="ca_idm" value="<?= (isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '0'); ?>" />
		<input type="text" style="display:none;" name="ca_dest" id="ca_dest" value="<?= $row['membre_id'] ?>" />
		<input type="text" style="display:none;" name="ca_marchand"id="ca_marchand" value="0"/>
		<input type="text" style="display:none;" name="ca_nom" id="ca_nom" value=""/>
		<input type="checkbox" style="display:none;" name="validerform" id="validerform" value="1" />
		<br />
		<?php
			if(isset($_SESSION['membre_id'])) { ?>
				<div style="display:flex;justify-content:flex-end;flex-direction:row;align-items:center;justify-content:space-around;">
					<a class="close" style="justify-self: end;" href="">Annuler</a>
					<input class="bouton" style="vertical-align:middle;justify-self: end;" type="submit" name="contactmail" onclick="return confirm('Etes vous sûre de vouloir envoyer ce message ?');" value="Envoyer" />
				</div>
		<?php
			} else { ?>
				<a class="close" href="">Annuler</a> <a href="" style="float:right" rel="popup_inscription" class="poplight bouton">Envoyer</a>
		<?php
			}
		?>
	</form>
</div>

	<!-- Modal Inscription si pas isncris lors essaie d'envoi un MP.  -->

<div id="popup_inscription" class="popup_block">
	<h2>Inscrivez-vous</h2><br />
	<p>Pour pouvoir envoyer un message à ce parrain, il est essentiel de s'inscrire, ce n'est qu'une formalité en remplissant ces champs : </p>

	<!-- FORMULAIRE d'inscription membre -->
	<h2> Inscription</h2>

	<form action="<?= ROOTPATH ?>/inscription" method="post" id="inscription" style="text-align:left;">
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


	<!--  SCRIPTS JS--> 
<script src="<?= ROOTPATH ?>/script/stars.js"></script>
<script>
//Ajax de pagination et affichage commentaires
function fetch_comments(id, page) 
{

$.ajax({
	type: "POST",
	url: "<?= ROOTPATH; ?>/membres/includes/avis-profil-ajax.php",
	data: { id : id,
			page: page
	},
	success: function (data) {
		$("#listcomment").html(data);
	}
});

}

//Script de changement de page lors du click
$(document).on("click", ".page-item2", function(){
$(this).removeClass('paginat').addClass('current');
var page = $(this).attr("id");
var id = <?= $row['membre_id'] ?>;
$('html, body').animate({
scrollTop: $("#comment").offset().top
}, 1000);
	fetch_comments(id, page);
});

fetch_comments(<?= $row['membre_id'] ?>)
//AJAX DE COMMENTAIRE : 

$('#comments').submit( function() {
$('.erreur').remove();
$('.add_comment').hide();
$("#reponseaddcom").append('<div class="spinner" style="text-align:center;margin:50px 0 50px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" /></div>');
donnees = $(this).serialize();
url = "<?= ROOTPATH ?>/membres/includes/comments.php"
$.ajax({
	type: "post",
	url: url,
	data: donnees,
	dataType: "JSON",
	success: function (response) {
		if(response.ok) {
			$('#listcomment').prepend(response.ok).delay(100);
			$('#inputadd_comment').val('');
			$("#reponseaddcom").empty();
		} else if(response.error) {
			$("#reponseaddcom").empty();
			$('#reponseaddcom').append(response.error);
			$('.add_comment').show();
			
		}
		
		
	}
});

return false;
});


function closeit(){
$('.erreur').remove();
}

// GESTION D'ENVOI DE HEADER ET BACKGROUND IMAGE :

$(function() {
var time = function() {
	return '?' + new Date().getTime()
};

// Avatar setup
$('#avatarModal').imgPicker({
	url: '<?= ROOTPATH ?>/includes/upload_avatar.php',
	aspectRatio: 1, // Crop aspect ratio
	// Delete callback
	deleteComplete: function() {
		$('.avatar').attr('src', '//gravatar.com/avatar/0?d=mm&s=150');
		this.modal('hide');
	},
	// Crop success callback
	cropSuccess: function(image) {
		console.log(image);
		$('.avatar').attr('src', image.versions.avatar.url + time());
		this.modal('hide');
	},
	// Send some custom data to server
	data: {
		key: 'value',
	}
});

// Header setup
$('#headerModal').imgPicker({
	url: '<?= ROOTPATH ?>/includes/upload_header.php',
	aspectRatio: 1300/300,
	deleteComplete: function() {
		$('.cover').css('background-image', 'url(//placehold.it/1000x250/701818/)');
		this.modal('hide');
	},
	cropSuccess: function(image) {
		$('.cover').css('background-image', 'url(' + image.versions.header.url + time() + ')');
		this.modal('hide');
	}
});

});



//  AJAX ENVOI DE MP :

$('#contactmail').submit(function() {
$("#result").append('<div style="text-align:center;margin:50px 0 50px;"><i class="fas fa-spinner fa-2x rotating"></i></div>');
$.ajax({
	type: "POST",
	cache: false,
	url: "../pages/include/contacter-annonceur-jqueryphp.php",
	data: $('form#contactmail').serialize(),
	dataType: 'json',
	success: function(msg) { // si l'appel a bien fonctionné
		if (msg.erreurs == 'no') { // si la connexion en php a fonctionnée
			$('#contactmail').hide();
			$('#result').empty().append('<div class="valider">Votre message a été envoyé au parrain.</div>');
		} else if (msg.erreurs == 'one') { // si la connexion en php a fonctionnée
			$('#result').empty().append('<div class="erreur">Il y a une erreur dans votre formulaire, merci de le corriger.</div>');
			if(msg.info == 'empty')
			$('#result').append('<div class="erreur">Tous les champs sont obligatoires</div>');
		}else if (msg.erreurs == 'plusieurs') {
			$('#result').empty().append('<div class="erreur">Il y a plusieurs erreurs dans votre formulaire de contact, merci de les corriger.</div>');
			if(msg.info == 'empty')
			$('#result').append('<div class="erreur">Tous les champs sont obligatoires</div>');
		}else if (msg.fvalidation == 'yes') {
			$('#contactmail').hide();
			$("#ca_pseudo, #ca_emailaddr, #ca_message, #ca_idm, #ca_dest, #ca_nom, #ca_emaildestinataire, #ca_marchand, #ca_validerform").prop('disabled', true); 
			$('#result').empty().append('<div class="valider">Message envoyé .</div>');
		}
		else
			$('#result').empty().append('<div class="erreur">Erreur inconnue</div>');

		// on affiche un message d'erreur dans le span prévu à cet effet
	},
	error: function (xhr, ajaxOptions, thrownError) {
		$('#result').empty().append(xhr.responseText);
	
	}   
});
return false;
// permet de rester sur la même page à la soumission du formulaire*/
});



//  FENETRE MODAL  :
$(document).ready(function() {
$('a.poplight').click(function() {
	var popID = $(this).attr('rel'); //Trouver la pop-up correspondante
	var popURL = $(this).attr('href'); //Retrouver la largeur dans le href

	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#' + popID).fadeIn().prepend('<a href="#" class="close" style="float:right"><img src="../images/close_pop.png" class="btn_close" title="Fermer" alt="Fermer" /></a>');

	//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;

	//On affecte le margin
	$('#' + popID).css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});

	//Effet fade-in du fond opaque
	$('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
	$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn();

	return false;
});

///Fermeture de la pop-up et du fond
jQuery('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		jQuery('#fade , .popup_block').fadeOut(function() {
			jQuery('#fade, a.close').remove();  
	}); //...ils disparaissent ensemble
		
		return false;
	});
});
		</script>


<?php }
}
require_once '../elements/footer.php'; ?>