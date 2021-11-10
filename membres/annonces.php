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
		' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
		ROOTPATH,
		20
	);
	require_once('../information.php');
	exit();
}
if(isset($_POST['orderparam'])) {

	$order = (int) $_POST['orderparam'];
	$sql = $pdo->prepare("UPDATE user SET membre_anorder = :membre_anorder WHERE membre_id = :membre_id");
	$sql->bindParam(":membre_anorder", $order);
	$sql->bindParam(":membre_id", $_SESSION['membre_id']);
	$sql->execute();
}

if(isset($_POST['sort'])) {
	$sort = (int) $_POST['sort'];
	$sql = $pdo->prepare("UPDATE user SET membre_sort = :membre_sort WHERE membre_id = :membre_id");
	$sql->bindParam(":membre_sort", $sort);
	$sql->bindParam(":membre_id", $_SESSION['membre_id']);
	$sql->execute();
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

		$title = 'Annonces de parrainage de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'mesannonces';
		require_once 'includes/menu_membres.php';


		//SUPPRIMER UNE ANNONCE
		if (isset($_GET["del"]) && is_numeric($_GET["del"])) {
			$id = intval($_GET["del"]);
			
			if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] = 'connecter') 
			$sql = "SELECT * FROM annonces_parrainage WHERE id = :id";
			else
			$sql = "SELECT * FROM annonces_parrainage WHERE id = :id AND idmembre = ".$_SESSION['membre_id'];

			$prepa = $pdo->prepare($sql);
			$prepa->execute(array(":id" => $id));
			$GLOBALS['nb_req']++;
			if ($prepa->rowcount() == 1) {
				$sql = $pdo->prepare("DELETE FROM annonces_parrainage WHERE id = " . $id);
				$GLOBALS['nb_req']++;
				$idmarchand = $prepa->fetch();
				
				//On ajout l'annonce supprimée dans la table an_deleted afin de sécuriser suite a abus : suppression/ajout d'annonce pour forcer l'actualisation. 
				$sql2= "INSERT INTO an_deleted (id_membre, id_marchand, date) VALUES (:id_membre, :id_marchand, :date)";
				$sqladd = $pdo->prepare($sql2);
				$sqladd->bindParam(":id_membre", $_SESSION['membre_id'],PDO::PARAM_INT);
				$sqladd->bindParam(":id_marchand", $idmarchand['idmarchand'],PDO::PARAM_INT);
				$sqladd->bindValue(":date", time(),PDO::PARAM_INT);
				

				if ($sqladd->execute() && $sql->execute()) {
					echo '<div class="valider">L\'annonce a été supprimée.</div>';
					unset($_GET);
				}

			} else {
				echo '<div class="erreur">L\'annonce n\'existe pas dans notre base de donnée !</div>';
				$_GET["del"] = '';
				$_GET['nomsite'] = '';
			}
		}

	

		if (isset($_GET['edited']))
		echo '<div class="valider">Votre annonce de parrainage '.strip_tags($_GET['edited']).' a bien été modifiée. </div>';

		//Menu déroulant trier par date, alphabétique, actualisation, nombre de vues
		if(isset($row['membre_anorder']) OR $row['membre_anorder'] == NULL) {
			$param = "";
			$ascdsc = "";
			$order = $row['membre_anorder'];
			
			switch ($order) {
				case '':
					$param = "";
					$ascdsc = "";
				break;
				case 1:
					$param = "ORDER BY AP.dateajout";
					break;
				case 2:
					$param = "ORDER BY M.nom_marchand";
				break;
				case 3:
					$param = "ORDER BY AP.actualisation";
				break;
				case 4:
					$param = "ORDER BY AP.vues";
				break;
			}
		}
			
			if(isset($row['membre_sort'])) {
		
				$sort = $row['membre_sort'];
				
				switch ($sort) {
					case 1:
						$ascdsc = " ASC";
						break;
					case 2:
						$ascdsc = " DESC";
					break;
				}
			}
			
			if($param == "") 
				$ascdsc = "";		
					
?>


<div class="block_inside">

	<div id="annonces_parrainage">
		<legend>Mes offres de parrainage</legend>
		<a href="<?= ROOTPATH; ?>/parrain/ajouter" id="ajouter_annonce" style="margin-bottom:20px;color:#FFF;width:320px;padding-top:17px;" class="pboutonr">AJOUTER UNE ANNONCE DE PARRAINAGE</a>
		
		<p style="text-align:left;"><b>Si tu souhaite devenir un parrain </b>tu peux ici ajouter et gérer tes offres de parrainage. Tu peux modifier tes offres autant de fois que tu le souhaite ou supprimer une offre.<br />
		Tu peux actualiser tes offres de parrainage <b>2 fois par jour</b> afin de les faire remonter en tête de liste. La remise à zéro se fait chaque jour à minuit (00h00).<br />
		Tu peux également désactiver une annonce pour qu'elle n'apparaisse plus dans la liste des marchands. Utile lorsque tu as atteint ton quota de parrainage.
	</p>
		<!-- ORDRE D'AFFICHAGE MENU DEROULANT -->
		
		
		<div style="float:left;margin-top:7px">
			<button id="aflist" title="Affichage sous forme de liste"><i class="fas fa-bars fa-2x"></i></button> 
			<button id="aftabl" title="Affichage sous forme de tableau"><i class="fas fa-th fa-2x"></i></button> 
		</div>
		<form action="" method="post" id="selectparam" name="selectparam" style="text-align:right;padding-right:0;">
		<i class="fas fa-sort-amount-up-alt fa-2x"></i>
			<select id="orderparam" name ="orderparam" style="min-width:200px;width:200px;">
				<option value="">Choix ordre</option>
				<option value="1" <?= ((isset($order) && $order == 1) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 1) ) ? 'selected' : ''; ?> >Date d'ajout</option>
				<option value="2" <?= ((isset($order) && $order == 2) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 2) ) ? 'selected' : ''; ?>>Alphabétique</option>
				<option value="3" <?= ((isset($order) && $order == 3) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 3) ) ? 'selected' : ''; ?>>Dernière actualisation</option>
				<option value="4" <?= ((isset($order) && $order == 4) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 4) ) ? 'selected' : ''; ?>>Vues</option>
			
			</select>
			<input type="radio" id="sort1" name="sort" value="1" style="display:none;" <?= ((isset($sort) && $sort == 1) ? 'checked' : (isset($row['membre_sort']) && $row['membre_sort'] == 1) ) ? 'checked' : ''; ?>/>
			<input type="radio" id="sort2" name="sort" value="2" style="display:none;" <?= ((isset($sort) && $sort == 2) ? 'checked' : (isset($row['membre_sort']) && $row['membre_sort'] == 2) ) ? 'checked' : ''; ?>/>
			<a href="" id="sorta"><i id="sort" class="fas fa-sort fa-lg"></i></a>
		</form>
		<div id="putcolumn">
		<br />

		
		<?php

		$sql = "SELECT AP.id, AP.idmarchand, AP.idmembre, AP.vues, AP.etatvalidation, M.montantremise, M.montantdevise, M.montantachatminimal, M.choixoffre, M.actif, AP.description, AP.dateajout, AP.actualisation, M.img, M.nom_marchand, M.cat 
		FROM annonces_parrainage AP
		LEFT JOIN marchands M ON AP.idmarchand = M.id
		WHERE AP.idmembre = :idmembre
		".$param.$ascdsc;
		
		$prepare = $pdo->prepare($sql);
		$prepare->execute(array("idmembre" => $_SESSION['membre_id']));
		$GLOBALS['nb_req']++;
		if ($prepare->rowcount() > 0) {
			$results = $prepare->fetchAll(PDO::FETCH_ASSOC);
			
			$sql = $pdo->query("SELECT id FROM annonces_parrainage");
			$sql->execute();
			$rowsorder = $sql->fetchAll();
			
			foreach ($results as $result) {

				echo '<div class="box-annonce2" style="position: relative;'.(($result['etatvalidation'] == 2 OR $result['etatvalidation'] == 3) ? 'opacity:0.5' : '').'">
					<div style="align-items:flex-start;width:20%;min-width:160px;max-width:180px;">'.($result['actif'] == 0 ? '<p class="desactivate"> PARRAINAGE DESACTIVE</p>' : '').'<a href="'.ROOTPATH.'/'.format_url(find_categorie($result['cat'])).'-'.$result['cat'].'/'.format_url($result['nom_marchand']).'-'.$result['idmarchand'].'/parrainage"><span style="color:#701818;">' . $result['nom_marchand'] . '</span><div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $result['img'] . '\');background-size:140px;"></div></a><br /><span id="dateactu" style="font-size:10px;">' . mepd($result['dateajout']) . '</span> - <div style="font-size:11px;display:inline-block;" id="refresh"> <i class="fas fa-sync"></i></div> <span style="font-size:10px;" id="haha"> ' . $result['actualisation'] . '</span></div>
						<div class="hideit" style="word-wrap:break-word;width:75%"><span class="hide-media">' . substr($result['description'], 0, 250) . '</span>
							<div class="actu-div" style="bottom: 10px;left:40%;"><a href="javascript:void(0);" onclick="actu(this,' . $result['id'] . ',' . $_SESSION['membre_id'] . ')" class="pboutonv" id="actualiser" style="margin: 10px auto;font-size: 17px; padding: 10px 20px 7px; color: #ffffff;" title="Remonter en tête de liste mon annonce">Actualiser <i style="margin-left:7px;" class="fas fa-sync"></i></a></div>
							<div class="statsannon">
								<span>'.(($result['choixoffre'] == 1) ? '<i class="fas fa-euro-sign fa-lg"></i> '.$result['montantremise'].$result['montantdevise'] : (($result['choixoffre'] == 2) ? '<i style="margin-right:5px" class="fas fa-truck"></i>Livraison offerte' : (($result['choixoffre'] == 3) ? '<i style="margin-right:5px" class="fas fa-gift"></i>Cadeau offert' : '<i class="fas fa-euro-sign fa-lg"></i> '.$result['montantremise'].$result['montantdevise']))).' ' . ($result['montantachatminimal'] != '' ? ' dès ' . $result['montantachatminimal'] . '€ d\'achats' : '') . '</span>
								<span><i class="fas fa-chart-line fa-lg"></i> ' .$result['vues']. ' Vues</span>			
							</div>
						</div>
						
						<div class="hideit" style="align-items: flex-end;width:20%;min-width:150px;max-width:280px;">
							<form action="?desactiver='.$result['id'].'" class="formactif" name="form_ajouter" type="GET">
							<label style="padding:0;margin:0 0 0 40px;width:110px;" for="desactiver'.$result['id'].'"></label>
							<input onclick="if(!confirm(\'Etes vous sûr de vouloir '.(($result['etatvalidation'] == 2 OR $result['etatvalidation'] == 3) ? 'activer' : 'désactiver').' votre annonce '.$result['nom_marchand'].' ?\')) return false;" id="desactiver'.$result['id'].'" name="desactiver" type="checkbox" value="'.$result['id'].'"  '.(($result['etatvalidation'] == 2 OR $result['etatvalidation'] == 3) ? 'checked' : '').'><label style="margin-top:-7px;margin-left:-70px;" for="desactiver'.$result['id'].'"><span class="ui"></span></label>
							</form>
							<a class="pboutonr" style="font-size: 17px; padding: 7px 15px 4px; margin-bottom:5px;margin-top:5px;width:110px;" title="Voir l\'annonce" href="' . ROOTPATH . '/parrain/' . strtolower(format_url($result['nom_marchand'])) . '-' . $result['id'] . '">Voir <i style="margin-left:7px;" class="fas fa-search"></i></a><br />
							<a class="pboutono" style="font-size: 17px; padding: 7px 15px 4px; color: #ffffff;margin-bottom:5px;width:110px;" href="'.ROOTPATH.'/parrain/editer/'.$result['id'].'" title="Editer l\'annonce">Editer <i style="margin-left:7px;" class="far fa-edit"></i></a>
							<a class="pboutonblanc" style="font-size: 17px; padding: 7px 15px 4px;width:110px;" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette annonce ?\')) return false;" title="Supprimer l\'annonce" href="?del=' . $result['id'] . '">Supprimer <i style="margin-left:7px;" class="far fa-trash-alt"></i></a>
							</div>
					</div>
						<br />';
			}
		} else {
			echo '<br /><div class="box_annonces"><p>Vous n\'avez actuellement aucune annonce de parrainage.</p></div>';
		}

		?>
	</div>
	</div>

</div>


<?php }
}
require_once '../elements/footer.php'; ?>

<script>
	
	var aflist = document.getElementById('aflist');
	var aftabl = document.getElementById('aftabl');
	var hideit = document.getElementsByClassName('hideit');
	var column = document.getElementById('putcolumn');
	var boxannonce = document.getElementsByClassName('box-annonce2');

	list = [aflist, aftabl]
	list.forEach(element => {
		
		element.addEventListener('click', function() {
			console.log(element)
			if(element == aftabl) {
				for (var i = 0, max = hideit.length; i < max; i++) {
					hideit[i].style.display = "none";
				}
				column.style.display = 'flex';
				column.style.flexdirection = 'column';
				column.style.flexWrap = 'wrap';
				column.style.justifyContent = 'center';
				Array.from(boxannonce).forEach(element => {
				element.style.padding = "10px 0px 10px 10px";
				element.style.margin = "0 15px 15px 0";
				element.style.border = "0";
			});

			} if(element == aflist) {
				for (var i = 0, max = hideit.length; i < max; i++) {
					hideit[i].style.display = "block";
				}

				column.style.display = 'block';
				column.style.flexdirection = 'row';
				column.style.flexWrap = 'wrap';
				column.style.justifyContent = 'left';
				Array.from(boxannonce).forEach(element => {
				element.style.padding = "15px 7px";
				element.style.margin = "0";
				element.style.border = "1px solid #DBDBDB";
			});
	}
		});
		return false;
	});

var desactiver = document.querySelectorAll('input[type=checkbox]');


desactiver.forEach(function(element) {

	element.addEventListener('change', function() {
	info = element.value;

	fetch('<?= ROOTPATH ?>/membres/includes/desactive.php?desactive='+info, {
    method : "GET",
    // -- or --
    // body : JSON.stringify({
        // user : document.getElementById('user').value,
        // ...
    // })
}).then((response) => response.json())
.then((responseData) => {
      if(responseData.status == "desactivate")
		element.parentNode.parentNode.parentNode.style.opacity = '1'
		if(responseData.status == "activate")
		element.parentNode.parentNode.parentNode.style.opacity = '0.5'
      
});

});
});
	function actu(item, idannonce, idmembre) {
		dada = $(item).parent().parent().parent();
		nana = $(item).parent();
		$(dada).find("div#refresh").addClass('rotating');
		$.ajax({
			type: "POST",
			cache: false,
			url: "includes/actu-ajax.php",
			data: {
				'idannonce': idannonce,
				'idmembre': idmembre
			},
			dataType: 'json',
			success: function(msg) { // si l'appel a bien fonctionné
				if (msg.etat == 'ok') { // si la connexion en php a fonctionnée
					$(nana).empty().append('<div class="valider"><b>L\'annonce a été actualisée.</b></div>');
					$(dada).find("span#haha").empty().append('<b>' + msg.actu + '<b>').hide().fadeIn(2000);
				} else if (msg.etat == 'limit') // si la connexion en php a fonctionnée
					$(nana).empty().append('<div class="erreur">2 actualisations maximum par jour</div>');
				else if (msg.etat == 'limit')
					$(nana).empty().append('<div class="erreur">L\'annonce n\'existe pas dans notre base de donnée !</div>');
				else
					$(nana).empty().append('<div class="erreur">Erreur</div>');

				// on affiche un message d'erreur dans le span prévu à cet effet
			}
		});
		return false;
		// permet de rester sur la même page à la soumission du formulaire*/
	}
	
			var orderparam = document.getElementById('orderparam');
			var selectparam = document.getElementById('selectparam');
			orderparam.addEventListener('change', (event) => {
				
				selectparam.submit();
				
				
			});

			var sorta = document.getElementById('sorta');
			var sort1 = document.getElementById('sort1');
			var sort2 = document.getElementById('sort2');

			sorta.addEventListener('click', (event) => {
				if(sort1.checked == true) {
					sort1.checked = false;
					sort2.checked = true;
				}else if(sort2.checked == true) {
					sort2.checked = false;
					sort1.checked = true
				}
				selectparam.submit();
				
				event.preventDefault();
			});
		

</script>