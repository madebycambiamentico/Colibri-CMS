<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true);



//control variables
if (!isset($_GET['template']))
	jsonError('Variabili errate');
$template = preg_replace('/\s+|\.+|\/+/',' ',trim($_GET['template']));
if (!@is_dir("../templates/{$template}"))
	jsonError("This template doesn't exists anymore!");


//copy previous
$params = [];
$pdostat = $pdo->query("SELECT template, autore, descr, email, info, motto, recaptcha_key, recaptcha_secret, titolo FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_NUM);
if ($params = $pdostat->fetch()){
	if ($params[0] == $template)
		jsonError("Template already running!");
	$params[0] = $template;
}
else
	jsonError("Couldn't find current site properties.");


$query = "INSERT INTO sito (template, autore, descr, email, info, motto, recaptcha_key, recaptcha_secret, titolo) VALUES (?,?,?,?,?,?,?,?,?)";

//run edit
$pdostat = $pdo->prepare($query) or jsonError('Errore durante modifica sito [prepare]');
if (!$pdostat->execute($params)) jsonError('Errore durante modifica sito [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun articolo da ripristinare');
jsonSuccess(['new' => $params]);


?>