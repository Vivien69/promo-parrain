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

<section class="block_inside">

		<div id="flex">
			<article class="messagerieScrollConversation" >
				
				<h2>Conversations</h2>


				<?php
				//On cherche les conversation existante du membre :
				$conversations_list = messagerieConversationList();
						
				// Au moin une conversation existe donc on affiche la liste :
				if($conversations_list['nb'] > 0 ) {

					$sql="SELECT U1.membre_id AS mid1, U2.membre_id AS mid2, U1.membre_utilisateur AS nom1, U2.membre_utilisateur AS nom2, I1.image AS image1, I2.image AS image2, MES.id1, MES.id2, MES.message, MES.lu1, MES.lu2, MES.date, M.id, M.nom_marchand, MES.conversation_id, MES.id, MES.info_message, CU.read_at_delete, CU.user_delete FROM messagerie MES
						JOIN user U1 ON MES.id1 = U1.membre_id
						LEFT JOIN images I1 ON I1.id_membre = U1.membre_id AND I1.type = 'avatar'
						JOIN user U2 ON MES.id2 = U2.membre_id
						LEFT JOIN images I2 ON I2.id_membre = U2.membre_id AND I2.type = 'avatar'
						LEFT JOIN conversations_users CU ON MES.conversation_id = CU.id
						LEFT JOIN execparrainages EP ON EP.id_conversation = CU.id
						LEFT JOIN marchands M ON EP.id_marchand = M.id
						INNER JOIN (
							SELECT conversation_id, MAX(id) AS id
							FROM messagerie GROUP BY conversation_id
							) AS max ON (max.conversation_id = MES.conversation_id AND max.id = mes.id)
					WHERE MES.conversation_id IN (".$conversations_list['conversations_list'].") AND (CU.".$conversations_list['useris']." = 0)
					GROUP by conversation_id
					ORDER by MES.date DESC";

					$prep = $pdo->prepare($sql);
					$prep->execute();
					$GLOBALS['nb_req']++;
					$resultat = $prep->fetchAll(PDO::FETCH_ASSOC);

					if ($prep->rowcount() > 0) {
						$i = 0;
						$len = count($resultat);
						$notifa = 0;
						foreach($resultat as $result) {
						($result['id1'] == $_SESSION['membre_id'] ? $lu = $result['lu2'] :  $lu = $result['lu1']);
						($lu == 0 ? $notifa++ : '');  
						echo '<div class="box-messagerie-id" data-conv="'.$result['conversation_id'].'" data-id1="'.$result['mid1'].'" data-id2="'.$result['mid2'].'">	
								<div>
									<img class="avatar" style="width:80px;height:80px;" src="'.ROOTPATH.'/membres/images/'.($result['mid1'] == $_SESSION['membre_id'] ? (isset($result['image2']) ? $result['image2'] : "default_avatar.png") : (isset($result['image1']) ? $result['image1'] : "default_avatar.png")).'" />
								</div> 
								<div>
									<p style="font-weight:bold;margin-bottom:5px;">'.($result['mid1'] == $_SESSION['membre_id'] ? $result['nom2'] : $result['nom1']).'</p>
									<p>' . ($result['info_message'] == 1 ? 'Message système' : str_replace("<br />", '', mb_substr(strip_tags($result['message']), 0, 50))). '</p>
									<p style="font-size:11px;margin-bottom:0px;">'.mepd($result['date']).'</p>
								</div>
								<div class="newmessage" style="align-self: center;">'.(($lu == 0) ? '<i class="fas fa-plus fa-2x" style="color:#67B72D" ></i>' : '').'</div>
								<div><a href="?delete=ok&idc='.$result['conversation_id'].'" title="Supprimer la conversation" onclick="if(!confirm(\'Etes-vous sur de vouloir supprimer cette conversation ?\')) return false;" style="color:#CCC;align-self:start;justify-self:end;z-index:10;"><i class="fas fa-times fa-lg"></i></a></div>
									
							</div>';
							$i++;
							if($i == $len - 1 OR $len = 1) {
								echo '<a href="javascript:void(0);" style="display:none;" id="lastentry"  "></a>';
							}
						}
					} else $no = '<br /><div class="box_annonces"><p>Aucune conversation actuellement</p></div>';
				} else $no = '<br /><div class="box_annonces"><p>Aucune conversation actuellement</p></div>';
			
					?>
				
			</article>

			<article class="messagerieScrollMessages">
				

				<!-- CONVERSATION : -->
	<form action="<?= ROOTPATH.'/includes/upload-img.php'; ?>" method="post" name="image_upload" id="image_upload" enctype="multipart/form-data"> 
        <input type="file" style="display:none;" size="45" name="uploadfile" id="uploadfile" onchange="vpb_upload_and_resize();" />
	</form>

				<div id="repondre-fc" style="padding:10px 0 10px;">
						<form action="" method="POST" name="chatResponse" id="chatResponse" style="padding:10px;text-align:center;display:none;">
						<p style="margin: 10px 0 5px 5px;font-weight:bold;font-size:18px;">Message</p>
						
							<div contentEditable="true" class="liketextarea" style="max-width:90%;min-height:90px;display:inline-block;" id="msgrepdiv" name="msgrepdiv"></div>

							<textarea id="msgrep" name="msgrep" style="display:none;" ></textarea>
							<input type="text" style="display:none;" name="ca_idm" id="ca_idm" value="<?= (isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '0'); ?>" />
							<input type="text" style="display:none;" name="ca_idconvers" id="ca_idconvers" value="" />
							<input type="text" style="display:none;" name="ca_dest" id="ca_dest" value="" /><br />
							
							<div class="messagerioptions">
								
								<div style="display:inline-block;">
									<div class="pboutonr" id="upload_area" style="font-size: 16px; padding: 8px 15px 5px;margin-right:5px;"><i class="fas fa-image"></i> Image</div>
									<div class="dialog-open" style="padding:20px;">
									<button type="button" class="vpb-close"><i class="fas fa-times"></i></button><div class="dialog-open_inner"><div><label for="charg-img">Charger une image</label><div class="pboutonr" id="parcourir-img" style="min-width:210px;margin:10px 0;">Parcourir</div></div></div><div class="dialog-open_inner"><div><label for="adr-img">Image depuis une URL</label><input id="adr-imgtemp" name="urlimg" type="text" style="width:30%;margin:0;" placeholder="http://" <?= (isset($_SESSION['form']['adr-img']) ? 'value="'.$_SESSION['form']['adr-img'].'"' : ''); ?>><div class="pboutonr" id="parcourir-img" onclick="downloadimg();" style="min-width:210px;margin:10px 0;">Envoyer</div></div></div>
										</div>
								</div>
								<div style="display:inline-block;">
									<div class="pboutonr" id="emoticones" style="font-size: 16px; padding: 8px 15px 5px;margin-right:5px;"><i class="far fa-smile"></i> Emoticones</div>
									<div class="dialog-open" style="padding:20px;">
									<button type="button" class="vpb-close"><i class="fas fa-times"></i></button>
										<div class="emoji">
											<span>😀</span><span>😃</span><span>😄</span><span>😁</span><span>😆</span><span>😅</span><span>😂</span><span>🤣</span>
										</div>
										</div>
								</div>
								<button class="pboutonr" type="submit">Envoyer</button>
								
								<!-- EMOJI <div style="display:inline-block;">
								<div class="pboutonr" id="upload_emoji" style="font-size: 16px; padding: 8px 15px 5px;margin-right:5px;"><i class="fas fa-smile"></i> Emoji</div>
									<div class="dialog-open" style="padding:20px;">
										</div>
								</div>
								-->
										
							</div>
							

							
						</form>
				</div>
				<?= (isset($no) ? $no : '') ?>
			<div id="message-voir">
					
			</div>

			</article>
			</div>

</section>

<?php }
}
require_once '../elements/footer.php'; ?>

<script>
function timeConverter(UNIX_timestamp){
  var a = new Date(UNIX_timestamp * 1000);
  var months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
  var year = a.getFullYear();
  var month = months[a.getMonth()];
  var date = (a.getDate() < 10 ? '0' : '') + a.getDate();
  var hour = (a.getHours() < 10 ? '0' : '') + a.getHours();
  var min = (a.getMinutes() < 10 ? '0' : '') + a.getMinutes();
  var time = date + '/' + month + '/' + year + ' à ' + hour + 'h' + min;
  return time;
}

boxconv = document.getElementsByClassName('box-messagerie-id');
isIdentified = parseInt(window.location.hash.substr(1));

// On écoute si un clique sur une conversation et on lance la fonction voirmessage
for (let i = 0; i < boxconv.length; i++) {
	let element = boxconv[i]
	element.addEventListener('click', function() {
		id = element.dataset.conv
		idfrom = element.dataset.id1
		idto = element.dataset.id2
		removeclass();
		element.classList.add('conv-selected')

		voirmessage(id, idfrom, idto)
		window.history.pushState(null, '', 'messagerie#'+element.dataset.conv);
	})

	if(element.dataset.conv == isIdentified)
		{
			element.click();
			element.scrollIntoView()
		}
}
if(!Number.isInteger(isIdentified)){ 
		boxconv[0].click();
	}

function removeclass() 
{
	for (let i = 0; i < boxconv.length; i++) {
		const element = boxconv[i];
		element.classList.remove('conv-selected');
		
	}	
}

// CHARGEMENTS DES MESSAGES DE CONVERSATIONS 
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
				$('#message-voir').append('<div style="width:100%;padding: 5px 0 0;margin-left:-10px;"><a href="javascript:void(0);" id="brep" onclick="repondrefc();" class="pboutonr" style="float:right;margin-top:10px;">Répondre</a><img style="grid-row: 1; justify-content: space-between;width:90px;height:90px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+( msg[0].from_id == idmembre ?  msg[0].to_avat : msg[0].from_avat)+'" class=\"avatar\" /><div><a href="<?= ROOTPATH ?>/profil/'+( msg[0].from_id == idmembre ?  msg[0].to_id : msg[0].from_id)+'" style="margin:25px 0 15px 10px;font-weight:bold;font-size:20px;">'+msg[0].destinataire+'</a><p style="font-size:12px;">'+((msg[0].marchand == null) ? '' : 'Parrainage '+msg[0].marchand)+'</p></div></div>');
					if(idto == <?= $_SESSION['membre_id']; ?> ) 
						$("#ca_dest").val(idfrom);
					else
						$("#ca_dest").val(idto);
				$("#ca_idconvers").val(idconvers);
				
				$('#message-voir').append('<div id="liste-messages"></div>');
				
				if(msg.etat = "lu") {
				delete msg.etat
				$.each(msg, function(index, value) {

					if(value.info_message == 1) {
					data = '<div id="messageinfo"><span style="font-size:11px;">Le '+timeConverter(value.date)+' </span><p style="margin:0;padding:0;font-weight:bold;"><i style="vertical-align:middle;margin-right:5px;" class="fas fa-info-circle fa-2x"></i> '+value.message+'</p></div><hr />';
						
					} else {
					data = '<div style="display:grid;grid-template-columns:60px auto;grid-row: 1;margin:20px 0 20px 10px;"><img style="grid-row: 1; justify-content: space-between;width:50px;height:50px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+( value.from_id == idmembre ? ( value.from_id == idmembre ? value.to_avat : value.from_avat) : ( value.from_id == idmembre ? value.from_avat : value.to_avat))+'" class=\"avatar\" />';
					data += '<div style="grid-row: 1;grid-column: 2;align-self: center;"><p style="font-weight:bold;margin-bottom:5px;">'+(value.from_id == idmembre ? value.destinataire : value.pseudo_ecris)+'</p><p style="font-size:10px;">'+timeConverter(value.date)+'</p></div>';
					data += '<div style="grid-column: 1 / span 2;">';
					data += '<p>'+value.message+'</p>';
					data += '</div></div><hr />';
					}
					$('#liste-messages').append(data);
				});
				}
				if(msg.etat == "nofound") {
					$('#liste-messages').append('<p>Messages non trouvés, veuillez nous contacter pour nous signaler l\'erreur</p>');
			}
			}
		});
		$(".spinner, .valider, .erreur").delay(1000).fadeOut();

		return false;

	}
	
	//Lorsque l'on clique sur répondre, le formulaire de réponse apparait.
	function repondrefc() {
		$(".spinner, .valider, .erreur").delay(1000).fadeOut();
		var x = document.getElementById("chatResponse");
		var y = document.getElementById("brep");
		if (x.style.display === "none") {
			x.style.display = "block";
			y.style.display = "none";
			document.getElementById("msgrepdiv").focus();
		} else {
			x.style.display = "none";
		}
	}


	//AJAX REPONSE A UN MESSAGE : 
	$('#chatResponse').submit(function() {
		$(".spinner").show();
		var serialized = $(this).serialize(); 
		$('#chatResponse').css("display", "none");
		$("#repondre-fc").append('<div class="spinner" style="text-align:center;margin:20px 0 20px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" /></div>');
		$('#liste-messages').fadeOut();
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
					
					$('#msgrep').empty();
					$('#msgrepdiv').empty();
					
					data = '<div style="display:grid;grid-template-columns:60px auto;grid-row: 1;margin:20px 0 20px 10px;"><img style="grid-row: 1; justify-content: space-between;width:50px;height:50px;margin-bottom:20px;" src="<?=ROOTPATH ?>/membres/images/'+msg.info.from_avat+'" class=\"avatar\" />';
					data += '<div style="grid-row: 1;grid-column: 2;align-self: center;"><p style="font-weight:bold;margin-bottom:5px;">'+msg.info.pseudo_ecris+'</p><p style="font-size:10px;">'+timeConverter(msg.info.date)+'</p></div>';
					data += '<div style="grid-column: 1 / span 2;">';
					data += '<p>'+msg.info.message+'</p>';
					data += '</div></div><hr />';
	
					$('#liste-messages').prepend(data).fadeIn('slow');

				}
				
				else if (msg.erreurs == 'one') { // si la connexion en php a fonctionnée
					$('#repondre-fc').prepend('<div class="erreur">Il y a une erreur dans votre formulaire, merci de le corriger.</div>');
					
                    if(msg.info == 'emptymessage')
					$('#repondre-fc').prepend('<div class="erreur">Tous les champs sont obligatoires. Veuillez inscrire un message. </div>');
					
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
		$(".valider, .erreur").delay(1000).fadeOut();
		var y = document.getElementById("brep");
		y.style.display = "block";
		$(".spinner").fadeOut();
		return false;
		
	 });


//UPLOAD IMAGE
function vpb_upload_and_resize() {
$("#image_upload").vPB({
	dataType: "JSON",
	url: '<?= ROOTPATH; ?>/includes/upload-img.php',
	success: function(response) 
	{
		$("#msgrepdiv").append('<a href="<?=ROOTPATH;?>/membres/includes/uploads-img/800-'+response+'"><img src="<?=ROOTPATH;?>/membres/includes/uploads-img/120-'+response+'"></a>');
		$("#adr-img").val(response);
	}
}).submit(); 
}

let parcourirImg = document.getElementById('parcourir-img')
	parcourirImg.addEventListener('click', function() {
		document.getElementById('uploadfile').click()
	});

	//UPLOAD IMAGE PAR URL
	var msgrepdiv = document.querySelector("#msgrepdiv");
	var msgrep = document.getElementById("msgrep");
	
	msgrepdiv.addEventListener("DOMSubtreeModified", function() {
		var texte = msgrepdiv.innerHTML;
		msgrep.value = texte.replaceAll("<span>","").replaceAll("</span>","");
	});

	function downloadimg(){
		var urlimg = $('#adr-imgtemp').val();
		var img = $("<img />").attr('src', urlimg)
					$.ajax({
						method: "POST",
						url: "<?= ROOTPATH; ?>/includes/upload-img.php?messagerie=true",
						data: {urlimg:urlimg, messagerie:"true"},
						beforeSend:function() {
							$("#msgrepdiv").append('<div class="loading" style="padding:20px;min-width:230px;margin-top:25px;"><img src="<?= ROOTPATH; ?>/images/loading.gif" align="absmiddle" title="Envoi ..."/></div><br clear="all">');
						},
						success: function (response) {
							$(".loading").remove();
							
							$("#msgrepdiv").append('<a href="<?=ROOTPATH;?>/membres/includes/uploads-img/800-'+response+'"><img src="<?=ROOTPATH;?>/membres/includes/uploads-img/120-'+response+'"></a>');
						
						},
						error: function (response) {
							alert('error');
						}
					});
					
	}
	//AFFICHAGE IMAGES FORM ADR-IMG value rempli

	closemodal = document.getElementsByClassName('vpb-close')
	
	for (let divop of closemodal) {
		divop.addEventListener('click', function() {
			this.parentElement.style.display = "none"
			
		});
	}

	

	//CLICK SUR AJOUTER IMAGE -> APPARAIT BOITE DIALOGUE POUR PARCOURIR
	$(document).ready( function(){
		var nav = $('.dialog-open');
		
		$('#upload_area').click( function(event){
			event.stopPropagation();
			$(this).next().toggle().show();
		});

		$('#emoticones').click( function(event){
			event.stopPropagation();
			$(this).next().toggle().show();
		});
		$('.emoji span').click( function(event){
			event.stopPropagation();
			$("#msgrepdiv, #msgrep").append(this);
		});
	});

</script>
<script type="text/javascript" src="<?= ROOTPATH ?>/script/upload_img.js"></script>