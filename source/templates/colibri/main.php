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
if (!isset($CONFIG, $page)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die('variabili mancanti');
}

//load template classes (custom)
require_once TEMPLATES::custom($web['template'],'php/link.class.php');

?><!DOCTYPE html>

<html lang="it-IT">
<head>
	<title><?php echo htmlentities($web['titolo']); ?></title>
	<?php include TEMPLATES::custom($web['template'],'_meta.php') ?>
	
	<?php
		//main stylesheet
		LINK::stylesheet('style.css?v=1.2');
	?>
	
	<!-- custom stylesheet browser-sensitive or article-sensitive :) -->
	<!--[if lte IE 9]><style type="text/css">#contactform label{display:block}</style><![endif]-->
	<style type="text/css">
	<?php
		if ($page['src']):
			$cssurl = addslashes(str_replace(['(',')'],['\\(','\\)'], $page['src']));
			//you should add @media for multiple sizes (mobile-friendly)
	?>
	/* customized main image from database */
	.image-main{
		background-image:url('<?php echo LINK::thumb('L1024/'.$cssurl) ?>');
	}
	@media only screen and (max-width:768px){
		.image-main{
			background-image:url('<?php echo LINK::thumb('L768/'.$cssurl) ?>');
		}
	}
	@media only screen and (max-width:520px){
		.image-main{
			background-image:url('<?php echo LINK::thumb('L520/'.$cssurl) ?>');
		}
	}
	<?php
		endif;
	?>
	</style>
</head>


<body>

<div id="mapmark"></div>



<?php include TEMPLATES::custom($web['template'],'_menu.php') ?>



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
				include TEMPLATES::custom($web['template'],'_YTiframe.php');
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
				//search all sub-main-pages
				$pdostat = ARTQUERY::query('subMainArts', [1, 0]);
				$subarticles = $pdostat->fetchAll();
				$pdostat->closeCursor();
				$sa_idx = 0;
				function addlinktomainpages($parentid,&$i,$sa){
					if (!isset($sa[$i])) return '';
					$res = '';
					if ($sa[$i]['parentid'] == $parentid){
						while(isset($sa[$i]) && $sa[$i]['parentid'] == $parentid){
							$res .= '<div class="subs-art-goto"><a href="'.htmlentities($sa[$i]['remaplink'],ENT_QUOTES).'">'.htmlentities($sa[$i]['titolo']).'</a></div>';
							$i++;
						}
					}
					return $res;
				}
				//search all main-pages (1), no limit (0), no full image (false)
				$pdostat = ARTQUERY::query('mainArts');
				while ($sp = $pdostat->fetch()){
					$link = htmlentities($sp['remaplink'],ENT_QUOTES);
					echo '<div class="article"><div class="sub-art-cont">'.
						'<div class="image"><a href="'.$link.'"'.($sp['src'] ? ' style="background-image:url(\'img/thumbs/320x320/'.htmlentities($sp['src'],ENT_QUOTES).'\')"' : '').'></a></div>'.
						'<div class="desc">'.
							'<h2>'.htmlentities($sp['titolo']).'</h2>'.
							'<p>'.$sp['inbreve'].'</p>'.
							'<div class="sub-art-goto"><a href="'.$link.'">'.htmlentities($sp['titolo']).'</a></div>'.
							addlinktomainpages($sp['id'],$sa_idx,$subarticles).
						'</div>'.
					'</div></div>';
				}
				$pdostat->closeCursor();
			?>
		</div>
	
	</div>

</div>


<?php include TEMPLATES::custom($web['template'],'_quotation.php') ?>

<?php include TEMPLATES::custom($web['template'],'_news.php') ?>

<?php include TEMPLATES::custom($web['template'],'_links.php') ?>

<?php include TEMPLATES::custom($web['template'],'_powered.php') ?>

<?php include TEMPLATES::custom($web['template'],'_contact.php') ?>






<?php LINK::getJQuery() ?>

<!-- plugins -->
<?php
	LINK::script('js/main.min.js?v=1.0');
	
	if (!$page['src'] && isset($YTIframeJsParams))
		LINK::script('_YTiframe.js.php?'.$YTIframeJsParams);
?>

</body>
</html>