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

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<meta charset="utf-8">
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
	
	<title>M.B.C. WebSite Manager</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">
	
	<?php $Colibrì->getBaseCss() ?>

	<style type="text/css">
	#all-templates{
		line-height:0;
	}
	#all-templates input{
		visibility:hidden;
	}
	.template{
		max-width:256px;
		margin:10px;
		line-height:normal;
		display:inline-block;
	}
	#my-template{
		display:block;
		margin:10px auto;
	}
	.template .image,
	.template .imgcont{
		display:block;
		height:180px;
		background:#222 no-repeat center;
		background-size:cover;
	}
	.template p{
		padding-top:4px;
	}
	</style>
</head>






<body class="tools-bkg">

<?php

//find current web properties
$web = null;
$pdores = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1", PDO::FETCH_ASSOC) or die("Errore durante ricerca proprietà web [query]");
foreach ($pdores as $r){
	$web = $r;
}
$pdores->closeCursor();
if (!$web) die("Void properties!</body>");


//echo '<pre>'.print_r($web,true).'</pre>';die;
?>



<!-- popups -->

<div class="popup-cont" id="templates-pop">
	<div class="popup overthrow">
		<div>
		<h5></h5>
		<div id="all-templates"></div>
		</div>
	</div>
	<h4>Templates disponibili</h4>
</div>



<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		<input type="hidden" name="id" id="w-id" value="<?php echo $web['id'] ?>">
		
		<!-- START main -->
		<div class="main">
			<h1>MBC - WebSite Manager</h1>
		
			<div class="inputs maxi aligned">
				<h4>Titolo del sito</h4>
				<input id="w-title" name="title" type="text" value="<?php echo htmlentities($web['titolo'],ENT_QUOTES) ?>" placeholder="Titolo">
			</div>
		
			<div class="inputs maxi aligned">
				<h4>Proprietario</h4>
				<input id="w-author" name="author" type="text" value="<?php echo htmlentities($web['autore'],ENT_QUOTES) ?>" placeholder="Titolo">
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Breve descrizione</h4>
				<textarea id="w-descr" class="short" name="descr" placeholder="Breve descrizione del sito"><?php echo htmlentities($web['descr']) ?></textarea>
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Motto (facoltativo)</h4>
				<textarea id="w-motto" class="short" name="quote" placeholder="Il tuo motto personale"><?php echo htmlentities($web['motto']) ?></textarea>
			</div>
			
			<div class="inputs maxi aligned">
				<h4>Informazioni generali</h4>
				<p>Inserire indicazioni stradali, recapito, numeri telefonici, email, partita IVA...</p>
				<p>Tenere presente che inserire la propria mail testuale attirerà grandi quantità di spam. Se il template in uso è dotato di una <i>form</i> di contatto, è consigliabile evitare questo rischio.</p>
				<textarea id="w-info" name="info" placeholder="Informazioni..."><?php echo htmlentities($web['info']) ?></textarea>
			</div>
			
			<div class="inputs center hide-on-cell">
				<b class="btn save-arctic">Aggiorna sito</b>
			</div>
			
		</div>
		<!-- END main -->



		<div class="right">
		
			<div class="inputs maxi aligned">
				<h4>Email amministratore</h4>
				<p>Inserire l'email attraverso la quale verrai contattato dal sito.</p>
				<p>Questa mail non sarà direttamente visibile nel sito, ma nascosta lato server.</p>
				<input id="w-email" name="email" type="text" value="<?php
					if ($web['email']){
						$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
						$decrypted = $ENCRYPTER->decrypt($web['email']);
						echo htmlentities($decrypted,ENT_QUOTES);
					}
				?>" placeholder="Email">
			</div>
		
			<div class="inputs maxi aligned">
				<h4>chiavi reCAPTCHA (v2)</h4>
				<p>(facoltativo) Inserire le chiavi ottenute all'indirizzo <a href="https://www.google.com/recaptcha/admin" target="_blank">reCAPTCHA admin</a> per questo sito</p>
				<p>Chiave pubblica (per HTML):</p>
				<input id="w-recap-1" name="rcptc[k]" type="text" value="<?php echo htmlentities($web['recaptcha_key'],ENT_QUOTES) ?>" placeholder="public key">
				<p>Chiave segreta (lascia vuoto per non cambiare):</p>
				<input id="w-recap-2" name="rcptc[s]" type="text" value="" placeholder="secret key">
			</div>
			
			<div class="inputs center">
				<b class="btn save-arctic">Aggiorna sito</b>
			</div>
			
			<br><br>
		
			<div class="inputs maxi aligned">
				<?php
					//template makers should be trusted... there whould be an official revisor/validator
					$templatepath = $CONFIG['mbc_cms_dir'].'templates/'.$web['template'].'/';
					$phisicaltp = $CONFIG['c_dir'].'templates/'.$web['template'].'/';
					$json = null;
					if (is_file($phisicaltp.'properties.json'))
						$json = json_decode(file_get_contents($phisicaltp.'properties.json'), true);
					if (!$json)
						echo '<b>Missing template properties! JSON file not well formed or missing.</b>';
					$img = $templatepath.'screenshots/0.png';
					if (!is_file($phisicaltp.'screenshots/0.png')) $img = null;
				?>
				<div id="my-template" class="template">
					<h4>Template in uso</h4>
					<input id="w-templ" type="hidden" value="<?php echo htmlentities($web['template'],ENT_QUOTES) ?>">
					<div class="imgcont"<?php echo ($img ? ' style="background-image:url(\''.$img.'\')"' : '') ?>></div>
					<p><b>Titolo:</b> <?php echo htmlentities($json['name']) ?></p>
					<p><b>Autore:</b> <?php echo htmlentities($json['author']) ?></p>
					<p><b>Descrizione:</b> <?php echo nl2br(htmlentities($json['description'])) ?></p>
					<div class="tools">
					<!-- TODO: check updates -->
					<?php if ($json['web']): ?>
						<p><a target="_blank" href="<?php echo htmlentities($json['web'][0],ENT_QUOTES) ?>" title="<?php echo htmlentities($json['web'][1],ENT_QUOTES) ?>"><b class="sicon"><i class="star"></i></b>
							Sito di riferimento</a></p>
					<?php
						endif;
						if (is_file($phisicaltp.'editor.php')):
					?>
						<p><a href="<?php echo $templatepath.'editor.php' ?>"><b class="sicon"><i class="pencil"></i></b>
							Strumenti</a></p>
					<?php endif; ?>
					</div>
				</div>
			</div>
			
			<div class="inputs center">
				<b class="btn red" id="change-template">Cambia template</b>
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

<script>
$(function(){
	$('.save-arctic').click(function(){
		BUSY.start();
		$.post('database/website-edit.php',$('#my-article').serialize(),null,'json')
			.success(function(json){
				if (json.error !== false) return alert("ERRORE:\n"+json.error);
				alert("Sito aggiornato correttamente");
			})
			.error(function(e){
				console.log(e);
			})
			.always(function(){
				BUSY.end();
			});
	});
	var openedtempl = false;
	$('#templates-pop .popup').on('scroll.lazy',checkLoadImage);
	$('#change-template').click(function(){
		$('#templates-pop').modalbox('open');
		$.get('database/website-templates.php',null,null,'json')
			.success(function(json){
				console.log(json);
				if (json.error !== false) return alert("Errore:\n"+json.error);
				if (openedtempl){
					updateView('#templates-pop');
					return false;
				}
				openedtempl=true;
				var c = $('#w-templ').val();
				var templates = new Array(json.templates.length);
				$.each(json.templates,function(k,v){
					templates[k] = '<div id="template-'+k+'" class="template">'+
						'<div class="imgcont load" data-thumb="templates/'+printAttr(v.folder)+'/screenshots/0.png"><label class="image">'+
							'<input id="w-templ-'+k+'" name="newtemplate" type="radio" value="'+printAttr(v.folder)+'"'+(c==v.folder ? ' checked' : '')+'>'+
						'</label></div>'+
						'<p><b>Titolo:</b> '+printText(v.name)+'</p>'+
						'<p><b>Autore:</b> '+printText(v.author)+'</p>'+
						'<p><b>Descrizione:</b> '+printText(v.description).replace("\s+"," ")+'</p>'+
						'<div class="tools">'+
							('web' in v ? '<p><a target="_blank" href="'+printAttr(v.web[0])+'" title="'+printAttr(v.web[1])+'"><b class="sicon"><i class="star"></i></b> Sito di riferimento</a></p>' : '')+
							'<p><label for="w-templ-'+k+'"><b class="sicon"><i class="hearth"></i></b> SCEGLI</label></p>'+
						'</div>'+
					'</div>';
				})
				$('#all-templates').html(templates.join(''));
				//$('#templates-pop')[0].modalbox.refresh();
				$('#all-templates input').change(function(){
					var newt = this.value;
					$.get('database/website-edit-template.php',{template:newt},null,'json')
						.success(function(json){
							console.log(json);
							if (json.error) return alert("ERRORE:\n"+json.error);
							$('#my-template p, #my-template .tools').remove();
							$('#w-templ').val(newt);
							var bkg = $('#my-template .imgcont').css('background-image').replace(/templates\/[a-z0-9_\- ]+\//i,"templates/"+newt+"/");
							$('#my-template .imgcont').css('background-image',bkg);
							$('#templates-pop').modalbox('close');
						})
						.error(function(e){
							console.log(e);
						})
				});
				updateView('#templates-pop');
			})
			.error(function(e){
				console.log(e);
			})
	});
})
</script>

</body>
</html>