<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$titre = 'Admin :: Edit avis';
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
	//Edit du message traitement
    if(isset($_POST['edit_avis']) && isset($_POST['type'])) {
        if ($_POST['inputadd_comment'] != '') {
            $commentaire = htmlspecialchars($_POST['inputadd_comment']);
            if($_POST['type'] == 'marchand')
            $prep = $pdo->prepare("UPDATE comments_marchands SET commentaire = :commentaire WHERE id = :id");
            else 
            $prep = $pdo->prepare("UPDATE comments SET commentaire = :commentaire WHERE id = :id");
            $prep->bindValue(":commentaire", $commentaire, PDO::PARAM_STR);
            $prep->bindValue(":id", $id,  PDO::PARAM_INT);
            if($prep->execute()) {
                echo '<div class="valider">Commentaire modifié</div>';
            }
        }
    }

	if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $typeavis = $_GET['type'];
        if($typeavis == 1)
        $sql= 'SELECT * FROM comments_marchands WHERE id = '.$id;
        else
        $sql= 'SELECT * FROM comments WHERE id = '.$id;
        $GLOBALS['nb_req']++;
        $prep = $pdo->prepare($sql);
        $prep->execute();
        if($typeavis == 1)
        $sql2 = $pdo->prepare('UPDATE comments_marchands SET verif = 1 WHERE id = '.$id.'');
        else
        $sql2 = $pdo->prepare('UPDATE comments SET verif = 1 WHERE id = '.$id.'');
        
        $sql2->execute();
                
    ?>

  <section class="block_inside">


	<div id="aff_liste" style="margin-bottom:10px;"><a href="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>">Retour</a></div>
  	<?php
  	if ($prep->rowcount() == 1) {
        $result = $prep->fetch(PDO::FETCH_ASSOC) ?>
    <h1>Modifier le commentaire n°<?= $result['id'].' d\'un '. ((isset($_GET['type']) && $_GET['type'] == 1) ? 'marchand' : 'profil'); ?></h1>
	  
      <form method="post" id="edit_avis" style="display:flex;flex-direction:column;">
            <textarea id="inputadd_comment" name="inputadd_comment" placeholder="Ajoutez un commentaire ..."><?= $result['commentaire'] ?></textarea>
            <input type="text" style="display:none;" name="type" value="<?= ((isset($_GET['type']) && $_GET['type'] == 1) ? 'marchand' : 'profil'); ?> " />
            <button type="submit" class="bouton" name ="edit_avis" form="edit_avis" style="align-self:flex-end;">Envoyer</button>
        </form>
    <?php
  	 
  	
  	} else echo 'Aucun message';
  	}
	 ?>

</section>

<?php } } require_once '../../elements/footer.php'; ?>