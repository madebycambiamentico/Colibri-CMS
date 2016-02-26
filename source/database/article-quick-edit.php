<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);

//controllo generale variabili
if (!isset(
		$_POST['id'],
		$_POST['title'],
		$_POST['image'],
		$_POST['map'],
		$_POST['description'],
		$_POST['wasindex'])
	){
		//echo '<pre>'.print_r($_POST,true).'</pre>';
		jsonError('Variabili errate');
	}

//controllo variabili !== empty
$id			= intval($_POST['id'],10);
	if (!$id) jsonError('Variabili errate');
$titolo		= trim($_POST['title']);
$map			= trim($_POST['map']);
if (empty($_POST['title']) ||
	empty($_POST['map'])
	) jsonError("Controlla di aver correttamente assegnato titolo e contenuto");

//controllo variabili opzionali
$wasindex	= intval($_POST['wasindex'],10);
$desc			= preg_replace("/\s+/"," ",trim($_POST['description']));
$image		= trim($_POST['image']);

//controllo checkbox
$isinmenu	= isset($_POST['isinmenu']) ? 1 : 0;
$isindex		= isset($_POST['isindex']) ? 1 : 0;


if ($isindex && !$wasindex){
	//remove previous isindex to prevent conflicts
	$pdores = $pdo->query("UPDATE articoli SET isindex=0 WHERE isindex") or jsonError('Errore durante modifica articoli in conflitto [query]');
	$pdores->closeCursor();
}


$userid = $_SESSION['uid'];


//preparazione parametri + query
$params = [$titolo, $desc, $map];
$query = "UPDATE articoli SET
	titolo = ?, inbreve = ?, remaplink = ?,
	ideditor = {$userid},
	dataedit = CURRENT_TIMESTAMP, isindex = {$isindex}, isinmenu = {$isinmenu}";
if (!empty($image)){
	$query .= ", idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1)";
	$params[] = $image;
}
else{
	$query .= ", idimage = NULL";
}
$query .= " WHERE id = {$id}";
//exit($query);
//UPDATE
$pdostat = $pdo->prepare($query) or jsonError('Errore durante aggiornamento articolo [prepare]');
if (!$pdostat->execute($params)) jsonError('Errore durante aggiornamento articolo [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun articolo da aggiornare');
jsonSuccess([
	"success" => "update",
	'id' => $id,
	'title' => $params[0],
	'desc' => $params[1],
	'img' => $image,
	'idx' => $isindex,
	'mnu' => $isinmenu
]);
?>