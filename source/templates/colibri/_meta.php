<?php

/*
* add META TAGS section in <head> on the standard template Colibrì 2016.
*
* @author Nereo Costacurta
* @license GPLv3
* @copyright: (C)2016 nereo costacurta
*/

if (!isset($page,$web)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

//common meta
echo '<meta charset="UTF-8">
<meta name="author" content="'.htmlentities( $web['autore'], ENT_QUOTES).'">
<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">
<meta name="description" content="'.htmlentities( $page['inbreve'], ENT_QUOTES).'">
<meta property="og:site_name" content="'.htmlentities( $web['titolo'], ENT_QUOTES).'">
<meta property="og:title" content="'.htmlentities( $web['titolo'].($page['isindex'] ? '' : ' - '.$page['titolo']), ENT_QUOTES).'">
<meta property="og:type" content="article">
<meta property="og:url" content="'.htmlentities( $Config->domain . $Config->script_path . $page['remaplink'], ENT_QUOTES).'">
<meta property="og:description" content="'.htmlentities( $page['inbreve'], ENT_QUOTES).'">';

//main image
if ($page['src']) echo "\n".
	'<meta property="og:image" content="'.htmlentities( $Config->domain . $Config->script_path . 'uploads/'.$page['src'], ENT_QUOTES).'">
	<meta property="og:image:width" content="'.$page['width'].'">
	<meta property="og:image:height" content="'.$page['height'].'">';

/* favicon?
<!-- For IE 9 and below. ICO should be 32x32 pixels in size -->
<!--[if IE]><link rel="shortcut icon" href="<?php echo $Config->script_path ?>favicon.ico"><![endif]-->

<!-- Touch Icons - iOS and Android 2.1+ 180x180 pixels in size. --> 
<link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

<!-- Firefox, Chrome, Safari, IE 11+ and Opera. 196x196 pixels in size. -->
<link rel="icon" href="path/to/favicon.png">
*/
?>