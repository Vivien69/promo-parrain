<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Parrainages boostés, durée limitée';
?>
<meta name="description" content="Les dernières offres promotionnels de parrainages proposée par les parrains pour vous filleul. Obtenez des réduction grâce aux codes promo publiés par les parrains. ">
<meta name="keywords" content="code, promo, parrain, parrainage, réduction, promotionnel, bon, argent, economie, gagnez, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/parrainages" />
<meta name="robots" content="noodp,noydir" />
<?php
require_once '../../elements/header2.php';

?>

<section class="no-block_inside">
    <h1>Les offres de parrainages actuellement boostés</h1>
        <?php
        $sql = "SELECT *, CP.id AS idcat
                    FROM marchands M 
                    LEFT JOIN histo_marchands HM ON M.id = HM.id_marchand
                    JOIN categories_principales CP ON M.cat = CP.id
                    WHERE HM.boosted = 1
                    GROUP BY M.id";
        $prep = $pdo->prepare($sql);
        $prep->execute();
        $GLOBALS['nb_req']++;
        while ($row = $prep->fetch()) {
            
            echo '  <a href="'.ROOTPATH.'/'.format_url($row['nom_categorie']).'-'.$row['idcat'].'/'.format_url($row['nom_marchand']).'-'.$row['id_marchand'].'/parrainage">
                        <div class="presentation-categories">
                            <img class="item-img-solo" style="border:none;background-image:url(\''.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'\')" />
                            <p>'.$row['nom_marchand'].'</p>
                            <p>Du '.datedeftofr($row['date_debut'], true).' au '.datedeftofr($row['date_fin'], true).'</p>
                            <h4>'.$row['montantremise'].' '.$row['montantdevise'].'</h4>
                        </div>
                    </a>';
        }

        ?>
</section>


<?php require_once '../../elements/footer.php'; ?>