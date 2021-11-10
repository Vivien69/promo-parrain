<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

if(isset($_GET['idcat']) && isset($_GET['categorie']) && is_numeric($_GET['idcat'])) {
    $idcat = intval($_GET['idcat']);
    $prep = $pdo->prepare('SELECT count(AP.id) AS nb, M.nom_marchand, M.id AS idmarchand, M.cat, M.img, CP.id AS idcat, CP.nom_categorie
    FROM marchands M
    JOIN categories_principales CP ON M.cat = CP.id
    LEFT JOIN annonces_parrainage AP ON AP.idmarchand = M.id
    WHERE M.cat = :idcat
    GROUP BY M.id
    ORDER BY count(AP.id) DESC');
    $prep->bindValue(':idcat', $idcat, PDO::PARAM_INT);
    $prep->execute();
    $title = 'Parrains pour la catégorie '.find_categorie($idcat);
    ?>

<meta name="description" content="Trouver votre parrain dans la liste de marchand de la catégorie <?= find_categorie($idcat); ?>. Des offres de parrainage inscroyables. ">
<meta name="keywords" content="<?= find_categorie($idcat); ?>, code, promo, parrain, parrainage, catégories, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/<?= format_url(find_categorie($idcat)).'-'.$idcat; ?>" />
<meta name="robots" content="noodp,noydir" />

<?php
    require_once '../elements/header2.php';
} else {
    $informations = Array(/*L'id n'est pas un chiffre ou n'existe pas*/
        true,
        'Erreur',
        'Erreur lors de l\'accès aux marchands de la catégorie.',
        ' - <a href="' . ROOTPATH . '/index.php">Retour</a>',
        ''. ROOTPATH . '/contact',
        10
        );
    require_once('../information.php');
    exit(); 
}
if($prep->rowcount() == 0) {
    ?>
    <section class="block_inside">

        <h1><i style="margin: 0 10px;color:#701414" class="fas fa-store"></i>Aucune offre de parrainage pour la catégorie <?= find_categorie(intval($_GET['idcat'])); ?></h1>
        <article class="article">
        Il n'y a actuellement aucun marchand pour cette catégorie
        </article>  
    </section>
    <?php
    
} else {
?>
<section class="no-block_inside">
    <article id="categories_de_parrainage">
        <h1>Code de réduction <?= find_categorie(intval($_GET['idcat'])) ?></h1>
        <?php
        while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
            echo '
                <a href="'.ROOTPATH.'/'.format_url($row['nom_categorie']).'-'.$row['idcat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage"><div class="presentation-categories">
                    <img class="item-img-solo" style="border:none;background-image:url(\''.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'\')" />
                    <p>'.$row['nom_marchand'].'</p>
                </div></a>';
        }
        ?>


    </article>
</section>
                

<?php } require_once '../elements/footer.php'; ?>