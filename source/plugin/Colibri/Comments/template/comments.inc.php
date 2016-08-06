<?php

/**
* add comments plugin to website template
*/

global $web, $page;



?>
<!-- START COMMENT PLUGIN -->
<div id="colibri-comments" class="plugin">
<?php


//-----------------------------
//   CONTROL COMMENT ALLOWED
//-----------------------------

if (!$web['comment_allow'] || !$web['comment_allow']){
	goto anchor_comments;
}



//-----------------------------
//    CONTROL ALLOWED USERS
//-----------------------------

if ($web['comment_class'] != -1){
	if (!isset($_SESSION['uclass']))
		goto anchor_comments;
	if ($_SESSION['uclass'] < $web['comment_class'])
		goto anchor_comments;
}

/*

//-----------------------------
//        COMMENT FORM
//-----------------------------

if (isset($_SESSION['uclass'])){
	//***********************
	//form for a logged user
?>
<!-- comment form (logged user) -->
	<form>
		<input type="hidden" name="pageid" value="<?php echo $page['id']; ?>">
		<textarea name="comment"></textarea>
	</form>
<?php
}
else{
	//*************************
	//form for non-logged user
?>
<!-- comment form (logged user) -->
	<form>
		<input type="hidden" name="pageid" value="<?php echo $page['id']; ?>">
		<input type="text" name="name" placeholder="Il tuo nome">
		<input type="email" name="email" placeholder="e-mail">
		<textarea name="comment"></textarea>
	</form>
<?php
}

*/

//-----------------------------
//         COMMENTS!
//-----------------------------

anchor_comments:

?>
<a name="colibri-comment"></a>
<div class="cmt-loader"></div>
<div id="cmt-all" class="empty">
	<p class="cmt-1st-line"></p>
	<p class="cmt-1st-circle cmt-btn-info"></p>
	<p class="cmt-1st-circle-btn cmt-plus">+</p>
	<!-- comments will be displayed HERE ;) -->
</div>

</div>
<!-- END COMMENT PLUGIN -->
