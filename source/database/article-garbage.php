<?php
header('Content-Type: application/json');

require_once "../config.php";
$Config->i_need_functions();

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



if (isset($_POST['isgarbage'])){
	//cancellazione definitiva
	$pdostat = $pdo->prepare("DELETE FROM articoli WHERE id IN ({$in})") or jsonError('Errore durante cancellazione articoli [prepare]');
	if (!$pdostat->execute($ids)) jsonError('Errore durante cancellazione articoli [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun articolo da cancellare');
	jsonSuccess(['ids' => $ids]);
}
else{
	//sposto nel cestino (ovvero imposto FLAG isgarbage = true)
	$pdostat = $pdo->prepare("UPDATE articoli SET isgarbage=1 WHERE id IN ({$in})") or jsonError('Errore durante cestinamento articoli [prepare]');
	if (!$pdostat->execute($ids)) jsonError('Errore durante cestinamento articoli [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun articolo da cestinare');
	jsonSuccess(['ids' => $ids]);
}

?>