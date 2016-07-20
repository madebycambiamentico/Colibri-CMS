<?php

/**
* autoloader for non categorized Colibrì classes
*
* include this file, then call the class.ì
* folder structure to be expected: "/php/<Class>.class.php"
* where <Class> is the name of the class, which must be te same as the file name.
*
* @copyright (C)2016 Nereo Costacurta
*/

spl_autoload_register(function ($class) {
	
	$file = __DIR__ . '/php/' . $class . '.class.php';
	
	//request class (if file exists)
	if (file_exists($file)) {
		require $file;
	}
	
}, true);


/*
//educational moment...
//how does it works?

// istead of write:
include_once("./php/foo.class.php");
include_once("./php/bar.class.php");
...

// the right classes will be loaded automatically on call
// with no need to explicitly include file!!!
$foo = new Foo;
BAR::dosomething();
*/

?>