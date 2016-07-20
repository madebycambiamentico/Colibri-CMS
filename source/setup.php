<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}


function setup_error($string){
	header($_SERVER["SERVER_PROTOCOL"]." 422 Validation failure");
	echo '<b>An Error occurred:</b> '.$string;
	die;
}


//configuration variables: folder, database position...
require_once "config.php";


/* create database from mbcsqlite3-default.db */
$default_database = $CONFIG['c_dir'].$CONFIG['database']['dir'].'mbcsqlite3-default.db';
if (!file_exists($CONFIG['database']['path'])){
	if (file_exists($default_database)){
		copy($default_database, $CONFIG['database']['path']) or
			setup_error("couldn't create database from ({$default_database}). Cannot proceed with installation.");
		//proceed 
	}
	else{
		setup_error("default database not found ({$default_database}). Cannot proceed with installation.");
	}
}


$rwapi = new \Colibri\Setup(true);

if ($rwapi->check === false){
	setup_error('something went wrong during the <b>setup</b>.<br><br>'.
		'Rules for redirects: <pre>'.htmlentities($rwapi->get_rewrite_rules_apache()).'</pre><br>'.
		'Rules for database directory: <pre>'.htmlentities($rwapi->get_permission_rules_db()).'</pre>');
}
else{
	header('Location: ./login?setup=ok');
}

?>