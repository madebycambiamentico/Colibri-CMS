<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);

//control variables
if (!isset($_GET['id'])) jsonError('Variabili errate');
$id = intval($_GET['id'],10);
if (!$id) jsonError("Variabili non acettabili");

$pdostat = $pdo->prepare("DELETE FROM immagini_albums WHERE id = ?") or jsonError('Errore durante cancellazione album [prepare]');
if (!$pdostat->execute([$id])) jsonError('Errore durante cancellazione album [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun album da cancellare');
jsonSuccess(["success" => "delete", 'id' => $id]);

?>