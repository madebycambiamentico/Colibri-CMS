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

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1);

$Colibrì = new Colibri();
$Pop = new Popups();

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. <?php echo (isset($_GET['garbage']) ? 'Cestino ' : ''); ?>Articoli</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- START popups -->

<div class="popup-cont" id="quick-edit-art">
	<h4>Modifica veloce articolo</h4>
	<form id="edit-article" class="popup overthrow" method="get">
		<h5></h5>
		<input type="hidden" id="eart-id" name="id" value="">
		<input type="hidden" id="eart-wasindex" name="wasindex" value="0">
		<input type="hidden" id="eart-wasindexlang" name="wasindexlang" value="0">
		
		<ul class="inputs list">
			<li>Titolo</li>
		</ul>
		<div class="inputs">
			<input id="eart-title" name="title" type="text" placeholder="titolo...">
			<input id="eart-smart-1" name="map" type="hidden" class="ronly">
		</div>
		
		<div class="inputs">
			<p>
				<label><input id="eart-isindex" type="checkbox" name="isindex" value="1"> Pagina iniziale del sito</label><br>
				<label><input id="eart-isinmenu" type="checkbox" name="isinmenu" value="1"> Mostra nel menu</label>
			</p>
		</div>
		
		<div class="inputs">
			<p>
				<select id="eart-lang" name="lang"><?php
					$langs = [
						'sigla' => ['it','en','de','fr'],
						'estesa' => ['italiano','english','deutsch','française']
					];
					foreach ($langs['sigla'] as $o => $opt)
						echo "<option value='{$opt}'>{$langs['estesa'][$o]} ({$opt})</option>";
				?></select><br>
				<label><input id="eart-isindexlang" type="checkbox" name="isindexlang" value="1">
				Pagina iniziale del sito per la lingua selezionata</label><br>
			</p>
		</div>
		
		<ul class="inputs list">
			<li>Descrizione<p class="small">(facoltativa) A capo riga non permessi.</p></li>
		</ul>
		<div class="inputs">
			<textarea id="eart-desc" name="description" placeholder="descrizione..."></textarea>
		</div>
		
		<ul class="inputs list">
			<li>Immagine in evidenza<p class="small">(facoltativa)</p></li>
		</ul>
		<div class="inputs field-btn">
			<input id="eart-image" name="image" type="text" class="ronly" placeholder="immagine principale..." readonly><!--
			--><b id="eart-fm-1-del" class="btn mid tinymcefont">&#xe012;</b><!--
			--><b id="eart-fm-1" class="btn tinymcefont">&#xe034;</b>
		</div>
		
		<br>
		<div class="inputs btn">
			<b class="btn" id="saveArctic">Salva Articolo</b>
		</div>
	</form>
</div>


<?php $Pop->getForFileManager() ?>

<!-- END popups -->










<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - <?php echo (isset($_GET['garbage']) ? 'Cestino ' : ''); ?>Articoli</h1>
			
			<p>Raccolta di tutti gli articoli presenti nel database</p>
	<?php
//get all article types:
$TYPES = [];
if ($pdores = $pdo->query("SELECT * FROM articoli_types", PDO::FETCH_ASSOC)){
	foreach ($pdores as $r){
		$TYPES[$r['id']] = [
			'n'		=> $r['nome'],
			'pfx'		=> $r['remapprefix']
		];
	}
	$pdores->closeCursor();
}
//determine filter request
$query = "SELECT articoli.*, immagini.src FROM articoli LEFT JOIN immagini ON articoli.idimage = immagini.id";
$addtoquery = [];
$filter = "nessuno";
if (isset($_GET['q'])){
	$idtype = intval($_GET['q'],0);
	if (!empty($TYPES[$idtype])){
		$addtoquery[] = "articoli.idtype=".$idtype;
		$filter = $TYPES[$idtype]['n'];
	}
}

if (isset($_GET['garbage'])){
	$addtoquery[] ='articoli.isgarbage';
	echo '<input type="hidden" name="isgarbage" value="1">';
}
else
	$addtoquery[] ='NOT articoli.isgarbage';


$query .= ' WHERE '.implode(' AND ',$addtoquery)." ORDER BY articoli.dataedit DESC LIMIT 50";
	?>
			<p>Filtro applicato: <b><?php echo htmlentities($filter) ?></b></p>
			
			<br>
			
			<div class="inputs maxi aligned tools">
				<?php if (!isset($_GET['garbage'])): ?>
				<p><a href="./editor?q=new"><b class="sicon"><i class="add"></i></b>
					Aggiungi nuovo articolo</a></p>
				<?php endif; ?>
				<p><input type='checkbox' id="sel-all" value="select">
					<label for="sel-all"><b class="sicon"><i class="flag"></i></b>
					Seleziona tutto</label></p>
				<p><input type='checkbox' id="desel-all" value="select">
					<label for="desel-all"><b class="sicon"><i class="less"></i></b>
					Deseleziona tutto</label></p>
				<p><input type='checkbox' id="del-all" value="delete">
					<label for="del-all"><b class="sicon"><i class="x"></i></b>
					Cancella articoli selezionati</label></p>
				<?php if (!isset($_GET['garbage'])){ ?>
				<br>
				<p><a href="./articles?garbage"><b class="sicon"><i class="trash"></i></b>
					Apri il cestino</a></p>
				<?php } else { ?>
				<p><input type='checkbox' id="resurr-all" value="resurr">
					<label for="resurr-all"><b class="sicon"><i class="reload"></i></b>
					Ripristina articoli selezionati</label></p>
				<?php } ?>
			</div>
			
			<br>
			
			<div id="all-articles"><?php

if ($pdores = $pdo->query($query, PDO::FETCH_ASSOC)){
	$hasrows = false;
	foreach ($pdores as $r):
		$hasrows = true;
?>
<div class="art-cont type-<?php echo $r['idtype'] ?>" id="art-<?php echo $r['id'] ?>">
	<div class="art-img-cont<?php echo ($r['src'] ? ' load" data-thumb="img/thumbs/'.htmlentities($r['src'],ENT_QUOTES) : ''); ?>">
		<label class="art-img">
			<input type="checkbox" name="trash[]" value="<?php echo $r['id'] ?>">
		</label>
	</div>
	<div class="art-props">
		<h4 data-t="<?php echo $r['idtype']; ?>"><?php echo htmlentities($r['titolo']).' <i class="art-type">('.htmlentities($TYPES[$r['idtype']]['n']).')</i>' ?></h4>
		<div class="art-desc"><?php echo htmlentities($r['inbreve']) ?></div>
		<div class="inputs maxi aligned tools">
			<p>
				<label><b class="sicon"><i class="pen"></i></b>
				<input type='checkbox' class="quick-mod" value="<?php echo $r['id'] ?>"
					<?php echo "data-t='{$r['idtype']}' data-img='".
							($r['src'] ? htmlentities($r['src'],ENT_QUOTES) : '').
							"' data-mnu='{$r['isinmenu']}' data-idx='{$r['isindex']}' data-idxl='{$r['isindexlang']}' data-lang='{$r['lang']}'";
					?>>
				Modifica veloce</label>
				
				<a href="./editor?q=<?php echo $r['id'] ?>"><b class="sicon"><i class="pencil"></i></b>
				Modifica</a>
				
				<?php if (isset($_GET['garbage'])): ?>
				<label class="resurrect" data-id="<?php echo $r['id'] ?>"><b class="sicon"><i class="reload"></i></b>
				Ripristina</label>
				<?php endif;  ?>
				
				<label class="quick-delete" data-id="<?php echo $r['id'] ?>"><b class="sicon"><i class="trash"></i></b>
				Cestina</label>
			</p>
		</div>
	</div>
</div>

<?php endforeach;
	if (!$hasrows) echo '<p style="font-size:larger;text-align:center;"><b>Nessun risultato per la ricerca corrente.</b></p>';
	$pdores->closeCursor();
}
			?></div>
		</div>
		<!-- END main -->



		<div class="right">
			
			<!-- navigazione -->
			<h4>Mostra solo articoli...</h4>
			<div class="inputs maxi aligned tools upp">
				<?php
				foreach($TYPES as $id => $t){
					echo '<p><a href="./articles?q='.$id.'"><b class="sicon"><i class="marker"></i></b> '.htmlentities($t['n']).'</a></p>';
				}
				if (!isset($_GET['garbage']))
					echo '<p><a href="./articles?garbage"><b class="sicon"><i class="marker"></i></b> nel cestino</a></p>'
				?>
			</div>
			
			<br>
			
			<!-- filtri on the fly -->
			<h4>Filtra Contenuti</h4>
			<br>
			<p>Ricerca per titolo</p>
			<div class="inputs maxi field-btn">
				<input type="text" id="search-title" placeholder="cerca..."><!--
				--><b class="sicon btn"><i class="search search-icon"></i></b>
			</div>
			
			<br>
			<p>Ricerca per descrizione</p>
			<div class="inputs maxi field-btn">
				<input type="text" id="search-desc" placeholder="cerca..."><!--
				--><b class="sicon btn"><i class="search search-icon"></i></b>
			</div>
			
			<?php if ($filter === 'nessuno') : ?>
			<br>
			<p>Filtra tipo articolo</p>
			<div class="inputs maxi">
				<select id="filtertype"><option value="0">Nessun filtro</option><?php
					foreach($TYPES as $id => $t){
						echo '<option value="'.$id.'">'.htmlentities($t['n']).'</option>';
					}
				?></select>
			</div>
			<?php endif; ?>
			
			
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
<script src="js/diatrics-remover.min.js"></script>

<!-- main script -->
<script src="js/common.js"></script>
<script>
	var ISGARBAGE = <?php echo (isset($_GET['garbage']) ? 'true' : 'false'); ?>;
	var artTypes = $.parseJSON('<?php echo json_encode($TYPES) ?>');
</script>
<script src="js/articles-list-manager.min.js"></script>

</body>
</html>