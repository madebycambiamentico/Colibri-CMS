<?php

/**
* start colibri session if not already started.
* skip if comment not allowed in this page.
*/

global $web, $page;

if ($web['comment_allow'] && $page['comment_allow']){
	global $SessionManager;
	if (!isset($SessionManager)){
		$SessionManager = new \Colibri\SessionManager;
		$SessionManager->sessionStart('colibri');
	}
}

?>