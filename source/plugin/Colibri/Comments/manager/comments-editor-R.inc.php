<?php

/**
* add inputs in editor.php.
*
* variable that should be present:
* $id = (null|int) if it is a new article, (int) if editing an existing article.
* $ARTICLE = (null|array) contains article properties (all + images)
*/

//we MUST declare what variables we need, since this included page is called within a function.
global $id, $ARTICLE;

?><div class="inputs maxi aligned">
	<h4>Abilita commenti</h4>
	<label><input type="checkbox" name="CC[comment_allow]" <?php echo ($id ? ($ARTICLE['comment_allow'] ? 'checked' : '') : '') ?>> Permetti commenti</label>
</div>