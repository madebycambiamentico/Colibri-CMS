<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>icon set</title>
	<meta name="description" content="">
	<meta name="author" content="Costacurta Nereo">

	<style type="text/css">
		.filtering .sicon>i{
			opacity:0.3;
			transition:opacity 0.2s;
		}
		.filtering .sicon>i{
			background-color:transparent;
		}
		.filtering .sicon>i.filtered{
			opacity:1;
			background-color:#FFD55C;
		}
		#search{
			border:none;
			box-shadow:0 0 2px inset;
			width:100px;
			padding:0 5px;
			height:22px;
			line-height:22px;
			font-size:13px;
			vertical-align:bottom;
			transition:width 0.3s;
		}
		#search:focus{
			width:150px;
		}
		/* all next .sicon are like this: */
		#search ~ .sicon{
			vertical-align:bottom;
			background-color:#fff;
			padding:0;
			margin-left:3px;
		}
		#search ~ .sicon>i{
			background-color:#fff;
		}
		
		.icon-album{
			line-height:0;
		}
		pre{
			-moz-tab-size: 4;
			-o-tab-size:   4;
			tab-size:      4;
		}
		
		
		.sicon{
			display:inline-block;
			width:22px;
			height:22px;
			padding:8px;
			cursor:pointer;
			/* testing */
			*background-color:#ccc;
		}
		.sicon:hover{
			background-color:#eee;
		}
		.sicon>i{
			display:block;
			width:22px;
			height:22px;
			background:url(icon-set.png);/*580 x 404*/
			/* testing */
			*background-color:#ccc;
		}

<?php
$styles = [
	["speed",572,396],
	["world",530,396],
	["nsew",489,396],
	["sun",447,396],
	["fuel",406,396],
	["geoloc",364,396],
	["off",323,396],
	["stop",281,396],
	["search",240,396],
	["marker",198,396],
	["battery-0",157,396],
	["battery-50",115,396],
	["battery-100",74,396],
	["battery-zap",32,396],
	["alarm",572,362],
	["shuttle",530,362],
	["bicycle",489,362],
	["vespa",447,362],
	["airplane",406,362],
	["ship",364,362],
	["train",323,362],
	["metro",281,362],
	["bus",240,362],
	["bus-2",198,362],
	["car",157,362],
	["car-2",115,362],
	["watch",74,362],
	["trash",32,362],
	["options",572,329],
	["options-2",530,329],
	["options-3",489,329],
	["play",447,329],
	["gallery",406,329],
	["upload",364,329],
	["download",323,329],
	["cloud",281,329],
	["unlock",240,329],
	["unlock-2",198,329],
	["lock",157,329],
	["erase",115,329],
	["medkit",74,329],
	["weightlift",32,329],
	["like",572,296],
	["dislike",530,296],
	["share",489,296],
	["fb",447,296],
	["tweet",406,296],
	["basketball",364,296],
	["istagram",323,296],
	["istagram-2",281,296],
	["photo",240,296],
	["video",198,296],
	["video-camera",157,296],
	["tv",115,296],
	["tab",74,296],
	["eye",32,296],
	["home",572,262],
	["weight",530,262],
	["shopper",489,262],
	["clothesbasket",447,262],
	["label-less",406,262],
	["label-more",364,262],
	["label",323,262],
	["label-2",281,262],
	["label-3",240,262],
	["flickr",198,262],
	["flickr-2",157,262],
	["info",115,262],
	["flag",74,262],
	["diamond",32,262],
	["cuffie",572,229],
	["audio-0",530,229],
	["audio-1",489,229],
	["audio-2",447,229],
	["audio-3",406,229],
	["speaker",364,229],
	["speaker-2",323,229],
	["sim",281,229],
	["hair",240,229],
	["chat",198,229],
	["chat-2",157,229],
	["chat-3",115,229],
	["chat-4",74,229],
	["hearth",32,229],
	["glass",572,195],
	["glass-2",530,195],
	["glass-3",489,195],
	["glass-4",447,195],
	["glass-5",406,195],
	["beer",364,195],
	["beer-2",323,195],
	["coffee",281,195],
	["american-coffee",240,195],
	["american-coffee-2",198,195],
	["icecream",157,195],
	["icecream-2",115,195],
	["icecream-3",74,195],
	["chicken",32,195],
	["index",572,162],
	["v",530,162],
	["x",489,162],
	["add",447,162],
	["less",406,162],
	["star",364,162],
	["reload",323,162],
	["arrow-d",281,162],
	["arrow-u",240,162],
	["arrow-r",198,162],
	["arrow-l",157,162],
	["check-ok",115,162],
	["check-ok-2",74,162],
	["problem",32,162],
	["key",572,129],
	["list-1",530,129],
	["list-2",489,129],
	["list-3",447,129],
	["list-4",406,129],
	["list-5",364,129],
	["expand",323,129],
	["selection",281,129],
	["calendar",240,129],
	["calendar-2",198,129],
	["people",157,129],
	["person",115,129],
	["man",73,129],
	["girl",31,129],
	["erase-2",572,95],
	["pen",530,95],
	["edit",489,95],
	["pencil",447,95],
	["necktie",406,95],
	["necktie-2",364,95],
	["bag",323,95],
	["floppy",281,95],
	["crop",240,95],
	["mail",198,95],
	["pec",157,95],
	["schedule",115,95],
	["attachment",74,95],
	["attachment-2",32,95],
	["notes",572,62],
	["money",530,62],
	["docs",489,62],
	["id",447,62],
	["docs-2",406,62],
	["calc",364,62],
	["checkboard",322,62],
	["checkboard-ok",281,62],
	["file",240,62],
	["file-add",198,62],
	["file-remove",157,62],
	["folder",115,62],
	["folder-add",74,62],
	["folder-remove",32,62],
	["statistic",572,28],
	["statistic-2",530,28],
	["increase",489,28],
	["decrease",447,28],
	["mouse",406,28],
	["pc",364,28],
	["tablets",323,28],
	["tablets-2",281,28],
	["tablet-portrait",240,28],
	["tablet-landscape",198,28],
	["cellphones",157,28],
	["cellphones-2",115,28],
	["cellphone-portrait",74,28],
	["cellphone-landscape",32,28]
];
?>

<?php

function print_all_styles($space=""){
	global $styles;
	foreach ($styles as $s){
		echo '.sicon>.'.$s[0].'{background-position:'.$s[1].'px '.$s[2].'px}'.$space;
	}
}

function print_some_styles($ids=[],$space=""){
	global $styles;
	for ($i=0;$i<count($styles);$i++){
		//if ($i % 14 == 0) echo "\n";
		if (in_array($i,$ids)) echo '.sicon>.'.$styles[$i][0].'{background-position:'.$styles[$i][1].'px '.$styles[$i][2].'px}'.$space;
	}
}

function print_all_tiles($space="",$newrow=""){
	global $styles;
	foreach ($styles as $i => $s){
		echo '<a class="sicon"><i class="'.$s[0].'" title="('.$i.') '.$s[0].'"></i></a>'.$space;
		if ($i && ($i+1) % 14 == 0) echo $newrow;
	}
}

function print_some_tiles($ids,$space=""){
	global $styles;
	for ($i=0;$i<count($styles);$i++){
		if (in_array($i,$ids)) echo '<a class="sicon"><i class="'.$styles[$i][0].'" title="'.$styles[$i][0].'"></i></a>'.$space;
	}
}

function print_some_html($ids,$space=""){
	global $styles;
	for ($i=0;$i<count($styles);$i++){
		if (in_array($i,$ids)) echo '&lt;a class="sicon">&lt;i class="'.$styles[$i][0].'">&lt;/i>&lt;/a>'.$space;
	}
}

print_all_styles();


	/*$x = 14;
	$y = 12;
	$wx = 580;
	$wy = 404;
	$dx = 41.5;
	$dy = 33.5;
	for ($i=0;$i<$y;$i++){
		for ($j=0;$j<$x;$j++){
			echo '.sicon.'.($i.'_'.$j).'{background-position:'.intval($wx-$j*$dx-8,10).'px '.intval($wy-$i*$dy-8,10).'px;}';
		}
	}*/
?>
	</style>
</head>

<body>

<?php
/*
for ($i=0;$i<$y;$i++){
	for ($j=0;$j<$x;$j++){
		echo '<i title="'.($i.'_'.$j).'" class="sicon '.($i.'_'.$j).'"></i> ';
	}
	echo '<br>';
}
*/
?>

<h2>Icons</h2>

<h3>All tiles:</h3>
<div>
<input type="text" id="search" placeholder="search..."><a class="sicon"><i class="search"></i></a>
</div>

<br>

<div id="all-tiles" class="icon-album">
<?php print_all_tiles("","<br>") ?>
</div>

<br><br>

<h3>Original PNG:</h3>
<img src="icon-set.png">

<br><br>

<h3>Custom icon set:</h3>
<?php
	$filter = [8,27,28,30,33,34,50,52,55,57,73,99,100,101,102,105,106,107,108,111,113,114,115,116,117,118,129,146,147,151,152,153];
	if (isset($_GET['filter']))
		if (is_array($_GET['filter'])) $filter = $_GET['filter'];
?>
<div id="some-tiles" class="icon-album">
<?php print_some_tiles($filter,"","<br>") ?>
</div>

<h4>Custom style:</h4>
<pre>
.sicon{
	display:inline-block;
	*display:inline;
	zoom:1;
	width:22px;
	height:22px;
	/*(optional)*/
	padding:8px;
	cursor:pointer;
}
.sicon:hover{
	/*(optional)*/
	background-color:#eee;
}
.sicon>i{display:block;width:22px;height:22px;background:url(<?php echo isset($_GET['dir']) ? htmlentities($_GET['dir']) : ""; ?>icon-set.png);}
<?php
	print_some_styles($filter,"\n");
?></pre>

<h4>Ready html:</h4>
<pre><?php print_some_html($filter,"\n") ?></pre>

<!--[if lte IE 8]><script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script><![endif]--><!--[if gt IE 8]><!--><script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script><!--<![endif]-->
<script>
$(function(){
	$('a.sicon').click(function(e){e.preventDefault()})
	var searching = false;
	$('#search').keyup(function(){
		var filter = this.value.trim();
		if (filter !== '') filter = filter.split(' ');
		else{
			$('#all-tiles').removeClass('filtering');
			searching = false;
			return false;
		}
		if (!searching){
			searching = true;
			$('#all-tiles').addClass('filtering');
		}
		var selector = filter.join('], #all-tiles [title*=');
		/* attributes selectors:
			~= contain whitespaced separated word...
			|= start with..., optionally followed by "-"
			^= value with prefix...
			$= value with suffix...
			*= contain at least 1 substring...
		*/
		$('#all-tiles .sicon>i.filtered').removeClass('filtered');
		$('#all-tiles .sicon>i[title*='+selector+']').addClass('filtered');
	})
});
</script>

</body>

</html>