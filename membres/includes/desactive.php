<?php
include_once '../../includes/config.php';
include_once '../../includes/function.php';
//DESACTIVER UNE ANNONCE OU REACTIVER
		if (isset($_GET["desactive"]) && is_numeric($_GET["desactive"])) {
			$id = intval($_GET["desactive"]);
			
			if(isset($_SESSION['admin_co']) && $_SESSION['admin_co'] = 'connecter') 
			$sql = "SELECT AP.etatvalidation, M.actif FROM annonces_parrainage AP
			LEFT JOIN marchands M ON AP.idmarchand = M.id
			WHERE AP.id = :id";
			else
			$sql = "SELECT AP.etatvalidation, M.actif FROM annonces_parrainage AP
			LEFT JOIN marchands M ON AP.idmarchand = M.id
			WHERE AP.id = :id AND AP.idmembre = ".$_SESSION['membre_id'];

			$prepa = $pdo->prepare($sql);
			$prepa->execute(array(":id" => $id));
			$GLOBALS['nb_req']++;

			if ($prepa->rowcount() == 1) {
				$row = $prepa->fetch();

				if($row['actif'] == 1) {
					($row['etatvalidation'] == 1 ? $val = 2 : $val = 1);
					$sql = $pdo->prepare("UPDATE annonces_parrainage SET etatvalidation = $val WHERE id = " . $id);
					$GLOBALS['nb_req']++;
				}
				
				
				if ($sql->execute()) {
					if($val == 1)
					echo '{"status":"desactivate"}';
					else
					echo '{"status":"activate"}';
				}

			} else {
				echo '{"status":"error"}';
			}
		}