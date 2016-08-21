<?php

/**
* Colibri > Comments installer
*
* INSTALLATION: not supported
* UN-INSTALLATION: not supported
*
* @param (bool) $_POST['install'] [optional] Ask to perform installation or unintallation
*
* @version 1.0.0
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../../../config.php';


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2); // only webmasters are meant to edit database.


//control variables
if (!isset($_POST['install']))
	jsonError("Missing variables");






//this plugin doesn't support uninstallation
if (!$_POST['install'] || $_POST['install']=='false'){
	jsonError("This plugin doesn't support uninstallation.");
}



jsonSuccess();

?>