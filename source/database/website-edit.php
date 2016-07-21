<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
$SessionManager = new \Colibri\SessionManager;
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
	multilanguage = ?,
	delivery_quantity=?, delivery_delay=?,
	email=?";
$params = [];

//id of current saved properties
$id = intval($_POST['id'],10);

//common properties
$prop = [
	preg_replace('/\s+/',' ',trim($_POST['author'])),		//site owner
	preg_replace('/\s+/',' ',trim($_POST['title'])),		//site title
	preg_replace('/\s+/',' ',trim($_POST['descr'])),		//brief description
	preg_replace('/\s+/',' ',trim($_POST['quote'])),		//motto
	trim($_POST['info']),											//info (optional)
	isset($_POST['multilanguage']),								//is this site multi-language?
	max(0,intval($_POST['delivery']['n'],10)),				//n. of email to delivery every interval (if set)
	min(3600,max(0,intval($_POST['delivery']['t'],10)))	//interval in seconds [0 - 3600]
];
foreach($prop as $p){ $params[] = $p; }
unset($prop);


$Encrypter = new \Colibri\Encrypter( $CONFIG['encrypt']['secret_key'] );

//control email
$email = trim($_POST['email']);
if ($email!==''){
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('e-mail non accettabile');
	$email = $Encrypter->encrypt($email);
}
$params[] = $email;

//recaptcha keys
$recaptcha = [
	'k' => trim($_POST['rcptc']['k']),	//public key
	's' => trim($_POST['rcptc']['s'])	//private key (will be encrypted)
];
if (!empty($recaptcha['k']) && !empty($recaptcha['s'])){
	$query .= ", recaptcha_key=?, recaptcha_secret=?";
	$params[] = $recaptcha['k'];
	$params[] = $Encrypter->encrypt($recaptcha['s']);
}
elseif (empty($recaptcha['k'])){
	$query .= ", recaptcha_key=?, recaptcha_secret=?";
	$params[] = '';
	$params[] = '';
}
// (else: do not update keys)

$query .= " WHERE id={$id}";



//run edit
$pdostat = $pdo->prepare($query) or jsonError('Errore durante modifica sito [prepare]');
if (!$pdostat->execute($params)) jsonError('Errore durante modifica sito [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun sito da ripristinare');
jsonSuccess();


?>