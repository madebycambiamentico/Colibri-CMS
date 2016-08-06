<?php

/**
* add inputs in editor.php.
*
* variable that should be present:
* $web = website properties
*/

//we MUST declare what variables we need, since this included page is called within a function.
global $web;

?><div class="inputs maxi aligned">
	<h4><i>(Colibri)</i> Selezione commentatori</h4>
	<p>Chi può commentare gli articoli abilitati?</p>
	<p><select name="CC[comment_class]">
		<option value="-1"<?php echo $web['comment_class']==-1 ? ' selected' : '' ?>>Anche i non registrati</option>
		<option value="0"<?php  echo $web['comment_class']== 0 ? ' selected' : '' ?>>Da ospite in su</option>
		<option value="1"<?php  echo $web['comment_class']== 1 ? ' selected' : '' ?>>Da admin in su</option>
		<option value="2"<?php  echo $web['comment_class']== 2 ? ' selected' : '' ?>>Solo webmaster</option>
	</select></p>
	
	<h4><i>(Colibri)</i> Gestione commenti</h4>
	<p><label><input type="checkbox" name="CC[comment_disallow]" <?php echo $web['comment_allow'] ? '' : 'checked' ?>> <b>Disabilita commenti:</b></label> blocca l'aggiunta di ulteriori commenti in ogni parte del sito. Quelli presenti rimarrano visibili. Per disabilitare completamente il plugin usare gestione plugins.</p>
</div>