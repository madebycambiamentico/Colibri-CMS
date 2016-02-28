<?php

/*
 * @template Colibrì 2016 v.1.0 -- youtube loader for main page
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

//control variables
if (!isset($page,$video)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

//in future: get values from sqlite
$YTIframe = [
	'width' => $video['videow'],
	'height' => $video['videoh'],
	'id' => $video['videoid'],
	'params' => [
		'start' => $video['videostart'],
		'end' => $video['videoend'],
		'autoplay' => 1
	]
];

$YTIframeJsParams = http_build_query($YTIframe,'&');

?>

<div class="image-main video">
	<div class="image-sizer iframe"style="padding-bottom:<?php echo $YTIframe['height']/$YTIframe['width']*100 ?>%" id="video-container">
		<div id="YTPlayer"></div>
		<!--iframe src="https://www.youtube.com/embed/v10PDyCbwWY?autoplay=1&loop=1&start=32&end=104&modestbranding=1&disablekb=1&fs=0&rel=0&showinfo=0&iv_load_policy=3&theme=light"></iframe-->
	</div>
	<div id="image-YT-loader"><canvas id="load-canvas" width="120" height="120">loading video,<br>please wait</canvas><div class="logo"></div></div>
	<div id="YT-switcher" class="image-iframe-web"></div>
</div>