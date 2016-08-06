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
allow_user_from_class(2);

$Colibrì = new \Colibri\Template;
$Pop = new \Colibri\Popups;
$PlugManager = new \Colibri\PluginsManager(true);

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Plugin Manager</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss(); ?>
	<link rel="stylesheet" href="<?php $Colibrì->link("css/plugins.min.css") ?>">

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<?php
	//hold plugin info
	$Pop->generic(
		"plugin-info", [], "Informazioni plugin",
		"pl-info-cont", [],
		//Heredoc: start with <<<XXX, end with XXX; with no spaces before!
		<<<EOD
<h3 id="pl-info-title"></h3>
<div id="pl-info-logo"></div>
<table id="pl-info-tab">
	<tr><th>Autore:</th><td class="small-center"><span id="pl-info-author"></span></td></tr>
	<tr><th>Versione plugin:</th><td class="small-center"><span id="pl-info-version"></span></td></tr>
	<tr><th>Descrizione</th><td><p id="pl-info-desc"></p></td></tr>
</table>
EOD
	);
	
	$Pop->generic_iframe('plugin-custom-page', [], "Personalizza plugin");
	
?>

<!-- END popups -->




<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - Plugins Manager</h1>
		
			<div class="inputs maxi aligned">
				<h4>Plugin disponibili</h4>
				<div id="all-plugins"><?php
					$plug_prefix_url = $Config->script_path . 'plugin/';
					foreach($PlugManager->available as $plug_url => $plugin){
						
						$plug_folder_url = htmlentities($plug_prefix_url . $plug_url . "/",ENT_QUOTES);
						
?><div class="plugin-box info-loading<?php echo ($plugin['installed'] ? '' : ' install') . ($plugin['active'] ? ' active' : ''); ?>">
	<div class="logo"<?php
		if (is_file(CMS_INSTALL_DIR."/plugin/{$plug_url}/logo.png"))
			echo ' style="background-image:url(\''.$plug_folder_url.'logo.png\')"'; 
	?>></div>
	<div class="info"></div>
	<div class="plugin-tools tools">
		<p>
		<a class="plugin-install"
			data-plugin="<?php echo $plug_folder_url; ?>"
			href="<?php echo $plug_folder_url; ?>installer.php">
			<b class="sicon"><i class="options"></i></b>
			<span class="install-ing">Installa</span>
			<span class="install-ed">Disinstalla</span>
		</a>
		<br>
		<a class="plugin-activate">
			<span class="activ-ating"><b class="sicon"><i class="v"></i></b> Attiva</span>
			<span class="activ-e"><b class="sicon"><i class="x"></i></b> Disattiva</span>
		</a>
		<?php if ($plugin['custom']) : ?>
		<a class="plugin-custom" href="<?php echo $plug_folder_url . htmlentities($plugin['custom'],ENT_QUOTES); ?>">
			<b class="sicon"><i class="pen"></i></b> Personalizza
		</a>
		<?php endif; ?>
		<label class="plugin-info">
			<input type="checkbox">
			<b class="sicon"><i class="watch"></i></b> Informazioni
		</label>
		</p>
	</div>
	<div class="loader">
		<p></p>
	</div>
</div><?php
					}
				
				?></div>
			</div>
			
		</div>
		<!-- END main -->



		<div class="right">
		
			<div class="inputs maxi aligned">
				<h4>Guida</h4>
				<p>....</p>
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
<script src="js/overthrow-0.7.1/overthrow.min.js"></script>
<script src="js/simple-modal-box.min.js"></script>

<!-- main script -->
<script src="js/common.js"></script>
<script src="js/plugins.min.js"></script>

</body>
</html>