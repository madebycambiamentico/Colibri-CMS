<?php

/**
* Colibri > Comments protection from outer domain requests
*
* @param (bool) $_POST['crossdomain'] [optional] Set if cross domain request are allowed
*
* @version 1.0.0
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../../../config.php';


//control variables
if (!isset($_POST['style']))
	jsonError("Missing variables");


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true); // only webmasters are meant to edit database.


function update_director($style=""){
	$_GET = []; //assicuriamoci che non ci siano richieste get...
	$json_director = include('../director.php'); //altrimenti l'include potrebbe far cose strane...
	
	foreach ($json_director['template']['style']['auto'] as &$css){
		if (0 === strpos($css,'comments',4)){
			$css = "css/comments" . ($style ? ".{$style}" : "") . ".css";
			break;
		}
	}
	
	//write to file...
	if (false === @file_put_contents('director.json', json_encode($json_director), LOCK_EX))
		jsonError("Impossibile modificare director.json...");
}

switch ($_POST['style']){
	//default styles:
	case 'dark':
		update_director('dark');// => css/comments.dark.css
	break;
	case 'light':
		update_director('light');// => css/comments.light.css
	break;
	case "":
		update_director('');// => css/comments.css
	break;
	//custom style:
	default;
		update_director('custom');// => css/comments.custom.css
}

jsonSuccess();

?>