<?php 

require_once '../../includes/config.php';
require_once '../../includes/function.php';

if (isset($_POST["motif"]) && isset($_POST['idannonce'])) {
	$errors = 0;
	if (isset($_POST['motif'])) {
		$motif = trim($_POST['motif']);
        $motif_result = checkisnumerique($motif);
		 if ($motif_result == 'ok') {
            $_SESSION['form_motif'] = $motif;
            $_SESSION['motif_info'] = '';
		} else if ($motif_result == 'non') {
            $_SESSION['motif_info'] = '<div class="erreurform">Le motif sélectionné n\'existe pas !</div>';
            $motif = '';
            $errors++;
		} else if ($motif_result == 'empty') {
            $_SESSION['motif_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de motif.</div>';
            $motif = '';
            $errors++;
        }
	}
	    // Adresse e-mail
    if (isset($_POST['monemail'])) {
        $monemail = trim($_POST['monemail']);
        $monemail_result = checkifmail($monemail);
        if ($monemail_result == 'isnt') {
            $_SESSION['monemail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($monemail, ENT_QUOTES) . ' n\'est pas valide.</div>';
            $monemail = '';
            $errors++;
        } else if ($monemail_result == 'ok') {
            $_SESSION['form_monemail'] = $monemail;
            $_SESSION['monemail_info'] = '';
        } else if ($monemail_result == 'empty') {
            $_SESSION['monemail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
            $monemail = '';
            $errors++;
        }
    }
	if (isset($_POST['idannonce'])) {
        $idannonce = trim($_POST['idannonce']);
        if (is_numeric($idannonce)) {
            $_SESSION['form_idannonce'] = $idannonce;
            $_SESSION['idannonce_info'] = '';
        } 
    }
	if (isset($_POST['idmembre'])) {
        $idmembre = trim($_POST['idmembre']);
        if (is_numeric($idmembre)) {
            $_SESSION['form_idmembre'] = $idmembre;
            $_SESSION['idmembre_info'] = '';
        } 
    }
// Message
		if (isset ($_POST['message'])) {
			$message = trim(nl2br($_POST['message']));
			$message_result = checkobligatoire($message);
			 if ($message_result == 'ok') {
						$_SESSION['message_info'] = '';
						$_SESSION['form_message'] = $message;
		} else if ($message_result == 'empty') {
            $_SESSION['message_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de message !</div>';
            $_SESSION['message_info'] = '';
            $errors++;
        }
		}
		
		if ($errors == 0) {
			$sql = "INSERT INTO signaler (id_annonce,motif,email,message,id_membre,date) VALUES(:id_annonce,:motif,:email,:message,:id_membre,:date)";
			$time = time();
			$sqlbind=$pdo->prepare($sql);
				$sqlbind->bindParam('id_annonce', $idannonce);
				$sqlbind->bindParam('motif',$motif);
				$sqlbind->bindParam('email',$monemail);
				$sqlbind->bindParam('message', $message);
				$sqlbind->bindParam('id_membre', $idmembre);
				$sqlbind->bindParam('date', $time);
			$GLOBALS['nb_req']++;
				
				if ($sqlbind->execute()) {
					$reponse = 'ok';
				}else {
					$reponse = 'error';
				}
	} else
	$reponse = 'errorform';
	
} else 
$reponse = 'noform';


echo json_encode($reponse);