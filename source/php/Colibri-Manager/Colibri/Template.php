<?php namespace Colibri;

/**
* Base CSS, JS and HTML parts used in manager template, such as the menu, the album showroom...
*
* @method (public) link
* @method (public) getBaseCss
* @method (public) getToolbar
* @method (public) getMenu
* @method (public) getJQuery
* @method getaAlbumEditor
*/


class Template {
	
	public $version = "0.4.0&beta;m";

	public static function link($rpath='', $echo=true){
		global $CONFIG;
		if ($echo) echo $CONFIG['mbc_cms_dir'].$rpath;
		else return $CONFIG['mbc_cms_dir'].$rpath;
	}

	//-------------------------------------------------
	public function getBaseCss(){
?>
	<link rel="stylesheet" href="<?php self::link("css/modalbox.css") ?>">
	<link rel="stylesheet" href="<?php self::link("php/mbc-filemanager/css/icons.css") ?>">
	<link rel="stylesheet" href="<?php self::link("php/mbc-filemanager/css/sicons.custom.css") ?>">
	<link rel="stylesheet" href="<?php self::link("php/mbc-filemanager/css/inputs.css") ?>">
	<link rel="stylesheet" href="<?php self::link("php/mbc-filemanager/css/style.css") ?>">
	<link rel="stylesheet" href="<?php self::link("css/style.css") ?>">
<?php
	}

	//-------------------------------------------------
	public function getToolbar(){
		global $CONFIG;
?>
	<div id="toolbar">
		<i id="colibrì-icon"></i>
		<a href="<?php self::link("bacheca") ?>" class="sicon"><i class="home"></i></a>
		<b id="menu-toggle" class="sicon"><i class="list-3"></i></b>
	</div>
<?php
	}



	//-------------------------------------------------
	public function getMenu(){
?>
	<ul class="menu">
		<li><div id="colibrì"><code>v<?php echo $this->version; ?></code></div></li>
		<li><a href="<?php self::link("bacheca") ?>"><b class="sicon"><i class="home"></i></b>Bacheca</a></li>
		<li><a href="<?php self::link() ?>" target="_blank"><b class="sicon"><i class="eye"></i></b>Visualizza sito</a></li>
		
		<?php
			//only webmasters
			if ($_SESSION['uclass'] == 2):
		?>
		<li><a href="./options"><b class="sicon"><i class="options-3"></i></b>Gestione sito</a></li>
		<?php endif;
			//only for administrators + webmasters
			if ($_SESSION['uclass'] >= 1):
		?>
		<li><a href="./articoli?q=0"><b class="sicon"><i class="label-2"></i></b>Articoli</a>
			<ul class="sub-menu">
				<li><a href="<?php self::link("editor?q=new") ?>">Nuovo</a></li>
				<li><a href="<?php self::link("articoli?q=0") ?>">Mostra Tutti</a></li>
				<li><a href="<?php self::link("articoli?q=1") ?>">Mostra Pagine Principali</a></li>
				<li><a href="<?php self::link("articoli?q=2") ?>">Mostra News</a></li>
				<li><a href="<?php self::link("articoli?q=3") ?>">Mostra Links</a></li>
				<li><a href="<?php self::link("articoli?garbage") ?>">Apri il Cestino</a></li>
			</ul>
		</li>
		<li><a href="<?php self::link("albums") ?>"><b class="sicon"><i class="hearth"></i></b>Albums</a></li>
		<?php endif; ?>
		
		<li><a href="<?php self::link("profilo") ?>"><b class="sicon"><i class="man"></i></b>Profilo</a>
			<ul class="sub-menu">
				<li><a href="<?php self::link("profilo") ?>">Modifica</a></li>
				<li><a href="<?php self::link("database/logout.php?redirect") ?>">Scollegati</a></li>
				<?php
					//START allowed only for administrators + webmasters
					if ($_SESSION['uclass'] >= 1):
				?>
				<li><a href="<?php self::link("profili") ?>">Gestisci Profili Pubblici<br></a></li>
				<?php endif; ?>
			</ul>
		</li>
	</ul>
<?php
	}


	//-------------------------------------------------
	public function getJQuery(){
		//for debug offline... remove 
		global $CONFIG;
		$local = substr($CONFIG['c_dir'],0,2) === "C:";
		if (!$local):
?>
<!--[if lte IE 8]>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
<![endif]-->
<!--[if gt IE 8]><!-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!--<![endif]-->
<?php
		else:
?>
<!--[if lte IE 8]>
<script src="<?php self::link("js/jquery/jquery-1.11.3.min.js") ?>"></script>
<![endif]-->
<!--[if gt IE 8]><!-->
<script src="<?php self::link("js/jquery/jquery-2.1.4.min.js") ?>"></script>
<!--<![endif]-->
<?php
		endif;
	}



	//-------------------------------------------------
	public function getaAlbumEditor($title='<h1>MBC - Album Editor</h1>'){
		global $pdo, $id, $ARTICLE;
		if (!empty($id) && !empty($ARTICLE)) $ida = $ARTICLE['idalbum'];
		else $ida = 0;
		
		echo $title;
?>
	<div class="inputs maxi aligned tools">
		<p><input type='checkbox' id="at-0" value="add">
			<label for="at-0"><b class="sicon"><i class="add"></i></b>
			Aggiungi nuovo album</label></p>
		<p><input type='checkbox' id="at-2" value="unsel">
			<label for="at-2"><b class="sicon"><i class="less"></i></b>
			Deseleziona album</label></p>
		<p><input type='checkbox' id="at-1" value="edit">
			<label for="at-1"><b class="sicon"><i class="pencil"></i></b>
			Modifica album selezionato</label></p>
		<p><input type='checkbox' id="at-3" value="delete">
			<label for="at-3"><b class="sicon"><i class="x"></i></b>
			Cancella album selezionato</label></p>
	</div>
	
	<h4>Album disponibili</h4>
	<div id="all-albums" class="inputs maxi aligned albums active"><?php

if ($pdores = $pdo->query("SELECT immagini_albums.*, immagini.src FROM immagini_albums left outer join immagini on immagini_albums.idimage = immagini.id")):
	//stampo preview + titolo album
	foreach ($pdores as $r):
		echo '<figure id="album-'.$r['id'].'" class="album'.($ida == $r['id'] ? ' selected' : '').'">'.
			'<div class="icon i-jpg loaded">'.
				'<label class="image" for="alb-'.$r['id'].'"'.
					($r['src'] ? ' style="background-image:url(\''.self::link('',false).'img/thumbs/'.htmlentities($r['src'],ENT_QUOTES).'\')"' : '').
				'></label>'.
			'</div>'.
			'<div class="title">'.
				'<label for="alb-'.$r['id'].'">'.htmlentities($r['titolo'],ENT_NOQUOTES).'</label>'.
			'</div>'.
			'<figcaption>'.
				'<input type="radio" name="album" id="alb-'.$r['id'].'" data-img="'.htmlentities($r['src'],ENT_QUOTES).'" data-t="'.htmlentities($r['titolo'],ENT_QUOTES).'" value="'.$r['id'].'">'.
				'<b class="sicon ed" data-id="'.$r['id'].'"><i class="pencil"></i></b>'.
				'<b class="sicon del" data-id="'.$r['id'].'"><i class="trash"></i></b>'.
			'</figcaption>'.
		'</figure>';
	endforeach;
endif;

	?></div>
<?php
	}

}


?>