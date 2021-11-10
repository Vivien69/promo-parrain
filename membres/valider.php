<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
actualiser_session();

$titre = 'Validation de votre compte';
require_once '../elements/header2.php';
if (isset($_SESSION['membre_id'])) {
    $informations = Array(/*Membre qui essaie de se connecter alors qu'il l'est déjà*/
        true,
        'Vous êtes déjà connecté',
        'Vous êtes déjà connecté avec le pseudo <span class="infotexte">' . htmlspecialchars($_SESSION['membre_utilisateur'], ENT_QUOTES) . '</span>.',
        ' - <a href="' . ROOTPATH . '/deconnexion">Se déconnecter</a>',
        ROOTPATH . '/membres/',
        3
        );

    require_once('../information.php');
    exit();
}

$_SESSION['erreurs'] = 0;
?>
<div class="block_inside">
<h2> Validation de votre compte</h2>
<?php
if (isset($_GET['log']) && isset($_GET['valid'])) {
	$idverif = $_GET['log'];
	$cle = $_GET['valid'];
    
	$sql = $pdo->prepare("SELECT membre_utilisateur,membre_cle,membre_etat FROM user WHERE membre_cle = :mem_cle");

    if($sql->execute(array(":mem_cle" => $cle)) && $row = $sql->fetch())
    {
    
    $clebdd = $row['membre_cle'];	// Récupération de la clé
    $actif = $row['membre_etat']; // $actif contiendra alors 0 ou 1
    $GLOBALS['nb_req']++;
    }
	if($actif == '1') // Si le compte est déjà actif on prévient
  {
     echo "Votre compte est déjà actif !";
  }
else // Si ce n'est pas le cas on passe aux comparaisons
  {
     if($cle == $clebdd) // On compare nos deux clés	
       {
          // La requête qui va passer notre champ actif de 0 à 1
          $stmt = $pdo->prepare("UPDATE user SET membre_etat = 1 WHERE membre_cle like :mem_cle ");
          $stmt->bindParam(':mem_cle', $clebdd);
          if($stmt->execute()) {
            unset($_SESSION['inscrit']);
          	// Si elles correspondent on active le compte !	
          echo '<div class="valider">Votre compte a bien été activé !</div>';
          }

          
		
       }
     else // Si les deux clés sont différentes on provoque une erreur...
          echo '<div class="erreur">Erreur ! Votre compte ne peut être activé...';
  }
  
    
}
?>


<div class="hrl"></div>
<br /><br />
<a class="pboutonr" style="text-decoration:none;line-height:35px;color:#FFF;width:250px;margin-left:300px;" title="Connexion" href="<?php echo ROOTPATH; ?>/connexion">Connexion</a></p><br />
	</div>

<?php require_once '../elements/footer.php'; ?>