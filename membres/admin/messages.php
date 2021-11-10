<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Messages';
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
	$current = 'admin_messages' ;
	 require_once '../includes/menu_membres.php';
    /**
     * *******FIN Gestion avant affichage...**********
     */
	
	if (isset($_GET["delete"]) && $_GET["delete"] == 'ok') {
	$id = $_GET['message'];
	$sql = "SELECT * FROM messages WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("DELETE FROM messages WHERE id = ".$id);
		echo 'deleted';
		
	}
}
if (isset($_GET["traite"]) && $_GET["traite"] == 'ok') {
	$id = $_GET['message'];
	$sql = "SELECT * FROM messages WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE messages SET etat = 1 WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">Le message <b>'.$id.'</b> a été traité.</div>';
			unset($_GET); 
		}
	}
}
if (isset($_GET["traite"]) && $_GET["traite"] == true) {
	$id = $_GET['message'];
	$id_membre = $_GET['idmembre'];
	$sql = "SELECT * FROM messages WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE messages SET etat = 1 WHERE id = ".$id);
		if ($sql->execute()) {
			checkHowManyEntry($_SESSION['membre_id'], 6, 'messages', 'id_membre');
			$GLOBALS['nb_req']++;
			echo '<div class="valider">Le message <b>'.$id.'</b> a été traité  + check de Badge</div>';
			unset($_GET); 
		}
	}
}

	$check_contents = $pdo->query('SELECT COUNT(*) as total FROM messages');
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
			$sql= 'SELECT ME.*, US.membre_utilisateur, US.membre_id, US2.membre_utilisateur as namevupar, I.type, I.image FROM messages ME
					LEFT JOIN user US ON ME.id_membre = US.membre_id
					LEFT JOIN user US2 ON ME.vu_par = US2.membre_id
					LEFT JOIN images I ON I.id_membre = ME.id_membre AND I.type = "avatar"
				  ORDER BY date DESC
				  LIMIT '.$start_page.', '.$page_limit.'';
				  $GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
    ?>

  <div class="block_inside">

	
  	<h2>Liste des messages</h2>
	  <p style="font-size:14px;float:right;"><b><?= $get_total_pages ?></b> messages</p>
      <p style="font-size:14px;float:left;">Page : <b><?= $current_page ?></b></p>

  	<?php
  	if ($prep->rowcount() >= 1) {
		// ECHO EN-TETE TABLEAU
		echo '<div style="height:30px;width:100%;background:#EEE;border:1px solid #CCC;display:flex;padding:5px 0">
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Avatar</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Date</div>
				<div style="width:20%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">Sujet</div>
				<div style="width:45%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">Message</div>
				<div style="width:15%;line-height:30px;text-align:center;text-transform:uppercase;font-weight:bold;">Vu par</div>
			</div><br />';
	while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
			// ECHO AFFICHAGE d'UN MESSAGE
	echo '<div class="list-item" style="'.($result['etat'] ? 'border:1px solid #5CA838' : 'border-bottom:1px solid #CCC').';display:block;text-align:left;position:relative;">
			
				
				
				<button type="button" class="btn btn-info gerer">Gérer <i style="margin-left:10px" class="fa fa-angle-down fa-lg"></i></button>
					<div class="vpb_down_triangle">
						<div class="vpb_down_triangle_inner" align="left">
							<div><a href="'.ROOTPATH.'/membres/admin/messages.php?message='.$result['id'].'&idmembre='.$result['id_membre'].'&traite=true" onclick="if(!confirm(\'Etes-vous sur de vouloir traiter ce message ?\')) return false;"><i style="margin-right:10px;" class="fa fa-check fa-lg"></i>Message traité</a></div>
						</div>
						<div class="vpb_down_triangle_inner" align="left">
							<div><a href="'.ROOTPATH.'/membres/admin/messages.php?message='.$result['id'].'&idmembre='.$result['id_membre'].'&traite=false" onclick="if(!confirm(\'Etes-vous sur de vouloir refuser ce message ?\')) return false;"><i style="margin-right:10px;" class="fa fa-check fa-lg"></i>Message refusé</a></div>
						</div>
						<div class="vpb_down_triangle_inner" align="left">
							<div><a href="'.ROOTPATH.'/membres/admin/messagesvoir.php?message='.$result['id'].'"><i style="margin-right:10px;" class="fas fa-reply fa-lg"></i>Répondre</a></div>
						</div>
						<div class="vpb_down_triangle_inner" align="left">
							<div><a href="'.ROOTPATH.'/membres/admin/messages.php?message='.$result['id'].'&delete=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer ce message ?\')) return false;"><i style="margin-right:10px;" class="fa fa-times fa-lg"></i> Supprimer</a></div>
						</div>
					</div>
				<div style="display:flex;justify-content:space-between">
					<div style="width:10%;min-width:100px;text-align:center;border-right:1px solid #CCC;"><a href="' . ROOTPATH . '/profil/'.$result['id_membre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($result['image']) ? $result['image'] : '/default_avatar.png').'" class="avatar" /><p class="titre_message_utilisateur" style="margin:-15px 0 0 -10px;font-size:16px"><br />' . $result['membre_utilisateur'] . '</p></a></div>
					<div style="line-height:45px;width:10%;text-align:center;border-right:1px solid #CCC;">'.date("d.m.Y", $result['date']).'</div>
					<div style="line-height:45px;width:20%;text-align:center;border-right:1px solid #CCC;">'.($result['sujet'] == 1 ? '<span style="font-weight:bold;">Renseignement' : (($result['sujet'] == 2) ? '<span style="font-weight:bold;color:#51D836">Problème' : (($result['sujet'] == 3) ? '<span style="font-weight:bold;color:#CC0000">Abus' : (($result['sujet'] == 4) ? '<span style="font-weight:bold;">Autre' : (($result['sujet'] == 5) ? '<span style="font-weight:bold;">Offre '.$result['offre'] : '<span style="font-weight:bold;">Aucun sujet'))))).'</span></div>
					<div style="padding:10px;width:47%;text-align:center;border-right:1px solid #CCC;font-weight:bold;">'.$result['message'].'</div>
					<div style="line-height:45px;width:14%;text-align:center;">'.(isset($result['namevupar']) ? $result['namevupar'] : 'Personne').'</div>
				</div>
			</div>';	
  	} 
  	echo $pagination_system;
  	} else echo 'Aucun message'; ?>
  				
  		
  		
  	
  	
  	
    
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
