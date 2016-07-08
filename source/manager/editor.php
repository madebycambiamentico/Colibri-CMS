<?php

/*
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/
if (!isset($CONFIG)){ header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); die; }

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(1);

$Colibrì = new Colibri();
$Pop = new Popups();

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Article Editor</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<?php $Pop->getForAlbums() ?>

<?php $Pop->getForFileManager() ?>

<!-- END popups -->










<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>
	
	<?php
//determine if requested an edit or new album
$id = null;
if (isset($_GET['q'])){
	if ($_GET['q'] != 'new'){
		$id = intval($_GET['q'],0);
		if ($id){
			//retrieve all data from article... + image...
			if ($pdores = $pdo->query("SELECT articoli.*, immagini.src as 'image' FROM articoli LEFT JOIN immagini ON articoli.idimage = immagini.id WHERE articoli.id={$id}", PDO::FETCH_ASSOC)){
				$ARTICLE = null;
				foreach ($pdores as $r){
					$ARTICLE = $r;
				}
				$pdores->closeCursor();
				if ($ARTICLE == null) $id = null;
				else{
					//parse dates
					$ARTICLE['_di'] = new DateTime($ARTICLE['data']);
					$ARTICLE['_de'] = new DateTime($ARTICLE['dataedit']);
				}
			}
			else $id = null;
		}
		else $id = null;
	}
}
	?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		<input type="hidden" name="id" id="art-id" value="<?php echo ($id ? $id : '') ?>">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - Article Editor</h1>
		
			<div class="inputs maxi aligned">
				<h4>Titolo dell'articolo</h4>
				<input id="art-title" name="title" type="text" value="<?php echo ($id ? htmlentities($ARTICLE['titolo'],ENT_QUOTES) : '') ?>" placeholder="Titolo">
			</div>
			
			<!-- tinymce -->
			<div class="inputs maxi aligned">
				<h4>Contenuto dell'articolo</h4>
				<textarea id="art-body" class="tiny-area" name="content" placeholder="Contenuto dell'articolo"><?php echo ($id ? htmlentities($ARTICLE['corpo']) : '') ?></textarea>
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Breve descrizione del contenuto (facoltativo)</h4>
				<textarea id="art-desc" class="short" name="description" placeholder="Riassunto dell'articolo"><?php echo ($id ? htmlentities($ARTICLE['inbreve']) : '') ?></textarea>
			</div>
			
			<!-- image for article -->
			<div class="inputs field-btn maxi aligned">
				<div id="art-thumb-table">
					<div id="art-thumb" class="image"<?php echo ($id ?
						($ARTICLE['image'] ? ' style="background-image:url(\'img/thumbs/'.htmlentities($ARTICLE['image'],ENT_QUOTES).'\')"' : '')
						: '');
					?>></div>
					<div>
						<p>
						<input id="art-image" name="image" type="text" class="ronly" placeholder="immagine principale..." value="<?php echo ($id ? ($ARTICLE['image'] ? htmlentities($ARTICLE['image'],ENT_QUOTES) : '') : '') ?>" readonly><!--
						--><b id="art-fm-1-del" class="btn mid tinymcefont">&#xe012;</b><!--
						--><b id="art-fm-1" class="btn tinymcefont">&#xe034;</b>
						</p>
					</div>
				</div>
			</div>
			
			<?php /*
			<ul class="inputs maxi aligned">
				<li>Video in evidenza (opzionale)<br>[work in progress...]</li>
			</ul>
			<div class="inputs maxi aligned field-btn">
				<input id="ealb-video" name="video" type="text" class="ronly" placeholder="video principale..." readonly><!--
				--><b id="ealb-fm-2-del" class="btn tinymcefont">&#xe012;</b><!--
				--><b id="ealb-fm-2" class="btn mid tinymcefont">&#xe034;</b>
			</div>
			*/ ?>
			
			<div class="inputs center hide-on-cell">
				<b class="btn save-arctic">Salva Articolo</b>
			</div>
		
		</div>
		<!-- END main -->



		<div class="right">
		
			<div class="inputs maxi aligned">
				<h4>Proprietà articolo</h4>
				<input type="hidden" name="wasindex" value="<?php echo ($id ? ($ARTICLE['isindex'] ? 1 : 0) : 0) ?>">
				<input type="hidden" name="wasindexlang" value="<?php echo ($id ? ($ARTICLE['isindexlang'] ? 1 : 0) : 0) ?>">
				<p><label><input type="checkbox" name="isindex" value="1" <?php echo ($id ? ($ARTICLE['isindex'] ? 'checked' : '') : '') ?>> Pagina iniziale del sito</label><br>
				<label><input type="checkbox" name="isinmenu" value="1" <?php echo ($id ? ($ARTICLE['isinmenu'] ? 'checked' : '') : '') ?>> Mostra nel menu</label></p>
				
				<h4>Gestione lingue</h4>
				<p>
				<label><input type="checkbox" name="isindexlang" value="1" <?php echo ($id ? ($ARTICLE['isindexlang'] ? 'checked' : '') : '') ?>>
				Pagina iniziale del sito per la lingua selezionata</label>
				<select name="lang"><?php
					$langs = [
						'sigla' => ['it','en','de','fr'],
						'estesa' => ['italiano','english','deutsch','française']
					];
					foreach ($langs['sigla'] as $o => $opt)
						echo "<option value='{$opt}' ".($id ? ($ARTICLE['lang']==$opt ? 'selected' : '') : '').">{$langs['estesa'][$o]} ({$opt})</option>";
				?></select>
				</p>
				<p>
				Articolo originale:<br>
				<select id="art-parentlang" name="parentlang">
					<option value="0">Nessuno</option><?php

$temp = [];
if ($id) $temp[] = $ARTICLE['lang'];
if ($pdostat = $pdo->prepare("SELECT id,titolo FROM articoli WHERE (idarticololang IS NULL OR idarticololang = id)".($id ? " AND (lang != ? OR id = {$id})" : ''))){
	if ($pdostat->execute($temp)){
		while ($r = $pdostat->fetch(PDO::FETCH_ASSOC)){
			echo '<option value="'.$r["id"].'"'.($id && $ARTICLE['idarticololang'] == $r["id"] ? ' selected' : '').'>'.htmlentities($r["titolo"],ENT_NOQUOTES).'</option>';
		}
	}
}
				?></select>
				</p>
				
				<h4>Smart-Links all'articolo</h4>
				<input id="art-smart-1" name="map" value="<?php echo ($id ? htmlentities($ARTICLE['remaplink'],ENT_QUOTES) : '') ?>" type="text" class="ronly" readonly>
				<input id="art-smart-date" type="hidden" value="<?php echo ($id ? $ARTICLE['_de']->format('Y/m/d/') : date('Y/m/d/')) ?>">
				<input id="art-smart-2" value="<?php echo ($id ? $ARTICLE['_de']->format('Y/m/d/').htmlentities($ARTICLE['remaplink'],ENT_QUOTES) : date('Y/m/d/')) ?>" type="text" class="ronly" readonly>
				
				<h4>Classe articolo</h4>
				<select id="art-type" name="type"><?php

$selected_type = ($id ? $ARTICLE['idtype'] : 1);
if ($pdores = $pdo->query("SELECT id,nome,remapprefix FROM articoli_types", PDO::FETCH_ASSOC)){
	foreach ($pdores as $r){
		echo '<option value="'.$r["id"].'" data-prefix="'.$r['remapprefix'].'"'.($selected_type == $r["id"] ? ' selected' : '').'>'.htmlentities($r["nome"],ENT_NOQUOTES).'</option>';
	}
	$pdores->closeCursor();
}
				?></select>
				
				<h4>Sotto-articolo di...</h4>
				<select id="art-subof" name="parent">
					<option value="0">Nessuno</option><?php

$selected_type = ($id ? $ARTICLE['idarticolo'] : 0);
if ($pdores = $pdo->query("SELECT id,titolo FROM articoli WHERE idtype = 1 AND idarticolo IS NULL".($id ? " AND id!={$id}" : ''), PDO::FETCH_ASSOC)){
	foreach ($pdores as $r){
		echo '<option value="'.$r["id"].'"'.($selected_type == $r["id"] ? ' selected' : '').'>'.htmlentities($r["titolo"],ENT_NOQUOTES).'</option>';
	}
	$pdores->closeCursor();
}
				?></select>
			
				<?php if ($id): ?>
				<h4>Sub-Articoli correlati</h4>
				<p>Seleziona per rimuovere dipendenza</p><div id="all_sub_arts"><?php

if ($pdores = $pdo->query("SELECT id,titolo FROM articoli WHERE idarticolo = {$id}", PDO::FETCH_ASSOC)){
	$hasdenpendecies = false;
	foreach ($pdores as $r){
		$hasdenpendecies = true;
		echo '<p><label><input type="checkbox" name="removelinkedart[]" value="'.$r["id"].'"> '.htmlentities($r["titolo"],ENT_NOQUOTES).'</label></p>';
	}
	$pdores->closeCursor();
	if (!$hasdenpendecies) echo '<p><b>(nessun articolo correlato a questa pagina)</b></p>';
}
				endif; ?></div>
			</div>
			
			<div class="inputs center hide-on-cell">
				<b class="btn save-arctic">Salva Articolo</b>
			</div>
			
			
			<!-- START album selection -->
			<?php $Colibrì->getaAlbumEditor('<h4>Seleziona Album Fotografico</h4>'); ?>
			<!-- END album selection -->
			
			<div class="inputs center">
				<b class="btn save-arctic">Salva Articolo</b>
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
<script src="js/tinymce/tinymce.min.js"></script>
<script src="js/overthrow-0.7.1/overthrow.min.js"></script>
<script src="js/simple-modal-box.min.js"></script>
<script src="js/diatrics-remover.min.js"></script>

<!-- main script -->
<script src="js/common.js"></script>
<script src="js/albums-manager.min.js"></script>
<script src="js/article-manager.min.js"></script>

<script>
</script>

</body>
</html>