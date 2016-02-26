<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);

//control variables
if (!isset($_GET['title'], $_GET['id'], $_GET['image'])) jsonError('Variabili errate');

$titolo = trim($_GET['title']);
if (empty($titolo)) jsonError("Devi assegnare un nome all'album");
$image = trim($_GET['image']);
$id = intval($_GET['id'],10);


function addImagesToAlbum($images=[], $album=0){
	global $pdo;
	if (!isset($pdo) || !$album || empty($images)) return false;
	//add images to this album... not duplicate!!! see table TRIGGERS!!!
	$pdostat = $pdo->prepare("INSERT INTO link_album_immagini (idalbum,idimage) VALUES (?,?)") or jsonError('Errore durante aggiunta immagini album [prepare]');
	foreach ($images as &$imid){
		$imid = intval($imid,10);
		if (!$pdostat->execute([$album, $imid])) jsonError('Errore durante aggiunta immagini album [execute]');
	}
	unset($imid);
	$ids = implode(",",$images);
	$pdostat = $pdo->prepare("DELETE FROM link_album_immagini WHERE (idalbum = ? AND idimage NOT IN (".$ids."))") or jsonError('Errore durante rimozione immagini album [prepare]');
	if (!$pdostat->execute([$album])) jsonError('Errore durante rimozione immagini album [execute]');
	return true;
}


//controllo se il titolo esiste già (già occupato)
$pdostat = $pdo->prepare("SELECT id FROM immagini_albums WHERE titolo = ?") or jsonError('Errore durante controllo esistenza album [prepare]');
if (!$pdostat->execute([$titolo])) jsonError('Errore durante controllo esistenza album [execute]');
if ($res = $pdostat->fetch(PDO::FETCH_ASSOC)){
	if ($id != $res['id']) jsonError("Quest'album esiste già! Cambia il titolo.");
	$id = $res['id'];
	if ($image){
		$pdostat = $pdo->prepare("UPDATE immagini_albums SET titolo = ?, idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1) WHERE id = {$id}") or jsonError('Errore durante aggiornamento album [prepare]');
		if (!$pdostat->execute([$titolo, $_GET['image']])) jsonError('Errore durante aggiornamento album [execute]');
		if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare* (+img)');
		if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
		jsonSuccess(['img' => $_GET['image'], "success" => "update", 'id' => $id, 't' => $titolo]);
	}
	else{
		$pdostat = $pdo->prepare("UPDATE immagini_albums SET titolo = ?, idimage = NULL WHERE id = {$id}") or jsonError('Errore durante aggiornamento* album [prepare]');
		if (!$pdostat->execute([$titolo])) jsonError('Errore durante aggiornamento* album [execute]');
		if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare*');
		if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
		jsonSuccess(['img' => false, "success" => "update", 'id' => $id, 't' => $titolo]);
	}
}
else{
	//se il titolo non è già occupato...
	//modifica album esistente secondo ID (uguale a prima, cambia la ricerca dell'album)
	if ($id){
		if ($image){
			$pdostat = $pdo->prepare("UPDATE immagini_albums SET titolo = ?, idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1) WHERE id = {$id}") or jsonError('Errore durante aggiornamento album [prepare]');
			if (!$pdostat->execute([$titolo, $_GET['image']])) jsonError('Errore durante aggiornamento album [execute]');
			if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare (+img)');
			if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
			jsonSuccess(['img' => $_GET['image'], "success" => "update", 'id' => $id, 't' => $titolo]);
		}
		else{
			$pdostat = $pdo->prepare("UPDATE immagini_albums SET titolo = ?, idimage = NULL WHERE id = {$id}") or jsonError('Errore durante aggiornamento album [prepare]');
			if (!$pdostat->execute([$titolo])) jsonError('Errore durante aggiornamento album [execute]');
			if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare');
			if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
			jsonSuccess(['img' => false, "success" => "update", 'id' => $id, 't' => $titolo]);
		}
	}
	//oppure inserimento nuovo album
	else{
		if ($image){
			$pdostat = $pdo->prepare("INSERT INTO immagini_albums (titolo, idimage) VALUES (?, (SELECT id FROM immagini WHERE src = ? LIMIT 1))") or jsonError('Errore durante inserimento album [prepare]');
			if (!$pdostat->execute([$titolo, $image])) jsonError('Errore durante inserimento album [execute]');
			if (!$id = $pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');
			if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
			jsonSuccess(['img' => $_GET['image'], "success" => "insert", 'id' => $id, 't' => $titolo]);
		}
		else{
			$pdostat = $pdo->prepare("INSERT INTO immagini_albums (titolo) VALUES (?)") or jsonError('Errore durante inserimento album [prepare]');
			if (!$pdostat->execute([$titolo])) jsonError('Errore durante inserimento album [execute]');
			if (!$id = $pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');
			if (isset($_GET['IMAGES'])) addImagesToAlbum($_GET['IMAGES'], $id);
			jsonSuccess(['img' => false, "success" => "insert", 'id' => $id, 't' => $titolo]);
		}
	}
}
?>