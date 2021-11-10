<?php
/**************************************************************
* This script is brought to you by Vasplus Programming Blog
* Website: www.vasplus.info
* Email: info@vasplus.info
****************************************************************/
require_once 'config.php';
require_once 'function.php';
	// You may change any of the below information if you wish
	if(isset($_POST['messagerie']) && $_POST['messagerie']) {
	$vpb_upload_image_directory = "../membres/includes/uploads-messagerie/";
	$vpb_with_of_first_image_file = 1500;      // You may adjust the width here as you wish
	$vpb_with_of_second_image_file = 400;     // You may adjust the height here as you wish
	} else {
	$vpb_upload_image_directory = "../membres/includes/uploads-img/";
	$vpb_with_of_first_image_file = 1000;      // You may adjust the width here as you wish
	$vpb_with_of_second_image_file = 140;     // You may adjust the height here as you wish
	}
	$uniquename = uniqid(rand(),true);
	/* Variables Declaration and Assignments */
	if(isset($_POST['urlimg'])) {
		if(filter_var($_POST['urlimg'], FILTER_VALIDATE_URL)) 
		{
			$pathinfo = pathinfo($_POST['urlimg']);
			$allowed_extension = array("jpg", "png", "jpeg", "gif", "bmp");
			$image_name = $pathinfo['basename'];
			$extension = $pathinfo['extension'];

				//On vérifie que l'extension soit autorisée
			if(in_array($extension, $allowed_extension))
			{
				ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 
				$image_data = file_get_contents($_POST["urlimg"], false, stream_context_create(['http' => ['ignore_errors' => true]]));
				$rand = rand();
				$tmp_name = "../membres/includes/uploads-img/" . $rand . "." . $extension;
				$vpb_image_tmp_name = file_put_contents($tmp_name, $image_data);
			}
		$vpb_image_tmp_name = "../membres/includes/uploads-img/" . $rand . "." . $extension;
		$vpb_image_filename = str_replace(' ', '-',urldecode($image_name));
		$vpb_file_size = filesize($tmp_name);
		$vpb_file_extensions = exif_imagetype($tmp_name);
	} 
} else {
	$vpb_image_filename = str_replace(' ', '-',urldecode($_FILES['uploadfile']['name']));
	$vpb_image_tmp_name = $_FILES['uploadfile']['tmp_name'];
	$vpb_file_size = filesize($_FILES['uploadfile']['tmp_name']);
	$vpb_file_extensions = exif_imagetype($_FILES['uploadfile']['tmp_name']);
}
	
	
	//Validate file upload field to be sure that the user attached a file and did not upload an empty field to proceed
  	if(!isset($vpb_image_filename) OR $vpb_image_filename == "") 
  	{
		echo '<div class="erreur" style="padding:20px;">Merci de sélectionner un fichier</div>';
	}
	else
	{
	$vpb_maximum_allowed_file_size = 4000*4000; // You may change the maximum allowed upload file size here if you wish
	$vpb_additional_file_size = $vpb_file_size - $vpb_maximum_allowed_file_size;
		//Validate attached file for allowed file extension types
        if ($vpb_file_extensions != 1 && $vpb_file_extensions != 2 && $vpb_file_extensions != 3 && $vpb_file_extensions != 6) 
  		{
			echo '<div class="erreur" style="padding:20px;">Le fichier que vous essayez d\'envoyer n\'est pas accepté. Essayez en un autre.</div>';
  		}
		elseif ($vpb_file_size > $vpb_maximum_allowed_file_size) //Validate attached file to avoid large files
		{
			echo "<div class='erreur' style='padding:20px;'>La limite de taille est de <b>".$vpb_maximum_allowed_file_size."</b> par <b>".$vpb_additional_file_size."</b><br>Merci de redimensionner votre image.</div>";
		}
 		else
		{
			/* Create images based on their file types */
			if($vpb_file_extensions == 1) //If the attached file extension is a gif, carry out the below action
			{
				$vpb_image_src = imagecreatefromgif($vpb_image_tmp_name); //This will create a gif image file
			}
			elseif($vpb_file_extensions == 2) //If the attached file is a jpg or jpeg, carry out the below action
			{
				$vpb_image_src = imagecreatefromjpeg($vpb_image_tmp_name); //This will create a jpg or jpeg image file
			}
			else if($vpb_file_extensions== 3) //If the attached file extension is a png, carry out the below action
			{
				$vpb_image_src = imagecreatefrompng($vpb_image_tmp_name); //This will create a png image file
			}
			else if($vpb_file_extensions== 6) //If the attached file extension is a bmp, carry out the below action
			{
				$vpb_image_src = imagecreatefrombmp($vpb_image_tmp_name); //This will create a bmp image file
			}
			else
			{
				$vpb_image_src = "invalid_file_type_realized";
			}
			
			//The file attached is unknow
			if($vpb_image_src == "invalid_file_type_realized")
			{
				echo '<div class="info">Désolé le fichier est invalide. <br>Fichiers acceptés : gif, jpg, png ou bmp<br> Votre format de fichier envoyé : <b>'.$vpb_file_extensions.'</b>.</div>';
			}
			else
			{
				//Get the size of the attached image file from where the resize process will take place from the width and height of the image
				list($vpb_image_width,$vpb_image_height) = getimagesize($vpb_image_tmp_name);
				
				/* The uploaded image file is supposed to be just one image file but 
				   we are going to split the uploaded image file into two images with different sizes for demonstration purpose and that process 
				   starts from below */
				   
				   
				//This is the width of the first image file from where its height will be determined
				$vpb_first_image_new_width = $vpb_with_of_first_image_file; 
				$vpb_first_image_new_height = ($vpb_image_height/$vpb_image_width)*$vpb_first_image_new_width;
				$vpb_first_image_tmp = imagecreatetruecolor($vpb_first_image_new_width,$vpb_first_image_new_height);
				switch ($vpb_file_extensions) {

					case '1':
					case '3':
						// integer representation of the color black (rgb: 0,0,0)
						imagefill($vpb_first_image_tmp, 0, 0, imagecolorallocate($vpb_first_image_tmp, 255, 255, 255));  // white background;
						break;
				
					default:
						break;
				}
				// end changes
				
				
				//This is the width of the second image file from where its height will be determined
				$vpb_second_image_new_width = $vpb_with_of_second_image_file; 
				$vpb_second_image_new_height = ($vpb_image_height/$vpb_image_width)*$vpb_second_image_new_width;
				$vpb_second_image_tmp = imagecreatetruecolor($vpb_second_image_new_width,$vpb_second_image_new_height);
				switch ($vpb_file_extensions) {

					case '1':
					case '3':
						imagefill($vpb_second_image_tmp, 0, 0, imagecolorallocate($vpb_second_image_tmp, 255, 255, 255));  // white background;
					
						break;
				
					default:
						break;
				}
				// end changes
				
				//Resize the first image file
				imagecopyresampled($vpb_first_image_tmp,$vpb_image_src,0,0,0,0,$vpb_first_image_new_width,$vpb_first_image_new_height,$vpb_image_width,$vpb_image_height); 
				
				//Resize the second image file
				imagecopyresampled($vpb_second_image_tmp,$vpb_image_src,0,0,0,0,$vpb_second_image_new_width,$vpb_second_image_new_height, $vpb_image_width,$vpb_image_height);
				
				//Pass the attached file to the uploads directory for the first image file
				$vpb_uploaded_file_movement_one = $vpb_upload_image_directory."800-$uniquename".$vpb_image_filename;
				
				//Pass the attached file to the uploads directory for the second image file
				$vpb_uploaded_file_movement_two = $vpb_upload_image_directory."120-$uniquename".$vpb_image_filename;
				
				//Upload the first and second images
				imagejpeg($vpb_first_image_tmp,$vpb_uploaded_file_movement_one,90);
				imagejpeg($vpb_second_image_tmp,$vpb_uploaded_file_movement_two,90);
	
				imagedestroy($vpb_image_src);
				imagedestroy($vpb_first_image_tmp);
				imagedestroy($vpb_second_image_tmp);
				
				echo $uniquename.$vpb_image_filename;
				if(isset($_POST['urlimg']))
					unlink($tmp_name);
				
			}
			//<span class="vpb_image_style"><img src="'.$vpb_uploaded_file_movement_two.'"></span>
		}
	}
?>