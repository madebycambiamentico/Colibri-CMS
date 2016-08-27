<?php

/**
* autoloader for Colibrì website
*
* autoload the the default function to query the database and include the chosen template.
* include this file, then call the main class to initialize the wrapper.
*		$my_var = new \WebSpace\<Class>;	// <Class> is one of the manager classes available (e.g. Query)
*													// with optional parameters if needed
*
* @link WebSpace/Query.php
* @link WebSpace/Template.php
*
* @copyright (C)2016 Nereo Costacurta
*/

spl_autoload_register(function ($class) {
	
	//check if trying to load the namespaced 'WebSpace\' (9 chars):
	if (substr($class, 0, 9) !== 'WebSpace\\')
		return;
	
	//namespaced "\WebSpace\Query" --> file "./WebSpace/Query.php"
	$class = str_replace('\\', '/', $class);
	$file = __DIR__ . '/' . $class . '.php';
	
	//request class (if file exists)
	if (is_file($file))
		require __DIR__ . '/' . $class . '.php';
	
}, true);

?>