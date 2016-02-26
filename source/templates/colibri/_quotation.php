<?php

/*
 * @template Colibrì 2016 v.1.0
 * quotation -- this is not a required file for standard templates.
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

if (!isset($web)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

?>
<div id="quotator">
	<div class="logo"></div>
	<h4><?php echo htmlentities($web['autore']) ?></h4>
	<?php
		echo	'<p class="motto"><q>'.htmlentities($web['motto']).'</q></p>'.
				'<p class="desc">'.$web['descr'].'</p>';
	?>
</div>