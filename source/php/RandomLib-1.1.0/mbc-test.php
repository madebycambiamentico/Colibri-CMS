<?php

die("This test has gone well, no need to run it again!!!");

$time1 = microtime(true);


spl_autoload_register(function ($class) {
	$nslen = strlen(__NAMESPACE__);
	if (substr($class, 0, $nslen) != __NAMESPACE__) {
		//Only autoload libraries from this package
		return;
	}
	$path = substr(str_replace('\\', '/', $class), $nslen);
	$path = __DIR__ . '/lib/' . $path . '.php';
	if (file_exists($path)) {
		require_once $path;
	}
});




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
?>