<?php

/*
 * @template Colibrì 2016 v.1.0
 * meta tags -- this is not a required file for standard templates.
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

if (!isset($page,$web)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

//common meta
echo '<meta charset="UTF-8">
<meta name="author" content="'.htmlentities( $web['autore'], ENT_QUOTES).'">
<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? 'maximum-scale=1,user-scalable=no' : '').'">
<meta name="description" content="'.htmlentities( $page['inbreve'], ENT_QUOTES).'">
<meta property="og:site_name" content="'.htmlentities( $web['titolo'], ENT_QUOTES).'">
<meta property="og:title" content="'.htmlentities( $web['titolo'].(ISINDEX ? '' : ' - '.$page['titolo']), ENT_QUOTES).'">
<meta property="og:type" content="article">
<meta property="og:url" content="'.htmlentities( $CONFIG['domain'].$CONFIG['mbc_cms_dir'].$page['remaplink'], ENT_QUOTES).'">
<meta property="og:description" content="'.htmlentities( $page['inbreve'], ENT_QUOTES).'">';

//main image
if ($page['src']) echo "\n".
'<meta property="og:image" content="'.htmlentities( $CONFIG['domain'].$CONFIG['mbc_cms_dir'].'uploads/'.$page['src'], ENT_QUOTES).'">
<meta property="og:image:width" content="'.$page['width'].'">
<meta property="og:image:height" content="'.$page['height'].'">';

//echo '<pre>'.print_r($page,true).'</pre>';
//echo '<pre>'.print_r($_SERVER['HTTP_USER_AGENT'],true).'</pre>';
?>
