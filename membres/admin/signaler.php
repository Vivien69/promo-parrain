<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Offres signalées';
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
	$current = 'admin_signaler' ;
	 require_once '../includes/menu_membres.php';
    /**
     * *******FIN Gestion avant affichage...**********
     */
    if (isset($_GET["delete"]) && $_GET["delete"] == 'ok') {
	$id = $_GET['id'];
		$sql = $pdo->prepare("DELETE FROM signaler WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="erreur">Le signalement <b>'.$id.'</b> a été supprimée.</div>';
		}
	
}
if (isset($_GET["traite"]) && $_GET["traite"] == 'ok') {
	$id = $_GET['id'];
	$sql = "SELECT * FROM signaler WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE signaler SET etat = 1 WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">Le signalement <b>'.$id.'</b> a été traité.</div>';
			unset($_GET); 
		}
	}
}


	$check_contents = $pdo->query('SELECT COUNT(*) as total FROM signaler');
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
				$pagination_system.= "<a class='paginat' href='1'>1</a>";
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
			$sql= 'SELECT US.*, AP.idmarchand, AP.idmembre, M.nom_marchand, M.cat, U.membre_utilisateur, U2.membre_utilisateur as user2, I.id_membre, I.image, I.type, I2.image as img2, I2.type as type2 FROM signaler US
			LEFT JOIN annonces_parrainage AP ON US.id_annonce = AP.id
			LEFT JOIN marchands M ON AP.idmarchand = M.id
			LEFT JOIN user U ON U.membre_id = US.id_membre
			LEFT JOIN user U2 ON U2.membre_id = AP.idmembre
			LEFT JOIN images I ON U.membre_id = I.id_membre AND I.type = "avatar"
			LEFT JOIN images I2 ON U2.membre_id = I2.id_membre AND I2.type = "avatar"
			ORDER BY id DESC
			LIMIT '.$start_page.', '.$page_limit.'';
				  $GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
    ?>

  <div class="block_inside">

  	<h2>Annonces signalées</h2>
  	<p style="float:right;margin:-40px 550px 0 0px;font-size:24px;font-family:BebasNeueRegular;"><b><?= $get_total_pages; ?></b> annonces signalées</p>
	  <div style="height:30px;width:100%;background:#EEE;border:1px solid #CCC;display:flex;padding:5px 0">
	  			<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Signaleur</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Concerné</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Date</div>
				<div style="width:20%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Motif</div>
				<div style="width:40%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Message</div>
				<div style="width:10%;line-height:30px;text-align:center;text-transform:uppercase;font-weight:bold;">Gérer</div>
        	</div><br />
  	<?php
  	if ($prep->rowcount() >= 1) {
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
	echo '<div class="list-item" style="'.($row['etat'] ? 'border:1px solid #5CA838' : 'border-bottom:1px solid #CCC').';display:block;text-align:left;position:relative;">
	<button type="button" class="btn btn-info gerer" style="position:relative;">Gérer <i style="margin-left:10px;" class="fa fa-angle-down fa-lg"></i></button>
		<div class="vpb_down_triangle" style="margin:29px -10px 0 0px;">
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id_annonce'] . '"><i style="margin-right:10px;" class="fas fa-search"></i> Voir</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a '.($row['etat'] ? 'style="color:#5CA838"' : '').' href="'.ROOTPATH.'/membres/admin/signaler.php?id='.$row['id'].'&traite=ok"><i style="margin-right:10px;" class="fas fa-check-circle"></i> Traiter</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="'.ROOTPATH.'/membres/admin/signaler.php?id='.$row['id'].'&delete=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer ce signalement ?\')) return false;"><i style="margin-right:10px;" class="fa fa-times fa-lg"></i> Supprimer</a></div>
			</div>
	</div>
	
	<div style="display:flex;">
		<div style="width:10%;text-align:center;border-right:1px solid #CCC;"><a href="' . ROOTPATH . '/profil/'.$row['id_membre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['image']) ? $row['image'] : '/default_avatar.png').'" class="avatar" /><p class="titre_message_utilisateur" style="margin:-15px 0 0 -10px;font-size:16px"><br />' . $row['membre_utilisateur'] . ' ('.nombrecodes($row['id_membre']).')</p></a></div>
		<div style="width:11%;text-align:center;border-right:1px solid #CCC;"><a href="' . ROOTPATH . '/profil/'.$row['idmembre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['img2']) ? $row['img2'] : '/default_avatar.png').'" class="avatar" /><p class="titre_message_utilisateur" style="margin:-15px 0 0 -10px;font-size:16px"><br />' . $row['user2'] . ' ('.nombrecodes($row['idmembre']).')</p></a></div>
		<div style="line-height:70px;width:10%;text-align:center;border-right:1px solid #CCC;">'.date("d.m.Y", $row['date']).'</div>
		<div style="line-height:70px;width:20%;text-align:center;border-right:1px solid #CCC;word-break: break-all;padding:0 10px;max-height:150px;">'.($row['motif'] == 1 ? 'Mauvaise catégorie' : '').($row['motif'] == 2 ? 'Offre incorrecte' : '').($row['motif'] == 3 ? 'Photo inappropriée' : '').($row['motif'] == 4 ? 'Texte ou contenu choquant' : '').($row['motif'] == 5 ? 'Autre abus' : '').'</div>
		<div style="line-height:70px;width:40%;text-align:center;border-right:1px solid #CCC;word-break: break-all;padding:0 10px;max-height:150px;">'.$row['message'].'</div>
		</div>
</div>';
  	}
  	echo $pagination_system;
  	} else echo 'Aucun signalement'; ?>	
  		
				
    
</div>
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