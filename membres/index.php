<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */

if (isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
} else {
	require_once '../elements/header2.php';
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Vous n\'&ecirc;tes pas connect&eacute;',
		'Impossible d\'accéder à cette page membre.',
		' - <a href="' . ROOTPATH . '/connexion">Se connecter</a>',
		ROOTPATH,
		20
	);
	require_once('../information.php');
	exit();
}
$title = "Tableau de bord :: Promo-Parrainage";
$sql = "SELECT * FROM user WHERE membre_id=" . $id;
$prep = $pdo->prepare($sql);
$prep->execute();

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
		$titre = 'Annonces de parrainage de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';
		require_once '../elements/header2.php';
		$current = 'index';
		require_once 'includes/menu_membres.php';
?>


		<section class="block_inside">
		
			<h1>Tableau de bord de <?= $row['membre_utilisateur']; ?></h2><br />
			<p style="text-align:left;background-color:#F8F8F8;padding:20px 20px 0"><b>Mise à jour du 17/10/2021 :</b><br />
			- Possiblité pour le filleul d'annuler un parrainage lancé si l'inscription sur le site marchand ne se fait pas.<br />
			- Possibilité pour parrain et filleul d'archiver un parrainage annulé! Cela pour mieux trier sa page de parrainages en cours. <br />
			- Message lors du changement du statut d'un parrainage. 
	</p>


				<div id="aff_profil1">
					<div class="aff_annoncespostees">
						<?php echo nombreannonces($row['membre_id']); ?>
					</div>
					<div class="aff_droiteannposte">
						<p style="font-size:17px;font-weight:bold;margin-bottom:10px;">Offre de parrainage publiée</p>
						<i class="fa fa-chevron-circle-right fa-lg" style="margin-left:10px;margin-bottom:10px;"></i> <a href="../parrain/ajouter" title="">Ajouter une annonce</a><br />
						<i class="fa fa-chevron-circle-right fa-lg" style="margin-left:10px;"></i> <a href="annonces" title="">Voir mes annonces</a>
					</div>
				</div><!--
				<div id="aff_profil1" class="display:inline-block;width:450px;">
					<div id="aff_annonces_nombre">
						<div class="aff_annoncespostees">
							<?php //echo nombrefavoris($row['membre_id']); 
							?>
						</div>
						<div class="aff_droiteannposte">
							<p style="font-size:17px;font-weight:bold;margin-bottom:10px;">Avis</p>
							<p><i class="fa fa-chevron-circle-right fa-lg" style="margin-left:10px;"></i> <a href="<?php echo ROOTPATH . '/'; ?>" title="Gérer mes avis">Gérer vos avis</a></p>
						</div>
					</div>
				</div>-->
				<div id="aff_profil1" class="display:inline-block;width:450px;">
					<div id="aff_annonces_nombre">
						<div class="aff_annoncespostees">
							<?= nombre_notifications($row['membre_id'], 'chiffre') ?>
						</div>
						<div class="aff_droiteannposte">
							<p style="font-size:17px;font-weight:bold;margin-bottom:10px;">Parrainages</p>
							<p><i class="fa fa-chevron-circle-right fa-lg" style="margin-left:10px;"></i> <a href="<?= ROOTPATH . '/membres/parrainages'; ?>" title="Mes parrainages">Voir mes parrainages</a></p>
						</div>
					</div>
				</div>
				<!-- MILIEU DES 2 LIGNE de ROND--><br /><br /><br />
				<div id="aff_profil1" class="display:inline-block;width:450px;">
					<div id="aff_annonces_nombre">
					
						<div class="aff_annoncespostees">
						<?= nombremessagenonlus(); ?>
						</div>
						<div class="aff_droiteannposte">
							<p style="font-size:17px;font-weight:bold;margin-bottom:10px;">Messages non lus</p>
							<p><i class="fa fa-chevron-circle-right fa-lg" style="margin-left:20px;"></i> <a href="<?= ROOTPATH . '/membres/messagerie'; ?>" title="Voir mes messages">Voir ma messagerie</a></p>
						</div>
					</div>
				</div>
				<div id="aff_profil1" class="display:inline-block;width:450px;">
					<div id="aff_annonces_nombre">
						<div class="aff_annoncespostees">
							
						</div>
						<div class="aff_droiteannposte">
							<p style="font-size:17px;font-weight:bold;margin-bottom:10px;margin-left:30px;">Badges</p>
							<p><i class="fa fa-chevron-circle-right fa-lg" style="margin-left:20px;"></i> <a href="<?= ROOTPATH . '/membres/badges'; ?>" title="Gérer mes baddes">Voir mes badges</a></p>
						</div>
					</div>
				</div>
	
		</section>

		<div id="popuptelephone" class="popup_block">
		</div>

<?php  }
}
require_once '../elements/footer.php'; ?>