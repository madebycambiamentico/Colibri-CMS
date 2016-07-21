<?php
header('Content-Type: application/json');

require_once "config.php";

$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(0,true);


//control variables
if (!isset($_GET['dir'],$_GET['f'])) jsonError("Variabili errate");


//determine directory
$u_subdir = empty($_GET['dir']) ? "" : fix_path($_GET['dir'],true);
if (!is_dir( $Config->FM['upload_dir'].$u_subdir ))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be found.");
if (in_array($Config->FM['upload_dir'].$u_subdir, $Config->FM['hidden_dirs']))
	jsonError("The specified sub-directory (".$u_subdir.") cannot be deleted.");
if ($u_subdir!== "") $u_subdir .= "/";


//determine file
$fixedname = fix_filename($_GET['f'],true);
if ($fixedname === "")
	jsonError("Empty file name...");
if (!file_exists( $Config->FM['upload_dir'].$u_subdir.$fixedname ))
	jsonError("The file doesn't exists anymore...");


//description (only for images -- for now at least)
$info = pathinfo($Config->FM['upload_dir'].$u_subdir.$fixedname);
if (!in_array($info['extension'],$Config->FM['allowed_ext']['img']))
	jsonSuccess(['id' => 0, 'desc' => ""]);


//query database
//SEARCH ID
$pdostat = $pdo->prepare("SELECT descr FROM immagini WHERE src = ? LIMIT 1") or jsonError('Errore durante ricerca immagine '.$fixedname.' [prepare]');
if (!$pdostat->execute([ $u_subdir.$fixedname ])) jsonError('Errore durante ricerca immagine '.$fixedname.' [execute]');
if ($r = $pdostat->fetch())
	jsonSuccess(['id' => 0, 'desc' => $r['descr']]);
else
	jsonError("Couldn't find file in database.");
?>