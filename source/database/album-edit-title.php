<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

//control variables
if (!isset($_GET['id']) || !isset($_GET['title'])) jsonError('Variabili errate');
$id = intval($_GET['id'],10);
if (!$id) jsonError("Variabili non acettabili");


$pdostat = $pdo->prepare("UPDATE immagini_albums SET titolo = ? WHERE id = ?") or jsonError('Errore durante aggiornamento album [prepare]');
if (!$pdostat->execute([$_GET['title'], $id])) jsonError('Errore durante aggiornamento album [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun album da aggiornare');
jsonSuccess(["success" => "update", 'id' => $id, 't' => $_GET['title']]);

?>