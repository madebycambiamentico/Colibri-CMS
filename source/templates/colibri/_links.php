<?php

/*
 * @template Colibrì 2016 v.1.0
 * links -- this is not a required file for standard templates.
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
	//search all news (idtype=3), no limit, no full image (false)
	$pdostat = \WebSpace\Query::query('byType', [3, 0]);
	$hasrows = false;
	while ($sp = $pdostat->fetch()){
		if (!$hasrows){
			//---------------------------------------------
			echo '<div id="links"><div class="article-cont">';
			$hasrows = true;
		}
		$img = htmlentities($sp['src'],ENT_QUOTES);
		$link = 'target="_blank" href="'.htmlentities($sp['inbreve'],ENT_QUOTES).'"';
		echo '<div class="article"><div class="sub-art-cont">'.
			'<div class="image"><a '.$link.($img ? ' style="background-image:url(\''.LINK::thumb('320x200/'.$img).'\')"' : '').'></a></div>'.
			'<div class="desc imgfix">'.
				'<h3>'.htmlentities($sp['titolo']).'</h3>'.
				'<p>'.$sp['corpo'].'</p>'.
				'<a class="jumplink" '.$link.' title="jump to... '.htmlentities($sp['titolo'],ENT_QUOTES).'"></a>'.
			'</div>'.
		'</div></div>';
	}
	$pdostat->closeCursor();
	if ($hasrows){
		echo '</div></div>';
		//---------------------------------------------
	}
?>
	