<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';

// ***** DEBUT TRAITEMENT *****
if (isset($_GET['id']) && isset($_GET['marchand'])) {
$id = intval(htmlentities($_GET['id']));
    $sql = "SELECT AP.id, AP.idmarchand, AP.idmembre, AP.vues, AP.bonus, M.montantremise, AP.code, M.montantdevise, M.montantachatminimal, AP.code, AP.lien, AP.description, AP.dateajout, M.choixoffre, AP.choixcode, U.membre_id, U.membre_utilisateur, U.membre_email, U.conf_datemask, M.id as idmarchand, M.nom_marchand, M.cat, M.url_marchand, M.offrefilleul, M.img, I.id_membre, I.image, I.type, U.membre_lastco, U.conf_online
            FROM annonces_parrainage AP
            JOIN user U ON AP.idmembre = U.membre_id
            LEFT JOIN images I ON AP.idmembre = I.id_membre AND I.type = 'avatar'
            JOIN marchands M ON AP.idmarchand = M.id
            WHERE AP.id = :id
            ";
    $prep = $pdo->prepare($sql);
    $prep->execute(array(':id' => $id));
    $GLOBALS['nb_req']++;
    $avatar = ROOTPATH . '/membres/images/default_avatar.png';
    //COMPTE LE NOMBRE DE RESULTAT AVEC UN COUNT(*)

            //On verifie si le visiteur est déja venu sur cette page avec les cookies et sessions
            if (!isset($_COOKIE['count'.$id]) || !isset($_SESSION['count'][$id])) {
                setcookie('count'.intval($id), time(), time() + 15 * 24 * 3600, "/");
                $_SESSION['count'][$id] = time();
            }

    $count = $pdo->prepare("SELECT COUNT(*) AS num FROM annonces_parrainage AP
             JOIN user U ON AP.idmembre = U.membre_id
             JOIN marchands M ON AP.idmarchand = M.id
             WHERE AP.id = :id");
    $count->execute(array(':id' => $id));
    $nb_enregistrements = $count->fetch();
    // Si plus de 1 resultat ou surtout 0 Resultat alors on affiche une erreur 

    if ($nb_enregistrements['num'] != 1) {
        require_once '../../elements/header2.php';
        $informations = array(/*L'id n'est pas un chiffre ou n'existe pas*/
            true,
            'Erreur',
            'Erreur lors de l\'accès à la fiche de l\'offre de parrainage.<br />Elle n\'existe pas',
            ' - <a href="' . ROOTPATH . '/index.php">Retour</a>',
            ROOTPATH,
            10
        );
        require_once('../../information.php');
        http_response_code(404);
        exit();
    }
}

// ***** FIN TRAITEMENT *****
// Sinon on affiche la page

while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
    $title = 'Offre de parrainage ' . $row['nom_marchand'] . ' du parrain ' . $row['membre_utilisateur'];
    $urlcanonic = ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'];
    ?>
    <meta name="description" content="Offre de parrainage <?=$row['nom_marchand'] ?> de <?=$row['membre_utilisateur'] ?>">
    <meta name="keywords" content="<?=$row['nom_marchand'] ?>, <?=$row['membre_utilisateur'] ?>, parrain, parrainage">
    <link rel="canonical" href="<?= $urlcanonic ?>" />
    <meta name="robots" content="noodp,noydir" />
    <meta property="og:title" content="Code promo de Parrainage <?= $row['nom_marchand'] ?> de <?=$row['membre_utilisateur'] ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $urlcanonic ?>" />
    <meta property="og:image" content="<?= ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img']; ?>" />
    <meta property="og:description" content="<?= $row['description'] ?>" />
    
    <?php
        require_once '../../elements/header2.php';
    ?>

    <section class="noblock_inside" style="padding:0;text-align:left;">

        <!-- HEADER IMAGE MARCHAND -->
            <div class="img_presentation" style="background-image: url('<?= ROOTPATH . '/membres/includes/uploads-img/800-' . $row['img']; ?>');"></div>

    </section>

    <section class="ar_dimension noblock_inside">
        
        
        <!-- Fil d'ariane -->
        <?php
            $accents = array("é","è","ê","à","'");
            $replace = array("e","e","e","a","");
            echo '<p style="text-align:left;margin:5px;"><a title="Accueil" href="'.ROOTPATH.'">Accueil</a> › ';
            echo '<a title="Parrainages '.find_categorie($row['cat']).'" href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'">'.find_categorie($row['cat']).'</a> › ';
            echo '<a  title="Liste des parrainages '.$row['nom_marchand'].'" href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage">Parrainage '.$row['nom_marchand'].'</a> › ';
            echo '<span>Parrain '.$row['membre_utilisateur'].'</span></p>';
            
        ?>
        
        <div id="flex" style="align-items: flex-start;">
        
            <div class="columnflex">
            <article class="voir_annonce_texte" style="padding-bottom:0;">

               
            
        
                <?php /*
                if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] = 'connecter') {
                    $id = intval($_SESSION['membre_id']);
                    $sql = "SELECT * FROM user WHERE membre_id = ".$id.' AND membre_etat = 2';
                    $prep = $pdo->prepare($sql);
                    $prep->execute();
                    if($prep->rowCount() == 1) {
                       echo '<select id="adminChangeEtat">
                            <option value="0" '.(isset($row['actif']) && $row['actif'] == 0 ? 'selected' : '').'>Sélectionner</option>
                            <option value="1" '.(isset($row['actif']) && $row['actif'] == 2 ? 'selected' : '').'>Code Lien dans description</option>
                            <option value="2" '.(isset($row['actif']) && $row['actif'] == 1 ? 'selected' : '').'>Publicité</option>
                        </select>';
                    }
                }*/
                ?>

                <div id="debut_voir" style="text-align:left;padding:10px;">
                    <h1 style="margin:0;"><?= $row['membre_utilisateur']; ?> votre Parrain <?= $row['nom_marchand'] ?></h1>
                    <p style="text-align:left;margin-left:15px;">Offre actualisé <?= (isset($row['conf_datemask']) && $row['conf_datemask'] == 1 ? mepd($row['dateajout'], 1) : mepd($row['dateajout'])) ?></p>
                </div>
            </article>
                    

                <div class="menuContextuel">
                    <p style="margin:0px 0 5px 10px;">Pour profiter de parrainage <?= $row['nom_marchand'] ?> vous devez 
                    <?= (!isset($row['choixcode']) && isset($row['code']) ? 'copier le code ou le lien ci dessous afin de vous inscrire sur '.$row['nom_marchand'].' avec celui-ci ' : ''); ?>
                    <?= (isset($row['choixcode']) && $row['choixcode'] == 1 && !empty($row['code']) ? 'copier le code ci dessous afin de vous inscrire sur '.$row['nom_marchand'].' avec celui-ci ' : ''); ?>
                    <?=  (isset($row['choixcode']) && $row['choixcode'] == 1 && !empty($row['code']) && !empty($row['lien']) ? ' ou ' : '') ?>
                    <?= (isset($row['choixcode']) && $row['choixcode'] == 1 && !empty($row['lien']) ? 'cliquer sur le lien ci dessous afin de vous inscrire sur '.$row['nom_marchand'].' par son billet ' : ''); ?>
                    <?= (isset($row['choixcode']) && $row['choixcode'] == 2 ? 'demander à votre parrain '.$row['membre_utilisateur'].' une invitation ' : ''); ?>
                    :</p>
                </div>

            <!-- CODES A COPIER ET LIEN A COPIER OU INVITATION-->
                <div class="flex codelinkcopi">
<?php 

filter_var($row['code'],FILTER_VALIDATE_URL) ? $codeURL = 1 : $codeURL = 0;

switch ($row['choixcode']) {
    case 1:
        if(isset($row['code']) && $row['code'] != '') 
        {
            echo '<div class="copiInside"><span>Code promo à copier <i style="float:right;margin:2px 5px 0 0;" class="fas fa-percentage"></i></span>
            <input type="text" id="codecopy" value="'.$row['code'].'" style="min-width:100px;width:85%;" /><br /></div>';
            
        } 

        if(isset($row['lien']) && $row['lien'] != '') 
        {
            echo '<div class="copiInside"><span> Lien à cliquer <i style="float:right;margin:2px 5px 0 0;" class="fas fa-link"></i></span>
            <p style="margin-top:5px;"><a style="line-height:20px" id="linktarget" target="_blank" class="pboutonblanc" href="'.$row['lien'].'">Cliquer pour utiliser le lien de parrainage</a><input type="text" id="linkcopy" value="'.$row['lien'].'" style="display:none;" /></p><a style="display:none;" href="" id="popuplinkurl" rel="popup_clicklien" class="poplight"></a></div>';
            
        } 
        break;

    case 2:
        echo '<div class="copiInside"><span>Invitation <i style="float:right;margin:2px 5px 0 0;" class="fas fa-envelope-open-text"></i></span>';
        echo '<p style="color:#FFF">Contacter le parrain avec le formulaire présent sur cette page et demander une invitation à votre parrain, n\'oubliez pas de lui indiquer votre email. </p></div>';

        break;

    default: 
        if($codeURL)
        echo '<div class="copiInside"><span>Adresse à copier<i style="float:right;margin:2px 5px 0 0;" class="fas fa-percentage"></i></span>';
        else
        echo '<div class="copiInside"><span>Code promo à copier <i style="float:right;margin:2px 5px 0 0;" class="fas fa-percentage"></i></span>';

        echo '<input type="text" id="codecopy" value="'.$row['code'].'" style="min-width:100px;width:85%;" /><br /></div>';
        echo '<input type="text" style="display:none;" name="ajN_action" id="ajN_action" value="2" />';
    break; 
} 

echo '<input type="text" style="display:none;" name="ajN_idsender" id="ajN_idsender" value="'.(isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : session_id()).'" />
        <input type="text" style="display:none;" name="ajN_idreceiver" id="ajN_idreceiver" value="'.$row['membre_id'].'" />
        <input type="text" style="display:none;" name="ajN_idannonce" id="ajN_idannonce" value="'.$row['id'].'" />
        <input type="text" style="display:none;" name="ajN_idmarchand" id="ajN_idmarchand" value="'.$row['idmarchand'].'" />
        <a style="display:none;" href="" id="popupcode" rel="popup_copiecode" class="poplight"></a>';?>
    </div>


            <article class="voir_annonce_texte" id="debut_voir" style="text-align:left;padding:10px;">
            <h2>Description du parrainage <?= $row['nom_marchand'] ?></h2>
                <p style="text-align:center;font-size:13px;"><?= nl2br($row['description']); ?></p>
                
                <br /><br />
            </article>

                <!-- MENU CONTEXTUEL D'INTERACTION AVEC LES UTILISATEURS-->
            

            <div class="menuContextuel">
                    <ul>
                    <?= (isset($row['choixcode']) && $row['choixcode'] == 1 && isset($row['lien']) && $row['lien'] != '' ? '<li><a target="_blank" href="'.$row['lien'].'" id="linktarget2"><i class="fas fa-link fa-lg"></i> Aller sur '.$row['nom_marchand'].'</a></li>' : ''); ?>
                    <?= (isset($row['choixcode']) && $row['choixcode'] == 1 && isset($row['code']) && $row['code'] != '' ? '<li><a href="" onclick="return false;" id="copieurcode"><i class="fas fa-copy fa-lg"></i> Copier le code</a></li>' : ''); ?>
                    <li><a href="" <?= isset($_SESSION['membre_id']) ? 'rel="popup_signaler"' : 'rel="popup_inscription"'; ?> class="poplight" id="tellus"><i class="fas fa-exclamation-circle fa-lg"></i> Signaler ?</a></li>
                    </ul>
                    
                </div>
    </div>
            <!-- MENU PROFIL DROIT-->
            
            <?php
                $sql = $pdo->query("SELECT COUNT(*) as nb FROM comments WHERE id_receiver = ".$row['idmembre']);
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                
            ?>
            <article class="rightcontext">
                <aside class="aff_droit">
                    <span><?= $row['membre_utilisateur']; ?><?= ($row['conf_online'] == 1 ? is_online($row['membre_lastco'], null, 1) : '') ?><i style="float:right;margin:-2px 5px 0 0;color:#5CA732" class="fa fa-user"></i></span>
                    <div class="profilavatar"><a href="<?= ROOTPATH.'/profil/'.$row['idmembre'] ?>"><img style="width:70px;height:70px;position:relative;" alt="Profil du parrain <?= $row['membre_utilisateur']; ?>" src="<?= ROOTPATH ?>/membres/images/<?=(isset($row['image']) && $row['type'] == 'avatar' ? $row['image'] : '/default_avatar.png'); ?>" class="avatar" /><?= (is_online($row['membre_lastco'] == 'En ligne') ? '<i style="position:relative;font-size:18px;top:-62px;right:8px;" class="fas fa-check-circle green"></i>' : '') ?></a><a href="<?= ROOTPATH.'/profil/'.$row['idmembre'] ?>"><p style="font-size:14px;font-weight:bold;margin-bottom:10px;"><?= $row['membre_utilisateur']; ?></p><?= notemoyenne($row['idmembre']) ?></a></div>
                    <p style="background-color:#F5F5F5;padding:6px 5px;margin:0;"><i style="color:#701818" class="fas fa-shopping-cart"></i> <b style="padding:4px 5px;">Offres de <?= $row['membre_utilisateur']; ?> :</b> <a href="<?= ROOTPATH ?>/profil/<?= $row['membre_id']; ?>" title="Tous les offres de parrainage de <?= $row['membre_utilisateur']; ?>"><?=nombrecodes($row['membre_id']); ?></a></p>
                    <p style="background-color:#fefefe;padding:6px 5px;margin:0;"><i style="color:#701818" class="fas fa-comments"></i> <b style="padding:4px 5px;">Avis :</b><?= ($data['nb'] > 0 ? '<a href="'.ROOTPATH.'/profil/'.$row['idmembre'].'#comment" >'.$data['nb'].' avis</a>' : $data['nb'].' avis'); ?>
                    <p style="background-color:#F5F5F5;padding:6px 5px;margin:0;"><i style="color:#701818" class="fas fa-eye"></i> <b style="padding:4px 5px;">Vues :</b> <?php echo compteuran($row['id'], $row['vues']); ?></p>
                    <div class="boutonvoir"><a href="#contacter_parrainage" class="pboutonblanc">Contacter</a><a href="<?= ROOTPATH.'/profil/'.$row['idmembre'] ?>" class="pboutonblanc">Voir le profil</a></div>
                </aside>

                <!--OFFRE PROPOSES + BONUS-->
                <aside class="aff_droit" style="background-color:#701818;">
                <span style="color:#FFF;">Offre de parrainage proposée <i style="float:right;margin:-2px 5px 0 0;" class="fas fa-money-bill-wave-alt"></i></span>
                <div style="color:#FFF;"><br />
                    <?php
                      echo '<b>'.$row['nom_marchand'].' :</b> '.$row['offrefilleul'].'<br />';
                      echo isset($row['bonus']) && $row['bonus'] != '' ? '<b>'.$row['membre_utilisateur'].'</b> vous offre : '.$row['bonus'].'€ supplémentaire<br />' : '';
                     ?></div>
                    <br />
                </aside>

        <!-- FORMULAIRE CONTACT-->

                <aside class="aff_droit" id="contacter_parrainage">
                    <span>Contacter le parrain <i style="float:right;margin:2px 5px 0 0;color:#0080FF" class="fa fa-envelope"></i></span>
                    <div id="result" style="padding:5px;"></div>
                    <form action="../pages/include/contacter-annonceur-jqueryphp.php" method="post" id="contactmail" name="contactmail">
                        <textarea name="ca_message" id="ca_message" style="min-width:100px;height:130px;" required="required">Bonjour,
J'aimerais avoir plus d'information concernant votre offre de parrainage à savoir : ...
...

Cordialement,</textarea>
                            <br /><?php if (isset($_SESSION['message_info']))echo $_SESSION['message_info']; ?>
                        <input type="text" <?php if(isset($_SESSION['membre_utilisateur'])) echo 'style="display:none;"'; ?> name="ca_pseudo" id="ca_pseudo" placeholder="Votre nom" style="width:110px;min-width:100px;" value="<?php if (isset($_SESSION['membre_utilisateur'])) echo $_SESSION['membre_utilisateur']; ?>" required="required" />
                        <input type="text" <?php if(isset($_SESSION['membre_email'])) echo 'style="display:none;"'; ?> name="ca_email" id="ca_email" placeholder="Votre email" style="width:110px;min-width:100px;" value="<?php if (isset($_SESSION['membre_email'])) echo $_SESSION['membre_email']; ?>" required="required" />
                        <input type="text" style="display:none;" name="ca_idm" id="ca_idm" value="<?= (isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '0'); ?>" />
                        <input type="text" style="display:none;" name="ca_dest" id="ca_dest" value="<?= $row['membre_id'] ?>" />
                        <input type="text" style="display:none;" name="ca_marchand" id="ca_marchand" value="<?= $row['idmarchand'] ?>"/>
                        <input type="text" style="display:none;" name="ca_nom" id="ca_nom" value=""/>
                        <input type="checkbox" style="display:none;" name="validerform" id="validerform" value="1" />
                        <div class="flex" style="justify-content:center;margin-top:15px">
                            <?php
                            if(isset($_SESSION['membre_id'])) { ?>
                            
                                <input class="bouton" type="submit" name="contactmail" onclick="return confirm('Etes vous sûre de vouloir envoyer ce message ?');" value="Envoyer" />
                            <?php
                            }else { ?>
                                <br /><a href="" rel="popup_inscription" class="poplight bouton">Envoyer</a>
                            <?php
                            }
                            ?>
                            </div>
                    </form>
                    <br />
                </aside>

                <!--PARTAGER-->
                <aside class="aff_droit" style="text-align:center;">
                    <a style="margin-right:15px;" href="http://www.facebook.com/share.php?u=<?= $urlcanonic ?>" onclick="return fbs_click()" title="Partager ce code de parrainage sur Facebook" target="_blank"><i style="color:#3b5998" class="fab fa-facebook-square fa-3x"></i></a>
                    <a href="http://twitter.com/share?url=<?= $urlcanonic ?>&hashtags=PromoParrain&related=twitterapi/twitter&text=J'ai trouvé un super code de parrainage pour toi sur <?= $row['nom_marchand'] ?>" title="Partager ce code de parrainage sur Twitter" target="_blank"><i style="color:#00acee" class="fab fa-twitter-square fa-3x"></i></a>
                    <br />
                </aside>

            </article>
        </div>
    </section>
<br /><br />

     <!-- FIN DE PAGE : AUTRE CODE PROMO DE PARRAINAGE-->

    <section class="ar_dimension noblock_inside" style="background-color:#FFF;padding-top:20px;">
        <h3><i style="margin: 0 10px;" class="far fa-copy"></i> Autres offres de parrainage <?= $row['nom_marchand'] ?></h3>
        <article>
            <br />
            <?php
            $count = $pdo->query("SELECT COUNT(*) AS num FROM annonces_parrainage WHERE idmarchand LIKE " . $row['idmarchand']);
            $nom = $count->fetch();

            if ($nom['num'] > 0) {
                $sql = "SELECT AP.id as aid, AP.idmembre, AP.idmarchand, M.montantremise, M.montantdevise, U.membre_utilisateur, M.id, M.nom_marchand, I.id_membre, I.image, I.type FROM annonces_parrainage AP
                JOIN user U ON AP.idmembre = U.membre_id
                LEFT JOIN images I ON AP.idmembre = I.id_membre
                JOIN marchands M ON AP.idmarchand = M.id
                WHERE idmarchand LIKE " . $row['idmarchand'] . " AND (I.type = 'avatar' OR I.type IS NULL)
                LIMIT 0,6";
                $prep = $pdo->prepare($sql);
                $prep->execute();
                $row2 = $prep->fetchAll(PDO::FETCH_ASSOC);

                foreach($row2 as $res) {
                    echo '  <a style="padding:5px 15px;border:1px solid #DDD;border-radius:3px;margin-bottom:20px;" href="' . ROOTPATH . '/parrain/' . strtolower(format_url($res['nom_marchand'])) . '-' . $res['aid'] . '">
                                <p style="margin-bottom:0;font-family:BebasNeueRegular;font-size:16px;">' . $res['membre_utilisateur'] . '</p>
                                    <div style="display:flex;justify-content:center;align-items:end;background-size: cover;background-repeat: no-repeat;background-image: url(' . ROOTPATH . '/membres/images/'.(isset($res['image']) ? $res['image'] : 'default_avatar.png').');width:100px;height:100px;" class="avatar">
                    
                                </div>
                               <div style="display:flex;align-items:center;justify-content:space-between;margin:5px 0">
                               <img style="width:60px;height:auto;" src="'.ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'].'" /> ' . $res['montantremise'] . $res['montantdevise'] . '
                               </div>
                                
                            </a>';

                    //echo '<a style="margin-bottom:20px;" href="' . ROOTPATH . '/parrain/' . strtolower(format_url($res['nom_marchand'])) . '-' . $res['aid'] . '">
                    //<img style="width:100px;height:100px;margin:5px 10px;" src="' . ROOTPATH . '/membres/images/'.(isset($res['image']) ? $res['image'] : 'default_avatar.png').'" class="avatar" alt="Parrainage '.$res['nom_marchand'].' de '. $res['membre_utilisateur'] . '"/><br/>
                    //' . $res['membre_utilisateur'] . ' - ' . $res['montantremise'] . $res['montantdevise'] . '</a>';
                }
            } else {
                echo 'Aucune annonce similaire';
            }
           
            ?>
        </article>
    </section>

<!-- Code copié-->
<article id="popup_copiecode" class="popup_block" style="text-align:left;">
	<h2>Inscrivez vous pour sécuriser vos parrainages</h2><br />
	<p class='valider'>Le code promo de parrainage a bien été copié !</p><br />
        <p>Rendez vous sur <?= $row['nom_marchand'] ?> et inscrivez vous en ajoutant le code de parrainage dans la case prévue à cet effet</p>
        <h5>Inscrivez vous sur Promo-Parrain</h5>
        <p>Ceci permettra de sécuriser le parrainage par le biais d'un suivi détaillé et sécurisé avec le parrain. <br /><br />
        <b>Votre parrain prévoit de vous reverser un bonus ? </b><br />
        Afin de sécuriser un maximum la réception de ce bonus, inscrivez-vous sur promo-parrain et relancer un parrainage avec votre parrain.</p>
    
    </p>
        <a href="" rel="popup_inscription" class="poplight bouton">Inscrivez vous</a>

</article>
<article id="popup_clicklien" class="popup_block" style="text-align:left;">
	<h2>Inscrivez vous pour sécuriser vos parrainages</h2><br />
	<p class='valider'>Une nouvelle fenêtre vient de s'ouvrir</p><br />
        <p><strong>Inscrivez vous sur <?= $row['nom_marchand'] ?></strong> dans la fenêtre qui vient de s'ouvrir.</strong> <br />
        <h5>Inscrivez vous sur Promo-Parrain</h5>
        <p>Ceci permettra de sécuriser le parrainage par le biais d'un suivi détaillé et sécurisé avec le parrain. <br /><br />
        <b>Votre parrain prévoit de vous reverser un bonus ? </b><br />
        Afin de sécuriser un maximum la réception de ce bonus, inscrivez-vous sur promo-parrain et relancer un parrainage avec votre parrain.</p>
    
    </p>
        <a href="" rel="popup_inscription" class="poplight bouton">Inscrivez vous</a>

</article>

 <!-- Signaler une offre -->
<article id="popup_signaler" class="popup_block">
    <h2>Signaler une offre</h2>
    <p>Vous êtes sur le point de <b>signaler l'offre <?= $row['nom_marchand'].' de '. $row['membre_utilisateur'] ?></b></p><br />
    <div id="signaler-response"></div>
    <form action="../include/ajax-signaler.php" method="post" id="signaler">

    <div id="aff_motif"><label for="motif" class="iconic2 plus">Motif de l'abus :</label> 
            <select id="motif" name="motif">
                        <option value="">Choisissez le motif</option>
                        <option value="1" <?php if(isset($_SESSION['form_motif']) && $_SESSION['form_motif'] == 1) echo "selected"; ?>>Mauvaise catégorie</option>
                        <option value="2" <?php if(isset($_SESSION['form_motif']) && $_SESSION['form_motif'] == 2) echo "selected"; ?>>Offre incorrect</option>
                        <option value="3" <?php if(isset($_SESSION['form_motif']) && $_SESSION['form_motif'] == 3) echo "selected"; ?>>Photo inappropriée</option>
                        <option value="4" <?php if(isset($_SESSION['form_motif']) && $_SESSION['form_motif'] == 4) echo "selected"; ?>>Texte ou contenu choquant</option>
                        <option value="5" <?php if(isset($_SESSION['form_motif']) && $_SESSION['form_motif'] == 5) echo "selected"; ?>>Autre abus</option> 
            </select>
    <br /><?php if (isset($_SESSION['motif_info']))echo $_SESSION['motif_info']; ?>
    </div>
    <input type="text" name="monemail" id="monemail" <?= (isset($_SESSION['membre_email']) ?  'style="display:none" ' : '') ?> placeholder="Ajoutez votre adresse email" value="<?= (isset($_SESSION['membre_email']) ?  $_SESSION['membre_email'] : '') ?>" /><br />
    <input type="text" name="idannonce" id="idannonce" style="display:none" value="<?= $row['id'] ?>" />
    <input type="text" name="idmembre" id="idmembre" style="display:none" value="<?= (isset($_SESSION['membre_id']) ?  $_SESSION['membre_id'] : '') ?>" />
    <?php if (isset($_SESSION['monemail_info'])) echo $_SESSION['monemail_info']; ?>
    <label for="message">Votre message :</label>
    <textarea rows="5" name="message" id="message"></textarea>
    <br /><?php if (isset($_SESSION['message_info']))echo $_SESSION['message_info']; ?>
    <br />
    <input class="bouton" type="submit" name="signaler" value="Envoyer" />
    </form>
</article>

<!-- Inscription -->
<article id="popup_inscription" class="popup_block">
	<h2>Inscrivez-vous</h2><br />
	<p>Pour bénéficier de davantage de fonctionnalitées, il est essentiel de s'inscrire, ce n'est qu'une formalité en remplissant ces champs : </p>
    <!-- FORMULAIRE d'inscription membre -->
    <h2> Inscription</h2>

	<form action="<?= ROOTPATH ?>/inscription" method="post" id="inscription" style="text-align:left;">
        <div id="d_nom_utilis">
            <label for="nom_utilis">Nom d'utilisateur :</label>
            
                <input type="text" class="<?=$class ?>" name="nom_utilis" id="nom_utilis" placeholder="Inscrivez un pseudo" value="<?php if (isset($_SESSION['form']['pseudo'])) echo $_SESSION['form']['pseudo']; ?>" />
                <br />
        </div>	
        <h2> Informations de connexion</h2>

        <div id="d_email_addr">
            <label for="email_addr">Adresse email :</label>
                <input type="email" class="<?=$class ?>" name="email_addr" id="email_addr" placeholder="Ajoutez votre adresse em@il" value="<?php if (isset($_SESSION['form']['mail'])) echo $_SESSION['form']['mail']; ?>" required />
                <br />
        </div>
        <div id="d_mot_pass">
            <label for="mot_pass">Mot de passe :</label>
                <input type="password" class="<?=$class ?>" name="mot_pass" id="mot_pass" placeholder="Votre mot de passe"  value="<?php if (isset($_SESSION['form']['mdp'])) echo $_SESSION['form']['mdp']; ?>" required />
                <br />
        </div>
        <br />
        <label></label>
        <input class="bouton" type="submit" name="inscription" value="Envoyer" /><br /><br />
        </form>
</article>
<script>
	$('#contactmail').submit(function() {
		$("#result").append('<div style="text-align:center;margin:50px 0 50px;"><i class="fas fa-spinner fa-2x rotating"></i></div>');
		$.ajax({
			type: "POST",
			cache: false,
			url: "../pages/include/contacter-annonceur-jqueryphp.php",
			data: $('form#contactmail').serialize(),
			dataType: 'json',
			success: function(msg) { // si l'appel a bien fonctionné
				if (msg.erreurs == 'no') { // si la connexion en php a fonctionnée
                    $('#contactmail').hide();
					$('#result').empty().append('<div class="valider">Votre message a été envoyé au parrain.</div>');
				} else if (msg.erreurs == 'one') { // si la connexion en php a fonctionnée
                    $('#result').empty().append('<div class="erreur">Il y a une erreur dans votre formulaire, merci de le corriger.</div>');
                    if(msg.info == 'empty')
                    $('#result').append('<div class="erreur">Tous les champs sont obligatoires</div>');
                }else if (msg.erreurs == 'plusieurs') {
                    $('#result').empty().append('<div class="erreur">Il y a plusieurs erreurs dans votre formulaire de contact, merci de les corriger.</div>');
                    if(msg.info == 'empty')
                    $('#result').append('<div class="erreur">Tous les champs sont obligatoires</div>');
                }else if (msg.fvalidation == 'yes') {
                    $('#contactmail').hide();
                    $("#ca_pseudo, #ca_emailaddr, #ca_message, #ca_idm, #ca_dest, #ca_nom, #ca_emaildestinataire, #ca_marchand, #ca_validerform").prop('disabled', true); 
                    $('#result').empty().append('<div class="valider">Message envoyé .</div>');
                }
				else
                    $('#result').empty().append('<div class="erreur">Erreur inconnue</div>');

				// on affiche un message d'erreur dans le span prévu à cet effet
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#result').empty().append(xhr.responseText);
           
            }   
		});
		return false;
		// permet de rester sur la même page à la soumission du formulaire*/
	});



$(document).ready(function() {

$('#signaler').submit(function() {
data = $('#signaler').serialize();
$.ajax({
        type: "POST",
        url: "<?= ROOTPATH ?>/pages/include/ajax-signaler.php",
        data: data,
        dataType: "JSON",
        success: function (response) {
            if(response == 'ok') {
            $('#signaler').empty();
            $('#signaler-response').empty();
            $('#signaler-response').append('<div class="valider">Annonce signalée</div>');
        }
            if(response == 'error') {
            $('#signaler-response').empty();
            $('#signaler-response').append('<div class="erreur">Erreur</div>');
        }
        if(response == 'errorform') {
            $('#signaler-response').empty();
            $('#signaler-response').append('<div class="erreur">Erreur dans le formulaire</div>');
        }
        }
    });
    return false;
});

//SCRIPT DE COPIE CODE LIEN PARRAINAGE
<?= (isset($_SESSION['membre_id']) ? 'const membreid = '.$_SESSION['membre_id'].';' : '') ?>

const urlcode = document.querySelector("#copieurcode");
const codecopi = document.getElementById("codecopy");
const linkcopi = document.getElementById("linkcopy");
const linktarget = document.getElementById("linktarget");
const linktarget2 = document.getElementById("linktarget2")
const popupCodeCopy = document.getElementById("popupcode");
const popupClickLink = document.getElementById("popuplinkurl");

array = [urlcode, codecopi, linkcopi, linktarget, linktarget2];

arrayFilter = []
array.forEach(element => {
    if(element !== null)
        arrayFilter.push(element)

});


for (let i = 0; i < arrayFilter.length; i++) {
    const element = arrayFilter[i];

    element.addEventListener("mousedown", function() {

         if((element == linktarget) || (element == linktarget2)) {
            action = 1;
            $this = linkcopi;
            window.open(linktarget.href, "s", "width= 640, height= 480, left=0, top=0, resizable=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no").blur();
            window.focus()
         }
         
         if((element == codecopi) || (element == urlcode)) {
            action = 2;
            $this = codecopi;
         }
         
         $this.select();
         document.execCommand("copy");
         
         if(typeof membreid === 'undefined') {
            membreid = document.getElementById('ajN_idsender').value;
         }
        
        notification_create(action, membreid, action)
            
             
    });
    
}


// On crée la notification lors de la copie du code. 
function notification_create(action, membreid, action) {

    var data = {    "idsender" : $('#ajN_idsender').val(),
                    "idreceiver" : $('#ajN_idreceiver').val(),
                    "idannonce" :  $('#ajN_idannonce').val(),
                    "idmarchand" :  $('#ajN_idmarchand').val(),
                    "action" : action
                };
    $.ajax({
        type: "POST",
        url: "<?= ROOTPATH ?>/pages/include/ajax-parrainage-etape1.php",
        data: data,
        dataType: "JSON",
        success: function (response) {
            integer = Number.isInteger(membreid)
            if(response.etat == 'ok') {
                if(action == 1 && integer) {
                    $('#popup_copiecode').empty().append("<h2>Parrainage lancé</h2><p class='valider'>Le parrainage entre vous et le parrain a été lancé.</p><br /><p><strong>Inscrivez vous sur <?= $row['nom_marchand'] ?></strong> avec le lien de parrainage ouvert sur un second onglet de votre navigateur.</strong> <br /><h5>Que faire apres votre inscription sur <?= $row['nom_marchand'] ?></h5>Une fois votre inscritpion effectuée sur <?= $row['nom_marchand'] ?> : <b> Vous devez valider cette étape pour que votre parrain soit au courant et que la procédure de suivi soit lancée</b><br /> Rendez vous dans vos parrainages avec le lien suivant : </n> </p><br /><a style='display:flex;justify-content:center;' href='<?= ROOTPATH; ?>/membres/parrainages' class='pboutonr'>Mes parrainages</a>");
                    popupClickLink.click()
                }
                if(action == 2 && integer) {
                    $('#popup_copiecode').empty().append("<h2>Parrainage lancé</h2><p class='valider'>Le parrainage entre vous et le parrain a été lancé.</p><br /><p><strong>Inscrivez vous sur <?= $row['nom_marchand'] ?></strong> avec le code copié.</strong> <br /><h5>Que faire apres votre inscription sur <?= $row['nom_marchand'] ?></h5>Une fois votre inscritpion effectuée sur <?= $row['nom_marchand'] ?> : <b> vous devez valider cette étape pour que votre parrain soit au courant et que la procédure de suivi soit lancée</b><br /> Rendez vous dans vos parrainages avec le lien suivant : </n> </p><br /><a style='display:flex;justify-content:center;' href='<?= ROOTPATH; ?>/membres/parrainages' class='pboutonr'>Mes parrainages</a>");
                    popupCodeCopy.click()
                }
                ((response.type == 'notRegistered' || response.type == 'reactualiseparrainage') && action == 1) ? popupClickLink.click() : ((response.type == 'notRegistered' && action == 2) ? popupCodeCopy.click() : '')
                
        }
            if(response.etat == 'delai') {
                if(action == 1 && integer) {
                    $('#popup_copiecode').empty().append("<h2>Parrainage lancé</h2><p class='valider'>Le parrainage entre vous et le parrain a été lancé.</p><br /><p><strong>Inscrivez vous sur <?= $row['nom_marchand'] ?></strong> avec le lien de parrainage ouvert sur un second onglet de votre navigateur.</strong> <br /><h5>Que faire apres votre inscription sur <?= $row['nom_marchand'] ?>/h5>Une fois votre inscritpion effectuée sur <?= $row['nom_marchand'] ?> : <b> Vous devez valider cette étape pour que votre parrain soit au courant et que la procédure de suivi soit lancée</b><br /> Rendez vous dans vos parrainages avec le lien suivant : </n> </p><br /><a style='display:flex;justify-content:center;' href='<?= ROOTPATH; ?>/membres/parrainages' class='pboutonr'>Mes parrainages</a>");
                    popupClickLink.click()
                }
                if(action == 2 && integer) {
                    $('#popup_copiecode').empty().append("<h2>Parrainage lancé</h2><p class='valider'>Le parrainage entre vous et le parrain a été lancé.</p><br /><p><strong>Inscrivez vous sur <?= $row['nom_marchand'] ?></strong> avec le code copié.</strong> <br /><h5>Que faire apres votre inscription sur <?= $row['nom_marchand'] ?></h5>Une fois votre inscritpion effectuée sur <?= $row['nom_marchand'] ?> : <b> vous devez valider cette étape pour que votre parrain soit au courant et que la procédure de suivi soit lancée</b><br /> Rendez vous dans vos parrainages avec le lien suivant : </n> </p><br /><a style='display:flex;justify-content:center;' href='<?= ROOTPATH; ?>/membres/parrainages' class='pboutonr'>Mes parrainages</a>");
                    popupCodeCopy.click()
                }
               
                ((response.type == 'notRegistered' || response.type == 'reactualiseparrainage') && action == 1) ? popupClickLink.click() : ((response.type == 'notRegistered' && action == 2) ? popupCodeCopy.click() : '')
        }
            if(response.etat == 'error') {
            $('#popup_copiecode').empty().append("<h2>Parrainage lancé</h2><p class='erreur'>Erreur lors du lancement du parrainage.</p><br/><a style='display:flex;justify-content:center;' href='<?= ROOTPATH; ?>/membres/parrainages' class='pboutonr'>Mes parrainages</a>");
                popupCodeCopy.click();
        }
        }
    });
}

$('a.poplight').click(function() {
	var popID = $(this).attr('rel'); //Trouver la pop-up correspondante
	var popURL = $(this).attr('href'); //Retrouver la largeur dans le href

	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#' + popID).fadeIn().prepend('<a href="#" class="close" style="float:right"><img src="../images/close_pop.png" class="btn_close" title="Fermer" alt="Fermer" /></a>');

	//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;

	//On affecte le margin
	$('#' + popID).css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});

	//Effet fade-in du fond opaque
	$('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

	return false;
});

///Fermeture de la pop-up et du fond
jQuery('body').on('click', 'a.close, #fade, #annul_parrainage, #openregister', function(e) { //Au clic sur le body...
        this.parentNode.style.display = 'none'
        document.getElementById("fade").remove()

		return false;
	});
});

</script>

<?php }
require_once '../../elements/footer.php'; ?>