<?php

/*
* add LINKS section on the standard template Colibrì 2016.
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

<!-- news -->
<?php
	//search all links (idtype=3), no limit, no full image (false)
	$pdostat = \WebSpace\Query::query(
		'byType',
		[ 'type' => 3, 'lang' => CMS_LANGUAGE ]
	);
	
	$doonce = true;
	
	while ($sp = $pdostat->fetch()){
		if ($doonce){
			//---------------------------------------------
			echo '<div id="links"><div class="article-cont">';
			$doonce = false;
		}
		
		$img = htmlentities($sp['src'],ENT_QUOTES);
		$link = htmlentities($sp['inbreve'],ENT_QUOTES);
		echo '<div class="article"><div class="sub-art-cont">'.
			'<div class="image"><a href="'.$link.'" target="_blank"'.($img ? ' style="background-image:url(\''.Links::thumb('320x200/'.$img).'\')"' : '').'></a></div>'.
			'<div class="desc imgfix">'.
				'<h3>'.htmlentities($sp['titolo']).'</h3>'.
				'<p>'.$sp['corpo'].'</p>'.
				'<a href="'.$link.'" target="_blank" class="jumplink" title="jump to... '.htmlentities($sp['titolo'],ENT_QUOTES).'"></a>'.
			'</div>'.
		'</div></div>';
	}
	
	$pdostat->closeCursor();
	if (!$doonce){
		echo '</div></div>';
		//---------------------------------------------
	}
?>
	