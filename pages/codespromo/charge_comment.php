<?php

    require_once '../../includes/config.php';
    require_once '../../includes/function.php';

    $marchand                           = $_GET['id'];
    //$limite = 10;
    $sql = "SELECT * FROM comments WHERE idcodepromo = ".$marchand;
    $prep = $pdo->prepare($sql);
    $prep->execute();
    while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
        echo '  <div class="commentaire-unique">
                    <p><b>'.$result['id_membre'].'</b> '.mepd($result['date']).'</p>
                    <p>'.$result['commentaire'].'</p>
                </div>';
    }


?>
