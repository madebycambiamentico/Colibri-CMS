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
	
	//check if trying to load the namespaced class:
	if (substr($class, 0, 10) !== 'RandomLib\\' && substr($class, 0, 12) !== 'SecurityLib\\')
		return;
	
	//namespaced "\ReCaptcha\Translate" --> file "./reCaptcha/Translate.php"
	$class = str_replace('\\', '/', $class);
	$file = __DIR__ . '/lib/' . $class . '.php';
	
	//request class (if file exists)
	if (is_file($file))
		require $file;
	
}, true);


/*/
//die("This test has gone well, no need to run it again!!!");

//-----------
//test
//-----------

$time1 = microtime(true);

$factory = new RandomLib\Factory;

//$generator = $factory->getLowStrengthGenerator();
$generator = $factory->getMediumStrengthGenerator();
//$generator = $factory->getHighStrengthGenerator();

$randomStringLength = 16;
$randomStringAlphabet = '0123456789@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
$randomString = '';

for ($i=0;$i<10;$i++){
	$randomString = $generator->generateString( $randomStringLength , $randomStringAlphabet);
	echo $randomString.'<br>';
}

$time2 = microtime(true);
echo 'script execution time: '.($time2 - $time1).' seconds';
//*/
?>