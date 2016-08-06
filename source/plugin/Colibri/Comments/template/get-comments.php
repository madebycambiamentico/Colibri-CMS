<?php
header('Content-Type: application/json');

/**
* add 1 comment to database
*/

require_once "../../../../config.php";

if (!isset($_GET['pageid']))
	jsonError("Missing variables");
$pageid = intval($_GET['pageid'],10);
if (!$pageid)
	jsonError("Not valid variables");


//check if plugin enabled / activated
$PlugManager = new \Colibri\PluginsManager(false);
$PlugManager->get_plugins_status();
if (!isset($PlugManager->available['Colibri/Comments']))
	jsonError("This plugin is not installed.");
if (!$PlugManager->available['Colibri/Comments']['active'])
	jsonError("This plugin is not active.");



function get_sub_comments($comment_id, $max_depth=0){
	//todo: join users to get photo
	global $pdo;
	$query =
"SELECT
	d.*,
	p.*,
	ut.nome AS 'author', ut.hasimage,
	GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM commenti AS d
JOIN commenti_treepath AS p ON d.id = p.descendant
JOIN commenti_treepath AS crumbs ON crumbs.descendant = p.descendant
LEFT JOIN utenti AS ut ON ut.id = d.idutente
WHERE p.ancestor = {$comment_id}".($max_depth ? " AND p.depth <= {$max_depth} " : "")."
GROUP BY d.id
ORDER BY breadcrumbs;";
	$pdores = $pdo->query($query,\PDO::FETCH_ASSOC);
	
	$comments = [];
	
	setlocale(LC_TIME, $loc_code);	// set custom language
	while ($r = $pdores->fetch()){
		$comments[] = [
			'cid' => $r['id'],
			'c' => $r['content'],
			'a' => [
				'id' => $r['idutente'] ? $r['idutente'] : 0,
				'name' => $r['idutente'] ? $r['author'] : $r['name'],
				'hasimage' => $r['idutente'] ? $r['hasimage'] : 0,
				'website' => $r['idutente'] ? "" : $r['website']
			],
			'd' => strftime($format)
		];
	}
	setlocale(LC_TIME,false);		// reset locale language
	
	jsonSuccess(['comments' => $comments]);
}


function get_comments($page_id, $max_depth=0, $loc_code='it_IT', $format="%d %B %Y"){
	//todo: join users to get photo
	global $pdo;
	$query =
"SELECT
	cmt.*,
	ut.nome AS 'author', ut.hasimage,
	GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM commenti AS cmt
JOIN commenti_treepath AS tree ON cmt.id = tree.descendant
JOIN commenti_treepath AS crumbs ON crumbs.descendant = tree.descendant
LEFT JOIN utenti AS ut ON ut.id = cmt.idutente
WHERE tree.ancestor IN (SELECT id FROM commenti WHERE idarticolo = {$page_id} AND idcommento IS NULL)".
	($max_depth ? " AND tree.depth <= {$max_depth} " : "")."
GROUP BY cmt.id
ORDER BY breadcrumbs;";
	$pdores = $pdo->query($query,\PDO::FETCH_ASSOC);
	
	$comments = [];
	
	setlocale(LC_TIME, $loc_code);	// set custom language
	while ($r = $pdores->fetch()){
		$comments[] = [
			'o' => $r['breadcrumbs'],
			'cid' => $r['id'],
			'c' => $r['content'],
			'a' => [
				'id' => $r['idutente'] ? $r['idutente'] : 0,
				'name' => $r['idutente'] ? $r['author'] : $r['name'],
				'hasimage' => $r['idutente'] ? $r['hasimage'] : 0,
				'website' => $r['idutente'] ? "" : $r['website']
			],
			'd' => strftime($format,strtotime($r['datacreaz']))
		];
	}
	setlocale(LC_TIME,false);		// reset locale language
	
	jsonSuccess(['comments' => $comments]);
	
}

if (isset($_GET['commentid'])){
	$commentid = intval($_GET['commentid'],10);
	if (!$commentid)
		jsonError("Not valid variables");
	get_sub_comments($commentid);
}
else{
	get_comments($pageid);
}


?>