<?php
header('Content-Type: application/json');

require_once "config.php";
require_once "functions.inc.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(0,true);


//control variables
if (!isset($_GET['type'],$_GET['dir'],$_GET['f'])) jsonError("Variabili errate");



switch($_GET['type']){
	//create new directory
	case 'newdir':
		goto anchor_1;
	break;
	//edit directory name
	case 'dir':
		if (!isset($_GET['original'])) jsonError("Variabili non acettabili");
		goto anchor_2;
	break;
	//edit file name [and description]
	default:
		if (!isset($_GET['original'])) jsonError("Variabili non acettabili");
		goto anchor_0;
}



anchor_0://edit file name [and description]

if (!isset($_GET['original'])) jsonError("Variabili errate");

//fix directory
$u_subdir = empty($_GET['dir']) ? "" : fix_path($_GET['dir'],true);
if (!is_dir( $CONFIG['upload_dir'].$u_subdir ))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be found.");
if (in_array($CONFIG['upload_dir'].$u_subdir, $CONFIG['hidden_dirs']))
	jsonError("The specified sub-directory (".$u_subdir.") is not writable.");
if ($u_subdir!== "") $u_subdir .= "/";

//determine original file name
$fixedOLDname = fix_filename($_GET['original'],true);
if ($fixedOLDname === "")
	jsonError("Empty file name...");
if (!file_exists( $CONFIG['upload_dir'].$u_subdir.$fixedOLDname ))
	jsonError("The file doesn't exists anymore...");
if (is_dir($CONFIG['upload_dir'].$u_subdir.$fixedOLDname))
	jsonError("Cannot edit folders like was files!");
$oldExt = pathinfo( $CONFIG['upload_dir'].$u_subdir.$fixedOLDname, PATHINFO_EXTENSION );

//determine file name
$fixedname = fix_filename($_GET['f'].'.'.$oldExt,true);
if ($fixedname === "")
	jsonError("Empty file name...");
if (file_exists( $CONFIG['upload_dir'].$u_subdir.$fixedname && $fixedOLDname !== $fixedname))
	jsonError("A file with same name already exists!");

//rename file
if (rename($CONFIG['upload_dir'].$u_subdir.$fixedOLDname, $CONFIG['upload_dir'].$u_subdir.$fixedname)){
	
	if (!in_array($oldExt,$CONFIG['allowed_ext']['img'])){
		jsonSuccess([
			'f'			=> $fixedname,
			'd'			=> $u_subdir,
			'action'		=> 'file'
		]);
	}
	
	//try to rename all thumbs
	//DEFAULT:
	@rename($CONFIG['default_thumb']['dir'].$u_subdir.$fixedOLDname, $CONFIG['default_thumb']['dir'].$u_subdir.$fixedname);
	//CUSTOMS:
	foreach ($CONFIG['custom_thumbs'] as $dt){
		@rename( $CONFIG['default_thumb']['dir'].$dt['dir'].$u_subdir.$fixedOLDname, $CONFIG['default_thumb']['dir'].$dt['dir'].$u_subdir.$fixedname );
	}
	
	//update database -- only for images
	$pdostat = $pdo->prepare("SELECT id FROM immagini WHERE src = ? LIMIT 1") or jsonError('Errore durante ricerca immagine '.$fixedOLDname.' [prepare]');
	if (!$pdostat->execute([ $u_subdir.$fixedOLDname ])) jsonError('Errore durante ricerca immagine '.$fixedOLDname.' [execute]');
	if ($r = $pdostat->fetch()){
		$id = $r['id'];
		$pdostat = $pdo->prepare("UPDATE immagini SET descr = ?, src = ? WHERE id = ?") or jsonError('Errore durante cancellazione immagine '.$fixedname.' [prepare]');
		if (!$pdostat->execute([ $_GET['desc'], $u_subdir.$fixedname, $id ])) jsonError('Errore durante cancellazione immagine '.$fixedname.' [execute]');
		if (!$pdostat->rowCount())
			jsonError("Couldn't update file in database.");
		else
			jsonSuccess([
				'f'			=> $fixedname,
				'd'			=> $u_subdir,
				'desc'		=> (string) $_GET['desc'],
				'action'		=> 'file',
				'db'			=> 'update',
				'id'			=> $id
			]);
	}
	else
		jsonError("Couldn't find file in database.");
	
}
else
	jsonError("Couldn't rename file.");

exit;





anchor_1://create new directory

//fix directory
$u_subdir = empty($_GET['dir']) ? "" : fix_path($_GET['dir'],true);
if (!is_dir( $CONFIG['upload_dir'].$u_subdir ))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be found.");
if (in_array($CONFIG['upload_dir'].$u_subdir, $CONFIG['hidden_dirs']))
	jsonError("The specified sub-directory (".$u_subdir.") is not writable.");
$u_subdir .= "/";

//determine folder name
$fixedname = fix_filename($_GET['f'],true);
if ($fixedname === "")
	jsonError("Empty folder name...");
if (file_exists( $CONFIG['upload_dir'].$u_subdir.$fixedname ))
	jsonError("The folder or file already exists...");

//create directory
if (mkdir( $CONFIG['upload_dir'].$u_subdir.$fixedname, 0755 ))
	jsonSuccess(['d' => $u_subdir, 'f' => $fixedname, 'action' => 'newdir']);
else
	jsonError("Couldn't create folder.");

exit;





anchor_2://edit directory name

jsonError("not implemented (TODO)");



exit;


?>