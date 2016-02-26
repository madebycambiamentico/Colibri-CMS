/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: popup box multi purpose. Small screen are filled, large screen have margin and can be set max height.
 *
 * @require: [jquery.js >= 1.11.3]
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/
 
 (function(){
	var modalBkg = {
		modals : [],
		opened : 0,
		open : function(){
			if (modalBkg.opened) return false;
			modalBkg.dom.addClass('open');
			$('body').addClass('modalbox-blocked');
		},
		close : function(){
			if (!modalBkg.opened){
				modalBkg.dom.removeClass('open');
				$('body').removeClass('modalbox-blocked');
			}
		},
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
		
	function Modal(elem, options, customCallbacks, iscroller){
		var self = this;
		self.isOpen = false;
		self.O = $.extend({
			maxH : 0
		},options);
		self.on = $.extend({
			open : $.noop,
			close : $.noop,
			resize : $.noop
		},customCallbacks);
		self.iscroll = iscroller;
		
		//custom id for this element -> searchable in modalBkg.modals[...]
		var modalid = modalBkg.modals.length;
		
		self.refresh = function(){
			if (self.iscroll) setTimeout(function () {
				self.iscroll.refresh();
			}, 0);
		}
		
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
		
		self.close = function(){
			if (!self.isOpen) return false;
			$(window).off('resize.mb-'+modalid)
			self.isOpen = false;
			elem.removeClass("open")
			modalBkg.opened--;
			modalBkg.close();
			self.on.close(self);
		}
		
		elem.addClass('ready')
			.append( $('<div class="pop-x">&times;</div>').click(function(){self.close()}) )
	}
	
	/*
	* jquery call method: open/close/toggle, and/or edit options and properties
	*
	* (object|undefined)			opt -> modal options:
	*										(int) maxH >= 0, if 0 box height automatic
	* (object|bool|undefined)	callbacks ->
	*										if (true|undefined): allow dialog toggle.
	*										if (false): modalbox not called
	*										else modal callbacks on...
	*											(function) open
	*											(function) resize
	*											(function) close
	* (bool)							stopscript -> if true dialog box will not toggle
	*/
	$.fn.modalbox = function(opt,callbacks,stopscript){
		return this.each(function(){
			if ('modalbox' in this){
				switch(opt){
					//close
					case 'off': this.modalbox.close(); break;
					//open
					case 'on': this.modalbox.open(); break;
					//toggle
					default:
						if (opt !== undefined){
							//update options in this Modal... before toggle!
							$.extend(this.modalbox.O, opt);
							if (callbacks === false) return true;
						}
						if (callbacks !== undefined){
							//update callbacks in this Modal... before toggle!
							$.extend(this.modalbox.on, callbacks);
						}
						if (stopscript) return true;
						if (this.modalbox.isOpen) this.modalbox.close();
						else this.modalbox.open();
				}
			}
			else{
				this.modalbox = new Modal($(this), opt, callbacks, ('IScroll' in window ? new IScroll(this,{click:true}) : null));
				modalBkg.modals.push(this.modalbox);
			}
		})
	}
})();


//remove this line if you don't want the script to apply standard modal-box
//to every element with .popup-cont class at document ready:
$(function(){ $('.popup-cont').modalbox(); });