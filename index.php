<?php
require_once 'includes/config.php';
require_once 'includes/function.php';
require_once 'elements/header.php';

 ?>
	<meta name="description" content="Plateforme communautaire entre parrains et filleuls pour s'échanger les meilleurs bons plans et trouver des codes promos de parrainage sur Promo-Parrain.com ">
	<meta name="keywords" content="code, promo, parrain, parrainage, réduction, promotionnel, economisez, gagner, argent, filleul">
	<link rel="canonical" href="https://www.promo-parrain.com" />
	<meta property="og:site_name" content="Promo-Parrain.com"/>
	<meta property="og:title" content="Promo-Parrain.com : Code promo de parrainage et codes de réduction"/> 
	<meta property="og:url" content="https://www.promo-parrain.com"/> 
	<meta property="og:image" content="https://www.promo-parrain.com/images/logo.png"/> 
	<meta property="og:type" content="website"/>
	
<?php
$title= "Code promo et Parrainage, trouvez votre réduction grâce à un parrain";
require_once 'elements/header2.php';
?>
<section class="block_inside">
							    
	<div id="flex" style="align-items: flex-start;">
		<article class="columnflex">
		<h1 class="iconic alaune">Code promo et parrainage</h1>
			<p>Gagnez des €uros en trouvant facilement un parrain sur le marchand de votre choix ou proposez vos offres et vos services de parrainage pour economiser de l'argent.</p><br />
		
		<h2>L'expert du Parrainage</h2>
			<p>Sur Promo-Parrain.com déposer votre offre de parrainage pour la boutiques en ligne de votre choix et trouvez de nombreux filleuls qui vous feront gagner de l'argent. Filleuls trouvez votre parrain et gagnez également de l'argent sur votre 1er achat. <br/>
			Promo-parrain est une solution gagnant sur les boutiques en ligne et les sites de cashback, missions... Profitez en dès à présent. 
			</p>
			
		<?php

			

			$prep = $pdo->prepare('SELECT COUNT(AP.id) AS nb, AP.id, AP.idmarchand, M.nom_marchand, M.id as idm, M.img, M.cat, CP.nom_categorie, CP.id AS idcat FROM marchands M
									LEFT JOIN annonces_parrainage AP ON AP.idmarchand = M.id
									LEFT JOIN categories_principales CP ON M.cat = CP.id
									GROUP BY AP.idmarchand
									ORDER BY nb DESC
									LIMIT 0,10
								');
			$prep->execute();
			$results = $prep->fetchAll(PDO::FETCH_ASSOC);

			foreach($results as $row){
				$nombre = $row['nb'] / 2;
				echo '
					<a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idm'].'/parrainage"><div class="presentation-categories">
					<p>'.$row['nom_marchand'].'</p>
						<img class="item-img-solo" style="border:none;background-image:url(\''.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'\')" />
						<p>'.$nombre.' parrain'.($nombre > 1 ? 's' : '').'</p>
					</div></a>';
			}

		?>
	<br /><a class="pboutonr" href="<?= ROOTPATH ?>/liste-marchands" title="Liste des marchands proposant une offre parrainages">Liste complète des magasins </a>
	<br /><br /><br />
	<p style="text-align:left;background-color:#F8F8F8;padding:20px"><b>Mise à jour du 17/10/2021 :</b><br />
			- Possiblité pour le filleul d'annuler un parrainage lancé si l'inscription sur le site marchand ne se fait pas.<br />
			- Possibilité pour parrain et filleul d'archiver un parrainage annulé! Cela pour mieux trier sa page de parrainages en cours. <br />
			- Message lors du changement du statut d'un parrainage. <br /><br />
	Des suggestions ? <a href="<?= ROOTPATH ?>/contact.html" title="Contactez-nous" >N'hésitez pas à nous en faire part. </a></p>
	<br /><br />
		<h3>Nouveaux codes promo de parrainage</h3>
		<br />
		<?php
			$sql = "SELECT AP.id, AP.idmembre, M.img, M.montantremise, M.montantdevise, AP.description, M.choixoffre, AP.dateajout, M.id as idmarchand, M.nom_marchand, M.cat, M.img, U.membre_utilisateur, U.conf_datemask, I.id_membre, I.image, I.type
			FROM annonces_parrainage AP
			LEFT JOIN user U ON U.membre_id = AP.idmembre
			LEFT JOIN marchands M ON AP.idmarchand = M.id
			LEFT JOIN images I ON AP.idmembre = I.id_membre AND I.type = 'avatar'
			WHERE AP.etatvalidation = 1
			ORDER BY AP.dateajout DESC
			LIMIT 0,5";
	$prep = $pdo->prepare($sql);
	$prep->execute();
	$GLOBALS['nb_req']++;
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
	echo '<div class="box-annonce" style="align-items:center;">
			<div style="align-items:flex-start;width:17%;min-width:160px;max-width:180px"><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage">' . $row['nom_marchand'] . '<div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'] . '\');background-size:140px;"></div></a></div>
			<div class="box-mid"><h6 class="titre-annonce">Offre de parrainage '.$row['nom_marchand'].' de ' . $row['membre_utilisateur'] . '</h6><span style="display:block;margin-top:-2px;font-size:10px;font-family:Verdana;color:#333333;margin-bottom:10px;">Publiée '.(isset($row['conf_datemask']) && $row['conf_datemask'] == 1 ? mepd($row['dateajout'], 1) : mepd($row['dateajout'])).'</span><span id="descrip-annonce">' . mb_substr($row['description'], 0, 200) . '</span></div>
			<div style="width:15%;"><a href="' . ROOTPATH . '/profil/'.$row['idmembre'].'"><img alt="Code promo de Parrainage de ' . $row['membre_utilisateur'] . '" style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['image']) ? $row['image'] : '/default_avatar.png').'" class="avatar" /> <span style="font-weight:bold"><br />' . $row['membre_utilisateur'] . '</a></div>
			<div class="box-view"><span style="font-weight:bold;font-size:16px">'.(($row['choixoffre'] == 1) ? $row['montantremise'].$row['montantdevise'] : (($row['choixoffre'] == 2) ? '<i style="margin-right:5px" class="fas fa-truck"></i>Livraison offerte' : (($row['choixoffre'] == 3) ? '<i style="margin-right:5px" class="fas fa-gift"></i>Cadeau offert' : $row['montantremise'].$row['montantdevise']))).'</span><br /><br /><a class="pboutonr" style="font-size:17px;padding:10px 15px 7px;" href="' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '" title="Voir l\'annonce">Voir le parrainage</a></div>
		</div>
		<br />';
	}
	?>

		</article>


		<!-- MENU DROIT -->

		<article class="rightcontext">
			<aside class="aff_droit aff_droitindex">
			<span>DERNIERS MARCHANDS</span>
				<div class="gridindex">
				<?php
		$prep = $pdo->prepare('SELECT COUNT(DISTINCT AP.id) AS nb, M.id as idmarchand, M.nom_marchand, M.img, M.cat, CP.nom_categorie, M.dateajout, CP.id AS idcat FROM marchands M
								LEFT JOIN annonces_parrainage AP ON AP.idmarchand = M.id
								LEFT JOIN categories_principales CP ON M.cat = CP.id
								GROUP BY M.id
								ORDER BY M.dateajout DESC
								LIMIT 0,6');
		$prep->execute();
		$results = $prep->fetchAll(PDO::FETCH_ASSOC);
		//echo print_r($results);
		foreach($results as $row){
			echo '
				<a style="margin: 0 10px 10px;" href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage">
				<div class="presentation-categories" style="padding:0;margin:0">
					<span>'.$row['nom_marchand'].'</span>
					<img class="item-img-solo" style="border:none;background-image:url(\''.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'\')" />
				</div></a>';
		}
	?>
				</div>
			</aside>


			<aside class="aff_droit aff_droitindex">
			<span>TOP MEMBRES</span>
			<div class="gridindex nohoverafter">
				<?php
		$prep = $pdo->prepare('SELECT U.membre_id, U.membre_utilisateur, I.image, I.type FROM user U
								LEFT JOIN annonces_parrainage AP ON U.membre_id = AP.idmembre
								LEFT JOIN images I ON I.id_membre = U.membre_id AND I.type="avatar"
								WHERE I.image IS NOT NULL
								GROUP BY membre_id
								ORDER BY RAND()
								LIMIT 0,6');
		$prep->execute();
		$results = $prep->fetchAll(PDO::FETCH_ASSOC);
		//echo print_r($results);
		foreach($results as $row){
			echo '<a style="margin: 0 10px 10px;width:120px;" title="Profil du parrain '.$row['membre_utilisateur'].'" href="'.ROOTPATH.'/profil/'.$row['membre_id'].'">
				<div class="presentation-categories" style="padding:0;margin:0">
					<span>'.$row['membre_utilisateur'].'</span>
					<div class="profilavatar"><img style="width:70px;height:70px;position:relative;" alt="Profil du parrain '.$row['membre_utilisateur'].'" src="'.ROOTPATH.'/membres/images/'.(isset($row['image']) && $row['type'] == 'avatar' ? $row['image'] : '/default_avatar.png').' " class="avatar" /></div>
				</div></a>';
		}

	?>
				</div>
			</aside>
		</article>
	</div>
</section>
	
<?php require_once 'elements/footer.php'; ?>