<?php

/*
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/
if (!isset($CONFIG)){ header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); die; }

//if user already logged, why should he register another profile?
/*$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
if (isLoggedIn()){
	//La sessione è ancora attiva.
	closeConnection();
	header('Location: '.$CONFIG['mbc_cms_dir'].'bacheca');
	exit;
}*/


//search website properties
$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
if (!$web = $pdostat->fetch(PDO::FETCH_ASSOC))
	noPageFound('Database corrotto [cod 001]');
$pdostat->closeCursor();
if (!$web['multilanguage']) $mylang = null;


$Colibrì = new \Colibri\Template;


?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Register</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">

	<link rel="stylesheet" href="<?php $Colibrì->link("php/mbc-filemanager/css/inputs.min.css") ?>">
	<link rel="stylesheet" href="<?php $Colibrì->link("css/login.min.css") ?>">
</head>



<body>

<div id="wrapper">

<form id="my-login">

	<div id="loader" class="done"></div>
	
	<div class="inputs">
	
		<p class="red"><b>Registrazione di un nuovo utente</b></p><br>
		
		<h4>Utente:</h4>
		<p><input type="text" name="u" id="my-name" maxlength="100" placeholder="Nome Utente..." autofocus></p>
		<br>
		
		<h4>Email:</h4>
		<p><input type="email" name="e" id="email" maxlength="100" placeholder="e-mail..."></p>
		<br>
		
		<h4>Richiesta:</h4>
		<p><input type="text" name="request" placeholder="(commento opzionale)"></p>
		<br>
		
		<?php
			$reCaptcha = new \ReCaptcha\ReCaptcha($web['recaptcha_key']);
			$reCaptcha->get_browser_widget($mylang ? $mylang : 'en');
		?>
		
		<p>Tutti i campi sono obbligatori. Una mail di conferma ti verrà inviata quando la tua richiesta verrà accettata.</p>
	</div>
	
	<div class="inputs center">
		<b id="send-me" class="btn">Registrati</b>
		<br><br><a href="login">Accedi</a> | <a href="#todo">Recupera password</a>
	</div>
	
</form>

</div>


<?php $Colibrì->getJQuery() ?>

<!-- plugins -->

<!-- main script -->
<script src="js/signin.min.js"></script>

</body>
</html>