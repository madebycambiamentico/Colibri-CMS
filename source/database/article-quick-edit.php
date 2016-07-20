<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

//controllo generale variabili
if (!isset(
		$_POST['id'],
		$_POST['title'],
		$_POST['image'],
		$_POST['map'],
		$_POST['description'],
		$_POST['wasindex'],
		$_POST['wasindexlang'],
		$_POST['lang'])
	){
		//echo '<pre>'.print_r($_POST,true).'</pre>';
		jsonError('Variabili errate');
}


$reserved_titles = ['signin','iscriviti','login','accedi','new','editor','nuovo','albums','options','opzioni','profile','profilo','profiles','profili','articles','articoli','dashboard','bacheca'];


//controllo variabili !== empty
$id			= intval($_POST['id'],10);
	if (!$id) jsonError('Variabili errate');
$titolo		= trim($_POST['title']);
$map			= trim($_POST['map']);
if (empty($titolo) || empty($map))
	jsonError("Controlla di aver correttamente assegnato titolo e contenuto");
if (in_array($titolo,$reserved_titles))
	jsonError("Questo titolo è riservato! Cambialo.");
$lang			= trim($_POST['lang']);

//controllo variabili opzionali
$wasindex	= intval($_POST['wasindex'],10);
$desc			= preg_replace("/\s+/"," ",trim($_POST['description']));
$image		= trim($_POST['image']);

//controllo checkbox
$isinmenu	= isset($_POST['isinmenu']) ? 1 : 0;
$isindex		= isset($_POST['isindex']) ? 1 : 0;
$isindexlang	= isset($_POST['isindexlang']) ? 1 : 0;


if ($isindex && !$wasindex){
	//remove previous isindex to prevent conflicts
	$pdores = $pdo->query("UPDATE articoli SET isindex=0 WHERE isindex") or jsonError('Errore durante modifica articoli in conflitto [query]');
	$pdores->closeCursor();
}


$userid = $_SESSION['uid'];


//preparazione parametri + query
$params = [$titolo, $desc, $map, $lang];
$query = "UPDATE articoli SET
	titolo = ?, inbreve = ?, remaplink = ?, lang = ?,
	ideditor = {$userid},
	dataedit = CURRENT_TIMESTAMP,
	isindex = {$isindex}, isinmenu = {$isinmenu}, isindexlang = {$isindexlang}";
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
	'mnu' => $isinmenu,
	'idxl' => $isindexlang,
	'lang' => $lang
]);
?>