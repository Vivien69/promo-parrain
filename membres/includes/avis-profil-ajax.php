<?php

require_once("../../includes/config.php");
require_once("../../includes/function.php");

$Pagelimit = 10;
$output = '';
$id = intval($_POST['id']);

//Y a il une page postee
if(isset($_POST['page']))
    $page = intval($_POST['page']);
else
    $page = 1;

$startPage = ($page - 1) * $Pagelimit;

//Requête SQL pour selectionner les commentaires à afficher
$query = $pdo->prepare("SELECT C.*,C.id as idc, M.id, M.nom_marchand, U.membre_utilisateur, U.membre_id, I.image FROM comments C
    LEFT JOIN marchands M ON M.id = C.id_marchand
    LEFT JOIN user U ON U.membre_id = C.id_sender
    LEFT JOIN images I ON C.id_sender = I.id_membre
    WHERE C.id_receiver = :id_receiver AND (I.type = 'avatar' OR I.type IS NULL)
    ORDER BY C.date DESC
    LIMIT :startPage,:Pagelimit");
$query->bindParam(":id_receiver",$id, PDO::PARAM_INT);
$query->bindParam(":startPage",$startPage, PDO::PARAM_INT);
$query->bindParam(":Pagelimit",$Pagelimit, PDO::PARAM_INT);
$query->execute();
$GLOBALS['nb_req']++;
$results = $query->fetchAll();
$nombre = count($results);
                          
if($nombre > 0) {

    foreach($results as $result) {
        $output .= '<div id="'.$result['idc'].'" class="viewcomment">
            <img style="align-items:flex-start;width:70px;height:70px;border-radius:50%;margin-right:10px;" src="'. ROOTPATH .'/membres/images/'. (isset($result['image']) && $result['image'] != '' ? $result['image'] : 'default_avatar.png') .'" />
            <div>
                <div style="display:flex;justify-content:space-between;height:30px;position:relative;">
                    <p style="margin:0;"><a style="font-weight:bold" href="'.ROOTPATH .'/profil/'.$result['id_sender'] .'">'. $result['membre_utilisateur'] .'</a>
                    <p style="margin:0;font-size:18px;text-align:center;">';
                        for($i=0;$i < 5; $i++) {
                            if($i < $result['note'])
                                $type = 'fas';
                            else
                                $type = 'far';
                                $output .= "<i class='$type fa-star fa-1x' style='color:#701818;'></i>";
                        }
                        $output .='</p>
                        <div>
                            <button type="button" class="btn btn-info opts" style="position:absolute;right:10px;background-color:#FFF;color:#666;"><i class="fa fa-angle-down fa-lg"></i></button>
                            <div class="vpb_down_triangle" style="margin-top:31px;>
                                <div class="vpb_down_triangle_inner">
                                    <div><a href="' . ROOTPATH . '/parrain/"><i style="margin-right:10px;" class="fas fa-search"></i> Signaler</a></div>
                                </div>
                            </div>
                        </div>
                    <div>
                        <p style="margin-top:12px;">'.$result['commentaire'] .'</p>
                        <span style="color:#8A8A8A;margin-right:10px;font-size:11px;float:right;">'.strtolower(mepd($result['date'])) .'</span>
                    </div>
                </div>
                
            </div>
               
                
            
        </div><hr>';
    }
    
    }

//Code de pagination 
$query = $pdo->query("SELECT C.*, U.membre_id, U.membre_utilisateur FROM comments C
LEFT JOIN user U ON U.membre_id = C.id_sender
WHERE C.id_receiver = $id");
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
<script>

    var nav = document.getElementsByClassName('vpb_down_triangle');
    var opts = document.getElementsByClassName('opts');

    document.addEventListener('click', function() {
    for (let item of nav) {
            item.style.display = 'none';
        }
    });
    for (let item of nav) {
        item.style.display = 'none';
    }

    for (let i = 0; i < opts.length; i++) {

    opts[i].addEventListener('click', function(e) {
        e.stopPropagation();

        if(this.nextElementSibling.style.display == "block") {
            this.nextElementSibling.style.display = "none";
        } else {
            this.nextElementSibling.style.display = "block";
        }

        for (let item of nav) {

            if(item !=  this.nextElementSibling) {
                item.style.display = "none";
            }

        }

    });
    
}
    

</script>