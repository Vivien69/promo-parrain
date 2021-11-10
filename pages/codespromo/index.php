<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Codes promotionnels';
require_once '../../elements/header2.php';
/**
 * *******Gestion avant affichage...**********
 */ 
$id =8;
if(isset($id) && is_numeric($id)) {
    $idmarchand = (int)$id;
    $sql = 'SELECT * FROM marchands WHERE id = '.$idmarchand;
    $GLOBALS['nb_req']++;
    $prep = $pdo->prepare($sql);
    $prep->execute();
    
    } else {
        $informations = Array(/*L'id n'est pas un chiffre ou n'existe pas*/
            true,
            'Erreur marchand',
            'Erreur lors de l\'accès au marchand.<br />Merci de contacter le staff pour nous signaler cette anomalie.',
            ' - <a href="' . ROOTPATH . '/index.php">Retour aux annonces</a>',
            ''. ROOTPATH . '/contact.html',
            6
            );
        require_once '../../information.php';
        exit();
    }

    if ($prep->rowcount() == 1) {
        while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
    /**
     * *******FIN Gestion avant affichage...**********
     */
?>
<div class="content">
  <!-- MENU PRINCIPAL DE DROITE -->
  <aside class="aside">
        <img title="<?= $result['nom_marchand']; ?>" src="<?= 'images/marchands/'.$result['img']; ?>" /><br />
        <?='<a href="'.$result['url_marchand'].'"  target="_blank" title="'.$result['nom_marchand'].'">'.$result['url_marchand'].' </a>' ?><br /><br />
        <p>30 Avis</p>
        Meilleur cashback :
        <div class="best-cashback">3% iGraal</div>
    </aside>
    <!-- FIN MENU DE DROITE -->

    <!-- DEBUT SECTION -->
    <section class="block_inside">
        <article id="marchand">
            <h1 class="iconic alaune" > <?= $result['nom_marchand']; ?></h1>
        </article>


        <article id="codes-promo">
            <h2>Codes promo <?= $result['nom_marchand']; ?></h2>
                <?php /** SQL Codes promo*/
                    $sql = "SELECT * FROM codespromo WHERE idmarchand=".$id." LIMIT 9";
                    $prep = $pdo->prepare($sql);
                    $prep->execute();
                    $GLOBALS['nb_req']++;
                    if($prep->rowCount() >= 1) {
                        while ($resultat = $prep->fetch(PDO::FETCH_ASSOC)) {
                            $long = strlen($resultat['code']);
                            $conditions = addslashes($resultat['conditions']);
                    echo '
                        <div class="list-item" data-id="'.$resultat['id'].'">
                            <div class="llist">
                                <div class="titre">'.$resultat['montantremise'].' '.$resultat['montantdevise'].($resultat['montantachatminimal'] ? ' dès '.$resultat['montantachatminimal'].' € d\'achats' : ' de remise').'</div>
                                <div class="conditions">'.substr($resultat['conditions'], 0, 75).' <a class="conditions gerer" onclick="conditions_change('.$conditions.');"> Suite <i class="fa fa-angle-down fa-lg"></i></a></div>  
                                
                                <input type="text" class="idmarchand voir" value="'.$resultat['id'].'" />
                            </div>
                            
                            <div class="rlist">
                                <p><i class="fa fa-clock-o fa-lg"></i> Il y a 4h</p>
                                <div class="offre">'.$resultat['montantremise'].' '.$resultat['montantdevise'].'</div>
                                <div class="voir bouton">Voir le code <i style="margin-left:7px;font-size:16px" class="fa fa-external-link-alt fa-lg"></i></div>
                                
                            </div>
                        </div>

                        <div class="bottom-list">
                            <a class=""><i class="fa fa-user fa-lg"></i> Vivien</a>
                            <a class="liencomment" onclick="charger_commentaires('.$resultat['id'].');" ><i class="fa fa-comments fa-lg"></i> '.$resultat['id'].'</a>
                            <span><i title="Valable pour les anciens clients" class="fa fa-user-tag fa-lg"></i> <i title="Valable pour les nouveaux clients" class="fa fa-user-plus fa-lg"></i> clients</span>
                            <span style="justify-content:flex-end">Expire  : '.mepd($resultat['dateajout']).'</span>
                        </div>
                        <div id="commentaires"><img id="imgload" style="margin:0 auto;display:none;" src="'.ROOTPATH.'/images/loading.gif" alt="Chargement..." /></div>
                        ';
                        }
                    }
                ?>
        </article>
        <article id="avis">
            <h2>Avis sur <?php echo $result['nom_marchand']; ?></h2>   
        </article>
        <article id="codepromo">
            <h2>Comment ajouter un code promo sur <?php echo $result['nom_marchand']; ?></h2>   
        </article>
        
    </section>
    <!-- FIN SECTION -->
    
</div>

<script>

    function conditions_change(data) {
        $(this).empty();
            

        }
        
        $(".idmarchand, .codevu, #loading,#commentaires").hide();
        $(".codevu").hide();

    $('.voir').click(function(event) {
            event.stopPropagation();
            $(this).hide();
            $(this).next().toggle();
        });

    $('.liencomment').click(function(event) {
            var div = $(this).children('a');
            alert(div.toSource());
            event.stopPropagation();
            return false;
        });

    function charger_commentaires(id){
        $('#commentaires').fadeIn();
        $("#imgload").show();
        $.ajax({
           url: "<?= ROOTPATH; ?>/pages/codespromo/charge_comment.php?id="+id,
            complete: function(){$("#imgload").hide();},
            success: function(data) {
            $('#commentaires').html(data);
          }
     });
        event.stopPropagation();
        return false;
    }

</script>   

<?php  } } require_once '../../elements/footer.php'; ?>
            