<?php

include_once 'config.php';
$GLOBALS['nb_req'] = 0;

function getPDOObject() {
    $dsn = 'mysql:host=localhost;dbname=reduc';
    $user = 'root';
    $pass = '';
    return new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}

function vd($data)
{
    echo "<pre>" , var_dump($data) , "</pre>";
}
function pr($data)
{
    echo "<pre>" , print_r($data) , "</pre>";
}

function infos(string $erreurname, string $erreurmessage, string $redirect)
{
	$informations = array(/*L'id n'est pas un chiffre ou n'existe pas*/
		true,
		$erreurname,
		$erreurmessage,
		' - <a href="' . ROOTPATH . '/index.php">Retour aux annonces</a>',
		'' . ROOTPATH . '/contact.php',
		6
	);
	require_once '../information.php';
	exit();
}

function checkisnumerique($donnee)
{
	if (is_numeric($donnee)) return 'ok';
	if ($donnee == '') return 'empty';
	else return 'non';
}

function checkcat($typer)
{
	if ($typer == 'codep') return 'ok';
	if ($typer == 'remise') return 'ok';
	if ($typer == 'parrain') return 'ok';
	if ($typer == 'coupon') return 'ok';
	if ($typer == 'odr') return 'ok';
	else {
		return 'probleme';
	}
}

function random_password($length = 8)
{
	$chars = "abcdefghjklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";
	$numbers = "0123456789";
	$signes = "%&!*()_-=+;:,.?";
	$password = substr(str_shuffle($chars), 0, 4);
	$password .= substr(str_shuffle($numbers), 0, 2);
	$password .= substr(str_shuffle($signes), 0 , 1);
	$password = substr(str_shuffle($password), 0, $length);

	return $password;
}

function random_name($length = 15)
{
	$chars = "abcdefghjklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789";

	$name = substr(str_shuffle($chars), 0, $length);

	return $name;
}

function checkpseudo($nom_utilis)
{
	if ($nom_utilis == '') return 'empty';
	else if (strlen($nom_utilis) < 4) return 'tooshort';
	else if (strlen($nom_utilis) > 31) return 'toolong';

	else {
		$pdo = getPDOObject();
		$sql = "SELECT membre_utilisateur FROM user WHERE membre_utilisateur = :nom_utilis";
		$prep = $pdo->prepare($sql);
		$prep->bindParam(':nom_utilis', $nom_utilis, PDO::PARAM_STR);
		$prep->execute();
		$GLOBALS['nb_req']++;
		if ($prep->rowCount() > 0) return 'exists';
		else return 'ok';
		$prep->closeCursor();
	}
}
function sendmessage($id_parrain, $id_filleul, $message) {
	$pdo = getPDOObject();
	$sql = $pdo->prepare("SELECT id, COUNT(*) as nombre FROM conversations_users WHERE user_id = :user_id AND read_at = :read_at OR (user_id = :read_at AND read_at = :user_id)");
	$sql->bindParam(":user_id", $id_parrain, PDO::PARAM_INT);
	$sql->bindParam(":read_at", $id_filleul, PDO::PARAM_INT);
	$sql->execute();
	$GLOBALS['nb_req']++;
	$fetch = $sql->fetch();
	if($fetch['nombre'] == 0) {
		$prep1 = $pdo->prepare("INSERT INTO conversations_users (user_id, read_at, date_last)
		VALUES (:id1, :id2, :date_last)");
		$prep1->bindParam(':id1', $id_filleul, PDO::PARAM_INT);
		$prep1->bindParam(':id2', $id_parrain, PDO::PARAM_INT);
		$prep1->bindValue(':date_last', time(), PDO::PARAM_INT);
		$prep1->execute();
		$GLOBALS['nb_req']++;
		$id_conversation = $pdo->lastInsertId();
	} else {
		$id_conversation = $fetch['id'];
		$prep = $pdo->prepare("UPDATE conversations_users SET user_delete = 0, read_at_delete = 0 WHERE id = :id"); 
		$prep->bindValue(':id', $fetch['id'], PDO::PARAM_INT);
		$prep->execute();
	}

	$prep = $pdo->prepare("INSERT INTO messagerie (message, date, id1, id2, lu1, lu2, conversation_id, ip, info_message)
	VALUES (:message, :date, :id1, :id2, :lu1, :lu2, :conversation_id, :ip, :info_message)");
	$prep->bindValue(':message', $message, PDO::PARAM_STR);
	$prep->bindValue(':date', time(), PDO::PARAM_INT);
	$prep->bindParam(':id1', $id_filleul, PDO::PARAM_INT); // Si 0 c'est l'admin / robot qui envoi le message
	$prep->bindParam(':id2', $id_parrain, PDO::PARAM_INT);
	$prep->bindValue(':lu1', 0, PDO::PARAM_INT);
	$prep->bindValue(':lu2', 0, PDO::PARAM_INT);
	$prep->bindValue(':conversation_id', $id_conversation, PDO::PARAM_INT);
	$prep->bindValue(':ip', 0, PDO::PARAM_STR);
	$prep->bindValue(':info_message', 1, PDO::PARAM_INT);
	$prep->execute();
	$GLOBALS['nb_req']++;
}

function checknomarchand($data)
{
	if ($data == '') return 'empty';
	else if (strlen($data) < 2) return 'tooshort';
	else if (strlen($data) > 31) return 'toolong';

	else {
		$data = addslashes($data);
		$pdo = getPDOObject();
		$sql = "SELECT nom_marchand FROM marchands WHERE nom_marchand = :nom_marchand";
		$prep = $pdo->prepare($sql);
		$prep->bindParam(':nom_marchand', $data, PDO::PARAM_STR);		
		$prep->execute();
		$GLOBALS['nb_req']++;
		if ($prep->rowCount() > 0) return 'exists';
		else return 'ok';
		$prep->closeCursor();
	}
}
function checkcodeexist($data, $marchand)
{
	if ($data == '') return 'empty';

	$pdo = getPDOObject();
	$sql = "SELECT code FROM codespromo WHERE code = :code AND idmarchand = :id_marchand";
	$prep = $pdo->prepare($sql);
	$prep->bindParam(':code', $data, PDO::PARAM_STR);
	$prep->bindParam(':id_marchand', $marchand, PDO::PARAM_INT);
	$prep->execute();
	if ($prep->rowCount() > 0) return 'exists';
	else return 'ok';
	$prep->closeCursor();
	
}
function checkobligatoire($data)
{
	if ($data == '') return 'empty';
	else return 'ok';
}

function checkcodeorlien($code, $lien)
{
	if($code != '' OR $lien != '') 
	return 'ok';
	else return 'oneobligatoire';
}

function checksansverif($data)
{
	if ($data == '') return 'empty';
	else if (strlen($data) < 4) return 'tooshort';
	else if (strlen($data) > 31) return 'toolong';
	else return 'ok';
}

function checkvide($data)
{
	if ($data != '') return 'ok';
	else return 'empty';
}

function checkmemevide255($data)
{
	if (strlen($data) > 255) return 'toolong';
	else return 'ok';
}
function check30carac($data)
{
	if (strlen($data) < 30) return 'tooshort';
	else return 'ok';
}
function check150carac($data)
{
	if (strlen($data) < 150) return 'tooshort';
	else return 'ok';
}

function checksansverif50($data)
{
	if ($data == '') return 'empty';
	else if (strlen($data) < 4) return 'tooshort';
	else if (strlen($data) > 51) return 'toolong';
	else return 'ok';
}

function checkmdp($mdp)
{
	if ($mdp == '') return 'empty';
	else if (strlen($mdp) < 8) return 'tooshort';
	else if (strlen($mdp) > 30) return 'toolong';
	else return 'ok';
}

function checkmdpS($mdp, $mdp2)
{
	if ($mdp != $mdp2 && $mdp != '' && $mdp2 != '') return 'different';
	else return checkmdp($mdp);
}
function checkmdpancien($mot_pass_ancien, $id)
{
		$id = intval($id);
	if ($mot_pass_ancien == '') return 'empty';
	else {
		$pdo = new PDO('mysql:host=localhost;dbname=reduc', 'root','', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$sql = "SELECT membre_pass FROM user WHERE membre_id = $id";
		$prep = $pdo->prepare($sql);
		$prep->execute();
		$GLOBALS['nb_req']++;
		if ($data = $prep->fetch(PDO::FETCH_ASSOC)) {
			if (password_verify($mot_pass_ancien, $data['membre_pass'])) 
				return 'ok'; 
			else return 'erreur'; 
			} $prep->closeCursor();
	}
}
function checkcpville($cp, $ville)
{
	if ($cp == '') return 'emptycp';
	if ($ville == '') return 'emptyville';
	$pdo = getPDOObject();
	$sql = "SELECT ville_nom_reel FROM villes_france WHERE ville_nom_reel = '" . addslashes($ville) . "'";
	$prep = $pdo->prepare($sql);
	$prep->execute();
	$GLOBALS['nb_req']++;
	if ($prep->rowCount() <= 0) return 'villeexistpas';
	$prep->closeCursor();
	$sql2 = "SELECT ville_code_postal FROM villes_france WHERE ville_code_postal = '" . $cp . "'";
	$prep2 = $pdo->prepare($sql2);
	$prep2->execute();
	$GLOBALS['nb_req']++;
	if ($prep2->rowCount() <= 0) return 'cpexistpas';
	$prep2->closeCursor();
	$sqltotal = "SELECT ville_nom_reel,ville_code_postal FROM villes_france WHERE ville_nom_reel = '" . addslashes($ville) . "' AND ville_code_postal = '" . $cp . "'";
	$preptotal = $pdo->prepare($sqltotal);
	$preptotal->execute();
	$GLOBALS['nb_req']++;
	if ($preptotal->rowCount() > 0) return 'ok';
	else return 'concordance';
	$preptotal->closeCursor();
}

function checkmail($email_addr)
{
	if ($email_addr == '') return 'empty';
	else if (filter_var($email_addr, FILTER_VALIDATE_EMAIL) == FALSE) return 'isnt';

	else {
		$pdo = getPDOObject();
		$sql = "SELECT membre_email FROM user WHERE membre_email = '" . $email_addr . "'";
		$prep = $pdo->prepare($sql);
		$prep->execute();
		$GLOBALS['nb_req']++;
		if ($prep->rowCount() > 0) return 'exists';
		else return 'ok';
		$prep->closeCursor();
	}
}

function checkifmail($email_addr)
{
	if ($email_addr == '') return 'empty';
	else if (filter_var($email_addr, FILTER_VALIDATE_EMAIL) == FALSE) return 'isnt';
	else return 'ok';
}

function passperdu($email)
{
	if ($email == '') return 'empty';
	else if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) return 'isnt';

	else {
		$pdo = getPDOObject();
		$prep = $pdo->prepare("SELECT count(*) membre_email FROM user WHERE membre_email = :email");
		$prep->bindValue(':email', $email, PDO::PARAM_STR);
		$prep->execute();
		$GLOBALS['nb_req']++;
		$nombre = $prep->fetch();
		if ($nombre[0] == 1) return 'ok';
		elseif ($nombre[0] > 1) return 'plusieurs';
		elseif ($nombre[0] < 0) return 'existpas';
		$prep->closeCursor();
	}
}

function actualiser_session()
{

	if (isset($_SESSION['membre_id']) && intval($_SESSION['membre_id']) != 0) //Vérification id
	{
		//utilisation de la fonction sqlquery, on sait qu'on aura qu'un résultat car l'id d'un membre est unique.
		$pdo = getPDOObject();
		$sql = $pdo->prepare("SELECT U.membre_id, U.membre_utilisateur, U.membre_pass, U.membre_email, U.membre_CP, U.membre_ville, U.membre_etat, U.membre_lastco, I.image, I.type FROM user U
		LEFT JOIN images I ON U.membre_id = I.id_membre AND (I.type = 'avatar' OR I.type IS NULL)
		WHERE U.membre_id = :membre_id");
		$sql->bindParam(':membre_id', $_SESSION['membre_id'], PDO::PARAM_INT);
		$sql->execute();
		$retour = $sql->fetch();
		//pr($retour);
		$GLOBALS['nb_req']++;
		//Si l'utilisateur est admin ou modo
		if ($retour['membre_etat'] == 1 or $retour['membre_etat'] == 2) {

			//Si la requête a un résultat (c'est-à-dire si l'id existe dans la table membres)
			if ((isset($retour['membre_utilisateur']) && $retour['membre_utilisateur'] != '')) {
				if ($_SESSION['membre_pass'] != $retour['membre_pass']) {
					//Dehors vilain pas beau !
					$informations = array(/*Mot de passe de session incorrect*/
						true,
						'Session invalide',
						'Le mot de passe de votre session est incorrect, vous devez vous reconnecter.',
						'',
						'connexion',
						3
					);
					require_once(ROOTPATH.'/information.php');
					setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
					setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
					session_destroy();
					exit();
				} else {
					//Validation de la session.
					$_SESSION['membre_id'] = $retour['membre_id'];
					$_SESSION['membre_utilisateur'] = $retour['membre_utilisateur'];
					$_SESSION['membre_pass'] = $retour['membre_pass'];
					$_SESSION['membre_email'] = $retour['membre_email'];
					$_SESSION['membre_CP'] = $retour['membre_CP'];
					$_SESSION['membre_ville'] = $retour['membre_ville'];
					$_SESSION['image'] = $retour['image'];
					$sql = $pdo->prepare('UPDATE user SET membre_lastco = ?, membre_IP = ? WHERE membre_id = ?');
					$sql->execute(array(time(), $_SERVER['REMOTE_ADDR'], $retour['membre_id']));
					$GLOBALS['nb_req']++;
					if ($retour['membre_etat'] == 2) {
						$_SESSION['admin_co'] = 'connecter';
					}
				}
			}
		} else 
			header('Refresh:0;URL=' . ROOTPATH . '/deconnexion');
		
	} else //On vérifie les cookies et sinon pas de session
	{
		if (isset($_COOKIE['membre_id']) && isset($_COOKIE['membre_pass'])) //S'il en manque un, pas de session.
		{
			if (intval($_COOKIE['membre_id']) != 0) {
				//idem qu'avec les $_SESSION
				$pdo = getPDOObject();
				$sql = $pdo->prepare("SELECT U.membre_id, U.membre_utilisateur, U.membre_pass, U.membre_email, U.membre_CP, U.membre_ville, U.membre_etat, U.membre_lastco, I.image, I.type FROM user U
				LEFT JOIN images I ON U.membre_id = I.id_membre AND (I.type = 'avatar' OR I.type IS NULL)
				WHERE membre_id = :membre_id");
				$sql->bindParam(':membre_id', $_COOKIE['membre_id'], PDO::PARAM_INT);
				$sql->execute();
				$retour = $sql->fetch();
				//pr($retour);
				$GLOBALS['nb_req']++;
				if ($retour['membre_etat'] == 1 or $retour['membre_etat'] == 2) {

					if ((isset($retour['membre_utilisateur']) && $retour['membre_utilisateur'] != '')) {
						if ($_COOKIE['membre_pass'] != $retour['membre_pass']) {
							//Dehors vilain tout moche !
							$informations = array(/*Mot de passe de cookie incorrect*/
								true,
								'Mot de passe cookie erroné',
								'Le mot de passe conservé sur votre cookie est incorrect vous devez vous reconnecter.',
								'',
								ROOTPATH . '/connexion',
								3
							);
							require_once(ROOTPATH.'/information.php');
							setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
							setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
							session_destroy();
							exit();
						} else {
							//Bienvenue :D
							$sql = $pdo->prepare('UPDATE user SET membre_lastco = ?, membre_IP = ? WHERE membre_id = ?');
							$sql->execute(array(time(), $_SERVER['REMOTE_ADDR'], $retour['membre_id']));
							$GLOBALS['nb_req']++;
							$_SESSION['membre_id'] = $retour['membre_id'];
							$_SESSION['membre_utilisateur'] = $retour['membre_utilisateur'];
							$_SESSION['membre_pass'] = $retour['membre_pass'];
							$_SESSION['membre_email'] = $retour['membre_email'];
							$_SESSION['membre_CP'] = $retour['membre_CP'];
							$_SESSION['membre_ville'] = $retour['membre_ville'];
							$_SESSION['image'] = $retour['image'];
							if ($retour['membre_etat'] == 2) {
								$_SESSION['admin_co'] = 'connecter';
							}
						}
					}
				} else 
					header('Refresh:0;URL=' . ROOTPATH . '/deconnexion');
				
			} else //cookie invalide, erreur plus suppression des cookies.
			{
				$informations = array(/*L'id de cookie est incorrect*/
					true,
					'Cookie invalide',
					'Le cookie conservant votre id est corrompu, il va donc être détruit vous devez vous reconnecter.',
					'',
					'deconnexion',
					2
				);
				require_once('../information.php');
				setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
				setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
				session_destroy();
				exit();
			}
		} else {
			//Fonction de suppression de toutes les variables de cookie.
			if (isset($_SESSION['membre_id'])) unset($_SESSION['membre_id'], $_SESSION['membre_pass']);
			setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
			setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
		}
	}
}

function vider_cookie()
{
	foreach ($_COOKIE as $cle => $element) {
		setcookie($element, '', time() - 365 * 24 * 3600, '/');
	}
}

function vidersession()
{
	foreach ($_SESSION as $cle => $element) {
		unset($_SESSION[$cle]);
	}
}

function datefr($datebdd)
{
	setlocale(LC_ALL, 'fr_FR', 'fra');
	$val = explode(" ", $datebdd);
	$date = explode("-", $val[0]);
	$time = explode(":", $val[1]);
	$timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

	$ladate = strftime("Le %d %B %Y &agrave; %H:%M", $timestamp);

	return utf8_encode($ladate);
}

function datedeftofr($data, $heurs = false) {
	if($heurs) {
		$f = 'd/m/Y' . (date('H:i:s', strtotime($data)) != '00:00:00' ? ' H:i' : '');
    	$date = new DateTime($data);
		$donnee = $date->format($f);
	} else {
		$date = DateTime::createFromFormat('Y-m-d', $data);
		$donnee = $date->format('d/m/Y');
	}
	
	return $donnee;
}

function nombreannonces($idmembre)
{
	if (isset($idmembre)) {
		$pdo = getPDOObject();
		$sql = $pdo->prepare("SELECT count(id) FROM annonces_parrainage WHERE idmembre = " . $idmembre);
		$sql->execute();
		$GLOBALS['nb_req']++;
		$resultat = $sql->fetch();
		return $resultat[0];
	} else {
		return '?';
	}
}
function is_online($date, $heure = null, $icone = null)
{
	if (intval($date) == 0) return $date;

	$tampon = time();
	$diff = $tampon - $date;

	$dateDay = date('d', $date);
	$tamponDay = date('d', $tampon);
	$diffDay = $tamponDay - $dateDay;
	if ($diff < 1800 && $diffDay == 0) {
		return ($icone == 1 ? '<i title="En ligne" style="vertical-align:top;margin-left:5px;" class="green fas fa-circle"></i>' : '<i class="green"> En ligne</i>');
	} else if ($diff < 3600 && $diffDay == 0) {
		return ($icone == 1 ? '<i title="Hors ligne" style="vertical-align:top;margin-left:5px;" class="redhl fas fa-circle"></i>' : '<i style="margin-right:5px;" class="far fa-clock"></i> Il y a ' . floor($diff / 60) . 'mn');
	} else if ($diff < 7200 && $diffDay == 0) {
		return ($icone == 1 ? '<i title="Hors ligne" style="vertical-align:top;margin-left:5px;" class="redhl fas fa-circle"></i>' : '<i style="margin-right:5px;" class="far fa-clock"></i> Il y a ' . floor($diff / 3600) . 'h et ' . floor(($diff % 3600) / 60) . 'mn');
	} else if ($diff < 24 * 3600 && $diffDay == 0) {
		return ($icone == 1 ? '<i title="Hors ligne" style="vertical-align:top;margin-left:5px;" class="redhl fas fa-circle"></i>' : '<i style="margin-right:5px;" class="far fa-clock"></i> Aujourd\'hui à ' . date('H\hi', $date));
	} else if ($diff < 48 * 3600 && $diffDay == 1) {
		return ($icone == 1 ? '<i title="Hors ligne" style="vertical-align:top;margin-left:5px;" class="redhl fas fa-circle"></i>' : '<i style="margin-right:5px;" class="far fa-clock"></i> Hier à ' . date('H\hi', $date));
	} else {
		return ($icone == 1 ? '<i title="Hors ligne" style="vertical-align:top;margin-left:5px;" class="redhl fas fa-circle"></i>' : '<i style="margin-right:5px;" class="far fa-clock"></i> le ' . utf8_encode(strftime('%d %B %Y', $date)) . ($heure ? strftime(' &agrave; %Hh%M', $date) : ''));
	}
}

function mepd($date, $heure = 0)
{
	if (intval($date) == 0) return $date;

	if(!is_numeric($date)) 
	{
		$date = new DateTime($date);
		$date = $date->getTimestamp();
	}

		$tampon = time();
		$diff = $tampon - $date;
		$dateDay = date('d', $date);
		$tamponDay = date('d', $tampon);
		$diffDay = $tamponDay - $dateDay;

	if ($diff == 0 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'A l\'instant');
	} else if ($diff < 60 && $diff > 0 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'Il y a ' . $diff . 's');
	} else if ($diff < 600 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'Il y a ' . floor($diff / 60) . 'mn et ' . floor($diff % 60) . 's');
	} else if ($diff < 3600 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'Il y a ' . floor($diff / 60) . 'mn');
	} else if ($diff < 7200 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'Il y a ' . floor($diff / 3600) . 'h et ' . floor(($diff % 3600) / 60) . 'mn');
	} else if ($diff < 24 * 3600 && $diffDay == 0) {
		return ($heure ? 'Aujourd\'hui' : 'Aujourd\'hui à ' . date('H\hi', $date));
	} else if ($diff < 48 * 3600 && $diffDay == 1) {
		return ($heure ? 'le ' . utf8_encode(strftime('%d %B %Y', $date)) : 'Hier à ' . date('H\hi', $date));
	} else {
		return 'le ' . utf8_encode(strftime('%d %B %Y', $date)) . ($heure ? strftime(' &agrave; %Hh%M', $date) : '');
	}
}

function nombremarchands()
{
	$pdo = getPDOObject();
	$sql = $pdo->query("SELECT count(*) as total FROM marchands");
	$GLOBALS['nb_req']++;
	$resultat = $sql->fetch();
	return $resultat['0'];
	$sql->closeCursor();
}

function nombrecodes($idmembre)
{
	if (isset($idmembre)) {
		$pdo = getPDOObject();
		$sql = $pdo->query("SELECT count(*) as total FROM annonces_parrainage WHERE idmembre = " . $idmembre . " AND etatvalidation = 1");
		$GLOBALS['nb_req']++;
		$resultat = $sql->fetch();
		return $resultat['0'];
		$sql->closeCursor();
	} else {
		return '?';
	}
}
function nombrecom($id)
{
	if (isset($id)) {
		$pdo = getPDOObject();
		$sql = $pdo->query("SELECT count(*) as total FROM comments WHERE id_receiver = " . $id);
		$GLOBALS['nb_req']++;
		$resultat = $sql->fetch();
		return $resultat['0'];
		$sql->closeCursor();
	} else {
		return '?';
	}
}
function notemoyenne($id)
{
	if (isset($id)) {
		$pdo = getPDOObject();
		$sql = $pdo->query("SELECT note FROM comments WHERE id_receiver = " . $id);
			$GLOBALS['nb_req']++;
			$results = $sql->fetchAll();
			$nbnote = $sql->rowcount();
			if($nbnote > 0) {
				$somme = 0;
				foreach($results as $sum) {
					$somme += $sum['note'];
				}
				
				$moyenne = $somme / $nbnote;
				$output = '';
				for($i=0;$i < 5; $i++) {
                    if($i < $moyenne)
                        $type = 'fas';
                    else
                        $type = 'far';
                        $output .= "<i class='$type fa-star fa-1x' style='color:#701818;'></i>";
                }
				return $output;
				
			}
		$sql->closeCursor();
	} else {
		return '?';
	}
}

function affiche_com(int $id)
{
	if (isset($id) && $id != 0) :
		$pdo = getPDOObject();
		$sql = $pdo->query("SELECT * FROM comreduc WHERE idreduc = " . $id);
		$GLOBALS['nb_req']++;
		$result = $sql->fetch();
		echo '<div class="listcommentaires">
				<p>' . $result['idmembre'] . '</p>
				<p>' . $result['commentaire'] . '</p>
				<p style="text-align:right;">' . mepd($result['date']) . '</p>
			</div>';


	endif;
}

function compteuran($numero, $vues)
{
	if (!isset($_COOKIE['count'.$numero]) OR !isset($_SESSION['count'][$numero])) {
	$vues++;
	$pdo = getPDOObject();
	$sql = $pdo->prepare("UPDATE annonces_parrainage SET vues = :vues WHERE id = :id");
	$sql->bindParam(":id", $numero, PDO::PARAM_INT);
	$sql->bindParam(":vues", $vues, PDO::PARAM_INT);
	$sql->execute();
	}
	return $vues;
}

function lirecompteur($numero)
{
	$nomfichier = $_SERVER['DOCUMENT_ROOT']."/Reduc-POO/includes/compteurs/" . $numero . ".txt";
	if (!file_exists($nomfichier)) {
		return '0';
	} else {
		$fh = fopen($nomfichier , "r");
		$theData = fread($fh, 5);
		fclose($fh);
		return $theData;
	}
}

function nombre_notifications($idmembre, $type = 'phrase'){
	$pdo = getPDOObject();
	// On affiche le nombre de nouvelles notifications
	$prepare = $pdo->prepare('SELECT COUNT(*) FROM execparrainages WHERE (id_parrain = '.$idmembre.' OR id_filleul = '.$idmembre.') AND (deleted_parrain = 0 OR deleted_filleul = 0)');
	$prepare->execute();
	$GLOBALS['nb_req']++;
	$nb = $prepare->fetch(); 
	if($nb['0'] > 0) {
	if($type == 'phrase') {
		echo '<p>Vous avez <b>'.$nb['0'].'</b> parrainage'.($nb['0'] > 1 ? "s" : "").'</p>';
	} else {
		echo $nb['0'];
}
	}
}

function format_url($str)
{
	$str = strtolower($str);
	// On supprime les accents
	$arrayy = array("é","è","ê","à","'"," ","+");
	$arrayyy = array("e","e","e","a","","-", "");
	$str = str_replace($arrayy, $arrayyy, $str);


	// Supprime le dernier caract�re si c'est un tiret
	$str = rtrim($str, '-');
	while (strpos($str, '--') !== false)
		$str = str_replace('--', '-', $str);
	return utf8_encode($str);
}

function find_categorie($id)
{
	if ($id != "") {
		$categorie = array(
			"1" => "Alimentation-Supermarché",
			"2" => "Animaux",
			"3" => "Assurances-Mutuelles",
			"4"	=> "Auto-Moto",
			"5"	=> "Banques",
			"6"	=> "Beauté-Santé",
			"7"	=> "Bijoux-Accessoires",
			"8"	=> "Cadeaux-Box",
			"9"	=> "Cashback",
			"10" => "CD-DVD-Livres",
			"11" => "Chaussures",
			"12" => "Décoration",
			"13" => "Energies-Bois-Electricité-Gaz",
			"14" => "Enfants-Bébés-Jouets",
			"15" => "Internet-Hébergement-VPN",
			"16" => "Investissement",
			"17" => "Jardin-Fleurs",
			"18" => "Jeux-vidéo",
			"19" => "Jeux d'Argent",
			"20" => "Loisirs-Voyages",
			"21" => "Matelas-Literie",
			"22" => "Maison-Bricolage",
			"23" => "Missions-Sondages",
			"24" => "Multimédia-Electroménager",
			"25" => "Mode-Vêtements",
			"26" => "Opérateurs internet-Téléphone",
			"27" => "Optique",
			"28" => "Photo-Impression",
			"29" => "Rencontre",
			"30" => "Sport",
			"31" => "Autre",
			"32" => "Généralistes-Vente",
			"33" => "Cryptomonnaies"
		);

		return $categorie[$id];
	} else {
		return 'Inconnu';
	}
}

function nombremessagenonlus($notif = false) {
	$pdo = getPDOObject();
	$query = $pdo->query("SELECT id FROM conversations_users WHERE user_id = ".$_SESSION['membre_id']." OR read_at = ".$_SESSION['membre_id']);
	$query->execute();
	$GLOBALS['nb_req']++;
	if($query->rowCount() > 0) {
		$resultat = $query->fetchAll(PDO::FETCH_COLUMN);
		$conversations_list = implode(",", $resultat);
		$sql = $pdo->query("SELECT id1, id2, lu1, lu2, conversation_id FROM messagerie
		WHERE conversation_id IN($conversations_list)");
		$GLOBALS['nb_req']++;
		$sql->execute();
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($result) > 0) {
			$i = 0;
			foreach($result as $row) {
				if($row['id1'] == $_SESSION['membre_id'])  {
					if($row['lu2'] == 0)
					$i++;
				}
				else {
				if($row['id2'] == $_SESSION['membre_id']) 
					if($row['lu1'] == 0)
					$i++;
				}
			}
			return $notif ? ($i === 0 ? '' : $i) : $i;
			

		} else if($notif) 
			return '';

		else return '0';

		} else if($notif) 
			return '';

		else return '0';
}
function nombrenotif() {
	$pdo = getPDOObject();
	$query = $pdo->query("SELECT COUNT(*) as nb FROM notifications WHERE idmembre = ".$_SESSION['membre_id']." AND vu = 0");
	$query->execute();
	$query = $query->fetch();
	$GLOBALS['nb_req']++;
			if($query['nb'] == 0)
				return '';
			else
				return $query['nb'];
	
}
function departement($codepostal)
{
	if ($codepostal != "") {
		$nom_departement = array(
			"01" => "Ain",
			"02" => "Aisne",
			"03" => "Allier",
			"04" => "Alpes-de-Haute Provence",
			"05" => "Hautes-Alpes",
			"06" => "Alpes Maritimes",
			"07" => "Ardèche",
			"08" => "Ardennes",
			"09" => "Ariège",
			"10" => "Aube",
			"11" => "Aude",
			"12" => "Aveyron",
			"13" => "Bouches-du-Rhône",
			"14" => "Calvados",
			"15" => "Cantal",
			"16" => "Charente",
			"17" => "Charente-Maritime",
			"18" => "Cher",
			"19" => "Corrèze",
			"20" => "Corse",
			"21" => "Côte d'Or",
			"22" => "Côtes d'Armor",
			"23" => "Creuse",
			"24" => "Dordogne",
			"25" => "Doubs",
			"26" => "Drôme",
			"27" => "Eure",
			"28" => "Eure-et-Loire",
			"29" => "Finistère",
			"30" => "Gard",
			"31" => "Haute-Garonne",
			"32" => "Gers",
			"33" => "Gironde",
			"34" => "Hérault",
			"35" => "Ille-et-Vilaine",
			"36" => "Indre",
			"37" => "Indre-et-Loire",
			"38" => "Isère",
			"39" => "Jura",
			"40" => "Landes",
			"41" => "Loir-et-Cher",
			"42" => "Loire",
			"43" => "Haute-Loire",
			"44" => "Loire-Atlantique",
			"45" => "Loiret",
			"46" => "Lot",
			"47" => "Lot-et-Garonne",
			"48" => "Lozère",
			"49" => "Maine-et-Loire",
			"50" => "Manche",
			"51" => "Marne",
			"52" => "Haute-Marne",
			"53" => "Mayenne",
			"54" => "Meurthe-et-Moselle",
			"55" => "Meuse",
			"56" => "Morbihan",
			"57" => "Moselle",
			"58" => "Nièvre",
			"59" => "Nord",
			"60" => "Oise",
			"61" => "Orne",
			"62" => "Pas-de-Calais",
			"63" => "Puy-de-Dôme",
			"64" => "Pyrenées-Atlantiques",
			"65" => "Hautes-Pyrenées",
			"66" => "Pyrenées-Orientales",
			"67" => "Bas-Rhin",
			"68" => "Haut-Rhin",
			"69" => "Rhône",
			"70" => "Haute-Saône",
			"71" => "Saône-et-Loire",
			"72" => "Sarthe",
			"73" => "Savoie",
			"74" => "Haute-Savoie",
			"75" => "Paris",
			"76" => "Seine-Maritime",
			"77" => "Seine-et-Marne",
			"78" => "Yvelines",
			"79" => "Deux-Sèvres",
			"80" => "Somme",
			"81" => "Tarn",
			"82" => "Tarn-et-Garonne",
			"83" => "Var",
			"84" => "Vaucluse",
			"85" => "Vendée",
			"86" => "Vienne",
			"87" => "Haute-Vienne",
			"88" => "Vosges",
			"89" => "Yonne",
			"90" => "Territoire de Belfort",
			"91" => "Essonne",
			"92" => "Hauts-de-Seine",
			"93" => "Seine-Saint-Denis",
			"94" => "Val-de-Marne",
			"95" => "Val-d'Oise",
			"97" => "Guadeloupe",
			"972" => "Martinique",
			"973" => "Guyane",
			"974" => "Reunion",
			"975" => "Saint-Pierre-et-Miquelon",
			"976" => "Mayotte",
			"977" => "Saint-Barthelemy",
			"978" => "Saint-Martin",
			"986" => "Wallis-et-Futuna",
			"987" => "Polynesie-francaise",
			"988" => "Nouvelle-Caledonie"
		);
		$departement = substr($codepostal, 0, 2);

		return $nom_departement[$departement];
	} else {
		return 'Inconnu';
	}
}

/*
Les types :
0 : Présentation profil F
1 : Ajout d'une annonce de parrainage F
2 : A effectué un parrainage en tant que parrain avec succès  F
3 : A obtenu des avis F
4 : A donner son avis sur un marchand F
5 : Ajout d'un marchand F
6 : Participation : A signalé un changement d'offre de marchand, ou signaler une annonce non conforme ou un avis non conforme. 
7 : Ajout d'un code promo
8 : A effectué un parrainage en tant que filleul avec succès F
*/ 

//On compte le nombre d'entree (offre de parrainage, avis postés ...) pour savoir si cela permet d'obtenir un badge
function checkHowManyEntry($idmembre, $type, $bddname, $tablename = 'idmembre', $double = null, $checkbadge = 'true') {
	
	$pdo = getPDOObject();
    $sql = $pdo->query("SELECT COUNT(*) FROM $bddname WHERE $tablename = $idmembre ".($double != null ? $double.$idmembre : ''));
	$row = $sql->fetch();
	$GLOBALS['nb_req']++;

	if($checkbadge)
		checkIfBadge($idmembre, $type, $row[0]);
	else
	return $row[0];

}

//Consulter le nombre de points
function SeeHowManyPoints($idmembre) {

    $pdo = getPDOObject();
	$sql = $pdo->prepare('SELECT credit_points FROM user WHERE membre_id = :idmembre');
    $sql->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
    $sql->execute();
	$GLOBALS['nb_req']++;
    $fetch = $sql->fetch();

    return $fetch[0]; // Retourne le nombre d'entrées

}



function checkIfBadge($idmembre, $type, $palier) {

	$pdo = getPDOObject();

	//On selectionne la liste de tous les badges de type
	$sql2 = $pdo->query('SELECT * FROM badges WHERE type = '.$type, PDO::FETCH_ASSOC);
	$res = $sql2->fetchAll();
	$nb = count($res);
	$GLOBALS['nb_req']++;

	for ($i=0; $i < $nb; $i++) { 
		if($palier == $res[$i]['palier']) {
			$tab = $i;
		}
	}
	//SI $tab existe alors on a eu correspondance et on continue le script sinon pas de palier pas de badge. Fin
	if(isset($tab) && $tab != '') {
		//On confirme le palier
		if($palier == $res[$tab]['palier']) {

			
			//Le resultat correspond à un palier donc on vérifie si il a déja obtenus ce badge
			$sql = $pdo->prepare('SELECT * FROM userbadges
			WHERE type = :type AND idmembre = :idmembre
			ORDER BY date DESC');
			$sql->bindParam(':type', $type, PDO::PARAM_INT);
			$sql->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
			$sql->execute();
			$GLOBALS['nb_req']++;
			$row = $sql->fetchAll();

			if(empty($row)) {
				$execute = true;
			} else {
				foreach($row as $r) {
					if($r['idbadge'] != $res[$tab]['id']) {
						$execute = true;
					} else 
						$execute = false;
					}
			}
			//Il n'a pas ce badge donc on lui attribue
			if($execute) {
					

					$stmt = $pdo->prepare('INSERT INTO userbadges (idmembre, idbadge, type) VALUES (:idmembre,:idbadge,:type)');
					$stmt->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
					$stmt->bindParam(':idbadge', $res[$tab]['id'], PDO::PARAM_INT);
					$stmt->bindParam(':type', $type, PDO::PARAM_INT);
					$GLOBALS['nb_req']++;

					if($stmt->execute()) {
						$idBadge = $pdo->lastInsertId();
						$texte = [
							0 => 'de votre profil',
							1 => 'sur l\'ajout d\'offres de parrainages',
							2 => 'sur les parrainages effectués en tant que parrain',
							3 => 'sur les avis obtenus',
							4 => 'sur les avis postés aux marchands',
							5 => 'sur l\'ajout des marchands',
							6 => 'de participation',
							7 => 'sur l\'ajout de codes promo',
							8 => 'sur les parrainages effectués en tant que filleul',
						];
						
						addNotification($idmembre, 0, 6, $idBadge, $texte[$type]);
						$stmt2 = $pdo->prepare("UPDATE user SET credit_points = credit_points+".$res[$tab]['points']." WHERE membre_id = $idmembre"); 
						$GLOBALS['nb_req']+2;
						if($stmt2->execute()) {

						} else {
							$pdo->query("DELETE FROM userbadges WHERE id = $idBadge");
						}
					}
				}
			

		}


	} else {
		echo 'pas de palier';
	}


}

function addNotification($idmembre,$idsender,$action,$annonce,$texte) {
	$pdo = getPDOObject();
	$stmt = $pdo->prepare("INSERT INTO notifications (idmembre,idsender,action,annonce,texte) VALUES (:idmembre,:idsender,:action,:annonce,:texte)");
	$stmt->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
	$stmt->bindParam(':idsender', $idsender, PDO::PARAM_INT);
	$stmt->bindParam(':action', $action, PDO::PARAM_INT);
	$stmt->bindParam(':annonce', $annonce, PDO::PARAM_INT);
	$stmt->bindParam(':texte', $texte, PDO::PARAM_STR);
	$stmt->execute();
}


function messagerieConversationList()
{
	$pdo = getPDOObject();
	$sql = $pdo->query("SELECT id, user_id, read_at, user_delete, read_at_delete FROM conversations_users WHERE user_id = ".$_SESSION['membre_id']." OR read_at = ".$_SESSION['membre_id']);
	$sql->execute();
	$GLOBALS['nb_req']++;
	$result = $sql->fetchAll(PDO::FETCH_ASSOC);
	if ($sql->rowcount() >= 1) {
		$conversations_list = array();
		foreach($result as $row) {
			if($row['user_id'] == $_SESSION['membre_id'])
				$useris = 'user_delete';
			else if ($row['read_at'] == $_SESSION['membre_id'])
				$useris = 'read_at_delete';

				if($row[$useris] == 0)
				$conversations_list[] .= $row['id'];
		}
		$nb = count($conversations_list);
		$conversations_list = implode(",", $conversations_list);

		return 	[	'nb' => $nb,
					'conversations_list' => $conversations_list,
					'useris' => $useris
		];
	
		} else 
			return	['nb' => 0];
}

/* Notifications ACTIONS
action 1 : Copie du code d'une annonce
action 2 : Copie du lien d'une annonce
action 3 : Nouveauté dans les parrainages idsender = id filleul ou parrain & annonce = lien
action 4 : Nouveau message
action 5 : Nouvel avis sur son profil
action 6 : Nouveau badge
action 7 : Refus points*/