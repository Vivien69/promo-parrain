<?php
            $accents = array("é","è","ê","à","'");
            $replace = array("e","e","e","a","");
            echo '<p style="text-align:left;"><a title="Accueil" href="'.ROOTPATH.'">Accueil</a> › ';
            echo '<a title="Codes promo '.find_categorie($row['cat']).'" href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'">'.find_categorie($row['cat']).'</a> › ';
            echo 'Codes promo '.$row['nom_marchand'];
            
        ?>
        
<br />
<?php
        if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] == "connecter") 
            echo '<div id="admin_menu"><a style="float:right;" href="'.ROOTPATH.'/membres/admin/marchands/edit/'.$row['id'].'" title="Editer le marchand"><i class="fas fa-edit fa-2x"></i> </a>';
            
            if(isset($admin)) {
                echo $admin.'</div>';
                echo "<script>
                const adminchange = document.getElementById('adminChangeEtat');
                const admin_menu = document.getElementById('admin_menu');
            
                adminchange.addEventListener('change', function() {
                    changetat = adminchange.value;
                    id = ".$row['id'].";
            
                    fetch('".ROOTPATH."/pages/include/changeadminetat.php', {
                        method: 'POST',
                        headers: {
                            'Content-type': 'application/json'
                        },
                        body: JSON.stringify({changetat : changetat, id :id})
                     })
                    .then((reponse) => reponse.json())
                    .then((donnees) => {
                        if(donnees.erreur == 'ok') {
                        admin_menu.innerHTML += '<div class=\'valider\'>Etat modifié</div>';
                        document.getElementById('adminChangeEtat').value = donnees.val;
                        }
                        if(donnees.erreur == 'error')
                        admin_menu.innerHTML += '<div class=\'erreur\'>Erreur</div>';
                    })
                    .catch((erreur) => {
                        admin_menu.innerHTML += '<div class=\'erreur\'>Erreur catched</div>';
                    });
            
                });
                </script>";
            }
            
            ?>

        <h1><i style="margin: 0 10px;color:#701414" class="fas fa-coins"></i>Codes promo <?=$row['nom_marchand']; ?></h1>


        <section>
            <script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script>
            <div class="sharebutton">
             <a href="http://www.facebook.com/share.php?u=<?= $urlcanonic ?>" onclick="return fbs_click()" title="Partager la liste des codes promo sur Facebook" target="_blank"><i class="fab fa-facebook-square fa-3x"></i></a>
             <a href="http://twitter.com/share?url=<?= $urlcanonic ?>&hashtags=PromoParrain&related=twitterapi/twitter&text=Trouve un code promo pour <?= $row['nom_marchand'] ?>" title="Partager la liste des codes promo sur Twitter" target="_blank"><i class="fab fa-twitter-square fa-3x"></i></a>
            </div>
            <br />
            <h2><i style="margin: 0 10px;color:#701414" class="fas fa-search"></i> Trouver votre code promo <?=$row['nom_marchand']; ?></h2>



            <script>
    //Ajax de pagination
    function fetch_data(marchand, page) 
    {
        $.ajax({
            type: "POST",
            url: "<?= ROOTPATH; ?>/pages/pagination.php",
            data: { marchand : marchand,
                    page: page
            },
            success: function (data) {
                $("#pagination").html(data);
            }
        });
    }
    
    //Script de changement de page lors du click
    $(document).on("click", ".page-item", function(){
        $(this).removeClass('paginat').addClass('current');
        var page = $(this).attr("id");
        var marchand = <?= $idma; ?>;
        $('html, body').animate({
    scrollTop: $("#pagination").offset().top
}, 1000);
        fetch_data(marchand, page);
    });
</script> 
            <article id="pagination"><script>fetch_data(<?= $idma; ?>);</script></article>
            
        </section>

        <section>
        <br />
            <h2 style="text-align:left;margin-left:10px;"><i style="margin: 0 10px;" class="fas fa-store"></i>Description de l'offre de parrainage <?=$row['nom_marchand']; ?></h4>
            <article style="padding:0 20px 20px;"><p><?=$row['description']; ?></article>

            <?php if(isset($row['foncparrainage']) && $row['foncparrainage'] != ''): ?>
            <article style="padding:10px 0px 20px;">
            <h3 style="margin-left:10px;text-align:left;"><i style="margin: 0 10px;" class="fas fa-wrench"></i>Comment se faire parrainer sur <?=$row['nom_marchand']; ?> ?</h5>
           <br />
                <?php
                $array = array('1.', '2.', '3.', '4.', '5.', '6.', '7.', '8.', '9.');
                ?>
                <ol style="text-align:left">
                <li>
                    <?= str_replace($array, '', str_replace('<br />', '</li><li>', $row['foncparrainage'])); ?>
                </li>
                </ol>
            
            </article>
            <br />
            <?php endif; ?>
        </section>
        <br />

        <section>
            <h2 style="margin-left:10px;"><i style="margin: 0 10px;" class="far fa-copy"></i>Catégories de parrainages similaires</h6>
            <br />
            <?php
						$sql = "SELECT M.* FROM marchands M
						WHERE M.cat = ". $row['cat'] ." AND M.id NOT IN ($idma)
                        ORDER BY RAND()
                        LIMIT 0,7";
						$prepare = $pdo->prepare($sql);
						$prepare->execute();
						$GLOBALS['nb_req']++;
						if ($prepare->rowcount() > 0) {
							while ($result = $prepare->fetch(PDO::FETCH_ASSOC)) {
								echo '<a href="'.ROOTPATH.'/'.format_url(find_categorie($result['cat'])).'-'.$result['cat'].'/'.format_url($result['nom_marchand']).'-'.$result['id'].'/parrainage"><div class="presentation-categories" style="padding:20px 20px 10px 20px;display:inline-block;background-color:#eee;height:140px;margin-right:10px;vertical-align: top;">
                                <span style="color: #701818;">' . $result['nom_marchand'] . '</span>
                                <div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $result['img'] . '\'); background-size: 140px;background-repeat: no-repeat;"></div>
                                </div></a>';
							}
						} else {
							echo '<div class="box_annonces"><p>Aucun autre magasin actuellement</p></div>';
						}
					
						?>
                        <br /><br />
        </section>