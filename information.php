<?php
if(isset($informations[1])) {
	$titre = $informations[1];
} else { $titre = 'Erreur'; }
if(!isset($informations))
{
	$informations = Array(/*Erreur*/
		true,
		'Erreur',
		'Une erreur interne est survenue...',
		'',
		'index.php',
		3
	);
}

if($informations[0] === true) $type = 'erreurinfo';
else $type = 'validinfo';
header('Refresh: '.$informations[5].';URL='.$informations[4].' ');
?>
	<div class="block_inside">
        <h1 class="iconic alaune"> <?php if(isset($informations[1])) echo $informations[1]; else echo 'Erreur'; ?></h1>
            <div class="<?php echo $type; ?>"><?php echo $informations[2]; ?><br /><br />Redirection en cours...<br/><br/>
            <a href="<?php echo $informations[4]; ?>">Cliquez ici si vous ne voulez pas attendre...</a><?php echo $informations[3]; ?></div>
    </div>
<?php
require_once 'elements/footer.php';
unset($informations);