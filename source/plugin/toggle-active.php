<?php

/**
* flag a plugin activated or deactivated
*
* @param (string)	$_POST['p']								the name of the plugin in the form "<Author>/<Title>"
* @param (bool)	$_POST['active'] [optional]		override active flag. if not set, the activated flag will be toggled
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
if ( ! isset( $PlugManager->available[$_POST['p']]['active'] ) )
	jsonError("This plugin doesn't exists!");



//edit current plugin status
if (isset($_POST['active'])){
	$override = $_POST['active'] && $_POST['active']!=='false' ? true : false;
	//--- override (do not toggle)
	if ($PlugManager->available[$_POST['p']]['active'] === $override)
		jsonSuccess(['active' => $override]);
	else
		$active = $PlugManager->available[$_POST['p']]['active'] = $override;
}
else{
	//--- toggle flag
	$active = $PlugManager->available[$_POST['p']]['active'] = !$PlugManager->available[$_POST['p']]['active'];
}



//write to list.json
if ( ! $PlugManager->update_plugins_status( $PlugManager->available ))
	jsonError("Cannot update list.json");


jsonSuccess(['active' => $active]);

?>