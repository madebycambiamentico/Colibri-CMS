<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(0,true);


if (isset($_GET['remove'])) goto anchor_remove;


function check_and_create_path($path){
	if (!file_exists($path)){
		if (!mkdir($path,0755,true)) return false;
	}
	elseif (!is_writable($path) || !is_dir($path))
		return false;
	return true;
}

function createThumbnail($source, $path, $filename, $MOParams){
	$magicianObj = new imageLib($source);
	//rezized image...
	$magicianObj->resizeImage($MOParams['w'], $MOParams['h'], $MOParams['resize'], true);//true -> sharpening
	//create thumbnail:
	// - control if directory exists - or write it
	if (!check_and_create_path($path))
		return false;
	//png 2 step compression
	$magicianObj->saveImage( $path . $filename, 20);
	return true;
}





//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//NB - no MIME check... TODO...
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!





if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])){
	
	//----------- empty file -----------
	if (empty($_FILES['file']['name'])){
		jsonError("Nessun file in upload!");
	}
	
	//----------- multiple files -----------
	//something went wrong with <form> upload
	if (is_array($_FILES['file']['name'])){
		jsonError("File multipli non consentiti");
	}
	
	//----------- single file -----------
	// loop all files
	
	//errors log...
	if ($_FILES['file']['error'] !== UPLOAD_ERR_OK){
		include "../php/mbc-filemanager/classes/uploadexception.class.php";
		jsonError($UploadExceptionErrors[ $_FILES['file']['error'] ]);
	}
	
	// if file not uploaded then skip it
	if ( !is_uploaded_file($_FILES['file']['tmp_name']) )
		jsonError("File wasn't uploaded.".$_FILES['file']['tmp_name']);
	
	//max 5 Mb
	if ($_FILES['file']['size'] > 5e6)
		jsonError("Il file è troppo grande. Puoi caricare al massimo 5 Mb!");
	
	
	
	$ext = mb_strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
	if (!in_array($ext,['jpg','jpeg','png','gif','bmp']))
		jsonError("Tipo di file non permesso.");
	
	
	$user_path = '../img/users/'.$_SESSION['uid'].'/';
	$user_filename = 'face-';//add 256, 128, 64, 32  +  .png
	
	if (!check_and_create_path($user_path))
		jsonError("Impossibile creare cartella per il file");
	
	
	$temp_file = $user_path . mt_rand().'.'.$ext;
	
	if( move_uploaded_file($_FILES["file"]["tmp_name"], $temp_file) ){
		
		//handle thumbnails
		if (getimagesize($temp_file)){
			
			require_once "../php/php-image-magician/php_image_magician.php";
			
			$sizes = [256,128,64,32];
			
			$source = $temp_file;
			foreach ($sizes as $s){
				if ( !createThumbnail(
					$source,
					$user_path,
					$user_filename.$s.'.png',
					[
						'w' => $s,
						'h' => $s,
						'resize' => 'crop'
					])
				){
					jsonError("Impossibile creare le immagini");
					unlink($temp_file);
				}
				else
					$source = $user_path.$user_filename.$s.'.png';
			}
			
			unlink($temp_file);
			
			//update database (set hasimage to true)
			$pdostat = $pdo->query("UPDATE utenti SET hasimage=1 WHERE id=".$_SESSION['uid']) or jsonError('Errore durante aggiornamento utente [query]');
			if (!$pdostat->rowCount()) jsonError('Nessun utente da aggiornare');
			
			jsonSuccess(['image' => $_SESSION['uid'].'/face-', 'sizes' => $sizes]);
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
	jsonError('Variabili errate');





anchor_remove:

$user_path = '../img/users/'.$_SESSION['uid'].'/';

//remove all images in user folder
array_map('unlink', glob("{$user_path}*.png"));

//remove folder
if (rmdir($user_path)){
	//update database (set hasimage to true)
	$pdostat = $pdo->query("UPDATE utenti SET hasimage=0 WHERE id=".$_SESSION['uid']) or jsonError('Errore durante aggiornamento utente [query]');
	if (!$pdostat->rowCount()) jsonError('Nessun utente da aggiornare');
	jsonSuccess();
}
else
	jsonError('Impossibile cancellare la cartella');


?>