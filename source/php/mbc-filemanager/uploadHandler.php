<?php
header('Content-Type: application/json');

require_once "config.php";

$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(0,true);


//extend CONFIG with custom thumbnails
$pdores = $pdo->query("SELECT template FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC) or jsonError("STOP QUERY");
if ($templ = $pdores->fetch()){
	//if file exists include custom thumbnails
	$tumbnail_extension = CMS_INSTALL_DIR . "/templates/".$templ['template'].'/fm-custom-thumbnails.php';
	if (is_file($tumbnail_extension))
		include $tumbnail_extension;
}


require_once "../php-image-magician/php_image_magician.php";

include "classes/uploadexception.class.php";






//set subdirectory
$u_subdir = empty($_POST['dir']) ? "" : fix_path($_POST['dir'],true);

if (!is_dir( $Config->FM['upload_dir'].$u_subdir ))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be found.");

if (in_array($Config->FM['upload_dir'].$u_subdir, $Config->FM['hidden_dirs']))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be writed.");

if ($u_subdir!== "") $u_subdir .= "/";


/* @description remove uploaded+moved image if something went wrong (write to database for example).
 *
 * @params
 * (String)	$newfile		: file to remove.
 * (Bool)	$stopscript	: if TRUE, then stops the upload process completely
 * (String)	$report		: the error to print (only if $stopscript == TRUE) or to assign to failure response
 *
 * @return
 * (Void)
*/
function abortImage($newfile, $stopscript=false, $report="errore sconosciuto", $fixedname=''){
	global $response;
	if (!is_dir($newfile) && is_file($newfile)) unlink($newfile);
	$response['fail'][] = [
		'f' => $fixedname,
		'e' => $report
	];
	if ($stopscript) jsonError($report);
}



/**
* create thumbnail from $Config configured thumbnail sizes and filters.
*
* @params (String) $source			link to source image. must exists.
* @params (String) $filename		name of the file (eg. "file.ext") without path
* @params (Array)  $CT				item from $Config->FM['default_thumb'] or $Config->FM['custom_thumbs'][$i]
* @params (String) $subdir			relative sub-directory from thumb folder. should be $CT['dir'] but the default thumb.
*/
function createThumbnail($source, $filename, $CT, $subdir=""){
	global $Config;
	$magicianObj = new imageLib($source);
	//rezized image...
	if ($CT['sizes'][0]>0 && $CT['sizes'][1]>0)
		$magicianObj->resizeImage($CT['sizes'][0], $CT['sizes'][1], $CT['resize'], true);//true -> sharpening
	//apply filters...
	foreach ($CT['filters'] as $filter){
		//complex filters... TODO. for now only non-argumented filters
		$magicianObj->$filter();
	}
	//create thumbnail:
	$thumbpath = $Config->FM['default_thumb']['dir'] . $subdir;
	// - control if directory exists - or write it
	if (!file_exists($thumbpath)){
		if (!mkdir($thumbpath,0755,true)) return false;
	}
	else{
		if (!is_writable($thumbpath)) return false;
	}
	$magicianObj->saveImage( $thumbpath . $filename, isset($CT['quality']) ? $CT['quality'] : 100);
	return true;
}



$response = [
	'done' => [],
	'fail' => []
];






//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//NB - no MIME check... TODO...
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!





if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_FILES['files'])){
	//----------- empty file -----------
	if (empty($_FILES['files']['name'])){
		jsonError("Nessun file in upload!");
	}
	
	//----------- single file -----------
	//something went wrong with <form> upload
	if (!is_array($_FILES['files']['name'])){
		jsonError("Il tuo browser non supporta l'upload di file multipli. Aggiornalo!");
	}
	
	//----------- multiple files -----------
	// loop all files
	else foreach ( $_FILES['files']['name'] as $i => $name ){
		
		//errors log...
		if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK){
			$response['fail'][] = [
				'f' => $name,
				'e' => $UploadExceptionErrors[ $_FILES['files']['error'][$i] ]
			];
			continue;
		}
		
		/*
		//max 15 Mb
		if ($_FILES['file']['size'][$i] > 15e6) {
			jsonError("File too big.");
		}
		*/
		
		//purge file name
		$fixedname = fix_filename($name,true);
		if ($fixedname === ""){
			$response['fail'][] = [
				'f' => $name,
				'e' => "Empty file name..."
			];
			continue;
		}
		
		// if file not uploaded then skip it
		if ( !is_uploaded_file($_FILES['files']['tmp_name'][$i]) ){
			$response['fail'][] = [
				'f' => $fixedname,
				'e' => "File wasn't uploaded."
			];
			continue;
		}
		
		$control_ext = 0;
		$ext = "";
		foreach ($Config->FM['allowed_ext'] as $group => $extensions){
			$ext = mb_strtolower(pathinfo($fixedname, PATHINFO_EXTENSION));
			if ( in_array($ext, $extensions) ){
				// skip large files
				if ( $_FILES['files']['size'][$i] >= $Config->FM['max_file_size'][$group] ){
					$response['fail'][] = [
						'f' => $fixedname,
						'e' => "The uploaded file exceeds the max file size directive in config.php"
					];
					continue;
				}
				break;
			}
			$control_ext++;
		}
		// skip unprotected files
		if ($control_ext == count($Config->FM['allowed_ext'])){
			$response['fail'][] = [
				'f' => $fixedname,
				'e' => "Extension not allowed!"
			];
			continue;
		}
		
		//set path + file as new file...
		$newfile = $Config->FM['upload_dir'].$u_subdir.$fixedname;
		
		$existent = false;
		if (file_exists($newfile)){
			if (!$Config->FM['allow_overwrite_file']){
				$response['fail'][] = [
					'f' => $fixedname,
					'e' => "Overwirte file disabled in config.php. Change directive to give permission to write on existent files"
				];
				continue;
			}
			else $existent = true;
		}
		
		// now we can move uploaded files
		if( move_uploaded_file($_FILES["files"]["tmp_name"][$i], $newfile) ){
			
			
			//handle image + thumbnails
			if ($imgsize = getimagesize($newfile)){
				
				//create thumbnails...
				//DEFAULT:
				createThumbnail($newfile, $fixedname, $Config->FM['default_thumb'], $u_subdir);
				//CUSTOMS:
				foreach ($Config->FM['custom_thumbs'] as $custom_thumb){
					createThumbnail($newfile, $fixedname, $custom_thumb, $custom_thumb['dir'].$u_subdir);
				}
				
				//insert into database...
				$src = $u_subdir.$fixedname;
				if ($existent){
					//SEARCH ID
					$pdostat = $pdo->prepare("SELECT id FROM immagini WHERE src = ? LIMIT 1") or abortImage($newfile,true,'Errore durante ricerca immagine '.$fixedname.' [prepare]');
					if (!$pdostat->execute([$src])) abortImage($newfile,true,'Errore durante ricerca immagine '.$fixedname.' [execute]');
					if ($r = $pdostat->fetch()){
						$id = $r['id'];
						
						//UPDATE
						$pdostat = $pdo->prepare("UPDATE immagini SET width = ?, height = ?, data = CURRENT_TIMESTAMP WHERE id = ?") or abortImage($newfile,true,'Errore durante aggiornamento immagine '.$fixedname.' [prepare]');
						if (!$pdostat->execute([$imgsize[0], $imgsize[1], $id])) abortImage($newfile,true,'Errore durante aggiornamento immagine '.$fixedname.' [execute]');
						if (!$pdostat->rowCount()){
							abortImage($newfile,false,"Non c'era un match nel database!", $fixedname);
							continue;
						}
						$response['done'][] = [
							'f'			=> $fixedname,
							'e'			=> $ext,
							'g'			=> $control_ext,
							'w'			=> $imgsize[0],
							'h'			=> $imgsize[1],
							'd'			=> $u_subdir,
							"s"			=> human_filesize($newfile),
							'db'			=> 'update',
							'id'			=> $id
						];
						continue;
					}
					//else continue script to INSERT...
				}
				
				//INSERT
				$pdostat = $pdo->prepare("INSERT INTO immagini (src, width, height) VALUES (?, ?, ?)") or abortImage($newfile,true,'Errore durante inserimento immagine '.$fixedname.' [prepare]');
				if (!$pdostat->execute([$src, $imgsize[0], $imgsize[1]])) abortImage($newfile,true,'Errore durante inserimento immagine '.$fixedname.' [execute]');
				if (!$id = $pdo->lastInsertId()){
					abortImage($newfile,false,"Couldn't insert image in database (empty id returned)!", $fixedname);
					continue;
				}
				$response['done'][] = [
					'f'			=> $fixedname,
					'e'			=> $ext,
					'g'			=> $control_ext,
					'w'			=> $imgsize[0],
					'h'			=> $imgsize[1],
					'd'			=> $u_subdir,
					"s"			=> human_filesize($newfile),
					'db'			=> 'insert',
					'id'			=> $id
				];
				continue;
			}
			else{
				//common file (no image)
				$response['done'][] = [
					'f'			=> $fixedname,
					'e'			=> $ext,
					'g'			=> $control_ext,
					"s"			=> human_filesize($newfile),
					'd'			=> $u_subdir,
					'db'			=> ($existent ? 'update' : 'insert')
				];
			}
		}
	}
}

jsonSuccess($response);

?>