<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

//control variables
if (!isset($_GET['id']) || !isset($_GET['image'])) jsonError('Variabili errate');
$id = intval($_GET['id'],10);
if (!$id) jsonError("Variabili non acettabili");

//controllo input
$image = trim($_GET['image']);

if ($image === ''){
	$pdostat = $pdo->query("UPDATE immagini_albums SET idimage = NULL WHERE id = {$id}") or jsonError('Errore durante aggiornamento album [query]');
	if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare');
	jsonSuccess(["success" => "update", 'id' => $id, "img" => false]);
}
else{
	$pdostat = $pdo->prepare("UPDATE immagini_albums SET idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1) WHERE id = {$id}") or jsonError('Errore durante aggiornamento album [prepare]');
	if (!$pdostat->execute([$_GET['image']])) jsonError('Errore durante aggiornamento album [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare');
	jsonSuccess(["success" => "update", 'id' => $id, "img" => $_GET['image']]);
}



?>