/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: manage albums edit/add/delete.
 *
 * @require: [jquery.js >= 1.11.3], ./common.js, ./simple-modal-box.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

(function(){

var OPT = {
	jsimage : 1
};

/*
* @param	(object)	a
*		{
*			id :		<album id in database>
*			t :		<album title>
*			img :		<image relative src from uploads>
*		}
*/
function getHtmlAlbum(a, prepare){
	var htmlID = 'alb-'+a.id;
	var ojim = OPT.jsimage===1 && a.img !== undefined;
	var html = '<figure class="album" id="album-'+a.id+'">'+
		//loading image...
		'<div class="icon i-jpg' + (ojim ? ' load" data-thumb="img/thumbs/'+printAttr(a.img) : '')+'">'+
			'<label for="'+htmlID+'" class="image" id="L'+htmlID+'"'+
				(ojim ? '' : 'style="background:url(\'img/thumbs/'+printAttr(a.img)+'\')"')+
			'></label>'+
		'</div>'+
		//tools
		'<div class="title"><label for="'+htmlID+'">'+printText(a.t)+'</label></div>'+
		'<figcaption>'+
			'<input type="radio" name="album" id="'+htmlID+'" data-img="'+printAttr(a.img)+'" data-t="'+printAttr(a.t)+'" value="'+a.id+'">'+
			'<b class="sicon ed" data-id="'+a.id+'"><i class="pencil"></i></b>'+
			'<b class="sicon del" data-id="'+a.id+'"><i class="trash"></i></b>'+
		'</figcaption>'+
	'</figure>';
	
	if (prepare){
		html = $(html).setFigureTitle();
		html.find('#'+htmlID).setAsAlbum();
		html.find('b.ed').editAlbum();
		html.find('b.del').deleteAlbum();
	}
	
	return html;
}

$.fn.setFigureTitle = function(){
	return this.each(function(){
		$(this).attr( 'data-t', $(this).find('.title label').text().toLowerCase() );
	});
}

$.fn.setAsAlbum = function(){
	return this.change(function(){
		var id = this.value;
		$('#all-albums figure').removeClass('selected');
		$('#album-'+id).addClass('selected');
	});
}

$.fn.editAlbum = function(){
	return this.click(function(){
		var id = $(this).data('id');
		var t = $('#alb-'+id).data('t');
		var img = $('#alb-'+id).data('img');
		editorAlbum(id,t,img)
	});
}
function editorAlbum(id,t,img){
	//console.log(arguments)
	//update form
	$('#edit-album')[0].reset();
	$('#ealb-id').val(id);
	$('#ealb-title').val(t);
	$('#ealb-image').val(img);
	//show popup...
	ACTIVE_VIEW = '#ealb-all-images';
	$('#edit-album-popup').modalbox();
	albumLoadImages(id,0);
}

$.fn.deleteAlbum = function(){
	return this.click(function(){
		var id = $(this).data('id');
		//show dialog...
		deleteAlbum(id);
	});
}
function deleteAlbum(id){
	if (!confirm("Vuoi veramente cancellare l'album\n"+$('#album-'+id).data('t')+"?\nNon sarà più disponibile per nessun articolo precedentemente collegato.")) return false;
	//lock form...
	BUSY.start();
	$.ajax({
		type: 'GET',
		dataType: 'json',
		data: {id: id},
		url: 'database/album-delete.php'
	})
	.done(function(e){
		//console.log(e);
		if (e.error) return alert("ERRORE:\n"+e.error);
		$('#album-'+e.id).remove();
	})
	.fail(function(e){
		console.log(e);
		alert("Oooops!");
	})
	.always(function(){
		//unlock form...
		BUSY.end();
	});
	return true;
}

function addAlbum(a){
	//console.log("adding album");
	//console.log(a);
	$('#all-albums').prepend( getHtmlAlbum(a, true) )
}

function updateAlbum(obj){
	//console.log('---- updateAlbum ----')
	//console.log(obj)
	//update title
	if ('t' in obj){
		$('#album-'+obj.id+' .title>label').text(obj.t);
		$('#alb-'+obj.id).data('t',obj.t);
		$('#album-'+obj.id).setFigureTitle();
	}
	//update image
	if ('img' in obj){
		switch (obj.img){
			case true:
				break;
			case false:
				$('#album-'+obj.id+' label.image').css('background-image','none');
				$('#alb-'+obj.id).data('img','');
				break;
			default:
				$('#album-'+obj.id+' label.image').css('background-image','url("img/thumbs/'+printAttr(obj.img)+'")');
				$('#alb-'+obj.id).data('img',obj.img);
		}
	}
}


//print images, order by selected for (id) album before non selected
function albumLoadImages(id, which){
	switch(which){
		case 0://all (selected and not)
			which = 0;
		break;
		case 1://only selected
			which = 1;
		break;
		case 2://only not selected
			which = 2;
		break;
		default:
			which = 0;
	}
	if (which <= 1) $('#ealb-all-images').empty();
	//lock form...
	//TODO
	$.get('database/album-get-images.php',{id: id, request: which},null,'json')
	.done(function(e){
		//console.log(e);
		if (e.error) return alert("ERRORE:\n"+e.error);
		//print images with loading feature
		var newimages = '';
		$.each(e.images,function(i,im){
			newimages += getHtmlImage(im)
		})
		$('#ealb-all-images').append(newimages);
		$('#ealb-all-images input').setAsImage();
		updateView();
	})
	.fail(function(e){
		console.log(e);
		alert("Oooops!");
	})
	.always(function(){
		//unlock form...
		//TODO
	});
	return true;
}


window.getHtmlImage = function(a, prepare){
	//prepare container
	var htmlID = 'img-'+a.id;
	var title = (a.src.split('/').pop()).replace(/\.[a-z0-9]+$/i,"");
	var html = '<figure id="image-'+a.id+'"'+(a.select ? ' class="selected"' : '')+'>'+
		//loading image...
		'<div class="icon i-jpg' + (OPT.jsimage==1 ? ' load" data-thumb="img/thumbs/'+printAttr(a.src) : '')+'">'+
			'<label for="'+htmlID+'" class="image" id="L'+htmlID+'"'+
				(OPT.jsimage==1 ? '' : 'style="background:url(\'img/thumbs/'+printAttr(a.src)+'\')"')+
			'></label>'+
		'</div>'+
		'<div class="title">'+
			'<label id="imagesrc-'+a.id+'" for="'+htmlID+'">'+printText(title)+'</label>'+
		'</div>'+
		'<figcaption>'+
			'<input type="checkbox" name="IMAGES[]" id="'+htmlID+'" value="'+a.id+'"'+(a.select ? ' checked' : '')+'>'+
			'<b class="sicon" onclick="preview('+a.id+')"><i class="search"></i></b>'+
		'</figcaption>'+
	'</figure>';
	if (prepare){
		html = $(html);
		html.find('#'+htmlID).setAsImage();
	}
	return html;
}


$.fn.setAsImage = function(){
	return this.change(function(){
		var id = this.value;
		//toggle class for <figure>
		if (this.checked){
			$('#image-'+id).addClass('selected');
		}
		else{
			$('#image-'+id).removeClass('selected');
		}
	});
}

function preview(){
	
}



function saveAlbum(options){
	var path = 'database/album-add-or-edit.php';
	//controllo i dati
	var datas = {
		id: $('#ealb-id').val(),
		title: $('#ealb-title').val(),
		image: $('#ealb-image').val()
	};
	if (!datas.title){
		alert("Non puoi inserire un album senza titolo!");
		return false;
	}
	else{
		//aggiorno la chiamata a php
		switch (options){
			case 'title': case 1:
				//update only title... if there's an id. else insert whole data.
				if (datas.id) path = 'database/album-edit-title.php';
			break;
			case 'image': case 2:
				//update only image... only if there's an id. else insert whole data
				if (datas.id){
					path = 'database/album-edit-image.php';
				}
			break;
			default:
				//console.log("update title + image")
		}
	}
	//lock form...
	BUSY.start();
	$.ajax({
		type: 'GET',
		dataType: 'json',
		data: $("#edit-album").serialize(),
		url: path
	})
	.done(function(json){
		//console.log(json);
		if (json.error) return alert("ERRORE:\n"+json.error);
		if (json.success == 'insert'){
			addAlbum(json);
			alert("Album inserito con successo.");
		}
		else if (json.success == 'update'){
			updateAlbum(json);
			alert("Modifica album effettuata.");
		}
	})
	.fail(function(e){
		console.log(e);
		alert("Oooops!");
	})
	.always(function(){
		//unlock form...
		BUSY.end();
	});
	return true;
}


$(function(){
	//apply function to buttons
	$('#at-0').change(function(){editorAlbum('','','')});
	$('#at-1').change(function(){
		var input = $('#all-albums input:checked');
		if (!input.length) return false;
		var id = input[0].id;
		var t = input.data('t');
		var img = input.data('img');
		editorAlbum(id,t,img)
	});
	$('#at-2').change(function(){
		$('#my-article')[0].reset();
		$('#my-article .selected').removeClass('selected');
	});
	$('#at-3').change(function(){
		var input = $('#all-albums input:checked');
		if (!input.length) return false;
		var id = input[0].value;
		deleteAlbum(id)
	});
	
	$('#ealb-fm-1').click(function(){FM_LISTENER=0; openFM('image')});
	$('#ealb-fm-1-del').click(function(){
		$('#ealb-image').val('');
	});
	
	$('#edit-album .savealbum').click(function(){
		//console.log($(this).data('action'));
		saveAlbum($(this).data('action'));
	});
	
	//enable exitent albums
	$('#all-albums>figure').setFigureTitle();
	$('#all-albums input').setAsAlbum();
	$('#all-albums b.ed').editAlbum();
	$('#all-albums b.del').deleteAlbum();
	
	//on scroll/resize, load active images...
	$('#edit-album-popup .popup').on('scroll.lazy resize.lazy',checkLoadImage);
	
	$('#edit-album-popup')[0].modalbox.on.close = function(){
		updateView('#all-albums');
	}
});

})();