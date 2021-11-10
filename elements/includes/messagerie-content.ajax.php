<?php
    require_once '../../includes/config.php';
    require_once '../../includes/function.php';

if(isset($_POST['json'])) {

    $json =  json_decode($_POST['json']);
    $idmembre = (int) $json->{'idmembre'};
    $limit =  (int) $json->{'limit'};
    $startm =  (int) $json->{'startm'};

    $conversations_list = messagerieConversationList();

    if($conversations_list['nb'] > 0 ) {

        $sql="SELECT U1.membre_id AS mid1, U2.membre_id AS mid2, U1.membre_utilisateur AS nom1, U2.membre_utilisateur AS nom2, I1.image AS image1, I2.image AS image2, MES.id1, MES.id2, MES.message, MES.lu1, MES.lu2, MES.date, MES.conversation_id, MES.id, MES.info_message, CU.read_at_delete, CU.user_delete FROM messagerie MES
            JOIN user U1 ON MES.id1 = U1.membre_id
            LEFT JOIN images I1 ON I1.id_membre = U1.membre_id AND I1.type = 'avatar'
            JOIN user U2 ON MES.id2 = U2.membre_id
            LEFT JOIN images I2 ON I2.id_membre = U2.membre_id AND I2.type = 'avatar'
            LEFT JOIN conversations_users CU ON MES.conversation_id = CU.id
            LEFT JOIN execparrainages EP ON EP.id_conversation = CU.id
            INNER JOIN (
                SELECT conversation_id, MAX(id) AS id
                FROM messagerie GROUP BY conversation_id
                ) AS max ON (max.conversation_id = MES.conversation_id AND max.id = mes.id)
        WHERE MES.conversation_id IN (".$conversations_list['conversations_list'].") AND (CU.".$conversations_list['useris']." = 0)
        GROUP by conversation_id
        ORDER by MES.date DESC
        LIMIT $startm,$limit";

        $prep = $pdo->prepare($sql);
        $prep->execute();
        $GLOBALS['nb_req']++;
        $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);

        $nb = count($fetch);
        for ($i=0; $i < $nb ; $i++) { 
            $fetch[$i]['date'] = mepd($fetch[$i]['date']);
        }
            } else {
                $fetch = '';
            }
        echo json_encode($fetch);
}