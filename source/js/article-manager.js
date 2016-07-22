/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: manage article.
 *
 * @require: [jquery.js >= 1.11.3], ./common.js, ./simple-modal-box.js, ./diatrics-remover.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
 */

//--------- START tinymce plugin ----------
//see common.js for FM_LIST, FM_LISTENER, FM_FILTER_FUNC
(function(){
	function openTFM(types,callback){
		FM_LIST.tiny = callback;
		FM_LISTENER = 'tiny-'+types;
		openFM(types === 'file' ? 'all' : types);
	}
	tinymce.PluginManager.add('filemanager', function(editor, url) {
		editor.addShortcut("Ctrl+E", "", function(){FM_LIST.tiny = $.noop; openTFM('all')});
		// Add a button that opens a window
		editor.addButton('manager', {
			text: 'MBC',
			icon: "browse",
			tooltip: "File manager",
			shortcut: "Ctrl+E",
			onclick: function() {
				openTFM('all',$.noop)
			}
		});
		// Adds a menu item to the tools menu
		editor.addMenuItem('manager', {
			text: 'File manager',
			icon: "browse",
			shortcut: "Ctrl+E",
			context: 'insert',
			onclick: function() {
				openTFM('all',$.noop)
			}
		});
	});
	function tinyMCEFileManager(callback, value, meta){
		//console.log(arguments);
		openTFM(meta.filetype, callback);
		return false;
	}
	tinymce.init({
		language: 'it',
		selector: 'textarea.tiny-area',
		/*theme: "modern",
		skin: 'light',*/
		plugins: ['autolink link image lists hr code fullscreen insertdatetime media colorpicker textcolor autoresize table contextmenu filemanager'],//preview
		menu: {
			//file: {title: 'File', items: 'newdocument'},
			//edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
			//insert: {title: 'Insert', items: 'link media | template hr'},
			//view: {title: 'View', items: 'visualaid'},
			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
			table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
			tools: {title: 'Tools', items: 'spellchecker code'}
		},
		toolbar: [
			'insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
			'forecolor backcolor link unlink | image media manager | code' //fullscreen preview
		],
		autoresize_max_height: 320,
		file_picker_callback: tinyMCEFileManager
	});
})();
//--------- END tinymce plugin ----------



$(function(){
	
	var smartlink = {
		date: $('#art-smart-date').val(),
		prefix: "",
		setPrefix: function(){
			var pfx = $('#art-type').val();
			if (pfx == '1') smartlink.prefix = "";//main pages
			else smartlink.prefix = $('#art-type option:selected').data('prefix')+'/';
			return smartlink.update($('#art-title').val());
		},
		update: function(title){
			title = removeDiacritics(title).replace(/\s+|['"]+/g,'-').replace(/[?:;"#\\\/]/g,"").toLowerCase();
			$('#art-smart-1').val(smartlink.prefix+title);
			$('#art-smart-2').val(smartlink.date+smartlink.prefix+title);
			return smartlink;
		}
	};
	
	//title and shortlinks
	$('#art-type').change(smartlink.setPrefix);
	$('#art-title').change(function(){
		smartlink.update(this.value)
	});
	
	//article image
	$('#art-fm-1').click(function(){FM_LISTENER=2; openFM('image')});
	$('#art-fm-1-del').click(function(){
		$('#art-image').val('');
		$('#art-thumb').css('background-image','');
	});
	
	//sendform
	$(".save-arctic").click(function(){
		BUSY.start();
		//tinymce will not update before "submit" event, so now we force it,
		//then sanitize tinyMCE html if manually edited, to prevent page/css breaks...
		tinyMCE.triggerSave();
		var art = $('#art-body').val();
		art = $('<div>').html(art).html();
		$('#art-body').val(art);
		//create short description if not exists
		if ($('#art-desc').val() == ''){
			//try to determine a description from first 156 characters
			var txt = $('<div>').html($('#my-article .tiny-area').val()).text().substr(0,156).replace(/\s+|\n/g," ");
			if (txt.length > 153) txt = txt.substr(0,153)+'...';
			$('#art-desc').val(txt);
		}
		//send ajax
		$.post('database/article-add-or-edit.php', $("#my-article").serialize(), null, 'json')
		.done(function(json){
			//console.log(json);
			if (json.error) return alert("ERRORE:\n"+json.error);
			if (json.success == 'insert'){
				$('#art-id').val(json.id);
				//update options in "articolo originale"
				$('#art-parentlang option:first-child').after('<option value="'+json.id+'">'+printText($('#art-title').val())+'</option>');
				alert('Nuovo articolo inserito con successo!')
			}
			else if (json.success == 'update'){
				//delete "sub-articoli correlati"
				//TODO
				$('#all_sub_arts input:checked').each(function(){
					$(this).parent(/*label*/).parent(/*p*/).remove();
				});
				if (!$('#all_sub_arts input').length){
					if (!$('#all_sub_arts p').length)
						$('#all_sub_arts').append("<p><b>(nessun articolo correlato a questa pagina)</b></p>");
				}
				alert('Aggiornamento completato con successo!')
			}
		})
		.fail(function(e){
			console.log(e);
			alert("Oooops!");
		})
		.always(function(){
			//unlock form...
			BUSY.end();
			//update sitemap
			$.get('sitemap-generator-generic.php').always(function(e){console.log(e)});
		});
	});
});