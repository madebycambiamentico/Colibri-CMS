<?php
header('Content-Type: application/json');

require_once "config.php";
require_once "functions.inc.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(0,true);


//control variables
if (!isset($_POST['files'])) jsonError("Variabili errate");
if (!is_array($_POST['files'])) jsonError("Variabili non conformi");



$response = [
	'done' => [],
	'fail' => []
];



/**
* deletes all files listed in array
*
* @param		(array)	$file
*								(string)		dir	[relative directory from uploads]
*								(string)		f		[file name, eg. "file.ext"]
*								(anything)	data	[anything you want to pass to keep track of the file (eg. html id)]
*/
foreach ($_POST['files'] as $file){
	if (empty($file['f'])) continue;
	
	
	//determine directory
	$u_subdir = empty($file['dir']) ? "" : fix_path($file['dir'],true);
	if (!is_dir( $CONFIG['upload_dir'].$u_subdir )){
		$response['fail'][] = [
			'f' => $name,
			'e' => "The specified sub-directory (".$u_subdir.") cannot be found."
		];
		continue;
	}
	if (in_array($CONFIG['upload_dir'].$u_subdir, $CONFIG['hidden_dirs'])){
		$response['fail'][] = [
			'f' => $name,
			'e' => "The specified sub-directory (".$u_subdir.") cannot be deleted."
		];
		continue;
	}
	if ($u_subdir!== "") $u_subdir .= "/";
	
	
	//determine file
	$fixedname = fix_filename($file['f'],true);
	if ($fixedname === ""){
		$response['fail'][] = [
			'f' => $file['f'],
			'e' => "Empty file name..."
		];
		continue;
	}
	if (!file_exists( $CONFIG['upload_dir'].$u_subdir.$fixedname )){
		$response['fail'][] = [
			'f' => $fixedname,
			'e' => "The file doesn't exists anymore..."
		];
		continue;
	}
	
	
	$info = pathinfo($CONFIG['upload_dir'].$u_subdir.$fixedname);
	
	
	//delete main file from disk
	if (!unlink($CONFIG['upload_dir'].$u_subdir.$fixedname)){
		$response['fail'][] = [
			'f' => $fixedname,
			'e' => "Couldn't delete file from disk."
		];
		continue;
	}
	
	
	//---------------------------------------------
	//only images...
	if (getFileGroup($info['extension']) === 0){
		
		//delete thumbs...
		//DEFAULT:
		@unlink( $CONFIG['default_thumb']['dir'].$u_subdir.$fixedname );
		//CUSTOMS:
		foreach ($CONFIG['custom_thumbs'] as $dt){
			@unlink( $CONFIG['default_thumb']['dir'].$dt['dir'].$u_subdir.$fixedname );
		}
		
		//delete from database
		//SEARCH ID
		$pdostat = $pdo->prepare("SELECT id FROM immagini WHERE src = ? LIMIT 1") or jsonError('Errore durante ricerca immagine '.$fixedname.' [prepare]');
		if (!$pdostat->execute([ $u_subdir.$fixedname ])) jsonError('Errore durante ricerca immagine '.$fixedname.' [execute]');
		if ($r = $pdostat->fetch()){
			$id = $r['id'];
			$pdostat = $pdo->prepare("DELETE FROM immagini WHERE id = ?") or jsonError('Errore durante cancellazione immagine '.$fixedname.' [prepare]');
			if (!$pdostat->execute([ $id ])) jsonError('Errore durante cancellazione immagine '.$fixedname.' [execute]');
			if (!$pdostat->rowCount()){
				$response['fail'][] = [
					'f' => $fixedname,
					'e' => "Couldn't delete file from database."
				];
				continue;
			}
			$response['done'][] = [
				'f'		=> $fixedname,
				'e'		=> $info['extension'],
				'd'		=> $u_subdir,
				'data'	=> (isset($file['data']) ? $file['data'] : ""),
				'db'		=> 'delete',
				'id'		=> $id
			];
			continue;
		}
		else {
			$response['fail'][] = [
				'f' => $fixedname,
				'e' => "Couldn't find file in database."
			];
			continue;
		}
	}
	//... end only images
	//---------------------------------------------
	else{
		$response['done'][] = [
			'f'		=> $fixedname,
			'e'		=> $info['extension'],
			'd'		=> $u_subdir,
			'data'	=> (isset($file['data']) ? $file['data'] : ""),
		];
	}
}


jsonSuccess($response);
?>