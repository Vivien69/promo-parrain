<?php
require 'config.php';
include 'function.php';

/* Get the query string "q" variable -- this is what the user typed in. */

/* Run some sort of searching operation.
			- Usually you will be searching a database and then pulling back results.
			- In this case, we're going to just use a simple array as our data source and find any matches. */
$arrayy = array(' ','É','È','Ê','Ë','é','è','ê','ë','Â','Ä','À','à','â','ç','Ô','Ö','ô','ö','Î','Ï','î','ï','Û','Ü','Ù','û','ü','ù');
$arrayyy = array('-','E','E','E','E','e','e','e','e','A','A','A','a','a','c','O','O','o','o','I','I','i','i','U','U','U','u','u','u');
$q = str_replace($arrayy, $arrayyy,addslashes($_GET['q']));
$prep = $pdo->prepare("SELECT ville_nom_reel,ville_code_postal FROM villes_france WHERE ville_nom_reel LIKE '".$q."%'");
$GLOBALS['nb_req']++;
$prep->execute();
$ville = array();
while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
							$ville[] = array('name' => str_replace($arrayy, $arrayyy, addslashes($result['ville_nom_reel'])),'namepropre' => $result['ville_nom_reel'], 'dep' => $result['ville_code_postal']);
	
								
}

$prep2 = $pdo->prepare("SELECT ville_nom_reel,ville_code_postal FROM villes_france WHERE ville_code_postal LIKE '".$q."%'");
$GLOBALS['nb_req']++;
$prep2->execute();
$codepostal = array();
while ($result2 = $prep2->fetch(PDO::FETCH_ASSOC)) {
								$codepostal[] = array('name' => $result2['ville_code_postal'],  'dep' =>  addslashes($result2['ville_nom_reel']));
								
}

$results = array('ville' => array(), 'codepostal' => array());
foreach ($ville as $name => $data)
{
	if (mb_stripos($data['name'], $q) !== false)
	{
		$results['ville'][$name] = $data;
	}
	
}
foreach ($codepostal as $name => $data)
{
	if (mb_stripos($data['name'], $q) !== false)
	{
		$results['codepostal'][$name] = $data;
	}
}
/* Get the data into a format that Smart Suggest will read (see documentation). */

$final_ville = array('header' => array(), 'data' => array());
$final_ville['header'] = array(
																		'title' => 'Ville',										# Appears at the top of this category
																		'num' => count($results['ville']),			# Displayed as the total number of results.
																		'limit' => 8																# An arbitrary number that you want to limit the results to.
																	);
foreach ($results['ville'] as $name => $data)
{
	$final_ville['data'][] = array(
														'primary' => $data['namepropre'],																								# Title of result row
														'secondary' => $data['dep'],																# Description below title on result row																				# Optional URL of 40x40px image
														'onclick' => 'alert(\'You clicked on the '.$data['name'].' ville!\');',	# JavaScript to call when this result is clicked on
														'fill_text' => $data['name']													# Used for "auto-complete fill style" example
													);
}

$final_codepostal = array('header' => array(), 'data' => array());
$final_codepostal['header'] = array(
																		'title' => 'codepostal',										# Appears at the top of this category
																		'num' => count($results['codepostal']),			# Displayed as the total number of results.
																		'limit' => 8																# An arbitrary number that you want to limit the results to.
																	);
foreach ($results['codepostal'] as $name => $data)
{
	$final_codepostal['data'][] = array(
														'primary' => $data['dep'],																									# Title of result row
														'secondary' => $data['name'],																# Description below title on result row																					# Optional URL of 40x40px image
														'onclick' => 'alert(\'You clicked on the '.$data['name'].' codepostal!\');',	# JavaScript to call when this result is clicked on
														'fill_text' => $data['dep']																		# Used for "auto-complete fill style" example
													);
}
/* Output JSON */
$final = array($final_ville, $final_codepostal);
header('Content-type: application/json');
echo json_encode($final);
die();
?>