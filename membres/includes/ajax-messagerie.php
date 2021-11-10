<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';

if (isset($_POST["idfrom"]) && is_numeric($_POST["idto"])) {
    $idconvers = intval($_POST["id"]);
    $idfrom = intval($_POST["idfrom"]);
    $idto = intval($_POST["idto"]);
 
    $sql = "SELECT MES.id as id, U1.membre_id AS mid1, U2.membre_id AS mid2, U1.membre_utilisateur AS nom1, U2.membre_utilisateur AS nom2, I1.image AS image1, I2.image AS image2, I1.type, I2.type, MES.id1, MES.id2, MES.message, MES.lu1, MES.date, MES.lu1, MES.lu2, MES.conversation_id, MES.info_message, M.nom_marchand, M.img, EP.id_marchand, EP.id as EPid, EP.statut_parrainage
	FROM messagerie MES
	JOIN user U1 ON MES.id1 = U1.membre_id
	LEFT JOIN images I1 ON I1.id_membre = U1.membre_id AND I1.type = 'avatar'
	JOIN user U2 ON MES.id2 = U2.membre_id
	LEFT JOIN images I2 ON I2.id_membre = U2.membre_id AND I2.type = 'avatar'
    LEFT JOIN conversations_users CU ON MES.conversation_id = CU.id
    LEFT JOIN execparrainages EP ON EP.id_conversation = CU.id
    LEFT JOIN marchands M ON EP.id_marchand = M.id
    WHERE MES.conversation_id = :idconvers
	GROUP BY id
	ORDER by date DESC";
    $prepa = $pdo->prepare($sql);
    $GLOBALS['nb_req']++;
    $prepa->bindValue(':idconvers', $idconvers, PDO::PARAM_INT);
    $prepa->execute();
    

    $nombre = $pdo->prepare("SELECT COUNT(*) as nb FROM messagerie WHERE id2 IN(:id2,:id1) OR id1 IN(:id1,:id2)");
    $nombre->bindValue(':id1', $idto, PDO::PARAM_INT);
    $nombre->bindValue(':id2', $idfrom, PDO::PARAM_INT);
    $nombre->execute();
    $GLOBALS['nb_req']++;
    $result = $nombre->fetch(PDO::FETCH_ASSOC);
    $nb = $result['nb'];
    if ($nb >= 1) {
        while($result = $prepa->fetch(PDO::FETCH_ASSOC)) {

            $idsave = $result['id1'];
            $conversation_id = $result['conversation_id'];

            // Un parrainage
            (isset($result['EPid']) && $result['statut_parrainage'] < 5 ? '' : '');

            // On distingue les 2 utilisateurs

            if($_SESSION['membre_id'] == $result['id1']) {
                $destintaire = $result['nom2'];
                $membre_ecris = $result['nom1'];
            } else {
                $destintaire = $result['nom1'];
                $membre_ecris = $result['nom2'];
            }

            // On initialise les 2 avatars

            if($result['image2'] == 'null' OR $result['image2'] == '') 
            $result['image2'] = 'default_avatar.png';
            if($result['image1'] == 'null' OR $result['image1'] == '') 
            $result['image1'] = 'default_avatar.png';
            
            $reponse[] = 
                ["message" => str_replace("&lt;img", "<img",str_replace("&gt;", ">",$result['message'])),
                "date" => $result['date'],
                "lu1" => $result['lu1'],
                "lu2" => $result['lu2'],
                "from_id" => $result['id2'],
                "to_id" => $result['id1'],
                "pseudo_ecris" => $membre_ecris,
                "destinataire" => $destintaire,
                "from_avat" => $result['image2'],
                "to_avat" => $result['image1'],
                "marchand" => $result['nom_marchand'],
                "convid" => $conversation_id,
                "info_message" => $result['info_message'],
                "img" => $result['img']
            ];
        }

        // On indique que l'utilisateur a vu le message afin d'enlever la notification. 
        if($_SESSION['membre_id'] ==  $idfrom) {
            $sql = $pdo->prepare("UPDATE messagerie SET lu2 = 1 WHERE conversation_id = $conversation_id AND id1 = ".$_SESSION['membre_id']);
            $GLOBALS['nb_req']++;
            if ($sql->execute()) 
                $reponse["etat"] = 'lu';
        } else {
            $sql = $pdo->prepare("UPDATE messagerie SET lu1 = 1 WHERE conversation_id = $conversation_id AND id2 = ".$_SESSION['membre_id']);
            $GLOBALS['nb_req']++;
            if ($sql->execute()) 
                $reponse["etat"] = 'lu';
        }

    } else {
        $reponse["etat"] = 'nofound';
    }
    echo json_encode($reponse);
}
