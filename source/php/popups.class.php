<?php


class Popups {

	//-------------------------------------------------
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

	//-------------------------------------------------
	public function getForFileManager(){
?>
<div class="popup-cont full" id="file-manager">
	<h4>File Manager</h4>
	<div class="popup overthrow">
		<iframe></iframe>
	</div>
</div>
<?php
	}

}


?>