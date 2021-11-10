<?php
include_once 'config.php';
$GLOBALS['nb_req'] = 0;

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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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

function checknomarchand($data)
{
	if ($data == '') return 'empty';
	else if (strlen($data) < 2) return 'tooshort';
	else if (strlen($data) > 31) return 'toolong';

	else {
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$sql = "SELECT nom_marchand FROM marchands WHERE nom_marchand = :nom_marchand";
		$prep = $pdo->prepare($sql);
		$prep->bindParam(':nom_marchand', addslashes($data), PDO::PARAM_STR);		
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

	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
function check200carac($data)
{
	if (strlen($data) < 200) return 'tooshort';
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$sql = $pdo->prepare("SELECT membre_id, membre_utilisateur, membre_pass, membre_email, membre_CP, membre_ville, membre_etat, membre_lastco FROM user WHERE membre_id = :membre_id");
		$sql->execute(array(":membre_id" => intval($_SESSION['membre_id'])));
		$retour = $sql->fetch();
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
						'connexion.html',
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
					$sql = $pdo->prepare('UPDATE user SET membre_lastco = ?, membre_IP = ? WHERE membre_id = ?');
					$sql->execute(array(time(), $_SERVER['REMOTE_ADDR'], $retour['membre_id']));
					$GLOBALS['nb_req']++;
					if ($retour['membre_etat'] == 2) {
						$_SESSION['admin_co'] = 'connecter';
					}
				}
			}
		} else {
			header('Refresh:0;URL=' . ROOTPATH . '/deconnexion.html');
		}
	} else //On vérifie les cookies et sinon pas de session
	{
		if (isset($_COOKIE['membre_id']) && isset($_COOKIE['membre_pass'])) //S'il en manque un, pas de session.
		{
			if (intval($_COOKIE['membre_id']) != 0) {
				//idem qu'avec les $_SESSION
				$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
				$sql = $pdo->prepare("SELECT membre_id, membre_utilisateur, membre_pass, membre_email, membre_CP, membre_ville, membre_etat FROM user WHERE membre_id = :membre_id");
				$sql->execute(array(":membre_id" => intval($_COOKIE['membre_id'])));
				$retour = $sql->fetch();
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
								ROOTPATH . '/connexion.html',
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
							if ($retour['membre_etat'] == 2) {
								$_SESSION['admin_co'] = 'connecter';
							}
						}
					}
				} else {
					header('Refresh:0;URL=' . ROOTPATH . '/deconnexion.html');
				}
			} else //cookie invalide, erreur plus suppression des cookies.
			{
				$informations = array(/*L'id de cookie est incorrect*/
					true,
					'Cookie invalide',
					'Le cookie conservant votre id est corrompu, il va donc être détruit vous devez vous reconnecter.',
					'',
					'deconnexion.html',
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

function datedeftofr($data) {
	$date = DateTime::createFromFormat('Y-m-d', $data);
	$donnee = $date->format('d/m/Y');

	return $donnee;
}

function nombreannonces($idmembre)
{
	if (isset($idmembre)) {
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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

	$tampon = time();
	$diff = $tampon - $date;

	$dateDay = date('d', $date);
	$tamponDay = date('d', $tampon);
	$diffDay = $tamponDay - $dateDay;
	if ($diff == 0 && $diffDay == 0) {
		return 'A l\'instant';
	} else if ($diff < 60 && $diff > 0 && $diffDay == 0) {
		return 'Il y a ' . $diff . 's';
	} else if ($diff < 600 && $diffDay == 0) {
		return 'Il y a ' . floor($diff / 60) . 'mn et ' . floor($diff % 60) . 's';
	} else if ($diff < 3600 && $diffDay == 0) {
		return 'Il y a ' . floor($diff / 60) . 'mn';
	} else if ($diff < 7200 && $diffDay == 0) {
		return 'Il y a ' . floor($diff / 3600) . 'h et ' . floor(($diff % 3600) / 60) . 'mn';
	} else if ($diff < 24 * 3600 && $diffDay == 0) {
		return 'Aujourd\'hui à ' . date('H\hi', $date);
	} else if ($diff < 48 * 3600 && $diffDay == 1) {
		return 'Hier à ' . date('H\hi', $date);
	} else {
		return 'le ' . utf8_encode(strftime('%d %B %Y', $date)) . ($heure ? strftime(' &agrave; %Hh%M', $date) : '');
	}
}

function nombremarchands()
{
	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$sql = $pdo->query("SELECT count(*) as total FROM marchands");
	$GLOBALS['nb_req']++;
	$resultat = $sql->fetch();
	return $resultat['0'];
	$sql->closeCursor();
}

function nombrecodes($idmembre)
{
	if (isset($idmembre)) {
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
		$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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
	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	// On affiche le nombre de nouvelles notifications
	$prepare = $pdo->prepare('SELECT COUNT(*) FROM execparrainages WHERE id_parrain = '.$idmembre.' OR id_filleul = '.$idmembre);
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
	$pdo = new PDO('mysql:host=localhost;dbname=hlbf1825_reduc', 'hlbf1825', 'hdg@(qf-kCcC', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
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