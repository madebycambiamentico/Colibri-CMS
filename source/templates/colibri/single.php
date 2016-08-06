<?php

/*
 * @template Colibrì 2016 v.1.0 -- single page + generic
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

//control variables
if (!isset($Config)){
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	die;
}

$PlugManager = new \WebSpace\PluginsManager;

$PlugManager->run_plugins('ethereal','top');
$PlugManager->run_plugins('ethereal','auto');

//load template classes (custom)
require_once __DIR__ . '/php/link.class.php';

?><!DOCTYPE html>

<html lang="<?php echo CMS_LANGUAGE ?>">
<head>
	<title><?php echo htmlentities($web['titolo'].' - '.$page['titolo']); ?></title>
	<?php
		$PlugManager->run_plugins('head','top');
		
		include __DIR__ . '/_meta.php';
		
		$PlugManager->run_plugins('head','auto');
		
		//main stylesheet
		Links::stylesheet('style.css');
		$PlugManager->run_plugins('style','top');
	?>
	
	<!-- custom stylesheet browser-sensitive or article-sensitive :) -->
	<!--[if lte IE 9]><style type="text/css">#contactform label{display:block}</style><![endif]-->
	<?php
		if ($page['src']):
			$cssurl = addslashes(str_replace(['(',')'],['\\(','\\)'], $page['src']));
			//you should add @media for multiple sizes (mobile-friendly)
	?>
	<style type="text/css">
		/* customized main image from database */
		.image-main{
			background-image:url('<?php echo Links::thumb('L1024/'.$cssurl) ?>');
		}
		@media only screen and (max-width:768px){
			.image-main{
				background-image:url('<?php echo Links::thumb('L768/'.$cssurl) ?>');
			}
		}
		@media only screen and (max-width:520px){
			.image-main{
				background-image:url('<?php echo Links::thumb('L520/'.$cssurl) ?>');
			}
		}
	</style>
	<?php
		endif;
	?>
	
	<!-- plugins -->
	<?php
	
		//styles
		Links::stylesheet('plugins/autoadapt-mosaic-grid/autoadapt-2.min.css');
		Links::stylesheet('plugins/simplelightbox/simplelightbox.min.css');
		$PlugManager->run_plugins('style','auto');
		$PlugManager->run_plugins('style','bottom');
		
		//javascript
		$PlugManager->run_plugins('js','top');
		
		//others
		$PlugManager->run_plugins('head','bottom');
	?>
</head>






<body>
<?php $PlugManager->run_plugins('body','top'); ?>

<div id="mapmark"></div>



<?php include __DIR__ . '/_menu.php'; ?>



<div id="wrapper">

	<div id="articles">
	
		<!-- main image -->
		<?php
			//search for videos
			$video = null;
			if ($pageid){
				$pdostat = $pdo->query("SELECT * FROM youtube WHERE idarticolo={$pageid}",PDO::FETCH_ASSOC);
				$video = $pdostat->fetch();
			}
			//print header...
			if ($video){
				include __DIR__ . '/_YTiframe.php';
			}
			elseif ($page['src']){
				echo '<div class="image-main"><div class="image-sizer web"></div></div>';
			}
			else{
				echo '<div id="image-spacer"></div>';
			}
		?>
		
		
		<!-- main article -->
		<div id="main-article" class="inside imgfix"><?php
			echo '<h1>'.htmlentities($page['titolo']).'</h1>';
			echo $page['corpo'];
		?></div>
		
		
		<!-- gallery -->
		<?php
			//GALLERY
			if ($page['idalbum']){
				$pdostat = \WebSpace\Query::query('getAlbum', [$page['idalbum'],true], 'ricerca', 'album');
				$hasrows = false;
				while ($image = $pdostat->fetch(PDO::FETCH_ASSOC)){
					if (!$hasrows){
						echo '<h3 class="inside">Galleria</h3>'.
							'<a name="gallery"></a>'.//anchor for #gallery
							'<div id="main-album" class="adaptiveGallery inside">';
						$hasrows = true;
					}
					$img = htmlentities($image['src'],ENT_QUOTES);
					echo '<div class="box">'.
						'<a href="'.Links::uploaded($img).'" target="_blank"><div class="overlay">'.htmlentities($image['descr']).'</div></a>'.
						'<img src="'.Links::thumb('300/'.$img).'">'.
					'</div>';
				}
				$pdostat->closeCursor();
				if ($hasrows) echo '</div>';
			}
			
		?>
		
		
		<!-- sub-articles -->
		<div id="sub-articles" class="article-cont">
			<?php
				//search all sub-pages with class "main page" (1), no limit (0), no full image (false)
				$pdostat = \WebSpace\Query::query('subArt', [$pageid, 1, 0]);
				while ($sp = $pdostat->fetch()){
					$link = Links::file(htmlentities($sp['remaplink'],ENT_QUOTES));
					echo '<div class="article"><div class="sub-art-cont">'.
						'<div class="image"><a href="'.$link.'"'.($sp['src'] ? ' style="background-image:url(\''.Links::thumb('320x320/'.htmlentities($sp['src'],ENT_QUOTES)).'\')"' : '').'></a></div>'.
						'<div class="desc">'.
							'<h2>'.htmlentities($sp['titolo']).'</h2>'.
							'<p>'.$sp['inbreve'].'</p>'.
							'<div class="sub-art-goto"><a href="'.$link.'">'.htmlentities($sp['titolo']).'</a></div>'.
						'</div>'.
					'</div></div>';
				}
				$pdostat->closeCursor();
			?>
		</div>
	
	</div>

</div>



<?php
	$PlugManager->run_plugins('body','auto');
	
	include __DIR__ . '/_quotation.php';
	include __DIR__ . '/_news.php';
	include __DIR__ . '/_links.php';
	include __DIR__ . '/_powered.php';
	
	$PlugManager->run_plugins('body','bottom');
	
	include __DIR__ . '/_contact.php';
?>





<?php Links::getJQuery() ?>

<!-- plugins -->
<?php
	Links::script('plugins/autoadapt-mosaic-grid/autoadapt-2.3.min.js');
	Links::script('plugins/simplelightbox/simplelightbox.min.js');
	Links::script('js/main.min.js');
	
	if (isset($YTIframeJsParams)){
		Links::script('_YTiframe.js.php?'.$YTIframeJsParams);
	}
?>

<script>
/**
* initialize gallery and lightbox.
* needs "autoadapt mosaic grid 2.3" and "simple lightbox"
*/
$(function(){
	//gallery
	$('#main-album').adaptiveGallery({
		pad: 3,
		maxD: 300,
		minN: 1,
		popSpeed: 0
	});
	$('#main-album a').simpleLightbox();
});
</script>

<?php
	$PlugManager->run_plugins('js','auto');
	$PlugManager->run_plugins('js','bottom');
?>

</body>
</html>

<?php $PlugManager->run_plugins('ethereal','bottom'); ?>