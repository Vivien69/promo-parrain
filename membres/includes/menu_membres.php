<div class="sousmenucateg">
	<div style="width:auto;margin:0 auto;">
		<?php
		$sqla = "SELECT * FROM images WHERE id_membre = " . $row['membre_id'] . " AND type='avatar'";
		$prepa = $pdo->prepare($sqla);
		$prepa->execute();
		$GLOBALS['nb_req']++;
		if ($prepa->rowcount() == 0) {
			$avatar = ROOTPATH . '/membres/images/default_avatar.png';
		}
		if ($prepa->rowcount() == 1) {
			$resulta = $prepa->fetch(PDO::FETCH_ASSOC);
			$avatar = ROOTPATH . '/membres/images/' . $resulta['image'];
		}
		echo '<a style="padding-top:1px;" href="'.ROOTPATH.'/membres" title="Tableau de bord" '.($current == 'index' ? ' class="currenta" ' : '').'><img src="' . $avatar . '" class="avatar" style="margin:0px 5px -9px 0;width:35px;height:35px" alt="Bienvenue ' . $row['membre_utilisateur'] . '"></a>';
		$prepa->closecursor();
		?><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/membres/parrainages" <?= $current == 'parrainages' ? 'class="currenta"' : ''; ?>><i class="fas fa-bell"></i> Parrainages</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/membres/messagerie" class="<?= (isset($notif) && !empty($notif) ? ' menu_membrenotif' : '') ?> <?= $current == 'messagerie' ? ' currenta' : ''; ?>"><i class="fas fa-envelope"></i> Messagerie</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/membres/annonces" <?= $current == 'mesannonces' ? 'class="currenta"' : ''; ?>><i class="fas fa-store"></i> Annonces</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/membres/badges" <?= $current == 'badges' ? 'class="currenta"' : ''; ?>><i class="fas fa-trophy"></i> Badges</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/profil/<?= $_SESSION['membre_id'] ?>" <?= $current == 'monprofil' ? 'class="currenta"' : ''; ?>><i class="far fa-address-card"></i> Profil</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/membres/parametres" <?= $current == 'parametres' ? 'class="currenta"' : ''; ?>><i class="fa fa-cog"></i> Paramètres</a></li><!-- @whitespace
		--><li><a href="<?= ROOTPATH; ?>/deconnexion"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li><!-- @whitespace
		<?php if ($row['membre_etat'] == 2) {
		echo '--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/liste-marchands" ' . ($current == 'admin_marchands' ? 'class="currenta"' : '') . ' ><i class="fas fa-store"></i> Marchands</a></li><!-- @whitespace
	 	--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/liste-messages/1" ' . ($current == 'admin_messages' ? 'class="currenta"' : '') . '><i class="fa fa-reply"></i> Messages</a></li><!-- @whitespace
	 	--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/liste-utilisateurs/1" ' . ($current == 'admin_utilisateurs' ? 'class="currenta"' : '') . '><i class="fas fa-users"></i> Utilisateurs</a></li><!-- @whitespace
		--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/parrainages/1" ' . ($current == 'admin_ap' ? 'class="currenta"' : '') . '><i class="fa fa-coins"></i> Parrainages</a></li><!-- @whitespace
		--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/signaler/1" ' . ($current == 'admin_signaler' ? 'class="currenta"' : '') . '><i class="fas fa-exclamation"></i> Signalés</a></li><!-- @whitespace
		--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/liste-avis/1" ' . ($current == 'admin_avis' ? 'class="currenta"' : '') . '><i class="fas fa-comment"></i> Avis</a></li><!-- @whitespace
		--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/avis-marchands/1" ' . ($current == 'admin_avis_marchands' ? 'class="currenta"' : '') . '><i class="fas fa-comments"></i> Avis marchands</a></li><!-- @whitespace
		--><li><a style="color:#CC0000;" href="' . ROOTPATH . '/membres/admin/histo/1" ' . ($current == 'admin_histo' ? 'class="currenta"' : '') . '><i class="fas fa-monument"></i>Histo</a></li>';
	} ?><!-- @whitespace-->
	</div></div>