<?php
$timestart = microtime(true);
//echo print_r($_SESSION);
?>
<title><?= $title ?? TITRESITE ?></title>
</head>
<div id="center">
	<header class="cf navigation">	
		<nav>
			<a href="<?= ROOTPATH ?>">
				<div id="logo"></div>
			</a>
				<ul class="mobimenu">
					<li><a href="<?= ROOTPATH ?>/categories" title="CATEGORIES">CATEGORIES</a></li>
					<li><a href="<?= ROOTPATH ?>/parrainages" title="OFFRES DE PARRAINAGES">DERNIERS PARRAINAGES</a></li>
					<?php
					if (isset($_SESSION['membre_id'])) {
						$messagenonlus = nombremessagenonlus(1);
						$notif = nombrenotif();
						
						?>
						<!-- MENU MESSAGERIE-->
						<li><button class="dropdownmenu" id="messagerieload" style="position:relative;"><i class="fas fa-envelope"></i><?= (isset($messagenonlus) && !empty($messagenonlus) ? '<span class="notificationmessagerie">'.$messagenonlus.'</span>' : '') ?></button>
						<div class="dialog-open">
							<div class="dialog-open_inner" style="padding:0;">
							<ul id="messagerie-content"></ul>
							<div id="messagerie-content-message">Messagerie</div>
							</div>
						</div></li>
						<!-- MENU NOTIFICATIONS -->
						<li><button class="dropdownmenu" id="notifload" style="position:relative;"><i class="fas fa-bell"></i><?= (isset($notif) && !empty($notif) ? '<span class="notificationmessagerie">'.$notif.'</span>' : '') ?></button>
						<div class="dialog-open">
							<div class="dialog-open_inner" style="padding:0;">
							<ul id="notifload-content"></ul>
							<div id="notifload-content-message"></div>
							</div>
						</div></li>
						<!-- MENU MEMBRE -->
						<li><button class="dropdownmenu nohovernav"><img src="<?= ROOTPATH ?>/membres/images/<?= ((isset($_SESSION['image']) && $_SESSION['image'] != '') ? $_SESSION['image'] : 'default_avatar.png'); ?>" class="dropdown_avatar" alt="Bienvenue <?= $_SESSION['membre_utilisateur'] ?>"></button>
							<div class="dialog-open" style="display:none;right:unset;max-width:250px;">
								<ul class="dialog-open_inner">
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/membres/parrainages"><i class="fas fa-bell fa-lg" style="margin-right:5px"></i> Parrainages</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/membres/annonces"><i class="fas fa-store fa-lg" style="margin-right:5px"></i> Annonces</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/membres/messagerie"><i class="fas fa-envelope fa-lg" style="margin-right:5px"></i> Messagerie</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/membres/badges"><i class="fas fa-trophy fa-lg" style="margin-right:5px"></i> Badges</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/profil/<?= $_SESSION['membre_id'] ?>"><i class="far fa-address-card fa-lg" style="margin-right:5px"></i> Profil</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/membres/parametres"><i class="fa fa-cog fa-lg" style="margin-right:5px"></i> Paramètres</a></li>
									<li style="width:100%;"><a style="padding:10px;" href="<?= ROOTPATH ?>/deconnexion"><i class="fas fa-sign-out-alt fa-lg" style="margin-right:5px"></i> Déconnexion</a></li>
								</ul>
							</div>
						</li>

						<!-- MENU SIMILAIRE MOBILE A FAIRE -->
						<li><button class="mobileDropDown" style="display:none;"><i class="fas fa-user"></i></button></li>
						
						<?php
						//$messagenonlus = nombremessagenonlus(1);
						//echo '<li><a href="' . ROOTPATH . '/membres/index.html" style="position:relative;" title="ESPACE PRIVE"> <i class="fa fa-user"></i> COMPTE '.(isset($messagenonlus) && !empty($messagenonlus) ? '<span class="notificationmessagerie">'.$messagenonlus.'</span>' : '').'</a></li>';
					
					}else {
						echo '<li><a href="' . ROOTPATH . '/connexion" title="CONNEXION"> <i class="fa fa-user"></i> CONNEXION</a></li>
							<li><a href="' . ROOTPATH . '/inscription" title="INSCRIPTION"> <i class="fa fa-user-plus"></i> INSCRIPTION</a></li>'; 
					}
					?>
					<li><a href="<?= ROOTPATH ?>/parrain/ajouter" title="AJOUTER UNE OFFRE DE PARRAINAGE"><i class="fa fa-plus"></i> AJOUTER</a></li>
					<li><a href="" class="searchicone" title="Rechercher un magasin"><i class="fas fa-search"></i></a></li>
				</ul>
				
			</nav>
	</header>
	<div class="searchbox"><label for="searchinput" class="fas fa-search input-iconsearch"></label><input id="searchinput" name="searchinput" type="text" placeholder="Marchand"></div>
	<script src="<?= ROOTPATH ?>/script/jquery-3.5.0.min.js"></script>
