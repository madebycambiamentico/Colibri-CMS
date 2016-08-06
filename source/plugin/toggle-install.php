<?php

/**
* flag a plugin installed or uninstalled
*
* @param (string)	$_POST['p']								the name of the plugin in the form "<Author>/<Title>"
* @param (bool)	$_POST['installed'] [optional]	override install flag. if not set, the installed flag will be toggled
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../config.php';


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2); // only webmasters are meant to edit database.


//control variables
if (empty($_POST['p']))
	jsonError('Variabili errate');


$PlugManager = new \Colibri\PluginsManager(false);
$PlugManager->get_plugins_status();

//check if plugin in list
if ( ! isset( $PlugManager->available[$_POST['p']]['installed'] ) )
	jsonError("This plugin doesn't exists!");




//edit current plugin status
if (isset($_POST['installed'])){
	$override = $_POST['installed'] && $_POST['installed']!=='false' ? true : false;
	//--- override (do not toggle)
	if ($PlugManager->available[$_POST['p']]['installed'] === $override)
		jsonSuccess(['installed' => $override]);
	else
		$installed = $PlugManager->available[$_POST['p']]['installed'] = $override;
}
else{
	//--- toggle flag
	$installed = $PlugManager->available[$_POST['p']]['installed'] = !$PlugManager->available[$_POST['p']]['installed'];
}



//write to list.json
if ( ! $PlugManager->update_plugins_status( $PlugManager->available ))
	jsonError("Cannot update list.json");


jsonSuccess(['installed' => $installed]);

?>