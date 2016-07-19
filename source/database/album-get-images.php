<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

//control variables
if (!isset($_GET['id']) || !isset($_GET['request'])) jsonError('Variabili errate');
$id = intval($_GET['id'],10);

switch($_GET['request']){
	case 1:
		if (!$id) jsonError("Variabili non acettabili");
		goto anchor_1;//only selected
	break;
	case 2:
		if (!$id) jsonError("Variabili non acettabili");
		goto anchor_2;//only non-selected
	break;
	default:
		goto anchor_0;//all in database
}



anchor_0://all in database

$pdostat = $pdo->prepare('SELECT immagini.id, immagini.src, link_album_immagini.idalbum as "select" FROM immagini
LEFT JOIN link_album_immagini ON
	link_album_immagini.idalbum = ? AND
	link_album_immagini.idimage = immagini.id
ORDER BY
	link_album_immagini.idalbum DESC,
	immagini.data DESC') or jsonError('Errore durante ricerca immagini [query]');
if (!$pdostat->execute([$id])) jsonError('Errore durante ricerca immagini [execute]');
if (!$images = $pdostat->fetchAll(PDO::FETCH_ASSOC)) jsonSuccess(['id' => $id, 'images' => []]);
jsonSuccess(['id' => $id, 'images' => $images]);



anchor_1://only selected

$pdostat = $pdo->prepare("SELECT id,src,data FROM immagini INNER JOIN link_album_immagini ON link_album_immagini.idalbum = ? AND link_album_immagini.idimage = immagini.id ORDER BY immagini.data DESC") or jsonError('Errore durante ricerca immagini [prepare]');
if (!$pdostat->execute([$id])) jsonError('Errore durante ricerca immagini [execute]');
if (!$images = $pdostat->fetchAll(PDO::FETCH_ASSOC)) jsonSuccess(['id' => $id, 'images' => []]);
jsonSuccess(['id' => $id, 'images' => $images]);



anchor_2://only non-selected

$pdostat = $pdo->prepare('SELECT id,src,data FROM immagini WHERE id NOT IN (SELECT id FROM immagini INNER JOIN link_album_immagini ON link_album_immagini.idalbum = ? and link_album_immagini.idimage = immagini.id) ORDER BY data DESC') or jsonError('Errore durante ricerca immagini [prepare]');
if (!$pdostat->execute([$id])) jsonError('Errore durante ricerca immagini [execute]');
if (!$images = $pdostat->fetchAll(PDO::FETCH_ASSOC)) jsonSuccess(['id' => $id, 'images' => []]);
jsonSuccess(['id' => $id, 'images' => $images]);

?>