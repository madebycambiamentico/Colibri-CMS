<?php

/**
* Colibri > Comments protection from outer domain requests
*
* @param (bool) $_POST['crossdomain'] [optional] Set if cross domain request are allowed
*
* @version 1.0.0
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../../../config.php';


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true); // only webmasters are meant to edit database.



//control variables
if (!isset($_POST['crossdomain']))
	jsonError("Missing variables");


if ($_POST['crossdomain']){
	//this htaccess code should prevent call from other domains.
	//should we allow somebody to get something? nope.
	//possible issue: if site moved to other domain this installer should be re-runned
	$site = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
	$htaccess =
	"RewriteCond %{HTTP_REFERER} !^$
	RewriteCond %{HTTP_REFERER} !{$site} [NC]
	RewriteRule \.(php)$ - [F,NC]";

	if (!file_put_contents($htaccess, __DIR__ . "/template/.htaccess")
		jsonError("Unable to write .htaccess file.");
}
else{
	unlink( __DIR__ . "/template/.htaccess");
}

?>