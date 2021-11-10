<?php
require_once '../../includes/config.php';
require_once '../../includes/function.php';

$q = trim($_GET['q']);

$prep = $pdo->prepare("SELECT * FROM marchands WHERE nom_marchand LIKE '%".$q."%'");
$GLOBALS['nb_req']++;
$prep->execute();
$marchands = array();
while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
	$marchands[] = array(	'id' => $result['id'],
							'nom_marchand' => addslashes($result['nom_marchand']), 
							'image' => $result['img'],
							'url_marchand' => ROOTPATH.'/'.format_url(find_categorie($result['cat'])).'-'.$result['cat'].'/'.format_url($result['nom_marchand']).'-'.$result['id'].'/parrainage');
}

$results = array('marchands' => array());
foreach ($marchands as $name => $data)
{
	if (mb_stripos($data['nom_marchand'], $q) !== false)
	{
		$results['marchands'][$name] = $data;
	}
	
}
/* Get the data into a format that Smart Suggest will read (see documentation). */

$final = array('header' => array(), 'data' => array());
$final['header'] = array(
																		'title' => '',										# Appears at the top of this category
																		'num' => count($results),			# Displayed as the total number of results.
																		'limit' => 8																# An arbitrary number that you want to limit the results to.
																	);
foreach ($results['marchands'] as $name => $data)
{
	$final['data'][] = array(
														'primary' => $data['nom_marchand'],																						# Title of result row
														'secondary' => $data['url_marchand'],
														'image' => $data['image'],
														'id' => $data['id'],
														'url' => $data['url_marchand'],														# Description below title on result row																				# Optional URL of 40x40px image
														'onclick' => $data['url_marchand'],	# JavaScript to call when this result is clicked on
														'fill_text' => $data['nom_marchand']													# Used for "auto-complete fill style" example
													);
}

/* Output JSON */
header('Content-type: application/json');
echo json_encode(array($final));
die();
?>