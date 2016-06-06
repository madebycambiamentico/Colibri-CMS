<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
require_once "../php/sessionmanager.class.php";
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1,true);



//control variables
if (!isset(
	$_POST['id'],
	$_POST['title'],
	$_POST['author'],
	$_POST['descr'],
	$_POST['quote'],
	$_POST['info'],
	$_POST['email'],
	$_POST['rcptc']
	))
	jsonError('Variabili errate');

if (!isset($_POST['rcptc']['k'], $_POST['rcptc']['s']))
	jsonError('Variabili reCAPTCHA mancanti');

$query = "UPDATE sito SET
autore=?, titolo=?, descr=?, motto=?, info=?, email=?, multilanguage=?";
$params = [];


//id of current saved properties
$id = intval($_POST['id'],10);


//common properties
$prop = [
	'a' => preg_replace('/\s+/',' ',trim($_POST['author'])),
	't' => preg_replace('/\s+/',' ',trim($_POST['title'])),
	'd' => preg_replace('/\s+/',' ',trim($_POST['descr'])),
	'q' => preg_replace('/\s+/',' ',trim($_POST['quote'])),
	'i' => trim($_POST['info'])
];
foreach($prop as $p){ $params[] = $p; }


//email
$email = trim($_POST['email']);
if ($email!==''){
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('e-mail non accettabile');
	require_once "../php/encrypter.class.php";
	$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
	$email = $ENCRYPTER->encrypt($email);
}
$params[] = $email;


//is multilanguage?
$params[] = isset($_POST['multilang']) ? 1 : 0;


//recaptcha keys
$recaptcha = [
	'k' => trim($_POST['rcptc']['k']),		//public key
	's' => trim($_POST['rcptc']['s'])	//private key
];
if (!empty($recaptcha['k']) && !empty($recaptcha['s'])){
	require_once "../php/encrypter.class.php";
	$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
	$recaptcha['s'] = $ENCRYPTER->encrypt($recaptcha['s']);
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
if (!$pdostat->rowCount()) jsonError('Database corrotto: nessun dato da aggiornare');
jsonSuccess();


?>