<?php

if (!isset($response)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

//----------------------------------------------------- LOGO
if (isset($_FILES['logo'])){
	
	//----------- empty file -----------
	if (empty($_FILES['logo']['name'])){
		$response[] = "Nessun file LOGO in upload!";
		goto end_logo_upload;
	}
	
	//----------- multiple files -----------
	//something went wrong with <form> upload
	if (is_array($_FILES['logo']['name'])){
		die("File multipli non consentiti");
		goto end_logo_upload;
	}
	
	//----------- single file -----------
	// loop all files
	
	//errors log...
	if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK){
		include_once "../../../php/mbc-filemanager/classes/uploadexception.class.php";
		die($UploadExceptionErrors[ $_FILES['logo']['error'] ]);
	}
	
	// if file not uploaded then skip it
	if ( !is_uploaded_file($_FILES['logo']['tmp_name']) )
		die("File wasn't uploaded.".$_FILES['logo']['tmp_name']);
	
	//max 5 Mb
	if ($_FILES['logo']['size'] > 5e6)
		die("Il file è troppo grande. Puoi caricare al massimo 5 Mb!");
	
	
	
	$ext = mb_strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
	if (!in_array($ext,['jpg','jpeg','png','gif','bmp']))
		die("Tipo di file non permesso.");
	
	
	$user_path = '../img/';
	$user_filename = 'logo.png';//add 256, 128, 64, 32  +  .png
	
	if (!check_and_create_path($user_path))
		die("Impossibile creare cartella per il file");
	
	
	$temp_file = $user_path . mt_rand().'.'.$ext;
	
	if( move_uploaded_file($_FILES['logo']['tmp_name'], $temp_file) ){
		
		//handle thumbnails
		if ($imgsize = getimagesize($temp_file)){
			
			require_once "../../../php/php-image-magician/php_image_magician.php";
			
			//if image sizes are <= 140x140 save as png
			if ($imgsize[0]<=140 && $imgsize[1]<=140){
				if ($ext=='png'){
					copy($temp_file, $user_path . $user_filename);
				}
				else{
					$magicianObj = new imageLib($temp_file);
					$magicianObj->saveImage( $user_path . $user_filename, 20);
				}
			}
			else{
				createThumbnail(
					$temp_file,
					$user_path,
					$user_filename,
					//png compression 2 step -> quality = 20
					[
						'w' => 140,
						'h' => 140,
						'resize' => 'auto',
						'quality' => 20
					]);
			}
			unlink($temp_file);
			$response[] = "Logo aggiornato";
		}
		else{
			unlink($temp_file);
			die('File non permesso');
		}
	}
	else
		die('Impossibile spostare il file.');
}
else
	$response[] = "Nessun nuovo logo caricato.";
//----------------------------------------------------- FINE LOGO

end_logo_upload:

?>