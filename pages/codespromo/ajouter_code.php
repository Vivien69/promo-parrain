<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';
require_once '../../elements/header.php';
$title = 'Ajouter un code promo';
require_once '../../elements/header2.php';

?>
<section class='block_inside'>
    <article id="marchand">
        <h1 class="iconic alaune" >Ajoutez un code ppromo</h1>
        <div id="response"></div>
        <form id="form_ajouter" name="form_ajouter" action="<?= ROOTPATH; ?>/pages/codespromo/ajouter_code_ajax.php" method="post" style="text-align:left;">
            <!-- SITE, CODE-->
            <div id="ajouter_general" style="padding:15px 0;">
                <label for="nomsite">Nom du site :</label><input id="nomsite" name="nomsite" type="text" placeholder="ex: Cdiscount" <?= (isset($_SESSION['form']['nomsite']) ? 'value="'.$_SESSION['form']['nomsite'].'"' : ''); ?> required><input id="idm" style="display:none;" name="idm" type="text" <?= (isset($_SESSION['form']['idm']) ? 'value="'.$_SESSION['form']['idm'].'"' : ''); ?>>
                    <div id="nomsite-info"></div>
                    
                <label for="code">Code :</label><input id="code" name="code" type="text" placeholder="ex: ZERJ4" <?= (isset($_SESSION['form']['code']) ? 'value=" '.$_SESSION['form']['code'].'"' : ''); ?>>
                <div id="code-info"></div>
            </div>
                <!-- PRIX-->
            <div id="ajouter_prix" style="background-color:#eee;padding:15px 0;">
                <label for="remise" >Remise de :</label><input id="remise" name="remise" type="text" placeholder="ex: 5" style="width:5%;min-width:40px;" <?= (isset($_SESSION['form']['remise']) ? 'value="'.$_SESSION['form']['remise'].'"' : ''); ?>>
                                                                        <select id="devise" name="devise" style="width:5%;min-width:40px;">
                                                                            <option value="€" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '€' ? 'selected' : ''); ?>>€</option>
                                                                            <option value="%" <?= (isset($_SESSION['form']['devise']) && $_SESSION['form']['devise'] == '%' ? 'selected' : ''); ?>>%</option>
                                                                        </select>
                                                                                à partir de 
                                                    <input id="achatminimal" name="achatminimal" type="text" placeholder="ex: 100€" style="width:5%;min-width:40px;" <?= (isset($_SESSION['form']['achatminimal']) ? 'value="'.$_SESSION['form']['achatminimal'].'"' : ''); ?>> € d'achat
                <br />  <?= (isset($_SESSION['info']['remise']) != '' ? $_SESSION['info']['remise'] : '' ); ?>
                        <?= (isset($_SESSION['info']['devise']) != '' ? $_SESSION['info']['devise'] : '' ); ?>
                        <?= (isset($_SESSION['info']['achatminimal']) != '' ? $_SESSION['info']['achatminimal'] : '' ); ?>
                <label for="bondachat"></label> <label for="anciensclients">En bons d'achat</label><input id="bondachat" name="bondachat" type="checkbox" value="1" <?= (isset($_SESSION['form']['bondachat']) ? 'checked' : ''); ?>><label for="bondachat"><span class="ui"></span></label>
                    <?= (isset($_SESSION['info']['bondachat']) != '' ? $_SESSION['info']['bondachat'] : '' ); ?>
            </div>
                <!-- VALIDITE-->
            <div id="ajouter_validite" style="padding:15px 0;">
                <label for="datevalidite">Valable jusqu'au :</label><input id="datevalidite" name ="validitedate" type="text" placeholder="JJ/MM/AAAA à HH:mm" <?= (isset($_SESSION['form']['validitedate']) ? 'value="'.$_SESSION['form']['validitedate'].'"' : ''); ?>>
                <label for="valable"></label>
                <label for="fininconnue">Fin inconnue :</label><input id="fininconnue" name="validite[]" type="checkbox" value="1"  <?php if(isset($_SESSION['form']['tableauvalidite']) &&  in_array("1",$_SESSION['form']['tableauvalidite'])) echo 'checked'; ?>><label for="fininconnue"><span class="ui"></span></label>
                <label for="permanente">Validité permanente :</label><input id="permanente" name="validite[]" type="checkbox" value="2"  <?php if(isset($_SESSION['form']['tableauvalidite']) && in_array("2",$_SESSION['form']['tableauvalidite'])) echo 'checked'; ?>><label for="permanente"><span class="ui"></span></label><br /><br />
                <?= (isset($_SESSION['info']['validite']) != '' ? $_SESSION['info']['validite'] : '' ); ?>
            </div>
                <!-- NOUVEAUX CLIENTS / ANCIENS CLIENTS-->
            <div id="ajouter_clients" style="background-color:#eee;padding:15px 0 25px;">
                <label for="valable">Valable pour :</label>
                <label for="nvxclients">Nouveaux clients :</label><input id="nvxclients" name="clients[]" type="checkbox" value="1" <?php if (isset($_SESSION['form']['tableauclients']) && in_array("1",$_SESSION['form']['tableauclients'])) echo 'checked'; ?>><label for="nvxclients"><span class="ui"></span></label>
                <label for="anciensclients">Anciens clients :</label><input id="anciensclients" name="clients[]" type="checkbox" value="2" <?php if (isset($_SESSION['form']['tableauclients']) && in_array("2",$_SESSION['form']['tableauclients'])) echo 'checked'; ?>><label for="anciensclients"><span class="ui"></span></label>
                <?= (isset($_SESSION['info']['clients']) != '' ? $_SESSION['info']['clients'] : '' ); ?>
            </div>
            <!-- CONDITIONS-->
            <div id="ajouter_conditions" style="padding:15px 0;">
                <label style="vertical-align:top;" for="conditions">Conditions de l'offre :</label><textarea id="conditions" name="conditions" rows="4" required><?= (isset($_SESSION['form']['conditions']) ? $_SESSION['form']['conditions'] : ''); ?></textarea>
                30 caractères minimum
                <?=(isset($_SESSION['info']['conditions']) != '' ? $_SESSION['info']['conditions'] : '' ); ?>
            </div>
            <input type="submit" value="Envoyer" class="bouton" id="form_ajouter" name="form_ajouter" style="margin:0px auto; display:block;">
        </form>
    </article>
</section>

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

$("#form_ajouter").submit(function(e) {
e.preventDefault(); // avoid to execute the actual submit of the form.

var form = $(this);
var url = form.attr('action');

$.ajax({
       type: "POST",
       url: url,
       data: form.serialize(), // serializes the form's elements.
       success: function(data)
       {
           alert(data); // show response from the php script.
       }
     });
});
//RECHERCHE DE MARCHAND
    $(document).ready(function() {
    $('#nomsite').smartSuggest({
        src: '<?= ROOTPATH; ?>/pages/ajouter_offre/include/autocompletion_marchands.php',
        fillBox: true,
        fillBoxWith: 'fill_text',
        executeCode: false
        });
    });
//DATEPICKER
    $(function() {
            $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
            $( "#datevalidite" ).datepicker();
        });
//COCHE DECOCHE VALIDITE PERMANANTE OU FIN INCONNUE
    $(function(){
        $('#permanente').click(function() {
            if($('#permanente').is(':checked') )
                $('#fininconnue').prop( "checked", false );
                $('#datevalidite').val('');
        });
        $('#fininconnue').click(function() {
            if($('#fininconnue').is(':checked') )
                $('#permanente').prop( "checked", false );
                $('#datevalidite').val('');
        });
        $('#datevalidite').click(function() {
                $('#permanente').prop( "checked", false );
                $('#fininconnue').prop( "checked", false );
        });
    });
</script>

<?php require_once '../../elements/footer.php'; ?>