<?php
    $data['erreurs'] = 0;
    //GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
        if(isset($_POST['nomsite'])) {
            $donnee = trim($_POST['nomsite']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
               $data['info']['nomsite'] = '';
               $data['form']['nomsite'] = $donnee;
            } elseif ($result == 'empty') {
               $data['info']['nomsite'] = '<div class="erreurform">Vous n\'avez pas sélectionné de site concerné</div>';
               $data['form']['nomsite'] = '';
               $data['erreurs']++;
            }
        }
        if(isset($_POST['idm'])) {
            $donnee = trim($_POST['idm']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
               $data['info']['idm'] = '';
               $data['form']['idm'] = $donnee;
            } elseif ($result == 'empty') {
               $data['info']['idm'] = '<div class="erreurform">Selectionnez un site dans la liste</div>';
               $data['form']['idm'] = '';
               $data['erreurs']++;
            }
        }
    //MONTANT (4) REMISE, DEVISE, MONTANT MINIMUM, BON D'ACHAT 
        if(isset($_POST['remise'])) {
            $donnee = trim($_POST['remise']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
               $data['info']['remise'] = '';
               $data['form']['remise'] = $donnee;
            } elseif ($result == 'empty') {
               $data['info']['remise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
               $data['form']['remise'] = '';
               $data['erreurs']++;
            }
        }
        if(isset($_POST['devise'])) {
            $donnee = trim($_POST['devise']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
               $data['info']['devise'] = '';
               $data['form']['devise'] = $donnee;
            } elseif ($result == 'empty') {
               $data['info']['devise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
               $data['form']['devise'] = '';
               $data['erreurs']++;
            }
        }
        if(isset($_POST['achatminimal'])) {
            $donnee = trim($_POST['achatminimal']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
               $data['info']['achatminimal'] = '';
               $data['form']['achatminimal'] = $donnee;
            } elseif ($result == 'non') {
               $data['info']['achatminimal'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
               $data['form']['achatminimal'] = '';
               $data['erreurs']++;
            }
        }
        if(isset($_POST['bondachat'])) {
            $donnee = trim($_POST['bondachat']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
               $data['info']['bondachat'] = '';
               $data['form']['bondachat'] = $donnee;
            } elseif ($result == 'non') {
               $data['info']['bondachat'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
               $data['form']['bondachat'] = '';
               $data['erreurs']++;
            }
        }
		// VALIDITE DATE, FIN INCONNUE ou PERMANENTE
		if(isset($_POST['validite']) OR isset($_POST['validitedate'])) {
            if(!empty($_POST['validite'])) {
                    $validite = $_POST['validite'];
                    $validitesql = implode(',', $validite);
                   $data['form']['validite'] =  $validitesql;
                   $data['form']['tableauvalidite'] = $validite;
                   $data['info']['validite'] = '';
                    unset($data['form']['validitedate']);
            } elseif($_POST['validitedate'] != "") {
                    $validite = $_POST['validitedate'];
                   $data['form']['validitedate'] =  $validite;
                   $data['form']['tableauvalidite'] = [];
                   $data['info']['validite'] = '';
                    unset($data['form']['validite']);
            }
            } else {
               $data['info']['validite'] = '<div class="erreurform">Veuillez indiquer une validitée</div>';
                unset($data['form']['validite']);
                unset($data['form']['validitedate']);
               $data['form']['tableauvalidite'] = [];
               $data['erreurs']++;
            }
        //isset($_POST['validite']['date']) && $_POST['validite']['date'] != "") OR (isset($_POST['validite']['fininconnue']) && $_POST['validite']['fininconnue'] != "") OR (isset($_POST['validite']['permanente']) && $_POST['validite']['permanente'] != "")

		//NOUVEAUX CLIENTS OU ANCIENS CLIENTS
		if (isset($_POST['clients']) && $_POST['clients'] != "") {
			$clients = $_POST['clients'];

			$clientssql = implode(',', $clients);
			$data['form']['clients'] = $clientssql;
			$data['form']['tableauclients'] = $clients;
		}
        if(isset($_POST['conditions'])) {
            $donnee = trim($_POST['conditions']);
            $result = check30carac($donnee);
            if ($result == 'ok') {
               $data['info']['conditions'] = '';
               $data['form']['conditions'] = $donnee;
            } elseif ($result == 'tooshort') {
               $data['info']['conditions'] = '<div class="erreurform">Les conditions doivent faire 30 caractères minimum</div>';
               $data['form']['conditions'] = '';
               $data['erreurs']++;
            }
        }
        if(isset($_SESSION['membre_id'])) {
            $idmembre = $_SESSION['membre_id'];
        } else {
            $idmembre = '';
		}
		
        if($data['erreurs'] == 0){
            $ladate = time();
            
            $sql = "INSERT INTO codespromo (idmarchand,idmembre,code,montantremise,montantdevise,montantachatminimal,montantbondachat,validitedate,validiteinconnupermanente,clients,conditions,dateajout) 
            VALUES (:idmarchand,:idmembre,:code,:montantremise,:montantdevise,:montantachatminimal,:montantbondachat,:validitedate,:validiteinconnupermanente,:clients,:conditions,:dateajout)";
            $sqlbind = $pdo->prepare($sql);
            
            $sqlbind->bindParam(':idmarchand',$data['form']['idm'], PDO::PARAM_INT);
            $sqlbind->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
            $sqlbind->bindParam(':code',$data['form']['code'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantremise',$data['form']['remise'], PDO::PARAM_INT);
            $sqlbind->bindParam(':montantdevise',$data['form']['devise'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantachatminimal',$data['form']['achatminimal'], PDO::PARAM_INT);
            $sqlbind->bindParam(':montantbondachat',$data['form']['bondachat'], PDO::PARAM_INT);
            $sqlbind->bindParam(':validitedate',$data['form']['validitedate'], PDO::PARAM_STR);
            $sqlbind->bindParam(':validiteinconnupermanente',$data['form']['validite'], PDO::PARAM_INT);
            $sqlbind->bindParam(':clients',$data['form']['clients'], PDO::PARAM_INT);
            $sqlbind->bindParam(':conditions',$data['form']['conditions'], PDO::PARAM_STR);
            $sqlbind->bindParam(':dateajout', $ladate, PDO::PARAM_INT);

            if ($sqlbind->execute()) {
                $GLOBALS['nb_req']++;
                $data['valide'] = '<div class="valider">Votre remise a bien été ajouté. Toute l\'équipe vous remercie pour votre contribution.</div>';
            }
        } elseif ($data['erreurs'] > 0) {
            if ($data['erreurs'] == 1)
               $data['erreurs_info'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
            else
               $data['erreurs_info'] = '<div class="erreur">Il y a ' . $data['erreurs'] . ' erreurs dans le formulaire, merci de les corriger !</div>';
              
        }
        echo json_encode($data);
?>