<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
$title = 'Ajouter un code promo';
require_once '../elements/header2.php';
$erreurs = 0;

//require_once 'ajouter_code_php.php';
//require_once 'ajouter_remise_php.php';
//require_once 'ajouter_parrain_php.php';
?>

<script type="text/javascript">
$(document).ready(function() {
	var trp = '<?php if(isset($_GET['trp'])) echo $_GET['trp']; ?>';
	if(trp == 'codep') {
		ajoutercodepview();
	}if(trp == 'remise') {
		ajouterremiseview();
	}if(trp == 'parrain') {
		ajouterparrainview();
	} 
	
});

function ajoutercodepview() {
	$("#imgload").show();
	$('#codep').attr('class','currenta');
	$('#remise, #parrain, #coupon, #odr').attr('class','');	
	$.ajax({
				type : "POST",
				url : "ajouter_offre/ajouter_code.php",
				error : function() {
					alert("Error !: " + msg);
				},
				success : function(data) {
					$('#corps').html(data);
				}
			});
}
function ajouterremiseview() {
	$("#imgload").show();
	$('#remise').attr('class','currenta');
	$('#codep, #parrain, #coupon, #odr').attr('class','');	
	$.ajax({
				type : "GET",
				url : "ajouter_remise.php",
				error : function() {
					alert("Error !: " + msg);
				},
				success : function(data) {
					$('#corps').html(data);
				}
			});
}
function ajouterparrainview() {
	$("#imgload").show();
	$('#parrain').attr('class','currenta');
	$('#codep, #remise, #coupon, #odr').attr('class','');	
	$.ajax({
				type : "GET",
				url : "ajouter_offre/ajouter_parrain.php",
				error : function() {
					alert("Error !: " + msg);
				},
				success : function(data) {
					$('#corps').html(data);
				}
			});
}

function typedecode() {
		var selected_value = '<?php if(isset($_GET['trp'])) echo $_GET['trp']; ?>';
		$("#ajouter_general, #ajouter_prix, #ajouter_validite, #ajouter_clients, #ajouter_description, #ajouter_type").hide().find('input, select').attr('disabled', true);
		switch(selected_value) {
		case 'codep': // Code promo
			$("#d_etat").hide();
			$("#ajouter_general, #ajouter_prix, #ajouter_validite, #ajouter_clients, #ajouter_description").show().find('input, select').attr('disabled', false);
			$("#ajouter_prix, #ajouter_clients").css('background', "#EEE");
			$("#ajouter_general, #ajouter_validite, #ajouter_description").css('background', "#FFF");
		break;
		case 'remise': // Remise
			$("#ajouter_prix").hide();
			$("#ajouter_general, #ajouter_validite, #ajouter_type, #ajouter_clients, #ajouter_description").show().find('input, select').attr('disabled', false);
			$("#ajouter_validite, #ajouter_clients").css('background', "#EEE");
			$("#ajouter_general, #ajouter_type, #ajouter_description").css('background', "#FFF");
		break;

		}
}

</script>

<section class='block_inside'>
    <article id="marchand">
        <h1 class="iconic alaune" >Choisissez ce que vous souhaitez ajouter</h1>
    </article>
	<?= (isset($_SESSION['nb_erreurs']) ? $_SESSION['nb_erreurs'] : ''); ?>
    <article id="ajouter-un-code-promo">
        <div class="sousmenucateg">
        	<li><a href="?trp=codep" style="border-left:1px solid #FFF;" id="codep" ><i class="fa fa-user" onclick="ajoutercodepview()" ></i> Code promo</a></li><!-- @whitespace
			--><li><a href="?trp=parrain" id="parrain"><i class="fa fa-user" onclick="ajouterparrainview()" ></i> Parrain</a></li>
 </div>
        <div id="corps">
		    <img id="imgload" style="margin:0 auto;display:none;" src="<?php echo ROOTPATH.'/images/loading.gif'; ?>" alt="Chargement..." />
        </div>
    
    </article>
</section>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<?php require_once '../elements/footer.php'; ?>