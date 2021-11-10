</div></div>
	<div id="footer">
		<div class="container">
			 <div class="footer_column" style="min-width:180px">
	              	 <img src="<?= ROOTPATH; ?>/images/logo.png" alt="Promo Parrain" width="170px;" />
			   </div>
			   
	        <div class="footer_column">
				<h4 style="color:#CCC;">Parrainage</h4>
	        	<a title="Règles de Diffusions" href="<?= ROOTPATH.'/parrain/ajouter'; ?>">Ajouter une offre de parrainage</a><br />
	            <a title="Conditions Générales d'Utilisation" href="<?= ROOTPATH.'/mentions-legales.html'; ?>">Mentions légales</a><br />
	        </div>

	        <div class="footer_column">
	               <h5 style="color:#CCC;">Partenaires</h5>
	              	 <a href="<?= ROOTPATH.'/contact.html'; ?>">Proposez votre site</a>
	           </div>

	           <div class="footer_column">
	               <h5 style="color:#CCC;"><?= TITRESITE; ?></h5>
	               <a href="<?= ROOTPATH.'/contact.html'; ?>">Nous contacter</a><br />
	               <a href="#">Aide (Bientôt)</a>
			   </div>
			       
		</div>
		  <div class="basbas">
	 			<p>
	 			<?php $pdo = null; 
				$timeend=microtime(true);
				$time=$timeend-$timestart;
				$page_load_time = number_format($time, 3);
	          	 echo $GLOBALS['nb_req'].' requètes';
				 echo " éxecutées en " . $page_load_time . " sec";
				  ?><a href="https://www.xiti.com/xiti.asp?s=618569" title="WebAnalytics" target="_top">
				  <script type="text/javascript">
				  Xt_param = 's=618569&p=';
				  try {Xt_r = top.document.referrer;}
				  catch(e) {Xt_r = document.referrer; }
				  Xt_h = new Date();
				  Xt_i = '<img width="39" height="25" border="0" style="display:none;" alt="" ';
				  Xt_i += 'src="https://logv2.xiti.com/hit.xiti?'+Xt_param;
				  Xt_i += '&hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();
				  if(parseFloat(navigator.appVersion)>=4)
				  {Xt_s=screen;Xt_i+='&r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;}
				  document.write(Xt_i+'&ref='+Xt_r.replace(/[<>"]/g, '').replace(/&/g, '$')+'" title="Internet Audience">');
				  //-->
				  </script>
				  <noscript>
				  Mesure d'audience ROI statistique webanalytics par <img width="39" height="25" src="http://logv2.xiti.com/hit.xiti?s=618569&p=" alt="WebAnalytics" />
				  </noscript></a></p>
				   
	        </div>
   	
</div>
<script>
    $(document).ready(function() {
/*
		notification_show();

		// On affiche les notifications
$('#notif-view').click(function() {
$('.notificationMenuHeader').show();
});
function notification_show() {
    var data = {"idm" : <?= $_SESSION['membre_id']; ?> }
    $.ajax({
        type: "POST",
        url: "<?= ROOTPATH ?>/membres/includes/ajax-notif-voir.php",
        data: data,
        dataType: "JSON",
        success: function (response) {
            if(response.etat == 'ok') {
				if(response.data > 0) {
			$('.notificationMenuHeader').empty();
            $('.notificationMenuHeader').append(response.data);
				}
        }
            if(response.etat == 'error') {
                $('.notificationMenuHeader').append('Erreur');
        }
        }
    });
}

		setInterval(() => {
			notification_show();
		}, 10000);
	*/	
    	$('#searchinput').smartSuggest({
			src: '<?= ROOTPATH ?>/pages/include/recherche.php',
			executeCode: false
		});
    });
</script>
</body>
</html>