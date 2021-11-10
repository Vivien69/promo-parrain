<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */

if (isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
} else {
	require_once '../elements/header2.php';
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Vous n\'&ecirc;tes pas connect&eacute;',
		'Impossible d\'accéder à cette page membre.',
		' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
		ROOTPATH,
		20
	);
	require_once('../information.php');
	exit();
}
$title = "Tableau de bord :: Promo-Parrainage";
$sql = "SELECT * FROM user WHERE membre_id=" . $id;
$prep = $pdo->prepare($sql);
$prep->execute();

if ($prep->rowCount() == 0) {
	require_once '../elements/header2.php';
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Accès interdit',
		'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
		'',
		'../index.php',
		3
	);
	require_once('../information.php');
	exit();
} else {
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
		/**
		 * *******FIN Gestion avant affichage...**********
		 */
		$titre = 'Annonces de parrainage de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';
		require_once '../elements/header2.php';
		$current = 'badges';
		require_once 'includes/menu_membres.php';
?>


<section class="block_inside">
    <article>
            <h1>Points obtenus</h1>

        <?php 

        $points =  SeeHowManyPoints($row['membre_id']);


        if(empty($points)) 
            echo '<p>Vous n\'avez pas encore cumulé de points</p><p>Commencez par personnaliser <a href="'.ROOTPATH.'/profil/'.$_SESSION['membre_id'].'">votre avatar</a> pour bénéficier des premiers points</p>';
        else
            echo "Vous avez actuellement cumulé : <br /><div class='boutonblancclass'>{$points} points</div>";
        ?>

        <h2>Utiliser mes points</h2>

        <!-- ACTUALISATION -->
        <article style="display:flex">
            <div class="presentation-categories" style="margin:10px;width:250px;cursor:pointer;border:1px solid #E6E3E3;padding:10px 20px;">
                <h5 style="margin-top:0;padding-top:0;padding-bottom:10px;text-align:center;justify-content:start;">ACTUALISATION</h5>
                <h4>- 50 points</h4>
                <div class="item-img-moyen" style="border:none;width:auto;background-image: url('http://localhost/Reduc-POO/membres/includes/uploads-img/120-racetools-1448365799.jpg');background-size:140px;"></div>
                <p style="font-size:11px;margin-top:10px;">Obtenir une 3ème actualisation ce jour</p>
            </div>

            <!-- A LA UNE -->
            <div class="presentation-categories" style="margin:10px;width:250px;cursor:pointer;border:1px solid #E6E3E3;padding:10px 20px;">
                <h5 style="margin-top:0;padding-top:0;padding-bottom:10px;text-align:center;justify-content:start;">OFFRE A LA UNE</h5>
                <h4>- 100 points</h4>
                <div class="item-img-moyen" style="border:none;width:auto;background-image: url('http://localhost/Reduc-POO/membres/includes/uploads-img/120-racetools-1448365799.jpg');background-size:140px;"></div>
                <p style="font-size:11px;margin-top:10px;">Placez une offre à la une pendant 4 jours</p>
            </div>
        </article>

    </article>

    <?php
    $stmt = $pdo->prepare('SELECT idbadge,type,date FROM userbadges WHERE idmembre = '.$_SESSION['membre_id']);
    $stmt->execute();
    $fetch = $stmt->fetchAll();
    ?>

    <article>
        <h1>Trophés de <?= $row['membre_utilisateur']; ?></h1><br />

             <?php
                $sql = 'SELECT B.*, UB.date, UB.idbadge, UB.id as identifier FROM badges B
                LEFT JOIN userbadges UB ON B.id = UB.idbadge AND UB.idmembre = '.$_SESSION['membre_id'].'
                ';
                        
                $sql = $pdo->query($sql);
                $sql = $sql->fetchAll();
                //pr($sql);
                foreach($sql as $r){

                    switch ($r['type']) {
                        //0 : Présentation profil F
                        case 0:
                            echo ($r['palier'] == 1 ? '<h3>Présentation</h3><div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <img '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').' src="'.ROOTPATH.'/membres/images/trophes/'.($r['palier'] == 1 ? 'avatar' : ($r['palier'] == 2 ? 'presentation' : ($r['palier'] == 3 ? 'presentation' : ''))).'.png">
                                        <p>'.($r['palier'] == 1 ? 'Personnalise ton avatar' : ($r['palier'] == 2 ? 'Ecris un petit mot dans ta description' : ($r['palier'] == 3 ? 'Personnalise ta photo de fond de profil !' : ''))).' <br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 3 ? '</div>' : '');
                            break;
    
                        //1 : Ajout d'une annonce de parrainage F
                        case 1:
                            
                            echo ($r['palier'] == 1 ? '<h3>Offres de Parrainage</h3>' : '');
                            
                            echo ($r['palier'] == 1 ? '<div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icooffre"></div></div>
                                        <p>J\'ai publier ma '.$r['palier'].' '.($r['palier'] == 1 ? 'ere' : 'eme').' offre'.($r['palier'] == 1 ? '' : '4').' <br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 90 ? '</div>' : '');
                        break;
                            
                            //2 : A effectué un parrainage en tant que parrain avec succès  F
                        case 2:
                            echo ($r['palier'] == 1 ? '<h3>Parrainage</h3><div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icoparrainage"></div></div>
                                        <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 40 ? '</div>' : '');
                        break;

                        //8 : A effectué un parrainage en tant que filleul avec succès F
                        case 8:
                            echo ($r['palier'] == 1 ? '<h3>Je suis un super Filleul</h3><div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icoparrainage"></div></div>
                                        <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 40 ? '</div>' : '');
                        break;

                        //3 : A obtenu des avis F
                        case 3:
                            echo ($r['palier'] == 1 ? '<h3>Avis obtenus</h3><div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icoavis"></div></div>
                                        <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 30 ? '</div>' : '');
                        break;

                        //4 : A donner son avis sur un marchand F
                        case 4:
                            echo ($r['palier'] == 1 ? '<h3>Avis aux marchands</h3><div class="mainbadges">' : '');
                            echo '  <div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                        <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icoavis"></div></div>
                                        <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                            <span>'.$r['points'].' points</span>
                                            <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                        </p>
                                    </div>';
                            echo ($r['palier'] == 30 ? '</div>' : '');
                        break;

                        //5 : Ajout d'un marchand F
                        case 5:
                            echo ($r['palier'] == 1 ? '<h3>Ajout de marchands</h3><div class="mainbadges">' : '');
                            echo '<div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icomarchands"></div></div>
                                <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                    <span>'.$r['points'].' points</span>
                                    <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                </p>
                            </div>';
                            echo ($r['palier'] == 20 ? '</div>' : '');
                        break;

                         //6 : Ajout d'un marchand F
                         case 6:
                            echo ($r['palier'] == 1 ? '<h3>Participation</h3><div class="mainbadges">' : '');
                            echo '<div id="badges-'.$r['type'].'" class="prestrophes" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;box-shadow: 0px 0px 7px #BBB;"' : '').'>
                                <div class="crown'.$r['level'].'" '.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? ' style="opacity:1;"' : '').'><div class="icomarchands"></div></div>
                                <p>J\'ai conclu '.$r['palier'].' parrainage'.($r['palier'] == 1 ? '' : 's').' avec succès<br /><br />
                                    <span>'.$r['points'].' points</span>
                                    <br /><br /><i>'.(isset($r['idbadge']) && $r['idbadge'] == $r['id'] ? '<i class="fas fa-check" style="color:#60A63A"></i> Obtenu le '.$r['date'] : '').'</i>
                                </p>
                            </div>';
                            echo ($r['palier'] == 40 ? '</div>' : '');
                        break;
                    }
                
                }
            ?>
    </article>

</section>

<?php  }
}
require_once '../elements/footer.php'; ?>