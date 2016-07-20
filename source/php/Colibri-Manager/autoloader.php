<?php

/**
* autoloader for Colibrì Manager (dashboard, editor, profiles...)
*
* include this file, then call the main class to initialize the wrapper.
*		$my_var = new \Colibri\<Class>;	// <Class> is one of the manager classes available (e.g. Popups)
*													// with optional parameters if needed
*
* @link Colibri/Template.php
* @link Colibri/Popups.php
* @link Colibri/SessionManager.php
*
* @copyright (C)2016 Nereo Costacurta
*/

spl_autoload_register(function ($class) {
	
	//check if trying to load the namespaced 'Colibri\' (8 chars):
	if (substr($class, 0, 8) !== 'Colibri\\')
		return;
	
	//namespaced "\Colibri\Popups" --> file "./Colibri/Popups.php"
	$class = str_replace('\\', '/', $class);
	$file = __DIR__ . '/' . $class . '.php';
	
	//request class (if file exists)
	if (is_file($file))
		require __DIR__ . '/' . $class . '.php';
	
}, true);

?>