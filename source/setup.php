<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}


function setup_error($string){
	header($_SERVER["SERVER_PROTOCOL"]." 422 Validation failure");
	echo '<b>Error during installation:</b> '.$string;
	die;
}


//configuration variables: folder, database position...
require_once "config.php";


/* create database from mbcsqlite3-default.db */
$default_database = $CONFIG['c_dir'].$CONFIG['database']['dir'].'mbcsqlite3-default.db';
if (!file_exists($CONFIG['database']['path']) || !filesize($CONFIG['database']['path'])){
	if (file_exists($default_database)){
		copy($default_database, $CONFIG['database']['path']) or
			setup_error("Couldn't create database from ({$default_database}). Cannot proceed with installation.");
		//proceed 
	}
	else{
		setup_error("Default database not found ({$default_database}). Cannot proceed with installation.");
	}
}
clearstatcache();


$rwapi = new \Colibri\Setup(true);

if ($rwapi->check === false){
	setup_error( $rwapi->get_errors().'<br>'.$rwapi->get_logs() );
}
else{
	header('Location: ./login?setup=ok');
}

?>