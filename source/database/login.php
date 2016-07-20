<?php
header('Content-Type: application/json');

require_once "functions.inc.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
if (isLoggedIn()) jsonSuccess();//Hai già effettuato il login

//control variables
if (!isset($_POST['u'], $_POST['p'])) jsonErrorLogout('Variabili errate');

//ulteriore controllo variabili
$user = trim($_POST['u']);
$pass = trim($_POST['p']);
if (empty($user) || strlen($pass) != 128) jsonErrorLogout("Variabili errate");

/*
$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
$password = hash('sha512', $pass.$random_salt);
die($random_salt.'<br>'.$password);
/*
u: admin + p: admin =
user : "admin"
salt : "4522c4024f9bdae001e78e2839d354285169c82293c9106370faf3666958b38e2016d6f6ce5de5807e5ceb4856de34988794d59a77877960c3d444a418937b13"
pass : "620d289aef644aff1a36e0a516a01be0fbdb70caa67cfa5593f6ec5a5b4f1a30272b361fb79adcf148dfc94fcda09ed42afb7ad874bc5f8bb65c1cfb22dd0f4c"
*/

//controllo del database
$pdostat = $pdo->prepare("SELECT id, classe, pass, salt, flags FROM utenti WHERE nome = ? LIMIT 1") or jsonErrorLogout('Errore durante ricerca utente [prepare]');
if (!$pdostat->execute([$user])) jsonErrorLogout('Errore durante ricerca utente [execute]');
if ($r = $pdostat->fetch(PDO::FETCH_ASSOC)){
	$hashSaltPlusPass = hash('sha512', $pass.$r['salt']);
	if ($hashSaltPlusPass == $r['pass']){
		$_SESSION['uid'] = $r['id'];
		$_SESSION['uclass'] = $r['classe'];
		$_SESSION['uflags'] = $r['flags'];
		jsonSuccess();
	}
	else{
		jsonErrorLogout("L'utente o la password non corrispondono"); //password sbagliata
	}
}
else{
	jsonErrorLogout("L'utente o la password non corrispondono"); //utente ignoto
}

?>