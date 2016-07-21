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

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(0); // allow everybody to change his profile (if logged in)

$Colibrì = new \Colibri\Template;
$Pop = new \Colibri\Popups;

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>

	<title>M.B.C. Profilo</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>
	<link rel="stylesheet" href="<?php $Colibrì->link("php/mbc-filemanager/js/dropzone-4.2.0/dist/fallback.css") ?>">
	<link rel="stylesheet" href="<?php $Colibrì->link("css/profile.min.css") ?>">

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<div class="popup-cont" id="upload-files">
	<h4>Carica o Sovrascrivi i files</h4>
	<div class="popup overthrow">
		<h5></h5>
		<form id="my-dropzone" action="database/user-image.php" method="POST" class="dropzone" enctype="multipart/form-data">
			<div class="fallback">
				<br><br>
				<div class="inputs maxi tools center">
					<p><input type="file" id="ui-file" class="inputfile" name="file">
						<label for="ui-file"><b class="sicon"><i class="upload"></i></b>
						Scegli file
						<br>
						<b id="onupload">nessun file selezionato</b></label></p>
				</div>
						<br>
				<div class="inputs center">
					<b id="saveImage" class="btn">Carica Immagine</b> &nbsp; <b id="deleteImage" class="btn red">Cancella Immagine</b>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- END popups -->










<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - Profilo</h1>
<?php
if ($pdores = $pdo->query("SELECT * FROM utenti WHERE id = ".$_SESSION['uid']." LIMIT 1", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		$hasrows = true;
?>
			<div id="pf-image" class="pf-img _256" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>face-256.png')">
				<b class="sicon"><i class="pencil"></i></b>
			</div>
			<div class="inputs ultra tools center">
				<p><a href="./database/logout.php?redirect">
					<b class="sicon"><i class="off"></i></b>
					Scollegati</a></p>
				<?php if ($_SESSION['uclass'] == 1): ?>
				<p><a href="./profiles"><b class="sicon"><i class="people"></i></b>
					Gestisci Profili Pubblici</a></p>
				<?php endif; ?>
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Username</h4>
				<input type="text" class="ronly" value="<?php echo htmlentities($r['nome'],ENT_QUOTES) ?>" readonly>
			</div>
			
			<div id="all-email-inputs">
				<div class="inputs maxi aligned">
					<h4>Modifica e-mail</h4>
					<p>Inserisci vecchia e-mail (<b id="my-email-hint"><?php
						if ($r['email']){
							$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );
							$decrypted = $Encrypter->decrypt($r['email']);
							//delete all character, leave first, @ and last
							$emailparts = explode('@',$decrypted);
							echo htmlentities( $emailparts[0][0] . str_repeat("*", mb_strlen($emailparts[0])-1) . '@' . str_repeat("*", mb_strlen($emailparts[1])-1) . mb_substr($emailparts[1],-1) );
						}
						else echo 'nessuna';
					?></b>)</p>
					<input type="text" id="my-email" name="emailold" value="">
				</div>
				
				<div class="inputs maxi aligned">
					<p>Inserisci nuova e-mail</p>
					<input type="text" id="my-email-new" name="email" value="">
				</div>
			</div>
			
			<div class="inputs center">
				<br>
				<b id="saveEmail" class="btn">Salva modifiche Email</b>
			</div>
			
<?php
	endforeach;
	if (!$hasrows) logout('nouser');//should not happen...
}
?>
		</div>
		<!-- END main -->



		<div class="right">
			
			<div id="all-pass-inputs">
				<div class="inputs maxi aligned">
					<h4>Modifica Password</h4>
					<p>Password vecchia</p>
					<input type="hidden" name="p" id="my-hashed-pass">
					<p><input type="password" id="my-password" placeholder="Password vecchia..."></p>
				</div>
				
				<div class="inputs maxi aligned">
					<p>Password nuova - almeno 8 caratteri (numeri, lettere, caratteri speciali)</p>
					<input type="hidden" name="pnew" id="my-hashed-pass-new">
					<p><input type="password" id="my-password-n" placeholder="Password nuova..."></p>
					<div id="passmeter"><p></p></div>
				</div>
				
				<div class="inputs maxi aligned">
					<p>Ripeti nuova password</p>
					<p><input type="password" id="my-password-nr" placeholder="Password nuova..."></p>
				</div>
			</div>
			
			<div class="inputs maxi aligned">
				<p>Suggerimento se dimentichi la password (per evitare di seguire tutta la procedura per il recupero)</p>
				<p><textarea id="my-password-hint" class="short" placeholder="Suggerimento..."><?php echo htmlentities($r['passhint']); ?></textarea></p>
			</div>
			
			<div class="inputs center">
				<br>
				<b id="savePass" class="btn">Salva modifiche Password</b>
			</div>
		</div>
	
	</form>
	</div>
</div>

<?php
//!!!!!!!!!!!!!!!!
closeConnection();
//!!!!!!!!!!!!!!!!
?>


<!-- initial loader fullscreen, until all scripts loaded.
		must be placed before </body> (and before scripts) -->
<div id="loader"></div>



<?php $Colibrì->getJQuery() ?>

<!-- plugins -->
<script src="js/simple-modal-box.min.js"></script>
<script src="js/jsSHA-2.0.2/src/sha512.js"></script>
<script src="php/mbc-filemanager/js/dropzone-4.2.0/dist/min/jquery.form.js"></script>

<!-- main script -->
<script src="js/common.js"></script>
<script src="js/profile.min.js"></script>


</body>
</html>