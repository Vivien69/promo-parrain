<?php
require_once '../config.php';
require_once '../function.php';
require_once '../../elements/header.php';
echo ((isset($_GET['offre']) && $_GET['offre'] != '') ? '<meta name="robots" content="noindex" />' : '');
$title = 'Contactez-nous';
require_once '../../elements/header2.php';
require_once 'Contact.class.php';

$errors = null;
$success = false;
if (isset($_POST["contact"])) {
    $form = new Formulaire($_POST['username'], $_POST['sujet'], $_POST['email'], $_POST['message'], $_POST['offre']);
    
    if($form->isEmpty()) {
        isset($_SESSION['membre_id']) ? $membre_id = $_SESSION['membre_id'] : $membre_id = null;
        $username = htmlentities(trim($_POST['username']));
        $sql = "INSERT INTO messages (nom_utilisateur,sujet,email,message,ip,id_membre,date,offre) 
        VALUES(:nom_utilisateur,:sujet,:email,:message,:ip,:id_membre,:date,:offre)";
        
        $sqlbind=$pdo->prepare($sql);
        
        $sqlbind->bindParam(':nom_utilisateur', $_POST['username'], PDO::PARAM_STR);
        $sqlbind->bindParam(':sujet', $_POST['sujet'], PDO::PARAM_STR);
        $sqlbind->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $sqlbind->bindParam(':message', $_POST['message'], PDO::PARAM_STR);
        $sqlbind->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_INT);
        $sqlbind->bindParam(':id_membre', $membre_id, PDO::PARAM_INT);
        $sqlbind->bindValue(':date', time(), PDO::PARAM_INT);
        $sqlbind->bindParam(':offre', $_POST['offre'], PDO::PARAM_STR);

        
        
        if ($sqlbind->execute()) {
            $form->emailit($pdo->lastInsertId());
            $GLOBALS['nb_req']++;
            $success = true;
            $_POST = null;
        }
    } else {
        $errors = $form->getErrors();
    }
}
                    ?>
<div class="block_inside">

<?php if(!empty($errors)) : ?>
    <div class="erreur">Il y <?= count($errors) ?> erreur<?= count($errors) > 1 ? 's' : ''; ?> dans votre formulaire, merci de <?= count($errors) > 1 ? 'les' : 'la'; ?> corriger !</div>
<?php endif ?>
<?php if($success) : ?>
    <div class="valider">Nous avons bien pris en compte votre message <strong><?= $username ?></strong> !<br/>Nous vous soumetrons une réponse au plus vite.</div>
<?php endif ?>
	<h2>Contactez-Nous</h2>

	<form action="<?= ROOTPATH ?>/contact.html" method="post" id="contact">

        <div id="form_ajouter">
            <div id="d_username">
                <label for="username">Nom d'utilisateur :</label>
                    <input type="text" <?= isset($errors['username']) ? 'style="border: 2px solid #CC0000;"' : ''; ?> name="username" id="username" placeholder="Inscrivez un pseudo" value="<?php if(isset($_POST['username'])) echo htmlspecialchars($_POST['username']);elseif(isset($_SESSION['membre_utilisateur'])) echo $_SESSION['membre_utilisateur']; ?>" />
                    <br /><div class="erreurform"><?= $errors['username'] ?? '' ?></div>
            </div>	
            
            <div id="d_sujet">
                <label for="sujet">Sujet :	</label>
                <select name="sujet" id="sujet" <?= isset($errors['sujet']) ? 'style="border: 2px solid #CC0000;"' : ''; ?>>
                    <option value="1" <?php if (isset($_POST['sujet']) && $_POST['sujet'] == "1") echo 'selected'; ?>>Demande de renseignement</option>
                    <option value="2" <?php if (isset($_POST['sujet']) && $_POST['sujet'] == "2") echo "selected"; ?>>Vous avez un problème ?</option>
                    <option value="3" <?php if (isset($_POST['sujet']) && $_POST['sujet'] == "3") echo "selected"; ?>>Signaler un abus</option>
                    <option value="5" <?php if (isset($_POST['sujet']) && $_POST['sujet'] == "5") echo "selected"; else if(isset($_GET['offre']) && !empty($_GET['offre'])) echo "selected"; ?>>Par rapport à une offre</option>
                    <option value="4" <?php if (isset($_POST['sujet']) && $_POST['sujet'] == "4") echo "selected"; ?>>Autre</option>
                </select><br /><div class="erreurform"><?= $errors['sujet'] ?? '' ?></div>
            </div>
            
            <div id="d_email">
                <label for="email">Adresse email :</label>
                    <input type="email" <?= isset($errors['email']) ? 'style="border: 2px solid #CC0000;"' : ''; ?> name="email" id="email" placeholder="Ajoutez votre adresse em@il" value="<?php if (isset($_POST['email'])) echo $_POST['email'];elseif(isset($_SESSION['membre_email'])) echo $_SESSION['membre_email']; ?>" required="required" />
                    <br /><div class="erreurform"><?= $errors['email'] ?? '' ?></div>
            </div>
            
            <div id="d_message">
                <label for="message" style="vertical-align:top;">Message :</label>
                    <textarea id="message" name="message" rows=10 <?= isset($errors['message']) ? 'style="border: 2px solid #CC0000;"' : ''; ?>><?= $_POST['message'] ?? '' ?></textarea>
                    <br /><div class="erreurform"><?= $errors['message'] ?? '' ?></div>
            </div>
            

            <p class="indication" style="margin-top:10px;margin-right:240px;">Tous les champs sont obligatoires</p>
            <input type="text" style="display:none;" name="offre" id="offre" value="<?php if (isset($_POST['offre'])) echo $_POST['offre'];elseif(isset($_GET['offre'])) echo str_replace('/', '', $_GET['offre']); ?>" />
            <br />
            <input class="bouton" type="submit" name="contact" value="Envoyer" />
    </form>
	<br /><br />
</div>
<?php require_once '../../elements/footer.php'; ?>