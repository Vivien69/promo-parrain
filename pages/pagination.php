<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

$Pagelimit = 10;
$output = '';
$marchand = intval($_POST['marchand']);

//Y a il une page postee
if(isset($_POST['page']))
    $page = intval($_POST['page']);
else
    $page = 1;

$startPage = ($page - 1) * $Pagelimit;

//Requête SQL
$query = $pdo->prepare($sql = "SELECT AP.id, AP.idmembre, AP.etatvalidation, M.img, M.montantremise, M.montantdevise, AP.description, M.id as idmarchand, AP.bonus, AP.dateajout, M.choixoffre, M.nom_marchand, M.img, M.cat, U.membre_utilisateur, U.conf_datemask, I.id_membre, I.image, I.type
    FROM annonces_parrainage AP
    JOIN marchands M ON AP.idmarchand = M.id
    LEFT JOIN images I ON AP.idmembre = I.id_membre AND I.type = 'avatar'
    JOIN user U ON AP.idmembre = U.membre_id
    WHERE idmarchand = :idmarchand AND AP.etatvalidation = 1
    ORDER BY AP.dateajout DESC
    LIMIT :startPage,:Pagelimit");
$query->bindParam(":idmarchand",$marchand, PDO::PARAM_INT);
$query->bindParam(":startPage",$startPage, PDO::PARAM_INT);
$query->bindParam(":Pagelimit",$Pagelimit, PDO::PARAM_INT);
$query->execute();
$GLOBALS['nb_req']++;
$rows = $query->fetchAll();

if(count($rows) > 0) {
    foreach($rows as $row)
        //Si il y a des resultats alors on affiche les tableaux
        $output .= '<article class="box-annonce" style="align-items:center;margin:0 10px;">
        <div style="align-items:flex-start;width:17%;min-width:170px;max-width:180px;"><a href="'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage">' . $row['nom_marchand'] . '<div class="item-img-moyen" style="background-image: url(\'' . ROOTPATH . '/membres/includes/uploads-img/120-' . $row['img'] . '\');background-size:140px;"></div></a></div>
        <div class="box-mid"><h6 class="titre-annonce">Offre de parrainage '.$row['nom_marchand'].' de ' . $row['membre_utilisateur'] . '</h6><span style="display:block;margin-top:-2px;font-size:10px;font-family:Verdana;color:#333333;margin-bottom:10px;">Publiée '.(isset($row['conf_datemask']) && $row['conf_datemask'] == 1 ? mepd($row['dateajout'], 1) : mepd($row['dateajout'])).'</span><span id="descrip-annonce">' . mb_substr($row['description'], 0, 200) . '</span></div>
        <div style="width:15%;"><a href="' . ROOTPATH . '/profil/'.$row['idmembre'].'"><img style="width:65px;height:65px;" src="' . ROOTPATH . '/membres/images/'.(isset($row['image']) ? $row['image'] : '/default_avatar.png').'" class="avatar" /> <span style="font-weight:bold"><br />' . $row['membre_utilisateur'] . '</a></div>
        <div  class="box-view"><span style="font-weight:bold;font-size:16px">'.(($row['choixoffre'] == 1) ? $row['montantremise'].$row['montantdevise'] : (($row['choixoffre'] == 2) ? '<i style="margin-right:5px" class="fas fa-truck"></i>Livraison offerte' : (($row['choixoffre'] == 3) ? '<i style="margin-right:5px" class="fas fa-gift"></i>Cadeau offert' : ''))).'</span><br /><br /><a class="pboutonr" style="font-size:17px;padding:10px 15px 7px;" href="' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '" title="Voir le parrainage">Voir le parrainage</a></div>
    </article>
         <br />'; 
        
} else {
    $output .= '<article><div class="box_annonces"><p>Aucune offre de parrain pour ce marchand pour le moment. </p></div><br /><br />
    <button class="bouton" onclick="location.href=\''.ROOTPATH.'/parrain/ajouter\'" type="button">Soyez le premier</button>
    <br /><br /></article>';
}

//Code de pagination 
$query = $pdo->query("SELECT AP.id, AP.idmembre, M.img, M.montantremise, M.montantdevise, AP.description, AP.dateajout, M.nom_marchand, M.img, U.membre_utilisateur
    FROM annonces_parrainage AP
    JOIN marchands M ON AP.idmarchand = M.id
    JOIN user U ON AP.idmembre = U.membre_id
    WHERE idmarchand = $marchand AND etatvalidation = 1");
$query->execute();
$GLOBALS['nb_req']++;

//Initialisation des variables de pages
$TotalRows = $query->rowcount();
$current_page = $page;
$previous_page = $current_page - 1;	
$next_page = $current_page + 1;							
$last_page = ceil($TotalRows/$Pagelimit);		
$lastpaged = $last_page - 1;
$pagination_system = '';

if($last_page > 1)
{
    $pagination_system .= "<div class='pagination_system'>";
    // Previous Page
    ($current_page > 1) ? $pagination_system .= "<a class='paginat page-item' id='".($current_page - 1)."'>< Précedent</a>" : '';

    // Pages	
    if ($last_page < 7 + ($Pagelimit * 2))	// Not enough pages to breaking it up
    {	
        for ($page_counter = 1; $page_counter <= $last_page; $page_counter++)
        {
            if ($page_counter == $current_page) {
                $pagination_system.= "<span class='current'>$page_counter</span>";
            }
            else {
                $pagination_system.= "<a class='paginat page-item' id='".($page_counter)."'>$page_counter</a>";
            }					
        }
    }
    elseif($last_page > 5 + ($Pagelimit * 2))	// This hides few pages when the displayed pages are much
    {
        //Beginning only hide later pages
        if($current_page < 1 + ($Pagelimit * 2))		
        {
            for ($page_counter = 1; $page_counter < 4 + ($Pagelimit * 2); $page_counter++)
            {
                if ($page_counter == $current_page) {
                    $pagination_system.= "<span class='current'>$page_counter</span>";
                }
                else {
                    $pagination_system.= "<a class='paginat page-item' id='".$page_counter."'>$page_counter</a>";
                }					
            }
            $pagination_system.= "...";
            $pagination_system.= "<a class='paginat page-item' id='".$lastpaged."'>$lastpaged</a>";
            $pagination_system.= "<a class='paginat page-item' id='".$last_page."'>$last_page</a>";		
        }
        //Middle hide some front and some back
        elseif($last_page - ($Pagelimit * 2) > $current_page && $current_page > ($Pagelimit * 2))
        {
            $pagination_system.= "<a class='paginat page-item' id='1'>1</a>";
            $pagination_system.= "<a class='paginat page-item' id='2'>2</a>";
            $pagination_system.= "...";
            for ($page_counter = $current_page - $Pagelimit; $page_counter <= $current_page + $Pagelimit; $page_counter++)
            {
                if ($page_counter == $current_page) {
                    $pagination_system.= "<span class='current'>$page_counter</span>";
                }
                else {
                    $pagination_system.= "<a class='paginat' id='".$page_counter."'>$page_counter</a>";
                }					
            }
            $pagination_system.= "...";
            $pagination_system.= "<a class='paginat page-item' id='".$lastpaged."'>$lastpaged</a>";
            $pagination_system.= "<a class='paginat page-item' id='".$last_page."'>$last_page</a>";		
        }
        //End only hide early pages
        else
        {
            $pagination_system.= "<a class='paginat page-item' id='1'>1</a>";
            $pagination_system.= "<a class='paginat page-item' id='2'>2</a>";
            $pagination_system.= "...";
            for ($page_counter = $last_page - (2 + ($Pagelimit * 2)); $page_counter <= $last_page; $page_counter++)
            {
                if ($page_counter == $current_page) {
                    $pagination_system.= "<span class='current'>$page_counter</span>";
                }
                else {
                    $pagination_system.= "<a class='paginat page-item' id='".$page_counter."'>$page_counter</a>";
                }					
            }
        }
    }		
    //Next Page
    if ($current_page < $page_counter - 1) { $page_countersuiv = $current_page +1; $pagination_system.= "<a class='paginat page-item' id='".$page_countersuiv."'>Suivant >"; }
    else { $pagination_system.= ""; }
    $pagination_system.= "</div>";			
}
$output .= $pagination_system;
echo $output;

?>