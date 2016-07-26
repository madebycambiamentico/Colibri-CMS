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
allow_user_from_class(1);

$Colibrì = new \Colibri\Template;
$PlugManager = new \Colibri\PluginsManager(false, 'profiles-manager', ['active' => true]);
$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>

	<title>M.B.C. Gestione Profili</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>
	<link rel="stylesheet" href="<?php $Colibrì->link("css/profile.min.css") ?>">
	<?php $PlugManager->run_plugins( 'style' ); ?>

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<?php $PlugManager->run_plugins( 'popup' ); ?>

<!-- END popups -->










<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - Gestione Profili</h1>







<!-- USERS WAITING FOR ABILITATION -->
<div id="user_waiters" class="has-no-pfs">
	<h3>Profili in attesa</h3>
<?php

if ($pdores = $pdo->query("SELECT id, nome, email, about FROM utenti WHERE classe = -1", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		if (!$hasrows) $hasrows = true;
		$email = htmlentities( $Encrypter->decrypt($r['email']) );
?>
	<div class="inputs maxi aligned" id="profile--1-<?php echo $r['id']; ?>">
		<table class="profile-edit"><tr>
			<td class="pf-td-img">
				<div class="pf-img _64" style="background-image:url('img/users/default/face-64.png')"></div>
				<div class="tools"><p><!--
						this is the "do nothing"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_3" name="user_waiter[<?php echo $r['id']; ?>][action]" value="3" checked><label title="Lascia in attesa" for="u_act_<?php echo $r['id']; ?>_3"><b class="sicon"><i class="reload"></i></b></label><!--
						this is the "enable"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_1" name="user_waiter[<?php echo $r['id']; ?>][action]" value="1"><label title="Abilita" for="u_act_<?php echo $r['id']; ?>_1"><b class="sicon"><i class="v"></i></b></label><!--
						this is the "remove"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_2" name="user_waiter[<?php echo $r['id']; ?>][action]" value="2"><label title="Rifiuta" for="u_act_<?php echo $r['id']; ?>_2"><b class="sicon"><i class="x"></i></b></label>
				</p></div>
			</td>
			<td class="pf-td-tools">
				<h4 class="pf-name"><?php echo htmlentities($r['nome']) ?></h4>
				<div class="pf-info"><p><b>E-mail: </b><i><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></i></p>
				<p><b>Richiesta: </b><i><?php echo htmlentities($r['about']) ?></i></p></div>
				
				<h4>Assegna classe:</h4>
				<p>
				<select name="user_waiter[<?php echo $r['id']; ?>][class]">
					<option value="0" selected>Ospite</option>
					<option value="1">Amministratore</option>
					<?php if ($_SESSION['uclass'] == 2){?><option value="2">Webmaster</option><?php } ?>
				</select></p>
			</td>
		</tr></table>
	</div>
<?php
	endforeach;
}
?>
	<p class="no-pfs">Nessuna nuova richiesta</p>
	<div class="inputs center with-pf">
		<br>
		<b id="saveNewUsers" class="btn">Salva modifiche utenti in attesa</b>
	</div>
</div>







<!-- GUEST USERS -->
<div id="user_guests" class="has-no-pfs">
	<h3>Profili ospite</h3>
<?php

if ($pdores = $pdo->query("SELECT u.id, u.nome, u.hasimage, (SELECT count(*) FROM emails where iduser = u.id) as 'pendingemails' FROM utenti u WHERE classe = 0", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		if (!$hasrows) $hasrows = true;
?>
	<div class="inputs maxi aligned" id="profile-0-<?php echo $r['id']; ?>">
		<table class="profile-edit"><tr>
			<td class="pf-td-img">
				<div class="pf-img _64" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>face-64.png')"></div>
				<?php if ($r['pendingemails']){ ?>
					<div class="pf-email" title="Il sistema deve ancora recapitare <?php echo $r['pendingemails']; ?> email"><b class="sicon"><i class="mail"></i></b></div>
				<?php } ?>
				<div class="tools"><p><!--
						this is the "do nothing"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_1" name="user_guest[<?php echo $r['id']; ?>][action]" value="1" checked><label for="u_act_<?php echo $r['id']; ?>_1"><b class="sicon"><i class="v"></i></b></label><!--
						this is the "remove"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_2" name="user_guest[<?php echo $r['id']; ?>][action]" value="2"><label title="Rifiuta" for="u_act_<?php echo $r['id']; ?>_2"><b class="sicon"><i class="x"></i></b></label>
				</p></div>
			</td>
			<td>
				<h4 class="pf-name"><?php echo htmlentities($r['nome']) ?></h4>
				<h4>Assegna classe:</h4>
				<p><select name="user_guest[<?php echo $r['id']; ?>][class]">
					<option value="0" selected>Ospite</option>
					<option value="1">Amministratore</option>
					<?php if ($_SESSION['uclass'] == 2){?><option value="2">Webmaster</option><?php } ?>
				</select></p>
			</td>
		</tr></table>
	</div>
<?php
	endforeach;
}
?>
	<p class="no-pfs">Nessun profilo registrato con questa classe</p>
	<div class="inputs center with-pf">
		<br>
		<b id="saveGuestUsers" class="btn">Salva modifiche utenti ospite</b>
	</div>
</div>








<!-- ADMIN USERS -->
<div id="user_admins" class="has-no-pfs">
	<h3>Profili amministratore</h3>
<?php

if ($pdores = $pdo->query("SELECT u.id, u.nome, u.hasimage, (SELECT count(*) FROM emails where iduser = u.id) as 'pendingemails' FROM utenti u WHERE classe = 1", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		if (!$hasrows) $hasrows = true;
?>
	<div class="inputs maxi aligned" id="profile-1-<?php echo $r['id']; ?>">
		<table class="profile-edit"><tr>
			<td class="pf-td-img">
				<div class="pf-img _64" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>face-64.png')"></div>
				<?php if ($r['pendingemails']){ ?>
					<div class="pf-email" title="Il sistema deve ancora recapitare <?php echo $r['pendingemails']; ?> email"><b class="sicon"><i class="mail"></i></b></div>
				<?php } ?>
				<?php
					//webmasters can remove rights to admins, or delete them.
					if ($_SESSION['uclass'] == 2):
				?>
				<div class="tools"><p><!--
						this is the "do nothing"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_1" name="user_admin[<?php echo $r['id']; ?>][action]" value="1" checked><label for="u_act_<?php echo $r['id']; ?>_1"><b class="sicon"><i class="v"></i></b></label><!--
						this is the "remove"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_2" name="user_admin[<?php echo $r['id']; ?>][action]" value="2"><label title="Rifiuta" for="u_act_<?php echo $r['id']; ?>_2"><b class="sicon"><i class="x"></i></b></label>
				</p></div>
				<?php
					endif;
				?>
			</td>
			<td>
				<h4 class="pf-name"><?php echo htmlentities($r['nome']) ?></h4>
				<?php
					//webmasters can remove rights to admins, or delete them.
					if ($_SESSION['uclass'] == 2):
				?>
				<h4>Classe:</h4>
				<p><select name="user_admin[<?php echo $r['id']; ?>][class]">
					<option value="0">Ospite</option>
					<option value="1" selected>Amministratore</option>
					<option value="2">Webmaster</option>
				</select></p>
				<?php
					endif;
				?>
			</td>
		</tr></table>
	</div>
<?php
	endforeach;
}
	//webmasters can remove rights to admins, or delete them.
	if ($_SESSION['uclass'] == 2):
?>
	<p class="no-pfs">Nessun profilo registrato con questa classe</p>
	<div class="inputs center with-pf">
		<br>
		<b id="saveAdminUsers" class="btn">Salva modifiche utenti amministratori</b>
	</div>
<?php
	endif;
?>
</div>







<?php if ($_SESSION['uclass'] == 2){ ?>
<!-- WEBMASTER USERS -->
<div id="user_webmasters" class="has-no-pfs">
	<h3>Profili webmaster</h3>
<?php

if ($pdores = $pdo->query("SELECT u.id, u.nome, u.hasimage, (SELECT count(*) FROM emails where iduser = u.id) as 'pendingemails' FROM utenti u WHERE classe = 2", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		if (!$hasrows) $hasrows = true;
?>
	<div class="inputs maxi aligned" id="profile-2-<?php echo $r['id']; ?>">
		<table class="profile-edit"><tr>
			<td class="pf-td-img">
				<div class="pf-img _64" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>/face-64.png')"></div>
				<?php if ($r['pendingemails']){ ?>
					<div class="pf-email" title="Il sistema deve ancora recapitare <?php echo $r['pendingemails']; ?> email"><b class="sicon"><i class="mail"></i></b></div>
				<?php } ?>
				<div class="tools"><p><!--
						this is the "do nothing"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_1" name="user_webmaster[<?php echo $r['id']; ?>][action]" value="1" checked><label for="u_act_<?php echo $r['id']; ?>_1"><b class="sicon"><i class="v"></i></b></label><!--
						this is the "remove"
					--><input type="radio" id="u_act_<?php echo $r['id']; ?>_2" name="user_webmaster[<?php echo $r['id']; ?>][action]" value="2"><label title="Rifiuta" for="u_act_<?php echo $r['id']; ?>_2"><b class="sicon"><i class="x"></i></b></label>
				</p></div>
			</td>
			<td class="pf-td-tools">
				<h4 class="pf-name"><?php echo htmlentities($r['nome']) ?></h4>
				<?php
					//webmasters can remove rights to admins, or delete them.
					if ($_SESSION['uclass'] == 2){
				?>
				<h4>Assegna classe:</h4>
				<p><select name="user_webmaster[<?php echo $r['id']; ?>][class]">
					<option value="0">Ospite</option>
					<option value="1">Amministratore</option>
					<option value="2" selected>Webmaster</option>
				</select></p>
				<?php
					} //end webmaster tools
				?>
			</td>
		</tr></table>
	</div>
<?php
	endforeach;
}
?>
	<p class="no-pfs">Nessun profilo registrato con questa classe</p>
	<div class="inputs center with-pf">
		<br>
		<b id="saveWebmasterUsers" class="btn">Salva modifiche utenti webmaster</b>
	</div>
</div>
<?php } //end allowed only for webmaster (all the #user_webmasters DIV) ?>




			<?php $PlugManager->run_plugins( 'center' ); ?>

		</div>
		<!-- END main -->



		<div class="right">
			<div class="inputs maxi aligned">
				<h4>Suggerimenti</h4>
				<p>Gli utenti in attesa di abilitazione possono essere accettati o rifiutati.<br>Nel primo caso verrà spedita una mail con la nuova password (che potranno cambiare nel loro profilo). Nel secondo caso verranno eliminati dal database silenziosamente.<br>Se non hai ancora deciso cosa fare, puoi sempre mantenerli in attesa.</p>
				<br>
				<p>I profili <b>OSPITE</b> possono modificare la propria immagine nel sito, cancellare il proprio account, e commentare il sito pubblicamente.</p>
				<br>
				<p>I profili <b>AMMINISTRATORE</b> hanno i tuoi stessi privilegi: <i>possono modificare il sito in ogni sua parte, caricare files, modificare i profili</i>.<br>Assicurati che siano persone di cui ci si possa fidare: quando il danno è fatto non è facile rimediare (se non impossibile)!</p>
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Webmasters</h4>
				<p>Qui di seguito sono elencati i <b>WEBMASTERS</b>.<br>Questi profili possono eliminare sia i profili amministratore che i webmasters.</p>
				<div id="small_webmasters"><?php 
if ($pdores = $pdo->query("SELECT id, nome, hasimage FROM utenti WHERE classe = 2", PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		!$hasrows && $hasrows = true;

			?><div id="small_webmaster_<?php echo $r['id'] ?>" class="pf-img _32" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>face-32.png')" title="<?php echo htmlentities($r['nome'],ENT_QUOTES) ?>"></div><?php
		
	endforeach;
	if (!$hasrows) echo "<p><b>Nessun webmaster abilitato in questo sito!</b></p>";
}
?>
				</div>
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

<!-- main script -->
<script src="js/common.js"></script>
<script src="js/profiles-manager.min.js"></script>
<script>
//-------------------------------------------------------------------
//debug script...
$(function(){
	var winRef = null;
	$('.pf-email b').click(function(){
		var id = $(this).parents('[id^="profile-"]')[0].id.replace(/[a-z]+-\d+-/ig,"");
		winRef = window.open(
			'database/email-bodies/preview.php?id='+id,
			'email preview',
			"resizable=yes,scrollbars=yes,width=480,height=600"
		);
		winRef.focus();
	})
})
//end debug script...
//-------------------------------------------------------------------
</script>

<!-- 3rd party scripts -->
<?php $PlugManager->run_plugins( 'js' ); ?>


</body>
</html>