<?php echo '<?xml version="1.0" encoding="UTF-8" ?>';

// En-tete http
header("Content-Type: text/xml;charset=utf-8");

require_once 'config.php';
require_once 'function.php';

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

 <url>
  <loc>https://www.promo-parrain.com/</loc>
  <lastmod><?= date('Y-m-d', time() - 96400) ?></lastmod>
 </url>
 <url>
  <loc>https://www.promo-parrain.com/parrainages.html</loc>
  <lastmod><?= date('Y-m-d', time() - 4600) ?></lastmod>
 </url>
 <url>
  <loc>https://www.promo-parrain.com/categories</loc>
  <lastmod>2021-04-03</lastmod>
 </url>
 <url>
  <loc>https://www.promo-parrain.com/liste-marchands</loc>
  <lastmod><?= date('Y-m-d', time() - 96400) ?></lastmod>
 </url>
 <url>
  <loc>https://www.promo-parrain.com/contact.html</loc>
  <lastmod>2021-04-03T15:00:15+00:00</lastmod>
 </url>
 <url>
  <loc>https://www.promo-parrain.com/parrain/ajouter</loc>
  <lastmod><?= date('Y-m-d', time()- 21400) ?></lastmod>
 </url>
<?php



//On liste les pages de catégories principales
$sql = $pdo->prepare('SELECT * FROM categories_principales');
$sql->execute();
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    echo '<url>';
    echo '<loc>'.ROOTPATH.'/'.format_url($row['nom_categorie']).'-'.$row['id'].'</loc>';
    echo '<lastmod>2021-04-03</lastmod>';
    echo '</url>';
}
//On liste les pages des marchands
$sql = $pdo->prepare('SELECT id as idmarchand, cat, nom_marchand FROM marchands');
$sql->execute();
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    echo '<url>';
    echo '<loc>'.ROOTPATH.'/'.format_url(find_categorie($row['cat'])).'-'.$row['cat'].'/'.format_url($row['nom_marchand']).'-'.$row['idmarchand'].'/parrainage</loc>';
    echo '<lastmod>2021-04-03</lastmod>';
    echo '</url>';
}
//On liste les pages des annonces
$sql = $pdo->prepare('SELECT AP.id, AP.idmarchand, AP.dateajout, M.nom_marchand FROM annonces_parrainage AP
LEFT JOIN marchands M ON AP.idmarchand = M.id');
$sql->execute();
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    echo '<url>';
    echo '<loc>' . ROOTPATH . '/parrain/' . format_url($row['nom_marchand']) . '-' . $row['id'] . '</loc>';
    echo '<lastmod>'.date('Y-m-d', $row['dateajout']).'</lastmod>';
    echo '</url>';
}
?>
</urlset>