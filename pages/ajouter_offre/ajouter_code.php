<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Ajouter une code promo';
?>
<meta name="description" content="Partagez sur promo-parrain un code promo sur une boutique en ligne ">
<meta name="keywords" content="code, promo, parrain, parrainage, offre, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/parrain/ajouter" />
<meta name="robots" content="noodp,noydir" />

<?php

$erreurs = 0;
if(isset($_POST['form_ajouter'])) {
    //GENERAL(3) NOM SITE, IDSITE(transparent)) ET CODE
        if(isset($_POST['nomsite'])) {
            $donnee = trim($_POST['nomsite']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $data['info']['nomsite'] = '';
                $data['form']['nomsite'] = $donnee;
            } elseif ($result == 'empty') {
                $data['info']['nomsite'] = '<div class="erreurform">Vous n\'avez pas sélectionné de site concerné</div>';
                $data['form']['nomsite'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['idm'])) {
            $donnee = trim($_POST['idm']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $data['info']['idm'] = '';
                $data['form']['idm'] = $donnee;
            } elseif ($result == 'empty') {
                $data['info']['idm'] = '<div class="erreurform">Selectionnez un marchand dans la liste</div>';
                $data['form']['idm'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['code'])) {
            $donnee  = trim($_POST['code']);
            $result = checkcodeexist($donnee, $data['form']['idm']);
            if ($result == 'ok') {
                $data['info']['code'] = '';
                $data['form']['code'] = $donnee;
            } elseif ($result == 'empty') {
                $data['info']['code'] = '<div class="erreurform">Vous n\'avez pas ajouter le code promotionnel</div>';
                $data['form']['code'] = '';
                $erreurs++;
            } elseif ($result == 'exists') {
                $data['info']['code'] = '<div class="erreurform">Le code promotionnel existe déja sur Promo-Parrain !</div>';
                $data['form']['code'] = '';
                $erreurs++;
            }
        }
    //MONTANT (4) REMISE, DEVISE, MONTANT MINIMUM, BON D'ACHAT 
        if(isset($_POST['remise'])) {
            $donnee = trim($_POST['remise']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
                $data['info']['remise'] = '';
                $data['form']['remise'] = $donnee;
            } elseif ($result == 'non') {
                $data['info']['remise'] = '<div class="erreurform">La remise doit être exprimé en chiffres</div>';
                $data['form']['remise'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['devise'])) {
            $donnee = trim($_POST['devise']);
            $result = checkobligatoire($donnee);
            if ($result == 'ok') {
                $data['info']['devise'] = '';
                $data['form']['devise'] = $donnee;
            } elseif ($result == 'empty') {
                $data['info']['devise'] = '<div class="erreurform">Vous n\'avez pas ajouter de devise</div>';
                $data['form']['devise'] = '';
                $erreurs++;
            }
        }
        if(isset($_POST['achatminimal'])) {
            $donnee = trim($_POST['achatminimal']);
            $result = checkisnumerique($donnee);
            if ($result == 'ok') {
                $data['info']['achatminimal'] = '';
                $data['form']['achatminimal'] = $donnee;
            } elseif ($result == 'non') {
                $data['info']['achatminimal'] = '<div class="erreurform">Le montant minimal doit être exprimé en chiffres</div>';
                $data['form']['achatminimal'] = '';
                $erreurs++;
            }
        }
        //CHOIX CODE
    if(isset($_POST['choice'])) {
        $donnee = trim($_POST['choice']);
        $result = checkobligatoire($donnee);
        if ($result == 'ok') {
            $data['info']['choice'] = '';
            $data['form']['choice'] = $donnee;
        } elseif ($result == 'empty') {
            $data['info']['choice'] = '<div class="erreurform">Selectionnez un type de remise</div>';
            $data['form']['choice'] = '';
            $erreurs++;
        }
    }
		// VALIDITE DATE, FIN INCONNUE ou PERMANENTE
		if(isset($_POST['validite']) OR isset($_POST['validitedate'])) {
            if(!empty($_POST['validite'])) {
                    $validite = $_POST['validite'];
                    $validitesql = implode(',', $validite);
                    $data['form']['validite'] =  $validitesql;
                    $data['form']['tableauvalidite'] = $validite;
                    $data['info']['validite'] = '';
                    unset($data['form']['validitedate']);
            } elseif($_POST['validitedate'] != "") {
                    $validite = $_POST['validitedate'];
                    $data['form']['validitedate'] =  $validite;
                    $data['form']['tableauvalidite'] = [];
                    $data['info']['validite'] = '';
                    unset($data['form']['validite']);
            }
            } else {
                $data['info']['validite'] = '<div class="erreurform">Veuillez indiquer une validitée</div>';
                unset($data['form']['validite']);
                unset($data['form']['validitedate']);
                $data['form']['tableauvalidite'] = [];
                $erreurs++;
            }
        //isset($_POST['validite']['date']) && $_POST['validite']['date'] != "") OR (isset($_POST['validite']['fininconnue']) && $_POST['validite']['fininconnue'] != "") OR (isset($_POST['validite']['permanente']) && $_POST['validite']['permanente'] != "")

		//NOUVEAUX CLIENTS OU ANCIENS CLIENTS
		if (isset($_POST['clients']) && $_POST['clients'] != "") {
			$clients = $_POST['clients'];

			$clientssql = implode(',', $clients);
			$data['form']['clients'] = $clientssql;
			$data['form']['tableauclients'] = $clients;
		}
        if(isset($_POST['conditions'])) {
            $donnee = trim($_POST['conditions']);
                $data['info']['conditions'] = '';
                $data['form']['conditions'] = $donnee;
        }
        if(isset($_SESSION['membre_id'])) {
            $idmembre = $_SESSION['membre_id'];
        } else {
            $idmembre = '';
		}
		
        if($erreurs == 0){
            $ladate = time();
            
            $sql = "INSERT INTO codespromo (idmarchand,idmembre,code,montantremise,montantdevise,montantachatminimal,typeremise,validitedate,validiteinconnupermanente,clients,conditions,dateajout) 
            VALUES (:idmarchand,:idmembre,:code,:montantremise,:montantdevise,:montantachatminimal,:typeremise,:validitedate,:validiteinconnupermanente,:clients,:conditions,:dateajout)";
            $sqlbind = $pdo->prepare($sql);
            
            $sqlbind->bindParam(':idmarchand', $data['form']['idm'], PDO::PARAM_INT);
            $sqlbind->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
            $sqlbind->bindParam(':code', $data['form']['code'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantremise', $data['form']['remise'], PDO::PARAM_INT);
            $sqlbind->bindParam(':montantdevise', $data['form']['devise'], PDO::PARAM_STR);
            $sqlbind->bindParam(':montantachatminimal', $data['form']['achatminimal'], PDO::PARAM_INT);
            $sqlbind->bindParam(':typeremise', $data['form']['choice'], PDO::PARAM_INT);
            $sqlbind->bindParam(':validitedate', $data['form']['validitedate'], PDO::PARAM_STR);
            $sqlbind->bindParam(':validiteinconnupermanente', $data['form']['validite'], PDO::PARAM_INT);
            $sqlbind->bindParam(':clients', $data['form']['clients'], PDO::PARAM_STR);
            $sqlbind->bindParam(':conditions', $data['form']['conditions'], PDO::PARAM_STR);
            $sqlbind->bindParam(':dateajout', $ladate, PDO::PARAM_INT);

            if ($sqlbind->execute()) {
                $message = '<div class="valider">Merci ! Votre code promo a bien été ajouté </div>';
				$GLOBALS['nb_req']++;
                unset($data['form'],$data['info']);
            }
        } elseif ($erreurs > 0) {
            if ($erreurs == 1)
            $message = '<div class="erreur">Il y une erreur dans votre formulaire, merci de la corriger !</div>';
            else
            $message ='<div class="erreur">Il y a ' . $erreurs . ' erreurs dans le formulaire, merci de les corriger !</div>';
        
        
        }
            
}

require_once '../../elements/header2.php';
 ?>

<link href="<?= ROOTPATH ?>/css/dist/mc-calendar.min.css" rel="stylesheet" />
<script src="<?= ROOTPATH ?>/css/dist/mc-calendar.min.js"></script>

<section class='block_inside'>
    <?= (isset($message) ? $message : '')?>
    <article id="marchand">
        <h1 class="iconic alaune" >Ajoutez une offre</h1>
        <?php 
        $menuactive = 'codepromo';
        require_once 'menu_ajouter.php'; ?>
       
        
        <form class="form_ajouter containerform" id="form_ajouter" name="form_ajouter" action="" method="post" style="text-align:left;">
            <!-- SITE, CODE-->
            <ul class="flex-outer">
            <li>
                <label for="nomsite" style="margin-right:0px;">Nom du site marchand : </label>
                <input id="nomsite" style="margin-left:0px;" name="nomsite" type="text" placeholder="ex: Cdiscount" <?= (isset($data['form']['nomsite']) ? 'value="'.$data['form']['nomsite'].'"' : ''); ?> required><a href="#" style="margin-left:45px;vertical-align:middle;" rel="popup_marchand" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <input id="idm" name="idm"  style="display:none;" type="text" <?= (isset($data['form']['idm']) ? 'value="'.$data['form']['idm'].'"' : ''); ?>>
                <?= (isset($data['info']['nomsite']) != '' ? $data['info']['nomsite'] : '' ); ?>
                <?= (isset($data['info']['idm']) != '' ? $data['info']['idm'] : '' ); ?>
            </li>
            <li> 
                <label for="code" style="margin-right:0px;">Code :</label>
                <input style="margin-left:0px;" id="code" name="code" type="text" placeholder="ex: ZERJ4" <?= (isset($data['form']['code']) ? 'value=" '.$data['form']['code'].'"' : ''); ?>><a href="#" style="margin-left:27px;vertical-align:middle;" rel="popup_code" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
                <?= (isset($data['info']['code']) != '' ? $data['info']['code'] : '' ); ?>
                
            </li>
                
            <!--Offre de parrainage pour le filleul - Remise, Frais de port G, Cadeau -->
		<div style="padding-top:15px;margin-bottom:5px;">
                <label for="choice">Type de remise :</label>
                <div class="choiceType" id="choixtype" style="margin-left:8px">
                    <label for="choixremise" class="radios"><input type="radio" id="choixremise" name="choice" value="1" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '1' ? 'checked' : ''); ?>>Remise</label>
                    <label for="choixfdp" class="radios"> <input type="radio" id="choixfdp" name="choice" value="2" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '2' ? 'checked' : ''); ?>>Frais de port gratuit</label>
                    <label for="choixca" class="radios"><input type="radio" id="choixca" name="choice" value="3" <?= (isset($_SESSION['form']['choice']) && $_SESSION['form']['choice'] == '3' ? 'checked' : ''); ?>> Cadeau offert</label>
					
				</div>
                <?= (isset($_SESSION['info']['choice']) != '' ? $_SESSION['info']['choice'] : '' ); ?>
            <br />
            </div>
		<div class="cacher">
            
                <label for="remise">Montant de la remise :</label>
                <input id="remise" name="remise" type="text" style="width:5%;min-width:60px;max-width:60px;margin-left:8px" placeholder="ex: 5"  <?= (isset($_SESSION['form']['remise']) ? 'value="'.$_SESSION['form']['remise'].'"' : ''); ?>>
                    <select id="devise" name="devise" style="width:5%;min-width:60px;">
                        <option value="€" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '€' ? 'selected' : ''); ?>>€</option>
                        <option value="%" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '%' ? 'selected' : ''); ?>>%</option>
                        <option value="mois" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'mois' ? 'selected' : ''); ?>>minutes</option>
                        <option value="mois" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'mois' ? 'selected' : ''); ?>>mois</option>
                        <option value="jours" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'jours' ? 'selected' : ''); ?>>jours</option>
                        <option value="points" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == 'points' ? 'selected' : ''); ?>>points</option>
                    </select> &nbsp;
               à partir de 
            <input id="achatminimal" name="achatminimal" type="text" placeholder="ex: 60" style="width:5%;min-width:50px;" <?= (isset($_SESSION['form']['achatminimal']) ? 'value="'.$_SESSION['form']['achatminimal'].'"' : ''); ?>> 
            &nbsp;€ d'achat <a href="#" style="margin-left:20px;vertical-align:middle;" rel="popup_remise" class="poplight"><i class="far fa-question-circle fa-2x bred"></i></a>
            
            <br />  <?= (isset($_SESSION['info']['remise']) != '' ? $_SESSION['info']['remise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['devise']) != '' ? $_SESSION['info']['devise'] : '' ); ?>
                    <?= (isset($_SESSION['info']['achatminimal']) != '' ? $_SESSION['info']['achatminimal'] : '' ); ?>
            </div>

				<br /><br />
                <!-- VALIDITE-->
            <div id="ajouter_validite" style="padding:15px 0;">
                <label for="datevalidite">Valable jusqu'au :</label><input id="datevalidite" name ="validitedate" type="text" placeholder="JJ/MM/AAAA à HH:mm" <?= (isset($data['form']['validitedate']) ? 'value="'.$data['form']['validitedate'].'"' : ''); ?>>
                <label for="valable"></label>
                <label for="fininconnue">Fin inconnue :</label><input id="fininconnue" name="validite[]" type="checkbox" value="1"  <?php if(isset($data['form']['tableauvalidite']) &&  in_array("1",$data['form']['tableauvalidite'])) echo 'checked'; ?>><label for="fininconnue"><span class="ui"></span></label>
                <label for="permanente">Validité permanente :</label><input id="permanente" name="validite[]" type="checkbox" value="2"  <?php if(isset($data['form']['tableauvalidite']) && in_array("2",$data['form']['tableauvalidite'])) echo 'checked'; ?>><label for="permanente"><span class="ui"></span></label><br /><br />
                <?= (isset($data['info']['validite']) != '' ? $data['info']['validite'] : '' ); ?>
            </div>
                <!-- NOUVEAUX CLIENTS / ANCIENS CLIENTS-->
            <div id="ajouter_clients" style="padding:15px 0 25px;">
                <label for="valable">Valable pour :</label>
                <label for="nvxclients">Nouveaux clients :</label><input id="nvxclients" name="clients[]" type="checkbox" value="1" <?php if (isset($data['form']['tableauclients']) && in_array("1",$data['form']['tableauclients'])) echo 'checked'; ?>><label for="nvxclients"><span class="ui"></span></label>
                <label for="anciensclients">Anciens clients :</label><input id="anciensclients" name="clients[]" type="checkbox" value="2" <?php if (isset($data['form']['tableauclients']) && in_array("2",$data['form']['tableauclients'])) echo 'checked'; ?>><label for="anciensclients"><span class="ui"></span></label>
                <?= (isset($data['info']['clients']) != '' ? $data['info']['clients'] : '' ); ?>
            </div>
            <!-- CONDITIONS-->
            <div id="ajouter_conditions" style="padding:15px 0;">
                <label style="vertical-align:top;" for="conditions">Conditions de l'offre :</label><textarea id="conditions" name="conditions" rows="4"><?= (isset($data['form']['conditions']) ? $data['form']['conditions'] : ''); ?></textarea>
               
                <?=(isset($data['info']['conditions']) != '' ? $data['info']['conditions'] : '' ); ?>
            </div>
            <input type="submit" value="Envoyer" class="bouton" id="form_ajouter" name="form_ajouter" style="margin:0px auto; display:block;">
        </form>
    </article>
</section>

<div id="popup_marchand" style="text-align:left" class="popup_block">
	<h2>Nom du marchand</h2>
<p>Inscrivez au moin les 2 premières lettres du marchand puis selectionnez le dans la liste qui s'affichera dessous.<br />Si vous ne trouvez pas dans la liste la boutique concernée par votre offre. N'hésitez pas à proposer un marchand. </p>
</div>
<div id="popup_code" style="text-align:left" class="popup_block">
	<h2>Votre code promo</h2>
<p>- Inscrivez le code promo fourni par le marchand dans ce champ.<br />
- Si en plus du code il y a des conditions, indiquez les dans le champ prévu à cet effet ci-dessous.</p>
</div>
<div id="popup_remise" style="text-align:left" class="popup_block">
	<h2>Montant de la remise</h2>
<p>- Inscrivez le montant de la remise que permet ce code promo<br />
- Le champ <b>"à partir de" n'est pas obligatoire</b> et est à remplir si le code promo fonctionne à partir d'un montant d'achat minimal. <br />
- Si en plus du code il y a d'autre informations nécessaire pour le parrainage, ajoutez les dans le champ prévu à cet effet ci-dessous.</p>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo ROOTPATH; ?>/css/jquery.smartsuggest.css" />
<script>

$(document).ready(function(){  

      $('#form_ajouter').submit(function(){
            var nomsite = $('#nomsite').val();
            var idm = $('#idm').val();
            if(nomsite == '' || idm == '') {
                $('#nomsite-info').html('<div class="erreurform">Ce champ est obligatoire</div>');
                $('#nomsite').css('border', '2px solid #701818').css('background', '#FAE4E4');
            }
            var code = $('#code').val();   
             if(code == '') {
                $('#code-info').html('<div class="erreurform">Ce champ est obligatoire</div>');
                $('#code').css('border', '1px solid #701818').css('background', '#FAE4E4');
            }
           else  
           {  
            var url = form.attr('action');
                $.ajax({
                     url: url,  
                     method:"POST",  
                     data:$('#form_ajouter').serialize(),  
                     beforeSend:function(){  
                          $('#response').html('<span class="text-info"><img src="<?= ROOTPATH.'/images/loading.gif'; ?></span>');  
                     },  
                     success:function(data){  
                          $('#form_ajouter').trigger("reset");  
                          $('#response').fadeIn().html(data);  
                          setTimeout(function(){  
                               $('#response').fadeOut("slow");  
                          }, 5000);  
                     }  
                });  
           }  
      });  
 });  
//RECHERCHE DE MARCHAND
    $(document).ready(function() {
    $('#nomsite').smartSuggest({
        src: '<?= ROOTPATH; ?>/pages/include/autocompletion_marchands.php',
        fillBox: true,
        fillBoxWith: 'fill_text',
        executeCode: false
        });
    });
//DATEPICKER
const myDatePicker = MCDatepicker.create({ 
                    el: '#datevalidite'
                });
//COCHE DECOCHE VALIDITE PERMANANTE OU FIN INCONNUE
    $(function(){
        document.getElementById('permanente').addEventListener('click', function() {
            if( document.getElementById('permanente').checked = true)
            document.getElementById('fininconnue').checked = false;
            document.getElementById('datevalidite').value = '';
        });
        document.getElementById('fininconnue').addEventListener('click', function() {
            if( document.getElementById('fininconnue').checked = true)
            document.getElementById('permanente').checked = false;
            document.getElementById('datevalidite').value = '';
        });
        document.getElementById('datevalidite').addEventListener('focus', function() {
            document.getElementById('permanente').checked = false;
            document.getElementById('fininconnue').checked = false;
        });
        
        
    });

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
$('.cacher, #codecache').hide();
$('#remise, #devise, #achatminimal').prop( "disabled", true );
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");

$('#choixtype, #choixcode').click(function () {
$('#choixtype input:not(:checked), #choixcode input:not(:checked)').parent().removeClass("radios-checked").addClass("radios");
$('#choixtype input:checked, #choixcode input:checked').parent().removeClass("radios").addClass("radios-checked");
changeit();
});

function changeit() {
var selected_value = $("input[name='choice']:checked").val();

    if(selected_value == 1) {
        $('.cacher').show();
        $('#remise, #devise, #achatminimal').prop( "disabled", false );
    } else {
        $('.cacher').hide();
        $('#remise, #devise, #achatminimal').prop( "disabled", true );
    }

}


</script>
<?php require_once '../../elements/footer.php'; ?>