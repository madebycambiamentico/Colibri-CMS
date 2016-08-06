<!-- START COMMENT PLUGIN POPUP -->
<?php

/**
* add comments plugin to website template - form
*/

global $web, $page, $Config, $Pop;
if (!isset($Pop)){
	$Pop = new \Colibri\Popups;
}

//-----------------------------
//   CONTROL COMMENT ALLOWED
//-----------------------------

if (!$web['comment_allow'] || !$web['comment_allow']){
	goto anchor_skip;
}

//-----------------------------
//    CONTROL ALLOWED USERS
//-----------------------------

if ($web['comment_class'] != -1){
	if (!isset($_SESSION['uclass']))
		goto anchor_skip;
	if ($_SESSION['uclass'] < $web['comment_class'])
		goto anchor_skip;
}







//-----------------------------
//        COMMENT FORM
//-----------------------------


ob_start();

?>
		<input type="hidden" name="pageid" value="<?php echo $page['id']; ?>">
		<input type="hidden" name="commentid" id="CC_COMMENT_ID" value="">
		<textarea id="CC_COMMENT" class="tesource no-support" name="comment"></textarea>
		
		<?php if (isset($_SESSION['uclass'])): ?>
		<!-- comment form (logged user) -->
			<input type="hidden" id="CC_LOGGED" value="1">
		<?php else: ?>
		<!-- comment form (non-logged user) -->
			<input type="hidden" id="CC_LOGGED" value="0">
			<input id="CC_NAME"    type="text"  name="name"    placeholder="nome o soprannome">
			<input id="CC_WEBSITE" type="text"  name="website" placeholder="sito web">
			<input id="CC_EMAIL"   type="email" name="email"   placeholder="e-mail">
		<?php endif; ?>
		
		<p class="cmt-ac"><input type="submit" class="cmt-btn" value="Invia commento"></p>
<?php

$form_content = ob_get_contents();

ob_end_clean();

//generate popup
$Pop->generic_form(
	'colibri-comment-popup', ['loading cmt-form'], "Nuovo Commento",
	'', [],
	'POST', $Config->script_path . 'plugin/Colibri/Comments/template/save-comment.php', '_blank', null,
	$form_content
);






//-----------------------------
//        PLUGIN INFO
//-----------------------------

anchor_skip:


ob_start();

?>
		<input type="hidden" id="CC_PAGE_ID" value="<?php echo $page['id']; ?>">
		<input type="hidden" id="CC_PATH" value="<?php echo $Config->script_path; ?>">
		<div class="cmt-logo"></div>
		<p class="cmt-ac">Colibrì Comments versione 1.0<br><i>by Nereo Costacurta</i></p>
		<h3>Opzioni disponibili:</h3>
		<p><label><input type="radio" name="CC_SHOW" value="0" checked> <b>Mostra i commenti</b>: <i>vale per tutte le pagine. I commenti verranno caricati e mostrati non appena il plugin sarà visibile.</i></label></p>
		<p><label><input type="radio" name="CC_SHOW" value="1"> <b>Nascondi i commenti</b>: <i>vale per tutte le pagine. I commenti verranno comunque caricati ma non occuperanno spazio.</i></label></p>
		<p><label><input type="radio" name="CC_SHOW" value="2"> <b>Non caricare mai i commenti</b>: <i>risparmia dati internet e carica i commenti solo manualmente</i></label></p>
		</ul>
		<p>Attivando queste opzioni verrà salvato un cookie tecnico contenente le tue preferenze.</p>
		<p class="cmt-ac"><span class="cmt-btn cmt-btn-info">Fatto</span></p>
<?php

$form_content = ob_get_contents();

ob_end_clean();


//generate popup
$Pop->generic(
	'colibri-comment-info-popup', ['cmt-form'], "Colbrì Comment &copy; INFO",
	'', [],
	$form_content
);


?>
<!-- END COMMENT PLUGIN POPUP -->
