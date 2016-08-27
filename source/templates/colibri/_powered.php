<?php

/*
* add POWERED BY... section on the standard template Colibrì 2016.
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

<!-- pwoered by Colibrì, a CMS mabebycambiamentico -->
<div id="powered">
	<div class="logo"></div>
	<div class="mbc">
		<b>Powered By <span>Colibrì</span></b><br>
		<i>Fast and Reliable CMS ever <a href="http://cambiamentico.altervista.org/">MadeByCambiamentico</a></i><br>
		<i>Colbrì Theme &copy;2016</i> by Nereo Costacurta<br>
	</div>
	<div class="actions">
		<a href="<?php echo Links::file('login') ?>">Login</a><!--
	--><a href="<?php echo Links::file('signin') ?>">Signin</a>
	</div>
</div>