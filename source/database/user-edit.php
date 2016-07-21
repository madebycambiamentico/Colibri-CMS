<?php
header('Content-Type: application/json');

require_once "../config.php";
$Config->i_need_functions();

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(0,true);

/*
tipo di update:
- pass
- email
- image
*/

if (!isset($_POST['action'])){
	//echo '<pre>'.print_r($_POST,true).'</pre>';
	jsonError('Variabili errate');
}
$id = $_SESSION['uid'];

//GOTOS
switch ($_POST['action']){
	case 'pass':	goto anchor_0; break;
	case 'email':	goto anchor_1; break;
}
	






anchor_0:

//controllo generale variabili
if (!isset(
		$_POST['p0'],
		$_POST['p1'],
		$_POST['hint'])
	){
		//echo '<pre>'.print_r($_POST,true).'</pre>';
		jsonError('Variabili errate');
	}

//user properties
$user = null;
$pdores = $pdo->query("SELECT * FROM utenti WHERE id = {$id}", PDO::FETCH_ASSOC) or jsonError('Errore durante ricerca utente [query]');
if (!$user = $pdores->fetch()) jsonError("Utente non esistente");
$pdores->closeCursor();

//control pass+hash == hashpass from database
if ( hash('sha512', $_POST['p0'].$user['salt']) !== $user['pass'] ) jsonError('Non hai inserito la vecchia password correttamente');

//create pass + hash...
$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
$password = hash('sha512', $_POST['p1'].$random_salt);
//password hint
$hint = preg_replace("/\s+/"," ",trim($_POST['hint']));

//UPDATE
$pdostat = $pdo->prepare("UPDATE utenti SET salt=?, pass=?, passhint=? WHERE id={$id}") or jsonError('Errore durante aggiornamento utente [prepare]');
if (!$pdostat->execute([$random_salt, $password, $hint])) jsonError('Errore durante aggiornamento utente [execute]');
if (!$pdostat->rowCount()) jsonError('Nessun utente da aggiornare');
jsonSuccess(['count' => $pdostat->rowCount(), 'p' => $password, 'pp' => $_POST['p1']]);







anchor_1:


$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );

//controllo generale variabili
if (!isset($_POST['e0'], $_POST['e1'])) jsonError('Variabili errate');
$oldemail = trim($_POST['e0']);
$email = trim($_POST['e1']);
$oldencrypted = null;
$encrypted = null;
$hintemail = 'nessuna';

//controllo filtro email
if ($email !== ''){
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('e-mail non accettabile');
	//delete all character, leave first, @ and last
	$emailparts = explode('@',$email);
	$hintemail = $emailparts[0][0] . str_repeat("*", mb_strlen($emailparts[0])-1) . '@' . str_repeat("*", mb_strlen($emailparts[1])-1) . mb_substr($emailparts[1],-1);
	//encrypt email
	$encrypted = $Encrypter->encrypt($email);
}

//ricavo email vecchia e confronto con quella mandata
$pdostat = $pdo->query("SELECT email FROM utenti WHERE id={$id}",PDO::FETCH_ASSOC) or jsonError('Errore durante ricerca utente [query]');
if ($r = $pdostat->fetch()){
	if ($r['email']){
		if ($Encrypter->decrypt($r['email']) !== $oldemail)
			jsonError('La vecchia mail non coincide.');
	}
	elseif ($oldemail !== '')
		jsonError('La vecchia mail non coincide.');
}
else
	jsonError('Nessun utente trovato');

//------------------------------------------------------
if ($encrypted !== null){
	//controllo che l'email non sia già utilizzata
	$pdostat = $pdo->prepare("SELECT id FROM utenti WHERE email=? LIMIT 1") or jsonError('Errore durante ricerca utente [prepare]');
	if (!$pdostat->execute([$encrypted])) jsonError('Errore durante ricerca utente [execute]');
	if ($r = $pdostat->fetch(PDO::FETCH_ASSOC)){
		if ($r['id'] == $id)
			jsonError('e-mail uguale alla precedente!');
		else
			jsonError('e-mail già in utilizzo da un altro utente.');
	}
	//UPDATE
	//(new email)
	$pdostat = $pdo->prepare("UPDATE utenti SET email=? WHERE id={$id}") or jsonError('Errore durante aggiornamento utente [prepare]');
	if (!$pdostat->execute([$encrypted])) jsonError('Errore durante aggiornamento utente [execute]');
	if (!$pdostat->rowCount()) jsonError('Nessun utente da aggiornare');
	jsonSuccess(['email' => $hintemail]);
}
else{
	//UPDATE
	//(remove email)
	$pdostat = $pdo->query("UPDATE utenti SET email=NULL WHERE id={$id}") or jsonError('Errore durante aggiornamento utente [query]');
	if (!$pdostat->rowCount()) jsonError('Nessun utente da aggiornare');
	jsonSuccess(['email' => $hintemail]);
}

?>