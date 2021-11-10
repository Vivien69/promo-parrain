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
    <h1>File get</h1>

    <?php 

       
$source = file_get_contents('https://www.widilo.fr/code-promo/cdiscount');   


    ?>
	</div>
</section>
	
<?php require_once 'elements/footer.php'; ?>
use Wa72\HtmlPageDom\HtmlPageCrawler;
