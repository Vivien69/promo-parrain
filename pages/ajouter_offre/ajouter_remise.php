<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';

?>
<h3>Ajoutez une Remise</h3>
    <form class="form_ajouter" id="form_ajouter_remise" name="form_ajouter_remise" action="<?= ROOTPATH; ?>/pages/ajouter_offre/ajouter_remise_php.php" method="post" style="text-align:left;">
            <!-- SITE, CODE-->
        <div id="ajouter_general" style="padding:15px 0;">
            <label for="nomsite">Nom du site :</label><input id="nomsite" name="nomsite" type="text" placeholder="ex: Cdiscount" <?= (isset($_SESSION['form']['nomsite']) ? 'value="'.$_SESSION['form']['nomsite'].'"' : ''); ?> required><input id="idm" style="display:none;" name="idm" type="text" <?= (isset($_SESSION['form']['idm']) ? 'value="'.$_SESSION['form']['idm'].'"' : ''); ?>>
                <div id="info-nomsite"></div>
                <div id="info-idm"></div>
        </div>
            <!--Remise € % Livraison-->
        <div id="typederemise">
            <label for="anciensclients">Remise :</label>
                <label for="euros" class="radios"><input type="radio" name="remisede" id="euros" value="1" <?php if (isset($_SESSION['form']['remisede']) && $_SESSION['form']['remisede'] == '1') echo 'checked'; ?>> Euros €</label>
                <label for="pourcent" class="radios"><input type="radio" name="remisede" id="pourcent" value="2" <?php if (isset($_SESSION['form']['remisede']) && $_SESSION['form']['remisede'] == '1') echo 'checked'; ?>> Pourcentage %</label>
                <label for="livraison-gratuite" class="radios"><input type="radio" name="remisede" id="livraison-gratuite" value="3" <?php if (isset($_SESSION['form']['remisede']) && $_SESSION['form']['remisede'] == '3') echo 'checked'; ?>> Livraison gratuite</label>
        </div>
         <!-- PRIX-->
        <div id="ajouter_prix" style="background-color:#eee;padding:15px 0;display:none;">
            <label for="remise" >Montant :</label><input id="remise" name="remise" type="text" placeholder="ex: 5" style="width:5%;min-width:40px;" <?= (isset($_SESSION['form']['remise']) ? 'value="'.$_SESSION['form']['remise'].'"' : ''); ?>>
                                                                    <select id="devise" name="devise" style="width:5%;min-width:40px;">
                                                                        <option value="€" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '€' ? 'selected' : ''); ?>>€</option>
                                                                        <option value="%" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '%' ? 'selected' : ''); ?>>%</option>
                                                                    </select>
                                                                            à partir de 
                                                <input id="achatminimal" name="achatminimal" type="text" placeholder="ex: 100€" style="width:5%;min-width:40px;" <?= (isset($_SESSION['form']['achatminimal']) ? 'value="'.$_SESSION['form']['achatminimal'].'"' : ''); ?>> € d'achat
            <br />  
            <div id="info-devise"></div>
            <div id="info-achatminimal"></div>
            <div id="info-anciensclients"></div>
            <label for="bondachat"></label> <label for="anciensclients">En bons d'achat</label><input id="bondachat" name="bondachat" type="checkbox" value="1" <?= (isset($_SESSION['form']['bondachat']) ? 'checked' : ''); ?>><label for="bondachat"><span class="ui"></span></label>
            <div id="info-bondachat"></div>
        </div>
            <!-- VALIDITE-->
        <div id="ajouter_validite" style="padding:15px 0;">
            <label for="valable">Valable jusqu'au :</label><input id="datevalidite" name ="datevalidite" type="text" placeholder="JJ/MM/AAAA à HH:mm">
            <label for="valable"></label>
            <label for="fininconnue">Fin inconnue :</label><input id="fininconnue" name="fininconnue" type="checkbox"><label for="fininconnue"><span class="ui"></span></label>
            <label for="permanente">Validité permanente :</label><input id="permanente" name="permanente" type="checkbox"><label for="permanente"><span class="ui"></span></label><br /><br />
            <div id="info-validite"></div>
        </div>
        <!-- TYPE DE REMISE-->
        <div id="ajouter_type" style="background-color:#eee;padding:15px 0 25px;">
            <label for="valable">Valable sur :</label>
            <label for="ensembledusite">L'ensemble du site :</label><input id="ensembledusite" name="ensembledusite" type="checkbox"><label for="ensembledusite"><span class="ui"></span></label>
            <label for="marchandise">Une marchandise :</label><input id="marchandise" name="marchandise" type="checkbox"><label for="marchandise"><span class="ui"></span></label>
            <label for="quelmarchandise">Marchandise remisée :</label><input type="text" id="quelmarchandise" name="quelmarchandise" placeholder="Ex: La catégorie Puériculture ou un PC Asus">
            <label for="urlmarchandise">Adresse vers la remise :</label><input type="text" id="urlmarchandise" name="urlmarchandise" placeholder="Ex: Adresse vers la catégorie remisé ou la marchandise">
            
        </div>
            <!-- NOUVEAUX CLIENTS / ANCIENS CLIENTS-->
        <div id="ajouter_clients" style="background-color:#eee;padding:15px 0 25px;">
            <label for="valable">Valable pour :</label>
            <label for="nvxclients">Nouveaux clients :</label><input id="nvxclients" name="clients[]" type="checkbox" value="1" <?php if (isset($_SESSION['form']['tableauclients']) && in_array("1",$_SESSION['form']['tableauclients'])) echo 'checked'; ?>><label for="nvxclients"><span class="ui"></span></label>
            <label for="anciensclients">Anciens clients :</label><input id="anciensclients" name="clients[]" type="checkbox" value="2" <?php if (isset($_SESSION['form']['tableauclients']) && in_array("2",$_SESSION['form']['tableauclients'])) echo 'checked'; ?>><label for="anciensclients"><span class="ui"></span></label>
            <div id="info-clients"></div>
        </div>
        <!-- CONDITIONS-->
        <div id="ajouter_conditions" style="padding:15px 0;">
            <label style="vertical-align:top;" for="conditions">Conditions de l'offre :</label><textarea id="conditions" name="conditions" rows="4" required><?= (isset($_SESSION['form']['conditions']) ? $_SESSION['form']['conditions'] : ''); ?></textarea>
            30 caractères minimum
            <div id="info-conditions"></div>
        </div>

        <input type="submit" value="Envoyer" class="bouton" id="form_ajouter_remise" style="margin:0px auto;">

    </form>
<div id="response"></div>
<script>
//AJAX FORM
$('#form_ajouter_remise').submit(function(e){  
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var frm = $('#form_ajouter_remise');
                $.ajax({  
                     url: frm.attr('action'),  
                     type: frm.attr('method'),
                     data: frm.serialize(),
                     beforeSend:function(){  
                          $('#response').html('<span class="text-info">Loading response...</span>');  
                     },  
                     success: function (data) {
                        console.log('Submission was successful.');
                        console.log(data);
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                        console.log(data);
                    }, 
                });  
      });  
// FORM RADIO REMISE CHECKED
$(document).ready(function () {
    $('#typederemise input:checked').parent().removeClass("radios").addClass("radios-checked");
    $('#typederemise').click(function () {
    $('#typederemise input:not(:checked)').parent().removeClass("radios-checked").addClass("radios");
    $('#typederemise input:checked').parent().removeClass("radios").addClass("radios-checked");
   });
   $('#typederemise').change(function(){
       var selected= $("input[name='remisede']:checked").val();
       if ((selected == 1) || (selected == 2)) //Véhicules
		$('#ajouter_prix').show();
         if ((selected == 3))
        $('#ajouter_prix').hide();
   });
});
   
//SMARTSUGGEST MARCHAND
 $(document).ready(function() {
    $('#nomsite').smartSuggest({
        src: '<?= ROOTPATH; ?>/pages/ajouter_offre/include/autocompletion_marchands.php',
        fillBox: true,
        fillBoxWith: 'fill_text',
        executeCode: false
        });
    });
//CHECK PAS CHECK MARCHANDISE OU TOUT LE SITE
    $(function(){
        $('#ensembledusite').click(function() {
            if($('#ensembledusite').is(':checked') )
                $('#marchandise').prop( "checked", false );
                $('#quelmarchandise, #urlmarchandise').empty().attr('disabled', true).css('background', "#EEE");
                if($('#ensembledusite').is('input:not(:checked)') )
                $('#quelmarchandise, #urlmarchandise').empty().attr('disabled', false).css('background', "#FFF");
        });
        $('#marchandise').click(function() {
            if($('#marchandise').is(':checked') )
                $('#ensembledusite').prop( "checked", false );
                $('#quelmarchandise, #urlmarchandise').empty().attr('disabled', false).css('background', "#FFF");
        });
        $('#quelmarchandise, #urlmarchandise').on('mouseup', function() {
            $('#marchandise').prop( "checked", true );
            $('#ensembledusite').prop( "checked", false );
        });
    });

</script>
