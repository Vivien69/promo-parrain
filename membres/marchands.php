<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
$title = 'Proposition de marchands';
require_once '../elements/header2.php';

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
        ' - <a href="' . ROOTPATH . '/connexion">Se connecter</a>',
        ROOTPATH,
        20
        );
    require_once('../information.php');
    exit(); 
	}

$sql = "SELECT * FROM user WHERE membre_id=".$id;
$prep = $pdo->prepare($sql);
$prep->execute();

if($prep->rowCount() == 0) {
	$informations = Array(/*L'id de cookie est incorrect*/
            true,
            'Accès interdit',
            'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
            'Vous devez être membre ou connecté',
            '../connexion',
            3
            );
        require_once('../information.php');
        exit();
	
} else {
while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
    /**
     * *******FIN Gestion avant affichage...**********
     */
	 //AJOUT D'UN MARCHAND
	 if(isset($_POST['form_ajouter'])) {
		 $erreurs = 0;
		//GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
			if(isset($_POST['nom'])) {
				$donnee = trim($_POST['nom']);
				$result = checknomarchand($donnee);
				if ($result == 'tooshort') {
					$_SESSION['info']['nom'] = '<div class="erreurform">Le nom ' . htmlspecialchars($donnee, ENT_QUOTES) . ' est trop court (minimum 3 caract&egrave;res).</div>';
					$_SESSION['form']['nom'] = '';
					$erreurs++;
				} else if ($result == 'toolong') {
					$_SESSION['info']['nom'] = '<div class="erreurform">Le nom ' . htmlspecialchars($donnee, ENT_QUOTES) . ' est trop long (maximum 30 caract&egrave;res).</div>';
					$_SESSION['form']['nom'] = '';
					$erreurs++;
				} else if ($result == 'exists') {
					$_SESSION['info']['nom'] = '<div class="erreurform">Le nom ' . htmlspecialchars($donnee, ENT_QUOTES) . ' est d&eacute;j&agrave;  pris, choisissez-en un autre.</div>';
					$_SESSION['form']['nom'] = '';
					$erreurs++;
				} else if ($result == 'ok') {
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
					$_SESSION['form']['adr-img'] = $donnee;
				} else {
					$_SESSION['info']['adr-img'] = '<div class="erreur" >L\'image du marchand est obligatoire</div>';
					$_SESSION['form']['adr-img'] = $donnee;
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
					$_SESSION['form']['description'] = nl2br(strip_tags($donnee), '<strong></strong><h5></h5><br /><b></b>');
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
					$_SESSION['form']['foncparrainage'] = nl2br(strip_tags($donnee), '<strong></strong><h5></h5><br /><b></b>');
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
					$_SESSION['form']['offre-parrain'] = nl2br(strip_tags($donnee), '<strong></strong><h5></h5><br /><b></b>');
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
					$_SESSION['form']['offre-filleul'] = nl2br(strip_tags($donnee), '<strong></strong><h5></h5><br /><b></b>');
				} else {
					$_SESSION['info']['offre-filleul'] = '<div class="erreurform">Erreur offre filleul</div>';
					$_SESSION['form']['offre-filleul'] = '';
					$erreurs++;
				}
			}

			if($erreurs == 0){
				$ladate = time();
				
				$sql = "INSERT INTO marchands (nom_marchand, url_marchand, idmembre, img, cat, offres, choixoffre, montantremise, montantdevise, montantachatminimal, description, foncparrainage, offreparrain, offrefilleul, dateajout, etat) 
				VALUES (:nom_marchand, :url_marchand, :idmembre, :img, :cat, :offres, :choixoffre, :montantremise, :montantdevise, :montantachatminimal, :description, :foncparrainage, :offreparrain, :offrefilleul, :dateajout, :etat)";
				$sqlbind = $pdo->prepare($sql);
				$sqlbind->bindParam(':nom_marchand', $_SESSION['form']['nom'], PDO::PARAM_STR);
				$sqlbind->bindParam(':url_marchand', $_SESSION['form']['url'], PDO::PARAM_STR);
				$sqlbind->bindParam(':idmembre', $_SESSION['membre_id'], PDO::PARAM_INT);
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
				$sqlbind->bindParam(':dateajout', $ladate, PDO::PARAM_INT);
				$sqlbind->bindValue(':etat', 0, PDO::PARAM_INT);
	
				if ($sqlbind->execute()) {
					echo '<div class="valider">Merci, le marchand <b>'.$_SESSION['form']['nom'].'</b> a été ajouté. Vous pouvez désormais rajouter votre offre de parrainage sur ce marchand sur la page <a href="'.ROOTPATH.'/parrain/ajouter">"Ajouter une offre"</a></div>';
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

	<h1>Ajouter un marchand</h1>
	<?php 
        $menuactive = 'ajouter_marchand';
        require_once '../pages/ajouter_offre/menu_ajouter.php'; ?>
	
	<form class="form_ajouter containerform" action="" method="POST" id="add_marchand" name="form_ajouter" style="text-align:left;" autocomplete="on">
<br />
			<!--Ajouter un logo -->
		<div id="parent-hover">
		<?= (isset($_SESSION['info']['uploadfile']) != '' ? $_SESSION['info']['uploadfile'] : '' ); ?>
			<?= (isset($_SESSION['info']['adr-img']) != '' ? $_SESSION['info']['adr-img'] : '' ); ?>
		<p style="font-weight:bold">Je rajoute le logo du marchand</p>
			<div class="logopreview" id="upload_area"><i style="color:#701818;" class="fas fa-image fa-4x"></i><br /> Ajouter un logo</div>
				<div class="dialog-open" style="padding:20px;">
				<button type="button" class="vpb-close"><i class="fas fa-times"></i></button><div class="dialog-open_inner"><div><label for="charg-img">Charger une image</label><div class="pboutonr" id="parcourir-img" onclick="openinputfile();" style="min-width:210px;margin:10px 0;">Parcourir</div></div></div><div class="dialog-open_inner"><div><label for="adr-img">Image depuis une URL</label><input id="adr-imgtemp" name="adr-imgtemps" type="text" style="width:30%;margin:0;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : ''); ?>><div class="pboutonr" id="parcourir-img" onclick="downloadimg();" style="min-width:210px;margin:10px 0;">Envoyer</div></div></div>
				</div>
			
		</div>
		<input id="adr-img" name="adr-img" type="text" style="display:none;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : ''); ?>>
			
		<label for="nom">Nom du marchand</label><input id="nom" name="nom" type="text"  placeholder="Cdiscount" <?= (isset($_SESSION['form']['nom']) ? 'value="'.$_SESSION['form']['nom'].'"' : ''); ?> required>
			<?= (isset($_SESSION['info']['nom']) != '' ? $_SESSION['info']['nom'] : '' ); ?><br />
		<label for="url">URL </label><input id="url" name="url" type="text" placeholder="https://www.cdiscount.com" <?= (isset($_SESSION['form']['url']) ? 'value="'.$_SESSION['form']['url'].'"' : 'value="https://www."'); ?> required>
			<?= (isset($_SESSION['info']['url']) != '' ? $_SESSION['info']['url'] : '' ); ?><br />
		<label for="categories">Catégorie </label><select id="categories" name="categories">
				<option value="1" id="cat-alimentation-supermarche" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 1) echo 'selected'; ?>>Alimentation-Supermarché</option>
				<option value="2" id="cat-animaux" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 2) echo 'selected'; ?>>Animaux</option>
				<option value="3" id="cat-assurance-mutuelles" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 3) echo 'selected'; ?>>Assurance-Mutuelles</option>
				<option value="4" id="cat-auto-moto" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 4) echo 'selected'; ?>>Auto-Moto</option>
				<option value="5" id="cat-banque" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 5) echo 'selected'; ?>>Banques</option>
				<option value="6" id="cat-beaute-sante" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 6) echo 'selected'; ?>>Beauté-Santé</option>
				<option value="7" id="cat-bijoux-accessoires" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 7) echo 'selected'; ?>>Bijoux-Accessoires</option>
				<option value="8" id="cat-cadeaux-box" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 8) echo 'selected'; ?>>Cadeaux-Box</option>
				<option value="9" id="cat-cashback" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 9) echo 'selected'; ?>>Cashback</option>
				<option value="10" id="cat-cd-dvd-livres" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 10) echo 'selected'; ?>>CD-DVD-Livres</option>
				<option value="11" id="cat-chaussures" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 11) echo 'selected'; ?>>Chaussures</option>
				<option value="33" id="cat-cryptomonnaies" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 33) echo 'selected'; ?>>Cryptomonnaies</option>
				<option value="12" id="cat-decoration" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 12) echo 'selected'; ?>>Décoration</option>
				<option value="13" id="cat-energies-bois-electricite-gaz" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 13) echo 'selected'; ?>>Energies-Bois-Electricité-Gaz</option>
				<option value="14" id="cat-enfants-bebes-jouets" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 14) echo 'selected'; ?>>Enfants-Bébés-Jouets</option>
				<option value="32" id="cat-generaliste-vente" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 32) echo 'selected'; ?>>Généralistes-Vente</option>
				<option value="15" id="cat-internet-hebergement-vpn" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 15) echo 'selected'; ?>>Internet-Hébergement-VPN</option>
				<option value="16" id="cat-investissement" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 16) echo 'selected'; ?>>Investissement</option>
				<option value="17" id="cat-jardin-fleurs" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 17) echo 'selected'; ?>>Jardin-Fleurs</option>
				<option value="18" id="cat-jeux-video" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 18) echo 'selected'; ?>>Jeux-vidéo</option>
				<option value="19" id="cat-jeux-dargent" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 19) echo 'selected'; ?>>Jeux d'Argent</option>
				<option value="20" id="cat-loisirs-voyage" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 20) echo 'selected'; ?>>Loisirs-Voyages</option>
				<option value="21" id="cat-matelas-literie" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 21) echo 'selected'; ?>>Matelas-Literie</option>
				<option value="22" id="cat-maison-bricolage" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 22) echo 'selected'; ?>>Maison-Bricolage</option>
				<option value="23" id="cat-missions-sondages" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 23) echo 'selected'; ?>>Missions-Sondages</option>
				<option value="24" id="cat-multimedia-electromenager" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 24) echo 'selected'; ?>>Multimédia-Electroménager</option>
				<option value="25" id="cat-mode-vetements" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 25) echo 'selected'; ?>>Mode-Vêtements</option>
				<option value="26" id="cat-operateursinternet-telephone" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 26) echo 'selected'; ?>>Opérateurs internet-Téléphone</option>
				<option value="27" id="cat-optique" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 27) echo 'selected'; ?>>Optique</option>
				<option value="28" id="cat-photo-impression" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 28) echo 'selected'; ?>>Photo-Impression</option>
				<option value="29" id="cat-rencontre" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 29) echo 'selected'; ?>>Rencontre</option>
				<option value="30" id="cat-sport" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 30) echo 'selected'; ?>>Sport</option>
				<option value="31" id="cat-autre" <?php if(isset($_SESSION['form']['categories']) && $_SESSION['form']['categories'] == 31) echo 'selected'; ?>>Autre</option>
				</select>
		<br />
		<label for="codepromo">Type d'offres </label>
		<table>
			<tr>
				<td style="width:30%"></td>
				<td><label for="offre-codepromo" style="max-width:">Code Promo :</label><input id="offre-codepromo" name="offres[]" type="checkbox" value="1" <?php if(isset($_SESSION['form']['tableauoffres']) &&  in_array("1",$_SESSION['form']['tableauoffres'])) echo 'checked'; ?>><label for="offre-codepromo"><span class="ui"></span></label></td>
				<td></td>
				<td><label for="offre-remises">Remises :</label><input id="offre-remises" name="offres[]" type="checkbox" value="2"  <?php if(isset($_SESSION['form']['tableauoffres']) &&  in_array("2",$_SESSION['form']['tableauoffres'])) echo 'checked'; ?>><label for="offre-remises"><span class="ui"></span></label></td>
			</tr>
			<tr>
			<td></td>
			<td><label for="offre-parrainages">Parrainages :</label><input id="offre-parrainages" name="offres[]" type="checkbox" value="3"  <?php if(isset($_SESSION['form']['tableauoffres']) &&  in_array("3",$_SESSION['form']['tableauoffres'])) echo 'checked'; ?> checked><label for="offre-parrainages"><span class="ui"></span></label></td>
			<td> </td>
			<td><label for="offre-coupons">Coupons :</label><input id="offre-coupons" name="offres[]" type="checkbox" value="4"  <?php if(isset($_SESSION['form']['tableauoffres']) &&  in_array("4",$_SESSION['form']['tableauoffres'])) echo 'checked'; ?>><label for="offre-coupons"><span class="ui"></span></label></td>
			</tr>
			<tr>
			<td></td>
				<td><label for="offre-odr">ODR :</label><input id="offre-odr" name="offres[]" type="checkbox" value="5"  <?php if(isset($_SESSION['form']['tableauoffres']) &&  in_array("5",$_SESSION['form']['tableauoffres'])) echo 'checked'; ?>><label for="offre-odr"><span class="ui"></span></label></td>
			</tr>
		</table>
		<br /><br />
				<!--Offre de parrainage pour le filleul - Remise, Frais de port G, Cadeau -->
		<div style="background-color:#FBFBFB;padding-top:15px;margin-bottom:5px;">
                <label for="choice">Offre de parrainage pour le filleul :</label>
                <div class="choiceType" id="choixtype" style="margin-left:8px">
                    <label for="choixremise" class="radios"><input type="radio" id="choixremise" name="choice" value="1" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '1' ? 'checked' : ''); ?>>Remise</label>
                    <label for="choixfdp" class="radios"> <input type="radio" id="choixfdp" name="choice" value="2" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '2' ? 'checked' : ''); ?>>Frais de port gratuit</label>
                    <label for="choixca" class="radios"><input type="radio" id="choixca" name="choice" value="3" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '3' ? 'checked' : ''); ?>> Cadeau offert</label>
					<a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_offre_parrainage" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
				</div>
                <?= (isset($_SESSION['info']['choice']) != '' ? $_SESSION['info']['choice'] : '' ); ?>
            <br />
            </div>
		<div class="cacher" style="background-color:#FBFBFB;">
            
                <label for="remise">Montant de la remise :</label>
                <input id="remise" name="remise" type="text" style="width:5%;min-width:60px;max-width:60px;margin-left:8px" placeholder="ex: 5"  <?= (isset($_SESSION['form']['remise']) ? 'value="'.$_SESSION['form']['remise'].'"' : ''); ?>>
                    <select id="devise" name="devise" style="width:5%;min-width:60px;">
                        <option value="€" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '€' ? 'selected' : ''); ?>>€</option>
                        <option value="%" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '%' ? 'selected' : ''); ?>>%</option>
                        <option value="mois" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'mois' ? 'selected' : ''); ?>>minutes</option>
                        <option value="mois" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'mois' ? 'selected' : ''); ?>>mois</option>
                        <option value="jours" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'jours' ? 'selected' : ''); ?>>jours</option>
                        <option value="points" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'points' ? 'selected' : ''); ?>>points</option>
                    </select> &nbsp;
               à partir de 
            <input id="achatminimal" name="achatminimal" type="text" placeholder="ex: 60" style="width:5%;min-width:50px;" <?= (isset($_SESSION['form']['achatminimal']) ? 'value="'.$_SESSION['form']['achatminimal'].'"' : ''); ?>> 
            &nbsp;€ d'achat <a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_offre" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
            
            <br />  <?= (isset($_SESSION['info']['remise']) != '' ? $_SESSION['info']['remise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['devise']) != '' ? $_SESSION['info']['devise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['achatminimal']) != '' ? $_SESSION['info']['achatminimal'] : '' ); ?>
            </div>

				<br /><br />
			<!--Description-->
		<label for="description" style="vertical-align:top;">Description :</label>
			<textarea id="description" name="description" rows="12" required><?= (isset($_SESSION['form']['description']) ? $_SESSION['form']['description'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_description" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
		<?=(isset($_SESSION['info']['description']) != '' ? $_SESSION['info']['description'] : '' ); ?><br />
			
				<!--Fonctionnement parrainage-->
			<?php
			$textefp = 'Conservez que l\'un des 2 cas concerné et modifier ADRESSESITE.FR par le nom du marchand. Si les cas ne viennent pas modifiez les si une autre démarche est nécessaire pour le filleul:
- CAS DU CODE PROMO :
Rendez-vous sur ADRESSESITE.FR en cliquant sur "Aller sur le site"
Ajoutez l\'adresse email de votre parrain dans la case prévue à cet effet lors de la souscription pour profiter du <strong>parrainage NOMSITE</strong>

- CAS DU LIEN DE PARRAINAGE :
Suivez le lien de <strong>parrainage NOMSITE</strong> de votre parrain sur cette page.
Inscrivez vous directement et le parrainage sera traité automatiquement grâce au lien que vous avez suivi. ';

?>
		<label for="foncparrainage" style="vertical-align:top;">Fonctionnement parrainage :</label>
			<textarea id="foncparrainage" name="foncparrainage" rows="8"><?= (isset($_SESSION['form']['foncparrainage']) ? $_SESSION['form']['foncparrainage'] : $textefp); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_fp" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
			<?=(isset($_SESSION['info']['foncparrainage']) != '' ? $_SESSION['info']['foncparrainage'] : '' ); ?><br />
			
			<!--Offre parrain-->
		<label for="offre-parrain" style="vertical-align:top;">Offre parrain :<br /></label>
			<textarea id="offre-parrain" name="offre-parrain" rows="3"><?= (isset($_SESSION['form']['offre-parrain']) ? $_SESSION['form']['offre-parrain'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_op" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
		<?=(isset($_SESSION['info']['offre-parrain']) != '' ? $_SESSION['info']['offre-parrain'] : '' ); ?><br />
		
		
		<!--Offre filleul-->
		<label for="offre-filleul" style="">Offre filleul :<br /></label>
			<textarea id="offre-filleul" name="offre-filleul"  rows="3"><?= (isset($_SESSION['form']['offre-filleul']) ? $_SESSION['form']['offre-filleul'] : ''); ?></textarea>
			<a href="#" style="margin:20px 0 0 20px;" rel="popup_of" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
        <?=(isset($_SESSION['info']['offre-filleul']) != '' ? $_SESSION['info']['offre-filleul'] : '' ); ?><br />
		

		<input type="submit" value="Envoyer" class="bouton" name="form_ajouter" style="margin:10px auto; display:block;">
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
				document.getElementById('adr-img').value = response;
				if(response != 'empty') {
					$("#upload_area").fadeIn('slow').html('<div class="vpb_image_style"><button type="button" onclick="picturedelete();" class="picture-delete"><i class="fas fa-times"></i></button><img src="<?=ROOTPATH;?>/membres/includes/uploads-img/120-'+response+'" /></div>');
				}
				
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

	closemodal = document.getElementsByClassName('vpb-close')

	for (let divop of closemodal) {
		console.log(divop)
		divop.addEventListener('click', function() {
			this.parentElement.style.display = "none"
			
		});
	}
	//AFFICHAGE IMAGESI FORM ADR-IMG value rempli
	$(document).ready( function(){
		$value = $("#adr-img").val();
		if($value != "") {
			var img = $("<img />").attr('src', '<?= ROOTPATH; ?>/membres/includes/uploads-img/120-'+$value);
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
			$(this).next().toggle();
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
	<?php } } require_once '../elements/footer.php'; ?>