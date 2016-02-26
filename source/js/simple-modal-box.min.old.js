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
 * @minified 2016/01/24
**/
!function(){function o(o,e){return 480>=o||480>=e?0:20}function e(e,i,s){var d=this;d.isOpen=!1,d.O=$.extend({maxH:0},i),d.on=$.extend({open:$.noop,close:$.noop,resize:$.noop},s);var t=n.modals.length;d.open=function(){function i(n,i){var s=o(n,i);s&&d.O.maxH&&d.O.maxH<i?(s=(i-d.O.maxH)/2,i=d.O.maxH):i-=2*s;var t=$(window).scrollTop()+s,a=$(document).height();t+i>a&&(t-=t+i-a),e.css({height:i+"px",top:t+"px"}),$(window).scrollTop(t-s),d.on.resize(d)}if(d.isOpen)return!1;d.isOpen=!0,n.open(),n.opened++,e.css({height:0}),e.addClass("open"),i($(window).width(),$(window).height()),d.on.open(d);var s;$(window).on("resize.mb-"+t,function(){clearTimeout(s),s=setTimeout(function(){i($(window).width(),$(window).height())},100)})},d.close=function(){return d.isOpen?($(window).off("resize.mb-"+t),d.isOpen=!1,e.removeClass("open"),n.opened--,n.close(),void d.on.close(d)):!1},e.addClass("ready").append($('<div class="pop-x">&times;</div>').click(function(){d.close()}))}var n={modals:[],opened:0,open:function(){return n.opened?!1:(n.dom.addClass("open"),void $("body").addClass("modalbox-blocked"))},close:function(){n.opened||(n.dom.removeClass("open"),$("body").removeClass("modalbox-blocked"))},closeall:function(){n.opened&&($.each(n.modals,function(){this.close(!1)}),n.dom.removeClass("open"))}};n.dom=$('<div id="popup-bkg">').click(n.closeall).appendTo("body"),$.fn.modalbox=function(o,i,s){return this.each(function(){if("modalbox"in this)switch(o){case"off":this.modalbox.close();break;case"on":this.modalbox.open();break;default:if(void 0!==o&&($.extend(this.modalbox.O,o),i===!1))return!0;if(void 0!==i&&$.extend(this.modalbox.on,i),s)return!0;this.modalbox.isOpen?this.modalbox.close():this.modalbox.open()}else this.modalbox=new e($(this),o,i),n.modals.push(this.modalbox)})}}(),$(function(){$(".popup-cont").modalbox()});