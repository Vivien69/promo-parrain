<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Utilisateurs';
require_once '../../elements/header2.php';
/**
 * *******Gestion avant affichage...**********
 */
    if(isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
	} else {
		$informations = Array(/*L'id de cookie est incorrect*/
        true,
        'Vous n\'&ecirc;tes pas connect&eacute;',
        'Impossible d\'accéder à cette page membre.',
        ' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
        ''. ROOTPATH . '/index.html',
        3
        );
    require_once('../../information.php');
    exit(); 
	}

$sql = "SELECT * FROM user WHERE membre_id=".$id.' AND membre_etat = 2';
$prep = $pdo->prepare($sql);
$prep->execute();

if($prep->rowCount() == 0) {
	$informations = Array(/*L'id de cookie est incorrect*/
            true,
            'Accès interdit',
            'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
            '',
            ''. ROOTPATH . '/index.html',
            3
            );
        require_once('../../information.php');
        exit();
	
} else {
while($row = $prep->fetch(PDO::FETCH_ASSOC)) {
    /**
     * *******FIN Gestion avant affichage...**********
     */
    $current = 'admin_utilisateurs' ;
	 require_once '../includes/menu_membres.php';

	 //DELETE USER
	 if (isset($_GET["delete"]) && $_GET["delete"] == 'ok') {
		$id = intval($_GET['user']);
		$nomutilis = htmlspecialchars($_GET['nomutilis']);
		$sql = "SELECT * FROM user WHERE membre_id = " . $id;
		$prepa = $pdo->prepare($sql);
		$prepa->execute();
		$GLOBALS['nb_req']++;
		if ($prepa->rowcount() == 1) {
			$sql1 = $pdo->prepare('DELETE FROM annonces_parrainage WHERE idmembre = ' . $id);
			$sql2 = $pdo->prepare('UPDATE user SET membre_etat = 4 WHERE membre_id = ' . $id);
			if ($sql1->execute() & $sql2->execute()) {
				$GLOBALS['nb_req']+2;
				echo '<div class="valider">L\'utilisateur <b>'.$nomutilis.' </b> a été supprimée.</div>';
			}
		} else {
			echo '<div class="erreur">Erreur, merci de réeessayer ultérieurement ou de nous contacter.</div>';
		}
	}
	//BAN USER a venir


	$check_contents = $pdo->query('SELECT COUNT(*) as total FROM user');
									$GLOBALS['nb_req']++;
	$get_total_pages = $check_contents->fetch(PDO::FETCH_ASSOC);
	$get_total_pages = $get_total_pages['total'];
	$page_limit = 10; //This is the number of contents to display on each page
	$pagination_stages = 5;
	if(isset($_GET['page'])) {
	$current_page = strip_tags($_GET['page']);
	} else {
		$current_page = 1;
	}
	$start_page = ($current_page - 1) * $page_limit;
	
	
	//This initializes the page setup
	if($current_page == 0) { $current_page = 1; }
	$previous_page = $current_page - 1;	
	$next_page = $current_page + 1;							
	$last_page = ceil($get_total_pages/$page_limit);		
	$lastpaged = $last_page - 1;					
	$pagination_system = '';
	if($last_page > 1)
	{	
		$pagination_system .= "<div class='pagination_system'>";
		// Previous Page
		if ($current_page > 1) { $page_counterprec = $current_page - 1; $pagination_system.= "<a class='paginat' href='$page_counterprec'>< Précedent</a>"; }
		else { $pagination_system.= ""; }
		// Pages	
		if ($last_page < 7 + ($pagination_stages * 2))	// Not enough pages to breaking it up
		{	
			for ($page_counter = 1; $page_counter <= $last_page; $page_counter++)
			{
				if ($page_counter == $current_page) {
					$pagination_system.= "<span class='current'>$page_counter</span>";
				}
				else {
					$pagination_system.= "<a class='paginat' href='$page_counter'>$page_counter</a>";
				}					
			}
		}
		elseif($last_page > 5 + ($pagination_stages * 2))	// This hides few pages when the displayed pages are much
		{
			//Beginning only hide later pages
			if($current_page < 1 + ($pagination_stages * 2))		
			{
				for ($page_counter = 1; $page_counter < 4 + ($pagination_stages * 2); $page_counter++)
				{
					if ($page_counter == $current_page) {
						$pagination_system.= "<span class='current'>$page_counter</span>";
					}
					else {
						$pagination_system.= "<a class='paginat' href='$page_counter'>$page_counter</a>";
					}					
				}
				$pagination_system.= "...";
				$pagination_system.= "<a class='paginat' href='$lastpaged'>$lastpaged</a>";
				$pagination_system.= "<a class='paginat' href='$last_page'>$last_page</a>";		
			}
			//Middle hide some front and some back
			elseif($last_page - ($pagination_stages * 2) > $current_page && $current_page > ($pagination_stages * 2))
			{
				$pagination_system.= "<a class='paginat' href='1'>1</a>";
				$pagination_system.= "<a class='paginat' href='2'>2</a>";
				$pagination_system.= "...";
				for ($page_counter = $current_page - $pagination_stages; $page_counter <= $current_page + $pagination_stages; $page_counter++)
				{
					if ($page_counter == $current_page) {
						$pagination_system.= "<span class='current'>$page_counter</span>";
					}
					else {
						$pagination_system.= "<a class='paginat' href='$page_counter'>$page_counter</a>";
					}					
				}
				$pagination_system.= "...";
				$pagination_system.= "<a class='paginat' href='$lastpaged'>$lastpaged</a>";
				$pagination_system.= "<a class='paginat' href='$last_page'>$last_page</a>";		
			}
			//End only hide early pages
			else
			{
				$pagination_system.= "<a class='paginat' href=1'>1</a>";
				$pagination_system.= "<a class='paginat' href='2'>2</a>";
				$pagination_system.= "...";
				for ($page_counter = $last_page - (2 + ($pagination_stages * 2)); $page_counter <= $last_page; $page_counter++)
				{
					if ($page_counter == $current_page) {
						$pagination_system.= "<span class='current'>$page_counter</span>";
					}
					else {
						$pagination_system.= "<a class='paginat' href='$page_counter'>$page_counter</a>";
					}					
				}
			}
		}		
		//Next Page
		if ($current_page < $page_counter - 1) { $page_countersuiv = $current_page +1; $pagination_system.= "<a class='paginat' href='$page_countersuiv'>Suivant ></a>"; }
		else { $pagination_system.= ""; }
		$pagination_system.= "</div><div class='current' style='float:right;margin:-92px 5px 0 0px;'>Page : <b>$current_page</b> sur $last_page</div>";			
	}
			$sql= 'SELECT * FROM user US
			LEFT JOIN images IM ON US.membre_id = IM.id_membre AND type="avatar"
				  ORDER BY membre_date DESC
				  LIMIT '.$start_page.', '.$page_limit.'';
				  $GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
    ?>
  <section class="block_inside">

  	<h2>Liste des Utilisateurs</h2>
	  <p style="font-size:14px;float:right;"><b><?= $get_total_pages ?></b> membres</p>
      <p style="font-size:14px;float:left;">Page : <b><?= $current_page ?></b></p>

  	<?php

	  $sql = $pdo->query("SELECT COUNT(*) as nb FROM user WHERE membre_etat = 0");
	  $sql->execute();
	  $fet = $sql->fetch();
	  echo 'Comptes non activés : '.$fet['nb'];
  	if ($prep->rowcount() >= 1) {
		  // ECHO EN-TETE TABLEAU
	  echo '<div style="height:30px;width:100%;background:#EEE;border:1px solid #CCC;display:flex;padding:5px 0px;">
	  			<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Avatar</div>
				<div style="width:15%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Date</div>
				<div style="width:20%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Email</div>
				<div style="width:20%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">IP</div>
				<div style="width:25%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">Nom - Prénom</div>
				<div style="width:10%;line-height:30px;text-align:center;text-transform:uppercase;font-weight:bold;">Gérer</div>
        	</div><br />';
	while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
		// ECHO AFFICHAGE D'UN MEMBRE
	echo '	<div class="list-item" style="border-bottom:1px solid #CCC;display:block;text-align:left;position:relative;">
				
				<p class="titre_message_utilisateur"><a href="'.ROOTPATH.'/profil/' . $result['membre_id'] . '"">'.$result['membre_utilisateur'].'</a> - '.nombrecodes($result['membre_id']).' </p>
				
				<button type="button" class="btn btn-info gerer" style="position:relative;">Gérer <i style="margin-left:10px;" class="fa fa-angle-down fa-lg"></i></button>
				
				<div class="vpb_down_triangle">
					<div class="vpb_down_triangle_inner" align="left">
						<div><a href="'.ROOTPATH.'/membres/admin/membres.php?user='.$result['membre_id'].'&nomutilis='.$result['membre_utilisateur'].'&ban=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir bannir ce membre ?\')) return false;" rel="popupconnect" class="poplight"><i style="margin-right:10px;" class="fa fa-ban fa-lg"></i> Bannir</a></div>
					</div>
					<div class="vpb_down_triangle_inner" align="left">
						<div><a href="'.ROOTPATH.'/membres/admin/membres.php?user='.$result['membre_id'].'&nomutilis='.$result['membre_utilisateur'].'&delete=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer ce membre ?\')) return false;" rel="popupconnect" class="poplight"><i style="margin-right:10px;" class="fa fa-times fa-lg"></i> Supprimer</a></div>
					</div>
					
				</div>

				<div style="display:flex;justify-content:space-between;">
					<div style="width:10%;text-align:center;border-right:1px solid #CCC;"><a href="' . ROOTPATH . '/profil/'.$result['membre_id'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($result['image']) ? $result['image'] : '/default_avatar.png').'" class="avatar" /></a></div>
					<div style="line-height:45px;width:15%;text-align:center;border-right:1px solid #CCC;">'.date("d.m.Y", $result['membre_date']).'</div>
					<div style="line-height:45px;width:20%;text-align:center;border-right:1px solid #CCC;">'.$result['membre_email'].'</div>
					<div style="line-height:45px;width:20%;text-align:center;border-right:1px solid #CCC;font-weight:bold;">'.$result['membre_IP'].'</div>
					<div style="line-height:45px;width:25%;text-align:center;"><b>'.(!empty($result['membre_nom']) ? $result['membre_nom'] : 'Inconnu').'</b> - '.(!empty($result['membre_prenom']) ? $result['membre_prenom'] : 'Inconnu').'</div>
					<div style="width:9%;border-left:1px solid #CCC;text-align:center;"><br />'.($result['membre_etat'] == 0 ? '<span style="font-weight:bold;">Non-activé' : (($result['membre_etat'] == 1) ? '<span style="font-weight:bold;color:#51D836">Activé' : (($result['membre_etat'] == 4) ? '<span style="font-weight:bold;color:#CC0000">Compte Supprimé' :(($result['membre_etat'] == 2) ? '<span style="font-weight:bold;color:#ff9000">Admin' : '<span style="font-weight:bold;color:#CC0000">Admin')))).'</span></div>
				</div>
			</div>';
  	}
  	echo $pagination_system;
  	} else echo 'Aucuns utilisateurs'; ?>	
  		
</section>
<script>
	$(document).ready( function(){
	var nav = $('.vpb_down_triangle');
	nav.hide();
    $('.gerer').click( function(event){
        
        event.stopPropagation();
        $('.vpb_down_triangle').hide();
        $(this).next().toggle();
        
    });
    
    $(document).click( function(){

        $('.vpb_down_triangle').hide();

    });

});
</script>

<?php } } require_once '../../elements/footer.php'; ?>