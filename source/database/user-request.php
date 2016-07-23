<?php
header('Content-Type: application/json');

require_once "../config.php";

$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );

//controllo generale variabili
if (!isset(
		$_POST['u'],
		$_POST['e']
	)){
		jsonError('Variabili errate');
	}

$nome = trim($_POST['u']);
$email = trim($_POST['e']);
$request = isset($_POST['request']) ? trim($_POST['request']) : '';

if (empty($nome) || empty($email))
	jsonError("Controlla di aver inserito nome utente ed email");
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
	jsonError('e-mail non accettabile');


if ($pdores = $pdo->query("SELECT recaptcha_secret FROM sito ORDER BY id DESC LIMIT 1", PDO::FETCH_ASSOC)){
	if($r = $pdores->fetch()){
		if (!empty($r['recaptcha_secret'])){
			//verify recaptcha...
			//require_once "../php/Colibri-ReCaptcha/autoloader.php";
			$ReCaptcha = new \ReCaptcha\ReCaptcha([
				'secret_key' => $Encrypter->decrypt($r['recaptcha_secret'])
			]);
			$ReCaptcha->validate() or jsonError($ReCaptcha->error);
		}
	}
	$pdores->closeCursor();
}



//check if username is available
$pdostat = $pdo->prepare("SELECT id FROM utenti WHERE nome=? LIMIT 1") or jsonError('Errore durante ricerca utente [prepare]');
if (!$pdostat->execute([$nome])) jsonError('Errore durante ricerca utente [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC))
	jsonError("Il nome scelto non è disponibile.");


//check if email is available
$encrypted = $Encrypter->encrypt($email);
$pdostat = $pdo->prepare("SELECT id FROM utenti WHERE email=? LIMIT 1") or jsonError('Errore durante ricerca email [prepare]');
if (!$pdostat->execute([$encrypted])) jsonError('Errore durante ricerca email [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC))
	jsonError("Hai già richiesto la partecipazione al sito!");


//INSERT waiting guest:
$pdostat = $pdo->prepare("INSERT INTO utenti (nome, email, about, classe) VALUES (?,?,?,-1)") or jsonError('Errore durante inserimento utente [prepare]');
if (!$pdostat->execute([$nome, $encrypted, $request])) jsonError('Errore durante inserimento utente [execute]');
if (!$pdo->lastInsertId())
	jsonError('Impossibile inserire il nuovo utente.');

jsonSuccess();

?>