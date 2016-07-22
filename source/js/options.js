$(function(){
	//-------------------------------
	//multilanguage? create checks
	function updateSupportedLanguages(arr){
		var inputs = '';
		$.each(arr, function(i,v){
			inputs += '<input type="hidden" name="langs[]" value="'+printAttr(v)+'">';
		});
		$('#lang-supported').html(inputs);
	}
	//call languages-get-all.php only once
	var loadedlangs = false;
	var $onlymulti = $('#lang-options, #lang-open');
	//if site multilanguage show options
	$('#ismultilang').change(function(){
		if (this.checked){
			if (loadedlangs){
				$onlymulti.removeClass('hidden');
			}
			else{
				//load supported languages
				loadedlangs = true;
				$.get('database/languages-get-all.php',null,null,'json')
					.done(function(json){
						if (json.error !== false) return alert("ERRORE:\n"+json.error);
						console.log(json);
						var checks = '', selects = '';
						var checked = [];
						$.each(json.languages, function(k,arr){
							checks += '<label><input value="'+printAttr(arr['c'])+'" type="checkbox"'+(arr['x']=='1' ? ' checked' : '')+'> <i>'+printText(arr['n'])+'</i></label>';
							selects += '<option value="'+printAttr(arr['c'])+'"'+(arr['x']=='1' ? ' selected' : '')+'>'+printText(arr['n'])+'</option>';
							if (arr['x']=='1') checked.push(arr['c']);
						});
						$('#lang-codes').html(checks);
						$('#lang-default').html(selects);
						$onlymulti.removeClass('hidden');
						updateSupportedLanguages(checked);
					})
					.fail(function(e){
						alert("Ooops!");
						console.log(e);
					})
					.always(function(){
						BUSY.end();
					});
			}
		}
		else{
			$onlymulti.addClass('hidden');
		}
	})
	.change();
	
	$('#lang-open, #lang-apply').click(function(){
		$('#languages-pop').modalbox();//toggle dialog
	});
	
	$('#languages-pop')[0].modalbox.on.close = function(){
		//search for all checked inputs, store in array and call updateSupportedLanguages
		var checked = [];
		$('#languages-pop input:checked').each(function(){
			checked.push( this.value );
		});
		updateSupportedLanguages(checked);
	};
	
	//-------------------------------
	//save form edits
	$('.save-arctic').click(function(){
		BUSY.start();
		$.post('database/website-edit.php',$('#my-article').serialize(),null,'json')
			.done(function(json){
				if (json.error !== false) return alert("ERRORE:\n"+json.error);
				alert("Sito aggiornato correttamente");
			})
			.fail(function(e){
				alert("Ooops!");
				console.log(e);
			})
			.always(function(){
				BUSY.end();
			});
	});
	
	//-------------------------------
	//change template script
	var openedtempl = false;
	
	$('#templates-pop .popup').on('scroll.lazy',checkLoadImage);
	
	$('#change-template').click(function(){
		$('#templates-pop').modalbox('open');
		$.get('database/website-templates.php',null,null,'json')
			.done(function(json){
				//generate popup items
				console.log(json);
				
				if (json.error !== false) return alert("Errore:\n"+json.error);
				
				if (openedtempl){
					updateView('#templates-pop');
					return false;
				}
				
				openedtempl=true;
				var c = $('#w-templ').val();
				var templates = new Array(json.templates.length);
				
				$.each(json.templates,function(k,v){
					templates[k] = '<div id="template-'+k+'" class="template">'+
						'<div class="imgcont load" data-thumb="templates/'+printAttr(v.folder)+'/screenshots/0.png"><label class="image">'+
							'<input id="w-templ-'+k+'" name="newtemplate" type="radio" value="'+printAttr(v.folder)+'"'+(c==v.folder ? ' checked' : '')+'>'+
						'</label></div>'+
						'<p><b>Titolo:</b> '+printText(v.name)+'</p>'+
						'<p><b>Autore:</b> '+printText(v.author)+'</p>'+
						'<p><b>Descrizione:</b> '+printText(v.description).replace("\s+"," ")+'</p>'+
						'<div class="tools">'+
							('web' in v ? '<p><a target="_blank" href="'+printAttr(v.web[0])+'" title="'+printAttr(v.web[1])+'"><b class="sicon"><i class="star"></i></b> Sito di riferimento</a></p>' : '')+
							'<p><label for="w-templ-'+k+'"><b class="sicon"><i class="hearth"></i></b> SCEGLI</label></p>'+
						'</div>'+
					'</div>';
				});
				$('#all-templates').html(templates.join(''));
				
				//$('#templates-pop')[0].modalbox.refresh();
				$('#all-templates input').change(function(){
					var newt = this.value;
					$.get('database/website-edit-template.php',{template:newt},null,'json')
						.done(function(json){
							console.log(json);
							if (json.error) return alert("ERRORE:\n"+json.error);
							$('#my-template p, #my-template .tools').remove();
							$('#w-templ').val(newt);
							var bkg = $('#my-template .imgcont').css('background-image').replace(/templates\/[a-z0-9_\- ]+\//i,"templates/"+newt+"/");
							$('#my-template .imgcont').css('background-image',bkg);
							$('#templates-pop').modalbox('close');
						})
						.fail(function(e){
							alert("Ooops!");
							console.log(e);
						})
				});
				
				updateView('#templates-pop');
			})
			.fail(function(e){
				alert("Ooops!");
				console.log(e);
			})
	});
})