<?php
header('Content-Type: application/json');

require_once "../../../config.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true);

if (!isset(
	$_POST['video_id'],
	$_POST['video_w'],
	$_POST['video_h'],
	$_POST['video_start'],
	$_POST['video_end'])
)
	jsonError("Variabili errate");

$id = intval($_POST['article_id'],10);
//update...

if (empty($_POST['video_id'])){
	//delete video
	$query = "DELETE FROM youtube WHERE idarticolo={$id}";
	$pdostat = $pdo->query($query) or jsonError("Impossibile eseguire rimozione video [query]");
	if (!$pdostat->rowCount()) jsonError('Nessun video da rimuovere');
	jsonSuccess($_POST);
}
else{
	//update/add video.
	$pdostat = $pdo->query("SELECT id FROM youtube WHERE idarticolo={$id} LIMIT 1",PDO::FETCH_ASSOC) or jsonError("Impossibile eseguire controllo video [query]");
	if ($res = $pdostat->fetch()){
		//update
		$idvideo = $res['id'];
		$pdostat->closeCursor();
		$query = [];
		$params = [];
		foreach($_POST as $k => $v){
			if ($v!=='' && $k !== 'article_id'){
				$query[] = str_replace("_","",$k).'=?';
				$params[] = $v;
			}
		}
		$query = "UPDATE youtube SET ".implode(",",$query)." WHERE id={$idvideo}";
		$pdostat = $pdo->prepare($query) or jsonError("Impossibile aggiornare video [prepare]");
		if (!$pdostat->execute($params)) jsonError('Errore durante aggiornamento video [execute]');
		if (!$pdostat->rowCount()) jsonError('Nessun video da aggiornare');
		jsonSuccess($_POST);
	}
	else{
		//add
		$pdostat->closeCursor();
		$query = '';
		$params = [];
		foreach($_POST as $k => $v){
			if ($v!=='' && $k !== 'article_id'){
				$query .= str_replace("_","",$k).',';
				$params[] = $v;
			}
		}
		$query = "INSERT INTO youtube (".$query."idarticolo) VALUES (".str_repeat("?,",count($params))."{$id})";
		$pdostat = $pdo->prepare($query) or jsonError("Impossibile inserire video [prepare]");
		if (!$pdostat->execute($params)) jsonError('Errore durante inserimento video [execute]');
		if (!$pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');
		jsonSuccess($_POST);
	}
}
?>