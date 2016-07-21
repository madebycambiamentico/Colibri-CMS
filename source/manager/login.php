<?php

/*
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/
if (!isset($Config)){ header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); die; }

$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');

//redirect to dashboard if session still active
if (isLoggedIn()){
	closeConnection();
	header('Location: '.$Config->script_path . 'bacheca');
	exit;
}

$Colibrì = new \Colibri\Template;

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Login</title>
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
		<?php if (isset($_GET['logout'])): ?>
		<p class="red"><b>Sei stato scollegato correttamente.<br>A presto!</b></p>
		<br>
		<?php endif; ?>
	
		<h4>Utente:</h4>
		<p><input type="text" name="u" id="my-name" maxlength="100" placeholder="Nome Utente..." autofocus></p>
		<br>
		
		<h4>Password:</h4>
		<input type="hidden" name="p" id="my-hashed-pass">
		<p><input type="password" id="my-password" placeholder="Password..."></p>
		<br>
		</div>
	
	<div class="inputs center">
		<b id="send-me" class="btn">Accedi</b>
		<br><br><a href="signin">Registra nuovo utente</a> | <a href="#todo">Recupera password</a>
	</div>
	
</form>

</div>


<?php $Colibrì->getJQuery() ?>

<!-- plugins -->
<script src="js/jsSHA-2.0.2/src/sha512.js"></script>

<!-- main script -->
<script src="js/login.min.js"></script>

</body>
</html>