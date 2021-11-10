<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Avis';
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
	$current = 'admin_avis' ;
	 require_once '../includes/menu_membres.php';
    /**
     * *******FIN Gestion avant affichage...**********
     */
	
	if (isset($_GET["delete"]) && $_GET["delete"] == 'ok') {
	$id = intval($_GET['id']);
	$sql = "SELECT * FROM comments WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("DELETE FROM comments WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">L\'avis <b>'.$id.'</b> a été supprimée.</div>';
			unset($_GET); 
		}
	}
}
if (isset($_GET["verified"]) && $_GET["verified"] == 'ok') {
	$id = intval($_GET["id"]);
	$sql = "SELECT * FROM comments WHERE id = ".$id;
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE comments SET verif = 1 WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">L\'avis <b>'.$id.'</b> a été vérifié.</div>';
			unset($_GET); 
		}
	}
}

	$check_contents = $pdo->query('SELECT COUNT(*) as total FROM comments');
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
		$pagination_system.= "</div>";			
	}
			$sql= 'SELECT C.id, C.id_receiver, C.id_sender, C.date, C.commentaire, C.verif, US.membre_id as id1, US2.membre_id as id2, US.membre_utilisateur AS mu1, US2.membre_utilisateur AS mu2 FROM comments C
				LEFT JOIN user US ON C.id_receiver = US.membre_id
                LEFT JOIN user US2 ON C.id_sender = US2.membre_id
				  ORDER BY date DESC
				  LIMIT '.$start_page.', '.$page_limit.'';
				  $GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
    ?>

  <div class="block_inside">

	
  	<h2>Liste des avis</h2>
  	<p style="font-size:14px;float:right;"><b><?= $get_total_pages ?></b> avis</p>
      <p style="font-size:14px;float:left;">Page : <b><?= $current_page ?></b></p>

  	<?php
  	if ($prep->rowcount() >= 1) {
		// ECHO EN-TETE TABLEAU
		echo '<div style="height:30px;width:100%;background:#EEE;border:1px solid #CCC;display:flex;padding:5px 0">
				<div style="width:15%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Date</div>
				<div style="width:15%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Expéditeur</div>
				<div style="width:15%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Receveur</div>
				<div style="width:45%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">Avis</div>
				<div style="width:10%;line-height:30px;text-align:center;text-transform:uppercase;font-weight:bold;">Vérifié</div>
			</div><br />';
	while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
			// ECHO AFFICHAGE d'UN MESSAGE
	echo '<div class="list-item" style="border-bottom:1px solid #CCC;display:block;text-align:left;position:relative;">
				<p class="titre_message_utilisateur" style="margin-bottom:0;><a href="'.ROOTPATH.'/membres/admin/messagesvoir.php?message='.$result['id'].'">Avis n°'.$result['id'].'</a> - de '.(isset($result['mu2']) ? $result['mu2'] : 'Pas membre').'</p>
				
				<button type="button" class="btn btn-info gerer">Gérer <i style="margin-left:10px" class="fa fa-angle-down fa-lg"></i></button>
					<div class="vpb_down_triangle">
						<div class="vpb_down_triangle_inner">
							<div><a href="'.ROOTPATH.'/membres/admin/liste-avis/'.$current_page.'/'.$result['id'].'-no/ok" onclick="if(!confirm(\'Etes-vous sur de vouloir traiter ce message ?\')) return false;"><i style="margin-right:10px;" class="fa fa-check fa-lg"></i>Confirmer</a></div>
						</div>
						<div class="vpb_down_triangle_inner">
							<div><a href="'.ROOTPATH.'/membres/admin/avis_edit.php?id='.$result['id'].'&type=2"><i style="margin-right:10px;" class="fa fa-pen fa-lg"></i>Modifier l\'avis</a></div>
						</div>
						<div class="vpb_down_triangle_inner">
							<div><a href="'.ROOTPATH.'/membres/admin/liste-avis/'.$current_page.'/'.$result['id'].'-ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer ce message ?\')) return false;"><i style="margin-right:10px;" class="fa fa-times fa-lg"></i> Supprimer</a></div>
						</div>
					</div>
				<div style="display:flex;justify-content:space-between">
					<div style="line-height:40px;width:15%;height:40px;text-align:center;border-right:1px solid #CCC;">'.date("d.m.Y", $result['date']).'</div>
					<div style="line-height:40px;width:15%;height:40px;text-align:center;border-right:1px solid #CCC;"><a href="'.ROOTPATH.'/profil/'.$result['id2'].'#comment">'.$result['mu2'].'</a></div>
					<div style="line-height:40px;width:15%;height:40px;text-align:center;border-right:1px solid #CCC;font-weight:bold;"><a href="'.ROOTPATH.'/profil/'.$result['id1'].'#comment">'.$result['mu1'].'</a></div>
					<div style="padding:0 10px;width:45%;text-align:center;">'.$result['commentaire'].'</div>
					<div style="line-height:40px;width:9%;border-left:1px solid #CCC;height:40px;text-align:center;">'.($result['verif'] == 0 ? '<span style="color:#701818;">A verifier</span>' : '<span style="color:#5CA732;">Vérifié</span>').'</div>
				</div>
			</div>';	
  	} 
  	
  	} else echo 'Aucun avis'; 
      
      echo $pagination_system;?>
  				
  		
  		
  	
  	
  	
    
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
?>