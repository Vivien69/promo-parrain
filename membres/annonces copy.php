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
$param = "";
$ascdsc = "";
if(isset($_POST['orderparam'])) {

	$order = (int) $_POST['orderparam'];

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
			$param = "ORDER BY AP.dateajout";
		break;
	}
	$sql = $pdo->prepare("UPDATE user SET membre_anorder = :membre_anorder WHERE membre_id = :membre_id");
	$sql->bindParam(":membre_anorder", $order);
	$sql->bindParam(":membre_id", $_SESSION['membre_id']);
	$sql->execute();
}

	if(isset($_POST['sort'])) {

		$sort = (int) $_POST['sort'];

		switch ($sort) {
			case 1:
				$ascdsc = " ASC";
				break;
			case 2:
				$ascdsc = " DESC";
			break;
		}
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
		$titre = 'Annonces de parrainage de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'mesannonces';
		require_once 'includes/menu_membres.php';

		//SUPPRIMER UNE ANNONCE
		if (isset($_GET["del"]) && is_numeric($_GET["del"])) {
			$id = intval($_GET["del"]);

			$sql = "SELECT * FROM annonces_parrainage WHERE id = :id AND idmembre = :idmembre";
			$prepa = $pdo->prepare($sql);
			$prepa->execute(array(":id" => $id, "idmembre" =>  $_SESSION['membre_id']));
			$GLOBALS['nb_req']++;
			if ($prepa->rowcount() == 1) {
				$sql = $pdo->prepare("DELETE FROM annonces_parrainage WHERE id = " . $id);
				$GLOBALS['nb_req']++;
				if ($sql->execute()) {
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

		if(isset($row['membre_anorder'])) {
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
					$param = "ORDER BY AP.dateajout";
				break;
			}
		}
		
			if(isset($row['membre_sort'])) {
		
				$sort = (int) $row['membre_sort'];
		
				switch ($sort) {
					case 1:
						$ascdsc = " ASC";
						break;
					case 2:
						$ascdsc = " DESC";
					break;
				}
				if($param == "") 
					$ascdsc = "";
				
			}

?>

<div class="block_inside">

	<div id="annonces_parrainage">
		<legend>Mes offres de parrainage</legend>
		<a href="<?= ROOTPATH; ?>/parrain/ajouter" id="ajouter_annonce" style="margin-bottom:20px;color:#FFF;width:320px;padding-top:17px;" class="pboutonr">AJOUTER UNE ANNONCE DE PARRAINAGE</a>
		<p><b>Bienvenue <?= $_SESSION['membre_utilisateur'] ?></b> sur la page pour gérer tes offres de parrainage. Tu peux ici modifier tes annonces autant de fois que tu le souhaite ou supprimer une offre.</p>
		<p>Tu peux actualiser tes offres de parrainage 2 fois par jours afin de les faire remonter en tête de liste. La remise à zéro se fait chaque jour à minuit (00h00).</p>
		<!-- ORDRE D'AFFICHAGE MENU DEROULANT -->
		
		

		<form action="" method="post" id="selectparam" name="selectparam" style="text-align:right;padding-right:0;">
		<i class="fas fa-sort-amount-up-alt fa-lg"></i>
			<select id="orderparam" name ="orderparam" style="min-width:150px;width:150px;">
				<option value="">Choix ordre</option>
				<option value="1" <?= ((isset($order) && $order == 1) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 1) ) ? 'selected' : ''; ?> >Date d'ajout</option>
				<option value="2" <?= ((isset($order) && $order == 2) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 2) ) ? 'selected' : ''; ?>>Alphabétique</option>
				<option value="3" <?= ((isset($order) && $order == 3) ? 'selected' : (isset($row['membre_anorder']) && $row['membre_anorder'] == 3) ) ? 'selected' : ''; ?>>Dernière actualisation</option>
			
			</select>
			<input type="radio" id="sort1" name="sort" value="1" style="display:none;" <?= ((isset($sort) && $sort == 1) ? 'checked' : (isset($row['membre_sort']) && $row['membre_sort'] == 1) ) ? 'checked' : ''; ?>/>
			<input type="radio" id="sort2" name="sort" value="2" style="display:none;" <?= ((isset($sort) && $sort == 2) ? 'checked' : (isset($row['membre_sort']) && $row['membre_sort'] == 2) ) ? 'checked' : ''; ?>/>
			<a href="" id="sorta"><i id="sort" class="fas fa-sort fa-lg"></i></a>
		</form>
		<br />

		<script>
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

		<?php
		$sql = "SELECT AP.id, AP.idmarchand, AP.idmembre, AP.montantremise, AP.montantdevise, AP.montantachatminimal, AP.description, AP.dateajout, AP.actualisation, M.img, M.nom_marchand, M.cat 
		FROM annonces_parrainage AP
		LEFT JOIN marchands M ON AP.idmarchand = M.id
		WHERE idmembre = :idmembre
		".$param.$ascdsc;
		
		$prepare = $pdo->prepare($sql);
		$prepare->execute(array("idmembre" => $_SESSION['membre_id']));
		$GLOBALS['nb_req']++;
		if ($prepare->rowcount() > 0) {
			while ($result = $prepare->fetch(PDO::FETCH_ASSOC)) {
				echo '<div class="box-annonce2" style="position: relative;">
						<div style="align-items:flex-start;width:20%;min-width:160px;max-width:180px;"><a href="'.ROOTPATH.'/'.format_url(find_categorie($result['cat'])).'-'.$result['cat'].'/'.format_url($result['nom_marchand']).'-'.$result['idmarchand'].'/parrainage"><span style="color:#701818;">' . $result['nom_marchand'] . '</span><div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $result['img'] . '\');background-size:140px;"></div></a><br /><span id="dateactu" style="font-size:10px;">' . mepd($result['dateajout']) . '</span> - <div style="font-size:11px;display:inline-block;" id="refresh"> <i class="fas fa-sync"></i></div> <span style="font-size:10px;" id="haha"> ' . $result['actualisation'] . '</span></div>
						<div style="word-wrap:break-word;width:75%;"><span class="hide-media">' . substr($result['description'], 0, 250) . '</span>
							<div class="actu-div" style="bottom: 10px;left:50%;"><a href="javascript:void(0);" onclick="actu(this,' . $result['id'] . ',' . $_SESSION['membre_id'] . ')" class="pboutonv" id="actualiser" style="margin: 10px auto;font-size: 17px; padding: 10px 20px 7px; color: #ffffff;" title="Remonter en tête de liste mon annonce">Actualiser <i style="margin-left:7px;" class="fas fa-sync"></i></a></div>
							<div class="statsannon">
								<span><i class="fas fa-euro-sign fa-lg"></i> ' . $result['montantremise'] . $result['montantdevise'] . ' ' . ($result['montantachatminimal'] != '' ? ' dès ' . $result['montantachatminimal'] . '€ d\'achats' : '') . '</span>
								<span><i class="fas fa-chart-line fa-lg"></i> ' .lirecompteur($result['id']). ' Vues</span>			
							</div>
							</div>
						
						<div style="align-items: flex-end;width:20%;min-width:130px;"><a class="pboutonr" style="font-size: 17px; padding: 10px 15px 7px; margin-bottom:10px;width:95px;" title="Voir l\'annonce" href="' . ROOTPATH . '/parrain/' . strtolower(format_url($result['nom_marchand'])) . '-' . $result['id'] . '">Voir <i style="margin-left:7px;" class="fas fa-search"></i></a><br />
						<a class="pboutono" style="font-size: 17px; padding: 10px 15px 7px; color: #ffffff;margin-bottom:10px;width:95px;" href="'.ROOTPATH.'/parrain/editer/'.$result['id'].'" title="Editer l\'annonce">Editer <i style="margin-left:7px;" class="far fa-edit"></i></a><br />
						<a class="pboutonblanc" style="font-size: 17px; padding: 10px 15px 7px;width:95px;" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette annonce ?\')) return false;" title="Supprimer l\'annonce" href="?del=' . $result['id'] . '">Supprimer <i style="margin-left:7px;" class="far fa-trash-alt"></i></a></div>
						</div>
					<br />';
			}
		} else {
			echo '<br /><div class="box_annonces"><p>Vous n\'avez actuellement aucune annonce de parrainage.</p></div>';
		}

		?>
	</div>

</div>


<?php }
}
require_once '../elements/footer.php'; ?>

<script>
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
					$(nana).empty().append('<div class="valider">L\'annonce a été actualisée.</div>');
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
</script>