<?php

/**
* check compatibility with this plugin, and update query
*
* requires $_POST['CC']['comment_allow'], $_POST['CC']['comment_class']
*/

global $query, $params;

$query .= ", comment_allow = ?, comment_class = ?";

//control minimum class
$CC_class = isset($_POST['CC']['comment_class']) ? intval($_POST['CC']['comment_class']) : -1;
	if ($CC_class<-1) $CC_class = -1;
	elseif ($CC_class>2) $CC_class = 2;

$params[] = isset($_POST['CC']['comment_disallow']) ? false : true;
$params[] = $CC_class;


?>