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
	#lang-options.hidden{
		display:none;
	}
	#lang-open.hidden{
		visibility:hidden;
	}
	#lang-codes{
		display: flex;
		flex-wrap: wrap;
		/*text-align:justify;*/
		line-height:0;
	}
	#lang-codes label i{
		font-style:normal;
		font-size:13px;
	}
	#lang-codes label{
		flex: 1 1 auto;
		height:20px;
		line-height:20px;
		display:inline-block;
		margin:2px;
		padding:3px 6px;
		border:1px solid #ccc;
		/*border-radius: 4px;*/
		background:#e6e6e6;
	}
	#lang-codes label:hover{
		border-color:#4e94e3;
	}
	#lang-codes input:checked + i{
		/*text-decoration:underline;*/
		font-weight: bold;
	}
	#lang-open{
		vertical-align:middle;
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


<div class="popup-cont" id="languages-pop">
	<h4>Lingue supportate dal sito</h4>
	<div class="popup overthrow">
		<br>
		<br>
		<div id="lang-codes"></div>
		<br>
		<div class="inputs center">
			<b class="btn" id="lang-apply">Applica</b>
		</div>
		<br>
	</div>
</div>




<!-- main editor -->

<div id="wrapper">

	<?php $Colibrì->getToolbar(); ?>

	<?php $Colibrì->getMenu(); ?>

	<div class="content">
	<form id="my-article" action="please.use.js/" autocomplete="off">
		<input type="hidden" name="id" id="w-id" value="<?php echo $web['id'] ?>">
		<div id="lang-supported"></div>
		
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
				<p>Tenere presente che inserire la propria e-mail testuale attirerà grandi quantità di <i>spam</i>. Se il template in uso è dotato di una <i>form</i> di contatto, è consigliabile evitare questo rischio.</p>
				<textarea id="w-info" name="info" placeholder="Informazioni..."><?php echo htmlentities($web['info']) ?></textarea>
			</div>
			
			<div class="inputs maxi aligned">
				<h4><label><input id="ismultilang" name="multilanguage" value="1" type="checkbox"<?php echo $web['multilanguage'] ? ' checked' : '' ?>> Sito multi-lingua</label>
					<b id="lang-open" class="sicon"><i class="options" title="Scegli lingue supportate"></i></b>
				</h4>
			</div>
				
			<div id="lang-options">
				<div class="inputs maxi aligned">
					<h4>Lingua predefinita del sito</h4>
					<select id="lang-default" name="defaultlang"></select>
				</div>
			</div>
		
			<br>
			<hr>
			
			<h3>Gestione avvisi email</h3>
			
			<div class="inputs maxi aligned">
				<p><i>Metodo di invio - leggere attentamente:</i></p>
				<br>
				<p><b>Manuale</b>: richiede di avviare lo script <a href="database/email-bodies/delivery.php" target="_blank"><i>delivery.php</i></a> manualmente tante volte quanto è necessario. Può essere anche gestito attraverso un <i>cron-job</i>.<br><span style="background-color:#ff5">Per attivare l'invio manuale imposta a <b>0</b> il <i>cooldown</i></span>.</p>
				<p><b>Automatico (sperimentale)</b>: non richiede azioni da parte dell'amministratore del sito e lo script continua a mandare mail ad intervalli fissati fino a che non ci saranno più email in uscita.<br><span style="background-color:#ff5">Per attivare l'invio automatico il <i>cooldown</i> dev'essere <b>&geq; 1 secondo</b>.</span></p>
			</div>
		
			<div class="inputs maxi aligned">
				<h4>Limite numero e-mail</h4>
				<p>Massimo numero di e-mail inviate alla volta. Imposta a <b>0</b> se il numero di e-mail che prevedi di spedire è limitato e/o non hai limiti imposti lato server.</p>
				<input id="w-delivery-n" name="delivery[n]" type="number" value="<?php echo $web['delivery_quantity'] ?>" min="0" placeholder="limite n. e-mail">
			</div>
		
			<div class="inputs maxi aligned">
				<h4>Cooldown spedizione e-mail</h4>
				<p>Instervallo in secondi prima di spedire il prossimo set di e-mail. Se <b>0</b> lo script verrà fermato al termine del primo invio di mail.</p>
				<p>Aiuto: 1'=60'', 5'=300'', 10'=600'', 15'=900'', 30'=1800, 1h=3600''.</p>
				<input id="w-delivery-t" name="delivery[t]" type="number" value="<?php echo $web['delivery_delay'] ?>" min="0" max="3600" placeholder="cooldown invio e-mail (secondi)">
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
						$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );
						$decrypted = $Encrypter->decrypt($web['email']);
						echo htmlentities($decrypted,ENT_QUOTES);
					}
				?>" placeholder="Email">
			</div>
		
			<div class="inputs maxi aligned">
				<h4>Chiavi reCAPTCHA (v2)</h4>
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
					//template makers should be trusted... there will be an official revisor/validator
					$templatepath = $Config->script_path.'templates/'.$web['template'].'/';
					$phisicaltp = CMS_INSTALL_DIR.'/templates/'.$web['template'].'/';
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
<script src="js/options.min.js"></script>

</body>
</html>