<?php
header('Content-Type: application/json');

/**
* add 1 comment to database
*/

require_once "../../../../config.php";


//check if plugin enabled / activated
$PlugManager = new \Colibri\PluginsManager(false);
$PlugManager->get_plugins_status();
if (!isset($PlugManager->available['Colibri/Comments']))
	jsonError("This plugin is not installed.");
if (!$PlugManager->available['Colibri/Comments']['active'])
	jsonError("This plugin is not active.");







$query_pre = "INSERT INTO commenti (idarticolo, content, idcommento";
$query_post = ") VALUES (?, ?";

//check common variables
if (!isset(
	$_POST['pageid'],
	$_POST['comment'],
	$_POST['commentid']
)){
	jsonError("Wrong variables.");
}
//verify variables
$params = [
	intval($_POST['pageid'],10),
	trim($_POST['comment'])
];

$temp = intval($_POST['commentid'],10);
if (!$temp)
	$query_post .= ', NULL';
else{
	$query_post .= ', ?';
	$params[] = $temp;
}

if (empty($params[0]) || empty($params[1]))
	jsonError("Alcuni campi obbligatori risultano mancanti.");

//purify comment
//expected string should be MARKDOWN, but we allow some basic html anyway.
require_once CMS_INSTALL_DIR . '/php/htmlpurifier-lite/library/HTMLPurifier.auto.php';
$PurifierConfig = HTMLPurifier_Config::createDefault();
$PurifierConfig->set('HTML.Allowed',"b,strong,i,em,ul,ol,li,img[src|alt],span");
$Purifier = new HTMLPurifier( $PurifierConfig );
$params[1] = trim( $Purifier->purify( $params[1] ) );
if (empty($params[1]))
	jsonError("Campo commento non valido.");


$author = [
	'id' => null,
	'name' => '',
	'hasimage' => false,
	'website' => ''
];

//check variables for NON LOGGED USERS
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
if (!isset($_SESSION['uid'])){
	if (!isset(
		$_POST['name'],
		$_POST['website'],
		$_POST['email']
	)){
		jsonError("Wrong variables.");
	}
	//verify variables
	$author['name'] = trim($_POST['name']);
		if (empty($author['name']) || $author['name']<4)
			jsonError("Il tuo nome è troppo corto!");
		$params[] = $author['name'];
	$email = trim($_POST['email']);
		if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL))
			jsonError("La tua e-mail non è valida!");
		$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );
		$email = $Encrypter->encrypt($email);
		$params[] = $email;
	$params[] = trim($_POST['website']);
	$query_pre .= ", idutente, name, email, website";
	$query_post .= ", NULL, ?, ?, ?";
}
else{
	$author['id'] = $_SESSION['uid'];
	$query_pre .= ", idutente, name, email, website";
	$query_post .= ", ?, NULL, NULL, NULL";
	$params[] = $author['id'];
}

//check if website allow commenting, if this page allow comment, and check min user class to comment.
$pdostat = $pdo->prepare("SELECT
	comment_allow,
	comment_class,
	(SELECT comment_allow FROM articoli WHERE id=?) as 'comment_art_allow'
	FROM sito"
) or jsonError('Errore durante controllo abilitazioni [prepare]');
if (!$pdostat->execute([ $params[0] ]))
	jsonError('Errore durante controllo abilitazioni [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC)){
	if (!$r['comment_allow'] ||
		!$r['comment_art_allow'] ||
		($r['comment_class'] != -1 &&
			(!isset($_SESSION['uid']) ||
			$_SESSION['uclass'] < $r['comment_class'])
		)
	)
		jsonError('You are not allowed to comment.');
}
else{
	jsonError('Database broken or plugin not properly installed...');
}

//update author properties if logged in
if (isset($_SESSION['uid'])){
	$pdores = $pdo->query("SELECT id, nome as 'name', hasimage FROM utenti WHERE id = {$_SESSION['uid']}", PDO::FETCH_ASSOC) or
		jsonError('Impossibile cercare proprietà utente [query]');
	if ($r = $pdores->fetch()){
		$author = array_merge($author,$r);
	}
	else
		jsonError("L'utente non risulta più presente nel database.");
}


//ok, you can post.
$query = $query_pre . $query_post . ")";
//jsonError($query . print_r($params,true));

//INSERT
$pdostat = $pdo->prepare($query) or jsonError('Errore durante inserimento commento [prepare]');
if (!$pdostat->execute($params)) jsonError('Errore durante inserimento commento [execute]');
if (!$cid = $pdo->lastInsertId()) jsonError('Nessun inserimento effettuato (0)"');


function get_locale_date($loc_code='it_IT', $format="%d %B %Y"){
	setlocale(LC_TIME,$loc_code);	// set custom language
	$date = strftime($format);		// get string date (now)
	setlocale(LC_TIME,false);		// reset locale language
	return $date;
}

jsonSuccess([
	'comment' => [
		'cid' => $cid,
		'c' => $params[1],
		'a' => $author,
		'd' => get_locale_date()
	]
]);

?>