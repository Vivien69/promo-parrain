<?php
require_once '../includes/config.php';

setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
session_destroy();
require_once '../includes/function.php';
require_once '../elements/header.php';
require_once '../elements/header2.php';
$informations = Array(/*Déconnexion*/
                false,
                'Déconnexion',
                'Vous êtes à présent déconnecté.',
                ' - <a href="'.ROOTPATH.'/connexion">Se connecter</a>',
                ROOTPATH,
                2
                );

require_once '../information.php';
exit();

require_once '../elements/footer.php';

?>