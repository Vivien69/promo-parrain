<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */

if (isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
} else {
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Vous n\'&ecirc;tes pas connect&eacute;',
		'Impossible d\'accéder à cette page membre.',
		' - <a href="' . ROOTPATH . '/connexion">Se connecter</a>',
		ROOTPATH,
		20
	);
	require_once('../information.php');
	exit();
}

if(isset($_POST['conf_onlineedit'])) {
	
	if(isset($_POST['online_conf']) && $_POST['online_conf'] == 1)
		$conf_online = 1;
	 else
		$conf_online = 0;
	if(isset($_POST['datemask_conf']) && $_POST['datemask_conf'] == 1)
		$conf_datemask = 1;
	 else
		$conf_datemask = 0;

		
		
		$sql = $pdo->prepare("UPDATE user SET conf_online = :conf_online, conf_datemask = :conf_datemask WHERE membre_id=:id");
		$sql->bindParam(":conf_online", $conf_online, PDO::PARAM_INT);
		$sql->bindParam(":conf_datemask", $conf_datemask, PDO::PARAM_INT);
		$sql->bindParam(":id", $id, PDO::PARAM_INT);
		if ($sql->execute()) {
			$statut = '<div class="valider">Statut confidentiel mis à jour.</div>';
		} 
	}

$sql = "SELECT * FROM user WHERE membre_id=" . $id;
$prep = $pdo->prepare($sql);
$prep->execute();


if ($prep->rowCount() == 0) {
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Accès interdit',
		'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
		'',
		'../index.php',
		3
	);
	require_once('../information.php');
	exit();
} else {
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
		/**
		 * *******FIN Gestion avant affichage...**********
		 */
		$title = 'Paramètres de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'parametres';
		require_once 'includes/menu_membres.php';
?>

		<?php
		if (isset($_GET["deletecpt"]) && $_GET["deletecpt"] == 'ok') {
			$id = $row['membre_id'];

			$sql = "SELECT * FROM user WHERE membre_id = " . $id;
			$prepa = $pdo->prepare($sql);
			$prepa->execute();
			$GLOBALS['nb_req']++;
			if ($prepa->rowcount() == 1) {
				$sql1 = $pdo->prepare('DELETE FROM annonces_parrainage WHERE idmembre = ' . $id);
				$sql2 = $pdo->prepare('UPDATE user SET membre_etat = :etat WHERE membre_id = ' . $id);
				$sql2->bindValue(':etat', 4, PDO::PARAM_INT);
				if ($sql1->execute() & $sql2->execute()) {
					$GLOBALS['nb_req']+2;

					unset($_GET);
					setcookie('membre_id', '', time() - 365 * 24 * 3600, "/");
					setcookie('membre_pass', '', time() - 365 * 24 * 3600, "/");
					session_destroy();
					$informations = array(/*L'id de cookie est incorrect*/
						true,
						'Compte supprimé',
						'Votre compte a été supprimé ! ',
						'',
						ROOTPATH,
						10
					);
					require_once('../information.php');
					exit();
				}
			} else {
				echo '<div class="erreur">Erreur, merci de réeessayer ultérieurement ou de nous contacter.</div>';
			}
		}

		
		$_SESSION['erreurs'] = 0;
		if (isset($_POST['modifier_all'])) {
			// Civilité
			if (isset($_POST['civilite'])) {
				$civilite = trim(intval($_POST['civilite']));
				$civilite_result = checkobligatoire($civilite);
				if ($civilite_result == 'ok') {
					$_SESSION['civilite_info'] = '';
					$_SESSION['form_civilite'] = $civilite;
					$_SESSION['membre_civilite'] = $civilite;
				}
			}
			// Nom
			if (isset($_POST['nom'])) {
				$nom = trim($_POST['nom']);
				$nom_result = checkmemevide255($nom);
				if ($nom_result == 'toolong') {
					$_SESSION['nom_info'] = '<div class="erreurform">Le nom ' . htmlspecialchars($nom, ENT_QUOTES) . ' est trop long (maximum 255 caract&egrave;res).</div>';
					$nom = '';
					$_SESSION['erreurs']++;
				} else if ($nom_result == 'ok') {
					$_SESSION['nom_info'] = '';
					$_SESSION['form_nom'] = $nom;
					$_SESSION['membre_nom'] = $nom;
				}
			}
			// Prénom
			if (isset($_POST['prenom'])) {
				$prenom = trim($_POST['prenom']);
				$prenom_result = checkmemevide255($prenom);
				if ($prenom_result == 'toolong') {
					$_SESSION['prenom_info'] = '<div class="erreurform">Le prénom ' . htmlspecialchars($prenom, ENT_QUOTES) . ' est trop long (maximum 255 caract&egrave;res).</div>';
					$prenom = '';
					$_SESSION['erreurs']++;
				} else if ($prenom_result == 'ok') {
					$_SESSION['prenom_info'] = '';
					$_SESSION['form_prenom'] = $prenom;
					$_SESSION['membre_prenom'] = $prenom;
				}
			}

			// PAYS
			if (isset($_POST['pays'])) {
				$pays = trim($_POST['pays']);
				$pays_result = checkisnumerique($pays);
				if ($pays_result == 'ok') {
					$_SESSION['pays_info'] = '';
					$_SESSION['form_pays'] = $pays;
					$_SESSION['membre_pays'] = $pays;
				}
			}
			// Codepostal + ville
			if (isset($_POST['infodep'])) {
				$code_postal = trim(intval($_POST['infodep']));
				if (isset($_POST['ville']) && $_POST['ville'] != '') {
					$ville = trim($_POST['ville']);
					$cpville_result = checkcpville($code_postal, $ville);
					if ($cpville_result == 'emptycp') {
						$_SESSION['ville_info'] = '';
						$_SESSION['cp_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de code postal.</div>';
						$_SESSION['form_cp'] = '';
						$_SESSION['erreurs']++;
					} else if ($cpville_result == 'emptyville') {
						$_SESSION['ville_info'] = '<div class="erreurform">Vous n\'avez pas entré de ville.</div>';
						$_SESSION['form_ville'] = '';
						$_SESSION['form_cp'] = $code_postal;
						$_SESSION['cp_info'] = '';
						$_SESSION['erreurs']++;
					} else if ($cpville_result == 'cpexistpas') {
						$_SESSION['form_ville'] = $ville;
						$_SESSION['ville_info'] = '';
						$_SESSION['cp_info'] = '<div class="erreurform">Le code postal ' . htmlspecialchars($code_postal, ENT_QUOTES) . ' n\'existe pas dans notre base de donnée !</div>';
						$_SESSION['form_cp'] = '';
						$_SESSION['erreurs']++;
					} else if ($cpville_result == 'villeexistpas') {
						$_SESSION['ville_info'] = '<div class="erreurform">La ville ' . htmlspecialchars($ville, ENT_QUOTES) . ' n\'existe pas dans notre base de donnée !</div>';
						$_SESSION['form_cp'] = $code_postal;
						$_SESSION['cp_info'] = '';
						$_SESSION['form_ville'] = '';
						$_SESSION['erreurs']++;
					} else if ($cpville_result == 'concordance') {
						$_SESSION['cp_info'] == '';
						$_SESSION['ville_info'] = '<div class="erreurform">La ville et le code postal ne correspondent pas !</div>';
						$_SESSION['form_ville'] = '';
						$_SESSION['form_cp'] = '';
						$_SESSION['erreurs']++;
					} else if ($cpville_result == 'ok') {
						$_SESSION['cp_info'] = '';
						$_SESSION['ville_info'] = '';
						$_SESSION['form_cp'] = $code_postal;
						$_SESSION['form_ville'] = $ville;
						$_SESSION['membre_cp'] = $code_postal;
						$_SESSION['membre_ville'] = $ville;
					}
				}
			}
			if ($_SESSION['erreurs'] == 0) {
				$sql1 = $pdo->prepare("UPDATE user SET membre_civilite = :civilite, membre_nom = :nom, membre_prenom = :prenom, membre_pays = :pays, membre_CP = :CP, membre_ville = :ville WHERE membre_id=:id");
				$sql1->bindParam(":civilite", $civilite);
				$sql1->bindParam(":nom", $nom);
				$sql1->bindParam(":prenom", $prenom);
				$sql1->bindParam(":pays", $pays);
				$sql1->bindParam(":CP", $code_postal);
				$sql1->bindParam(":ville", $ville);
				$sql1->bindParam(":id", $id);
				if ($sql1->execute()) {

					unset($_SESSION['civilite_info'], $_SESSION['form_civilite'], $_SESSION['nom_info'], $_SESSION['form_nom'], $_SESSION['prenom_info'], $_SESSION['form_prenom'], $_SESSION['pays_info'], $_SESSION['form_pays'], $_SESSION['cp_info'], $_SESSION['form_cp'], $_SESSION['ville_info'], $_SESSION['form_ville']);
		?>
					<div class="valider">Vos informations ont étaient mises à jour.</div>

				<?php }
			} elseif ($_SESSION['erreurs'] > 0) {
				if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
				else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreurs'] . ' erreurs dans votre formulaire, merci de les corriger !</div>';

				echo $_SESSION['nb_erreurs'];
			}
		}

		$_SESSION['erreursmagasin'] = 0;

		if (isset($_POST['modifier_magasin'])) {
			if (isset($_POST['infosmagasin'])) {
				$infosmagasin = trim($_POST['infosmagasin']);
				$infosmagasin_result = checkisnumerique($infosmagasin);
				if ($infosmagasin_result == 'ok') {
					$_SESSION['form_infosmagasin'] = $infosmagasin;
				}
			}
			if ($_SESSION['erreursmagasin'] == 0) {
				$sql1 = $pdo->prepare("UPDATE user SET membre_plusinfos = :plusinfos WHERE membre_id=:id");
				$sql1->bindParam(":plusinfos", $infosmagasin);
				$sql1->bindParam(":id", $id);
				if ($sql1->execute()) {
					if($infosmagasin != '')
					checkIfBadge($_SESSION['membre_id'], 0, 2);
					
					unset($_SESSION['form_infosmagasin']);

				?>
					<div class="valider">Votre description à été mise à jour.</div>

				<?php }
			} elseif ($_SESSION['erreursmagasin'] > 0) {
				if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
				else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreurs'] . ' erreurs dans votre formulaire, merci de les corriger !</div>';

				echo $_SESSION['nb_erreurs'];
			}
		}
	

		$_SESSION['erreursmail'] = 0;

		if (isset($_POST['modifier_email'])) {
			if (isset($_POST['email_addr2'])) {
				$email_addr2 = trim($_POST['email_addr2']);
				$mail_result = checkmail($email_addr2);
				if ($mail_result == 'isnt') {
					$_SESSION['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr2, ENT_QUOTES) . ' n\'est pas valide.</div>';
					$_SESSION['form_mail'] = '';
					$_SESSION['erreursmail']++;
				} else if ($mail_result == 'exists') {
					$_SESSION['mail_info'] = '<div class="erreurform">L\'adresse email ' . htmlspecialchars($email_addr2, ENT_QUOTES) . ' est d&eacute;j&agrave; pris !</div>';
					$_SESSION['form_mail'] = '';
					$_SESSION['erreursmail']++;
				} else if ($mail_result == 'ok') {
					$_SESSION['form_mail'] = $email_addr2;
				} else if ($mail_result == 'empty') {
					$_SESSION['mail_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; d\'adresse email.</div>';
					$_SESSION['form_mail'] = '';
					$_SESSION['erreursmail']++;
				}
			}
			if ($_SESSION['erreursmail'] == 0 && isset($_POST['email_addr2'])) {
				$sql1 = $pdo->prepare("UPDATE user SET membre_email =:email_addr2 WHERE membre_id=:id");
				$sql1->bindParam(":email_addr2", $email_addr2);
				$sql1->bindParam(":id", $id);
				if ($sql1->execute()) {
					unset($_SESSION['mail_info'], $_SESSION['form_mail']);

				?>
					<div class="valider">Votre adresse email à été mise à jour.</div>

				<?php }
			} elseif ($_SESSION['erreursmail'] > 0) {
				if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
				else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreursmail'] . ' erreurs dans votre formulaire, merci de les corriger !</div>';

				echo $_SESSION['nb_erreurs'];
			}
		}
		$_SESSION['erreursmotpass'] = 0;
		if (isset($_POST['modifier_mot_pass'])) {
			// Mot de passe ancien
			if (isset($_POST['mot_pass_ancien'])) {
				$mot_pass_ancien = trim($_POST['mot_pass_ancien']);
				$mot_pass_ancien_result = checkmdpancien($mot_pass_ancien, $id);
				if ($mot_pass_ancien_result == 'empty') {
					$_SESSION['mdpancien_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de mot de passe.</div>';
					$_SESSION['form_mdpancien'] = '';
					$_SESSION['erreursmotpass']++;
				} else if ($mot_pass_ancien_result == 'erreur') {
					$_SESSION['mdpancien_info'] = '<div class="erreurform">L\'ancien mot de passe entré est érroné.</div>';
					$_SESSION['form_mdpancien'] = '';
					$_SESSION['erreursmotpass']++;
				} else if ($mot_pass_ancien_result == 'ok') {
					$_SESSION['mdpancien_info'] = '';
					$_SESSION['form_mdpancien'] = $mot_pass_ancien;
				}
			}
			// Mot de passe nouveau
			if (isset($_POST['mot_pass'])) {
				$mot_pass = trim($_POST['mot_pass']);
				$mdp_result = checkmdp($mot_pass);
				if ($mdp_result == 'tooshort') {
					$_SESSION['mdp_info'] = '<div class="erreurform">Le mot de passe entr&eacute; est trop court. (minimum 8 caract&egrave;res).</div>';
					$_SESSION['form_mdp'] = '';
					$_SESSION['erreursmotpass']++;
				} else if ($mdp_result == 'toolong') {
					$_SESSION['mdp_info'] = '<div class="erreurform">Le mot de passe entr&eacute; est trop long. (maximum 30 caract&egrave;res)</div>';
					$_SESSION['form_mdp'] = '';
					$_SESSION['erreursmotpass']++;
				} else if ($mdp_result == 'ok') {
					$_SESSION['mdp_info'] = '';
					$_SESSION['form_mdp'] = $mot_pass;
				} else if ($mdp_result == 'empty') {
					$_SESSION['mdp_info'] = '<div class="erreurform">Vous n\'avez pas entr&eacute; de mot de passe.</div>';
					$_SESSION['form_mdp'] = '';
					$_SESSION['erreursmotpass']++;
				}
			}
			// Mot de passe 2 verification
			if (isset($_POST['mot_pass2'])) {
				$mot_pass2 = trim($_POST['mot_pass2']);
				$mot_pass2_result = checkmdpS($mot_pass2, $mot_pass);
				if ($mot_pass2_result == 'different') {
					$_SESSION['mdp_verif_info'] = '<div class="erreurform">Le mot de passe de v&eacute;rification diff&egrave;re du mot de passe.</div>';
					$_SESSION['form_mdp_verif'] = '';
					$_SESSION['erreursmotpass']++;
				} else {
					if ($mot_pass2_result == 'ok') {
						$_SESSION['form_mdp_verif'] = $mot_pass2;
						$_SESSION['mdp_verif_info'] = '';
					} else {
						$_SESSION['mdp_verif_info'] = str_replace('passe', 'passe de v&eacute;rification', $_SESSION['mdp_info']);
						$_SESSION['form_mdp_verif'] = '';
						$_SESSION['erreursmotpass']++;
					}
				}
			}

			if ($_SESSION['erreursmotpass'] == 0 && isset($_POST['mot_pass']) && isset($_POST['mot_pass2'])) {
				$hash = password_hash($_POST['mot_pass'], PASSWORD_ARGON2ID);
				$sql2 = $pdo->prepare("UPDATE user SET membre_pass =:new_pass WHERE membre_id=:id");
				$sql2->bindParam(":new_pass", $hash);
				$sql2->bindParam(":id", $id);
				$GLOBALS['nb_req']++;
				if ($sql2->execute()) {
					unset($_SESSION['mdp_info'], $_SESSION['mdp_verif_info'], $_SESSION['form_mdp'], $_SESSION['form_mdp_verif']);

				?>
					<div class="valider">Votre mot de passe a été mise à jour.</div>
		<?php }
			} elseif ($_SESSION['erreursmotpass'] > 0) {
				if ($_SESSION['erreurs'] == 1) $_SESSION['nb_erreurs'] = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
				else $_SESSION['nb_erreurs'] = '<div class="erreur">Il y a eu ' . $_SESSION['erreursmotpass'] . ' erreurs dans votre formulaire, merci de les corriger !</div>';

				echo $_SESSION['nb_erreurs'];
			}
		}

		?>
		<script>
			$(document).ready(function() {
				$('#ville').smartSuggest({
					src: '<?= ROOTPATH ?>/includes/search_ville.php',
					fillBox: true,
					fillBoxWith: 'fill_text',
					executeCode: false
				});
			});

			$(document).ready(function() {
	var erreurs = '<?php echo $_SESSION['erreurs']; ?>';
	var erreursmagasin = '<?php echo $_SESSION['erreursmagasin']; ?>';
	var erreursmail = '<?php echo $_SESSION['erreursmail']; ?>'; 
	var erreursmotpass = '<?php echo $_SESSION['erreursmotpass']; ?>';
$('#edit_profil, #edit_mail, #edit_passe, #edit_magasin').hide();
if(erreurs > 0) {
	$("#view_profil").hide();
	$("#edit_profil").fadeIn();
}
if(erreursmagasin > 0) {
	$("#view_magasin").hide();
	$("#edit_magasin").fadeIn();
}
if(erreursmail > 0) {
	$("#view_mail").hide();
	$("#edit_mail").fadeIn();
}
if(erreursmotpass > 0) {
	$("#view_passe").hide();
	$("#edit_passe").fadeIn();
}
$("#linkeditprofil").on('click', function() {
	$("#view_profil").hide();
	$("#edit_profil").fadeIn();
});
$("#linkannulerprofil").on('click', function() {
	$("#edit_profil").hide();
	$("#view_profil").fadeIn();
});
$("#linkeditmagasin").on('click', function() {
	$("#view_magasin").hide();
	$("#edit_magasin").fadeIn();
});
$("#linkannulermagasin").on('click', function() {
	$("#edit_magasin").hide();
	$("#view_magasin").fadeIn();
});
$("#linkeditmail").on('click', function() {
	$("#view_mail").hide();
	$("#edit_mail").fadeIn();
});
$("#linkannulermail").on('click', function() {
	$("#edit_mail").hide();
	$("#view_mail").fadeIn();
});
$("#linkeditpasse").on('click', function() {
	$("#view_passe").hide();
	$("#edit_passe").fadeIn();
});
$("#linkannulerpasse").on('click', function() {
	$("#edit_passe").hide();
	$("#view_passe").fadeIn();
});
});




	</script>
<?= isset($statut) && $statut != '' ? $statut : '' ?>
		<div class="block_inside">

			<div id="view_profil">
				<fieldset>
					<legend>Vos informations</legend>
					<div id="d_nom_utilis" style="margin-left:70px;font-weight:bold;">
						<h4><?php if (isset($row['membre_utilisateur'])) echo $row['membre_utilisateur'];
							elseif (isset($row['membre_nom_societe'])) echo $row['membre_nom_societe']; ?></h4>
						<br />
					</div>
					<div id="form_ajouter">

					</div>
					<div id="d_civilite">
						<label for="civilite">Civilité :</label>
						<?php
						if (isset($civilite)) {
							if ($civilite == 1) echo 'Mr';
							elseif ($civilite == 2) echo 'Mme';
							elseif ($civilite == 3) echo 'Mlle';
							else echo 'Non renseigné';
						} else {
							if (isset($row['membre_civilite']) && $row['membre_civilite'] == 1) echo 'Mr';
							elseif (isset($row['membre_civilite']) && $row['membre_civilite'] == 2) echo 'Mme';
							elseif (isset($row['membre_civilite']) && $row['membre_civilite'] == 3) echo 'Mlle';
							else echo 'Non renseigné';
						} ?>
					</div>

					<div id="d_nom">
						<label for="nom_utilis">Nom :</label>
						<?php if (isset($nom) && $nom != "") {
							echo $nom;
						} else {
							if (isset($row['membre_nom']) && $row['membre_nom'] != "") echo $row['membre_nom'];
							else echo 'Non renseigné';
						} ?>
					</div>
					<div id="d_prenom">
						<label for="prenom_utilis">Prénom :</label>
						<?php if (isset($prenom) && $prenom != "") {
							echo $prenom;
						} else {
							if (isset($row['membre_prenom']) && $row['membre_prenom'] != "") echo $row['membre_prenom'];
							else echo 'Non renseigné';
						} ?>
					</div>
					<div id="d_pays">
						<label for="pays">Pays : </label>
						<?php
						if (isset($row['membre_pays']) && $row['membre_pays'] == 0) echo 'Non renseigné';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 1) echo 'France';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 2) echo 'Andorre';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 3) echo 'Belgique';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 4) echo 'Luxembourg';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 5) echo 'Monaco';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 6) echo 'Québec';
						elseif (isset($row['membre_pays']) && $row['membre_pays'] == 7) echo 'Suisse';
						else echo 'Non renseigné'; ?>
					</div>
					<div id="d_ville">
						<label for="ville">Ville :</label>
						<?php if (isset($ville) && $ville != "") {
							echo $ville;
						} else {
							if (isset($row['membre_ville'])) echo $row['membre_ville'];
						} ?> <?php if (isset($code_postal)) echo ' (' . $code_postal . ')';
								else echo ' (' . $row['membre_CP'] . ')'; ?>
					</div>
					<br /><a href="javascript:void(0);" style="text-decoration:none;color:#FFF;" id="linkeditprofil" class="pboutono" alt="Modifier vos informations personelles">Modifier</a>
				</fieldset>
			</div>

			<div id="edit_profil">
				<fieldset>
					<legend>Editer vos informations</legend>
					<form action="<?php echo ROOTPATH ?>/membres/parametres.php" method="post" id="modifier_all" autocomplete="off">
						<div id="d_nom_utilis" style="margin-left:70px;font-weight:bold;">
							<h4><?php if (isset($row['membre_utilisateur'])) echo $row['membre_utilisateur']; ?></h4>
							<br />
						</div>
						<div id="form_ajouter">
						</div>
						<div id="d_civilite">
							<label for="civilite">Civilité :</label>

							<select id="civilite" name="civilite" />
							<option value="">Choisissez</option>
							<option value="1" <?php if (isset($civilite)) {
													if ($civilite == 1) echo 'selected';
												} else {
													if (isset($row['membre_civilite']) && $row['membre_civilite'] == 1) echo 'selected';
												} ?>>Mr</option>
							<option value="2" <?php if (isset($civilite)) {
													if ($civilite == 2) echo 'selected';
												} else {
													if (isset($row['membre_civilite']) && $row['membre_civilite'] == 2) echo 'selected';
												} ?>>Mme</option>
							<option value="3" <?php if (isset($civilite)) {
													if ($civilite == 3) echo 'selected';
												} else {
													if (isset($row['membre_civilite']) && $row['membre_civilite'] == 3) echo 'selected';
												} ?>>Mlle</option>
							</select>
							<br /><?php if (isset($_SESSION['civilite_info'])) echo $_SESSION['civilite_info']; ?>
						</div>

						<div id="d_nom">
							<label for="nom">Nom :</label>
							<input type="text" name="nom" id="nom" placeholder="Inscrivez votre nom" value="<?php if (isset($nom) && $nom != "") {
																												echo $nom;
																											} else {
																												if (isset($row['membre_nom']) && $row['membre_nom'] != "") echo $row['membre_nom'];
																												else echo 'Non renseigné';
																											} ?>" />
							<br /><?php if (isset($_SESSION['nom_info'])) echo $_SESSION['nom_info']; ?>
						</div>
						<div id="d_prenom">
							<label for="prenom">Prénom :</label>
							<input type="text" name="prenom" id="prenom" placeholder="Inscrivez votre prenom" value="<?php if (isset($prenom) && $prenom != "") {
																															echo $prenom;
																														} else {
																															if (isset($row['membre_prenom']) && $row['membre_prenom'] != "") echo $row['membre_prenom'];
																															else echo 'Non renseigné';
																														} ?>" />
							<br /><?php if (isset($_SESSION['prenom_info'])) echo $_SESSION['prenom_info']; ?>
						</div>


						<div id="d_pays">
							<label for="pays">Pays : </label>
							<select name="pays" id="pays">
								<option value="0" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 0) echo 'selected';
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 0) echo 'selected'; ?>>Non renseigné</option>
								<option value="1" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 1) echo 'selected';
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 1) echo 'selected'; ?>>France</option>
								<option value="2" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 2) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 2) echo 'selected'; ?>>Andorre</option>
								<option value="3" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 3) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 3) echo 'selected'; ?>>Belgique</option>
								<option value="4" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 4) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 4) echo 'selected'; ?>>Luxembourg</option>
								<option value="5" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 5) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 5) echo 'selected'; ?>>Monaco</option>
								<option value="6" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 6) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 6) echo 'selected'; ?>>Québec</option>
								<option value="7" <?php if (isset($_SESSION['form_pays']) && $_SESSION['form_pays'] == 7) echo "selected";
													elseif (isset($row['membre_pays']) && $row['membre_pays'] == 7) echo 'selected'; ?>>Suisse</option>
							</select><br /><?php if (isset($_SESSION['pays_info'])) echo $_SESSION['pays_info']; ?>
						</div>
						<div id="d_ville">
							<label for="ville" style="display:inline-block;">Ville : </label>
							<input type="text" id="ville" name="ville" placeholder="75009, Paris" value="<?php if (isset($ville) && $ville != "") {
																												echo $ville;
																											} else {
																												if (isset($row['membre_ville'])) echo $row['membre_ville'];
																											} ?>" autocomplete="off" /><input type="text" id="infodep" name="infodep" style="margin:10px 0px;width:40px;display:none;" value="<?php if (isset($code_postal)) echo $code_postal;
																																																									else echo $row['membre_CP']; ?>" autocomplete="off" />
						</div>
						<br /><a href="javascript:void(0);" id="linkannulerprofil" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Annuler la modification de vos informations personelles">Annuler</a>
						<input class="pboutonb" style="margin-left:80px;" type="submit" name="modifier_all" value="Enregistrer" />
					</form>

				</fieldset>
			</div>
			<!-- MAGASIN -->
			<div id="view_magasin">
				<fieldset>
					<legend>Profil </legend>
					<div id="d_infosmagasin">
						<label for="horaires">Description du profil:</label>
						<?php if (isset($infosmagasin) && $infosmagasin != "") {
							echo $infosmagasin;
						} else {
							if (isset($row['membre_plusinfos']) && $row['membre_plusinfos'] != "") echo $row['membre_plusinfos'];
							else echo 'Non renseigné';
						} ?>
					</div>
					<br /><a href="javascript:void(0);" id="linkeditmagasin" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Modifier votre profil">Modifier</a>
				</fieldset>
			</div>

			<div id="edit_magasin">
				<fieldset>
					<legend>Editer votre profil</legend>
					<form action="<?php echo ROOTPATH ?>/membres/parametres.php" method="post" id="modifier_magasin">

						<div id="d_infosmagasin">
							<label for="infosmagasin" style="vertical-align:top;">Informations magasin :</label>
							<textarea name="infosmagasin" id="infosmagasin"><?php if (isset($infosmagasin) && $infosmagasin != "") {
																				echo $infosmagasin;
																			} else {
																				if (isset($row['membre_plusinfos']) && $row['membre_plusinfos'] != "") echo $row['membre_plusinfos'];
																				else echo 'Non renseigné';
																			} ?></textarea>
							<br /><?php if (isset($_SESSION['infosmagasin_info'])) echo $_SESSION['infosmagasin_info']; ?>
						</div>
						<br /><a href="javascript:void(0);" id="linkannulermagasin" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Annuler la modification de votre magasin">Annuler</a>
						<input class="pboutonb" style="margin-left:80px;" type="submit" name="modifier_magasin" value="Enregistrer" />
					</form>
				</fieldset>
			</div>
			<!-- EMAIL -->
			<div id="view_mail">
				<fieldset>
					<legend>Votre adresse mail</legend>

					<label for="email_addr" class="iconic2 email_addr">Adresse email :</label>
					<?php if (isset($email_addr2) && $email_addr2 != "") {
						echo $email_addr2;
					} else {
						if (isset($row['membre_email']) && $row['membre_email'] != "") echo $row['membre_email'];
						else echo 'Non renseigné';
					} ?>
					<br /><?php if (isset($_SESSION['siteweb_info'])) echo $_SESSION['siteweb_info']; ?>

					<br /><br /><a href="javascript:void(0);" id="linkeditmail" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Modifier votre adresse email">Modifier</a>
				</fieldset>
			</div>

			<div id="edit_mail">
				<fieldset>
					<legend>Editer votre adresse mail</legend>
					<form action="<?php echo ROOTPATH ?>/membres/parametres.php" method="post" id="modifier_email">
						<label for="email_addr" class="iconic2 email_addr">Adresse email actuelle :</label> <input type="email" class="disabledinput" name="email_addr" id="email_addr" value="<?php if (isset($email_addr2) && $email_addr2 != '') echo $email_addr2;
																																																else echo $row['membre_email']; ?>" disabled /><br />
						<label for="email_addr2" class="iconic2 email_addr">Nouvelle adresse email :</label> <input type="email" name="email_addr2" id="email_addr2" placeholder="Inscrivez votre nouvelle adresse email" /><br />
						<?php if (isset($_SESSION['mail_info'])) echo $_SESSION['mail_info']; ?>
						<br /><a href="javascript:void(0);" id="linkannulermail" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Annuler la modification de votre adresse email">Annuler</a>
						<input class="pboutonb" style="margin-left:80px;" type="submit" name="modifier_email" value="Enregistrer" />
					</form>
				</fieldset>
			</div>
			<!-- MOT DE PASSE -->
			<div id="view_passe">
				<fieldset>
					<legend>Votre mot de passe</legend>
					<label for="mot_pass_ancien" class="iconic2 mot_pass">Mot de passe actuelle :</label>
					*****
					<br /><br /><a href="javascript:void(0);" id="linkeditpasse" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Modifier votre mot de passe">Modifier</a>
				</fieldset>
			</div>
			<div id="edit_passe">
				<fieldset>
					<legend>Editer votre mot de passe</legend>
					<form action="<?php echo ROOTPATH ?>/membres/parametres.php" method="post" id="modifier_mot_pass">
						<label for="mot_pass_ancien" class="iconic2 mot_pass">Mot de passe actuelle :</label> <input type="password" name="mot_pass_ancien" id="mot_pass_ancien" placeholder="Ancien mot de passe" /><br />
						<?php if (isset($_SESSION['mdpancien_info'])) echo $_SESSION['mdpancien_info']; ?>
						<label for="mot_pass" class="iconic2 mot_pass">Nouveau mot de passe :</label> <input type="password" name="mot_pass" id="mot_pass" placeholder="Inscrivez votre nouveau mot de passe" /><br />
						<?php if (isset($_SESSION['mdp_info'])) echo $_SESSION['mdp_info']; ?>
						<label for="mot_pass2" class="iconic2 mot_pass">Réecrivez le nouveau passe :</label> <input type="password" name="mot_pass2" id="mot_pass2" placeholder="Réinscrivez votre nouveau mot de passe" /><br />
						<?php if (isset($_SESSION['mdp_verif_info'])) echo $_SESSION['mdp_verif_info']; ?>
						<br /><a href="javascript:void(0);" id="linkannulerpasse" style="text-decoration:none;color:#FFF;" class="pboutono" alt="Annuler la modification de votre mot de passe">Annuler</a>
						<input class="pboutonb" style="margin-left:80px;" type="submit" name="modifier_mot_pass" value="Enregistrer" />
					</form>
				</fieldset>
			</div>

			<!-- CONFIDENTIALITE -->
			<div id="view_conf">
				<fieldset>
					<legend>Confidentialité</legend>
					
					<form action="<?= ROOTPATH ?>/membres/parametres.php" method="post" name="conf_onlineedit" class="form_ajouter">
						<label for="online_conf" style="width:250px" class="iconic2">Afficher quand je suis en ligne : </label>
						<input type="checkbox" name="online_conf" id="online_conf" value="1" <?= ($row['conf_online'] == 1 ? 'checked' : '') ?>><label style="margin-left:50px;" for="online_conf"><span class="ui"></span></label>
						<br />
						<label for="datemask_conf" style="width:250px" class="iconic2">Masquer l'heure : </label>
						<input type="checkbox" name="datemask_conf" id="datemask_conf" value="1" <?= ($row['conf_datemask'] == 1 ? 'checked' : '') ?>><label style="margin-left:50px;" for="datemask_conf"><span class="ui"></span></label>
						<input class="pboutonb" type="submit" name="conf_onlineedit" id="conf_onlineeditsubmit" value="Envoyer" style="display:none;"/>
					</form>
					<br />
				</fieldset>
			</div>
			<br /><br /><br /><br />
			<i><i class="fas fa-exclamation-triangle bred"></i> <b>ATTENTION</b> <i class="fas fa-exclamation-triangle bred"></i><br /> Si vous supprimez votre compte, toutes vos annonces seront supprimés. <br />Vous pourrez réactiver votre compte en faisant une demande grace à notre formulaire de contact, cependant les annonces ne seront pas récupérables</i><br /><br />
			<a href="parametres?deletecpt=ok" id="supprimercompte" style="text-decoration:none;color:#FFF;width:270px;" onclick="if(!confirm('Etes-vous sur de vouloir supprimer votre compte ?\nToutes vos annonces seront supprimées\n\n! ')) return false;" class="pboutonr" alt="Supprimer mon compte">Supprimer mon compte</a>
			<div id="popup_cherchertitre" class="popup_block">
				<h4>Téléphone</h4><br />
				<p style="font-size:12px;">En inscrivant votre numéro de téléphone, celui-ci sera affiché dans les futurs annonces que vous posterez.</p>
			</div>
		</div>

		<link rel="stylesheet" type="text/css" href="<?php echo ROOTPATH; ?>/css/jquery.smartsuggest.css" />
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>/script/suggest-ville-cp.js"></script>

<?php  }
}
require_once '../elements/footer.php'; ?>

<script>
	var online = document.getElementById('online_conf');
	var datemask = document.getElementById('datemask_conf');
	var conf_onlineedit =  document.getElementById('conf_onlineeditsubmit');

[online, datemask].forEach(function(e) {

	e.addEventListener('change', function() {
	
	conf_onlineedit.click();
	
});

});


</script>