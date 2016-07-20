<?php

/**
* autoloader for reCAPTCHA v2 wrapper
*
* include this file, then call the main class to initialize the wrapper.
*		//------ without parameters
*		$reCaptcha = new \ReCaptcha\ReCaptcha;
*		//------ with optional parameters
*		$reCaptcha = new \ReCaptcha\ReCaptcha( $publickey, $secretkey, $ip );
*		//------ with optional parameters in array
*		$reCaptcha = new \ReCaptcha\ReCaptcha( ['public_key' => '...', ...] );
* all classes will be loaded automatically when called.
* if you want to add your custom translation, edit Translate.php.
* folder structure to be expected: "./ReCaptcha/<Class>.php" for every <Class> (ReCaptcha and Translate)
*
* @link ReCaptcha/ReCaptcha.php
* @link ReCaptcha/Translate.php
*
* @copyright (C)2016 Nereo Costacurta
*/

spl_autoload_register(function ($class) {
	
	//check if trying to load the namespaced 'ReCaptcha' (10 chars):
	if (substr($class, 0, 10) !== 'ReCaptcha\\')
		return;
	
	//namespaced "\ReCaptcha\Translate" --> file "./reCaptcha/Translate.php"
	$class = str_replace('\\', '/', $class);
	$file = __DIR__ . '/' . $class . '.php';
	
	//request class (if file exists)
	if (is_file($file))
		require __DIR__ . '/' . $class . '.php';
	
}, true);

?>