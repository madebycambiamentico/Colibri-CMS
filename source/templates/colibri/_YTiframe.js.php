<?php

/*
 * @template Colibrì 2016 v.1.0 -- youtube loader for main page | javascript
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

//control variables
if (!isset($_GET)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}
if (!isset(
		$_GET['width'],
		$_GET['height'],
		$_GET['id'],
		$_GET['params'])
	){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

header('Content-type: text/javascript');

$params = [
	'start' => (isset($_GET['params']['start']) ? floatval($_GET['params']['start']) : 0),
	'end' => (isset($_GET['params']['end']) ? floatval($_GET['params']['end']) : 0),
	'autoplay' => (isset($_GET['params']['autoplay']) ? 1 : 0),
];
if (!$params['end']) unset($params['end']);

?>
//<script>

// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
 
// requestAnimationFrame polyfill by Erik Möller
// fixes from Paul Irish and Tino Zijdel
 
(function() {
	var lastTime = 0;
	var vendors = ['ms', 'moz', 'webkit', 'o'];
	for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
		window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
		window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
								   || window[vendors[x]+'CancelRequestAnimationFrame'];
	}
 
	if (!window.requestAnimationFrame)
		window.requestAnimationFrame = function(callback, element) {
			var currTime = new Date().getTime();
			var timeToCall = Math.max(0, 16 - (currTime - lastTime));
			var id = window.setTimeout(function() { callback(currTime + timeToCall); },
			  timeToCall);
			lastTime = currTime + timeToCall;
			return id;
		};
 
	if (!window.cancelAnimationFrame)
		window.cancelAnimationFrame = function(id) {
			clearTimeout(id);
		};
}());


var onYouTubeIframeAPIReady = (function(){
	//load youtube api asynchronously
	$('<script>')
		.attr('src',"https://www.youtube.com/iframe_api")
		.insertBefore($('script').eq(0))

	//create iframe player
	var player;
	var params = {
		start:<?php echo $params['start'] ?>,
		<?php if (isset($params['end'])) echo 'end:'.$params['end'].',' ?>
		autoplay:<?php echo $params['autoplay'] ? '$(window).width()>520' : 0 ?>,
		//clear video from branding, infos etc., do not allow end suggested videos
		//NB - i'm sorry for non-adblocker users... but i cannot remove ads!
		autohide:1,
		modestbranding:1,
		disablekb:1,
		fs:0,
		rel:0,
		showinfo:0,
		iv_load_policy:3,
		wmode:'opaque' //don't know if it will work...
	};
	function onYouTubeIframeAPIReady() {
		window.player = player = new YT.Player("YTPlayer", {
			width: "<?php echo intval($_GET['width'],10) ?>",
			height: "<?php echo intval($_GET['height'],10) ?>",
			videoId: "<?php echo htmlentities($_GET['id'],ENT_QUOTES) ?>",
			playerVars: params,
			events: {
				"onReady": onPlayerReady,
				"onStateChange": onPlayerStateChange
			}
		});
	}

	//start player
	function onPlayerReady(event) {
		event.target.mute();
		event.target.playVideo();
		if (!('end' in params)) params.end = event.target.getDuration();
		if (!params.autoplay){
			loading = false;//stop loader
			$('#image-YT-loader').addClass('hidden');//fadeout
			covered = false;
		}
	}

	var loading = true;
	var covered = true;
	var looping = false;
	var looper;
	function loop(player, from, to){
		if ($(window).width() > 520){
			var dt = Math.max(0, to-from-0.25)*1000;
			console.log('start timeout for '+dt+'ms')
			looper = setTimeout(function(){
				console.log('timeout ended...')
				if (player.getPlayerState() !== YT.PlayerState.PLAYING) return false;
				player.seekTo(params.start,true);
				player.playVideo();
				looping = false;
			},dt);
			looping = true;
		}
	}
	function onPlayerStateChange(event) {
		if (!looping && event.data == YT.PlayerState.PLAYING) {
			//restart from <start> after <end-start> seconds of playing
			if (covered){
				loading = false;//stop loader
				$('#image-YT-loader').addClass('hidden');//fadeout
				covered = false;
				//add play/pause loop.
				$('#YT-switcher').click(function(){
					if (player.getPlayerState() == YT.PlayerState.PLAYING){
						clearTimeout(looper);
						player.pauseVideo();
						looping = false;
					}
					else{
						console.log("restart loop");
						looping = true;//prevent normal loop
						player.playVideo();
						loop(player,player.getCurrentTime(),params.end);
					}
				});
			}
			loop(event.target,params.start,params.end);
		}
	}
	
	//custom loader ... funny moment with canvas
	var bg = $('#load-canvas')[0];
	if ('getContext' in bg){
		var ctx = bg.getContext('2d');
		ctx.strokeStyle = '#ffffff';
		ctx.lineCap = 'square';
		ctx.lineWidth=4.0;
		ctx.save();
		
		bg.onclick = function(){
			loading = !loading;
			if (loading) draw();
		};
		
		function rads(deg){
			return (deg-90)/180*Math.PI;
		}
		
		//animation
		var initime, speed1, speed2, DT, a1, a2, invert, meetup, firstmeetup, steps;
		function init(){
			speed1 = 2;//degrees/DT
			speed2 = 6;//degrees/DT > speed1
			a1=0,a2=0;//degrees. a2 >= a1
			invert = false;
			meetup = (360)/(speed2-speed1);
			firstmeetup = (360+a1-a2)/(speed2-speed1);
			steps = firstmeetup;
			initime = new Date();
			draw();
		}
		function draw(){
			if (steps<=0){
				invert=!invert;
				steps = meetup;
				console.log('meetup')
			}
			if (invert){
				a1 += speed2;
				a2 += speed1;
			}
			else{
				a1 += speed1;
				a2 += speed2;
			}
			ctx.clearRect(0, 0, 240, 240);
			ctx.beginPath();
			ctx.arc(60, 60, 50, rads(a1), rads(a2), false);
			ctx.stroke();
			if (loading) window.requestAnimationFrame(draw);
			steps--;
		}
		init();
	}
	
	return onYouTubeIframeAPIReady;
})();
//</script>