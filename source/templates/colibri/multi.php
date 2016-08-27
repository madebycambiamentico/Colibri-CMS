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
	<title><?php echo htmlentities($web['titolo']); ?> - Ricerca per Data</title>
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



<?php include __DIR__ . '/_menu.php'; ?>



<div id="wrapper">

	<div id="articles">
	
		<!-- main image -->
		<?php
			//print header...
			if ($page['src']){
				echo '<div class="image-main"><div class="image-sizer web"></div></div>';
			}
			else{
				echo '<div id="image-spacer"></div>';
			}
		?>
		
		
		<!-- main article -->
		<div id="main-article" class="inside imgfix">
			<h1>Ricerca per data <i><?php echo htmlentities($_GET['date']) ?></i></h1>
			<p>I risultati della tua ricerca sono esposti qui in basso.</p>
			<p>Ricordati che la ricerca per data viene fatte secondo anno (4 cifre), mese (2 cifre) o giorno (2 cifre) nella forma rispettivamente <i>YYYY</i> o <i>YYYY-MM</i> o <i>YYYY-MM-DD</i></p>
		</div>
		
		
		<!-- result of article search -->
		<div id="sub-articles" class="article-cont">
			<?php
				$i = 0;
				//$pdostatPAGES first item is already fetched (into $page_n)
				if ($page_n){
				do{
					$link = Links::file( htmlentities($page_n['remaplink'],ENT_QUOTES) );
					$img = htmlentities($page_n['src'],ENT_QUOTES);
					echo '<div class="article"><div class="sub-art-cont">'.
						'<div class="image"><a href="'.$link.'"'.
							($page_n['src'] ? ' style="background-image:url(\''. Links::thumb( '320x200/'.$img ) .'\')"' : '').
						'></a></div>'.
						'<div class="desc">'.
							'<p style="font-size:smaller">Created on '.$page_n['datacreaz'].'<br>Edited on '.$page_n['dataedit'].'</p>'.
							'<h2>'.htmlentities($page_n['titolo']).'</h2>'.
							'<p>'.$page_n['inbreve'].'</p>'.
							'<div class="sub-art-goto"><a href="'.$link.'">'.htmlentities($page_n['titolo']).'</a></div>'.
						'</div>'.
					'</div></div>';
				}
				while( $page_n = $pdostatPAGES->fetch(PDO::FETCH_ASSOC) );
				
				$pdostat->closeCursor();
				}
				else{
					echo '<b>Nessun risultato</b> trovato corrispondente alla tua ricerca!';
				}
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
	$PlugManager->run_plugins('js','auto');
	$PlugManager->run_plugins('js','bottom');
?>

</body>
</html>

<?php $PlugManager->run_plugins('ethereal','bottom'); ?>