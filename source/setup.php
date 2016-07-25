<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}


function setup_error($string){
	header($_SERVER["SERVER_PROTOCOL"]." 422 Validation failure");
	die( '<b>Error during installation:</b> '.$string );
}


/*************
 START CONFIG
**************/
//configuration variables: folder, database position...

//phisycal Colibrì root directory (where config.php is)
define( 'CMS_INSTALL_DIR', __DIR__ );

//autoloader for non-categorized Colibrì classes
require CMS_INSTALL_DIR . '/autoloader.php';

$Config = new ColibriConfig();




/*************
 START SETUP
**************/

//write htaccess, generate secret key, give random name to database.
$rwapi = new \Colibri\Setup(true);
if ($rwapi->check === false)
	setup_error( $rwapi->get_errors().'<br>'.$rwapi->get_logs() );
else{
	header('refresh:15;url=./login?setup=ok');
	setup_error( $rwapi->get_logs() );
}

?>