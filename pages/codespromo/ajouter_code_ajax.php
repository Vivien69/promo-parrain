<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
$erreurs = 0;

    //GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
        if(isset($_POST['nomsite'])) {
            $donnee = trim($_POST['nomsite']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['nomsite'] = '';
                $_SESSION['form']['nomsite'] = $donnee;
            } elseif ($result == 'empty') {
                $_SESSION['info']['nomsite'] = '<div class="erreurform">Vous n\'avez pas sélectionné de site concerné</div>';
                $_SESSION['form']['nomsite'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['idm'])) {
            $donnee = trim($_POST['idm']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['idm'] = '';
                $_SESSION['form']['idm'] = $donnee;
            } elseif ($result == 'empty') {
                $_SESSION['info']['idm'] = '<div class="erreurform">Selectionnez un site dans la liste</div>';
                $_SESSION['form']['idm'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['code'])) {
            $donnee  = trim($_POST['code']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['code'] = '';
                $_SESSION['form']['code'] = $donnee;
            } elseif ($result == 'empty') {
                $_SESSION['info']['code'] = '<div class="erreurform">Vous n\'avez pas ajouter le code promotionnel</div>';
                $_SESSION['form']['code'] = '';
                $erreurs++;
            }
        }
    //MONTANT (4) REMISE, DEVISE, MONTANT MINIMUM, BON D'ACHAT 
        if(isset($_POST['remise'])) {
            $donnee = trim($_POST['remise']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['remise'] = '';
                $_SESSION['form']['remise'] = $donnee;
            } elseif ($result == 'empty') {
                $_SESSION['info']['remise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
                $_SESSION['form']['remise'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['devise'])) {
            $donnee = trim($_POST['devise']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['devise'] = '';
                $_SESSION['form']['devise'] = $donnee;
            } elseif ($result == 'empty') {
                $_SESSION['info']['devise'] = '<div class="erreurform">Vous n\'avez pas ajouter le montant de la remise</div>';
                $_SESSION['form']['devise'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['achatminimal'])) {
            $donnee = trim($_POST['achatminimal']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['achatminimal'] = '';
                $_SESSION['form']['achatminimal'] = $donnee;
            } elseif ($result == 'non') {
                $_SESSION['info']['achatminimal'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
                $_SESSION['form']['achatminimal'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['bondachat'])) {
            $donnee = trim($_POST['bondachat']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['bondachat'] = '';
                $_SESSION['form']['bondachat'] = $donnee;
            } elseif ($result == 'non') {
                $_SESSION['info']['bondachat'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
                $_SESSION['form']['bondachat'] = '';
                $erreurs++;
            }
        }
		// VALIDITE DATE, FIN INCONNUE ou PERMANENTE
		if(isset($_POST['validite']) OR isset($_POST['validitedate'])) {
            if(!empty($_POST['validite'])) {
                    $validite = $_POST['validite'];
                    $validitesql = implode(',', $validite);
                    $_SESSION['form']['validite'] =  $validitesql;
                    $_SESSION['form']['tableauvalidite'] = $validite;
                    $_SESSION['info']['validite'] = '';
                    unset($_SESSION['form']['validitedate']);
            } elseif($_POST['validitedate'] != "") {
                    $validite = $_POST['validitedate'];
                    $_SESSION['form']['validitedate'] =  $validite;
                    $_SESSION['form']['tableauvalidite'] = [];
                    $_SESSION['info']['validite'] = '';
                    unset($_SESSION['form']['validite']);
            }
            } else {
                $_SESSION['info']['validite'] = '<div class="erreurform">Veuillez indiquer une validitée</div>';
                unset($_SESSION['form']['validite']);
                unset($_SESSION['form']['validitedate']);
                $_SESSION['form']['tableauvalidite'] = [];
                $erreurs++;
            }
        //isset($_POST['validite']['date']) && $_POST['validite']['date'] != "") OR (isset($_POST['validite']['fininconnue']) && $_POST['validite']['fininconnue'] != "") OR (isset($_POST['validite']['permanente']) && $_POST['validite']['permanente'] != "")

		//NOUVEAUX CLIENTS OU ANCIENS CLIENTS
		if (isset($_POST['clients']) && $_POST['clients'] != "") {
			$clients = $_POST['clients'];

			$clientssql = implode(',', $clients);
			$_SESSION['form']['clients'] = $clientssql;
			$_SESSION['form']['tableauclients'] = $clients;
		}
        if(isset($_POST['conditions'])) {
            $donnee = trim($_POST['conditions']);
            $result = check30carac($donnee);
            if ($result == 'ok') {
                $_SESSION['info']['conditions'] = '';
                $_SESSION['form']['conditions'] = $donnee;
            } elseif ($result == 'tooshort') {
                $_SESSION['info']['conditions'] = '<div class="erreurform">Les conditions doivent faire 30 caractères minimum</div>';
                $_SESSION['form']['conditions'] = '';
                $erreurs++;
            }
        }
        if(isset($_SESSION['membre_id'])) {
            $idmembre = $_SESSION['membre_id'];
        } else {
            $idmembre = '';
		}
		
        if($erreurs == 0){
            $ladate = time();
            
            $sql = "INSERT INTO codespromo (idmarchand,idmembre,code,montantremise,montantdevise,montantachatminimal,montantbondachat,validitedate,validiteinconnupermanente,clients,conditions,dateajout) 
            VALUES (:idmarchand,:idmembre,:code,:montantremise,:montantdevise,:montantachatminimal,:montantbondachat,:validitedate,:validiteinconnupermanente,:clients,:conditions,:dateajout)";
            $sqlbind = $pdo->prepare($sql);
            
            $sqlbind->bindParam(':idmarchand', $_SESSION['form']['idm'], PDO::PARAM_INT);
            $sqlbind->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
            $sqlbind->bindParam(':code', $_SESSION['form']['code'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantremise', $_SESSION['form']['remise'], PDO::PARAM_INT);
            $sqlbind->bindParam(':montantdevise', $_SESSION['form']['devise'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantachatminimal', $_SESSION['form']['achatminimal'], PDO::PARAM_INT);
            $sqlbind->bindParam(':montantbondachat', $_SESSION['form']['bondachat'], PDO::PARAM_INT);
            $sqlbind->bindParam(':validitedate', $_SESSION['form']['validitedate'], PDO::PARAM_STR);
            $sqlbind->bindParam(':validiteinconnupermanente', $_SESSION['form']['validite'], PDO::PARAM_INT);
            $sqlbind->bindParam(':clients', $_SESSION['form']['clients'], PDO::PARAM_INT);
            $sqlbind->bindParam(':conditions', $_SESSION['form']['conditions'], PDO::PARAM_STR);
            $sqlbind->bindParam(':dateajout', $ladate, PDO::PARAM_INT);

            if ($sqlbind->execute()) {
				$GLOBALS['nb_req']++;
                unset ($_SESSION['info'],$erreurs, $_SESSION['form'],$_SESSION['nb_erreurs']);
                echo '<div class="valider">Merci ! Votre code promo a bien été ajouté/ </div>';
            }
        } elseif ($erreurs > 0) {
            if ($erreurs == 1)
               echo '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
            else
                echo '<div class="erreur">Il y a ' . $erreurs . ' erreurs dans le formulaire, merci de les corriger !</div>';
        
        
        }
            
?>