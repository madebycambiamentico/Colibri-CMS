<?php
header('Content-Type: application/json');

require_once "../config.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

$PlugManager = new \Colibri\PluginsManager(false, 'editor', ['active' => true]);

//controllo generale variabili
if (!isset(
		$_POST['id'],
		$_POST['title'],
		$_POST['content'],
		$_POST['description'],
		$_POST['image'],
		$_POST['wasindex'],
		$_POST['wasindexlang'],
		$_POST['parentlang'],
		$_POST['map'],
		$_POST['type'],
		$_POST['parent'],
		$_POST['lang']
	)){
		jsonError('Variabili errate '.print_r(array_keys($_POST),true));
	}


$reserved_titles = ['signin','iscriviti','login','accedi','new','editor','nuovo','albums','options','opzioni','profile','profilo','profiles','profili','articles','articoli','dashboard','bacheca'];


//controllo variabili !== empty
$titolo		= trim($_POST['title']);
$map			= trim($_POST['map']);
$corpo		= trim($_POST['content']);
	//if (!$corpo) $corpo = '&nbsp;' //allow empty content, substitute with blank space
$type			= intval($_POST['type'],10);
if (empty($titolo) || empty($map) || empty($_POST['type']))
	jsonError("Controlla di aver correttamente assegnato titolo e contenuto");
if (in_array($titolo,$reserved_titles) || is_file(CMS_INSTALL_DIR.'/'.$map))
	jsonError("Questo titolo è riservato! Cambialo.");

	
//controllo variabili opzionali
$id			= intval($_POST['id'],10);
$idparent	= intval($_POST['parent'],10);
	if (!$idparent) $idparent = 'NULL';
$wasindex	= intval($_POST['wasindex'],10);
$wasindexlang = intval($_POST['wasindexlang'],10);
$desc			= preg_replace("/\s+/"," ",trim($_POST['description']));
$image		= trim($_POST['image']);
$lang			= trim($_POST['lang']);
	if (strlen($lang) < 2 || strlen($lang) > 5) $lang = '';
$idparentlang = intval($_POST['parentlang'],10);
	if (!$idparentlang) $idparentlang = 'NULL';


//controllo checkbox
$idalbum		= isset($_POST['album']) ? intval($_POST['album'],10) : 'NULL';
	if (!$idalbum) $idalbum = 'NULL';
$isinmenu	= isset($_POST['isinmenu']) ? 1 : 0;
$isindex		= isset($_POST['isindex']) ? 1 : 0;
$isindexlang = isset($_POST['isindexlang']) ? 1 : 0;




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



//controllo conflitti INDEX
//remove previous isindex (and/or isindexlang) to prevent conflicts
if ( ($isindex && !$wasindex) ){
	$pdores = $pdo->query("UPDATE articoli SET isindex = 0 WHERE isindex") or jsonError('Errore durante modifica articoli in conflitto [query]');
	$pdores->closeCursor();
}
if ( ($isindexlang && !$wasindexlang) ){
	$pdostat = $pdo->prepare("UPDATE articoli SET isindexlang = 0 WHERE isindexlang AND lang = ?") or jsonError('Errore durante modifica articoli in conflitto [prepare]');
	if (!$pdostat->execute([$lang])) jsonError('Errore durante modifica articoli in conflitto [execute]');
}


$userid = $_SESSION['uid'];


if ($id){
	// -------------------------- UPDATE --------------------------
	if ($idwithmap && $idwithmap!=$id)
		jsonError("Questo articolo esiste già: cambia il titolo.");
	
	//preparazione parametri + query
	$params = [$titolo, $corpo, $desc, $map, $lang];
	$query = "UPDATE articoli SET
		titolo = ?, corpo = ?, inbreve = ?, remaplink = ?, lang = ?,
		ideditor = {$userid},
		dataedit = CURRENT_TIMESTAMP, isindex = {$isindex}, isinmenu = {$isinmenu},
		idtype = {$type}, idarticolo = {$idparent}, idalbum = {$idalbum},
		isindexlang = {$isindexlang}, idarticololang = {$idparentlang}";
	if (!empty($image)){
		$query .= ", idimage = (SELECT id FROM immagini WHERE src = ? LIMIT 1)";
		$params[] = $image;
	}
	
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	// allow plugins to do some magick to query
	// they could add something to $query
	// in form ", <custom_column> = ?". update $params!!!
	$PlugManager->run_plugins( 'db' );
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	
	$query .= " WHERE id = {$id}";
	//exit($query);
	//UPDATE
	$pdostat = $pdo->prepare($query) or jsonError('Errore durante aggiornamento articolo [prepare]');
	if (!$pdostat->execute($params)) jsonError('Errore durante aggiornamento articolo [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun articolo da aggiornare');
	
	//rimozione articoli correlati
	if (!empty($_POST['removelinkedart'])){
		if (is_array($_POST['removelinkedart'])){
			$count = count($_POST['removelinkedart']);
			if ($count > 1){
				$query = "UPDATE articoli SET idarticolo = NULL WHERE id IN (".str_repeat("?,", $count-1)."?)";
			}
			else{
				$query = "UPDATE articoli SET idarticolo = NULL WHERE id=?";
			}
			$pdostat = $pdo->prepare($query) or jsonError('Errore durante rimozione sub-articoli [prepare]');
			if (!$pdostat->execute($_POST['removelinkedart'])) jsonError('Errore durante rimozione sub-articoli [execute]');
			if (!$pdostat->rowCount()) jsonError('Nessun sub-articolo da rimuovere');
		}
	}
	
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	// allow plugins to do some magick when the normal query
	// has already run succesfully
	$PlugManager->run_plugins( 'postdb' );
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	
	jsonSuccess(["success" => "update", 'id' => intval($id,10)]);
}
else{
	// -------------------------- INSERT --------------------------
	if ($idwithmap)
		jsonError("Questo articolo esiste già: cambia il titolo.");
	
	//preparazione parametri + query
	$params = [$titolo, $corpo, $desc, $map, $lang];
	if (!empty($image)){
		$params[] = $image;
	}
	$query_insert = "INSERT INTO articoli (titolo, corpo, inbreve, remaplink, lang, idowner, ideditor, isindex, isinmenu, idtype, idarticolo, idalbum, isindexlang, idarticololang".
			(empty($image) ? '' : ', idimage');
	$query_values = " VALUES(?, ?, ?, ?, ?, {$userid}, {$userid}, {$isindex}, {$isinmenu}, {$type}, {$idparent}, {$idalbum}, {$isindexlang}, {$idparentlang}".
			(empty($image) ? '' : ", (SELECT id FROM immagini WHERE src = ? LIMIT 1)");
	
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	// allow plugins to do some magick to query
	// they could add something to $query_insert and $query_vaulues
	// in form ", <custom_column>" and ",?". update $params!!!
	$PlugManager->run_plugins( 'db' );
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	
	$query_insert .= ")";
	$query_values .= ")";
	//exit($query);
	//INSERT
	$pdostat = $pdo->prepare($query) or jsonError('Errore durante inserimento articolo [prepare]');
	if (!$pdostat->execute($params)) jsonError('Errore durante inserimento articolo [execute]');
	if (!$id = $pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');
	
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	// allow plugins to do some magick when the normal query
	// has already run succesfully
	$PlugManager->run_plugins( 'postdb' );
	//'''''''''''''''''''''''''''''''''''''''''''''''''''''''
	
	jsonSuccess(["success" => "insert", 'id' => intval($id,10)]);
}

?>