<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Editer un marchand';
require_once '../../elements/header2.php';

	$idm = (int) $_GET['idm'];
	$sql = "SELECT *, M.id as id, HM.id as idhm FROM marchands M
	LEFT JOIN histo_marchands HM ON M.id = HM.id_marchand WHERE HM.id=(SELECT max(id) FROM histo_marchands);
	WHERE M.id = :idmarchand";
	$sql = $pdo->prepare($sql);
	$sql->bindParam(':idmarchand', $idm, PDO::PARAM_INT);
	$sql->execute();
    $fetch = $sql->fetch(); 
	//pr($fetch);
	

	 //AJOUT D'UN MARCHAND
	 if(isset($_POST['form_ajouter'])) {
		 $erreurs = 0;
		//GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
			if(isset($_POST['nom'])) {
				$donnee = trim($_POST['nom']);
				$result = checkobligatoire($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['nom'] = '';
					$_SESSION['form']['nom'] = $donnee;
				} else if ($result == 'empty') {
					$_SESSION['info']['nom'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de nom.</div>';
					$_SESSION['form']['nom'] = '';
					$erreurs++;
				}
			}

			if(isset($_POST['url'])) {
				$donnee  = trim($_POST['url']);
				$result = checkobligatoire($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['url'] = '';
					$_SESSION['form']['url'] = $donnee;
				} elseif ($result == 'empty') {
					$_SESSION['info']['url'] = '<div class="erreurform">Vous n\'avez pas ajouter d\'url</div>';
					$_SESSION['form']['url'] = '';
					$erreurs++;
				}
			}

			if(isset($_POST['uploadfile'])) {
				$donnee  = trim($_POST['uploadfile']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['uploadfile'] = '';
					$_SESSION['form']['uploadfile'] = $donnee;
				} else {
					$_SESSION['info']['uploadfile'] = '<div class="erreur">Vous n\'avez pas ajouter de logo</div>';
					$_SESSION['form']['uploadfile'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['adr-img'])) {
				$donnee  = trim($_POST['adr-img']);
				$result =  checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['adr-img'] = '';
					$_SESSION['form']['adr-img'] = $_POST['adr-img'];
				} else {
					$_SESSION['info']['adr-img'] = '<div class="erreur" >L\'image du marchand est obligatoire</div>';
					$_SESSION['form']['adr-img'] = '';
					$erreurs++;
				}
			}
		

			//TABLEAUX CATEGORIES ET OFFRES

			if (isset($_POST['categories']) && $_POST['categories'] != "") {
				$categories = $_POST['categories'];
				$_SESSION['form']['categories'] = $categories;
			}
			if (isset($_POST['offres']) && $_POST['offres'] != "") {
				$offres = $_POST['offres'];
	
				$offressql = implode(',', $offres);
				$_SESSION['form']['offres'] = $offressql;
				$_SESSION['form']['tableauoffres'] = $offres;
			}

			 //CHOIX OFFRE PROPOSEE
			 if(isset($_POST['choice'])) {
				$donnee = trim($_POST['choice']);
				$result = checkobligatoire($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['choice'] = '';
					$_SESSION['form']['choice'] = $donnee;
				} elseif ($result == 'empty') {
					$_SESSION['info']['choice'] = '<div class="erreurform">Selectionnez une offre dans la liste</div>';
					$_SESSION['form']['choice'] = '';
					$erreurs++;
				}
			}
		
			if(isset($_POST['remise'])) {
				$donnee = trim($_POST['remise']);
				$result = checkobligatoire($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['remise'] = '';
					$_SESSION['form']['remise'] = $donnee;
				} elseif ($result == 'empty') {
					$_SESSION['info']['remise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
					$_SESSION['form']['remise'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['devise'])) {
				$donnee = trim($_POST['devise']);
				$result = checkobligatoire($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['devise'] = '';
					$_SESSION['form']['devise'] = htmlspecialchars($donnee);
				} elseif ($result == 'empty') {
					$_SESSION['info']['devise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
					$_SESSION['form']['devise'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['achatminimal'])) {
				$donnee = trim($_POST['achatminimal']);
				$result = checkisnumerique($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['achatminimal'] = '';
					$_SESSION['form']['achatminimal'] = $donnee;
				} elseif ($result == 'non') {
					$_SESSION['info']['achatminimal'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
					$_SESSION['form']['achatminimal'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['description'])) {
				$donnee = trim($_POST['description']);
				$result = check30carac($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['description'] = '';
					$_SESSION['form']['description'] = nl2br(strip_tags($donnee, '<strong></strong><h5></h5><br /><b></b>'));
				} elseif ($result == 'tooshort') {
					$_SESSION['info']['description'] = '<div class="erreurform">La description doit faire 30 caractères minimum</div>';
					$_SESSION['form']['description'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['foncparrainage'])) {
				$donnee = trim($_POST['foncparrainage']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['foncparrainage'] = '';
					$_SESSION['form']['foncparrainage'] = nl2br(strip_tags($donnee, '<strong></strong><h5></h5><br /><b></b>'));
				} elseif ($result == 'tooshort') {
					$_SESSION['info']['foncparrainage'] = '<div class="erreurform">Le fonctionnement du Parrainage doit faire 30 caractères minimum</div>';
					$_SESSION['form']['foncparrainage'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['offre-parrain']) && $_POST['offre-parrain'] != "") {
				$donnee = trim($_POST['offre-parrain']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['offre-parrain'] = '';
					$_SESSION['form']['offre-parrain'] = nl2br(strip_tags($donnee, '<strong></strong><h5></h5><br /><b></b>'));
				} else {
					$_SESSION['info']['offre-parrain'] = '<div class="erreurform">Erreur offre parrain</div>';
					$_SESSION['form']['offre-parrain'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['offre-filleul']) && $_POST['offre-filleul'] != "") {
				$donnee = trim($_POST['offre-filleul']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['offre-filleul'] = '';
					$_SESSION['form']['offre-filleul'] = nl2br(strip_tags($donnee, '<strong></strong><h5></h5><br /><b></b>'));
				} else {
					$_SESSION['info']['offre-filleul'] = '<div class="erreurform">Erreur offre filleul</div>';
					$_SESSION['form']['offre-filleul'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['montantfilleul']) && $_POST['montantfilleul'] != "") {
				$donnee = trim($_POST['montantfilleul']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['montantfilleul'] = '';
					$_SESSION['form']['montantfilleul'] = $donnee;
				} else {
					$_SESSION['info']['montantfilleul'] = '<div class="erreurform">Erreur montant filleul</div>';
					$_SESSION['form']['montantfilleul'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['montantparrain']) && $_POST['montantparrain'] != "") {
				$donnee = trim($_POST['montantparrain']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['montantparrain'] = '';
					$_SESSION['form']['montantparrain'] = $donnee;
				} else {
					$_SESSION['info']['montantparrain'] = '<div class="erreurform">Erreur montant parrain</div>';
					$_SESSION['form']['montantparrain'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['date_debut']) && $_POST['date_debut'] != "") {
				$donnee = trim($_POST['date_debut']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['date_debut'] = '';
					$_SESSION['form']['date_debut'] = $donnee;
				} else {
					$_SESSION['info']['date_debut'] = '<div class="erreurform">Erreur date_debut</div>';
					$_SESSION['form']['date_debut'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['date_fin']) && $_POST['date_fin'] != "") {
				$donnee = trim($_POST['date_fin']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['date_fin'] = '';
					$_SESSION['form']['date_fin'] = $donnee;
				} else {
					$_SESSION['info']['date_fin'] = '<div class="erreurform">Erreur date fin</div>';
					$_SESSION['form']['date_fin'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['boosted']) && $_POST['boosted'] != "") {
				$donnee = trim($_POST['boosted']);
				$result = checkvide($donnee);
				if ($result == 'ok') {
					$_SESSION['info']['boosted'] = '';
					$_SESSION['form']['boosted'] = $donnee;
				} else {
					$_SESSION['info']['boosted'] = '<div class="erreurform">Erreur boosted</div>';
					$_SESSION['form']['boosted'] = '';
					$erreurs++;
				}
			}

			if($erreurs == 0){
				
				
				$sql = "UPDATE marchands SET nom_marchand = :nom_marchand, url_marchand = :url_marchand, img = :img, cat = :cat, offres = :offres, choixoffre = :choixoffre, 
				montantremise = :montantremise, montantdevise = :montantdevise, montantachatminimal = :montantachatminimal, description = :description, foncparrainage = :foncparrainage, 
				offreparrain = :offreparrain, offrefilleul = :offrefilleul, etat = :etat
				WHERE id = :id";
				$sqlbind = $pdo->prepare($sql);
				$sqlbind->bindParam(':nom_marchand', $_SESSION['form']['nom'], PDO::PARAM_STR);
				$sqlbind->bindParam(':url_marchand', $_SESSION['form']['url'], PDO::PARAM_STR);
				$sqlbind->bindParam(':img', $_SESSION['form']['adr-img'], PDO::PARAM_STR);
				$sqlbind->bindParam(':cat', $_SESSION['form']['categories'], PDO::PARAM_INT);
				$sqlbind->bindParam(':offres', $_SESSION['form']['offres'], PDO::PARAM_STR);
				$sqlbind->bindParam(':choixoffre',$_SESSION['form']['choice'], PDO::PARAM_INT);
				$sqlbind->bindParam(':montantremise',$_SESSION['form']['remise'], PDO::PARAM_INT);
       			$sqlbind->bindParam(':montantdevise',$_SESSION['form']['devise'], PDO::PARAM_STR);
        		$sqlbind->bindParam(':montantachatminimal',$_SESSION['form']['achatminimal'], PDO::PARAM_INT);
				$sqlbind->bindParam(':description', $_SESSION['form']['description'], PDO::PARAM_STR);
				$sqlbind->bindParam(':foncparrainage', $_SESSION['form']['foncparrainage'], PDO::PARAM_STR);
				$sqlbind->bindParam(':offreparrain', $_SESSION['form']['offre-parrain'], PDO::PARAM_STR);
				$sqlbind->bindParam(':offrefilleul', $_SESSION['form']['offre-filleul'], PDO::PARAM_STR);
				$sqlbind->bindValue(':etat', 0, PDO::PARAM_INT);
				$sqlbind->bindParam(':id', $idm, PDO::PARAM_INT);

				if(isset($_SESSION['form']['montantfilleul']) && isset($_SESSION['form']['montantparrain']) && isset($_SESSION['form']['date_debut'])) {
				if($_SESSION['form']['montantfilleul'] != $fetch['montantfilleul'] OR $_SESSION['form']['montantparrain'] != $fetch['montantparrain'] OR $_SESSION['form']['date_debut'] != $fetch['date_debut'] OR $_SESSION['form']['date_fin'] != $fetch['date_fin']) 
				{
					$sql2 = "INSERT INTO histo_marchands (id_marchand, montantfilleul, montantparrain, boosted, date_debut, date_fin) VALUES (:id_marchand, :montantfilleul, :montantparrain, :boosted, :date_debut, :date_fin)";
					$sql2 = $pdo->prepare($sql2);
					$sql2->bindParam(':id_marchand', $idm, PDO::PARAM_INT);
					$sql2->bindParam(':montantfilleul', $_SESSION['form']['montantfilleul'], PDO::PARAM_INT);
					$sql2->bindParam(':montantparrain', $_SESSION['form']['montantparrain'] , PDO::PARAM_INT);
					$sql2->bindParam(':boosted', $_SESSION['form']['boosted'] , PDO::PARAM_INT);
					$sql2->bindParam(':date_debut', $_SESSION['form']['date_debut'], PDO::PARAM_STR);
					$sql2->bindParam(':date_fin', $_SESSION['form']['date_fin'], PDO::PARAM_STR);
					$sql2->execute();
				}
			}
				

				if ($sqlbind->execute()) {
					echo '<div class="valider">Le marchand <b>'.$_SESSION['form']['nom'].'</b> a été modifié.</div>';
					$GLOBALS['nb_req']++;
					unset ($_SESSION['info'],$erreurs, $_SESSION['form'],$_SESSION['nb_erreurs']);
				}
			} elseif ($erreurs > 0) {
				if ($erreurs == 1)
					$_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
				else
					$_SESSION['nb_erreurs'] = '<div class="erreur">Il y a ' . $erreurs . ' erreurs dans le formulaire, merci de les corriger !</div>';
			}		
	}

    ?>
<section class="block_inside">
	<?= (isset($_SESSION['nb_erreurs']) && $_SESSION['nb_erreurs'] != "") ? $_SESSION['nb_erreurs'] : ''; ?>


	<form action="<?= ROOTPATH.'/includes/upload-img.php'; ?>" method="post" name="image_upload" id="image_upload" enctype="multipart/form-data"> 
        <input type="file" style="display:none;" size="45" name="uploadfile" id="uploadfile" onchange="vpb_upload_and_resize();" />
	</form>

	<h1>Editer un marchand</h1>
	<?php 
        $menuactive = 'ajouter_marchand';
        require_once '../../pages/ajouter_offre/menu_ajouter.php'; ?>
	
	<form class="form_ajouter containerform" action="" method="POST" id="add_marchand" name="form_ajouter" style="text-align:left;" autocomplete="on">
<br />
			<!--Ajouter un logo -->
		<div id="parent-hover">
		<?= (isset($_SESSION['info']['uploadfile']) != '' ? $_SESSION['info']['uploadfile'] : '' ); ?>
			<?= (isset($_SESSION['info']['adr-img']) != '' ? $_SESSION['info']['adr-img'] : '' ); ?>
			
		<p style="font-weight:bold">Je rajoute le logo du marchand</p>
			<div class="logopreview" id="upload_area"><i style="color:#701818;" class="fas fa-image fa-4x"></i><br /> Ajouter un logo</div>
			<div class="dialog-open" style="padding:20px;">
				</div>
		</div>

		<input id="adr-img" name="adr-img" type="text" style="display:none;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : isset($fetch['img'])) ? 'value="'.$fetch['img'].'"' : ''; ?>>
		
		<label for="nom">Nom du marchand</label><input id="nom" name="nom" type="text"  placeholder="Cdiscount" <?= (isset($_SESSION['form']['nom']) ? 'value="'.$_SESSION['form']['nom'].'"' : 'value="'.$fetch['nom_marchand'].'"'); ?> required>
			<?= (isset($_SESSION['info']['nom']) != '' ? $_SESSION['info']['nom'] : '' ); ?><br />
		<label for="url">URL </label><input id="url" name="url" type="text" placeholder="https://www.cdiscount.com" <?= (isset($_SESSION['form']['url']) ? 'value="'.$_SESSION['form']['url'].'"' : 'value="'.$fetch['url_marchand'].'"'); ?> required>
			<?= (isset($_SESSION['info']['url']) != '' ? $_SESSION['info']['url'] : '' ); ?><br />
		<label for="categories">Catégorie </label><select id="categories" name="categories">
				<option value="1" id="cat-alimentation-supermarche" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 1) echo 'selected';  if(isset($fetch['cat']) && $fetch['cat'] == 1) echo 'selected'; ?>>Alimentation-Supermarché</option>
				<option value="2" id="cat-animaux" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 2) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 2) echo 'selected'; ?>>Animaux</option>
				<option value="3" id="cat-assurance-mutuelles" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 3) echo 'selected';if(isset($fetch['cat']) && $fetch['cat'] == 3) echo 'selected';  ?>>Assurance-Mutuelles</option>
				<option value="4" id="cat-auto-moto" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 4) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 4) echo 'selected'; ?>>Auto-Moto</option>
				<option value="5" id="cat-banque" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 5) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 5) echo 'selected'; ?>>Banques</option>
				<option value="6" id="cat-beaute-sante" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 6) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 6) echo 'selected'; ?>>Beauté-Santé</option>
				<option value="7" id="cat-bijoux-accessoires" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 7) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 7) echo 'selected'; ?>>Bijoux-Accessoires</option>
				<option value="8" id="cat-cadeaux-box" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 8) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 8) echo 'selected'; ?>>Cadeaux-Box</option>
				<option value="9" id="cat-cashback" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 9) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 9) echo 'selected'; ?>>Cashback</option>
				<option value="10" id="cat-cd-dvd-livres" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 10) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 10) echo 'selected'; ?>>CD-DVD-Livres</option>
				<option value="11" id="cat-chaussures" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 11) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 11) echo 'selected'; ?>>Chaussures</option>
				<option value="33" id="cat-cryptomonnaies" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 33) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 33) echo 'selected'; ?>>Cryptomonnaies</option>
				<option value="12" id="cat-decoration" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 12) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 12) echo 'selected'; ?>>Décoration</option>
				<option value="13" id="cat-energies-bois-electricite-gaz" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 13) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 13) echo 'selected'; ?>>Energies-Bois-Electricité-Gaz</option>
				<option value="14" id="cat-enfants-bebes-jouets" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 14) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 14) echo 'selected'; ?>>Enfants-Bébés-Jouets</option>
				<option value="32" id="cat-generaliste-vente" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 32) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 32) echo 'selected'; ?>>Généralistes-Vente</option>
				<option value="15" id="cat-internet-hebergement-vpn" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 15) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 15) echo 'selected'; ?>>Internet-Hébergement-VPN</option>
				<option value="16" id="cat-investissement" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 16) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 16) echo 'selected'; ?>>Investissement</option>
				<option value="17" id="cat-jardin-fleurs" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 17) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 17) echo 'selected'; ?>>Jardin-Fleurs</option>
				<option value="18" id="cat-jeux-video" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 18) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 18) echo 'selected'; ?>>Jeux-vidéo</option>
				<option value="19" id="cat-jeux-dargent" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 19) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 19) echo 'selected'; ?>>Jeux d'Argent</option>
				<option value="20" id="cat-loisirs-voyage" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 20) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 20) echo 'selected'; ?>>Loisirs-Voyages</option>
				<option value="21" id="cat-matelas-literie" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 21) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 21) echo 'selected'; ?>>Matelas-Literie</option>
				<option value="22" id="cat-maison-bricolage" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 22) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 22) echo 'selected'; ?>>Maison-Bricolage</option>
				<option value="23" id="cat-missions-sondages" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 23) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 23) echo 'selected'; ?>>Missions-Sondages</option>
				<option value="24" id="cat-multimedia-electromenager" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 24) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 24) echo 'selected'; ?>>Multimédia-Electroménager</option>
				<option value="25" id="cat-mode-vetements" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 25) echo 'selected';if(isset($fetch['cat']) && $fetch['cat'] == 25) echo 'selected';  ?>>Mode-Vêtements</option>
				<option value="26" id="cat-operateursinternet-telephone" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 26) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 26) echo 'selected'; ?>>Opérateurs internet-Téléphone</option>
				<option value="27" id="cat-optique" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 27) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 27) echo 'selected'; ?>>Optique</option>
				<option value="28" id="cat-photo-impression" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 28) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 28) echo 'selected'; ?>>Photo-Impression</option>
				<option value="29" id="cat-rencontre" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 29) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 29) echo 'selected'; ?>>Rencontre</option>
				<option value="30" id="cat-sport" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 30) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 30) echo 'selected'; ?>>Sport</option>
				<option value="31" id="cat-autre" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 31) echo 'selected'; if(isset($fetch['cat']) && $fetch['cat'] == 31) echo 'selected'; ?>>Autre</option>
				</select>
		<br />
		<label for="codepromo">Type d'offres </label>
		<table>
			<tr>
				<?php
				if(isset($fetch['offres']))
					$tableauoffres = explode(',', $fetch['offres']); ?>
				<td style="width:30%"></td>
				<td><label for="offre-codepromo" style="max-width:">Code Promo :</label><input id="offre-codepromo" name="offres[]" type="checkbox" value="1" <?= ((isset($_SESSION['form']['tableauoffres']) &&  in_array("1",$_SESSION['form']['tableauoffres']) ? 'checked' : isset($tableauoffres) &&  in_array("1",$tableauoffres)) ? 'checked' : '') ?>><label for="offre-codepromo"><span class="ui"></span></label></td>
				<td></td>
				<td><label for="offre-remises">Remises :</label><input id="offre-remises" name="offres[]" type="checkbox" value="2" <?= ((isset($_SESSION['form']['tableauoffres']) &&  in_array("2",$_SESSION['form']['tableauoffres']) ? 'checked' : isset($tableauoffres) &&  in_array("2",$tableauoffres)) ? 'checked' : '') ?>><label for="offre-remises"><span class="ui"></span></label></td>
			</tr>
			<tr>
			<td></td>
			<td><label for="offre-parrainages">Parrainages :</label><input id="offre-parrainages" name="offres[]" type="checkbox" value="3"  <?= ((isset($_SESSION['form']['tableauoffres']) &&  in_array("3",$_SESSION['form']['tableauoffres']) ? 'checked' : isset($tableauoffres) &&  in_array("3",$tableauoffres)) ? 'checked' : '') ?> checked><label for="offre-parrainages"><span class="ui"></span></label></td>
			<td> </td>
			<td><label for="offre-coupons">Coupons :</label><input id="offre-coupons" name="offres[]" type="checkbox" value="4"  <?= ((isset($_SESSION['form']['tableauoffres']) &&  in_array("1",$_SESSION['form']['tableauoffres']) ? 'checked' : isset($tableauoffres) &&  in_array("1",$tableauoffres)) ? 'checked' : '') ?>><label for="offre-coupons"><span class="ui"></span></label></td>
			</tr>
			<tr>
			<td></td>
				<td><label for="offre-odr">ODR :</label><input id="offre-odr" name="offres[]" type="checkbox" value="5"  <?php if(isset($tableauoffres) &&  in_array("5",$tableauoffres)) echo 'checked'; ?>><label for="offre-odr"><span class="ui"></span></label></td>
			</tr>
		</table>
		<br /><br />
				<!--Offre de parrainage pour le filleul - Remise, Frais de port G, Cadeau -->
		<div style="background-color:#FBFBFB;padding-top:15px;margin-bottom:5px;">
                <label for="choice">Offre de parrainage pour le filleul :</label>
                <div class="choiceType" id="choixtype" style="margin-left:8px">
                    <label for="choixremise" class="radios"><input type="radio" id="choixremise" name="choice" value="1" <?= ((isset($_SESSION['form']['choixoffre']) && $_SESSION['form']['choixoffre'] == '1' ? 'checked' : isset($fetch['choixoffre']) && $fetch['choixoffre'] == '1') ? 'checked' : ''); ?>>Remise</label>
                    <label for="choixfdp" class="radios"> <input type="radio" id="choixfdp" name="choice" value="2" <?= ((isset($_SESSION['form']['choixoffre']) && $_SESSION['form']['choixoffre'] == '2' ? 'checked' : isset($fetch['choixoffre']) && $fetch['choixoffre'] == '2') ? 'checked' : ''); ?>>Frais de port gratuit</label>
                    <label for="choixca" class="radios"><input type="radio" id="choixca" name="choice" value="3" <?= ((isset($_SESSION['form']['choixoffre']) && $_SESSION['form']['choixoffre'] == '3' ? 'checked' : isset($fetch['choixoffre']) && $fetch['choixoffre'] == '3') ? 'checked' : ''); ?>> Cadeau offert</label>
					<a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_offre_parrainage" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
				</div>
                <?= (isset($_SESSION['info']['choice']) != '' ? $_SESSION['info']['choice'] : '' ); ?>
            <br />
            </div>
		<div class="cacher" style="background-color:#FBFBFB;">
            
                <label for="remise">Montant de la remise :</label>
                <input id="remise" name="remise" type="text" style="width:5%;min-width:60px;max-width:60px;margin-left:8px" placeholder="ex: 5"  <?= ((isset($_SESSION['form']['montantremise']) ? $_SESSION['form']['montantremise'] : isset($fetch['montantremise'])) ? 'value="'.$fetch['montantremise'].'"' : ''); ?>>
                    <select id="devise" name="devise" style="width:5%;min-width:60px;">
                        <option value="€" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == '€' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == '€') ? 'selected' : ''); ?>>€</option>
                        <option value="%" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == '%' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == '%') ? 'selected' : ''); ?>>%</option>
                        <option value="mois" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == 'minutes' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == 'minutes') ? 'selected' : ''); ?>>minutes</option>
                        <option value="mois" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == 'mois' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == 'mois') ? 'selected' : ''); ?>>mois</option>
                        <option value="jours" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == 'jours' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == 'jours') ? 'selected' : ''); ?>>jours</option>
                        <option value="points" <?= ((isset($_SESSION['form']['montantdevise']) && $_SESSION['form']['montantdevise'] == 'points' ? 'selected' : isset($fetch['montantdevise']) && $fetch['montantdevise'] == 'points') ? 'selected' : ''); ?>>points</option>
                    </select> &nbsp;
               à partir de 
            <input id="achatminimal" name="achatminimal" type="text" placeholder="ex: 60" style="width:5%;min-width:50px;" <?= ((isset($_SESSION['form']['montantachatminimal']) ? $_SESSION['form']['montantachatminimal'] : isset($fetch['montantachatminimal'])) ? 'value="'.$fetch['montantachatminimal'].'"' : ''); ?>> 
            &nbsp;€ d'achat <a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_offre" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
            
            <br />  <?= (isset($_SESSION['info']['remise']) != '' ? $_SESSION['info']['remise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['devise']) != '' ? $_SESSION['info']['devise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['achatminimal']) != '' ? $_SESSION['info']['achatminimal'] : '' ); ?>
            </div>
			
				<br /><br />
			<!--Description-->
		<label for="description" style="vertical-align:top;">Description :</label>
			<textarea id="description" name="description" rows="12" required><?= ((isset($_SESSION['form']['description']) ? $_SESSION['form']['description'] : isset($fetch['description'])) ? $fetch['description'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_description" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
		<?=(isset($_SESSION['info']['description']) != '' ? $_SESSION['info']['description'] : '' ); ?><br />
			
				<!--Fonctionnement parrainage-->
			<?php
			$textefp = '
Rendez-vous sur '.$fetch['nom_marchand'].' en cliquant sur "Aller sur le site"
Ajoutez l\'adresse email de votre parrain dans la case prévue à cet effet lors de la souscription pour profiter du <strong>parrainage '.$fetch['nom_marchand'].'</strong>
Suivez le lien de <strong>parrainage '.$fetch['nom_marchand'].'</strong> de votre parrain sur cette page.
Inscrivez vous directement et le parrainage sera traité automatiquement grâce au lien que vous avez suivi. ';

?>
		<label for="foncparrainage" style="vertical-align:top;">Fonctionnement parrainage :</label>
			<textarea id="foncparrainage" name="foncparrainage" rows="8"><?= ((isset($_SESSION['form']['foncparrainage']) ? $_SESSION['form']['foncparrainage'] : isset($fetch['foncparrainage'])) ? $fetch['foncparrainage'] : $textefp); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_fp" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
			<?=(isset($_SESSION['info']['foncparrainage']) != '' ? $_SESSION['info']['foncparrainage'] : '' ); ?><br />
			
			<!--Offre parrain-->
		<label for="offre-parrain" style="vertical-align:top;">Offre parrain :<br /></label>
			<textarea id="offre-parrain" name="offre-parrain" rows="3"><?= ((isset($_SESSION['form']['offreparrain']) ? $_SESSION['form']['offreparrain'] : isset($fetch['offreparrain'])) ? $fetch['offreparrain'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_op" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
		<?=(isset($_SESSION['info']['offre-parrain']) != '' ? $_SESSION['info']['offre-parrain'] : '' ); ?><br />
		
		
		<!--Offre filleul-->
		<label for="offre-filleul" style="vertical-align:top;">Offre filleul :<br /></label>
			<textarea id="offre-filleul" name="offre-filleul"  rows="3"><?= ((isset($_SESSION['form']['offrefilleul']) ? $_SESSION['form']['offrefilleul'] : isset($fetch['offrefilleul'])) ? $fetch['offrefilleul'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;" rel="popup_of" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
        <?=(isset($_SESSION['info']['offre-filleul']) != '' ? $_SESSION['info']['offre-filleul'] : '' ); ?><br />
		<h3>Historique Marchand</h3>
		<label for="montantfilleul">Montant filleul :<br /></label>
		<input type="text" name="montantfilleul" value="<?= ((isset($_SESSION['form']['montantfilleul']) ? $_SESSION['form']['montantfilleul'] : isset($fetch['montantfilleul'])) ? $fetch['montantfilleul'] : ''); ?>">
		<br /><?=(isset($_SESSION['info']['montantfilleul']) != '' ? $_SESSION['info']['montantfilleul'] : '' ); ?>
		<label for="montantparrain">Montant parrain :<br /></label>
		<input type="text" name="montantparrain" value="<?= ((isset($_SESSION['form']['montantparrain']) ? $_SESSION['form']['montantparrain'] : isset($fetch['montantparrain'])) ? $fetch['montantparrain'] : ''); ?>">
		<br /><?=(isset($_SESSION['info']['montantparrain']) != '' ? $_SESSION['info']['montantparrain'] : '' ); ?>
		<label for="montantparrain">Date début<br /></label>
		<input type="text" name="date_debut" value="<?= ((isset($_SESSION['form']['date_debut']) ? $_SESSION['form']['date_debut'] : isset($fetch['date_debut'])) ? $fetch['date_debut'] : date('Y-m-d H:m:s')); ?>"> <a id="today" href="">Aujourd'hui</a>
		<br /><?=(isset($_SESSION['info']['date_debut']) != '' ? $_SESSION['info']['date_debut'] : '' ); ?>
		<label for="date_fin">Date fin<br /></label>
		<input type="text" name="date_fin" value="<?= ((isset($_SESSION['form']['date_fin']) ? $_SESSION['form']['date_fin'] : isset($fetch['date_fin'])) ? $fetch['date_fin'] : ''); ?>">
		<br /><?=(isset($_SESSION['info']['date_fin']) != '' ? $_SESSION['info']['date_fin'] : '' ); ?>
		<label for="boosted">Boosted<br /></label>
		<input type="checkbox" name="boosted" id="boosted" value="1" <?= ((isset($_SESSION['form']['boosted']) && $_SESSION['form']['boosted'] == 1 ? 'checked' : isset($fetch['boosted']) && $fetch['boosted'] == 1) ? 'checked' : ''); ?>><label style="margin-left:50px;" for="boosted"><span class="ui"></span></label>
		<?=(isset($_SESSION['info']['boosted']) != '' ? $_SESSION['info']['boosted'] : '' ); ?>
		
		<input type="submit" value="Modifier" class="bouton" name="form_ajouter" style="margin:10px auto; display:block;">
	</form> 

</section>

<div id="popup_offre_parrainage" style="text-align:left" class="popup_block">
	<h2>Offre de parrainage pour le filleul :</h2>
<p>Inscrivez ce que le marchand propose comme avantage de parrainage pour le filleul<br /></p>
</div>
<div id="popup_offre" style="text-align:left" class="popup_block">
	<h2>Offre proposée</h2>
<p>Inscrivez le gain pour le filleul.<br />
<b>Exemple : </b>pour un parrainage sur le site de cashback iGraal. <br />
Le site web Igraal reverse 3€ sur votre compte iGraal lors de l'inscription d'un nouveau membre par le biais d'un code ou d'un lien de parrainage. 
<br /><b>Donc vous inscriverez le gain de 3€ dans ces case.</b>
<br /><br />Le champ <i>à partir de XX € d'achats</i> n'est pas obligatoire et dépend des marchands.</p>
</div>
<div id="popup_description" style="text-align:left" class="popup_block">
	<h2>Description</h2>
<p>Ici je fais une description générale et complète du marchand. Plus il y a de ligne mieux c'est. <br />
Je ne rajoute pas mes code de parrainage personnel. <br />
</p>
</div>
<div id="popup_fp" style="text-align:left" class="popup_block">
	<h2>Fonctionnement du parrainage</h2>
<p>Ici je renseigne la marche à suivre pour que le filleul bénéficie du parrainage sous forme d'étape. Chaque étape est a entréer sur un nouvelle ligne.<br />
- Si c'est un code à entrer au moment de l'inscription<br />
- Si c'est un lien à suivre<br />
- Si c'est une invitation qu'il recevra par mail...<br />
</p>
</div>
<div id="popup_op" style="text-align:left" class="popup_block">
	<h2>Offre parrain</h2>
<p>J'indique le gain du parrain et les conditions.<br />
<b>Exemple : </b> 10€ offerts / filleul (30 filleuls max par année calendaire)<br />
</p>
</div>
<div id="popup_of" style="text-align:left" class="popup_block">
	<h2>Offre filleul</h2>
	<p>J'indique le gain du filleul et les conditions.<br />
<b>Exemple : </b> 3 mois de livraison gratuite illimitée dès 35€ d'achats<br />
</p>
</div>
<script>
$('.cacher, #codecache').hide();
$('#remise, #devise, #achatminimal, #code, #lien').prop( "disabled", true );
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");

$('#choixtype, #choixcode').click(function () {
$('#choixtype input:not(:checked), #choixcode input:not(:checked)').parent().removeClass("radios-checked").addClass("radios");
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");
changeit();
});

function changeit() {
var selected_value = $("input[name='choice']:checked").val();

    if(selected_value == 1) {
        $('.cacher').show();
        $('#remise, #devise, #achatminimal').prop( "disabled", false );
    } else {
        $('.cacher').hide();
        $('#remise, #devise, #achatminimal').prop( "disabled", true );
    }

    var codeoulien = $("input[name='choicecode']:checked").val();
    
    if(codeoulien == 1) {
        $('#codecache').show();
        $('#code').prop( "disabled", false );
        $('#lien').prop( "disabled", false );
    } else {
        $('#codecache').hide();
        $('#code').prop( "disabled", true );
        $('#lien').prop( "disabled", true );
    }
}
		//UPLOAD IMAGE
		function vpb_upload_and_resize() {
		$("#image_upload").vPB({
			dataType: "JSON",
			url: '<?= ROOTPATH; ?>/includes/upload-img.php',
			beforeSubmit: function() 
			{
				$(".logopreview").empty().html('<div style="padding:20px;min-width:230px;margin-top:25px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" align="absmiddle" title="Envoi ..."/></div><br clear="all">');
			},
			success: function(response) 
			{
				$(".dialog-open").hide();
				$('.logopreview').css({border : '2px solid #FFF'})
				$("#upload_area").fadeIn('slow').html('<div class="vpb_image_style"><button type="button" onclick="picturedelete();" class="picture-delete"><i class="fas fa-times"></i></button><img src="<?=ROOTPATH;?>/membres/includes/uploads-img/120-'+response+'" /></div>');
				$("#adr-img").val(response);
			}
		}).submit(); 
	}
	function openinputfile(){
		$('#uploadfile').trigger('click');
	}
	//UPLOAD IMAGE PAR URL
	function downloadimg(){
		var urlimg = $('#adr-imgtemp').val();
		var img = $("<img />").attr('src', urlimg)
					$.ajax({
						method: "POST",
						url: "<?= ROOTPATH; ?>/includes/upload-img.php",
						data: {urlimg:urlimg},
						beforeSend:function() {
							$(".logopreview").empty().html('<div style="padding:20px;min-width:230px;margin-top:25px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" align="absmiddle" title="Envoi ..."/></div><br clear="all">');
						},
						success: function (response) {
							$(".dialog-open").hide();
							$('.logopreview').css({border : '2px solid #FFF'})
							$("#upload_area").fadeIn('slow').html('<div class="vpb_image_style"><button type="button" onclick="picturedelete();" class="picture-delete"><i class="fas fa-times"></i></button><img src="<?=ROOTPATH;?>/membres/includes/uploads-img/120-'+response+'" /></div>');
							$("#adr-img").val(response);
						
						},
						error: function (response) {
							alert('error');
						}
					});
					
	}
	//AFFICHAGE IMAGESI FORM ADR-IMG value rempli
	$(document).ready( function(){
		$value = $("#adr-img").val();
		if($value != "") {
			var img = $("<img />").attr('src', '<?= ROOTPATH; ?>/membres/includes/uploads-img/120-'+$value);
			$(".logopreview").empty();
			$(".logopreview").append('<button type="button" class="picture-delete" onclick="picturedelete();"><i class="fas fa-times"></i></button>');
			$(".logopreview").append(img);
		}
	});
	function picturedelete(){
		$('.logopreview').empty();
		$('.logopreview').append('<i style="color:#701818;margin:20px 0;" class="fas fa-image fa-4x"></i><br /> Ajouter un logo');
	}
	function closemodal(){
		$('.dialog-open').hide();
		$('.logopreview').css({border : '2px solid #FFF'})
	}
	$(function(){
		$('label[for=offre-parrain], #offre-parrain, label[for=offre-filleul], #offre-filleul, #foncparrainage,  label[for=foncparrainage]').hide();
		if($('#offre-parrainages').is(':checked'))
		$('label[for=offre-parrain], #offre-parrain, label[for=offre-filleul], #offre-filleul, #foncparrainage,  label[for=foncparrainage]').fadeIn();
		
        $('#offre-parrainages').click(function() {
            if($('#offre-parrainages').is(':checked') )
				$('label[for=offre-parrain], #offre-parrain, label[for=offre-filleul], #offre-filleul, #foncparrainage,  label[for=foncparrainage]').fadeIn();
			else
			$('label[for=offre-parrain], #offre-parrain, label[for=offre-filleul], #offre-filleul, #foncparrainage,  label[for=foncparrainage]').hide();
        });
    });
	//CLICK SUR AJOUTER IMAGE -> APPARAIT BOITE DIALOGUE POUR PARCOURIR
	$(document).ready( function(){
		var nav = $('.dialog-open');
		nav.hide();
		$('.logopreview').click( function(event){
			event.stopPropagation();
			$('.logopreview').css({border : '2px solid #701818'})
			$(this).next().toggle().html('<button type="button" class="vpb-close" onclick="closemodal();"><i class="fas fa-times"></i></button><div class="dialog-open_inner"><div><label for="charg-img">Charger une image</label><div class="pboutonr" id="parcourir-img" onclick="openinputfile();" style="min-width:210px;margin:10px 0;">Parcourir</div></div></div><div class="dialog-open_inner"><div><label for="adr-img">Image depuis une URL</label><input id="adr-imgtemp" name="adr-imgtemps" type="text" style="width:30%;margin:0;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : ''); ?>><div class="pboutonr" id="parcourir-img" onclick="downloadimg();" style="min-width:210px;margin:10px 0;">Envoyer</div></div></div>');
		});
	});
	$('a.poplight').click(function() {
	var popID = $(this).attr('rel'); //Trouver la pop-up correspondante
	var popURL = $(this).attr('href'); //Retrouver la largeur dans le href

	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#' + popID).fadeIn().prepend('<a href="#" class="close" style="float:right"><img src="../images/close_pop.png" class="btn_close" title="Fermer" alt="Fermer" /></a>');

	//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 40) / 2;

	//On affecte le margin
	$('#' + popID).css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});

	//Effet fade-in du fond opaque
	$('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

	return false;
});

///Fermeture de la pop-up et du fond
jQuery('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		jQuery('#fade , .popup_block').fadeOut(function() {
			jQuery('#fade, a.close').remove();  
	}); //...ils disparaissent ensemble
		
		return false;
	});

</script>

<script type="text/javascript" src="<?= ROOTPATH ?>/script/upload_img.js"></script>

	<?php  require_once '../../elements/footer.php'; ?>