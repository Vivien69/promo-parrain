</div>
<div class="container">
	<div id="footer">
		<div class="footer_line">
					<img src="<?= ROOTPATH; ?>/images/logo.png" alt="Promo Parrain" />
		</div>
			
		<div class="footer_column">
			<h5 style="color:#CCC;">Communauté</h5>
			<a title="Ajouter un parrainage" href="<?= ROOTPATH.'/parrain/ajouter' ?>">Ajouter une offre de parrainage</a><br />
			<a title="Conditions Générales d'Utilisation" href="<?= ROOTPATH.'/regles-de-publication' ?>">Conditions Générales d'Utilisation</a><br />
			<a title="Foire Aux Questions" href="<?= ROOTPATH.'/parrain/ajouter' ?>">FAQ (bientôt)</a><br />
		</div>
		<div class="footer_column">
				<h5 style="color:#CCC;"><?= TITRESITE; ?></h5>
				<a title="Conditions Générales d'Utilisation" href="<?= ROOTPATH.'/mentions-legales'; ?>">Mentions légales</a><br />
				<a href="<?= ROOTPATH.'/contact' ?>">Contactez-nous</a>
		</div>
		<div class="footer_column">
				<h5 style="color:#CCC;">Partenaires</h5>
					<a href="<?= ROOTPATH.'/contact' ?>">Proposez votre site</a>
		</div>
		<div class="footer_column">
				<h5 style="color:#CCC;">Suivez-nous</h5>
				<a href=""><i class="fab fa-facebook-square fa-3x"></i></a>
				<a href="https://twitter.com/PromoParrain" title="Twitter"><i class="fab fa-twitter-square fa-3x"></i></a>
				<a href=""><i class="fab fa-instagram-square fa-3x"></i></a>
				<a href=""><i class="fab fa-linkedin fa-3x"></i></a>
		</div>
		<div class="footer_lineend">
			<p>
			<?php $pdo = null; 
			$timeend=microtime(true);
			$time=$timeend-$timestart;
			$page_load_time = number_format($time, 3);
				echo $GLOBALS['nb_req'].' requètes';
				echo " éxecutées en " . $page_load_time . " sec";
				?></p>
		</div>
	
	</div>
</div>
<script async src="<?= ROOTPATH ?>/elements/includes/notifajax.js"></script>
<script src="<?= ROOTPATH; ?>/script/jquery.smartsuggest-simple.js"></script>
<?php
if(isset($_SESSION['membre_id'])) : ?>
<script>
var notifloadcontent = document.getElementById('notifload-content');
var messageriecontent = document.getElementById('messagerie-content');
var nav = document.getElementsByClassName('dialog-open');
var dropdownmenu = document.getElementsByClassName('dropdownmenu');

document.addEventListener('click', function(e) {
	
for (let item of nav) {	
	hideOnClickOutside(item)
	}
});

function hideOnClickOutside(element) {
    const outsideClickListener = event => {
        if (!element.contains(event.target) && isVisible(element)) { // or use: event.target.closest(selector) === null
          element.style.display = 'none'
          removeClickListener()
        }
    }

    const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener)
    }

    document.addEventListener('click', outsideClickListener)
}

const isVisible = elem => !!elem && !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length ) // source (2018-03-11): https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js 

let limit = 5;
let startn =  0;
let startm =  0;
let action = 'active';

for (let i = 0; i < dropdownmenu.length; i++) {
	
	dropdownmenu[i].addEventListener('click', function(e) {

		e.stopPropagation();

		if(this.nextElementSibling.style.display == "block") {
			this.nextElementSibling.style.display = "none";
			this.nextElementSibling.scrollTop
			
		} else {
			this.nextElementSibling.style.display = "block";


			//Si le menu est fermé, on l'ouvre et on charge les données
			if(dropdownmenu[i].id === 'notifload') {
				if(notifloadcontent.childNodes.length === 0){
				notifAjaxData(limit,startn, <?= isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '' ?>);
				action = 'inactive';
				}
			}
			if(dropdownmenu[i].id === 'messagerieload') {
				if(messageriecontent.childNodes.length === 0){
				MessagerieAjaxData(limit,startm, <?= isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '' ?>);
				action = 'inactive';
			}

		}

		for (let item of nav) {

			if(item !=  this.nextElementSibling) {
				item.style.display = "none";
			}
		}
	}
		
		
	});
}

[notifloadcontent, messageriecontent].forEach(element => {

	element.addEventListener('scroll',function(e){
	
	if((element.scrollHeight - element.scrollTop) < (element.clientHeight + 1) && action == 'inactive')
	{
		((element == notifloadcontent) ? startn = startn + limit : startm = startm + limit)

		
		setTimeout(function() {
			((element == notifloadcontent) ? notifAjaxData(limit, startn, <?= isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '' ?>) : MessagerieAjaxData(limit,startm, <?= isset($_SESSION['membre_id']) ? $_SESSION['membre_id'] : '' ?>))
		}, 500)
	}
    });
	
});
</script>

<?php
endif ?>
<script>
$(document).ready(function() {
	
	$('#searchinput').smartSuggest({
		src: '<?= ROOTPATH ?>/pages/include/recherche.php',
		executeCode: false
	});
	$('.searchicone').click(function(e) {
		$('.input-iconsearch').hide();
		$('.searchbox').slideDown();
		$('#searchinput').focus();
		$('.input-iconsearch').slideDown();
			
			e.preventDefault();
		});
});
</script>
</body>
</html>