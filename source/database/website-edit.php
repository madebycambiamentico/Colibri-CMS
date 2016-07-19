<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);



//control variables
if (!isset(
	$_POST['id'],
	$_POST['title'],
	$_POST['author'],
	$_POST['descr'],
	$_POST['quote'],
	$_POST['info'],
	$_POST['email'],
	$_POST['rcptc'],
	$_POST['delivery']
	))
	jsonError('Variabili errate');

if (!isset($_POST['rcptc']['k'], $_POST['rcptc']['s']))
	jsonError('Variabili reCAPTCHA mancanti');

if (!isset($_POST['delivery']['n'], $_POST['delivery']['t']))
	jsonError('Variabili gestione mail mancanti');

$query = "UPDATE sito SET
	autore=?, titolo=?, descr=?, motto=?, info=?,
	delivery_quantity=?, delivery_delay=?,
	email=?";
$params = [];

//id of current saved properties
$id = intval($_POST['id'],10);

//common properties
$prop = [
	preg_replace('/\s+/',' ',trim($_POST['author'])),
	preg_replace('/\s+/',' ',trim($_POST['title'])),
	preg_replace('/\s+/',' ',trim($_POST['descr'])),
	preg_replace('/\s+/',' ',trim($_POST['quote'])),
	trim($_POST['info']),
	max(0,intval($_POST['delivery']['n'],10)),
	min(3600,max(0,intval($_POST['delivery']['t'],10)))
];
foreach($prop as $p){ $params[] = $p; }
unset($prop);

require_once "../php/encrypter.class.php";
	$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );

//email
$email = trim($_POST['email']);
if ($email!==''){
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('e-mail non accettabile');
	$email = $ENCRYPTER->encrypt($email);
}
$params[] = $email;

//recaptcha keys
$recaptcha = [
	'k' => trim($_POST['rcptc']['k']),	//public key
	's' => trim($_POST['rcptc']['s'])	//private key
];
if (!empty($recaptcha['k']) && !empty($recaptcha['s'])){
	$query .= ", recaptcha_key=?, recaptcha_secret=?";
	$params[] = $recaptcha['k'];
	$params[] = $recaptcha['s'];
}
elseif (empty($recaptcha['k'])){
	$query .= ", recaptcha_key=?, recaptcha_secret=?";
	$params[] = '';
	$params[] = '';
}
//(else do not update keys)

$query .= " WHERE id={$id}";



//run edit
$pdostat = $pdo->prepare($query) or jsonError('Errore durante modifica sito [prepare]');
if (!$pdostat->execute($params)) jsonError('Errore durante modifica sito [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun sito da ripristinare');
jsonSuccess();


?>