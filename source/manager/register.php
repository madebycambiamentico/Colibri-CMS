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

/*$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
if (isLoggedIn()){
	//La sessione è ancora attiva.
	closeConnection();
	header('Location: '.$CONFIG['mbc_cms_dir'].'bacheca');
	exit;
}*/

$Colibrì = new Colibri();

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
		<p><textarea name="request" class="short" placeholder="(commento opzionale)"></textarea></p>
		<br>
		
		<p>Tutti i campi sono obbligatori. Una mail di conferma ti verrà inviata quando la tua richiesta verrà accettata.</p>
	</div>
	
	<div class="inputs center">
		<b id="send-me" class="btn">Registrati</b>
	</div>
	
</form>

</div>


<?php $Colibrì->getJQuery() ?>

<!-- plugins -->

<!-- main script -->
<script>

function checkform(onsuccess){
	//control name
	var name = $('#my-name').val().trim();
	if (name == ''){
		alert("Inserire il nome!");
		return false;
	}
	//callback on success
	if ($.isFunction(onsuccess)) onsuccess();
	return true;
}

var LOGGING = false;

function log(){
	//prevent multiple login
	if (LOGGING){
		alert("Registrazione già in corso, attendi.");
		return false;
	}
	else LOGGING = true;
	//show loader
	$loader = $('#loader')
		.removeClass('done');
	//send post request
	if ( !checkform(function(){
		$.post('database/user-request.php',$('#my-login').serialize(),null,'json')
			.success(function(json){
				console.log(json);
				if (json.error !== false){
					alert("ERRORE\n"+json.error);
					$loader.addClass('done');
					LOGGING = false;
				}
				else
					location.assign('./');
			})
			.error(function(e){
				alert('Ooops!');
				$loader.addClass('done');
				LOGGING = false;
				console.log(e);
			})
	}) ){
		//check form failed!!!
		LOGGING = false;
		$loader.addClass('done');
	}
}

$(function(){
	$('#my-login').submit(function(e){
		e.preventDefault();
	});
	
	$('#my-login input').keypress(function(e){
		if (e.which == 13){
			log()
			return false;    //<---- Add this line
		}
	});
	
	$('#send-me').click(log);
});
</script>

</body>
</html>