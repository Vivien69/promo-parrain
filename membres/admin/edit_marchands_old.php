<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Modification d\'un marchand';
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
        20
        );
    require_once('../../information.php');
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
            '',
            '../index.php',
            3
            );
        require_once('../information.php');
        exit();
	
} else {
while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
    /**
     * *******FIN Gestion avant affichage...**********
     */



	$current = 'admin_marchands' ;
	 require_once '../includes/menu_membres.php';
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
				   $_SESSION['info']['uploadfile'] = '<div class="erreurform">Vous n\'avez pas ajouter de logo</div>';
				   $_SESSION['form']['uploadfile'] = '';
				   $erreurs++;
			   }
		   }
		   if(isset($_POST['adr-img']) && $_POST['adr-img'] != "") {
			   	$donnee  = trim($_POST['adr-img']);
				$_SESSION['info']['adr-img'] = '';
				$_SESSION['form']['adr-img'] = $donnee;
			 
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

		   if(isset($_POST['description'])) {
			   $donnee = trim($_POST['description']);
			   $result = check30carac($donnee);
			   if ($result == 'ok') {
				   $_SESSION['info']['description'] = '';
				   $_SESSION['form']['description'] = nl2br($donnee);
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
				$_SESSION['form']['foncparrainage'] = nl2br($donnee);
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
				   $_SESSION['form']['offre-parrain'] = nl2br($donnee);
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
				   $_SESSION['form']['offre-filleul'] = nl2br($donnee);
			   } else {
				   $_SESSION['info']['offre-filleul'] = '<div class="erreurform">Erreur offre filleul</div>';
				   $_SESSION['form']['offre-filleul'] = '';
				   $erreurs++;
			   }
		   }

		   if($erreurs == 0){
			   $id = intval($_GET['idm']);
				$sql = "UPDATE marchands
				SET nom_marchand = :nom_marchand, url_marchand = :url_marchand, img = :img, cat = :cat, offres = :offres, description = :description, foncparrainage = :foncparrainage, offreparrain = :offreparrain, offrefilleul = :offrefilleul
				WHERE id = $id";
				
				$sqlbind = $pdo->prepare($sql);
				$sqlbind->bindParam(':nom_marchand', $_SESSION['form']['nom'], PDO::PARAM_STR);
				$sqlbind->bindParam(':url_marchand', $_SESSION['form']['url'], PDO::PARAM_STR);
				$sqlbind->bindParam(':img', $_SESSION['form']['adr-img'], PDO::PARAM_STR);
				$sqlbind->bindParam(':cat', $_SESSION['form']['categories'], PDO::PARAM_INT);
				$sqlbind->bindParam(':offres', $_SESSION['form']['offres'], PDO::PARAM_STR);
				$sqlbind->bindParam(':description', $_SESSION['form']['description'], PDO::PARAM_STR);
				$sqlbind->bindParam(':foncparrainage', $_SESSION['form']['foncparrainage'], PDO::PARAM_STR);
				$sqlbind->bindParam(':offreparrain', $_SESSION['form']['offre-parrain'], PDO::PARAM_STR);
				$sqlbind->bindParam(':offrefilleul', $_SESSION['form']['offre-filleul'], PDO::PARAM_STR);

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
<p style="text-align:left;"><a href="<?=ROOTPATH;?>/membres/admin/liste-marchands.html">< Liste des marchands</a></p>
	<?= (isset($_SESSION['nb_erreurs']) && $_SESSION['nb_erreurs'] != "") ? $_SESSION['nb_erreurs'] : ''; ?>

	<form action="<?= ROOTPATH.'/includes/upload-img.php'; ?>" method="post" name="image_upload" id="image_upload" enctype="multipart/form-data"> 
        <input type="file" style="display:none;" size="45" name="uploadfile" id="uploadfile" onchange="vpb_upload_and_resize();" />
	</form>
	
	<br />

<?php
	/** SELECTIONNE LES INFORMATION DANS LA BDD DU MARCHAND */
	$idmarchand = intval($_GET['idm']);
	$sqlmarchand = "SELECT * FROM marchands WHERE id=".$idmarchand;
	$prepmarchand = $pdo->prepare($sqlmarchand);
	$prepmarchand->execute();
	while($edit = $prepmarchand->fetch(PDO::FETCH_ASSOC)) {
		
	
?>

	<h3>Modifier un marchand</h3>
	<form method="POST" id="add_marchand" name="form_ajouter" style="text-align:left;" autocomplete="on">

		<div id="parent-hover">
			<div class="logopreview" id="upload_area"> <?=($edit['img'] ? '<div class="vpb_image_style"><button type="button" class="picture-delete"><i class="fas fa-times"></i></button><img src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$edit['img'].'" /></div></div>' : '<i style="color:#701818;margin:20px 0;" class="fas fa-image fa-4x"></i><br /> Ajouter un logo</div>'); ?>
				<div class="dialog-open" style="padding:20px;">
				</div>
		</div>
		<input id="adr-img" style="display:none;" name="adr-img" type="text"  placeholder="http://" <?= (isset($edit['img']) ? 'value="'.$edit['img'].'"' : ''); ?>>
			<?= (isset($_SESSION['info']['uploadfile']) != '' ? $_SESSION['info']['uploadfile'] : '' ); ?>
			<?= (isset($_SESSION['info']['adr-img']) != '' ? $_SESSION['info']['adr-img'] : '' ); ?><br />
		<label for="nom">Nom </label><input id="nom" name="nom" type="text" style="width:30%" placeholder="Cdiscount" <?= (isset($edit['nom_marchand']) ? 'value="'.$edit['nom_marchand'].'"' : ''); ?> required>
			<?= (isset($_SESSION['info']['nom']) != '' ? $_SESSION['info']['nom'] : '' ); ?><br />
		<label for="url">URL </label><input id="url" name="url" type="text" style="width:30%" placeholder="http://www.cdiscount.com" <?= (isset($edit['url_marchand']) ? 'value="'.$edit['url_marchand'].'"' : 'value="http://www."'); ?> required>
			<?= (isset($_SESSION['info']['url']) != '' ? $_SESSION['info']['url'] : '' ); ?><br />

		<label for="categories">Catégories </label><select id="categories" name="categories" style="width:30%;">
				<option value="1" id="cat-alimentation-supermarche" <?php if(isset($edit['cat']) && $edit['cat'] == 1) echo 'selected'; ?>>Alimentation-Supermarché</option>
				<option value="2" id="cat-animaux" <?php if(isset($edit['cat']) && $edit['cat'] == 2) echo 'selected'; ?>>Animaux</option>
				<option value="3" id="cat-assurance-mutuelles" <?php if(isset($edit['cat']) && $edit['cat'] == 3) echo 'selected'; ?>>Assurance-Mutuelles</option>
				<option value="4" id="cat-auto-moto" <?php if(isset($edit['cat']) && $edit['cat'] == 4) echo 'selected'; ?>>Auto-Moto</option>
				<option value="5" id="cat-banque" <?php if(isset($edit['cat']) && $edit['cat'] == 5) echo 'selected'; ?>>Banques</option>
				<option value="6" id="cat-beaute-sante" <?php if(isset($edit['cat']) && $edit['cat'] == 6) echo 'selected'; ?>>Beauté-Santé</option>
				<option value="7" id="cat-bijoux-accessoires" <?php if(isset($edit['cat']) && $edit['cat'] == 7) echo 'selected'; ?>>Bijoux-Accessoires</option>
				<option value="8" id="cat-cadeaux-box" <?php if(isset($edit['cat']) && $edit['cat'] == 8) echo 'selected'; ?>>Cadeaux-Box</option>
				<option value="9" id="cat-cashback" <?php if(isset($edit['cat']) && $edit['cat'] == 9) echo 'selected'; ?>>Cashback</option>
				<option value="10" id="cat-cd-dvd-livres" <?php if(isset($edit['cat']) && $edit['cat'] == 10) echo 'selected'; ?>>CD-DVD-Livres</option>
				<option value="11" id="cat-chaussures" <?php if(isset($edit['cat']) && $edit['cat'] == 11) echo 'selected'; ?>>Chaussures</option>
				<option value="33" id="cat-cryptomonnaies" <?php if(isset($edit['cat']) && $edit['cat']  == 33) echo 'selected'; ?>>Cryptomonnaies</option>
				<option value="12" id="cat-decoration" <?php if(isset($edit['cat']) && $edit['cat'] == 12) echo 'selected'; ?>>Décoration</option>
				<option value="13" id="cat-energies-bois-electricite-gaz" <?php if(isset($edit['cat']) && $edit['cat'] == 13) echo 'selected'; ?>>Energies-Bois-Electricité-Gaz</option>
				<option value="14" id="cat-enfants-bebes-jouets" <?php if(isset($edit['cat']) && $edit['cat'] == 14) echo 'selected'; ?>>Enfants-Bébés-Jouets</option>
				<option value="32" id="cat-generaliste-vente"<?php if(isset($edit['cat']) && $edit['cat'] == 32) echo 'selected'; ?>>Généralistes-Vente</option>
				<option value="15" id="cat-internet-hebergement-vpn" <?php if(isset($edit['cat']) && $edit['cat'] == 15) echo 'selected'; ?>>Internet-Hébergement-VPN</option>
				<option value="16" id="cat-investissement" <?php if(isset($edit['cat']) && $edit['cat'] == 16) echo 'selected'; ?>>Investissement</option>
				<option value="17" id="cat-jardin-fleurs" <?php if(isset($edit['cat']) && $edit['cat'] == 17) echo 'selected'; ?>>Jardin-Fleurs</option>
				<option value="18" id="cat-jeux-video" <?php if(isset($edit['cat']) && $edit['cat'] == 18) echo 'selected'; ?>>Jeux-vidéo</option>
				<option value="19" id="cat-jeux-dargent" <?php if(isset($edit['cat']) && $edit['cat'] == 19) echo 'selected'; ?>>Jeux d'Argent</option>
				<option value="20" id="cat-loisirs-voyage" <?php if(isset($edit['cat']) && $edit['cat'] == 20) echo 'selected'; ?>>Loisirs-Voyages</option>
				<option value="21" id="cat-matelas-literie" <?php if(isset($edit['cat']) && $edit['cat'] == 21) echo 'selected'; ?>>Matelas-Literie</option>
				<option value="22" id="cat-maison-bricolage" <?php if(isset($edit['cat']) && $edit['cat'] == 22) echo 'selected'; ?>>Maison-Bricolage</option>
				<option value="23" id="cat-missions-sondages" <?php if(isset($edit['cat']) && $edit['cat'] == 23) echo 'selected'; ?>>Missions-Sondages</option>
				<option value="24" id="cat-multimedia-electromenager" <?php if(isset($edit['cat']) && $edit['cat'] == 24) echo 'selected'; ?>>Multimédia-Electroménager</option>
				<option value="25" id="cat-mode-vetements" <?php if(isset($edit['cat']) && $edit['cat'] == 25) echo 'selected'; ?>>Mode-Vêtements</option>
				<option value="26" id="cat-operateursinternet-telephone" <?php if(isset($edit['cat']) && $edit['cat'] == 26) echo 'selected'; ?>>Opérateurs internet-Téléphone</option>
				<option value="27" id="cat-optique" <?php if(isset($edit['cat']) && $edit['cat'] == 27) echo 'selected'; ?>>Optique</option>
				<option value="28" id="cat-photo-impression" <?php if(isset($edit['cat']) && $edit['cat'] == 28) echo 'selected'; ?>>Photo-Impression</option>
				<option value="29" id="cat-rencontre" <?php if(isset($edit['cat']) && $edit['cat'] == 29) echo 'selected'; ?>>Rencontre</option>
				<option value="30" id="cat-sport" <?php if(isset($edit['cat']) && $edit['cat'] == 30) echo 'selected'; ?>>Sport</option>
				<option value="31" id="cat-autre" <?php if(isset($edit['cat']) && $edit['cat'] == 31) echo 'selected'; ?>>Autre</option>
				</select>
		<br />
		<label for="codepromo">Type d'offres </label>
		<?php 
		$tableau = explode(',', $edit['offres']);
		?>
		<table>
			<tr>
				<td style="width:40%">&nbsp;</td>
				<td><label for="offre-codepromo">Code Promo :</label><input id="offre-codepromo" name="offres[]" type="checkbox" value="1" <?php if(isset($tableau) &&  in_array("1",$tableau)) echo 'checked'; ?>><label for="offre-codepromo"><span class="ui"></span></label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="offre-remises">Remises :</label><input id="offre-remises" name="offres[]" type="checkbox" value="2"  <?php if(isset($tableau) &&  in_array("2",$tableau)) echo 'checked'; ?>><label for="offre-remises"><span class="ui"></span></label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="offre-parrainages">Parrainages :</label><input id="offre-parrainages" name="offres[]" type="checkbox" value="3"  <?php if(isset($tableau) &&  in_array("3",$tableau)) echo 'checked'; ?>><label for="offre-parrainages"><span class="ui"></span></label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="offre-coupons">Coupons :</label><input id="offre-coupons" name="offres[]" type="checkbox" value="4"  <?php if(isset($tableau) &&  in_array("4",$tableau)) echo 'checked'; ?>><label for="offre-coupons"><span class="ui"></span></label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="offre-odr">ODR :</label><input id="offre-odr" name="offres[]" type="checkbox" value="5"  <?php if(isset($tableau) &&  in_array("5",$tableau)) echo 'checked'; ?>><label for="offre-odr"><span class="ui"></span></label></td>
			</tr>
		</table>
		<br /><br />
		<label for="description" style="vertical-align:top;">Description :</label>
			<textarea id="description" name="description" rows="8" required><?= (isset($edit['description']) ? str_replace('<br />', '', $edit['description']) : ''); ?></textarea>
		<?=(isset($_SESSION['info']['description']) != '' ? $_SESSION['info']['description'] : '' ); ?><br />
		<label for="foncparrainage" style="vertical-align:top;">Fonctionnement parrainage :</label>
			<textarea id="foncparrainage" style="max-width:700px" name="foncparrainage" rows="8"><?= (isset($edit['foncparrainage']) ?  str_replace('<br />', '', $edit['foncparrainage']) : ''); ?></textarea>
		<?=(isset($_SESSION['info']['foncparrainage']) != '' ? $_SESSION['info']['foncparrainage'] : '' ); ?><br />
		<label for="offre-parrain" style="vertical-align:top;">Offre parrain :</label>
			<textarea id="offre-parrain" name="offre-parrain" rows="3"><?= (isset($edit['offreparrain']) ? str_replace('<br />', '', $edit['offreparrain']) : ''); ?></textarea>
		<?=(isset($_SESSION['info']['offre-parrain']) != '' ? $_SESSION['info']['offre-parrain'] : '' ); ?><br />
		<label for="offre-filleul" style="vertical-align:top;">Offre filleul :</label>
			<textarea id="offre-filleul" name="offre-filleul" rows="3"><?= (isset($edit['offrefilleul']) ? str_replace('<br />', '', $edit['offrefilleul']) : ''); ?></textarea>
        <?=(isset($_SESSION['info']['offre-filleul']) != '' ? $_SESSION['info']['offre-filleul'] : '' ); ?>

		<input type="submit" value="Modifier" class="bouton" name="form_ajouter" style="margin:10px auto; display:block;">
	</form> 

</section>

<script>
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
		value = $("#adr-img").val();
		if(value != "") {
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
			$(this).next().toggle().html('<button type="button" class="vpb-close" onclick="closemodal();"><i class="fas fa-times"></i></button><div class="dialog-open_inner"><div><label for="charg-img">Charger une image</label><div class="pboutonr" id="parcourir-img" onclick="openinputfile();" style="min-width:210px;margin:10px 0;">Parcourir</div></div></div><div class="dialog-open_inner"><div><label for="adr-img">Image depuis une URL</label><input id="adr-imgtemp" name="adr-imgtemp" type="text" style="width:30%;margin:0;" placeholder="http://" <?= (isset($edit['img']) ? 'value="'.$edit['img'].'"' : ''); ?>><div class="pboutonr" id="parcourir-img" onclick="downloadimg();" style="min-width:210px;margin:10px 0;">Envoyer</div></div></div>');
			
			});
	});

	//RECHERCHE MENU INTUITIF
	$(document).ready(function() {
    	$('#nomsite').smartSuggest({
			src: '<?= ROOTPATH; ?>/pages/include/autocompletion_marchands.php',
			fillBox: true,
			fillBoxWith: 'fill_text',
			executeCode: false
		});

});
</script>

<script type="text/javascript" src="<?= ROOTPATH ?>/script/upload_img.js"></script>
	<?php } } } require_once '../../elements/footer.php'; ?>