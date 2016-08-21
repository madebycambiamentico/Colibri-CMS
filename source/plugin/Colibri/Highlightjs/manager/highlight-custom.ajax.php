<?php

/**
* Colibri > Highlightjs toggle to local files
*
* @param (bool) $_POST['localfiles'] [optional] Set if plugin should use local files or cdn's
*
* @version 1.0.0
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../../../../config.php';


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true); // only webmasters are meant to edit database.



//control variables
if (!isset($_POST['localfiles']))
	jsonError("Missing variables");

$files = [
	'css' => 'default.min.css',
	'js' => 'highlight.min.js'
];

$json = json_decode( file_get_contents(__DIR__ . "/../director.json"), true );


//set files to be loaded from local space or cdn?
$json['template']['style']['auto'][0] = ($_POST['localfiles']==0 ? 'css/' : '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/styles/') . $files['css'];
$json['template']['js']['auto'][0] = ($_POST['localfiles']==0 ? 'js/' : '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/') . $files['js'];


if ( file_put_contents(__DIR__ . "/../director.json", json_encode($json, JSON_PRETTY_PRINT )) )
	jsonSuccess();
else
	jsonError("Failed to update JSON")


?>