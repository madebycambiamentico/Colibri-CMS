<?php

/*
 * @template Colibrì 2016 v.1.0
 * main menu -- this is not a required file for standard templates.
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
<div id="menu">
	<div id="logo">
		<a class="logo" href="<?php echo Links::file(''); ?>"></a>
	</div>
	<ul id="menus"><?php
		$pdostat = \WebSpace\Query::query('menu');
		$lastid = false;
		$hassubmenu = false;
		$lis = $pdostat->fetchAll();
		foreach ($lis as $i => $li){
			$href = Links::file( htmlentities($li['remaplink'],ENT_QUOTES) );
			if ($lastid != $li['parentid']){
				//main menu
				//close previous item
				if (false != $lastid) echo ($hassubmenu ? '</ul></li>' : '</li>');
				//update last id
				$lastid = $li['parentid'];
				//detect if this item has submenu
				if (isset($lis[$i+1])){
					if ($lis[$i+1]['parentid'] == $lastid)
						$hassubmenu = true;
					else
						$hassubmenu = false;
				}
				else
					$hassubmenu = false;
				//print item (and start submenu if needed)
				if (!$hassubmenu){
					echo '<li id="menu-'.$lastid.'" class="single">'.
						'<a href="'.$href.'">'.htmlentities($li['titolo']).'</a>';
				}
				else{
					echo '<li id="menu-'.$lastid.'">'.
						'<a href="'.$href.'">'.htmlentities($li['titolo']).'</a>'.
						'<i data-id="'.$lastid.'"></i><ul class="sub" id="submenu-'.$lastid.'">';
				}
			}
			else{
				//sub menu
				echo '<li><a href="'.$href.'">'.htmlentities($li['titolo']).'</a></li>';
			}
		}
		if (false != $lastid) echo ($hassubmenu ? '</ul></li>' : '</li>');
	?></ul>
</div>