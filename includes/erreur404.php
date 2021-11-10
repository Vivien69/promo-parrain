<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
actualiser_session();
$title = 'Erreur 404';
require_once '../elements/header2.php';

?>

				<div class="block_inside">
			    <h1 class="iconic alaune"> Erreur 404</h1>

			    Page non trouvée ! Veuillez <b><a href="<?= ROOTPATH ?>/contact.html" alt="Contacter pour signaler votre problème">nous contacter</a></b> pour nous signaler le problème. <br /><br /><br />
			 </div>
			 
<?php require_once '../elements/footer.php';
