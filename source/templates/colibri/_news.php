<?php

/*
 * @template Colibrì 2016 v.1.0
 * breaking news -- this is not a required file for standard templates.
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

<!-- news -->
<?php
	//search all news (idtype=2), max 6, no full image (false)
	$pdostat = ARTQUERY::query('byType', [2, 6]);
	$hasrows = false;
	while ($sp = $pdostat->fetch()){
		if (!$hasrows){
			//---------------------------------------------
			echo '<div id="news"><div class="article-cont">';
			$hasrows = true;
		}
		$link = LINK::file(htmlentities($sp['remaplink'],ENT_QUOTES));
		$img = htmlentities($sp['src'],ENT_QUOTES);
		echo '<div class="article"><div class="sub-art-cont">'.
			'<div class="image"><a href="'.$link.'"'.($img ? ' style="background-image:url(\''.LINK::thumb('320x200/'.$img).'\')"' : '').'></a></div>'.
			'<div class="desc imgfix">'.
				'<h3>'.htmlentities($sp['titolo']).'</h3>'.
				'<p>'.$sp['corpo'].'</p>'.
			'</div>'.
		'</div></div>';
	}
	$pdostat->closeCursor();
	if ($hasrows){
		echo '</div></div>';
		//---------------------------------------------
	}
?>
	