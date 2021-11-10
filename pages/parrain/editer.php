<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Modifier mon offre de parrainage';
require_once '../../elements/header2.php';

$data['erreurs'] = 0;
    //GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
$id = intval($_GET['id']);
//On va récupérer les informations pour editer l'annonce 
$sql = "SELECT M.nom_marchand, AP.idmarchand, AP.id, M.montantdevise, M.montantremise, M.montantachatminimal, AP.code, AP.bonus, AP.description, AP.choixcode, M.choixoffre, AP.lien FROM annonces_parrainage AP
    LEFT JOIN marchands M ON AP.idmarchand = M.id
    WHERE AP.id = :id";
    $query = $pdo->prepare($sql);
    $query->bindParam(":id", $id);
    $query->execute();
    $edit = $query->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['form_editer'])) {
    if(isset($_POST['nomsite'])) {
        $donnee = trim($_POST['nomsite']);
        $result = checkobligatoire($donnee);
        if ($result == 'ok') {
            $data['info']['nomsite'] = '';
            $data['form']['nomsite'] = htmlspecialchars($donnee);
        } elseif ($result == 'empty') {
            $data['info']['nomsite'] = '<div class="erreurform">Vous n\'avez pas sélectionné de site concerné</div>';
            $data['form']['nomsite'] = '';
            $data['erreurs']++;
        }
    }
    if(isset($_POST['idma'])) {
        $donnee = trim($_POST['idma']);
        $result = checkisnumerique($donnee);
        if ($result == 'ok') {
            $data['info']['idma'] = '';
            $data['form']['idma'] = $donnee;
        } elseif ($result == 'empty') {
            $data['info']['idma'] = '<div class="erreurform">Selectionnez un site dans la liste</div>';
            $data['form']['idma'] = '';
            $data['erreurs']++;
        }
    }

    if(isset($_POST['bonus'])) {
        $donnee = trim($_POST['bonus']);
        $result = checkisnumerique($donnee);
        if ($result == 'ok') {
            $data['info']['bonus'] = '';
            $data['form']['bonus'] = $donnee;
        } elseif ($result == 'non') {
            $data['info']['bonus'] = '<div class="erreurform">Le bonus doit être exprimé en chiffres</div>';
            $data['form']['bonus'] = '';
            $data['erreurs']++;
        }
    }
    //CHOIX CODE
    if(isset($_POST['choicecode'])) {
        $donnee = trim($_POST['choicecode']);
        $result = checkobligatoire($donnee);
        if ($result == 'ok') {
            $data['info']['choicecode'] = '';
            $data['form']['choicecode'] = $donnee;
        } elseif ($result == 'empty') {
            $data['info']['choicecode'] = '<div class="erreurform">Selectionnez code/lien ou invitation dans la liste</div>';
            $data['form']['choicecode'] = '';
            $data['erreurs']++;
        }
    }
    //CODE
    if(isset($_POST['code'])) {
        $donne1 = trim($_POST['code']);
        $donne2 = trim($_POST['lien']);
        $result =  checkcodeorlien($donne1, $donne2);
        if ($result == 'ok') {
        $data['info']['code'] = '';
        $data['form']['code'] = htmlspecialchars($donne1);
        } elseif ($result == 'oneobligatoire') {
        $data['info']['code'] = '<div class="erreurform">Vous devez indiquer un code ou un lien de parrainage</div>';
        $data['form']['code'] = '';
        $data['erreurs']++;
        }
    }
    if(isset($_POST['lien'])) {
        $donne1 = trim($_POST['code']);
        $donne2 = trim($_POST['lien']);
        $result =  checkcodeorlien($donne1, $donne2);
        if ($result == 'ok') {
        $data['info']['lien'] = '';
        $data['form']['lien'] = htmlspecialchars($donne2);
        } elseif ($result == 'oneobligatoire') {
        $data['info']['lien'] = '<div class="erreurform">Vous devez indiquer un code ou un lien de parrainage</div>';
        $data['form']['lien'] = '';
        $data['erreurs']++;
        }
    }
    //DESCRIPTION
    if(isset($_POST['description'])) {
        $donnee = trim($_POST['description']);
        $result = check150carac($donnee);
        if ($result == 'ok') {
            $data['info']['description'] = '';
            $data['form']['description'] = htmlspecialchars($donnee);
        } elseif ($result == 'tooshort') {
            $data['info']['description'] = '<div class="erreurform">La description de votre annonce doit faire 200 caractères minimum</div>';
            $data['form']['description'] = '';
            $data['erreurs']++;
        }
    }
    if(isset($_SESSION['membre_id'])) {
        $idmembre = $_SESSION['membre_id'];
    } else {
        $idmembre = '';
    }
    
    if($data['erreurs'] == 0){
        if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] = 'connecter') 
            $sql = "SELECT * FROM annonces_parrainage WHERE id = :id";
         else 
            $sql = "SELECT * FROM annonces_parrainage WHERE id = :id AND idmembre = ".$_SESSION['membre_id'];
        
        $prep = $pdo->prepare($sql);
        $prep->bindParam(":id", $id);
        $prep->execute();  
        $GLOBALS['nb_req']++;
        if ($prep->rowcount() == 1) {
        $ladate = time();
        $sql = "UPDATE annonces_parrainage SET  idmarchand = :idmarchand, choixcode = :choixcode, code = :code, lien = :lien, description = :description, bonus = :bonus
        WHERE id = :id";
        $sqlbind = $pdo->prepare($sql);
        
        $sqlbind->bindParam(':idmarchand',$data['form']['idma'], PDO::PARAM_INT);
        $sqlbind->bindParam(':choixcode',$data['form']['choicecode'], PDO::PARAM_INT);
        $sqlbind->bindParam(':code',$data['form']['code'], PDO::PARAM_STR);
        $sqlbind->bindParam(':lien',$data['form']['lien'], PDO::PARAM_STR);
        $sqlbind->bindParam(':description',$data['form']['description'], PDO::PARAM_STR);
        $sqlbind->bindParam(':bonus', $data['form']['bonus'], PDO::PARAM_INT);
        $sqlbind->bindParam(':id', $id, PDO::PARAM_INT);
        


        if ($sqlbind->execute()) {
            $GLOBALS['nb_req']++;
            header('Location:'.ROOTPATH.'/membres/annonces.html?edited='.$edit['nom_marchand']);
            echo '<div class="valider">Votre annonce de parrainage a bien été modifiée. </div>';
            $data['form'] = "";
        }
    } else 
        echo "Ceci n'est pas votre annonce ! Coquin";
    
    } elseif ($data['erreurs'] > 0) {
        if ($data['erreurs'] == 1)
            echo '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
        else
            echo '<div class="erreur">Il y a ' . $data['erreurs'] . ' erreurs dans le formulaire, merci de les corriger !</div>';
            
    }
}
?>
<section class='block_inside'>
    
<h1>Modifier votre offre de parrainage</h1>


        <form class="form_ajouter" id="form_editer" name="form_editer" action="<?= ROOTPATH; ?>/parrain/editer/<?= $id; ?>" method="post" autocomplete="off">

            <ul class="flex-outer">
            <li>
                <label for="nomsite" style="margin-right:15px;">Nom du site marchand : </label>
                <input id="nomsite" style="margin-left:0px" name="nomsite" type="text" placeholder="ex: Cdiscount" <?= (isset($data['form']['nomsite']) ? 'value="'.$data['form']['nomsite'].'"' : (isset($edit['nom_marchand']) ? 'value="'.$edit['nom_marchand'].'"' : '')); ?> required><a href="#" style="margin-left:55px;vertical-align:middle;" rel="popup_marchand" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <input id="idma" name="idma" style="display:none;" type="text" <?= (isset($data['form']['idma']) ? 'value="'.$data['form']['idma'].'"' : (isset($edit['idmarchand']) ? 'value="'.$edit['idmarchand'].'"' : '')); ?>>
        
                <?= (isset($data['info']['nomsite']) != '' ? $data['info']['nomsite'] : '' ); ?>
                <?= (isset($data['info']['idma']) != '' ? $data['info']['idma'] : '' ); ?>
            </li>
            
            <li id="parrainage_phrase">
        <label style="min-width:100px;"></label>
            <p style="max-width:500px;font-weight:bold;">Le parrainage <span id="nomsitephrase"><?= $edit['nom_marchand'] ?></span> octroie <span id="gain_filleul"><?= $edit['montantremise'] ?></span><span id="devise_gain"><?= $edit['montantdevise'] ?></span> au filleul</p>
        </li>

        <p>Si vous offrez un bonus par paypal complétez le champ ci-dessous :</p>
            <li>
                <label for="bonus">Je verse un bonus ? </label> 
                <input id="bonus" name="bonus" type="number" min="1" max="1000" placeholder="Ex : 2" style="width:5%;min-width:60px;max-width:60px;margin-left:8px" <?= (isset($data['form']['bonus']) ? 'value="'.$data['form']['bonus'].'"' : (isset($edit['bonus']) ? 'value="'.$edit['bonus'].'"' : '')); ?>>&nbsp; €
                <span id="gainBonus" style="margin-left:20px"></span>
                <a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_bonus" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
            </li>

            <li style="background-color:#FFF;padding-top:15px;margin-bottom:5px;">
                <label for="choicecode">Code ou lien de parrainage:</label>
                <div class="choiceType" id="choixcode" style="margin-left:8px">
                    <label for="choixcodelien" class="radios"><input type="radio" id="choixcodelien" name="choicecode" value="1" <?= (isset($data['form']['choicecode']) && $data['form']['choicecode'] == '1' ? 'checked' : (isset($edit['choixcode']) && $edit['choixcode'] == '1' ? 'checked' : '')); ?>>Code ou lien</label>
                    <label for="choixsurinvit" class="radios"><input type="radio" id="choixsurinvit" name="choicecode" value="2" <?= (isset($data['form']['choicecode']) && $data['form']['choicecode'] == '2' ? 'checked' : (isset($edit['choixcode']) && $edit['choixcode'] == '2' ? 'checked' : '')); ?>>Sur invitation</label>
                </div>
                <?= (isset($data['info']['choicecode']) != '' ? $data['info']['choicecode'] : '' ); ?>
            </li>

            <div id="codecache">
            <li><label for="code" style="padding-right:25px;">Code :</label>
                <input id="code" name="code" type="text" placeholder="ex: PROMO5" <?= (isset($data['form']['code']) ? 'value="'.$data['form']['code'].'"' : (isset($edit['code']) ? 'value="'.$edit['code'].'"' : '')); ?>><input id="idm" style="display:none;" name="idm" type="text" <?= (isset($data['form']['idm']) ? 'value="'.$data['form']['idm'].'"' : ''); ?>><a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_code" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <?= (isset($data['info']['code']) != '' ? $data['info']['code'] : '' ); ?>
            </li>

            <li><label for="lien" style="padding-right:25px;">Lien :</label>
                <input id="lien" name="lien" type="text" placeholder="ex: http://" <?= (isset($data['form']['lien']) ? 'value="'.$data['form']['lien'].'"' : (isset($edit['lien']) ? 'value="'.$edit['lien'].'"' : '')); ?>>
                <a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_code" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <?= (isset($data['info']['lien']) != '' ? $data['info']['lien'] : '' ); ?>
            </li><br /><br />
            </div>


            
            <!-- DESCRIPTION-->
            <i><i class="fas fa-exclamation-triangle bred"></i> <b>ATTENTION :</b> Ne rajoutez pas votre code ou lien de parrainage dans la description, ceci pour vous proposer une meilleure expérience</i> <i class="fas fa-exclamation-triangle bred"></i><br />
            <li style="background-color:#FBFBFB;padding:15px 0;">
            <label for="description">Description de l'offre :<i>200 caractères minimum</i><p id="counterBlock"></p></label>
            <textarea id="description" name="description" rows="20" required><?= (isset($data['form']['description']) ? $data['form']['description'] : (isset($edit['description']) ? $edit['description'] : '')); ?></textarea><a href="#" style="margin:20px 0 0 20px;vertical-align:top;" rel="popup_description" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <br /><?=(isset($data['info']['description']) != '' ? $data['info']['description'] : '' ); ?>
            </li>
                <input type="submit" value="Envoyer" id="form_editer" name="form_editer" class="bouton" style="margin:0px auto;display:block;">
        </form>

</section>


<div id="popup_marchand" class="popup_block">
	<h2>Nom du marchand</h2>
<p>Inscrivez au moin les 2 premières lettres du marchand puis selectionnez le dans la liste qui s'affichera dessous.<br />Si vous ne trouvez pas dans la liste la boutique concernée par votre offre. N'hésitez pas à proposer un marchand. </p>
</div>
<div id="popup_code" style="text-align:left" class="popup_block">
	<h2>Votre code ou lien de parrainage</h2>
<p>Inscrivez votre code de parrainage ou votre lien de parrainage fourni par le marchand dans ce champ.<br />
Si en plus du code il y a d'autre informations nécessaire pour le parrainage, ajoutez les dans la description.</p>
</div>
<div id="popup_bonus" style="text-align:left" class="popup_block">
	<h2>Je verse un bonus au filleul</h2>
<p>Le bonus est un champ non obligatoire.<br />
Ce champ permet au parrain de verser un bonus supplémentaire au filleul en plus du montant déjà versé par le marchand pour le parrainage. <br />
Le versement de ce bonus se fait une fois que la parrainage est bien confirmé par le marchand.<br />
Le paiement s'effectue par paypal. </p>
</div>
<div id="popup_description" style="text-align:left" class="popup_block">
	<h2>Description de votre offre</h2>
<p>Inscrivez ici en détail le gain et la répartitions des gains (X€ de ma part en virement paypal...) <br />
Ajoutez ici les informations sur le parrainage, comment de bénéficier de cet avantage, comment s'inscrire sur le site etc .. </p>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo ROOTPATH; ?>/css/jquery.smartsuggest.css" />

<script>
var bonus = document.querySelector("#bonus");
var gainBonus = document.getElementById('gainBonus');

bonus.addEventListener('input', function() {

        euros = bonus.value;
        if(euros == '')
            euros = 0;
    

    isNaN(euros) ? euros = 0 : '';

    gainBonus.innerHTML = ' Vous vous engagez à reverser <b>'+euros+'</b> € au filleul';

});

$('#cacher, #codecache').hide();
$('#remise, #devise, #achatminimal, #code, #lien').prop( "disabled", true );
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");

$('#choixtype, #choixcode').click(function () {
$('#choixtype input:not(:checked), #choixcode input:not(:checked)').parent().removeClass("radios-checked").addClass("radios");
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");
changeit();
});

function changeit() {
var selected_value = $("input[name='choice']:checked").val();

    if(selected_value == 1) {
        $('#cacher').show();
        $('#remise, #devise, #achatminimal').prop( "disabled", false );
    } else {
        $('#cacher').hide();
        $('#remise, #devise, #achatminimal').prop( "disabled", true );
    }

    var codeoulien = $("input[name='choicecode']:checked").val();
    
    if(codeoulien == 1) {
        $('#codecache').show();
        $('#code').prop( "disabled", false );
        $('#lien').prop( "disabled", false );
    } else {
        $('#codecache').hide();
        $('#code').prop( "disabled", true );
        $('#lien').prop( "disabled", true );
    }
}

$(document).ready(function() {
    changeit();

    $('a.poplight').click(function() {
	var popID = $(this).attr('rel'); //Trouver la pop-up correspondante
	var popURL = $(this).attr('href'); //Retrouver la largeur dans le href

	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#' + popID).fadeIn().prepend('<a href="#" class="close" style="float:right"><img src="../../images/close_pop.png" class="btn_close" title="Fermer" alt="Fermer" /></a>');

	//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 40) / 2;

	//On affecte le margin
	$('#' + popID).css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});

	//Effet fade-in du fond opaque
	$('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

	return false;
});

///Fermeture de la pop-up et du fond
jQuery('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		jQuery('#fade , .popup_block').fadeOut(function() {
			jQuery('#fade, a.close').remove();  
	}); //...ils disparaissent ensemble
		
		return false;
	});
    $('#nomsite').smartSuggest({
        src: '<?= ROOTPATH; ?>/pages/ajouter_offre/include/autocompletion_marchands.php',
        fillBox: true,
        fillBoxWith: 'fill_text',
        executeCode: false
        });
    });
    $(function() {
            $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
            $( "#datevalidite" ).datepicker();
        });

    $(function(){
        $('#fininconnue').click(function() {
            if($('#fininconnue').is(':checked') )
                $('#permanente').prop( "checked", false );
                $('#datevalidite').val('');
        });
    });
    $(function(){
        $('#permanente').click(function() {
            if($('#permanente').is(':checked') )
                $('#fininconnue').prop( "checked", false );
                $('#datevalidite').val('');

        });
    });
    $(function(){
        $('#datevalidite').click(function() {
                $('#permanente').prop( "checked", false );
                $('#fininconnue').prop( "checked", false );
        });
    });

    //COMPTEUR DE CARACTERES

var textarea = document.querySelector('#description');
var blockCount = document.getElementById('counterBlock');

function count() {
    // la fonction count calcule la longueur de la chaîne de caractère contenue dans le textarea
    var count = 200-textarea.value.length;
    // et affche cette valeur dans la balise p#counterBlock grâce à innerHTML
   // si le count descend sous 0 on ajoute la class red à la balise p#counterBlock
   if(count<0) {
    	blockCount.classList.add("green");
        blockCount.innerHTML= "Validé";
   } 
   else if(count>=0 & count<200) {
     	blockCount.classList.remove("green");
         blockCount.innerHTML= count;
   }
   else{
    blockCount.innerHTML= count;
   }
   
}

// on pose un écouteur d'évènement keyup sur le textarea.
// On déclenche la fonction count quand l'évènement se produit et au chargement de la page
textarea.addEventListener('keyup', count);
count();
</script>
<?php require_once '../../elements/footer.php'; ?>