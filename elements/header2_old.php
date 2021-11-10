<?php
$timestart = microtime(true);
//echo print_r($_SESSION);
?>
<title><?= $title ?? TITRESITE ?></title>
</head>

<div id="center">
	<header class="cf navigation">
			<a href="<?= ROOTPATH ?>">
				<div id="logo"></div>
			</a>
			<div class="searchbox"><label for="searchinput" class="fas fa-search input-iconsearch"></label><input id="searchinput" name="searchinput" type="text" placeholder="Marchand"></div>
			<nav>
				<ul class="mobimenu">
					
					<li><a href="<?= ROOTPATH ?>/categories" title="CATEGORIES DE PARRAINAGE">CATEGORIES</a></li>
					<li><a href="<?= ROOTPATH ?>/parrainages.html" title="DERNIERES OFFRES DE PARRAINAGES">DERNIERS PARRAINAGES</a></li>
					<?php
					if (isset($_SESSION['membre_id'])) {?>
						<li><a href="<?=ROOTPATH ?>/membres/index.html" title="ESPACE MEMBRES"> <i class="fa fa-user"></i> COMPTE</a></li>
						<li style="position:relative;"><a href="javascript:void(0);" id="notif-view" title="Notifications"> <i class="fa fa-bell"></i></a>
						<div class="vpb_down_triangle notificationMenuHeader" style="right:-18px;">
							
						</div></li>
						
					<?php } else
						echo '<li><a href="' . ROOTPATH . '/connexion.html" title="CONNEXION"> <i class="fa fa-user"></i> CONNEXION</a></li>
							<li><a href="' . ROOTPATH . '/inscription.html" title="INSCRIPTION"> <i class="fa fa-user"></i> INSCRIPTION</a></li>'; ?>
					<li><a href="<?= ROOTPATH ?>/parrain/ajouter" title="AJOUTER UNE OFFRE DE PARRAINAGE"><i class="fa fa-plus"></i> AJOUTER</a></li>
					
				</ul>
			</nav>
	</header>
	

	<script src="<?= ROOTPATH ?>/script/jquery-3.5.0.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= ROOTPATH; ?>/css/jquery.smartsuggest.css" />
<script type="text/javascript" src="<?= ROOTPATH; ?>/script/jquery.smartsuggest-simple.js"></script>
	<script>
		$(document).ready(function() {

			$('.menu_deroulantmembre').click(function(event) {
				event.stopPropagation();

				$(this).next('.vpb_down_triangle').toggle();
				var cible = $(this).next('.vpb_down_triangle');
			});
		});

	</script>