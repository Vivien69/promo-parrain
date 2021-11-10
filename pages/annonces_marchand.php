<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

$base = basename($_SERVER['REQUEST_URI']);
if($base != 'parrainage' && $base != 'codes-promo')
{
    header("Status: 301 Moved Permanently", false, 301);
    header("Location: ".ROOTPATH.$_SERVER['REQUEST_URI']."/parrainage"); 
}


if(!isset($_GET['idm']) && !is_numeric($_GET['idm'])) {

    $informations = Array(/*L'id n'est pas un chiffre ou n'existe pas*/
        true,
        'Erreur',
        'Erreur lors de l\'accès à la catégorie.<br />Elle n\'existe pas.',
        ' - <a href="' . ROOTPATH . '/index.php">Retour</a>',
        ''. ROOTPATH . '/contact.html',
        10
        );
    require_once('../information.php');
    exit(); 
} else {
    $idma = intval($_GET['idm']);
    $prep = $pdo->prepare("SELECT * FROM marchands WHERE id = :idmarchand");
    $prep->bindValue(":idmarchand", $idma, PDO::PARAM_INT);
    $prep->execute();
    $row = $prep->fetch(PDO::FETCH_ASSOC);

    if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] = 'connecter') {
        $id = intval($_SESSION['membre_id']);
        $sql = "SELECT * FROM user WHERE membre_id=".$id.' AND membre_etat = 2';
        $prep = $pdo->prepare($sql);
        $prep->execute();
        if($prep->rowCount() == 1) {
            $admin = '<select id="adminChangeEtat"><option value="1" '.(isset($row['actif']) && $row['actif'] == 1 ? 'selected' : '').'>Activé</option><option value="0" '.(isset($row['actif']) && $row['actif'] == 0 ? 'selected' : '').'>Désactiver</option></select>';
        }
    }
        
$urlcanonic = ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['id'].'/parrainage';


if($base == 'parrainage'){
    $title = 'Code promo '.$row['nom_marchand'].' Parrainage - Réduction grâce aux parrains';
    echo '
    <meta name="description" content="Code promo de parrainage '.$row['nom_marchand'].'. Profitez de nombreux code promo de parrainage sur '.$row['nom_marchand'].' et économisez de l\'argent sur vos prochaines commandes. Les avantages du code de réduction pour le parrain sont '.strip_tags($row['offreparrain']).', et pour le filleul c\'est : '.strip_tags($row['offrefilleul']).'">
    <meta name="keywords" content="'.$row['nom_marchand'].', codes, promo, parrainage, réduction, promotionnel, parrain, bons, filleul">
    <link rel="canonical" href="'.$urlcanonic.'" />
    <meta name="robots" content="noodp,noydir" />
    <meta property="og:title" content="Code promo de Parrainage '.$row['nom_marchand'].'" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="'.$urlcanonic.'" />
    <meta property="og:image" content="'.ROOTPATH.'/membres/includes/uploads-img/800-'.$row['img'].'" />
    <meta property="og:description" content="Code promo de parrainage '.$row['nom_marchand'].'. Profitez de nombreux code promo de parrainage sur '.$row['nom_marchand'].' et économisez de l\'argent sur vos prochaines commandes. Les avantages du code de réduction pour le parrain sont '.strip_tags($row['offreparrain']).', et pour le filleul c\'est : '.strip_tags($row['offrefilleul']).'" />
    ';
}
elseif($base == 'codes-promo'){
    $title = 'Code promo '.$row['nom_marchand'].' - Trouver un code qui vous fera bénéficier de promotions exceptionnelles';
    echo '
    <meta name="description" content="Code promo '.$row['nom_marchand'].'. Profitez de nombreux code promo sur '.$row['nom_marchand'].' et économisez de l\'argent sur vos prochaines commandes. Les avantages du code de réduction pour le parrain sont '.strip_tags($row['offreparrain']).', et pour le filleul c\'est : '.strip_tags($row['offrefilleul']).'">
    <meta name="keywords" content="'.$row['nom_marchand'].', codes, promo, parrainage, réduction, promotionnel, parrain, bons, filleul">
    <link rel="canonical" href="'.$urlcanonic.'" />
    <meta name="robots" content="noodp,noydir" />
    <meta property="og:title" content="Code promo '.$row['nom_marchand'].'" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="'.$urlcanonic.'" />
    <meta property="og:image" content="'.ROOTPATH.'/membres/includes/uploads-img/800-'.$row['img'].'" />
    <meta property="og:description" content="Code promo '.$row['nom_marchand'].'. Profitez de nombreux code promo sur '.$row['nom_marchand'].' et économisez de l\'argent sur vos prochaines commandes. Les avantages du code de réduction pour le parrain sont '.strip_tags($row['offreparrain']).', et pour le filleul c\'est : '.strip_tags($row['offrefilleul']).'" />
    ';

}

    
    require_once '../elements/header2.php'; 
    ?>
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

    <div>
        <div class="img_presentation" style="background-image: url('<?= ROOTPATH . '/membres/includes/uploads-img/800-' . $row['img']; ?>');"></div>
    </div>

    <section class="block_inside" style="padding:0">
        
        <div class="sousmenucateg" style="text-align:left;">
	        <ul style="width:auto;margin:0 auto;padding:0;"><!-- @whitespace
                --><li><a href="<?= ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['id'] ?>/parrainage" <?= ($base == 'parrainage' ? 'class="currenta"' : '') ?>><i class="fa fa-user" style="margin-right:5px;"></i> Parrainage</a></li><!-- @whitespace
                -->
            </ul>
        </div>
        <br />
<!-- <li><a href="<?= ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['id'] ?>/codes-promo" <?= ($base == 'codes-promo' ? 'class="currenta"' : '') ?>><i class="fa fa-percentage" style="margin-right:5px;"></i> Codes Promo</a></li> -->
        <?php
            if($base == 'parrainage')
                require_once ('parrain/page_parrainage.php');
            elseif($base == 'codes-promo')
                require_once ('codespromo/page_codespromo.php');
            ?>

        <br /><br />
        <section>
            <h2 style="margin-left:10px;text-align:left;"><i style="margin: 0 10px;" class="far fa-comments"></i>Avis sur <?= $row['nom_marchand'] ?></h5>
   
            
        <!-- COMMENTAIRES  -->
    <div class="flex">

    <article id="comment" class="voir_annonce_texte" style="width:100%;border-top:none;border-right:none;border-left:none;padding-top:0;text-align:left;min-height:50px;margin-bottom:10px;">
    
    <script>
    //Ajax de pagination et affichage commentaires
    function fetch_comments(marchand, page) 
    {
        $.ajax({
            type: "POST",
            url: "<?= ROOTPATH; ?>/pages/include/avis-marchands-ajax.php",
            data: { marchand : marchand,
                    page: page
            },
            success: function (data) {
                $("#comment").html(data);
            }
        });
    }
    
    //Script de changement de page lors du click
    $(document).on("click", ".page-item2", function(){
        $(this).removeClass('paginat').addClass('current');
        var page = $(this).attr("id");
        var marchand = <?= $idma; ?>;
        $('html, body').animate({
    scrollTop: $("#comment").offset().top
}, 1000);
        fetch_comments(marchand, page);
    });
</script> 
<script>fetch_comments(<?= $idma; ?>);</script>

    </div>
    
    <?php
        if(isset($_SESSION['membre_id'])) :
            $bdd = $pdo->prepare("SELECT CM.*, M.img, M.id, M.nom_marchand FROM comments_marchands CM
            LEFT JOIN marchands M ON M.id = CM.id_marchand
            WHERE CM.id_marchand = :idmarchand and CM.id_sender = :id");
            $bdd->bindParam(':idmarchand', $idma, PDO::PARAM_INT);
            $bdd->bindParam(':id', $_SESSION['membre_id'], PDO::PARAM_INT);
            $bdd->execute();
            $fetch = $bdd->fetch();
            $nb = $bdd->rowcount();
            
            if($nb == 0 && isset($_SESSION['membre_id']))  :
                //echo print_r($fetch);
            ?>

<p style="grid-area: 2 / 1 / 2 / 4;">Laisser un avis sur <strong><?= $row['nom_marchand'] ?></strong></p>
<div id="reponseaddcom"></div>
    <div class="add_comment" style="padding:10px"><br/>
        
        <div class="note-star">
            <i class="far fa-star changestar fa-2x" data-value="1"></i><i class="far fa-star changestar fa-2x" data-value="2"></i><i class="far fa-star changestar fa-2x" data-value="3"></i><i class="far fa-star changestar fa-2x" data-value="4"></i><i class="far fa-star changestar fa-2x" data-value="5"></i>
            <div class="item-img-solo" style="background-image: url('<?= ROOTPATH ?>/membres/includes/uploads-img/120-<?= $row['img'] ?>');background-size:140px;justify-self:end;grid-area: 3 / 3 / 3 / 3;margin-top:15px;"></div>

        </div>
        
        <div style="grid-area: 1 / 2 / 4 / 4;">
            <form method="POST" id="comments" name="comments">
            
                <textarea id="inputadd_comment" name="inputadd_comment" placeholder="Ajoutez un commentaire ... 250 caractères minimum"></textarea>
                
                <input style="display:none" type="text" name="note" id="note">
                <input style="display:none" type="text" name="idsender" value="<?= isset($_SESSION['membre_id']) ?  $_SESSION['membre_id'] : ''; ?>">
                <input style="display:none" type="text" name="id_marchand" value="<?= $idma ?>">
                <input style="display:none" type="text" name="name" id="name" value="">
                <button class="bouton" id="send" name="submit" style="float:right;margin-right:10px;">Envoyer</button>
            </form>
        </div>
        </div>
        
            <?php endif;
            endif; ?>
    </article>
            
    </div>

</section>

    
                    
<script>
    //AJAX DE COMMENTAIRE : 

$('#comments').submit( function() {
	$('.erreur').remove();
	$('.add_comment').hide();
	$("#reponseaddcom").append('<div class="spinner" style="text-align:center;margin:50px 0 50px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" /></div>');
	donnees = $(this).serialize();
	url = "<?= ROOTPATH ?>/pages/include/comments-marchands.php"
	$.ajax({
		type: "post",
		url: url,
		data: donnees,
		dataType: "JSON",
		success: function (response) {
			if(response.ok) {
				$('#listcomment').prepend(response.ok).delay(100);
				$('#inputadd_comment').val('');
				$("#reponseaddcom").empty();
			} else if(response.error) {
				$("#reponseaddcom").empty();
				$('#reponseaddcom').append(response.error);
				$('.add_comment').show();
				
			}
			
			
		}
	});
	return false;
});

</script>
    

<script src="<?= ROOTPATH ?>/script/stars.js"></script>
<?php }  require_once '../elements/footer.php'; ?>