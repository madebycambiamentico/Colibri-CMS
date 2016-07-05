<?php

/* MBC file manager client side */

require_once "config.php";
require_once "functions.inc.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(0);

?><!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>M.B.C. file manager</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<!-- general styles -->
	<link rel="stylesheet" href="css/icons.min.css">
	<link rel="stylesheet" href="css/sicons.custom.min.css">
	<link rel="stylesheet" href="css/toolbar.min.css">
	<link rel="stylesheet" href="css/inputs.min.css">
	<link rel="stylesheet" href="css/style.min.css">
	
	<!-- plugins -->
	<link rel="stylesheet" href="../../css/modalbox.min.css">
	<link rel="stylesheet" href="js/mediaelement-2.19.0/build/mediaelementplayer.min.css"/>
	<link rel="stylesheet" type="text/css" href="js/dropzone-4.2.0/dist/min/dropzone.min.css">
	
	<style type="text/css">
		#upload-files .extallowed{
			position: absolute;
			top: 35px;
			z-index: 50;
			left: 5px;
			color: #97ABC2;
		}
		
		/* dropzone customization */
		.dropzone{
			position:relative;
			border:none;
			height:100%;
			transition:background-color 0.5s;
		}
		.dropzone .dz-message{
			position:relative;
			z-index:0;
			margin:0;
			width:100%;
			height:100%;
			display:table;
		}
		.dropzone.dz-drag-hover .dz-message{
			opacity:1;
		}
		.dropzone .dz-message>div{
			display:table-cell;
			vertical-align:middle;
			text-align:center;
			height:100%;
		}
		.dropzone.dz-drag-hover{
			border:none;
			background:#bfd6e3;
		}
		.dropzone i.cloud{
			display:block;
			opacity:0.25;
			position:absolute;
			top:50%;
			left:50%;
			height:135px;
			width:180px;
			background: url(img/big-upload.png);
			margin:-62.5px 0 0 -90px;
			transition:opacity 0.3s;
			z-index:-1;
		}
		.dropzone.dz-drag-hover i.cloud{
			opacity:1;
		}
		.dz-preview.icon{
			width:auto;
			height:auto;
		}
		.dz-preview .dz-details,
		.dz-preview .dz-success-mark,
		.dz-preview .dz-error-mark{
			background:none !important;
		}
		.dropzone .dz-preview .dz-image{
			border-radius:0;
			display:table-cell;
			vertical-align:middle;
		}
		.dropzone .dz-preview .dz-image img {
			margin: 0 auto;
		}
	</style>
</head>

<body class="tools-bkg">

<header>
	<div id="manager-tools"><!--
		--><b class="sicon" title="Carica un file" id="mt-open-upload"><i class="upload"></i></b><!--
		--><b class="sicon" title="Aggiungi cartella" id="mt-new-folder"><i class="folder-add"></i></b><!--
		--><b class="sicon" title="Cancella file selezionati" id="mt-delete-all"><i class="trash"></i></b><!--
	--></div>
	<div id="searchbar">
		<input type="text" id="search" placeholder="cerca..."><!--
		--><b class="sicon"><i id="search-icon" class="search"></i></b><!--
		--><b class="sicon" title="Assegna filtri" id="mt-open-filters"><i class="checkboard"></i></b>
	</div>
	<div id="filters">
		<?php
		$temp = ["immagini", "files", "video", "audio", "archivi"];
		//get filter in (Int) mode
		$filter = isset($_GET['filter']) ? getFilter($_GET['filter']) : false;
		for ($i=0; $i<count($CONFIG['allowed_ext']); $i++){
			if ($filter !== false && $filter !== $i) continue;
			echo "<p><input type='checkbox' id='filter-{$i}' value='{$i}' checked><label for='filter-{$i}'><b class='sicon'><i class='checkboard-ok'></i></b></label><label class='desc' for='filter-{$i}'>".$temp[$i]."</label></p>";
		}
		?>
	</div>
</header>



<!-- popups -->
<div class="popup-cont" id="image-preview">
	<h4>Anteprima Immagine</h4>
	<div class="popup">
		<h5></h5>
		<div class="image-wrapper"></div>
	</div>
</div>

<div class="popup-cont dialog" id="audio-preview">
	<h4>Anteprima Audio</h4>
	<div class="popup">
		<div class="audio-wrapper"></div>
	</div>
</div>

<div class="popup-cont" id="video-preview">
	<h4>Anteprima Video</h4>
	<div class="popup">
		<h5></h5>
		<div class="video-wrapper"></div>
	</div>
</div>

<div class="popup-cont" id="upload-files">
	<h4>Carica o Sovrascrivi i files</h4>
	<div class="extallowed"><?php
		if ($filter !== false){
			//set filter as CONFIG index (string)
			$filter = getGroupFilter($filter);
			echo "Estensioni permesse: <i>".implode(", ", $CONFIG['allowed_ext'][$filter]).'</i>';
		}
	?></div>
	<form id="my-dropzone" action="uploadHandler.php" method="POST" enctype="multipart/form-data" class="dropzone">
		<input type="hidden" name="dir" id="uf-dir" value="">
		<div class="fallback">
			<input type="file" name="files[]" multiple>
			<input type="submit" value="Upload">
		</div>
	</form>
</div>

<div class="popup-cont dialog" id="edit-files">
	<h4>Modifica file/cartelle</h4>
	<div class="popup">
		<h5></h5>
		<form id="my-editor" action="file-edit.php" method="POST">
			<input id="ef-type" type="hidden" name="type" value="">
			<input id="ef-dir" type="hidden" name="dir" value="">
			<input id="ef-orig-title" type="hidden" name="original" value="">
			<ul class="inputs list">
				<li>Titolo<p class="small">File e cartelle devono avere nome unico.<br>Le cartelle possono avere solo caratteri minuscoli.<br>Eventuali caratteri speciali verranno rimossi.</p></li>
			</ul>
			<div class="inputs center">
				<input id="ef-title" name="f" type="text" placeholder="titolo..."><i class="ext" id="ef-ext"></i>
			</div>
			<div class="inputs center">
				<textarea id="ef-desc" name="desc" type="text" placeholder="descrizione..."></textarea>
			</div>
			<div class="inputs center">
				<a class="btn" id="saveAllEdits">SALVA</a>
			</div>
		</form>
	</div>
</div>


<!-- manager -->
<div id="wrapper">
	<div id="manager_files" class="filtering t-0 t-1 t-2 t-3 t-4"></div>
</div>




<!--p><a href="./img/icons/" target="_blank">manage icons</a></p>
<p><a href="./img/icons-s/" target="_blank">manage small icons...</a></p-->




<!-- initial loader fullscreen, until all scripts loaded.
		must be placed before </body> (and before scripts) -->
<div id="loader"></div>



<!--[if lte IE 8]>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
<![endif]-->
<!--[if gt IE 8]><!-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!--<![endif]-->


<!-- constant and variables initialization. needs PHP!!! -->
<script>
//retrieve filter and other options
var MANAGER_OPTIONS = (function(){
	/*
	* @return window mode: iframe / popup / single page
	* if popup / iframe then call function <mbcManagerCallback(args)> from parent window
	* if popup autoclose option available after image click
	*/
	function getManagerMod(){
		if (window.opener) return window.opener;						//run in popup from window.open(...)
		if (window.parent !== window.self){								//run in iframe
			//return topmost page
			if (window.parent !== window.top)
				return window.top;
			else
				return window.parent;
		}
		return false;															//standalone page
	}
	
	//get var from GET request
	var _GET = $.parseJSON('<?php echo json_encode($_GET) ?>');
	var _available = {
		mode : ['iframe','standalone','popup'],
		autoclose : [false,true],
		display : ['cozy','compact'],
		filter : ['all','img','file','video','music','archives',
					//allowed, but not canonical
					'image','images', 'files', 'media','videos', 'audio','audios', 'zip','misc','miscellaneous'],
		jsimage : [0,1,2]
	}
	var _default = {
		autoclose : false,
		display : 'cozy',
		filter : 'all',
		jsimage : 1
	}
	
	//control and update requests
	if (_GET.hasOwnProperty('jsimage')) _GET.jsimage = parseInt(_GET.jsimage);
	$.each(_GET, function(k,v){
		if (_default.hasOwnProperty(k)){
			if ($.inArray(v,_available[k]) !== -1) _default[k] = v
			else console.log(v+' not in '+k)
		}
	});
	
	//add constant:
	_default.opener = getManagerMod();
	_default.allowedExt = [<?php
		foreach ($CONFIG['allowed_ext'] as $i => $arr){
			//virgola divisoria degli array dei vari gruppi
			if ($i !== 'img' && $filter === false) echo ',';
			//skip if filtered request
			if ($filter !== false && $filter !== $i) continue;
			//gruppi
			if (count($arr))
				echo '["' . implode('","',$arr) .'"]';
			else
				echo '[]';
		}
	?>];
	_default.getIntFilter = function(){
		switch (MANAGER_OPTIONS.filter){
		case '0': case 'img': case 'image': case 'images':
			return 0;
		break;
		case '1': case 'file': case 'files':
			return 1;
		break;
		case '2': case 'video': case 'media': case 'videos':
			return 2;
		break;
		case '3': case 'music': case 'audio': case 'audios':
			return 3;
		break;
		case '4': case 'archives': case 'zip': case 'misc': case 'miscellaneous':
			return 4;
		break;
		default:
			return false;
		}
	}
	_default.onChange = (function(){
		//debug
		if (_default.opener === false || typeof _default.opener.mbcFileManagerOnChange !== 'function')
			return function(){ console.log("--- DEBUG ---"); console.log(arguments) };
		//opener call
		else
			return _default.opener.mbcFileManagerOnChange;
	})();
	
	return _default;
	
})();
</script>


<!-- script dependecies -->
<script src="js/mediaelement-2.19.0/build/mediaelement-and-player.min.js"></script>
<script src="../../js/simple-modal-box.min.js"></script>
<script src="js/dropzone-4.2.0/dist/min/dropzone.custom.js"></script>
<!-- dependent script -->
<script src="js/manager.min.js"></script>
<script src="js/upload.min.js"></script>

<script>
//start scripts...
$(window).load(function(){
	$('body').addClass('ready');
	BUSY.end();
	loadFolder("");
});
</script>

</body>

</html>