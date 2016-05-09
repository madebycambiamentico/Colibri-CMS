<?php
header('Content-Type: application/json');

require_once "../../../config.php";
require_once "../../../".$CONFIG['database']['dir']."functions.inc.php";
closeConnection();

//control login
require_once "../../../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);


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
	$magicianObj->saveImage( $path . $filename, isset($MOParams['quality']) ? $MOParams['quality'] : 100);
	return true;
}


//response keeper - NEEDED
$response = [];



//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//NB - no MIME check... TODO...
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


if ($_SERVER['REQUEST_METHOD'] == 'POST')
	include "template-edit-logo.inc.php";
else
	die('Variabili errate');


jsonSuccess($response);

?>