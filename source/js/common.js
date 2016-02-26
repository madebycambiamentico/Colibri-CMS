/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: common functions for main project.
 *
 * @require: [jquery.js >= 1.11.3]
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

//show/hide loader on ajax requests (and at page fully loaded)
var BUSY = {
	busy: true,
	dom : $('#loader'),
	start: function(){
		this.busy = true;
		this.dom.removeClass('done');
	},
	end: function(){
		this.busy = false;
		this.dom.addClass('done');
	}
};

//replace " and ' with html entities
function printAttr(s){
	return s ? s.replace(/"/g,"&quot;").replace(/'/g,"&#039;") : '';
}

//replace < and > with html entities
function printText(s){
	return s ? s.replace(/</g,"&lt;").replace(/>/g,'&gt;') : '';
}



//------------------------ START lazy image loader
var ACTIVE_VIEW = null;//container id to search for .load
var ACTIVE_IMAGES = [];
function isInWindow(elem){
	var rect = elem.getBoundingClientRect();
	return (
		(rect.top<=25 && rect.bottom>=window.innerHeight-25) ||		//elemento che copre tutta l'area attiva
		(rect.top>=25 && rect.top<=window.innerHeight-25) ||			//top dell'elemento tra +25 e wh-25
		(rect.bottom>25 && rect.bottom<window.innerHeight-25)			//bottom dell'elemento tra +25 e wh-25
	);
}
function checkLoadImage(){
	if (ACTIVE_IMAGES.length){
		purge = 0;
		ACTIVE_IMAGES.each(function(){
			if (isInWindow(this)){
				var self = $(this).addClass('wait').removeClass('load');
				var src = self.data('thumb');
				$('<img>').load(function(){
					self.children('label, a').css('background-image','url("'+src+'")');
					setTimeout(function(){ self.addClass('loaded').removeClass('wait') },30);
				}).error(function(e){
					console.log(e)
				})[0].src = src;
				purge++
			}
		})
		if (purge) ACTIVE_IMAGES = $(ACTIVE_VIEW+' .load');
	};
}
function updateView(jid,waitresizeevent){
	if (jid) ACTIVE_VIEW = jid;
	ACTIVE_IMAGES = $(ACTIVE_VIEW+' .load');
	if (!waitresizeevent) $(window).trigger('resize.lazy');
}
//------------------------ END lazy image loader




//------------------------ START manager callbacks collection

//TODO:
//request filterig to iframe as javascript searchtool callback, istead of loading new page every time (if iframe has ALL)
var RFM = false;
//filtertype: image, media, all...
function openFM(filtertype){
	$('#file-manager').modalbox('on');
	if (RFM !== filtertype){
		RFM = filtertype;
		$('#file-manager iframe')[0].src = 'php/mbc-filemanager/?filter='+filtertype;
	}
}

/* FM_FILTER_FUNC: locks callback to listen only this event
* if true: allow all.
* if false: no lisener active.
* if "<listener>" call only that listener
*/
var FM_FILTER_FUNC = true;

// used for switch statement... TODO
var FM_LISTENER = false;

//available listener
var FM_LIST = {
	tiny: $.noop,
	
	//---------------------------------------------------
	selectFile: function(ofile,sdir){
		switch(FM_LISTENER){
			case 0://update album image
				if (ofile.g != 0) return false;
				$('#ealb-image').val(sdir + ofile.f);
				$('#file-manager').modalbox('off');
			break;
			case 1://update articles images
				if (ofile.g != 0) return false;
				$('#eart-image').val(sdir + ofile.f);
				$('#file-manager').modalbox('off');
			break;
			case 2://update article image
				if (ofile.g != 0) return false;
				$('#art-thumb').css('background-image', "url('img/thumbs/"+printAttr(sdir+ofile.f)+"')");
				$('#art-image').val(sdir + ofile.f);
				$('#file-manager').modalbox('off');
			break;
			case 'tiny-all'://update tinymce content
				var html;
				html = '<a href="'+printAttr(sdir+ofile.f)+'" class="file-'+ofile.e+'" title="'+printAttr(ofile.f)+'" rel="nofollow" target="_blank">'+printText(ofile.f)+'</a>';
				tinymce.activeEditor.insertContent(html);
				$('#file-manager').modalbox('off');
			break;
			case 'tiny-image'://update tinymce content - image
				var title = ofile.f.replace(/\.[a-z0-9]+$/i,"").replace(/[_-]+/g,' ');
				FM_LIST.tiny('uploads/'+sdir+ofile.f, {
					alt: 'desc' in ofile ? ofile.desc : title,
					title: title,
					width: ofile.w,
					height: ofile.h
				})
				FM_LIST.tiny = $.noop;
				$('#file-manager').modalbox('off');
			break;
			case 'tiny-file'://update tinymce content - generic file
				FM_LIST.tiny('uploads/'+sdir+ofile.f, {text: ofile.f.replace(/[_]+/g,' ')});
				FM_LIST.tiny = $.noop;
				$('#file-manager').modalbox('off');
			break;
			case 'tiny-media'://update tinymce content - video
				//TODO
				FM_LIST.tiny('uploads/'+sdir+ofile.f, {source2: "", poster: ""});
				FM_LIST.tiny = $.noop;
				$('#file-manager').modalbox('off');
			break;
			default:
		}
	},
	
	//---------------------------------------------------
	unselectFile: function(ofile,sdir){
		console.log(arguments)
	},
	
	//---------------------------------------------------
	deleteFiles: function(afiles){
		switch(FM_LISTENER){
			case 0: case 2://update album/article image
				var checkinput = $('#ealb-image').val();
				var checkinput2 = $('#art-image').val();
				$.each(afiles,function(i,ofile){
					if (Number(ofile.data.g) != 0) return true;
					if (ofile.d+ofile.f == checkinput) $('#ealb-image').val('');
					if (ofile.d+ofile.f == checkinput2){
						$('#art-image').val('');
						$('#art-thumb').css('background-image','');
					}
					$('#image-'+ofile.id).remove();
				})
			break;
			case 1://update articles image
				var checkinput = $('#eart-image').val();
				$.each(afiles,function(i,ofile){
					if (Number(ofile.data.g) != 0) return true;
					if (ofile.d+ofile.f == checkinput) $('#eart-image').val('');
				})
			break;
			default:
		}
	},
	
	//---------------------------------------------------
	addFiles: function(afiles){
		switch(FM_LISTENER){
			case 0://update album image
				var checkinput = $('#ealb-image').val();
				$.each(afiles,function(i,ofile){
					if (ofile.g != 0) return true;
					//INSERT
					if (ofile.db == 'insert'){
						$('#ealb-all-images').prepend( getHtmlImage({id: ofile.id, src: ofile.d+ofile.f},true) );
					}
					//UPDATE
					else{
						$('#Limg-'+ofile.id).css('background-image',"url('img/thumbs/"+printAttr(ofile.d+ofile.f)+'?'+(new Date().getTime())+"')");
					}
				});
				updateView()
			break;
			default:
		}
	}
};

//handle callback from mbc-filmanager.php
function mbcFileManagerOnChange(){
	console.log('--- mbcFileManagerOnChange ---');
	console.log(arguments);
	if (arguments[0] in FM_LIST){
		//console.log('existing function '+arguments[0]);
		if (!(FM_FILTER_FUNC == true || FM_FILTER_FUNC == arguments[0])) return false;
		console.log('----- '+arguments[0]+' -----');
		var args = Array.prototype.slice.call(arguments, 1);
		FM_LIST[arguments[0]].apply(null,args);
		return true;
	}
	return false;
}

//------------------------ END manager callbacks collection



$(window).load(function(){
	//support for 3dtransform
	if (!(function(){
		var el = $('<p>').css({
			'webkitTransform':'translateZ(1px)',
			'transform':'translateZ(1px)'
			//add other vendor prefixed properies if you want...
			//according to your css style sheet!
		});
		var has3d = el.css('-webkit-transform') || el.css('transform');
		return (has3d !== undefined && has3d.length > 0 && has3d !== "none");
	})()){
		$('body').addClass('no3d');
	};
	
	//window image loader listener + first check
	$(this).on('scroll.lazy resize.lazy',checkLoadImage);
	checkLoadImage();
	
	//toolbar
	$('#menu-toggle').click(function(){
		$('#wrapper>.menu').toggleClass('open')
	});
	
	//prevent forms to submit (use only ajax)
	$('form').submit(function(e){
		e.preventDefault();
	})
	
	//clear selections in file manager when closed.
	var FM = $('#file-manager');
	var FMI = FM.find('iframe');
	if (FM.length){
		FM[0].modalbox.on.close = function(){
			FMI[0].contentWindow.clearSel();
		}
	}
	
	//hide loader
	$('body').addClass('ready');
	BUSY.end();
});