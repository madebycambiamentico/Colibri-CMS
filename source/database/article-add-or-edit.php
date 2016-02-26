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
		$_POST['type'],
		$_POST['image'],
		$_POST['map'],
		$_POST['parent'],
		$_POST['content'],
		$_POST['description'],
		$_POST['wasindex'])
	){
		//echo '<pre>'.print_r($_POST,true).'</pre>';
		jsonError('Variabili errate');
	}

//controllo variabili !== empty
$titolo		= trim($_POST['title']);
$map			= trim($_POST['map']);
$corpo		= trim($_POST['content']);
$type			= intval($_POST['type'],10);
if (empty($_POST['title']) ||
	empty($_POST['type']) ||
	empty($_POST['map']) ||
	empty($_POST['content'])
	) jsonError("Controlla di aver correttamente assegnato titolo e contenuto");

//controllo variabili opzionali
$id			= intval($_POST['id'],10);
$idparent	= intval($_POST['parent'],10);
	if (!$idparent) $idparent = 'NULL';
$wasindex	= intval($_POST['wasindex'],10);
$desc			= preg_replace("/\s+/"," ",trim($_POST['description']));
$image		= trim($_POST['image']);



//controllo checkbox
$idalbum		= isset($_POST['album']) ? intval($_POST['album'],10) : 'NULL';
	if (!$idalbum) $idalbum = 'NULL';
$isinmenu	= isset($_POST['isinmenu']) ? 1 : 0;
$isindex		= isset($_POST['isindex']) ? 1 : 0;

if ($isindex && !$wasindex){
	//remove previous isindex to prevent conflicts
	$pdores = $pdo->query("UPDATE articoli SET isindex=0 WHERE isindex") or jsonError('Errore durante modifica articoli in conflitto [query]');
	$pdores->closeCursor();
}


//control if remaplink not in use: get current id (if exists)
//there can be multiple article with same title, but not with same type!
//for example, this ones can co-exist:
/*
news/my-title/
my-title/
links/my-title/
*/
$idwithmap = null;
$pdostat = $pdo->prepare("SELECT id FROM articoli WHERE remaplink=? LIMIT 1") or jsonError('Errore durante controllo articolo [prepare]');
if (!$pdostat->execute([$map])) jsonError('Errore durante controllo articolo [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC)) $idwithmap = $r['id'];


$userid = $_SESSION['uid'];


if ($id){
	if ($idwithmap && $idwithmap!=$id)
		jsonError("Questo articolo esiste già: cambia il titolo.");
	
	//preparazione parametri + query
	$params = [$titolo, $corpo, $desc, $map];
	$query = "UPDATE articoli SET
		titolo = ?, corpo = ?, inbreve = ?, remaplink = ?,
		ideditor = {$userid},
		dataedit = CURRENT_TIMESTAMP, isindex = {$isindex}, isinmenu = {$isinmenu},
		idtype = {$type}, idarticolo = {$idparent}, idalbum = {$idalbum}";
	if (!empty($image)){
		$query .= ", idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1)";
		$params[] = $image;
	}
	$query .= " WHERE id = {$id}";
	//exit($query);
	//UPDATE
	$pdostat = $pdo->prepare($query) or jsonError('Errore durante aggiornamento articolo [prepare]');
	if (!$pdostat->execute($params)) jsonError('Errore durante aggiornamento articolo [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun articolo da aggiornare');
	jsonSuccess(["success" => "update", 'id' => intval($id,10)]);
}
else{
	if ($idwithmap)
		jsonError("Questo articolo esiste già: cambia il titolo.");
	
	//preparazione parametri + query
	$params = [$titolo, $corpo, $desc, $map];
	if (!empty($image)){
		$params[] = $image;
	}
	$query = "INSERT INTO articoli
		(titolo, corpo, inbreve, remaplink, idowner, ideditor, isindex, isinmenu, idtype, idarticolo, idalbum".
			(empty($image) ? '' : ', idimage').")
		VALUES(?, ?, ?, ?, {$userid}, {$userid}, {$isindex}, {$isinmenu}, {$type}, {$idparent}, {$idalbum}".
			(empty($image) ? '' : ", (SELECT id FROM immagini WHERE src = ? LIMIT 1)").")";
	//exit($query);
	//INSERT
	$pdostat = $pdo->prepare($query) or jsonError('Errore durante inserimento articolo [prepare]');
	if (!$pdostat->execute($params)) jsonError('Errore durante inserimento articolo [execute]');
	if (!$id = $pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');
	jsonSuccess(["success" => "insert", 'id' => intval($id,10)]);
}

?>