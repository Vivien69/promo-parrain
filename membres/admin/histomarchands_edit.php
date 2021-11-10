<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$titre = 'Admin :: Edit historique';
require_once '../../elements/header2.php';
/**
 * *******Gestion avant affichage...**********
 */
    if(isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
	} else {
		$informations = Array(/*L'id de cookie est incorrect*/
        true,
        'Vous n\'&ecirc;tes pas connect&eacute;',
        'Impossible d\'accéder à cette page membre.',
        ' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
        ''. ROOTPATH . '/index.html',
        3
        );
    require_once('../../information.php');
    exit(); 
	}

$sql = "SELECT * FROM user WHERE membre_id=".$id.' AND membre_etat = 2';
$prep = $pdo->prepare($sql);
$prep->execute();

if($prep->rowCount() == 0) {
	$informations = Array(/*L'id de cookie est incorrect*/
            true,
            'Accès interdit',
            'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
            '',
            ''. ROOTPATH . '/index.html',
            3
            );
        require_once('../../information.php');
        exit();
	
} else {
while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
	$current = ((isset($_GET['type']) && $_GET['type'] == 1) ? 'admin_avis_marchands' : 'admin_avis'); ;
	 require_once '../includes/menu_membres.php';
    /**
     * *******FIN Gestion avant affichage...**********
     */
    $id = intval($_GET["id"]);

    $erreurs = 0;
	//Edit de l'histo marchand
    if(isset($_POST['edit'])) {
        if(isset($_POST['montantfilleul'])) {
            $donnee = trim($_POST['montantfilleul']);
            $result = checkvide($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['montantfilleul'] = '';
                $_SESSION['form']['montantfilleul'] = $donnee;
            } else {
                $_SESSION['info']['montantfilleul'] = '<div class="erreurform">Erreur montant filleul</div>';
                $_SESSION['form']['montantfilleul'] = '';
                $erreurs++;
            }
        }

        if(isset($_POST['montantparrain'])) {
            $donnee = trim($_POST['montantparrain']);
            $result = checkvide($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['montantparrain'] = '';
                $_SESSION['form']['montantparrain'] = $donnee;
            } else {
                $_SESSION['info']['montantparrain'] = '<div class="erreurform">Erreur montant parrain</div>';
                $_SESSION['form']['montantparrain'] = '';
                $erreurs++;
            }
        }

        if(isset($_POST['date_debut'])) {
            $donnee = trim($_POST['date_debut']);
            $result = checkvide($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['date_debut'] = '';
                $_SESSION['form']['date_debut'] = $donnee;
            } else {
                $_SESSION['info']['date_debut'] = '<div class="erreurform">Erreur date_debut</div>';
                $_SESSION['form']['date_debut'] = null;
                $erreurs++;
            }
        }

        if(isset($_POST['date_fin'])) {
            $donnee = trim($_POST['date_fin']);
            $result = checkvide($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['date_fin'] = '';
                $_SESSION['form']['date_fin'] = $donnee;
            } else {
                $_SESSION['info']['date_fin'] = '<div class="erreurform">Erreur date fin</div>';
                $_SESSION['form']['date_fin'] = null;
                $erreurs++;
            }
        }
        if(isset($_POST['boosted'])) {
            $donnee = (int) $_POST['boosted'];
            if ($_POST['boosted'] == 1) { 
                $_SESSION['info']['boosted'] = '';
                $_SESSION['form']['boosted'] = $donnee;
            } else {
                $_SESSION['info']['boosted'] = '<div class="erreurform">Erreur boosted</div>';
                $_SESSION['form']['boosted'] = '';
                $erreurs++;
            }
        } else {
            $_SESSION['info']['boosted'] = '';
            $_SESSION['form']['boosted'] = 0;
        }

            $sql = $pdo->prepare("UPDATE histo_marchands SET montantfilleul = :montantfilleul, montantparrain = :montantparrain, date_debut = :date_debut, date_fin = :date_fin, boosted = :boosted  WHERE id = :id");
            $sql->bindParam(':montantfilleul', $_SESSION['form']['montantfilleul'], PDO::PARAM_INT);
            $sql->bindParam(':montantparrain', $_SESSION['form']['montantparrain'] , PDO::PARAM_INT);
            $sql->bindParam(':boosted', $_SESSION['form']['boosted'] , PDO::PARAM_INT);
            $sql->bindParam(':date_debut', $_SESSION['form']['date_debut'], PDO::PARAM_STR);
            $sql->bindParam(':date_fin', $_SESSION['form']['date_fin'], PDO::PARAM_STR);
            $sql->bindParam(':id', $id, PDO::PARAM_INT);
            if($sql->execute()) {
                echo '<div class="valider">Histo modifié</div>';
            }
        
    }

      // On affiche le resultat
      if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $sql= 'SELECT * FROM histo_marchands WHERE id = '.$id;
        $GLOBALS['nb_req']++;
        $prep = $pdo->prepare($sql);
        $prep->execute();

    ?>

  <section class="block_inside">


	<div id="aff_liste" style="margin-bottom:10px;"><a href="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>">Retour</a></div>
  	<?php
  	if ($prep->rowcount() == 1) {
        $fetch = $prep->fetch(PDO::FETCH_ASSOC) ?>
    <h1>Modifier l'historique n°<?= $fetch['id'].' d\'un marchand' ?></h1>
	  
      <form method="post" id="edit" class="form_ajouter" style="display:flex;flex-direction:column;">
        <div>
            <label for="montantfilleul">Montant filleul :<br /></label>
            <input type="text" name="montantfilleul" value="<?= ((isset($_SESSION['form']['montantfilleul']) ? $_SESSION['form']['montantfilleul'] : isset($fetch['montantfilleul'])) ? $fetch['montantfilleul'] : ''); ?>">
            <br /><?=(isset($_SESSION['info']['montantfilleul']) != '' ? $_SESSION['info']['montantfilleul'] : '' ); ?>
        </div>
        <div>
            <label for="montantparrain">Montant parrain :<br /></label>
            <input type="text" name="montantparrain" value="<?= ((isset($_SESSION['form']['montantparrain']) ? $_SESSION['form']['montantparrain'] : isset($fetch['montantparrain'])) ? $fetch['montantparrain'] : ''); ?>">
            <br /><?=(isset($_SESSION['info']['montantparrain']) != '' ? $_SESSION['info']['montantparrain'] : '' ); ?>
        </div>
        <div>   
            <label for="montantparrain">Date début<br /></label>
            <input type="text" name="date_debut" value="<?= ((isset($_SESSION['form']['date_debut']) ? $_SESSION['form']['date_debut'] : isset($fetch['date_debut'])) ? $fetch['date_debut'] : date('Y-m-d H:m:s')); ?>">
            <br /><?=(isset($_SESSION['info']['date_debut']) != '' ? $_SESSION['info']['date_debut'] : '' ); ?>
        </div>
        <div>   
            <label for="date_fin">Date fin<br /></label>
            <input type="text" name="date_fin" value="<?= ((isset($_SESSION['form']['date_fin']) ? $_SESSION['form']['date_fin'] : isset($fetch['date_fin'])) ? $fetch['date_fin'] : ''); ?>">
            <?=(isset($_SESSION['info']['date_fin']) != '' ? $_SESSION['info']['date_fin'] : '' ); ?>
        </div>
        <div>
            <label for="boosted">Boosted<br /></label>
            <input type="checkbox" name="boosted" id="boosted" value="1" <?= ((isset($_SESSION['form']['boosted']) && $_SESSION['form']['boosted'] == 1 ? 'checked' : isset($fetch['boosted']) && $fetch['boosted'] == 1) ? 'checked' : ''); ?>><label style="margin-left:50px;" for="boosted"><span class="ui"></span></label>
            <?=(isset($_SESSION['info']['boosted']) != '' ? $_SESSION['info']['boosted'] : '' ); ?>
      </div>
            <button type="submit" class="bouton" name ="edit" form="edit" style="align-self:flex-end;">Modifier</button>
        
        </form>
    <?php
  	 
  	
  	} else echo 'Aucun message';
  	}
	 ?>

</section>

<?php } } require_once '../../elements/footer.php'; ?>