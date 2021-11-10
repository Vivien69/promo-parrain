<?php
require_once '../includes/config.php';
require_once '../includes/function.php';
require_once '../elements/header.php';
$title = 'Categories d\'offres de parrainage';
?>
<meta name="description" content="Liste des catégories d'offre de code promo de parrainage sur Promo-parrain. Trouvez votre magasin en ligne favoris">
<meta name="keywords" content="code, promo, parrain, parrainage, catégories, filleul">
<link rel="canonical" href="https://www.promo-parrain.com/categories" />
<?php
require_once '../elements/header2.php';

?>

<section class="block_inside">
    <article id="categories_de_parrainage">

        <h1>Liste des parrains par catégories</h1>
        <div class="categories-presentation-mere">
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/alimentation-supermarche-1"><i class="fas fa-pepper-hot fa-3x"></i>Alimentation-Supermarché</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/animaux-2"><i class="fas fa-paw fa-3x"></i>Animaux</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/assurance-mutuelle-3"><i class="fas fa-hand-holding-medical fa-3x"></i>Assurance-Mutuelles</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/auto-moto-4"><i class="fas fa-motorcycle fa-3x"></i>Auto-Moto</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/banques-5"><i class="fas fa-money-check fa-3x"></i>Banques</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/beaute-sante-6"><i class="fas fa-air-freshener fa-3x"></i>Beauté-Santé</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/bijoux-accessoires-7"><i class="fas fa-gem fa-3x"></i>Bijoux-Accessoires</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/cadeaux-bijoux-8"><i class="fas fa-gift fa-3x"></i>Cadeaux-Box</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/cashback-9"><i class="fas fa-money-bill-wave fa-3x"></i>Cashback</a></div>
				<div class="categories-presentation"><a href="<?=ROOTPATH ?>/cd-dvd-livres-10"><i class="fas fa-book fa-3x"></i>CD-DVD-Livres</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/chaussures-11"><i class="fas fa-shoe-prints fa-3x"></i>Chaussures</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/cryptomonnaies-33"><i class="fab fa-bitcoin fa-3x"></i>Cryptomonnaies</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/decoration-12"><i class="fas fa-couch fa-3x"></i>Décoration</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/energies-bois-electricite-gaz-13"><i class="fas fa-bolt fa-3x"></i>Energies-Bois-Electricité-Gaz</a></div>
				<div class="categories-presentation"><a href="<?=ROOTPATH ?>/enfants-bebes-jouets-14"><i class="fas fa-baby fa-3x"></i>Enfants-Bébés-Jouets</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/generaliste-vente-32"><i class="fas fa-shopping-cart fa-3x"></i>Généralistes-Vente</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/internet-hebergement-vpn-15"><i class="fas fa-network-wired fa-3x"></i>Internet-Hébergement-VPN</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/investissement-16"><i class="fas fa-piggy-bank fa-3x"></i>Investissement</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/jardin-fleurs-17"><i class="fas fa-leaf fa-3x"></i>Jardin-Fleurs</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/jeux-video-18"><i class="fas fa-gamepad fa-3x"></i>Jeux-vidéo</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/jeux-dargent-19"><i class="fas fa-dice fa-3x"></i>Jeux d'Argent</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/loisirs-voyages-20"><i class="fas fa-plane fa-3x"></i>Loisirs-Voyages</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/matelas-literie-21"><i class="fas fa-bed fa-3x"></i>Matelas-Literie</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/maison-bricolage-22"><i class="fas fa-tools fa-3x"></i>Maison-Bricolage</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/missions-sondages-23"><i class="fas fa-poll-h fa-3x"></i>Missions-Sondages</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/multimedia-electromenager-24"><i class="fas fa-laptop fa-3x"></i>Multimédia-Electroménager</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/mode-vetements-25"><i class="fas fa-tshirt fa-3x"></i>Mode-Vêtements</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/operateursinternet-telephone-26"><i class="fas fa-signal fa-3x"></i>Opérateurs internet-Téléphone</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/optique-27"><i class="fas fa-glasses fa-3x"></i>Optique</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/photo-impression-28"><i class="fas fa-photo-video fa-3x"></i>Photo-Impression</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/rencontre-29"><i class="fas fa-heart fa-3x"></i>Rencontre</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/sport-30"><i class="fas fa-football-ball fa-3x"></i>Sport</a></div>
                <div class="categories-presentation"><a href="<?=ROOTPATH ?>/autre-31"><i class="fas fa-globe-europe fa-3x"></i>Autre</a></div>
    </div>
                <br /><br /><br />
<a class="pboutonr" href="<?= ROOTPATH ?>/liste-marchands" title="Voir tous les marchands">Voir tous les marchands</a>
    </article>
</section>
<?php  require_once '../elements/footer.php'; ?>