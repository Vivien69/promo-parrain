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
$sql = "SELECT * FROM user WHERE membre_id = :id";
$prep = $pdo->prepare($sql);
$prep->execute(array(":id" => $id));

if ($prep->rowCount() == 0) {
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

		//TITRE DE LA PAGE et INCLUDES
		$title = 'Parrainages de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'parrainages';
		require_once 'includes/menu_membres.php';


		/*
		deleted_parrain ou deleted_filleul =
		0 = Actif
		1 = Supprimé 
		2 = Annulé
		3 = Archivé
		*/

//SUPPRESSION d'UNE OFFRE DE PARRAINAGE POUR LE PARRAIN (CODE COPIE PAR DES VISITEURS)
if (isset($_GET["copie"]) && $_GET['copie'] && isset($_GET["id"]) && is_numeric($_GET["id"])) {

	$id = (int) str_replace('/','',$_GET["id"]);

	$sql = $pdo->prepare('SELECT * FROM execparrainages WHERE id = :id  AND (id_parrain = '.$_SESSION['membre_id'].' OR id_filleul = '.$_SESSION['membre_id'].')');
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$sql->execute();
	$GLOBALS['nb_req']++;

	if ($sql->rowcount() == 1) {

		// Il y a bien 1 parrainage a supprimer, on vérifie le délai a respecter avant suppression
		$fetch = $sql->fetch();
		$delai_delete = time() - 7776000;
		$affdelete = ($delai_delete - $fetch['date'] > 0 ? true : false);

		//Délai non respecté alors on affiche une erreur et die()
		if(!$affdelete) {
			echo '<div class="erreur">Erreur !</div>';
			die();
		}

		//Si le script continue, on supprime pour le filleul ou le parrain
		
		$rowis = ($fetch['id_parrain'] == $_SESSION['membre_id'] ? 'parrain' : 'filleul');
	
		$sql = $pdo->prepare('UPDATE execparrainages SET deleted_'.$rowis.' = :deleted_'.$rowis.' WHERE id = :id AND id_'.$rowis.' = '.$_SESSION['membre_id']);
		$sql->bindValue(':deleted_'.$rowis, 1, PDO::PARAM_INT);
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$GLOBALS['nb_req']++;

		if ($sql->execute()) {
			echo '<div class="valider">Le parrainage a été supprimée.</div>';
		}
	} else {
		echo '<div class="erreur">Erreur !2</div>';
	}
	
}

//ANNULER UN PARRAINAGE PAR LE FILLEUL A L'ETAPE 0
if (isset($_GET["annule"]) && $_GET['annule']) {

	$id = (int) str_replace('/','',$_GET["id"]);

	$sql = $pdo->prepare('SELECT * FROM execparrainages WHERE id = :id  AND (id_filleul = '.$_SESSION['membre_id'].')');
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$sql->execute();
	$GLOBALS['nb_req']++;

	if ($sql->rowcount() == 1) {

		// Il y a bien 1 parrainage a annuler, on vérifie étape 0
		$fetch = $sql->fetch();

		if($fetch['id_filleul'] != $_SESSION['membre_id']) {
			echo '<div class="erreur">Erreur vous n\'êtes pas le filleul!</div>';
			die();
		} 
		if($fetch['statut_parrainage'] != 0) {
			echo '<div class="erreur">Erreur, interdiction d\'annuler un parrainage commencé</div>';
			die();
			}

		//Si le script continue, on annule pour le filleul ou le parrain
		
		$sql = $pdo->prepare('UPDATE execparrainages SET deleted_parrain = :deleted_parrain, deleted_filleul = :deleted_filleul WHERE id = :id AND id_filleul = '.$_SESSION['membre_id'].' AND statut_parrainage = 0');
		$sql->bindValue(':deleted_parrain', 2, PDO::PARAM_INT);
		$sql->bindValue(':deleted_filleul', 2, PDO::PARAM_INT);
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$GLOBALS['nb_req']++;
		sendmessage($fetch['id_parrain'], $fetch['id_filleul'], 'Le filleul à annulé le parrainage en cours');

		if ($sql->execute()) {
			echo '<div class="valider">Le parrainage a été annulé.</div>';
		}
	} else {
		echo '<div class="erreur">Erreur 2 !</div>';
	}

}

//ARCHIVER UN PARRAINAGE ANNULE
if (isset($_GET["archiv"]) && $_GET['archiv']) {
	
	$id = (int) $_GET['archiv'];

	$sql = $pdo->prepare('SELECT * FROM execparrainages WHERE id = :id  AND (id_parrain = '.$_SESSION['membre_id'].' OR id_filleul = '.$_SESSION['membre_id'].')');
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$sql->execute();
	$GLOBALS['nb_req']++;
	$fetch = $sql->fetch();
	
	($fetch['id_parrain'] == $_SESSION['membre_id'] ? $rowis = 'parrain' : $rowis = 'filleul');
	
	$sql = 'SELECT * FROM execparrainages WHERE id = :id  AND id_'.$rowis.' = '.$_SESSION['membre_id'].' AND deleted_'.$rowis.' = 2 AND statut_parrainage = 0';
	$sql = $pdo->prepare($sql);
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$sql->execute();
	$GLOBALS['nb_req']++;

	if ($sql->rowcount() == 1) {

		//On archive pour la personne concerné
		
		$sql = $pdo->prepare('UPDATE execparrainages SET deleted_'.$rowis.' = :deleted_'.$rowis.' WHERE id = :id AND id_'.$rowis.' = '.$_SESSION['membre_id']);
		$sql->bindValue(':deleted_'.$rowis, 3, PDO::PARAM_INT);
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$GLOBALS['nb_req']++;

		if ($sql->execute()) {
			echo '<div class="valider">Le parrainage a été archivé.</div>';
		}
	} else {
		echo '<div class="erreur">Erreur aucun parrainage trouvé !</div>';
	}

}


		//MISE A JOUR DU STATUT D'UNE OFFRE DE PARRAINAGE
		if (isset($_GET["id"]) && $_GET["id"] != "" && isset($_GET["etape"])) {

			
			
			
			$id = (int) str_replace('/','',$_GET["id"]);
			$etape = (int) str_replace('/','',$_GET["etape"]);
			$req = $pdo->prepare('SELECT * FROM execparrainages WHERE id = :id AND (id_parrain = '.$_SESSION['membre_id'].' OR id_filleul = '.$_SESSION['membre_id'].')') ;
			$req->bindParam('id', $id, PDO::PARAM_INT);
			$req->execute();
			$fet = $req->fetch();

			$wipf = ($_SESSION['membre_id'] == $fet['id_filleul'] ? 'id_filleul' : 'id_parrain');
			
			switch ($fet['statut_parrainage']) {
				case 0:
					if($fet['statut_parrainage'] == 0 && $etape == 0 && $_SESSION['membre_id'] == $fet['id_filleul']) {
						$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 1 WHERE id = :id');
						$req->bindValue('id', $id, PDO::PARAM_INT);
						if($req->execute())
							echo '<div class="valider">Vous venez de valider votre inscription</div>';
						}
						sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le filleul à valider son inscription');
				break;
				case 1:
					if($fet['statut_parrainage'] == 1 && $etape == 1 && $_SESSION['membre_id'] == $fet['id_parrain']) {
						if(!isset($fet['bonus']) && $fet['bonus'] == '') {
						$sql = $pdo->query('SELECT COUNT(*) as nb FROM comments WHERE id_receiver = '.$fet['id_parrain'].' AND id_sender = '.$fet['id_filleul']);
						$sql->execute();
						$rowit = $sql->fetch();
							if($rowit['nb'] >= 1) {
								$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 3 WHERE id = :id');
								$req->bindValue('id', $id, PDO::PARAM_INT);
								if($req->execute())
								echo '<div class="valider">Vous avez valider le parrainage</div>';
								echo '<div class="valider">Le filleul vous a déja évalué pour un autre parrainage, ainsi ce parrainage est finalisé. </div>';
								/*
								PARRAINAGE EFFECTUE MESSAGE + BADGES
								*/
								sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Félicitations, le parrain à valider le parrainage');
								checkHowManyEntry($_SESSION['membre_id'], 2, 'execparrainages', 'id_filleul', ' OR id_parrain = ');
								
								
							
							} else {
								$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 2 WHERE id = :id');
								$req->bindValue('id', $id, PDO::PARAM_INT);
									if($req->execute())
									echo '<div class="valider">Vous avez valider le parrainage.</div>';
									sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le parrain à valider le parrainage');
							}
						} else {
						$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 2 WHERE id = :id');
						$req->bindValue('id', $id, PDO::PARAM_INT);
							if($req->execute())
							echo '<div class="valider">Vous avez valider le parrainage.</div>';
							sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le parrain à valider le parrainage');
							}
						}
				break;
				case 2:
					if($fet['statut_parrainage'] == 2 && $etape == 2 && $_SESSION['membre_id'] == $fet['id_parrain']) {
						$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 3 WHERE id = :id');
						$req->bindValue('id', $id, PDO::PARAM_INT);
						if($req->execute())
						echo '<div class="valider">Vous avez confirmé le versement du bonus.</div>';
						sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le filleul à confirmé le versement du bonus');
						}
				break;
				case 3:
					if($fet['statut_parrainage'] == 3 && $etape == 3 && $_SESSION['membre_id'] == $fet['id_filleul']) {
						if(isset($fet['bonus']) && $fet['bonus'] != '') {
							$sql = $pdo->query('SELECT COUNT(*) as nb FROM comments WHERE id_receiver = '.$fet['id_parrain'].' AND id_sender = '.$fet['id_filleul']);
							$sql->execute();
							$rowit = $sql->fetch();
								if($rowit['nb'] >= 1) {
									$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 5 WHERE id = :id');
									$req->bindValue('id', $id, PDO::PARAM_INT);
									if($req->execute())
									echo '<div class="valider">Vous avez confirmé la réception du bonus.</div>';
									echo '<div class="valider">Le filleul vous a déjà évalué pour un autre parrainage, ainsi ce parrainage est finalisé. </div>';
									sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Félicitations, le filleul à confirmé la réception du bonus');
									checkHowManyEntry($_SESSION['membre_id'], 2, 'execparrainages', 'id_filleul', ' OR id_parrain = ');
								} else {
									$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 4 WHERE id = :id');
									$req->bindValue('id', $id, PDO::PARAM_INT);
										if($req->execute())
										echo '<div class="valider">Vous avez confirmé la réception du bonus.</div>';
										sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le filleul à confirmé le versement du bonus. Laissez vous un avis sur votre profil');
								}
						} else {
						$req = $pdo->prepare('UPDATE execparrainages SET statut_parrainage = 4 WHERE id = :id');
						$req->bindValue('id', $id, PDO::PARAM_INT);
							if($req->execute())
							echo '<div class="valider">Vous avez confirmé la réception du bonus.</div>';
							sendmessage($fet['id_parrain'], $fet['id_filleul'], 'Le filleul à confirmé le versement du bonus. Laissez vous un avis sur votre profil');
							}
						}
				break;
			}
		}

			if (isset($_GET["del"]) && $_GET["del"] == 1 && isset($_GET["id"])) {
			$id = (int) str_replace('/','',$_GET["id"]);
			$delai_delete = time() - 7776000;; 
			$sql = $pdo->prepare('SELECT * FROM execparrainages WHERE id = :id  AND (id_parrain = '.$_SESSION['membre_id'].' OR id_filleul = '.$_SESSION['membre_id'].')');
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
			if ($sql->rowcount() == 1) {
				$sql->execute();
				$fetch = $sql->fetch();
				$affdelete = ($delai_delete - $fetch['date'] > 0 ? true : false);

				if($affdelete) :

				$rowis = ($fetch['id_parrain'] = $_SESSION['membre_id'] ? 'parrain' : 'filleul');
				$sql = $pdo->prepare('UPDATE execparrainages SET deleted_'.$rowis.' = :deleted_'.$rowis.' WHERE id = :id AND id_'.$rowis = $_SESSION['membre_id']);
				$sql->bindParam(':deleted_'.$rowis, 1, PDO::PARAM_INT);
				$sql->bindParam(':id', $id, PDO::PARAM_INT);
				$GLOBALS['nb_req']++;
				if ($sql->execute()) :
					echo '<div class="valider">Le parrainage a été supprimée.</div>';
				endif;
				endif;
			} else {
				echo '<div class="erreur">Le parrainage n\'existe pas dans notre base de donnée !</div>';
			}
		
		}


// PAGINATION
$check_contents = $pdo->query('SELECT COUNT(*) as total FROM execparrainages WHERE (id_parrain = '.$_SESSION['membre_id'].' AND (deleted_parrain = 0 OR deleted_parrain = 2)) OR (id_filleul = '.$_SESSION['membre_id'].' AND (deleted_filleul = 0 OR deleted_filleul = 2))');
$GLOBALS['nb_req']++;
$get_total_pages = $check_contents->fetch(PDO::FETCH_ASSOC);
$get_total_pages = $get_total_pages['total'];
$page_limit = 10; //This is the number of contents to display on each page
$pagination_stages = 5;
if(isset($_GET['page']) && $_GET['page'] != "") {
$current_page = (int) strip_tags(str_replace('/','',$_GET['page']));
} else {
$_GET['page'] = 1;
$current_page = 1;
}
$start_page = ($current_page - 1) * $page_limit;


//This initializes the page setup
if($current_page == 0) { $current_page = 1; }
$previous_page = $current_page - 1;	
$next_page = $current_page + 1;							
$last_page = ceil($get_total_pages/$page_limit);		
$lastpaged = $last_page - 1;					
$pagination_system = '';
if($last_page > 1)
{	
$pagination_system .= "<div class='pagination_system'>";
// Previous Page
if ($current_page > 1) { $page_counterprec = $current_page - 1; $pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_counterprec'>< Précedent</a>"; }
else { $pagination_system.= ""; }
// Pages	
if ($last_page < 7 + ($pagination_stages * 2))	// Not enough pages to breaking it up
{	
for ($page_counter = 1; $page_counter <= $last_page; $page_counter++)
{
if ($page_counter == $current_page) {
$pagination_system.= "<span class='current'>$page_counter</span>";
}
else {
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_counter'>$page_counter</a>";
}					
}
}
elseif($last_page > 5 + ($pagination_stages * 2))	// This hides few pages when the displayed pages are much
{
//Beginning only hide later pages
if($current_page < 1 + ($pagination_stages * 2))		
{
for ($page_counter = 1; $page_counter < 4 + ($pagination_stages * 2); $page_counter++)
{
if ($page_counter == $current_page) {
$pagination_system.= "<span class='current'>$page_counter</span>";
}
else {
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_counter'>$page_counter</a>";
}					
}
$pagination_system.= "...";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$lastpaged'>$lastpaged</a>";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$last_page'>$last_page</a>";		
}
//Middle hide some front and some back
elseif($last_page - ($pagination_stages * 2) > $current_page && $current_page > ($pagination_stages * 2))
{
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/1'>1</a>";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/2'>2</a>";
$pagination_system.= "...";
for ($page_counter = $current_page - $pagination_stages; $page_counter <= $current_page + $pagination_stages; $page_counter++)
{
if ($page_counter == $current_page) {
$pagination_system.= "<span class='current'>$page_counter</span>";
}
else {
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_counter'>$page_counter</a>";
}					
}
$pagination_system.= "...";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$lastpaged'>$lastpaged</a>";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$last_page'>$last_page</a>";		
}
//End only hide early pages
else
{
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/1'>1</a>";
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/2'>2</a>";
$pagination_system.= "...";
for ($page_counter = $last_page - (2 + ($pagination_stages * 2)); $page_counter <= $last_page; $page_counter++)
{
if ($page_counter == $current_page) {
$pagination_system.= "<span class='current'>$page_counter</span>";
}
else {
$pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_counter'>$page_counter</a>";
}					
}
}
}		
//Next Page
if ($current_page < $page_counter - 1) { $page_countersuiv = $current_page +1; $pagination_system.= "<a class='paginat' href='".ROOTPATH."/membres/parrainages/$page_countersuiv'>Suivant ></a>"; }
else { $pagination_system.= ""; }
$pagination_system.= "</div><div class='current' style='float:right;margin:-60px 10px 0 0px;'>Page : <b>$current_page</b> sur $last_page</div>";			
}
$sql= 'SELECT  EP.*, EP.id as idnotif, EP.date as date_parrainage, AP.id, AP.idmarchand, EP.bonus, M.montantremise, M.nom_marchand, M.img, U1.membre_id as idparrain, U2.membre_id as idfilleul, U1.membre_utilisateur as nameparrain, U2.membre_utilisateur as namefilleul, IM1.image as imgparrain, IM1.type, IM2.image as imgfilleul, IM2.type, M.cat
FROM execparrainages EP
LEFT JOIN annonces_parrainage AP ON AP.id = EP.id_annonce
LEFT JOIN marchands M ON AP.idmarchand = M.id
LEFT JOIN user U1 ON EP.id_parrain = U1.membre_id
LEFT JOIN user U2 ON EP.id_filleul = U2.membre_id
LEFT JOIN images IM1 ON IM1.id_membre = EP.id_parrain AND IM1.type = "avatar"
LEFT JOIN images IM2 ON IM2.id_membre = EP.id_filleul AND IM2.type = "avatar"
WHERE (EP.id_filleul = '.$_SESSION['membre_id'].' AND (deleted_filleul = 0 OR deleted_filleul = 2)) OR (EP.id_parrain = '.$_SESSION['membre_id'].' AND (deleted_parrain = 0 OR deleted_parrain = 2))
ORDER BY date DESC
LIMIT '.$start_page.', '.$page_limit.'';
$GLOBALS['nb_req']++;
$prep = $pdo->prepare($sql);
$prep->execute();
?>
<style>
.without:after {
	content:none;
}
</style>
<?= isset($message) ? $message : ''; ?>


<div class="block_inside">
	
			<h1>Mes Parrainages</h1>
<p style="text-align:left;">
Vous avez actuellement <b><?= nombre_notifications($_SESSION['membre_id'], 'pasphrase') ?> parrainages</b><br />
Vous pourrez supprimer vos notifications après un délai de 90 jours.<br />
L'évaluation est disponible lorsqu'un parrainage s'est correctement déroulé ou après 30 jours. <br />
<a href="<?= ROOTPATH ?>/membres/parrainages_archives">Voir la liste des parrainages archivés</a>
</p><br />

 <?php

$nombre = $prep->rowcount();
	if ($nombre > 0) {
		$results = $prep->fetchALL(PDO::FETCH_ASSOC);

		//On numérote les lignes du tableau

		for($i = 1; $i < $nombre + 1; $i++) {
			$results[$i - 1]['nombre'] = $i;
		}
		
		
	//On peut supprime le parrainage après un délai de 3 mois
	foreach($results as $row) {
		
		$rowis = ($row['id_parrain'] == $_SESSION['membre_id'] ? 'parrain' : 'filleul');
		$delai_delete = time() - 7776000; //90 jours
		$affdelete = ($delai_delete - $row['date_parrainage'] > 0 ? true : false); 
		//echo pr($results);

		//On vérifie la présence d'un bous versé par le parrain pour paufiner l'affichage
		$bonus = (isset($row['bonus']) && $row['bonus'] != "" ? true : false);

		if($row['notif'] == 1) { ?>
		<div class="parrainage_codecopie">
			<div id="crumbs" style="flex: 1 1 0;"> 
				<span style="flex:0 2 0;" id="<?= $row['idnotif'] ?>"><?= $row['nombre'] ?></span>
				<ul style="justify-content:flex-start">
					<li <?= ($row['statut_parrainage'] >= 0 && $row['statut_parrainage'] <= 5) ? 'class="active colicopi"' : '' ?>><?= ($row['action'] == 1 ? '<i class="fas fa-link fa-lg" style="margin-right:5px;"></i> Lien utilisé' : '<i class="fas fa-tag fa-lg" style="margin-right:5px;"></i> Code copié'); ?></li>
				</ul>
	
				
				<div class="date"  style="flex: 2 1 0;justify-content:center;">
					<?= ucfirst(mepd($row['date_parrainage'])) ?> un visiteur à <?= ($row['action'] == 1 ? '<strong style="color:green">utiliser votre lien</strong>' : '<strong style="color:purple">copié votre code</strong>'); ?>
				</div>
				<div class="marchand" style="justify-self: center;flex: 1 1 0;">
					<a href="<?= ROOTPATH.'/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '"><img class="item-img" style="height:auto;width:auto;margin:0;" src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'" /></a>'; ?>
				</div>
				<?= $affdelete ? '<a href="'.ROOTPATH.'/membres/parrainages?copie=1&id='.$row['idnotif'].'" onclick="if(!confirm(\'Etes vous sur de supprimer ce parrainage ?\')) return false;" title="Supprimer le parrainage" style="text-align:right;margin-right:5px;"><i class="fas fa-times fa-lg"></i></a>' : '' ?>
	
			</div>
		</div>
			
			<hr><br /> 
			
			<?php
		} else {
		if($row['id_parrain'] == $_SESSION['membre_id']) {

			$statut = [
				0 => 'En attente de la validation de l\'inscription du filleul',
				1 => 'Le filleul à confirmé son inscription, en attente de la validation du parrainage par '.$row['nom_marchand'].' lorsque vous aurez reçu votre prime',
				2 => 'Bravo le parrainage a été effectué. '.($bonus ? '<br />Vous devez verser le bonus de <span style="color:#701818;font-size:15px;">'.$row['bonus'].' €</span> à '.$row['namefilleul'] : '').'',
				3 => 'Vous avez versé le bonus a votre filleul. Patienter la confirmation de sa réception.',
				4 => 'En attente de l\'avis du filleul <br /> '.(isset($row['id_avis_parrain']) ? 'Vous pouvez déposer un avis au filleul' : ''),
				5 => 'Bravo le parrainage a été un succès. '.($row['id_avis_parrain'] == null ? '<br />N\'oubliez pas de remercier votre filleul en lui laissant un <a href="'.ROOTPATH.'/profil/'.$row['idfilleul'].'#comment"">avis</a>' : '')
			];
			($bonus == false ? array_splice($statut,2,-2) : '');
			$bouton = [
				0 => '<p style="display:flex;align-items:center;justify-content:space-around;"><i class="fas bred fa-hourglass-start fa-2x"></i> En attente du filleul</p>',
				1 => '<a href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&id='.$row['idnotif'].'&etape='.$row['statut_parrainage'].'" onclick="if(!confirm(\'Vous confirmez avoir reçu votre prime de parrainage par suite à l\\\'inscription de '.$row['namefilleul'].'  sur '.$row['nom_marchand'].' ?\')) return false;" class="button">Valider le parrainage</a>',
				2 => '<a href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&id='.$row['idnotif'].'&etape='.$row['statut_parrainage'].'" onclick="if(!confirm(\'Vous confirmez avoir versé le bonus de '.$row['bonus'].' € à '.$row['namefilleul'].' par Paypal ou virement ? \')) return false;" class="button">Confirmer le versement du bonus</a>',
				3 => '<p style="display:flex;align-items:center;justify-content:space-around;"><i class="fas bred fa-hourglass-start fa-2x"></i> En attente du filleul</p>',
				4 => '<a href="'.ROOTPATH.'/profil/'.$row['idfilleul'].'#comment" class="button">Déposer un avis</a>',
				5 => '<div class="valider" style="display:flex;align-items:center;justify-content:space-around;"><i class="fas fa-check-circle fa-2x"></i> Parrainage réussi</div>'
			];
			($bonus == false ? array_splice($bouton,2,-2) : '');
		} else {
			$statut = [
				0 => 'Inscrivez vous sur '.$row['nom_marchand'].' et validez votre inscription. ',
				1 => 'En attente de la validation du parrainage par '.$row['nom_marchand'],
				2 => 'Bravo le parrainage a été effectué.',
				3 => 'Confirmez la réception du bonus',
				4 => 'Laisser un avis au parrain pour finaliser le parrainage',
				5 => 'Bravo le parrainage a été un succès. '
			];
			($bonus == false ? array_splice($statut,2,-2) : '');
			$bouton = [
				0 => '<a href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&id='.$row['idnotif'].'&etape='.$row['statut_parrainage'].'" onclick="if(!confirm(\'Votre inscription sur '.$row['nom_marchand'].' a bien été effectué grâce au code ou au lien de parrainage de '.$row['nameparrain'].' ? \')) return false;" class="button">Valider mon inscription</a>',
				1 => '<p style="display:flex;align-items:center;justify-content:space-around;"><i class="fas bred fa-hourglass-start fa-2x"></i> En attente du marchand</p>',
				2 => '<p style="display:flex;align-items:center;justify-content:space-around;"><i class="fas bred fa-hourglass-start fa-2x"></i> En attente du parrain</p>',
				3 => '<a href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&id='.$row['idnotif'].'&etape='.$row['statut_parrainage'].'" onclick="if(!confirm(\'Vous confirmez que '.$row['nameparrain'].' vous à versé '.$row['montantremise'].'€ par paypal ou virement ? \')) return false;" class="button">Confirmer la réception du bonus</a>',
				4 => '<a href="'.ROOTPATH.'/profil/'.$row['idparrain'].'#comment" class="button">Déposer un avis</a>',
				5 => '<div class="valider" style="display:flex;align-items:center;justify-content:space-around;"><i class="fas fa-check-circle fa-2x"></i> Parrainage réussi</div>'
			];
			($bonus == false ? array_splice($bouton,2,-2) : '');
		}
?>
	
	<div class="parrainage_en_cours <?= (($row['id_'.$rowis] === $_SESSION['membre_id']) && $row['deleted_'.$rowis] == 2 ? 'desactivedHover' : '') ?>">
	
	<?= $affdelete ? '<a href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'copie=1&id='.$row['idnotif'].'" onclick="if(!confirm(\'Etes vous sur de supprimer ce parrainage ?\')) return false;" title="Supprimer le parrainage" style="position:absolute;top:15px;right:15px;"><i class="fas fa-times fa-lg"></i></a>' : '<div></div>' ?>
		<div id="crumbs"> 
			<span id="<?= $row['idnotif'] ?>"><?= $row['nombre'] ?></span>
			<ul>
				<li <?= ($row['statut_parrainage'] >= 0 && $row['statut_parrainage'] <= 5) ? 'class="active"' : '' ?>>Code copié</li>
				<li <?= ($row['statut_parrainage'] >= 1 && $row['statut_parrainage'] <= 5) ? 'class="active"' : '' ?>>Filleul inscrit</li>
				<li <?= ($row['statut_parrainage'] >= 2 && $row['statut_parrainage'] <= 5) ? 'class="active"' : '' ?>>Parrainage confirmé</li>
				<?= ($bonus ? '<li '.(($row['statut_parrainage'] >= 3 && $row['statut_parrainage'] <= 5) ? 'class="active"' : '').'>Bonus versé</li>' : ''); ?>
				<?= ($bonus ? '<li '.((($row['statut_parrainage'] >= 5 && $row['statut_parrainage'] <= 5)) ? 'class="active"' : '').'>Avis publié</li>' : '<li '.((($row['statut_parrainage'] >= 3 && $row['statut_parrainage'] <= 3)) ? 'class="active"' : '').'>Avis publié</li>'); ?>
			</ul>
		</div>
		
		<div class="date">
			<p><?= mepd($row['date_parrainage']) ?></p>
			<?= ($bonus ? '<p>Montant du bonus : <b>'.$row['bonus'].' €</b></p>' : '') ?>
		</div>
		
		<div class="marchand" style="justify-self: center;">
			<a href="<?= ROOTPATH.'/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '"><img class="item-img" style="height:auto;width:auto;margin:0;" src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'" /></a>'; ?>
		</div>
		<div class="parrain">
			<p><?= '<a href="' . ROOTPATH . '/profil/'.$row['idparrain'].'">'.$row['nameparrain'].'</a>' ?></p>
			<?= '<a href="' . ROOTPATH . '/profil/'.$row['idparrain'].'"><img alt="Code promo de Parrainage de '.$row['namefilleul'] . '" style="vertical-align:middle;justify-self: end;width:60px;height:60px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['imgparrain']) ? $row['imgparrain'] : '/default_avatar.png').'" class="avatar" />'.(strpos($row['id_filleul'], 'Filleul') === 0 ? $row['id_filleul'] : '').'</a>'; ?>
		</div>
		<div class="exchange">
			<p><i class="fas fa-exchange-alt fa-2x"></i></p>
		</div>
		<div class="filleul">
			<p><?= '<a href="' . ROOTPATH . '/profil/'.$row['idfilleul'].'">'.$row['namefilleul'].'</a>' ?></p>
			<?= '<a href="' . ROOTPATH . '/profil/'.$row['idfilleul'].'"><img alt="Code promo de Parrainage de '.$row['nameparrain'] . '" style="vertical-align:middle;justify-self: end;width:60px;height:60px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['imgfilleul']) ? $row['imgfilleul'] : '/default_avatar.png').'" class="avatar" />'.(strpos($row['id_filleul'], 'Filleul') === 0 ? $row['id_filleul'] : '').'</a>'; ?>
			
		</div>
		
		<div class="statut">
			<p style="font-weight:bold"><?= $statut[$row['statut_parrainage']] ?></p>
		</div>
		<?php
		if(($row['id_'.$rowis] == $_SESSION['membre_id'] && $row['deleted_'.$rowis] != 2)) : ?>
			<div class="action">
				<?= $bouton[$row['statut_parrainage']] ?>
				<?= ($row['statut_parrainage'] == 0 && $row['id_filleul'] == $_SESSION['membre_id'] ? '<a title="Annuler le parrainage" onclick="if(!confirm(\'Etes vous sur de vouloir annuler ce parrainage ?\')) return false;" href="'.ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&annule=1&id='.$row['idnotif'].'" style="background:#701818;margin:5px" class="button">Annuler</a>' : ''); ?>
			</div>
			<?php else : ?>
				<div class="action">
					<p class="bred" style="display:flex;align-items:center;justify-content:space-around;font-weight:bold"><i class="fas bred fa-stop-circle fa-2x"></i>PARRAINAGE ANNULE PAR LE FILLEUL</p>
					<a href="<?= ROOTPATH.'/membres/parrainages?page='.$_GET['page'].'&archiv='.$row['idnotif'] ?>" onclick="if(!confirm(\'Vous confirmez que <?= $row['nameparrain'] ?> vous à versé  <?= $row['montantremise'] ?>€ par paypal ou virement ? \')) return false;" class="button">Archiver ce parrainage</a>
				</div>

			<?php endif; ?>
		
		
		</div>
		<hr><br />
	
<?php
}
}
} else {
	echo '<br /><div class="box_annonces"><p>Vous n\'avez actuellement aucun parrainage.</p></div>';
}

	echo $pagination_system;
	?>

</div>

<?php }
}
require_once '../elements/footer.php'; ?>