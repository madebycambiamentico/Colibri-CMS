/**
* manage popup boxes
*
* popup boxes affected by default have class "popup-cont".
* to create modalbox you need to setup your html with this teplate:
*		<div class="popup-cont" id="#custom_id#">
*			<h4>#custom_title#</h4>
*			<div class="popup">
*				<h5></h5>
*				#custom_content#
*			</div>
*		</div>
* replace #...# with content of your choice
* to add popup functionality to other element use $(<selector>).modalbox();
* edit events:
*		$(element).modalbox(null,{
*			open :   function(){...},
*			close :  function(){...},
*			resize : function(){...}
*		},true);
* open close or toggle a modalbox
*		$(element).modalbox();			// toggle modalbox
*		$(element).modalbox('open');	// open m.b.
*		$(element).modalbox('close');	// close m.b.
* About the default style: small screen are filled, large screen have margin. to customize edit modalbox.css
*
* @require: [jquery.js >= 1.11.3]
*
* @author: nereo costacurta
* @license: GPLv3
* @copyright: (C)2016 nereo costacurta
*/

(function(){
	//dark background + array of enabled modal boxes
	var modalBkg = {
		modals : [],
		opened : 0,
		//show dark background
		open : function(){
			if (modalBkg.opened) return false;
			modalBkg.dom.addClass('open');
			$('body').addClass('modalbox-blocked');
		},
		//hide dark background
		close : function(){
			if (!modalBkg.opened){
				modalBkg.dom.removeClass('open');
				$('body').removeClass('modalbox-blocked');
			}
		},
		//close modalboxes all at once and hide background
		closeall : function(){
			if (modalBkg.opened){
				$.each(modalBkg.modals,function(){
					this.close(false)
				})
				modalBkg.dom.removeClass('open');
			}
		}
	}
	
	modalBkg.dom = $('<div id="popup-bkg">')
		.click(modalBkg.closeall)
		.appendTo('body');
	
	/**
	* Create and store instance of siple modalbox
	*
	* @param (jQuery) elem							The element to attach events.
	* @param (object) options [optional]		Set custom options: 'maxH', 'maxW'. Options can be added from attribute 'data-h' and 'data-w' too.
	*														Provided dimensions should be with valid measure units (%, px, cm...).
	* @param (object) callbacks [optional]		Take 3 params: 'open', 'close', 'resize'. the function passed will be called on that events.
	* @param (IScroll) iscroller [optional]	Add IScroll entity as "iscroll" to be accessed later by code (if provided)
	*/
	function Modal(elem, options, callbacks, iscroller){
		var self = this;
		self.isOpen = false;
		self.O = $.extend({
			maxH : elem.attr('data-h') ? elem.data('data-h') : 0,
			maxW : elem.attr('data-w') ? elem.data('data-w') : 0
		},options);
		self.on = $.extend({
			open : $.noop,
			close : $.noop,
			resize : $.noop
		},callbacks);
		self.iscroll = iscroller;
		
		//custom id for this element -> searchable in modalBkg.modals[...]
		var modalid = modalBkg.modals.length;
		
		//if iScroll installed, refresh scroller if sizes changed or bad behavior happen...
		self.refresh = function(){
			if (self.iscroll) setTimeout(function () {
				self.iscroll.refresh();
			}, 0);
		}
		
		//show modalbox, call on.open()
		self.open = function(){
			if (self.isOpen) return false;
			self.isOpen = true;
			modalBkg.open();
			modalBkg.opened++;
			
			//set initial
			elem.addClass("open");
			self.on.open(self);
			self.refresh();
		}
		
		//hide modalbox, call on.close()
		self.close = function(){
			if (!self.isOpen) return false;
			$(window).off('resize.mb-'+modalid)
			self.isOpen = false;
			elem.removeClass("open")
			modalBkg.opened--;
			modalBkg.close();
			self.on.close(self);
		}
		
		//add max-width and/or max-height to modalbox.
		self.updateRender = function(){
			if (self.O.maxH){
				elem.css('max-height',self.O.maxH)
			}
			if (self.O.maxW){
				elem.css('max-width',self.O.maxW)
			}
		}
		
		
		elem.addClass('ready')
			.append(
				// X button (close)
				$('<div class="pop-x">&times;</div>')
					.click(function(){self.close()})
			);
		//set max height if provided
		self.updateRender();
	}
	
	/**
	* jquery call method: open/close/toggle, and/or edit options and properties
	*
	* @param (string|object) opt [optional]
	*		case 1) modalbox not setup: see Modal param "options"
	*		case 2) modalbox already setup: accept "open" (force open modalbox), "close" (force close modalbox),
	*					object to extend Modal param options (deprecated).
	* @param (object) callbacks [optional]
	*		case 1) modalbox not setup: see Modal param "callbacks"
	*		case 2) modalbox already setup: accept 3 functions: 'open', 'close', 'resize'.
	*					the function passed will be called on that events.
	* @param (bool) stopscript [optional]
	*		case 1) modalbox not setup: ignored.
	*		case 2) modalbox already setup:
	*					- if true no action to the modalbox will be taken.
	*					- (default) else the modalbox will be toggled (opened/closed)
	*
	* @return (jQuery) the jquery elements passed
	*/
	$.fn.modalbox = function(opt,callbacks,stopscript){
		return this.each(function(){
			if ('modalbox' in this){
				var mb = this.modalbox;
				switch(opt){
					//close
					case 'close': case 'off':
						mb.close();
					break;
					//open
					case 'open': case 'on':
						mb.open();
					break;
					//toggle
					default:
						//update options in this Modal... before toggle!
						if (opt){
							$.extend(mb.O, opt);
							mb.updateRender();
						}
						
						//update callbacks in this Modal... before toggle!
						if (callbacks){
							$.extend(mb.on, callbacks);
						}
						
						//toggle allowed?
						if (stopscript) return true;
						
						//toggle
						if (mb.isOpen)
							mb.close();
						else
							mb.open();
				}
			}
			else{
				var mb = this.modalbox = new Modal(
					$(this),
					opt,
					callbacks,
					('IScroll' in window ? new IScroll(this,{click:true}) : null)
				);
				modalBkg.modals.push(mb);
			}
		})
	}
})();


//remove this line if you don't want the script to apply standard modal-box
//to every element with .popup-cont class at document ready:
$(function(){ $('.popup-cont').modalbox(); });