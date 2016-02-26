<?php

/*
 * @template Colibrì 2016 v.1.0 -- template editor
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

require_once "../../config.php";
require_once "../../".$CONFIG['database']['dir']."functions.inc.php";

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1);

include "php/link.class.php";
include "php/ini.class.php";


$ini = INI_FILE::iread('php/template.ini',true);

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Template Editor</title>
	<meta name="description" content="Colibrì template editor">
	<meta name="author" content="Costacurta Nereo">

	<link type="text/css" rel="stylesheet" href="editor.css">
	<link type="text/css" rel="stylesheet" href="<?php echo LINK::file('php/mbc-filemanager/css/inputs.css?v=1.0') ?>">
	
	<style type="text/css">
	/* custom map height + map marker position */
	#map{
		background:#0e0e0e url(img/map-1366.png) no-repeat center bottom;
		height:<?php echo $ini['custom']['map_height'] ?>px;
		position:relative;
		cursor:crosshair;
	}
	#mapmark{
		position:absolute;
		width:64px;
		height:64px;
		background:url(img/mapmarker.png) no-repeat center;
		bottom:<?php echo $ini['custom']['mark_y'] ?>px;
		left:50%;
		margin-left:<?php echo $ini['custom']['mark_x'] ?>px;
	}
	</style>
</head>






<body>



<div id="wrapper">

<form action="php/template-edit.php" method="POST" enctype="multipart/form-data">

<div class="inputs ultra center">
	<h1><span id="colibrì">Colibrì</span> <i>Template Editor</i></h1>
</div>

<br>

<div class="inputs ultra">
	<h3>Logo</h3>
	<p><label for="file_logo" id="logo"></label>Dimensioni massime: 140&times;140 px.<br>Immagini di dimensioni maggiori verranno ridimensionate.<br>Formato preferenziale: <i>PNG</i></p>
	<p><label class="ifile"><b class="btn">Scegli logo</b><input type="file" id="file_logo" name="logo" accept="image/*"><span class="file"></span></label><br>
		<label><b class="btn red">Salva modifiche</b><input type="submit" value="Salva modifiche"></label></p>
</div>
<div class="fixfloat"></div>

<br>

<div id="powered">
	<div class="logo"></div>
	<div class="mbc">
		<b>Powered By <span>Colibrì</span></b><br>
		<i>Fast and Reliable CMS ever <a href="http://cambiamentico.altervista.org/">MadeByCambiamentico</a></i><br>
		<i>Colbrì Theme &copy;2016</i> by Nereo Costacurta<br>
	</div>
	<div class="actions">
		<a href="<?php echo LINK::file('bacheca') ?>">Bacheca</a><!--
	--><a href="<?php echo LINK::file('options') ?>">Gestione sito</a>
	</div>
</div>

<div class="inputs ultra" id="yellow">
	<h3>Mappa</h3>
	<p>Dimensioni consigliate: 1366&times;520 px.<br>Immagini di dimensioni minori potrebbero non avere un effetto estetico accettabile.<br>Formato preferenziale: <i>PNG o JPG</i></p>
	<p><label class="ifile"><b class="btn">Scegli mappa</b><input type="file" name="map" accept="image/*"><span class="file"></span></label><br>
		<label><b class="btn red">Salva modifiche</b><input type="submit" value="Salva modifiche"></label></p>
	<br>
	<br>
	<div class="clicca">Clicca sull'immagine per cambiare posizione al <i>marker</i></div>
	<div id="map">
		<div id="mapmark"></div>
		<div id="cell"></div>
		<input type="hidden" id="markx" name="markx" value="<?php echo $ini['custom']['mark_x'] ?>">
		<input type="hidden" id="marky" name="marky" value="<?php echo $ini['custom']['mark_y'] ?>">
	</div>
</div>

<div id="mysavebutton" class="inputs ultra center">
	<label><b class="btn red">Salva modifiche</b><input type="submit" value="Salva modifiche"></label>
</div>

</form>

</div>





<?php LINK::getJQuery() ?>

<script>
$("#map").click(function(e){
   var parentOffset = $(this).offset(); 
   var relX = e.pageX - parentOffset.left;
   var relY = e.pageY - parentOffset.top;
	var w = $(this).width();
	var h = $(this).height();
	var mark_x = Math.ceil(relX-w/2-32);
	var mark_y = Math.ceil(h-relY-16);
	$('#mapmark').css({
		marginLeft:mark_x+'px',
		bottom:mark_y+'px'
	});
	$('#markx').val(mark_x);
	$('#marky').val(mark_y);
});
$('input[type=file]').change(function(){
	$(this).next('span').text(this.value);
});
</script>

</body>
</html>