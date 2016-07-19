<?php
header('Content-Type: application/json');

require_once "functions.inc.php";
require_once "../php/encrypter.class.php";
$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );

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


//check if username is available
$pdostat = $pdo->prepare("SELECT id FROM utenti WHERE nome=? LIMIT 1") or jsonError('Errore durante ricerca utente [prepare]');
if (!$pdostat->execute([$nome])) jsonError('Errore durante ricerca utente [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC))
	jsonError("Il nome scelto non è disponibile.");


//check if email is available
$encrypted = $ENCRYPTER->encrypt($email);
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