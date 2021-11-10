<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';

/**
 * *******Gestion avant affichage...**********
 */

if (isset($_SESSION['membre_id'])) {
	$id = intval($_SESSION['membre_id']);
} else {
	require_once '../elements/header2.php';
	$informations = array(/*L'id de cookie est incorrect*/
		true,
		'Vous n\'&ecirc;tes pas connect&eacute;',
		'Impossible d\'accéder à cette page membre.',
		' - <a href="' . ROOTPATH . '/connexion.html">Se connecter</a>',
		'' . ROOTPATH . '/index.html',
		20
	);
	require_once('../information.php');
	exit();
}

$sql = "SELECT * FROM user WHERE membre_id = :id";
$prep = $pdo->prepare($sql);
$prep->execute(array(":id" => $id));

if ($prep->rowCount() == 0) {
	require_once '../elements/header2.php';
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
		$title = 'Messagerie interne de ' . htmlspecialchars($row['membre_utilisateur'], ENT_QUOTES) . '';

		require_once '../elements/header2.php';
		$current = 'messagerie';
		require_once 'includes/menu_membres.php';

		//SUPPRIMER UN MESSAGE
		if (isset($_GET["delete"]) && $_GET["delete"] == "ok" && is_numeric($_GET['idc'])) {

			$id = intval($_GET["idc"]);


			$sql = "SELECT * FROM conversations_users WHERE id = :idconvers";
			$prepa = $pdo->prepare($sql);
			$prepa->execute(array(":idconvers" => $id));
			$GLOBALS['nb_req']++;
			$result = $prepa->fetch(PDO::FETCH_ASSOC);
			
			if ($prepa->rowcount() == 1) {

				if($result['user_id'] == $_SESSION['membre_id'] OR $result['read_at'] == $_SESSION['membre_id']) {
					if($result['user_id'] == $_SESSION['membre_id'])
						$useris = 'user_delete';
					else if ($result['read_at'] == $_SESSION['membre_id'])
						$useris = 'read_at_delete';

					$sql1 = $pdo->prepare("UPDATE conversations_users SET ".$useris." = 1 WHERE id = $id");
					$GLOBALS['nb_req']++;
					
					if ($sql1->execute()) {
						echo '<div class="valider">La conversation a été supprimée.</div>';
						unset($_GET);
						
					}
				 } else
					echo 'Cette conversation ne vous appartient pas !!';
			} else 
				echo '<div class="erreur"La conversation n\'existe pas dans notre base de donnée !</div>';
			
		}
		
?>

		<div class="block_inside">
	
			<div id="annonces_parrainage">
				<fieldset>
					<legend>Messagerie</legend>
					
					<div id="flex">
						<div style="margin-right:5px;">
							
							<h2>Conversations</h2>


							<?php
							$sql = $pdo->query("SELECT user_id, read_at, user_delete, read_at_delete FROM conversations_users WHERE user_id = ".$_SESSION['membre_id']." OR read_at = ".$_SESSION['membre_id']);
							$sql->execute();
							$GLOBALS['nb_req']++;
							$result = $sql->fetch(PDO::FETCH_ASSOC);
							
							if ($sql->rowcount() >= 1) {
				
								if($result['user_id'] == $_SESSION['membre_id'] OR $result['read_at'] == $_SESSION['membre_id']) {
									if($result['user_id'] == $_SESSION['membre_id'])
										$useris = 'user';
									else if ($result['read_at'] == $_SESSION['membre_id'])
										$useris = 'read_at';
								}
							}

							$query = $pdo->query('SELECT id, user_delete, read_at_delete FROM conversations_users WHERE '.$useris.'_delete = 0 AND '.$useris.($useris == 'user' ? '_id' : '').' = '.$_SESSION['membre_id']);
							$query->execute();
							$resultat = $query->fetchAll(PDO::FETCH_COLUMN);
							$conversations_list = implode(",", $resultat);

							if(count($resultat) > 0 ) {

								$sql="SELECT U1.membre_id AS mid1, U2.membre_id AS mid2, U1.membre_utilisateur AS nom1, U2.membre_utilisateur AS nom2, I1.image AS image1, I2.image AS image2, MES.id1, MES.id2, MES.message, MES.lu1, MES.lu2, MES.date, MES.conversation_id, MES.id, CU.read_at_delete, CU.user_delete FROM messagerie MES
									JOIN user U1 ON MES.id1 = U1.membre_id
									LEFT JOIN images I1 ON I1.id_membre = U1.membre_id
									JOIN user U2 ON MES.id2 = U2.membre_id
									LEFT JOIN images I2 ON I2.id_membre = U2.membre_id
									LEFT JOIN conversations_users CU ON MES.conversation_id = CU.id
									INNER JOIN (
										SELECT conversation_id, MAX(id) AS id
										FROM messagerie GROUP BY conversation_id
										) AS max ON (max.conversation_id = MES.conversation_id AND max.id = mes.id)
								WHERE (I1.type = 'avatar' OR I1.type IS NULL) AND (I2.type = 'avatar' OR I2.type IS NULL) AND (MES.conversation_id IN ($conversations_list))
								GROUP by conversation_id
								ORDER by MES.id";
								$prep = $pdo->prepare($sql);
								$prep->execute();
								$GLOBALS['nb_req']++;
								$resultat = $prep->fetchAll(PDO::FETCH_ASSOC);

								if ($prep->rowcount() > 0) {
									foreach($resultat as $result) {
									($result['id1'] == $_SESSION['membre_id'] ? $lu = $result['lu2'] :  $lu = $result['lu1']);
									echo '<a href="javascript:void(0);" onclick="voirmessage('.$result['conversation_id'].', '.$result['mid1'].', '.$result['mid2'].')" class="box-messagerie-id">
												<div>
													<img style="width:90px;height:90px;" src="'.ROOTPATH.'/membres/images/'.($result['mid1'] == $_SESSION['membre_id'] ? (isset($result['image2']) ? $result['image2'] : "default_avatar.png") : (isset($result['image1']) ? $result['image1'] : "default_avatar.png")).'" />
												</div>
												<div>
													<p style="font-weight:bold;margin-bottom:5px;">'.($result['mid1'] == $_SESSION['membre_id'] ? $result['nom2'] : $result['nom1']).'</p>
													<p>' . str_replace("<br />", '', substr($result['message'], 0, 50)) . '</p>
													<p style="font-size:11px;margin-bottom:0px;">'.mepd($result['date']).'</p>
												</div>
												<div class="newmessage" style="align-self: center;">'.(($lu == 0) ? '<i class="fas fa-plus fa-2x"></i>' : '').'</div>
										</a>';
									}
								} else $no = '<br /><div class="box_annonces"><p>Aucune conversation actuellement</p></div>';
							} else $no = '<br /><div class="box_annonces"><p>Aucune conversation actuellement</p></div>';
								
								?>
							
						</div>

						<div style="width:70%;">
							<div id="repondre-fc" style="padding:10px 0 20px;margin:0 0 20px;">
									<form action="" method="POST" name="chatResponse" id="chatResponse" style="text-align:center;display:none;">
									<p style="margin: 10px 0 5px 20px;font-weight:bold;font-size:20px;">Message :</p>
										<textarea style="max-width:90%;height:100px;" id="msgrep" name="msgrep" placeholder="Inscrivez ici votre réponse"></textarea>
										<input type="text" style="display:none;" name="ca_idm" id="ca_idm" value="<?= (isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '0'); ?>" />
										<input type="text" style="display:none;" name="ca_idconvers" id="ca_idconvers" value="" />
										<input type="text" style="display:none;" name="ca_dest" id="ca_dest" value="" /><br />
										<input class="bouton" style="margin-top:10px;" type="submit" value="Envoyer" />
									</form>
							</div>
							<?= (isset($no) ? $no : '') ?>
						<div id="message-voir">
								
						</div>

						</div>
					</div>
				</fieldset>
			</div>

		</div>


<?php }
}
require_once '../elements/footer.php'; ?>

<script>
$('.box-messagerie-id').click(function() {
    $('.box-messagerie-id').removeClass('conv-selected');
    $(this).addClass('conv-selected');
});

//Lorsque l'on clique sur une conversation, les messages qui en découlent apparaissent. 
	function voirmessage(id, idfrom, idto) {
		$.ajax({
			type: "POST",
			cache: false,
			url: "includes/ajax-messagerie.php",
			data: {
				'id': id,
				'idfrom': idfrom,
				'idto': idto,
			},
			dataType: 'json',
			success: function(msg) { // si l'appel a bien fonctionné
				var idmembre = <?= $_SESSION['membre_id']; ?>;
				var idconvers = msg[0].convid;
				$('#message-voir').empty();
				$('#message-voir').append(' <div style="border-bottom:4px solid #EEE;width:100%;padding: 5px 0 10px;"><a href="?delete=ok&idc='+idconvers+'" title="Supprimer" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette conversation ?\')) return false;" style="float:right;margin: 17px 0 0 20px;font-size:18px"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" onclick="repondrefc();" class="pboutonr" style="float:right;margin-top:10px;">Répondre</a><img style="grid-row: 1; justify-content: space-between;width:90px;height:90px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+( msg[0].from_id == idmembre ? ( msg[0].from_id == idmembre ? msg[0].to_avat : msg[0].from_avat) : ( msg[0].from_id == idmembre ? msg[0].from_avat : msg[0].to_avat))+'" class=\"avatar\" /><div><p style="margin:25px 0 15px 0px;font-weight:bold;font-size:20px;">'+msg[0].destinataire+'</p><p style="font-size:11px;">'+msg[0].marchand+'</p></div></div>');
					if(idto == <?= $_SESSION['membre_id']; ?> ) 
						$("#ca_dest").val(idfrom);
					else
						$("#ca_dest").val(idto);
				$("#ca_idconvers").val(idconvers);
				
				$('#message-voir').append('<div id="liste-messages"></div>');
				
				if(msg.etat = "lu") {
				delete msg.etat
				$.each(msg, function(index, value) {
					date = new Date(value.date * 1000),
					datevalues = [
					date.getFullYear(),
					date.getMonth()+1,
					date.getDate(),
					date.getHours(),
					date.getMinutes()
					];
					ladate = 'Le '+datevalues[2]+'/'+datevalues[1]+'/'+datevalues[0]+' à '+datevalues[3]+'h'+datevalues[4];

					data = '<div style="display:grid;grid-template-columns:60px auto;grid-row: 1;margin:20px 0 20px 10px;"><img style="grid-row: 1; justify-content: space-between;width:50px;height:50px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+( value.from_id == idmembre ? ( value.from_id == idmembre ? value.to_avat : value.from_avat) : ( value.from_id == idmembre ? value.from_avat : value.to_avat))+'" class=\"avatar\" />';
					data += '<div style="grid-row: 1;grid-column: 2;align-self: center;"><p style="font-weight:bold;margin-bottom:5px;">'+value.pseudo_ecris+'</p><p style="font-size:10px;">'+ladate+'</p></div>';
					data += '<div style="grid-column: 1 / span 2;">';
					data += '<p>'+value.message+'</p>';
					data += '</div></div><hr />';
					$('#liste-messages').append(data);
				});
				}
				if(msg.etat == "nofound") {
					$('#liste-messages').append('<p>Messages non trouvés, veuillez nous contacter pour nous signaler l\'erreur</p>');
			}
			}
		});
		$(".spinner, .valider, .erreur").fadeOut();

		return false;
		// permet de rester sur la même page à la soumission du formulaire*/

	}
	
	//Lorsque l'on clique sur répondre, le formulaire de réponse apparait.
	function repondrefc() {
		$(".spinner, .valider, .erreur").fadeOut();
		var x = document.getElementById("chatResponse");
		if (x.style.display === "none") {
			x.style.display = "block";
		} else {
			x.style.display = "none";
		}
	}


	//AJAX REPONSE A UN MESSAGE : 


	$('#chatResponse').submit(function() {
		
		var serialized = $(this).serialize(); 
		$('#chatResponse').css("display", "none");
		$("#repondre-fc").append('<div class="spinner" style="text-align:center;margin:50px 0 50px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" /></div>');

		$.ajax({
			type: "POST",
			cache: false,
			url: "<?= ROOTPATH; ?>/membres/includes/messagerie-repondre-ajax.php",
			data: serialized,
			dataType: 'json',
			success: function(msg) {
				$(".spinner").fadeOut(); // si l'appel a bien fonctionné
				if (msg.erreurs == 'no') {
					
					// si la connexion en php a fonctionnée

					(function (el) {
						setTimeout(function () {
							el.children().fadeOut('div');
						}, 8000);
					}($('#repondre-fc').append('<div class="valider">Votre message a été envoyé au parrain.</div>')));
					
					$('#msgrep').val('');
					
					data = '<div style="display:grid;grid-template-columns:60px auto;grid-row: 1;margin:20px 0 20px 10px;"><img style="grid-row: 1; justify-content: space-between;width:50px;height:50px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+msg.info.from_avat+'" class=\"avatar\" />';
					data += '<div style="grid-row: 1;grid-column: 2;align-self: center;"><p style="font-weight:bold;margin-bottom:5px;">'+msg.info.pseudo_ecris+'</p><p style="font-size:10px;">'+ladate+'</p></div>';
					data += '<div style="grid-column: 1 / span 2;">';
					data += '<p>'+msg.info.message+'</p>';
					data += '</div></div><hr />';
	
					$('#liste-messages').prepend(data);

				} 
				
				else if (msg.erreurs == 'one') { // si la connexion en php a fonctionnée
					$('#repondre-fc').prepend('<div class="erreur">Il y a une erreur dans votre formulaire, merci de le corriger.</div>');
					
                    if(msg.info == 'emptymessage')
					$('#repondre-fc').prepend('<div class="erreur">Tous les champs sont obligatoires</div>');
					
                } else if (msg.erreurs == 'plusieurs') {
                    $('#repondre-fc').prepend('<div class="erreur">Il y a plusieurs erreurs dans votre formulaire de contact, merci de les corriger.</div>');
                    if(msg.info == 'empty')
					$('#repondre-fc').prepend('<div class="erreur">Tous les champs sont obligatoires</div>');
					
                } else if (msg.erreurs == 'notfound')
				$('#repondre-fc').prepend('<div class="erreur">Conversation non trouvée !</div>');
				
				else
                    $('#repondre-fc').prepend('<div class="erreur">Erreur inconnue</div>');

				// on affiche un message d'erreur dans le span prévu à cet effet
            },
            error: function (xhr, ajaxOptions, thrownError) {
				$('#repondre-fc').prepend('ERREUR RETOUR AJAX : ').append(xhr.responseText);
            }   
		});
		$(".spinner, .valider, .erreur").fadeOut();
		return false;
		
	});

	//menu apparait
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