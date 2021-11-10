<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Derniers codes promo de parrainage ajoutées';
?>
<meta name="description" content="Les dernières offres promotionnels de parrainages proposée par les parrains pour vous filleul. Obtenez des réduction grâce aux codes promo publiés par les parrains. ">
<meta name="keywords" content="code, promo, parrain, parrainage, réduction, promotionnel, bon, argent, economie, gagnez, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/parrainages" />
<meta name="robots" content="noodp,noydir" />
<?php
require_once '../../elements/header2.php';

?>

<section class="no-block_inside">
    <h1>Trouvez facilement un code promo de parrainage pour votre marchand favoris</h1>
        <?php
        $sql = "SELECT AP.id, AP.idmembre, M.img, M.montantremise, M.montantdevise, AP.description, AP.dateajout, M.choixoffre, M.id as idmarchand, M.nom_marchand, M.cat, M.img, U.membre_utilisateur, U.conf_datemask, I.id_membre, I.image, I.type
                    FROM annonces_parrainage AP
                    LEFT JOIN user U ON U.membre_id = AP.idmembre
                    LEFT JOIN marchands M ON AP.idmarchand = M.id
                    LEFT JOIN images I ON AP.idmembre = I.id_membre AND I.type = 'avatar'
                    WHERE AP.etatvalidation = 1
                    ORDER BY AP.dateajout DESC
                    LIMIT 0,20";
        $prep = $pdo->prepare($sql);
        $prep->execute();
        $GLOBALS['nb_req']++;
        while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
            echo '<article class="box-annonce" style="align-items:center;">
                    <div style="align-items:flex-start;width:17%;min-width:160px;max-width:180px;"><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage">' . $row['nom_marchand'] . '<div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'] . '\');background-size:140px;"></div></a></div>
                    <div class="box-mid"><h6 class="titre-annonce">Offre de parrainage '.$row['nom_marchand'].' de ' . $row['membre_utilisateur'] . '</h6><span style="display:block;margin-top:-2px;font-size:10px;font-family:Verdana;color:#333333;margin-bottom:10px;">Publiée '.(isset($row['conf_datemask']) && $row['conf_datemask'] == 1 ? mepd($row['dateajout'], 1) : mepd($row['dateajout'])).'</span><span id="descrip-annonce">' . substr($row['description'], 0, 150) . '</span></div>
                    <div style="width:15%;"><a href="' . ROOTPATH . '/profil/'.$row['idmembre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['image']) ? $row['image'] : '/default_avatar.png').'" class="avatar" /> <span style="font-weight:bold"><br />' . $row['membre_utilisateur'] . '</a></div>
                    <div class="box-view"><span style="font-weight:bold;font-size:16px">'.(($row['choixoffre'] == 1) ? $row['montantremise'].$row['montantdevise'] : (($row['choixoffre'] == 2) ? '<i style="margin-right:5px" class="fas fa-truck"></i>Livraison offerte' : (($row['choixoffre'] == 3) ? '<i style="margin-right:5px" class="fas fa-gift"></i>Cadeau offert' : $row['montantremise'].$row['montantdevise']))).'</span><br /><br /><a class="pboutonr" style="font-size:17px;padding:10px 15px 7px;" href="' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '" title="Voir l\'annonce">Voir le parrainage</a></div>
                </article>
                <br />';
        }

        ?>
</section>


<?php require_once '../../elements/footer.php'; ?>