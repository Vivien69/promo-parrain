<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

$Pagelimit = 1;
$output = '';
$marchand = intval($_POST['marchand']);

//Y a il une page postee
if(isset($_POST['page']))
    $page = intval($_POST['page']);
else
    $page = 1;

$startPage = ($page - 1) * $Pagelimit;

//Requête SQL
$query = $pdo->prepare("SELECT CM.*, M.id, M.nom_marchand, U.membre_utilisateur, U.membre_id, I.image FROM comments_marchands CM
    LEFT JOIN marchands M ON M.id = CM.id_marchand
    LEFT JOIN user U ON U.membre_id = CM.id_sender
    LEFT JOIN images I ON CM.id_sender = I.id_membre
    WHERE CM.id_marchand = :idmarchand AND (I.type = 'avatar' OR I.type IS NULL)
    ORDER BY CM.date DESC
    LIMIT :startPage,:Pagelimit");
$query->bindParam(":idmarchand",$marchand, PDO::PARAM_INT);
$query->bindParam(":startPage",$startPage, PDO::PARAM_INT);
$query->bindParam(":Pagelimit",$Pagelimit, PDO::PARAM_INT);
$query->execute();
$GLOBALS['nb_req']++;
$results = $query->fetchAll();
$nombre = count($results);
            $output .= '<div class="comment">
                        <p><i style="margin-right:10px;font-size:22px;" class="fas fa-comment"></i>'.$nombre.' avis </p>
                    </div><br />
                    <div id="listcomment">';

if($nombre > 0) {

    foreach($results as $result) {
        $output .= '<div id="'.$result['id'].'" class="viewcomment">
            <img style="align-items:flex-start;width:60px;height:60px;border-radius:50%;margin-right:10px;" src="'. ROOTPATH .'/membres/images/'. (isset($result['image']) && $result['image'] != '' ? $result['image'] : 'default_avatar.png') .'" />
            <div>
                <p style="margin-top:10px;margin-bottom:0;"><a style="font-weight:bold" href="'.ROOTPATH .'/profil/'.$result['id_sender'] .'">'. $result['membre_utilisateur'] .'</a> <span style="color:#999999;margin-left:15px;font-size:11px">'.strtolower(mepd($result['date'])) .'</span></p>
                <p style="margin-left:0px;font-size:15px;"><span style="font-size:12px;">Note attribuée : </span>';
                for($i=0;$i < 5; $i++) {
                    if($i < $result['note'])
                        $type = 'fas';
                    else
                        $type = 'far';
                        $output .= "<i class='$type fa-star fa-1x' style='color:#701818;'></i>";
                }
                $output .='</p>
            </div>
            <br /><br />
            <p>'.$result['commentaire'] .'</p>
        </div><hr>';
    }
    }

//Code de pagination 
$query = $pdo->query("SELECT CM.*, U.membre_id, U.membre_utilisateur FROM comments_marchands CM
LEFT JOIN user U ON U.membre_id = CM.id_sender
WHERE CM.id_marchand = $marchand");
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
    ($current_page > 1) ? $pagination_system .= "<a class='paginat page-item2' id='".($current_page - 1)."'>< Précedent</a>" : '';

    // Pages	
    if ($last_page < 7 + ($Pagelimit * 2))	// Not enough pages to breaking it up
    {	
        for ($page_counter = 1; $page_counter <= $last_page; $page_counter++)
        {
            if ($page_counter == $current_page) {
                $pagination_system.= "<span class='current'>$page_counter</span>";
            }
            else {
                $pagination_system.= "<a class='paginat page-item2' id='".($page_counter)."'>$page_counter</a>";
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
                    $pagination_system.= "<a class='paginat page-item2' id='".$page_counter."'>$page_counter</a>";
                }					
            }
            $pagination_system.= "...";
            $pagination_system.= "<a class='paginat page-item2' id='".$lastpaged."'>$lastpaged</a>";
            $pagination_system.= "<a class='paginat page-item2' id='".$last_page."'>$last_page</a>";		
        }
        //Middle hide some front and some back
        elseif($last_page - ($Pagelimit * 2) > $current_page && $current_page > ($Pagelimit * 2))
        {
            $pagination_system.= "<a class='paginat page-item2' id='1'>1</a>";
            $pagination_system.= "<a class='paginat page-item2' id='2'>2</a>";
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
            $pagination_system.= "<a class='paginat page-item2' id='".$lastpaged."'>$lastpaged</a>";
            $pagination_system.= "<a class='paginat page-item2' id='".$last_page."'>$last_page</a>";		
        }
        //End only hide early pages
        else
        {
            $pagination_system.= "<a class='paginat page-item2' id='1'>1</a>";
            $pagination_system.= "<a class='paginat page-item2' id='2'>2</a>";
            $pagination_system.= "...";
            for ($page_counter = $last_page - (2 + ($Pagelimit * 2)); $page_counter <= $last_page; $page_counter++)
            {
                if ($page_counter == $current_page) {
                    $pagination_system.= "<span class='current'>$page_counter</span>";
                }
                else {
                    $pagination_system.= "<a class='paginat page-item2' id='".$page_counter."'>$page_counter</a>";
                }					
            }
        }
    }		
    //Next Page
    if ($current_page < $page_counter - 1) { $page_countersuiv = $current_page +1; $pagination_system.= "<a class='paginat page-item2' id='".$page_countersuiv."'>Suivant ></a>"; }
    else { $pagination_system.= ""; }
    $pagination_system.= "</div>";			
}
$output .= $pagination_system;
echo $output;

?>



