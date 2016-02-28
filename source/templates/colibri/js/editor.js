
//post to iframe
(function ($, doc) {
	var _frameCount = 0,
		_callbackOptions = ['done', 'fail', 'always'],
		_hasFailed = function (_frame) {
			var frameHtml = $(_frame).contents().find('body').html();
			return /(server|unexpected)\s+error/i.test(frameHtml);
		},
		_createFrame = function () {
			return $('<iframe>').prop('name', 'jq-frame-submit-' + _frameCount++).hide().appendTo(doc.body);
		};

	$.fn.extend({
		frameSubmit: function (options) {

			return this.each(function () {
				var deferred = $.Deferred(),
					_form = this,
					initialTarget = _form.target,
					hasTarget = _form.hasAttribute('target'),
					hasFailed = options.hasFailed || _hasFailed,

					//The initial frame load will fire a load event so we need to
					//wait until it fires and then submit the _form in order to monitor
					//the _form's submission state.
					$frame = _createFrame().one('load', function(){
						$frame.one('load', function () {
							deferred[hasFailed(this) ? 'reject' : 'resolve'](_form, this, options);
							//$frame.remove();
						});

						_form.submit();

						//restore initial target attribute's value
						if (hasTarget) _form.target = initialTarget;
						else _form.removeAttribute('target');
					});

				//attach handlers to the deferred
				$.each(_callbackOptions, function(i, optName){
					options[optName] && deferred[optName](options[optName]);
				});

				//make sure the _form gets posted the to iframe
				_form.target = $frame.prop('name');
			});
		}
	});
})(jQuery, document);



$('#form-logo, #form-map').submit(function (e) {
	e.preventDefault();
	//submit through frame
	$(this).frameSubmit({
		done: function (_form, _frame, options) {
			//vanilla dom: form, frame +
			//done, fail, always functions in "options"
			try{
				var json = $.parseJSON( $(_frame).contents().text() );
				if (json.error) return alert("Errore:\n"+json.error);
				//console.log(json)
				switch (_form.id){
					case 'form-logo':
						$('#form-logo label[id^="logo-"]').css('background-image','url("img/logo.png?t='+(new Date()).getTime()+'")');
					break;
					case 'form-map':
						if (json.prop.map){
							$('#map').css({
								backgroundImage : 'url("img/map-1366.'+json.prop.map.ext+'?t='+(new Date()).getTime()+'")',
								height : json.prop.map.height+'px'
							});
							$('#file-map').val('');
						}
						var ext = $('#file-map').val().match(/\.[a-z0-9]+$/i);
						if (ext){
							$('#map').css('background-image','url("img/map-1366'+ext[0]+'?t='+(new Date()).getTime()+'")');
						}
					break;
					default:
				}
			}
			catch(e){
				console.log(e);
				options.fail(_form, _frame, options);
				return false;
			}
			//console.log('done!');
		},
		fail: function (_form, _frame, options) {
			console.log('fail!');
			console.log($(_frame).contents().text());
		},
		always: function (_form, _frame, options) {
			//console.log('always!');
		}
		//custom hasError implementation if needed
		//by default if the frame's body HTML contains the text "unexpected error" or "server error"
		//it is treated as an error
		/*,hasError: function (frame) {
		return false;
		}*/
	});
});


/*function ScrollAnimator(_selectors){
	var $animation_elements = $(_selectors);
	var $window = $(window);
	function check_if_in_view() {
		var window_height = $window.height();
		var window_top_position = $window.scrollTop();
		var window_bottom_position = (window_top_position + window_height);

		$.each($animation_elements, function() {
			var $element = $(this);
			var element_height = $element.outerHeight();
			var element_top_position = $element.offset().top;
			var element_bottom_position = (element_top_position + element_height);

			//check to see if this current container is within viewport
			if ((element_bottom_position >= window_top_position) &&
					(element_top_position <= window_bottom_position)) {
				$element.addClass('in-view');
			} else {
				$element.removeClass('in-view');
			}
		});

		//on dom ready
		if ($animation_elements.length){
			$window.on('scroll resize', check_if_in_view);
			$window.trigger('scroll');
		}
	}
}*/

var $youtubes = $('#youtubes');
var $youtube_editor = $('#yt-bkg');
var $youtube_form = $('#yt-bkg form');
function createPageVideo(obj){
	var page = $('<ul class="choice _x4">')
		var image = $('<li class="center">');
			var img = $('<div class="image" id="article-img-'+obj.pageid+'">');
		var buttons = $('<li class="center">');
			var video = $('<a class="button" id="article-video-'+obj.pageid+'">Video</a>');
		var content = $('<li>');
			var title = $('<h4>').text(obj.title);
			var desc = $('<p>').text(obj.desc);
	//actions
	if (obj.src) img.css('background-image',"url('../../img/thumbs/"+obj.src+"')");
	video.click(function(e){
		e.preventDefault();
		var data = $(this).data("article");
		//update editor properties
		$youtube_form.trigger('reset');
		$youtube_editor.addClass('active');
		$youtube_editor.find('input[name="article_id"]').val(data.pageid);
		$('#s_article_title').text(data.title);
		// video properties
		if (!data.videoid) return false;
		var p = {
			id : data.videoid,
			w : data.videow ? data.videow : 560,
			h : data.videoh ? data.videoh : 315,
			start : data.videostart ? data.videostart : 0,
			end : data.videoend ? data.videoend : ""
		}
		//update values for this video
		$.each(p,function(k,v){
			$('#form-yt input[name=video_'+k+']').val(v).change();
		});
	})
	.data("article",obj);
	if (obj.videoid) page.addClass('hasvideo');
	//create DOM
	image.append(img);
	buttons.append(video);
	content.append(title).append(desc);
	page.append(image).append(buttons).append(content);
	//add to HTML
	$youtubes.append(page);
}


$(function(){
	$('#open-menu').click(function(e){
		e.preventDefault();
		$(this).toggleClass('active');
		$('#menu-mobile').toggleClass('active');
	});
	
	
	$("#map").click(function(e){
		var parentOffset = $(this).offset(); 
		var relX = e.pageX - parentOffset.left;
		var relY = e.pageY - parentOffset.top;
		var w = $(this).width();
		var h = $(this).height();
		var mark_x = Math.ceil(relX-w/2-32);
		var mark_y = Math.ceil(h-relY-16);
		$('#mapmark').css({
			marginLeft:mark_x+'px',
			bottom:mark_y+'px'
		});
		$('#markx').val(mark_x);
		$('#marky').val(mark_y);
	});
	
	
	$('#file-logo').change(function(){
		if (this.value){
			console.log(this.value);
			$('#form-logo').submit();
		}
	})
	
	$('#file-map').change(function(){
		if (this.value){
			console.log(this.value);
			$('#form-map').submit();
		}
	})
	
	
	$('#yt-bkg').click(function(e){
		if (e.target == this){
			$(this).removeClass('active');
			$('#form-yt').trigger('reset');
			$('#form-yt input[type=hidden]').change();
		}
	});
	
	$('#form-yt').submit(function(e){
		e.preventDefault();
		//control inputs:
		var articleid = this.elements[0].value;
		if (!articleid) return false;
		//lock form...
		$.post('php/edit-video.php',$(this).serialize(),null,'json')
			.done(function(json){
				console.log(json);
				if (json.error) return alert("Errore:\n"+json.error);
				//update VIDEO button data.
				//todo
			})
			.fail(function(e){
				console.log(e)
			});
	});
	
	$('#form-yt input[type=hidden]').change(function(){
		//mirror values...
		$('#s_'+this.name).text(this.value);
	}).change();
	
	$('#form-yt textarea').on('change',function(){
		var str = this.value;
		if (!str){
			$('#form-yt').trigger('reset');
			$('#form-yt input[type=hidden]').change();
			return false;
		}
		// video properties
		var p = {
			w : str.match(/width=["'](d+)['"]/i),
			h : str.match(/height=["'](d+)['"]/i),
			id : str.match(/embed\/([0-9a-z_-]+)/i)
		}
		// set/control properties
		if (!p.id){
			p.id = str.match(/youtu.be\/([0-9a-z_-]+)/i);
			if (!p.id)
				return alert("Codice non riconosciuto.\nAssicurati di aver copiato il codice corretto");
		}
		p.id = p.id[1];
		p.w = !p.w ? 560 : p.w[1];
		p.h = !p.h ? 315 : p.h[1];
		//update values for this video
		$.each(p,function(k,v){
			$('#form-yt input[name=video_'+k+']').val(v).change();
		});
	});
});

$(document).ready(function(){
	var scrolltrigger = 0;
	var $window = $(window);
	$window.on('scroll.loadpages',function(){
		if ($window.scrollTop() >= scrolltrigger){
			$window.off('scroll.loadpages');
			$.get('php/get-pages.php',null,null,'json')
				.done(function(json){
					console.log(json);
					if (json.error) return alert("Si è verificato un errore:\n"+json.error);
					$.each(json,function(){
						createPageVideo(this);
					})
				})
				.fail(function(e){
					alert("unknow error");
					console.log(e);
				})
		}
	}).resize(function(){
		scrolltrigger = $(document).height()-$window.height()-100;
	})
	.resize()
	.scroll();
});