<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);



//control variables
if (!isset($_POST['trash'])) jsonError('Variabili errate');
if (!is_array($_POST['trash'])) jsonError("Variabili non acettabili");
$ids = [];
foreach ($_POST['trash'] as $id){
	$id = intval($id,10);
	if ($id) $ids[] = $id;
}
if (empty($ids)) jsonError("Variabili non acettabili");
$in = str_repeat('?,', count($ids)-1) . '?';



//ripristino dal cestino (FLAG isgarbage = false)
//NB - per evitare conflitti annullo possibilità che sia INDEX
$pdostat = $pdo->prepare("UPDATE articoli SET isgarbage=0, isindex=0 WHERE id IN ({$in})") or jsonError('Errore durante ripristino articoli [prepare]');
if (!$pdostat->execute($ids)) jsonError('Errore durante ripristino articoli [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun articolo da ripristinare');
jsonSuccess(['ids' => $ids]);


?>