<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
$title = 'Liste de tous les marchands de code promo de parrainage';
?>
<meta name="description" content="Liste de tous les marchands sur Promo-parrain. Trouvez votre boutique en ligne favoris">
<meta name="keywords" content="marchands, code, promo, parrain, parrainage, catégories, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/liste-marchands" />
<meta name="robots" content="noodp,noydir" />
<?php
require_once '../elements/header2.php';
$alphabet = array('tout','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
$cursql = "";
$cur = '';
if(isset($_GET['alpha']) && $_GET['alpha'] != '') {
	if(in_array($_GET['alpha'],$alphabet))
		$cur = $_GET["alpha"];
		if($cur == '9')
		$cursql = " WHERE M.nom_marchand >= '0' and M.nom_marchand < ':'";
		elseif($cur == 'tout')
		$cursql = '';
		else 
		$cursql = ' WHERE M.nom_marchand LIKE "'.$cur.'%"';
}

$prep = $pdo->prepare('SELECT M.nom_marchand, M.id AS idmarchand, M.cat, M.img, CP.id AS idcat, CP.nom_categorie
		FROM marchands M
		JOIN categories_principales CP ON M.cat = CP.id
		'.$cursql.'
		GROUP BY M.id');
		$prep->execute();
?>
<div class="sousmenucateg">
	<div style="width:auto;margin:0 auto;"><li>
		<?php
			foreach($alphabet as $alpha) {
				echo '<li><a style="border-left:1px solid #eee;" href="'.ROOTPATH.'/liste-marchands/'.$alpha.'" '.($cur == $alpha ? 'class="currenta" ' : '').'> '.strtoupper($alpha == '9' ? '0-9' : $alpha).'</a></li>';
			}
			
		?>
	</div></div>

<section class="block_inside">
    <article id="liste_marchands">

        <h1>Liste de tous les marchands</h1>
    
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
<?php  require_once '../elements/footer.php'; ?>