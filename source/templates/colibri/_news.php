<?php

/*
* add NEWS section on the standard template Colibrì 2016.
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
		[ 'type' => 2, 'lang' => CMS_LANGUAGE ]
	);
	
	$doonce = true;
	
	while ($sp = $pdostat->fetch()){
		if ($doonce){
			//---------------------------------------------
			echo '<div id="news"><div class="article-cont">';
			$doonce = false;
		}
		
		$img = htmlentities($sp['src'],ENT_QUOTES);
		$link = 'target="_blank" href="'.htmlentities($sp['inbreve'],ENT_QUOTES).'"';
		echo '<div class="article"><div class="sub-art-cont">'.
			'<div class="image"><a '.$link.($img ? ' style="background-image:url(\''. Links::thumb('320x200/'.$img) .'\')"' : '').'></a></div>'.
			'<div class="desc imgfix">'.
				'<h3>'.htmlentities($sp['titolo']).'</h3>'.
				'<p>'.$sp['corpo'].'</p>'.
			'</div>'.
		'</div></div>';
	}
	
	$pdostat->closeCursor();
	if (!$doonce){
		echo '</div></div>';
		//---------------------------------------------
	}
	
?>
	