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
	<title><?php echo htmlentities($web['titolo']); ?></title>
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



<?php include __DIR__ . '/_menu.php' ?>



<div id="wrapper">

	<div id="articles">
	
		<!-- main image -->
		<?php
			//search for videos
			$video = null;
			if ($page['id']){
				$pdostat = $pdo->query("SELECT * FROM youtube WHERE idarticolo={$page['id']}",PDO::FETCH_ASSOC);
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
		
		
		<!-- sub-articles -->
		<div id="sub-articles" class="article-cont">
			<?php
				//search all main-pages
				$pdostat = \WebSpace\Query::query(
					'arts',
					[ 'skipids' => $page['id'], 'type' => 1, 'depth' => 2, 'lang' => CMS_LANGUAGE ]
				);
				$articles = $pdostat->fetchAll();
				for ($i=0; $i<count($articles); $i++){
					$sp = $articles[$i];
					$link = Links::file( htmlentities($sp['remaplink'],ENT_QUOTES) );
					$img = htmlentities($sp['src'],ENT_QUOTES);
					echo '<div class="article"><div class="sub-art-cont">'.
						'<div class="image"><a href="'.$link.'"'.
							($sp['src'] ? ' style="background-image:url(\''. Links::thumb( '320x320/'.$img ) .'\')"' : '').
						'></a></div>'.
						'<div class="desc">'.
							'<h2>'.htmlentities($sp['titolo']).'</h2>'.
							'<p>'.$sp['inbreve'].'</p>'.
							'<div class="sub-art-goto"><a href="'.$link.'">'.htmlentities($sp['titolo']).'</a></div>';
							
							//add all sub-articles
							$j = $i;
							while (++$i && isset($articles[$i])){
								if (strlen($articles[$i]['breadcrumbs']) > strlen($articles[$j]['breadcrumbs']))
									echo '<div class="subs-art-goto">'.
										'<a href="'.htmlentities($articles[$i]['remaplink'],ENT_QUOTES).'">'.
											htmlentities($articles[$i]['titolo']).
										'</a></div>';
								else{
									--$i;
									break;
								}
							}
					echo
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
	Links::script('js/main.min.js');
	
	if (isset($YTIframeJsParams)){
		Links::script('_YTiframe.js.php?'.$YTIframeJsParams);
	}
	
	$PlugManager->run_plugins('js','auto');
	$PlugManager->run_plugins('js','bottom');
?>

</body>
</html>

<?php $PlugManager->run_plugins('ethereal','bottom'); ?>