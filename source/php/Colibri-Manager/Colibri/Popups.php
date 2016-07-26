<?php namespace Colibri;

/**
* allow to create popup from template of print common ones
*
* standard templates available:
* - generic (popup with a <div> container)
* - generic_form (popup with integrated <form>)
* - generic_iframe (popup with integrated <iframe>)
* popups commonly used in the manager pages:
* - album editor
* - file manager (iframe)
*/


class Popups {

	/**
	* Print a standard (DIV) popup
	*
	* @param (string) $pop_id					id of popup
	* @param (array)  $pop_classes			classes to add to popup, other than "popup-cont"
	* @param (string) $title [optional]		title of popup window
	* @param (string) $div_id					if of the <div>
	* @param (array)  $div_classes			classes to add to <div>, other than "popup overthrow"
	* @param (string) $div_html				arbitrary content of <div>
	*/
	public function generic(
		$pop_id="", $pop_classes=[], $title="",
		$div_id="", $div_classes=[],
		$div_html=""
	){
?>
<div class="popup-cont <?php echo htmlentities(implode(" ",$pop_classes),ENT_QUOTES) ?>" id="<?php echo htmlentities($pop_id,ENT_QUOTES) ?>">
	<h4><?php echo htmlentities($title) ?></h4>
	<div id="<?php echo htmlentities($div_id,ENT_QUOTES) ?>" class="popup overthrow <?php echo htmlentities(implode(" ",$div_classes),ENT_QUOTES) ?>">
		<h5></h5>
		<?php echo $div_html ?>
	</div>
</div>
<?php
	}

	/**
	* Print a standard FORM popup
	*
	* @param (string) $pop_id					id of popup
	* @param (array)  $pop_classes			classes to add to popup, other than "popup-cont"
	* @param (string) $title [optional]		title of popup window
	* @param (string) $form_id					if of the <form>
	* @param (array)  $form_classes			classes to add to <form>, other than "popup overthrow"
	* @param (string) $form_method			GET or POST. please use ajax and prevent form submission with page redirect.
	* @param (string) $form_enc				enctype: if you need to send files, use "multipart/form-data"
	* @param (string) $form_html				arbitrary content of <form>
	*/
	public function generic_form(
		$pop_id="", $pop_classes=[], $title="",
		$form_id="", $form_classes=[], $form_method="GET", $form_enc=null,
		$form_html=""
	){
?>
<div class="popup-cont <?php echo htmlentities(implode(" ",$pop_classes),ENT_QUOTES) ?>" id="<?php echo htmlentities($pop_id,ENT_QUOTES) ?>">
	<h4><?php echo htmlentities($title) ?></h4>
	<form id="<?php echo htmlentities($form_id,ENT_QUOTES) ?>"
			class="popup overthrow <?php echo htmlentities(implode(" ",$form_classes),ENT_QUOTES); ?>"
			method="<?php echo htmlentities($form_method,ENT_QUOTES); ?>"
			<?php if ($form_enc) echo 'enctype="'.htmlentities($form_enc,ENT_QUOTES).'"'; ?>>
		<h5></h5>
		<?php echo $form_html ?>
	</form>
</div>
<?php
	}
	
	
	
	/**
	* Print a standard IFRAME popup (wthout url)
	*
	* @param (string) $pop_id					id of popup
	* @param (array)  $pop_classes			classes to add to popup, other than "popup-cont"
	* @param (string) $title [optional]		title of popup window
	* @param (string) $url [optional]		iframe url. please leave it empty to fast load. set url by js in case of need.
	*/
	public function generic_iframe($pop_id="", $pop_classes=[], $title="", $url=null){
?>
<div class="popup-cont full <?php echo htmlentities(implode(" ",$pop_classes),ENT_QUOTES) ?>" id="<?php echo htmlentities($pop_id,ENT_QUOTES) ?>">
	<h4><?php echo htmlentities($title) ?></h4>
	<div class="popup overthrow iframe-holder">
		<iframe <?php echo $url ? 'src="'.htmlentities($url,ENT_QUOTES).'"' : '' ?>></iframe>
	</div>
</div>
<?php
	}
	
	
	
	/**
	* Print mbc file manager iframe popup
	*
	* @see generic_iframe()
	*/
	public function getForFileManager(){
		$this->generic_iframe("file-manager", [], "File Manager");
	}
	
	
	
	/**
	* Print albums manager popup
	*/
	public function getForAlbums(){
?>
<div class="popup-cont tools-bkg" id="edit-album-popup">
	<h4>Strumenti Album</h4>
	<form id="edit-album" class="popup overthrow" method="get">
		<h5></h5>
		<input type="hidden" id="ealb-id" name="id" value="-1">
		
		<ul class="inputs list">
			<li>Titolo<p class="small">(Gli album devono avere nome unico!)</p></li>
		</ul>
		<div class="inputs field-btn">
			<input id="ealb-title" name="title" type="text" placeholder="nome album..."><!--
			--><b class="btn savealbum" data-action="title">Salva</b>
		</div>
		
		<ul class="inputs list">
			<li>Immagine in evidenza</li>
		</ul>
		<div class="inputs field-btn">
			<input id="ealb-image" name="image" type="text" class="ronly" placeholder="immagine principale..." readonly><!--
			--><b id="ealb-fm-1-del" class="btn mid tinymcefont">&#xe012;</b><!--
			--><b id="ealb-fm-1" class="btn mid tinymcefont">&#xe034;</b><!--
			--><b class="btn savealbum" data-action="image">Salva</b>
		</div>
		<br>
		<div class="inputs btn">
			<b class="btn savealbum" data-action="">Salva Album</b>
		</div>
		
		<br>
		<ul class="list">
			<li>immagini dell'album</li>
		</ul>
		<!-- show all images available, order first the selected ones -->
		<div id="ealb-all-images" class="albums active"></div>
		<br>
		<div class="inputs btn">
			<b class="btn savealbum" data-action="">Salva Album</b>
		</div>
	</form>
</div>
<?php
	}

}


?>