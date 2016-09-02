var CommentsStore = (function(win){
'use strict';

if (!('console' in win)){
	console = {info:$.noop, log:$.noop, warn:$.noop, error:$.noop};
}

var INSTALL_DIR = '';
var PLUGIN_DIR = 'plugin/Colibri/Comments/';
var PAGE_ID = 0;

//replace " and ' with html entities
function printAttr(s){
	return s ? s.replace(/"/g,"&quot;").replace(/'/g,"&#039;") : '';
}

//replace < and > with html entities
function printText(s){
	return s ? s.replace(/</g,"&lt;").replace(/>/g,'&gt;') : '';
}

$.cachedScript = function( url, options ) {
	// Allow user to set any option except for dataType, cache, and url
	options = $.extend( options || {}, {
		dataType: "script",
			cache: true,
			url: url
		}
	);
	// Use $.ajax() since it is more flexible than $.getScript
	// Return the jqXHR object so we can chain callbacks
	return $.ajax( options );
};





/**
* store a comment class.
*
* You need to manually place $box in #colibri-comments
*
* @see /js/simple-modal-box.js
*
* @param (int)    _commentID Comment id (database index)
* @param (object) _author    Contains comment author properties: 'id', 'name', 'hasimage', 'website'
* @param (string) _date      The formatted date of posted comment
* @param (string) _markdown  The markdown string stored in database (comment content).
* @param (bool)   _haschild  Tell if comment has children (replies)
* @param (bool)   _hasnext   Tell if next to this comment there's another one
* @param (jquery) _modalbox  Popup element to display for commenting
*
* @return (object) Self
*/

var comment_n = 0;				//comment counter
var CommentsStore = {};		//contains { comment ID (database) => comment n. }

function ColibriComment(_commentID, _author, _date, _markdown, _haschild, _hasnext, _modalbox){
	var self = this;
		self.$box = null;
		self.$comment = null;
		self.$replies = null;
		self.id = false;
	
	var haschild = !!_haschild;
	
	
	/**
	* produce comment html from passed properties, and store in jquery variables this.$box|$comment|$replies
	* $box is the main box container (contains $comment and $replies)
	*
	* @param SEE FUNCTION DESCRIPTION
	*/
	function initHTML(_commentID, _author, _date, _markdown, _haschild, _hasnext, _isCommentAllowed){
		
		self.$comment = $( '<div class="cmt-comment'+(_hasnext ? ' cmt-hasnext' : '')+(_haschild ? ' cmt-haschild' : '')+'">'+
				'<div class="cmt-lines">'+
					'<a name="cc'+_commentID+'"></a>'+
					'<p class="cmt-toparent"></p>'+
					'<p class="cmt-tochild"></p>'+
					'<p class="cmt-toprofile"></p>'+
					'<p class="cmt-circle" title="#'+_commentID+'"></p>'+
					'<img src="'+INSTALL_DIR+'img/users/'+(_author.id && _author.hasimage ? _author.id : 'default')+'/face-32.png">'+
				'</div>'+
				'<a href="#cc'+_commentID+'" class="cmt-author">'+printText(_author.name)+'</a>'+
					' <i class="cmt-date">('+_date+')</i>: '+
					//markdownToHtml.makeHtml(_markdown)+
					_markdown +
					(_author.website ? '<a href="'+printAttr(_author.website)+' target="_blank" class="cmt-site">website</a>' : '')+
					(_isCommentAllowed ? '<a class="cmt-reply">Reply</a>' : '')+
				'<hr>'+
			'</div>'
		)//.not('script'); //will strip <script> ?
		
		self.$replies = $( '<div class="cmt-boxes"><p class="cmt-link"></p></div>');
		
		self.$box = $( '<div class="cmt-box" id="-cmt-'+_commentID+'"></div>' )
			.append(self.$comment)
			.append(self.$replies);
	}
	
	
	/**
	* initialize jquery objects to be putted in html, add functions, store instance in global variable
	*
	* @param SEE FUNCTION DESCRIPTION
	*
	* @return (object) Self istance.
	*/
	self.init = function(_commentID, _author, _date, _markdown, _haschild, _hasnext, _modalbox){
		//control input
		if (false !== self.id || !_commentID || !_author || !_date || !_markdown){
			return false;
		}
		
		//set new id
		self.id = comment_n++;
		//CommentsStore is available in window globals.
		CommentsStore[_commentID] = self;
		
		//main comment container
		initHTML(_commentID, _author, _date, _markdown, _haschild, _hasnext, _modalbox.length);
		
		//toggle children comments
		haschild = _haschild;
		self.$box.find('.cmt-circle').click(function(){
			if (haschild){ self.$box.toggleClass('open').delay(1).dclass(2,'fancy'); }
		});
		
		//reply function (if allowed)
		if (_modalbox.length){
			self.$box.find('.cmt-reply').click(function(e){
				e.preventDefault();
				_modalbox.modalbox('open')
				$('#CC_COMMENT_ID').val(_commentID);
				//clear form...
				if ('TINY' in win) tinyEditor.clear();
			});
		}
		
		return self;
	}
	
	
	/**
	* when istance already created, edit class if this has new childs/brothers
	* use this.update.hasChild(...) or this.update.hasNext(...)
	*
	* @param (bool) has Tell if this comment has or not a child/brother. default: true.
	*
	* @return (object) Self
	*/
	self.update = {
		
		hasNext: function(has){
			//this has new next comments
			if (has===false)
				self.$comment.removeClass('cmt-hasnext');
			else
				self.$comment.addClass('cmt-hasnext');
			return self;
		},
		
		hasChild :function(has){
			//this has new child comments
			if (haschild == has) return self;
			if (has===false){
				haschild = has;
				self.$comment.removeClass('cmt-haschild');
			}
			else{
				haschild = true;
				self.$comment.addClass('cmt-haschild');
			}
			return self;
		}
	};
	
	
	/**
	* add comment replies (see ColibriComment.$box) to ColibriComment.$replies container
	*
	* @param (array) aReplies Array of jQuery object to add to replies container.
	*
	* @return (object) Self
	*/
	self.addReplies = function(aReplies,doUpdateNext){
		//control input...
		if (!aReplies.length) return self;
		//update last children class to "hasnext" (if exists), since we add a child
		if (doUpdateNext){
			var x = self.$replies.children('.cmt-box:last-child');
			if (x.length){
				x = x[0].id.replace('-cmt-','');
				CommentsStore[x].update.hasNext();
			}
		}
		//add element $box to replies. you can pass a jQuery element, or a ColibriComment instance.
		for (var i=0; i<aReplies.length; i++){
			if (aReplies[i] instanceof ColibriComment){
				self.$replies.append(aReplies[i].$box);
			}
			else{
				//jquery object (or text) expected
				self.$replies.append(aReplies[i]);
			}
		}
		//update this class to "haschild"
		return self.update.hasChild();
	}
	
	
	return self.init(
		_commentID,
		_author,
		_date,
		_markdown,
		_haschild,
		_hasnext,
		_modalbox
	);
}



/**
* initialize parser Showdown
* transform Markdown to HTML
* use: converter = new showdown.Converter(<options>);
*      converter.makeHtml('#hello, markdown!'); //returns "<h1 id="hellomarkdown">hello, markdown!</h1>"
*/
/*/var markdownToHtml = new showdown.Converter({
	noHeaderId : true,
	headerLevelStart : 4
});//*/


/**
* initialize parser toMarkdown
*
* transform HTML to Markdown
* use: htmlToMarkdown.makeMD('<h1>Hello world!</h1>'); //returns "#hello, markdown!"
*/
/*/var htmlToMarkdown = {
	makeMD: function(html){
		if ('toMarkdown' in win)
			return toMarkdown(html);
		else{
			alert("toMarkdown plugin hasn't loaded yet, please wait a few seconds");
			return false;
		}
	}
};//*/


function tinyEditorSetup(){
	$('#CC_COMMENT').removeClass('no-support');
	new TINY.editor.edit('tinyEditor',{											// (required) variable name linking the new created editor entity
		id:'CC_COMMENT',																// (required) ID of the textarea
		controls:['bold','italic','|',											// (required) options you want available, a '|' represents a divider and an 'n' represents a new row
					'orderedlist','unorderedlist','|',
					'unformat','|','undo','redo'],
		footer:true,																	// (optional) show the footer
		xhtml:false,																	// (optional) generate XHTML vs HTML
		toggle:{																			// (optional) toggle to markup view options
			text:'show source',
			activetext:'show wysiwyg',
			cssclass:'toggle'
		},
		resize:{cssclass:'resize'}													// (optional) display options for the editor resize
	});
}


/**
* add the possibility to delay come css styles.
* call with $(<elem>).delay(<ms>).dcss({<prop>:<value>})
*/
$.fn.extend({
	dclass: function(add,classes) {
		return $(this).queue(function(next) {
			switch(add){
				case 0: $(this).removeClass(classes); break;
				case 1: $(this).addClass(classes); break;
				default: $(this).toggleClass(classes);
			}
			next();
		});
	},
});




$(function(){
	
	//set constants url paths
	INSTALL_DIR = $('#CC_PATH').val();
	PLUGIN_DIR = INSTALL_DIR+PLUGIN_DIR;
	
	//set page id
	PAGE_ID = $('#CC_PAGE_ID').val();
	
	if (!PAGE_ID || PAGE_ID=='0') return false;
	
	//set modalbox
	var $modalbox = $('#colibri-comment-popup');
	var $modalinfo = $('#colibri-comment-info-popup');
	var $main = $('#colibri-comments');
	var $all = $('#cmt-all');
	
	//colibrì logo button
	$('.cmt-btn-info').click(function(){
		$modalinfo.modalbox();
	});
	
	
	
	if ($modalbox.length){
		
		//open comment form
		$('#cmt-all>.cmt-plus').click(function(){
			$modalbox.modalbox('open');
			$('#CC_COMMENT_ID').val('');
			//clear form...
			if ('TINY' in win) tinyEditor.clear();
		});
		
		//load html to markup plugin and tinyeditor (if supported)
		if ('execCommand' in document){
			$modalbox.modalbox(
				//edit aspect option:
				null,
				//add callbacks
				{
					open: function(mb){
						$modalbox.addClass('loading');
						//how many script to load?
						var counter = 1//2;
						
						//stop Loading animation only after 2 seconds.
						var timer = new Date().getTime();
						function stoploading(counter){
							if (!counter){
								timer = new Date().getTime() - timer;
								if (timer < 2000)
									setTimeout(function(){ $modalbox.removeClass('loading'); }, 2000-timer);
								else
									$modalbox.removeClass('loading');
								console.info("Addon plugins loaded in "+timer+"ms");
							}
						}
						
						/*/ 1) html to markdown
						console.info("Loading toMarkup plugin...");
						$.cachedScript(PLUGIN_DIR+'js/to-markdown/to-markdown.min.js')
							.done(function(){
								console.info("toMarkup plugin loaded.");
							})
							.fail(function(e){
								alert("Ooops!");
								console.warn(e);
							})
							.always(function(){
								stoploading(--counter);
							});//*/
						
						// 2) tinyeditor
						console.info("Loading TinyEditor plugin...");
						$.cachedScript(PLUGIN_DIR+'TinyEditor/tinyeditor.colibri.min.js?v=2')
							.done(function(){
								console.info("TinyEditor plugin loaded.");
								//initialize TinyEditor
								tinyEditorSetup();
							})
							.fail(function(e){
								alert("Ooops!");
								console.warn(e);
							})
							.always(function(){
								stoploading(--counter);
							});
						
						//load only once
						mb.on.open = $.noop;
					}
				},
				//prevent open/toggle in this call
				true
			)
			.append('<div class="cmt-loader"></div>');
		}
		
		//form on submit
		$('#colibri-comment-popup form').submit(function(e){
			e.preventDefault();
			//control inputs...
			// (1) url to post to.
			var url = this.action;
			if (!url) return false;
			// (2) content of the comment
			var test;
			if ('TINY' in win){
				/*test = tinyEditor.post();//update textarea content;
				test = $('#CC_COMMENT').val( htmlToMarkdown.makeMD(test).trim() ).val();*/
				test = tinyEditor.post().trim();
			}
			else{
				test = $('#CC_COMMENT').val().trim();
			}
			if (!test || test.length < 10){
				alert("Il tuo commento è troppo corto!");
				return false;
			}
			if ($('#CC_LOGGED').val() !== '1'){
				// (3) name
				test = $('#CC_NAME').val();
				if (!test || test.length < 4){
					alert("Il tuo nome è troppo corto!");
					return false;
				}
				// (4) email
				test = $('#CC_EMAIL').val();
				if (!test || test.length < 4 || !/^(?=.{1,254}$)(?=.{1,64}@)[-!#$%&'*+\/0-9=?A-Z^_`a-z{|}~]+(\.[-!#$%&'*+\/0-9=?A-Z^_`a-z{|}~]+)*@[A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?(\.[A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*$/.test(test)){
					alert("L'email non è valida.");
					return false;
				}
			}
			var parentCID = $('#CC_COMMENT_ID').val();
			
			//start loader...
			$modalbox.addClass('loading');
			
			//add new post
			$.post(url,$(this).serialize(),null,'json')
				.done(function(json){
					if (json.error)
						return alert("Errore\n"+json.error);
					var cmt = json.comment;
					var newCC = new ColibriComment(
						cmt.cid,	//comment id in database
						cmt.a,		//author properties: 'id', 'name', 'hasimage', 'website'
						cmt.d,		//date
						cmt.c,		//comment markdown
						false,		//has child comments?
						false,		//has next comments?
						$modalbox
					);
					//TODO
					//update previous comment (if exists) to link down the new one
					if (!!parentCID && (parentCID in CommentsStore)){
						CommentsStore[parentCID].addReplies([ newCC.$box ], true);
					}
					else{
						$('#cmt-all > .cmt-box:last-child > .cmt-comment').addClass('cmt-hasnext');
						//add single comment to main container
						$all.append(newCC.$box);
					}
					//remove class empty...
					alert("Commento aggiunto!");
					$all.removeClass('empty');
					$modalbox.modalbox('close');
				})
				.fail(function(e){
					alert("Ooops!");
					console.warn(e);
				})
				.always(function(res){
					//end loader...
					$modalbox.removeClass('loading');
				})
		})
	}
	else{
		//hide buttons
		//open comment form
		$all.addClass('no-reply');
	}
	
	
	//load comments (with lazy load?)
	function loadComments(){
		console.info('Loading Colibrì Comments (c)...');
		$main.addClass('loading');
		
		//stop Loading animation only after 2 seconds.
		var timer = new Date().getTime();
		function stoploading(){
			timer = new Date().getTime() - timer;
			if (timer < 2000)
				setTimeout(function(){ $main.removeClass('loading'); }, 2000-timer);
			else
				$main.removeClass('loading');
			console.info("Colibrì Comments (c) loaded ["+timer+"ms].");
		}
		
		
		
		//******************************************************************
		var comments = [];
		function arrangeComments(parentCC, ichild, donexts){
			var si = ichild;
			//this function will link comments as a matrioska
			if (!comments[ichild]){
				return false;
			}
			var cmt = comments[ichild];
			var thisCC = new ColibriComment(
				cmt.cid,		//comment id in database
				cmt.a,		//author properties: 'id', 'name', 'hasimage', 'website'
				cmt.d,		//date
				cmt.c,		//comment markdown
				cmt.cld,		//has child comments?
				cmt.nxt,		//has next comments?
				$modalbox
			);
			//go to next element
			ichild++;
			
			//if this comment has children comments: add them to this $replies
			if (cmt.cld){
				var res = arrangeComments(thisCC, ichild, true);
				ichild = res.i;
			}
			
			//if this comment has siblings comments: add them to parent $replies
			var replies = [ thisCC.$box ];
			if (donexts){
				if (cmt.nxt){
					var matrioska_n = cmt.o.length;
					while(
						ichild<comments.length &&
						matrioska_n <= comments[ichild].o.length
					){
						var res = arrangeComments(parentCC, ichild, false);
						replies.push( res.cc.$box );
						ichild = res.i;
					};
				}
			}
			
			//update parent $replies:
			if (parentCC instanceof ColibriComment){
				parentCC.addReplies( replies );
			}
			//or add comment to main container
			else if (0===si){
				for (var i=0; i<replies.length; i++)
					parentCC.append(replies[i]);
			}
			
			//return current index and current ColibriComment instance.
			return {
				i : ichild,
				cc : thisCC
			}
		}
		//******************************************************************
		
		
		
		$.get(PLUGIN_DIR+'template/get-comments.php?', {pageid:PAGE_ID}, null, 'json' )
			.done(function(json){
				if (json.error)
					return alert("Errore\n"+json.error);
				//add all plugins...
				comments = json.comments; //shorthand
				
				var haschild = false, hasnext = false;
				for (var i=0;i<comments.length; i++){
					//find if has child is simple:
					haschild = false;
					if (comments[i+1] && comments[i].o.length < comments[i+1].o.length) haschild=true;
					//find if has siblings is a bit more tricky
					hasnext = false;
					for (var j=i+1; j<comments.length; j++){
						//next item has higher level than this: this haven't siblings.
						if (comments[i].o.length > comments[j].o.length){
							break;
						}
						//found an item that has same level as this (and no higher levels are in between)
						if (comments[i].o.length == comments[j].o.length){
							hasnext = true;
							break;
						}
					}
					//update element...
					comments[i].cld = haschild;
					comments[i].nxt = hasnext;
				}
				arrangeComments($all, 0, true);
				//remove empty if at least 1 comment
				if (json.comments.length)
					$all.removeClass('empty');
			})
			.fail(function(e){
				alert("Ooops!");
				console.warn(e);
			})
			.always(function(res){
				//end loader...
				stoploading();
			});
		
		//remove window listeners
		stopLazyPluginLoader(true);
		//do only once
		loadComments = $.noop;
	}
	
	
	
	//------------------------ START lazy comments loader
	var isLazyPluginLoaderStarted = false;
	function startLazyPluginLoader(){
		if (isLazyPluginLoaderStarted || 'off' === isLazyPluginLoaderStarted)
			return false;
		isLazyPluginLoaderStarted = true;
		function isInWindow(elem){
			var rect = elem.getBoundingClientRect();
			return (
				(rect.top<=50 && rect.bottom>=win.innerHeight-50) ||		//elemento che copre tutta l'area attiva
				(rect.top>=50 && rect.top<=win.innerHeight-50) ||			//top dell'elemento tra +25 e wh-25
				(rect.bottom>50 && rect.bottom<win.innerHeight-50)			//bottom dell'elemento tra +25 e wh-25
			);
		}
		$(win).on('scroll.ccmt resize.ccmt', function(){
			if (isInWindow($all[0])) loadComments();
		}).trigger('resize.ccmt');
	}
	function stopLazyPluginLoader(forever){
		$(win).off('scroll.ccmt resize.ccmt');
		isLazyPluginLoaderStarted = forever ? 'off' : false;
	}
	//------------------------ END lazy comments loader
	
	
	
	function setCookieInputs(_v){
		var vs = '#colibri-comment-info-popup input[name=CC_SHOW]';
		function update(v, reflect){
			console.info("Update Colibrì Comments preference to "+v)
			switch(v){
				case '0':
					$all.removeClass('closed').delay(5).dclass(1,'fancy');
					//re-start lazyload?
					startLazyPluginLoader();
				break;
				case '1':
					$all.addClass('closed').removeClass('fancy');
					//re-start lazyload?
					startLazyPluginLoader();
				break;
				case '2':
					$all.addClass('closed').removeClass('fancy');
					//stop lazyload?
					stopLazyPluginLoader();
				break;
			}
			
			//update check
			if (reflect){
				$(vs+'[value='+v+']').prop('checked',true);
			}
			return v;
		}
		var v = $(vs)
			.change(function(){// cookie for 7 days
				docCookies.setItem('ccmt',
					update(this.value,false),
					86400*7,
					'/',
					null,
					null
				);
			})
			.filter(':checked').val();
		//first time:
		if (_v){
			update(_v, true);
		}
		else{
			update('0', false);
		}
	}
	
	if (!('docCookies' in win)){
		console.info("Loading Cookie plugin...");
		$.cachedScript(PLUGIN_DIR+'js/cookie.min.js')
			.done(function(){
				console.info("Cookie plugin loaded");
				/*******************
				START COMMENT PLUGIN
				********************/
				setCookieInputs(docCookies.getItem('ccmt'));
			})
			.fail(function(e){
				alert("Ooops!");
				console.warn(e);
			});
	}
	else{
		/*******************
		START COMMENT PLUGIN
		********************/
		setCookieInputs(docCookies.getItem('ccmt'));
	}
});

return CommentsStore;

})(window);