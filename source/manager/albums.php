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
$Pop = new \Colibri\Popups;
$PlugManager = new \Colibri\PluginsManager(false, 'albums', ['active' => true]);

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>

	<title>M.B.C. Album Editor</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php
		$Colibrì->getBaseCss();
		$PlugManager->run_plugins( 'style' );
	?>

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<?php $PlugManager->run_plugins( 'popup' ); ?>

<?php $Pop->getForAlbums() ?>

<?php $Pop->getForFileManager() ?>

<!-- END popups -->











<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/">
	
		<!-- START album editor -->
		<div class="main">
			<?php
			
				$Colibrì->getaAlbumEditor();
				
				$PlugManager->run_plugins( 'center' );
			
			?>
		</div>
		<!-- END album editor -->
		
		<div class="right">
			<h4>Le ultime immagini caricate</h4>
			<div class="inputs maxi aligned tools">
				<p><input type='checkbox' id="ft-0" onchange="openFM('image')">
					<label for="ft-0"><b class="sicon"><i class="upload"></i></b>
					Apri file manager</label></p>
			</div>
			
			<div id="last-images" class="albums active"><?php

if ($pdores = $pdo->query("SELECT src FROM immagini ORDER BY data DESC LIMIT 6")){
	//stampo preview + titolo album
	foreach ($pdores as $r){
//-----------------------------------------------------------
echo '<img src="img/thumbs/'.$r['src'].'">';
//-----------------------------------------------------------
	}
}
			?></div>
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
<script src="js/tinymce/tinymce.min.js"></script>
<script src="js/overthrow-0.7.1/overthrow.min.js"></script>
<script src="js/simple-modal-box.min.js"></script>
<script src="js/diatrics-remover.min.js"></script>

<!-- main script -->
<script src="js/common.js"></script>
<script src="js/albums-manager.min.js"></script>

<!-- 3rd party scripts -->
<?php $PlugManager->run_plugins( 'js' ); ?>

</body>
</html>