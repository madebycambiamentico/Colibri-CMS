<?php

require_once "config.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(0);

$Colibrì = new Colibri();

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. Bacheca</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>
	<link rel="stylesheet" href="<?php $Colibrì->link("css/profile.min.css") ?>">

	<style type="text/css">
	</style>
</head>






<body class="tools-bkg">


<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<div id="my-article">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - Bacheca</h1>


<?php
if ($pdores = $pdo->query("SELECT id, hasimage, nome FROM utenti WHERE id = ".$_SESSION['uid']." LIMIT 1", PDO::FETCH_ASSOC)){
	foreach ($pdores as $r):
?>
			<div id="pf-image" class="_128" style="background-image:url('img/users/<?php echo $r['hasimage'] ? $r['id'].'/' : 'default/' ?>face-128.png')"></div>
			<div class="inputs ultra tools center">
				<p><a href="./database/logout.php?redirect">
					<b class="sicon"><i class="off"></i></b>
					Scollegati</a></p>
				<br>
				<p style="font-size:larger">Benvenuto <?php echo htmlentities($r['nome']) ?>!</p>
				<p>Ecco gli ultimi articoli inseriti:</p>
			</div>
<?php
	endforeach;
	$pdores->closeCursor();
}
?>
			
			
			<br>
			
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
function getQueryArticle($type=1){
	return "SELECT articoli.*, immagini.src FROM articoli
	LEFT JOIN immagini ON articoli.idimage = immagini.id
	WHERE articoli.idtype = {$type} AND NOT articoli.isgarbage
	ORDER BY articoli.dataedit DESC LIMIT 3";
}
	?>
			
			<div id="all-articles"><?php

foreach ($TYPES as $tid => $t){
	//query this article type
	if ($pdores = $pdo->query(getQueryArticle($tid), PDO::FETCH_ASSOC)){
		$hasrows = false;
		foreach ($pdores as $r):
			if (!$hasrows) echo '<h3>'.htmlentities($t['n']).'</h3>';
			$hasrows = true;
			$link = htmlentities($r['remaplink'],ENT_QUOTES);
?>
<div class="art-cont type-<?php echo $r['idtype'] ?>" id="art-<?php echo $r['id'] ?>">
	<div class="art-img-cont<?php echo ($r['src'] ? ' load" data-thumb="img/thumbs/'.htmlentities($r['src'],ENT_QUOTES) : ''); ?>">
		<?php
			//print <a> link only if it's not a "link" type
			if ($tid != 3) echo '<a href="'.$link.'" class="art-img"></a>';
			else echo '<label class="art-img"></label>';
		?>
	</div>
	<div class="art-props">
		<h4><?php echo htmlentities($r['titolo']).($r['isindex'] ? ' <i class="art-type">[PAGINA INIZIALE / HOME]</i>' : ''); ?></h4>
		<div class="art-desc"><?php
			//print <a> link only if it's not a "link" type
			if ($tid != 3) echo '<a href="'.$link.'">'.htmlentities($r['inbreve']).' <b title="continua a leggere" class="dots">[...]</b></a>';
			else echo htmlentities($r['inbreve']);
		?></a></div>
	</div>
</div>

<?php endforeach;
		$pdores->closeCursor();
	}
}
			?></div>
		</div>
		<!-- END main -->



		<div class="right">
			<h4>Le ultime immagini caricate</h4>
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

<!-- main script -->
<script src="js/common.js"></script>
<script>
$(function(){
	//load images...
	updateView('#my-article',true);
});
</script>

</body>
</html>