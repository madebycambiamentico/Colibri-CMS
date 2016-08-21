<?php

/**
* add option to switch from local file to cdn hosted files.
*/

require '../../../../config.php';

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true); // only webmasters are meant to edit database.



?><!DOCTYPE HTML>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>HighlightJS Plugin by Colibrì</title>
	<style>
	*{padding:0; margin:0;}
	body{
		font: 14px 'open sans',helvetica,arial,serif;
		padding:16px;
	}
	h2{
		text-align: center;
		font-size: 22px;
		font-weight: normal;
		border-bottom: 1px solid #e0e0e0;
		padding-bottom: 10px;
		max-width:400px;
		margin:0 auto;
	}
	p{padding-bottom:5px;}
	table{
		/*border:1px solid #ccc;*/
		border-collapse:collapse;
		/*width:100%;*/
		max-width:400px;
		margin:5px auto;
	}
	table td{
		padding:4px;
		vertical-align:middle;
	}
	
	.logo{
		vertical-align: text-bottom;
		width:32px;
		height:32px;
		animation: highlight 1s infinite;
	}
	@keyframes highlight{
		0%{ transform: translateX(-10px) }
		15%{ transform: translateX(10px) }
		100%{ transform: translateX(-10px) }
	}
	
	.switch{
		display:inline-block;
		position:relative;
		width:40px;
		height:16px;
		margin:1px;
		vertical-align: middle;
	}
	
	.switch .on,
	.switch .off{
		position:absolute;
		top:0;
		height:16px;
		transition:width 0.2s;
	}
	.switch .on{
		width:8px;
		border-radius:8px 0 0 8px;
		background: #8cceff;
		box-shadow: 0 0 4px #0075B4 inset;
		left:0;
	}
	.switch .off{
		width:32px;
		border-radius:0 8px 8px 0;
		background: #ccc;
		box-shadow: 0 0 4px #888 inset;
		right:0;
	}
	.switch.on .on{
		width:32px;
	}
	.switch.on .off{
		width:8px;
	}
	
	.switch .ball{
		position:absolute;
		left:0;
		top:0;
		margin:-1px;
		height:16px;
		width:16px;
		border:1px solid #666;
		background:#f0f0f0;
		box-shadow: 0 0 2px #aaa inset;
		border-radius:9px;
		transition:left 0.2s;
	}
	.switch.on .ball{
		left:24px;
	}
	
	.switch input{
		visibility:hidden;
		width:1px;
		height:1px;
		position:absolute;
		left:0;
		top:0;
	}
	.switch label{
		position:absolute;
		display:block;
		cursor:pointer;
		width:50%;
		height:100%;
		top:0;
	}
	.switch label.left{
		right:0;
	}
	.switch label.right{
		left:0;
	}
	</style>
</head>

<body>

<h2><img src="../img/logo-32.png" class="logo"> HighlightJS Plugin by Colibrì</h2>
<br>

<table>
<tr>
<td>Use <b>local files</b>:</td>
<td>
<?php
	$json = json_decode( file_get_contents(__DIR__ . "/../director.json"), true );
	$iscdn = ($json['template']['js']['auto'][0] === 'highlight.min.js');
?>
	<div class="switch">
		<div class="on"></div> <div class="off"></div> <div class="ball"></div>
		<label class="left" title="CDN"><input class="left" type="radio" value="0" name="localfiles" <?php echo $iscdn ? 'checked' : '' ?>></label>
		<label class="right" title="local"><input class="right" type="radio" value="1" name="localfiles" <?php echo $iscdn ? '' : 'checked' ?>></label>
	</div>
</td>
</tr>
</table>
<p>Choose between loading local files or (faster and probably optimized) load from CDN.</p>
<p>Cdnjs Cloudflare serves this files: <code>//cdnjs.cloudflare.com/ajax/libs/highlight.js/x.x.x/styles/default.min.css</code> and <code>//cdnjs.cloudflare.com/ajax/libs/highlight.js/x.x.x/highlight.min.js</code> which contains the top 20 used languages.</p>
<p>Local files are stored in <code>css/default.min.css</code> and <code>js/highlight.min.js</code> (from the plugin directory)</p>
<br>
<p style="font-size:smaller;text-align:center;">- thank you for using Colibri HighlightJS Plugin! -</p>

<script src="../../../../js/jquery/jquery-2.1.4.min.js"></script>
<script>
$.fn.switcher = function(){
	return this.each(function(){
		var $swtch = $(this);
		var notfirst = false;
		$swtch.find('input').change(function(){
			
			if ($(this).hasClass('left'))
				$swtch.addClass('on');
			else
				$swtch.removeClass('on');
			
			if (notfirst){
				$.post('highlight-custom.ajax.php',{localfiles: this.value}, null, 'json')
					.done(function(json){
						if (json.error)
							return alert("Errore:\n"+json.error);
						else
							console.info("localfile option updated!");
					})
					.fail(function(e){
						console.error(e);
					})
			}
			else
				notfirst = true;
		});
		$swtch.find('input:checked').change();
	})
}
$('.switch').switcher();
</script>
</body>

</html>