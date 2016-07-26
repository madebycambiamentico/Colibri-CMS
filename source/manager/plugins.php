<?php

//die('hello world');

$PlugManager = new \Colibri\PluginsManager(true, null, null);

//$PlugManager->parse_plugins_folder('editor');

echo '<pre>$available<br>'.print_r($PlugManager->available,true).'</pre>';

echo '<pre>$js<br>'.print_r($PlugManager->js,true).'</pre>';
echo '<pre>$style<br>'.print_r($PlugManager->style,true).'</pre>';
echo '<pre>$popup<br>'.print_r($PlugManager->popup,true).'</pre>';
echo '<pre>$center<br>'.print_r($PlugManager->center,true).'</pre>';
echo '<pre>$right<br>'.print_r($PlugManager->right,true).'</pre>';
echo '<pre>$db<br>'.print_r($PlugManager->db,true).'</pre>';

$PlugManager->run_plugins( 'center' );

?>