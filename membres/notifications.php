<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */


if(isset($_GET['deleteall']) && $_GET['deleteall']) {
	$sql ='DELETE FROM notifications WHERE idmembre = '.$_SESSION['membre_id'];
	$sql = $pdo->prepare($sql);
	$GLOBALS['nb_req']++;
	if($sql->execute())
	$message = '<div class="valider">Toutes vos notifications ont été supprimées</div>';
}
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
	$id = (int) $_GET['delete'];
	$sql = $pdo->prepare('DELETE FROM notifications WHERE id = :id AND idmembre = '.$_SESSION['membre_id']);
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$GLOBALS['nb_req']++;
	if($sql->execute())
	$message = '<div class="valider">Notification supprimée</div>';
} 

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
$sql = "SELECT * FROM user WHERE membre_id = :id";
$prep = $pdo->prepare($sql);
$prep->execute(array(":id" => $id));

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
		$titre = 'Notifications de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'notifications';
		require_once 'includes/menu_membres.php';

		//SUPPRIMER UNE NOTIFICATION
		if (isset($_GET["del"]) && is_numeric($_GET["del"])) {
			$id = intval($_GET["del"]);

			$sql = "SELECT * FROM notifications WHERE id = :id AND idmembre = :idmembre";
			$prepa = $pdo->prepare($sql);
			$prepa->execute(array(":id" => $id, "idmembre" =>  $_SESSION['membre_id']));
			$GLOBALS['nb_req']++;
			if ($prepa->rowcount() == 1) {
				$sql = $pdo->prepare("DELETE FROM notifications WHERE id = " . $id);
				$GLOBALS['nb_req']++;
				if ($sql->execute()) {
					echo '<div class="valider">La notification a été supprimée.</div>';
					unset($_GET);
				}
			} else {
				echo '<div class="erreur">La notification n\'existe pas dans notre base de donnée !</div>';
				$_GET["del"] = '';
				$_GET['nomsite'] = '';
			}
		}
// PAGINATION
$check_contents = $pdo->query('SELECT COUNT(*) as total FROM notifications WHERE idmembre = '.$_SESSION['membre_id']);
$GLOBALS['nb_req']++;
$get_total_pages = $check_contents->fetch(PDO::FETCH_ASSOC);
$get_total_pages = $get_total_pages['total'];
$page_limit = 15; //This is the number of contents to display on each page
$pagination_stages = 5;
if(isset($_GET['page']) && $_GET['page'] != '') {
	$current_page = (int) strip_tags(str_replace('/','',$_GET['page']));
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
$pagination_system.= "<a class='paginat' href='/$page_counter'>$page_counter</a>";
}					
}
}
}		
//Next Page
if ($current_page < $page_counter - 1) { $page_countersuiv = $current_page +1; $pagination_system.= "<a class='paginat' href='$page_countersuiv'>Suivant ></a>"; }
else { $pagination_system.= ""; }
$pagination_system.= "</div><div class='current' style='float:right;margin:-60px 10px 0 0px;'>Page : <b>$current_page</b> sur $last_page</div>";			
}
$sql= 'SELECT N.*, N.id as idnotif, AP.id, AP.idmarchand, M.nom_marchand, M.img, U.membre_id, U.membre_utilisateur as nameSender, IM.image as imguser, IM.type, M.cat
FROM notifications N
LEFT JOIN annonces_parrainage AP ON AP.id = N.annonce
LEFT JOIN marchands M ON AP.idmarchand = M.id
LEFT JOIN user U ON N.idsender = U.membre_id
LEFT JOIN images IM ON IM.id_membre = N.idsender AND IM.type = "avatar"
WHERE N.idmembre = '.$_SESSION['membre_id'].'
ORDER BY date DESC
LIMIT '.$start_page.', '.$page_limit.'';
$GLOBALS['nb_req']++;
$prep = $pdo->prepare($sql);
$prep->execute();
?>
<style>
.without:after {
	content:none;
}
</style>
<?= isset($message) ? $message : ''; ?>
<div class="block_inside">
			<h1>Mes notifications</h1>
			<p style="text-align:right"><a href="?deleteall=true" onclick="if(!confirm('Etes-vous sur de vouloir supprimer toutes vos notifications ?\n ')) return false;" class="pboutonr" style="font-size:16px;padding:8px 15px 5px;font-weight:normal">Supprimer les notifications</a></p>
			
 		
<?php

nombre_notifications($_SESSION['membre_id']);



	if ($prep->rowcount() > 0) {
		$results = $prep->fetchALL(PDO::FETCH_ASSOC);
		foreach($results as $row) {
			$bg = "";
			if($row['vu'] == 0) 
			$bg = ' background-color:#C8F6D0;';
			
			switch ($row['action']) {
				case 1:
					echo '<div class="box-annonce3" style="display:grid;grid-template-columns:1fr 2fr 1fr 0.3fr;padding:5px 10px;margin-bottom:7px;justify-content:space-around;'.$bg.'">
					<div><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage"><img class="item-img" style="height:auto;max-height:36px;width:auto;padding:2px;vertical-align:middle;" src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'" /></a></div>
					<div><img alt="Code promo de Parrainage de ' . $row['nameSender'] . '" style="vertical-align:middle;justify-self: end;width:40px;height:40px;margin-right:10px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['imguser']) ? $row['imguser'] : '/default_avatar.png').'" class="avatar" />'.(strpos($row['idsender'], 'Filleul') === 0 ? $row['idsender'] : '<a href="' . ROOTPATH . '/profil/'.$row['membre_id'].'">'.$row['nameSender'].'</a>').' à copier votre adresse de parrainage '.$row['nom_marchand'].' </div><div> '.mepd($row['date']).'</div><a class="without" href="?delete='.$row['idnotif'].'"><i class="fas fa-times"></i></a></div>';
					break;
				case 2:
					echo '<div class="box-annonce3" style="display:grid;grid-template-columns:1fr 2fr 1fr 0.3fr;padding:5px 10px;margin-bottom:7px;justify-content:space-around;'.$bg.'">
					<div><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage"><img class="item-img" style="height:auto;max-height:36px;width:auto;padding:2px;vertical-align:middle;" src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'" /></a></div>
					<div><img alt="Code promo de Parrainage de ' . $row['nameSender'] . '" style="vertical-align:middle;justify-self: end;width:40px;height:40px;margin-right:10px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['imguser']) ? $row['imguser'] : '/default_avatar.png').'" class="avatar" />'.(strpos($row['idsender'], 'Filleul') === 0 ? $row['idsender'] : '<a href="' . ROOTPATH . '/profil/'.$row['membre_id'].'">'.$row['nameSender'].'</a>').' à copier votre code de parrainage '.$row['nom_marchand'].' </div><div> '.mepd($row['date']).'</div><a class="without" href="?delete='.$row['idnotif'].'"><i class="fas fa-times"></i></a></div>';
					break;
				case 3:
					echo '<div class="box-annonce3" style="display:grid;grid-template-columns:1fr 2fr 1fr 0.3fr;padding:5px 10px;margin-bottom:7px;justify-content:space-around;'.$bg.'">
					<div><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage"><img class="item-img" style="height:auto;max-height:36px;width:auto;padding:2px;vertical-align:middle;" src="'.ROOTPATH.'/membres/includes/uploads-img/120-'.$row['img'].'" /></a></div>
					<div><img alt="Code promo de Parrainage de ' . $row['nameSender'] . '" style="vertical-align:middle;justify-self: end;width:40px;height:40px;margin-right:10px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['imguser']) ? $row['imguser'] : '/default_avatar.png').'" class="avatar" />'.(strpos($row['idsender'], 'Filleul') === 0 ? $row['idsender'] : '<a href="' . ROOTPATH . '/profil/'.$row['membre_id'].'">'.$row['nameSender'].'</a>').' vous à envoyer un message privé </div><div> '.mepd($row['date']).'</div><a class="without" href="?delete='.$row['idnotif'].'"><i class="fas fa-times"></i></a></div>';
					break;
			}

		}

	$sql = 'UPDATE notifications SET vu = 1 WHERE idmembre = '.$_SESSION['membre_id'];
	$prep = $pdo->prepare($sql);
	$prep->execute();
	$prep->closeCursor();

	} else {
		echo '<br /><div class="box_annonces"><p>Vous n\'avez actuellement aucune notification.</p></div>';
	}

	echo $pagination_system;
	?>

</div>


<?php }
}
require_once '../elements/footer.php'; ?>