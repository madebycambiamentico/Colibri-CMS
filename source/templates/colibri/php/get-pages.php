<?php
header('Content-Type: application/json');

require_once "../../../config.php";
require_once "../../../".$CONFIG['database']['dir']."functions.inc.php";

$query = 'SELECT a.id as "pageid", a.titolo as "title", a.inbreve as "desc", im.src, yt.*
	FROM articoli a
	LEFT JOIN immagini im ON a.idimage = im.id
	LEFT JOIN youtube yt ON yt.idarticolo = a.id
	WHERE a.idtype = 1 AND NOT a.isgarbage
	ORDER BY a.dataedit DESC';

$pdores = $pdo->query($query,PDO::FETCH_ASSOC) or jsonError("Couldn't fetch query!");
$pages = $pdores->fetchAll();

exit(json_encode($pages));

?>