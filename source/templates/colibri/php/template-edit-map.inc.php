<?php

if (!isset($response)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

//----------------------------------------------------- MAPPA
if (isset($_FILES['map'])){

	//----------- empty file -----------
	if (empty($_FILES['map']['name'])){
		$response[] = "Nessun file MAP in upload!";
		goto end_map_upload;
	}
	
	//----------- multiple files -----------
	//something went wrong with <form> upload
	if (is_array($_FILES['map']['name'])){
		jsonError("File multipli non consentiti");
		goto end_map_upload;
	}
	
	//----------- single file -----------
	// loop all files
	
	//errors log...
	if ($_FILES['map']['error'] !== UPLOAD_ERR_OK){
		include_once "../../../php/mbc-filemanager/classes/uploadexception.class.php";
		jsonError($UploadExceptionErrors[ $_FILES['map']['error'] ]);
	}
	
	// if file not uploaded then skip it
	if ( !is_uploaded_file($_FILES['map']['tmp_name']) )
		jsonError("File wasn't uploaded.".$_FILES['map']['tmp_name']);
	
	//max 5 Mb
	if ($_FILES['map']['size'] > 5e6)
		jsonError("Il file è troppo grande. Puoi caricare al massimo 5 Mb!");
	
	
	
	$ext = mb_strtolower(pathinfo($_FILES['map']['name'], PATHINFO_EXTENSION));
	if (!in_array($ext,['jpg','jpeg','png','gif','bmp']))
		jsonError("Tipo di file non permesso.");
	
	
	$user_path = '../img/';
	$user_filename = 'map';//add 1366, 800, 520*
	
	if (!check_and_create_path($user_path))
		jsonError("Impossibile creare cartella per il file");
	
	$temp_file = $user_path . mt_rand().'.'.$ext;
	
	if( move_uploaded_file($_FILES['map']['tmp_name'], $temp_file) ){
		
		//handle thumbnails
		if ($imgsize = getimagesize($temp_file)){
			
			require_once "../../../php/php-image-magician/php_image_magician.php";
			
			$filemediaquery = [true, false, false];
			$sizes = [1366, 800, 520];
			$fileheight = min($imgsize[1],520);
			
			//main map background must exist:
			createThumbnail($temp_file, $user_path, $user_filename.'-'.$sizes[0].'.'.$ext,
			[
				'w' => $imgsize[0],
				'h' => $fileheight,
				'resize' => 'crop',
				'quality' => $ext == 'png' ? 50 : 90
			]);
			//if w > 800 px create 800 and 520 sizes
			if ($imgsize[0]>800){
				$filemediaquery = [true, true, true];
				createThumbnail($temp_file, $user_path, $user_filename.'-'.$sizes[1].'.'.$ext,
				[
					'w' => $sizes[1],
					'h' => $fileheight,
					'resize' => 'crop',
					'quality' => $ext == 'png' ? 40 : 86
				]);
				createThumbnail($temp_file, $user_path, $user_filename.'-'.$sizes[2].'.'.$ext,
				[
					'w' => $sizes[2],
					'h' => $fileheight,
					'resize' => 'crop',
					'quality' => $ext == 'png' ? 20 : 80
				]);
			}
			//if 800 > w > 520 px create 520 sizes
			elseif($imgsize[0]>520){
				$filemediaquery = [true, false, true];
				createThumbnail($temp_file, $user_path, $user_filename.'-'.$sizes[2].'.'.$ext,
				[
					'w' => $sizes[2],
					'h' => $fileheight,
					'resize' => 'crop',
					'quality' => $ext == 'png' ? 20 : 95
				]);
			}
			
			unlink($temp_file);
			
			//--------------------------------------------
			//queue update css...
			$cssupdates['map'] = [
				'map_'.$sizes[0] => $filemediaquery[0],
				'map_'.$sizes[1] => $filemediaquery[1],
				'map_'.$sizes[2] => $filemediaquery[2],
				'height' => $fileheight,
				'ext' => $ext
			];
			//--------------------------------------------
			
			$response[] = "Mappa aggiornata";
		}
		else{
			unlink($temp_file);
			jsonError('File non permesso');
		}
	}
	else
		jsonError('Impossibile spostare il file.');
}
else
	$response[] = "Nessuna nuova mappa caricata.";
//----------------------------------------------------- FINE MAPPA

end_map_upload:

?>