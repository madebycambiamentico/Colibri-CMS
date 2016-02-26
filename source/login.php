<?php

require_once "config.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
if (isLoggedIn()){
	//La sessione è ancora attiva.
	closeConnection();
	header('Location: '.$CONFIG['mbc_cms_dir'].'bacheca');
	exit;
}

$Colibrì = new Colibri();

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Login</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">

	<link rel="stylesheet" href="<?php $Colibrì->link("php/mbc-filemanager/css/inputs.css?v=1.1") ?>">
	<link rel="stylesheet" href="<?php $Colibrì->link("php/mbc-filemanager/css/style.css?v=1.1") ?>">
	<style type="text/css">
		body{
			display:table;
			width:100%;
			height: 100%;
			background:#2d2d2d;
		}
		#wwrapper{
			display:table-cell;
			height:100%;
			width:100%;
			vertical-align:middle;
		}
		#my-login{
			display:block;
			position:relative;
			margin:0 auto;
			padding:160px 30px 30px 30px;
			background:#fff url(img/logo/colibri-mini-black.png) no-repeat center 30px;
			width:300px;
			border-radius:5px;
			box-shadow:0 0 12px #000;
		}
		.red{
			font-size:13px;
			color:#c00;
			text-align:center;
		}
		
		@media only screen and (max-width:480px),
				only screen and (max-height:480px){
			body,
			#wwrapper,
			#my-login{
				display:block;
				width: auto;
				background:#fff;
			}
			#my-login{
				border-radius:0;
				padding:90px 16px 16px 16px;
				background:#fff url(img/logo/colibri-micro-black.png) no-repeat center 16px;
				box-shadow:none;
			}
		}
	</style>
</head>



<body>

<div id="wwrapper">

<form id="my-login">

	<div id="loader done"></div>
	
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
	</div>
	
</form>

</div>


<?php $Colibrì->getJQuery() ?>

<!-- plugins -->
<script src="js/jsSHA-2.0.2/src/sha512.js"></script>

<!-- main script -->
<script>
function checkform(onsuccess){
	//control name
	var name = $('#my-name').val().trim();
	if (name == ''){
		alert("Inserire il nome!");
		return false;
	}
	//control password
	var pass = $('#my-password').val().trim();
	if (pass.length<4){
		alert("La password è troppo corta!");
		return false;
	}
	//generate hashed password
	var shaObj = new jsSHA("SHA-512", 'TEXT');
	shaObj.update(pass);
	$('#my-hashed-pass').val( shaObj.getHash("HEX") ); //128 CHAR
	//callback on success
	if ($.isFunction(onsuccess)) onsuccess();
	return true;
}

var LOGGING = false;

function log(){
	//prevent multiple login
	if (LOGGING){
		console.log("already logging in!");
		return false;
	}
	else LOGGING = true;
	//show loader
	console.log('l false')
	$('#loader').addClass('load');
	//send post request
	if ( !checkform(function(){
		console.log('l false')
		$.post('database/login.php',$('#my-login').serialize(),null,'json')
			.success(function(json){
				console.log(json);
				if (json.error !== false) return alert("ERRORE\n"+json.error);
				else location.assign('./bacheca.php');
			})
			.error(function(e){
				alert('Ooops!')
				console.log(e)
			})
			.always(function(){
				LOGGING = false;
				$('#loader').removeClass('load');
			})
	}) ){
		LOGGING = false;
		$('#loader').removeClass('load');
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