<?php

require_once "config.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(0);

$rwapi = new Setup(true);

if ($rwapi->check === false){
	header('HTTP/1.0 500 Not Found');
	echo '<b>Error:</b> something went wrong during the mbc cms <b>setup</b>.<br><br>';
	echo 'Rules for redirects: <pre>'.htmlentities($rwapi->get_rewrite_rules_apache()).'</pre><br>';
	echo 'Rules for database directory: <pre>'.htmlentities($rwapi->get_permission_rules_db()).'</pre>';
}
else{
	header('Location: ./bacheca?setup=ok');
}

?>