<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Marchands';
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
        ' - <a href="' . ROOTPATH . '/connexion">Se connecter</a>',
        ''. ROOTPATH,
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
					$_SESSION['info']['uploadfile'] = '<div class="erreurform">Vous n\'avez pas ajouter de logo</div>';
					$_SESSION['form']['uploadfile'] = '';
					$erreurs++;
				}
			}
			if(isset($_POST['adr-img']) && $_POST['adr-img'] != "") {
				$donnee  = trim($_POST['adr-img']);
				$result =  $donnee;
				if($result == 'errorfile') {
					$_SESSION['info']['adr-img'] = '<div class="erreurform">Erreur lors du téléchargement de l\'image</div>';
					$_SESSION['form']['adr-img'] = '';
				} else {
					$_SESSION['info']['adr-img'] = '';
					$_SESSION['form']['adr-img'] = $result;
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
				$ladate = time();
				
				$sql = "INSERT INTO marchands (nom_marchand, url_marchand, img, cat, offres, description, foncparrainage, offreparrain, offrefilleul, dateajout) 
				VALUES (:nom_marchand, :url_marchand, :img, :cat, :offres, :description, :foncparrainage, :offreparrain, :offrefilleul, :dateajout)";
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
				$sqlbind->bindParam(':dateajout', $ladate, PDO::PARAM_INT);
	
				if ($sqlbind->execute()) {
					echo '<div class="valider">Le marchand <b>'.$_SESSION['form']['nom'].'</b> a été ajouté.</div>';
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
	//SUPPRIMER LE MARCHAND
	if (isset($_GET["del"]) && intval($_GET["del"])) {
		$id = $_GET["del"];
		$marchand = $_GET["nomsite"];
		
		$sql = "SELECT * FROM marchands WHERE id = ".$id;
		$prepa = $pdo->prepare($sql);
		$GLOBALS['nb_req']++;
		$prepa->execute();
		if($prepa->rowcount() == 1) {
			$sql = $pdo->prepare("DELETE FROM marchands WHERE id = ".$id);
			$GLOBALS['nb_req']++;
			if ($sql->execute()) {
				echo '<div class="erreur">Le marchand <b>'.$marchand.'</b> a été supprimée.</div>';
				unset($_GET); 
			}
		}else {
			echo '<div class="erreur">Ce marchand n\'existe pas dans notre base de donnée !</div>';
			$_GET["del"] = '';
			$_GET['nomsite'] ='';
		}
	}
	//VALIDER LE MARCHAND
	if (isset($_GET["valider"]) && intval($_GET["valider"])) {
		$id = $_GET["valider"];
		$marchand = $_GET["nomsite"];
		$idmembreadd = $_GET["membre"];
		
		$sql = "SELECT * FROM marchands WHERE id = ".$id;
		$prepa = $pdo->prepare($sql);
		$GLOBALS['nb_req']++;
		$prepa->execute();
		if($prepa->rowcount() == 1) {
			$sql = $pdo->prepare("UPDATE marchands SET etat = 1 WHERE id = ".$id);
			$GLOBALS['nb_req']++;
			if ($sql->execute()) {
				checkHowManyEntry($idmembreadd, 5, 'marchands', 'idmembre'); //5 : Ajout d'un marchand
				echo '<div class="valider">Le marchand <b>'.$marchand.'</b> a été validé.</div>';
				unset($_GET); 
			}
		}else {
			echo '<div class="erreur">Ce marchand n\'existe pas dans notre base de donnée !</div>';
			$_GET["del"] = '';
			$_GET['nomsite'] ='';
		}
	}
    ?>
<section class="block_inside">
	<?= (isset($_SESSION['nb_erreurs']) && $_SESSION['nb_erreurs'] != "") ? $_SESSION['nb_erreurs'] : ''; ?>
	<h1>Les marchands : </h1>
	<p>Il y a actuellement <b><?= nombremarchands().' marchands'; ?></b> dans la base de donnée</p>
<h2>Marchands à valider :</h2>
	<form action="<?= ROOTPATH.'/includes/upload-img.php'; ?>" method="post" name="image_upload" id="image_upload" enctype="multipart/form-data"> 
        <input type="file" style="display:none;" size="45" name="uploadfile" id="uploadfile" onchange="vpb_upload_and_resize();" />
	</form>
	<div class="flex">
		<?php
			$sql = $pdo->prepare("SELECT * FROM marchands WHERE etat = 0");
			$sql->execute();
			$GLOBALS['nb_req']++;
			$fetch = $sql->fetchAll();
			foreach($fetch as $row) {
				echo '<div class="box-annonce3" style="align-items:center;margin:0 10px 10px 0;">
				<div style="align-items:flex-start;width:17%;min-width:160px;"><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['id'].'/parrainage">' . $row['nom_marchand'] . '</a><div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'] . '\');background-size:140px;"></div></div>
				<div style="align-items: flex-end;width:18%;min-width:130px;">
					<a class="pboutonr" style="font-size: 17px; padding: 10px 15px 7px; margin-bottom:10px;width:95px;" title="Voir le marchand" href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['id'].'/parrainage">Voir <i style="margin-left:7px;" class="fas fa-search"></i></a><br />
					<a class="pboutonv" style="font-size: 17px; padding: 10px 15px 7px; margin-bottom:10px;width:95px;color:#FFF" title="Validerle marchand" href="liste-marchands?valider='.$row['id'].'&membre=' . $row['idmembre'] . '&nomsite='.$row['nom_marchand'].'">Valider <i style="margin-left:7px;" class="fas fa-check"></i></a><br />
					<a class="pboutono" style="font-size: 17px; padding: 10px 15px 7px; color: #ffffff;margin-bottom:10px;width:95px;" href="marchands/edit/'.$row['id'].'" title="Editer l\'annonce">Editer <i style="margin-left:7px;" class="far fa-edit"></i></a><br />
					<a class="pboutonblanc" style="font-size: 17px; padding: 10px 15px 7px;width:95px;" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette annonce ?\')) return false;" title="Supprimer l\'annonce" href="liste-marchands?del='.$row['id'].'&nomsite='.$row['nom_marchand'].'">Supprimer <i style="margin-left:7px;" class="far fa-trash-alt"></i></a>
				</div>
				
				
			</div>';
			
			}

		?>
	</div>
	<form action="<?= ROOTPATH.'/membres/admin/liste-marchands'; ?>"  method="POST" style="text-align:left;" id="form_ajouter" name="form_ajouter">
		<label for="nomsite">Marchand :</label><input id="nomsite" name="nomsite" type="text" placeholder="ex: Cdiscount" <?= (isset($_SESSION['form']['nomsite']) ? 'value="'.$_SESSION['form']['nomsite'].'"' : ''); ?> required>
		<input id="idm" name="idm" style="visibility:hidden;height:0px;" type="text" <?= (isset($_SESSION['form']['idm']) ? 'value="'.$_SESSION['form']['idm'].'"' : ''); ?>>
		<div id="edit_marchand"></div>
	</form>
	<br />
	
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
			$(this).next().toggle().html('<button type="button" class="vpb-close" onclick="closemodal();"><i class="fas fa-times"></i></button><div class="dialog-open_inner"><div><label for="charg-img">Charger une image</label><div class="pboutonr" id="parcourir-img" onclick="openinputfile();" style="min-width:210px;margin:10px 0;">Parcourir</div></div></div><div class="dialog-open_inner"><div><label for="adr-img">Image depuis une URL</label><input id="adr-imgtemp" name="adr-imgtemps" type="text" style="width:30%;margin:0;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : ''); ?>><div class="pboutonr" id="parcourir-img" onclick="downloadimg();" style="min-width:210px;margin:10px 0;">Envoyer</div></div></div>');
		});
	});

	//RECHERCHE MENU INTUITIF
	$(document).ready(function() {
    	$('#nomsite').smartSuggest({
			src: '../../pages/include/autocompletion_marchands.php',
			fillBox: true,
			fillBoxWith: 'fill_text',
			executeCode: false
		});

	$('#nomsite-suggestions').on('click', function() {
		value = $("#idm").val();
		nom = $("#nomsite").val();
		$("#edit_marchand").append('<a href="../admin/marchands/edit/'+value+'">MODIFIER</a> - <a href="../membres/admin/liste-marchands.html?del='+value+'&nomsite='+nom+'">SUPPRIMER</a>');
	});

/*	$('#nomsite-suggestions').on('click', function() {
		value = $("#idm").val();

		$.ajax({
				type : "GET",
				dataType: "json",
				url : "includes/edit_marchands.php?idm="+value,
				error : function() {
					alert("Error !: " + msg);
				},
				success : function(response) {
				var len = response.length;
            	for(var i=0; i<len; i++){
                var id = response[i].id;
                var nom_marchand = response[i].nom_marchand;
                var url_marchand = response[i].url_marchand;
				var img = response[i].img;
				var cat = response[i].cat;
				var offres = response[i].offres;
				var description = response[i].description ;
				var offreparrain = response[i].offreparrain;
				var offrefilleul = response[i].offrefilleul;
				}
				$("#nom").val(nom_marchand);
				$("#url").val(url_marchand);
				$("#adr-img").val(img);
				$("#description").val(description);
				$("#offre-parrain").val(offreparrain);
				$("#offre-filleul").val(offrefilleul);
				}
			});
	});*/
});
</script>

<script type="text/javascript" src="<?= ROOTPATH ?>/script/upload_img.js"></script>
	<?php } } require_once '../../elements/footer.php'; ?>