<?php

/*
* add MOTTO section on the standard template Colibrì 2016.
*
* @author Nereo Costacurta
* @license GPLv3
* @copyright: (C)2016 nereo costacurta
*/

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