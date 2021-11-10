<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Admin :: Offres de parrainage';
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
    $current = 'admin_ap' ;
	 require_once '../includes/menu_membres.php';
	
	if (isset($_GET["delete"]) && $_GET["delete"] == 'ok') {
	$id = $_GET['id'];
	
	$sql = "SELECT * FROM annonces_parrainage WHERE id = $id";
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("DELETE FROM annonces_parrainage WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">L\'offre de parrainage n°<b>'.$id.' </b> a été supprimée.</div>';
		}
		
	}
}

if (isset($_GET["desactivate"]) && $_GET["desactivate"] == 'ok') {
	$id = $_GET['id'];
	
	$sql = "SELECT * FROM annonces_parrainage WHERE id = $id";
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE annonces_parrainage SET etatvalidation = 2 WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">L\'offre de parrainage n°<b>'.$id.' </b> a été desactivée.</div>';
		}
		
	}
}

if (isset($_GET["activate"]) && $_GET["activate"] == 'ok') {
	$id = $_GET['id'];
	
	$sql = "SELECT * FROM annonces_parrainage WHERE id = $id";
	$prepa = $pdo->prepare($sql);
	$prepa->execute();
	$GLOBALS['nb_req']++;
	if($prepa->rowcount() == 1) {
		$sql = $pdo->prepare("UPDATE annonces_parrainage SET etatvalidation = 1 WHERE id = ".$id);
		if ($sql->execute()) {
			$GLOBALS['nb_req']++;
			echo '<div class="valider">L\'offre de parrainage n°<b>'.$id.' </b> a été activée.</div>';
		}
		
	}
}
	$check_contents = $pdo->query('SELECT COUNT(*) as total FROM annonces_parrainage');
									$GLOBALS['nb_req']++;
	$get_total_pages = $check_contents->fetch(PDO::FETCH_ASSOC);
	$get_total_pages = $get_total_pages['total'];
	$page_limit = 10; //This is the number of contents to display on each page
	$pagination_stages = 3;
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
			$sql= ' SELECT AP.id, AP.idmembre, M.img, M.montantremise, M.choixoffre, M.montantdevise, AP.description, AP.dateajout, AP.etatvalidation, M.id as idmarchand, M.nom_marchand, M.cat, U.membre_utilisateur, I.id_membre, I.image, I.type
                    FROM annonces_parrainage AP
                    LEFT JOIN user U ON U.membre_id = AP.idmembre
                    LEFT JOIN marchands M ON AP.idmarchand = M.id
                    LEFT JOIN images I ON AP.idmembre = I.id_membre AND I.type = "avatar"
                    ORDER BY AP.created_at DESC
				    LIMIT '.$start_page.', '.$page_limit.'';
				    $GLOBALS['nb_req']++;
			$prep = $pdo->prepare($sql);
			$prep->execute();
    ?>
  <section class="block_inside">
  
  	<h1>Liste des offres de parrainage</h1>
  	<p style="font-size:14px;float:right;"><b><?= $get_total_pages ?></b> offres</p>
      <p style="font-size:14px;float:left;">Page : <b><?= $current_page ?></b></p>
  	<?php
  	if ($prep->rowcount() >= 1) {
		  // ECHO EN-TETE TABLEAU
	  echo '<div style="height:30px;width:100%;background:#EEE;border:1px solid #CCC;display:flex;padding:5px 0">
	  			<div style="width:12%;min-width:100px;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Avatar</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Date</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Marchand</div>
				<div style="width:53%;line-height:30px;text-align:center;border-right:1px solid #CCC;text-transform:uppercase;font-weight:bold;">Offre</div>
				<div style="width:10%;line-height:30px;text-align:center;border-right:1px solid #CCC;font-weight:bold;text-transform:uppercase;font-weight:bold;">Montant</div>
				<div style="width:5%;line-height:30px;text-align:center;text-transform:uppercase;font-weight:bold;">Etat</div>
        	</div><br />';
	while ($row = $prep->fetch(PDO::FETCH_ASSOC)) {
		// ECHO AFFICHAGE D'UN MEMBRE
	echo '<div class="list-item" style="border-bottom:1px solid #CCC;display:block;text-align:left;position:relative;">
			
	
	
	<button type="button" class="btn btn-info gerer">Gérer <i style="margin-left:10px" class="fa fa-angle-down fa-lg"></i></button>
		<div class="vpb_down_triangle">
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '"><i style="margin-right:10px;" class="fas fa-search"></i> Voir</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="' . ROOTPATH . '/parrain/editer/'.$row['id'].'"><i style="margin-right:10px;" class="fas fa-edit"></i> Modifier</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="'.ROOTPATH.'/membres/admin/ap.php?id='.$row['id'].'&desactivate=ok"><i style="margin-right:10px;" class="fas fa-minus"></i> Desactiver</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="'.ROOTPATH.'/membres/admin/ap.php?id='.$row['id'].'&activate=ok"><i style="margin-right:10px;" class="fas fa-check-circle"></i> Activer</a></div>
			</div>
			<div class="vpb_down_triangle_inner" align="left">
				<div><a href="'.ROOTPATH.'/membres/admin/ap.php?id='.$row['id'].'&delete=ok" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette offre ?\')) return false;"><i style="margin-right:10px;" class="fa fa-times fa-lg"></i> Supprimer</a></div>
			</div>
		</div>
	<div style="display:flex;justify-content:space-between">
	
	<div style="width:12%;text-align:center;border-right:1px solid #CCC;"><a href="' . ROOTPATH . '/profil/'.$row['idmembre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['image']) ? $row['image'] : '/default_avatar.png').'" class="avatar" /><p class="titre_message_utilisateur" style="margin:-15px 0 0 -10px;font-size:16px"><br />' . $row['membre_utilisateur'] . ' ('.nombrecodes($row['idmembre']).')</p></a></div>
	<div style="line-height:45px;width:10%;text-align:center;border-right:1px solid #CCC;">'.date("d.m.Y", $row['dateajout']).'</div>
	<div style="line-height:45px;width:10%;text-align:center;border-right:1px solid #CCC;"><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage"><div class="item-img" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'] . '\');"></div></a></div>
	<div style="width:53%;text-align:center;border-right:1px solid #CCC;word-break: break-all;padding:5px 10px;max-height:150px;">' . substr($row['description'], 0, 450) . '</div>
	<div style="line-height:45px;width:10%;text-align:center;border-right:1px solid #CCC;font-weight:bold;">'.(($row['choixoffre'] == 1) ? $row['montantremise'].$row['montantdevise'] : (($row['choixoffre'] == 2) ? '<i style="margin-right:5px" class="fas fa-truck"></i>Livraison offerte' : (($row['choixoffre'] == 3) ? '<i style="margin-right:5px" class="fas fa-gift"></i>Cadeau offert' : $row['montantremise'].$row['montantdevise']))).'</div>
	<div style="line-height:45px;width:5%;text-align:center;padding:10px;">'.($row['etatvalidation'] == 0 ? '<span style="font-weight:bold;">Non-activé' : (($row['etatvalidation'] == 1) ? '<span style="font-weight:bold;color:#51D836">Activé' : (($row['etatvalidation'] == 2) ? '<span style="font-weight:bold;color:#ff9000">Bloqué' : '<span style="font-weight:bold;color:#CC0000">Error etat validation'))).'</span></div>
	</div>
</div>
	
	
			';
  	}
  	echo $pagination_system;
  	} else echo 'Aucunes offres de parrainage'; ?>	
  		
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