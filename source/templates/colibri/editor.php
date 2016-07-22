<?php

/*
 * @template Colibrì 2016 v.1.0 -- template editor
 * @author Nereo Costacurta
 *
 * @require: this is a standalone page!
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

//-------------------------------------------------------------------------
// !!! WARNING !!!
// $Config->script_path contain the path to THIS folder
//-------------------------------------------------------------------------

require_once "../../config.php";
$Config->i_need_functions();

//control login - uncomment to hide this page from public
/*
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);
*/

include __DIR__ . "/php/link.class.php";
include __DIR__ . "/php/ini.class.php";


$ini = INI_FILE::iread('php/template.ini',true);

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Template Editor</title>
	<meta name="description" content="Colibrì template editor">
	<meta name="author" content="Costacurta Nereo">

	<link type="text/css" rel="stylesheet" href="img/editor-colibri/editor.css">
	
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

<!--
here the colibrì background image
2:1 fino a 512px
512x512 fino a 320px
320x480 per schermi più piccoli
-->
<div id="colibrì">
	<div class="noise">
		<!-- title -->
		<div class="titles">
			<div class="titles-table">
			<div class="titles-cell">
				<div class="titles-position">
					<h1>Colibrì Template</h1>
					<h2>Custom Editor</h2>
				</div>
			</div>
			</div>
		</div>
		
		<!-- menu -->
		<div id="menu">
			<a class="logo" href="http://colibricms.altervista.org/"></a><!--
			--><!--a class="item" href="http://cambiamentico.altervista.org/">MadeByCambiamentico</a--><!--
			--><a class="item" href="../../bacheca">Bacheca</a><!--
			--><a class="item" href="../../options">Gestione sito</a>
		</div>
		
		<!-- menu for mobiles -->
		<div id="menu-mobile">
			<div class="content">
				<a class="logo" href="http://colibricms.altervista.org/"></a><!--
				--><!--a class="item" href="http://cambiamentico.altervista.org/">MadeByCambiamentico</a--><!--
				--><a class="item" href="../../bacheca">Bacheca</a><!--
				--><a class="item" href="../../options">Gestione sito</a>
			</div>
		</div>
		<div id="open-menu"><a></a></div>
	</div>
</div>




<div id="wrapper">
	<p class="photocourtesy">Photo curtesy of <a href="http://www.patriciaisbellphotography.com" target="_blank">Patricia Isbell</a></p>

	<div class="content _withbottom">
	
		<h3>Logo</h3>
		
		<form id="form-logo" class="center choicer" action="php/template-edit-logo.php" method="POST" enctype="multipart/form-data">
			
			<!-- logo + instructions -->
			<ul class="choice _x1 _max420">
				<li class="center">
					<label for="file-logo" id="logo-square"></label><!--
					--><label for="file-logo" id="logo-circle"></label>
				</li>
				<li>
					<h4>Edita logo</h4>
					<div class="center">
					<input type="hidden" value="test" name="test">
					<label class="button">Scegli logo<input type="file" name="logo" id="file-logo" class="hidden-input" accept="image/*"></label>
					</div>
					<p>Selezionare il nuovo logo del sito.
					Le dimensioni massime consentite sono <code>140px &times; 140px</code>.
					L'immagine verrà eventualmente ridimensionata e convertita in formato <code>png</code></p>
				</li>
			</ul>
			
		</form>
	</div>
	
	
	<div class="content _withbottom">
	
		<h3>Mappa</h3>
		
		<form id="form-map" class="center choicer"
			action="php/template-edit-map.php"
			method="POST"
			enctype="multipart/form-data">
			
			<!-- mappa + istruzioni -->
			<ul class="choice _x2">
				<li>
					<h4>Edita mappa</h4>
					<div class="center">
						<label class="button">Scegli mappa<input type="file" name="map" id="file-map" class="hidden-input" accept="image/*"></label>
					</div>
					<p>Selezionare la nuova immagine per la mappa.
					Le dimensioni minime consigliate sono <code>1366px &times; 520px</code>. Sono ammesse anche dimensioni minori, a scapito della qualità visiva.
					L'immagine verrà automaticamente ridimensionata nei seguenti formati:
						<br><code>1366px &times; 520px</code>
						<br><code>800px &times; 520px</code>
						<br><code>520px &times; 520px</code>
					</p>
				</li>
			</ul><!--
				
				istruzioni x marker
			--><ul class="choice _x2">
				<li>
					<h4>Edita marker</h4>
					<div class="center">
						<input type="hidden" id="markx" name="markx" value="<?php echo $ini['custom']['mark_x'] ?>">
						<input type="hidden" id="marky" name="marky" value="<?php echo $ini['custom']['mark_y'] ?>">
						<input type="submit" class="button" value="Applica modifiche">
					</div>
					<p>Seleziona la nuova posizione del marker dalla mappa qui sotto riportata.
					Si consiglia di rimanere all'interno dell'area tratteggiata per contenere sempre il marker visibile in ogni dimensione dello schermo.</p>
				</li>
			</ul>
			
		</form>
	</div>
	
	<div class="content-wide">
		<!-- edit marker on big map -->
		<div id="map">
			<div id="mapmark"></div>
			<div id="cell"></div>
		</div>
	</div>

	
	<div class="content-wide _withbottom dark">
	
		<h3><span class="ytlogo">Youtube Video</span></h3>
		
		<div id="youtubes" class="choicer"></div>
		
		
	</div>

	
	<div class="content-wide _withbottom dark">
		<div class="center follows">
			<h4>Follow me</h4>
			<div class="moon-icons">
			<a id="ico-gp" href="https://plus.google.com/+NereoCostacurta" target="_blank">&#xe902;</a><!--
			--><a id="ico-fb" href="https://www.facebook.com/n.costacurta" target="_blank">&#xe903;</a><!--
			--><a id="ico-in" href="https://www.linkedin.com/in/nereocostacurta" target="_blank">&#xe906;</a>
			</div>
		</div>
	</div>
</div>


<div id="yt-bkg">
	<form id="form-yt" autocomplete="off">
		<h4>YouTube Video</h4>
		<input type="hidden" name="article_id" data-default="" value="">
		<input type="hidden" name="video_id" data-default="" value="">
		<input type="hidden" name="video_w" data-default="560" value="560">
		<input type="hidden" name="video_h" data-default="315" value="315">
		<p>Inserisci il codice di incorporamento <b>iframe</b> di YouTube (lo trovi nelle opzioni di condivisione video)</p>
		<textarea placeholder='&lt;iframe width="560" height="315" src="https://www.youtube.com/embed/VIDEOID" frameborder="0" allowfullscreen>&lt;/iframe>'></textarea>
		<table>
			<tr><th>pagina:</th><td id="s_article_title"></td></tr>
			<tr><th>video id:</th><td id="s_video_id"></td></tr>
			<tr><th>larghezza:</th><td id="s_video_w"></td></tr>
			<tr><th>altezza:</th><td id="s_video_h"></td></tr>
			<tr><th>inizio:</th><td class="numbers"><input type="text" name="video_start" value="0"> seconds</td></tr>
			<tr><th>fine:</th><td class="numbers"><input type="text" name="video_end" value=""> seconds</td></tr>
		</table>
		<div class="center">
			<br>
			<span class="nobutton nb-x" id="clear-yt">Cancella proprietà</span><br>
			<input class="button" type="submit" value="Salva modifiche">
		</div>
	</form>
</div>






<?php
	Links::getJQuery();
	Links::script('js/editor.js');
?>

</body>
</html>