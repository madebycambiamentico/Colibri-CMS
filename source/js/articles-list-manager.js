/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: manage articles quick edits, restore, move to trash and complete deletions.
 *
 * @require: [jquery.js >= 1.11.3], ./common.js, ./modalbox.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

(function(){

//searchability
$.fn.setFigureTitle = function(a,b){
	return this.each(function(){
		$(this)
			.attr( 'data-t', a ? a.toLowerCase() : $(this).find('.art-props h4').text().toLowerCase() )
			.attr( 'data-c', b ? b.toLowerCase() : $(this).find('.art-desc').text().toLowerCase() );
	});
}

$.fn.quickEdit = function(){
	return this.change(function(){
		var id = this.value;
		var props = $(this).data();
		//console.log(props);
		$('#eart-id').val(id);
		$('#eart-wasindex').val(props.idx);
		$('#eart-wasindexlang').val(props.idxl);
		$('#eart-isinmenu').prop('checked',props.mnu);
		$('#eart-isindex').prop('checked',props.idx);
		$('#eart-isindexlang').prop('checked',props.idxl);
		$('#eart-lang').val(props.lang);
		$('#eart-image').val(props.img);
		$('#eart-title').val( $($('#art-'+id+' .art-props h4').contents()[0]).text().trim() ).change();
		$('#eart-desc').val( $('#art-'+id+' .art-desc').text() );
		//determine article type
		smartlink.setPrefix(props.t+'');
		$('#quick-edit-art').modalbox();
	})
}


function saveArctic(){
	//console.log('---- saveArctic ----');
	BUSY.start();
	$.post('database/article-quick-edit.php',$('#edit-article').serialize(),null,'json')
		.success(function(json){
			//console.log(json);
			if (json.error) return alert("ERRORE:\n"+json.error);
			//update input data
			$('#art-'+json.id+' input.quick-mod').data({
				idx : json.idx,
				mnu : json.mnu,
				img : json.img,
				idxl: json.idxl,
				lang: json.lang
			});
			//update image
			if (!json.img) $('#art-'+json.id+' label.art-img').css('background-image',"");
			else $('#art-'+json.id+' label.art-img').css('background-image',"url('img/thumbs/"+printAttr(json.img)+"')");
			//update title and desc
			var type = ' <i class="art-type">('+printText(artTypes[$('#art-'+json.id+' .art-props h4').data('t')].n)+')</i>';
			$('#art-'+json.id+' .art-props h4').text(json.title).append(type);
			$('#art-'+json.id+' .art-desc').text(json.desc);
			//update data for search tool
			$('#art-'+json.id).setFigureTitle(json.title, json.desc);
			//close
			$('#quick-edit-art').modalbox('off');
		})
		.error(function(e){
			console.log(e);
			alert("Oooops!");
		})
		.always(function(){
			//unlock form...
			BUSY.end();
			//TODO update sitemap
			$.get('sitemap-generator-generic.php').always(function(e){console.log(e)});
		});
}


/*
* delete all articles specified in array of ids or serialized string.
* @param		(array) ids
* or			(string) ids
*/
function deleteArt(ids){
	//console.log('---- deleteArt ----');
	if (!ids || ids=="garbage=1") return false;
	BUSY.start();
	var data = ($.isArray(ids) ?
		(ISGARBAGE ?
			{trash:ids, isgarbage:true}
			: {trash:ids})
		: ids);
	//console.log(data);
	$.post('database/article-garbage.php',data,null,'json')
		.success(function(json){
			//console.log(json);
			if (json.error) return alert("ERRORE:\n"+json.error);
			//delete from view...
			ids = '#art-'+json.ids.join(',#art-');
			$(ids).remove();
		})
		.error(function(e){
			console.log(e);
			alert("Oooops!");
		})
		.always(function(){
			//unlock form...
			BUSY.end();
			//TODO: update sitemap
			if (!ISGARBAGE){
				$.get('sitemap-generator-generic.php').always(function(e){console.log(e)});
			}
		});
}

//for garbage article: resurrect (see delete)
function resurrectArt(ids){
	//console.log('---- resurrectArt ----');
	if (!ids || ids=="garbage=1") return false;
	BUSY.start();
	var data = ($.isArray(ids) ? {trash:ids} : ids);
	//console.log(data);
	$.post('database/article-resurrect.php',data,null,'json')
		.success(function(json){
			//console.log(json);
			if (json.error) return alert("ERRORE:\n"+json.error);
			//delete from view...
			ids = '#art-'+json.ids.join(',#art-');
			$(ids).remove();
		})
		.error(function(e){
			console.log(e);
			alert("Oooops!");
		})
		.always(function(){
			//unlock form...
			BUSY.end();
		});
}

$.fn.quickDel = function(){
	return this.click(function(){
		var id = $(this).data('id');
		var more = ISGARBAGE ? "Verrà eliminato definitivamente e non sarà più recuperabile." : "L'articolo sarà ancora disponibile nel cestino se cambi idea.";
		if (!confirm("Sei sicuro di voler cancellare l'articolo \""+$('#art-'+id+' .art-props h4').text()+"\"?\n"))
			return false;
		deleteArt([id]);
	})
}

$.fn.quickResurr = function(){
	return this.click(function(){
		var id = $(this).data('id');
		resurrectArt([id]);
	})
}


var smartlink = {
	date: $('#eart-smart-date').val(),
	prefix: "",
	setPrefix: function(type){
		$('#eart-type').val(type);
		if (type == 1) smartlink.prefix = "";//main pages
		else smartlink.prefix = artTypes[type].pfx+'/';
		return smartlink.update($('#eart-title').val());
	},
	update: function(title){
		title = removeDiacritics(title).replace(/\s+/g,'-').replace(/[?:;"#\\\/]/g,"").toLowerCase();
		$('#eart-smart-1').val(smartlink.prefix+title);
		return smartlink;
	}
}

var searchTool = {
	f_type : "",
	f_title : "",
	f_desc : "",
	setTitle : function(s){
		if (s === '') searchTool.f_title = '';
		else searchTool.f_title = '[data-t*="'+printAttr(s)+'"]';
		searchTool.checkFilter();
		return this;
	},
	setDesc : function(s){
		if (s === '') searchTool.f_desc = '';
		else searchTool.f_desc = '[data-c*="'+printAttr(s)+'"]';
		searchTool.checkFilter();
		return this;
	},
	setType : function(s){
		if (s === '0') searchTool.f_type = '';
		else searchTool.f_type = '.type-'+s;
		searchTool.checkFilter();
		return this;
	},
	checkFilter : function(){
		if (searchTool.f_type === '' && searchTool.f_title === '' && searchTool.f_desc === ''){
			//console.log("nessun filtro richiesto")
			$('#all-articles').removeClass('filtering');
		}
		else{
			//console.log("add filtering")
			$('#all-articles').addClass('filtering');
			searchTool.updateSearch();
		}
		updateView();
		return this;
	},
	updateSearch : function(){
		//console.log("updateSearch")
		$('#all-articles .art-cont').removeClass('show');
		return $('#all-articles .art-cont'+searchTool.f_type+searchTool.f_title+searchTool.f_desc).addClass('show');
	}
}

$(function(){
	//title and shortlinks
	$('#eart-title').change(function(){
		smartlink.update(this.value)
	});
	//load images...
	updateView('#all-articles',true);
	//search tools:
	$('#all-articles .art-cont').setFigureTitle();
	$('#filtertype').change(function(){
		//console.log('filtertype')
		searchTool.setType(this.value);
	});
	$('#search-title').keyup(function(){
		//console.log('title')
		searchTool.setTitle(this.value);
	});
	$('#search-desc').keyup(function(){
		//console.log('desc')
		searchTool.setDesc(this.value);
	});
	//selection tool (select only sorted articles)
	$('#sel-all').change(function(){
		$('#all-articles .art-img input').prop('checked',false);
		$('#all-articles .art-cont'+searchTool.f_type+searchTool.f_title+searchTool.f_desc+' .art-img input').prop('checked',true);
	});
	$('#desel-all').change(function(){
		$('#all-articles .art-img input').prop('checked',false);
	});
	//delete all!!!
	$('#del-all').change(function(){
		var more = ISGARBAGE ? "Verranno eliminati definitivamente e non saranno più recuperabili." : "Saranno ancora disponibili nel cestino se cambi idea.";
		if (!confirm("Sei sicuro di voler cancellare tutti gli articoli selezionati?\n"+more))
			return false;
		deleteArt($('#my-article').serialize());
	});
	//resurrect all!
	$('#resurr-all').change(function(){
		resurrectArt($('#my-article').serialize());
	});
	//edit button for each article
	$('.quick-mod').quickEdit();
	$('.quick-delete').quickDel();
	$('.resurrect').quickResurr();
	//save...
	$('#saveArctic').click(saveArctic);
	//open file manager/remove image
	$('#eart-fm-1').click(function(){FM_LISTENER=1; openFM('image')});
	$('#eart-fm-1-del').click(function(){
		$('#eart-image').val('');
	});
});

})();