<?php

/**
* check compatibility with this plugin, and update query
*
* requires $_POST['CC']['allow_comments']
*/

global $id, $params;

if ($id){
	//existent article
	global $query;
	$query .= ", comment_allow = ?";
	$params[] = isset($_POST['CC']['comment_allow']) ? true : false;
}
else{
	//new article
	global $query_insert, $query_values;
	$query_insert .= ", comment_allow";
	$query_values .= ", ?";
	$params[] = isset($_POST['CC']['comment_allow']) ? true : false;
}


?>