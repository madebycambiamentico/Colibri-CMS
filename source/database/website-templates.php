<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);


$dir = '../templates/';
$folders = array_diff(scandir($dir), array('..', '.'));
$templates = [];
foreach($folders as $f){
	if (!is_dir($dir.$f)) continue;
	//check if contains the minimum required files:
	if (!is_file($dir.$f."/properties.json")) continue;
	$json = json_decode(file_get_contents($dir.$f.'/properties.json'), true);
	if (!isset($json['folder']) || $f !== $json['folder']) continue;
	$templates[] = $json;
}

jsonSuccess(['templates' => $templates]);


?>