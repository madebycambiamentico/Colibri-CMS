<?php

/*
* add MENU section on the standard template Colibrì 2016.
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
<div id="menu">
	<div id="logo">
		<a class="logo" href="<?php echo Links::file(''); ?>"></a>
	</div>
	<ul id="menus"><?php
	
		$pdostat = \WebSpace\Query::query(
			'menu',
			[ 'level' => 2 ]
		);
		
		$articles = $pdostat->fetchAll();
		
		for ($i=0; $i<count($articles); $i++){
			$sp = $articles[$i];
			$link = Links::file( htmlentities($sp['remaplink'],ENT_QUOTES) );
			$hassubmenu = isset($articles[$i+1]) && strlen($articles[$i+1]['breadcrumbs']) > strlen($articles[$i]['breadcrumbs']);
			
			if ($hassubmenu){
				echo
				'<li>'.
					'<a href="'.$link.'">'.htmlentities($sp['titolo']).'</a>'.
					'<ul class="sub">';
					//add all sub-articles
					$j = $i;
					while (++$i && isset($articles[$i])){
						if (strlen($articles[$i]['breadcrumbs']) > strlen($sp['breadcrumbs'])){
							$link = Links::file( htmlentities($sp['remaplink'],ENT_QUOTES) );
							echo '<li><a href="'.$link.'">'.htmlentities($articles[$i]['titolo']).'</a></li>';
						}
						else{
							--$i;
							break;
						}
					}
				echo
					'</ul>'.
				'</li>';
			}
			else{
				echo '<li class="single"><a href="'.$link.'">'.htmlentities($sp['titolo']).'</a></li>';
			}
		}
		
	?></ul>
</div>