(function(){

//------------------------ START lazy plugin info loader
//uses isInWindow() from common.js
var ACTIVE_BOXES = [];
function checkLoadPlugin(){
	if (ACTIVE_BOXES.length){
		purge = 0;
		ACTIVE_BOXES.each(function(){
			if (isInWindow(this)){
				this.pluginBox.updateInfo();
				this.pluginBox.$box.removeClass('info-loading');
				purge++
			}
		})
		if (purge) ACTIVE_BOXES = $('#all-plugins .info-loading');
	};
}
//------------------------ END lazy plugin info loader


var pluginBoxes = [];

function pluginSetInfoPop(props, logo){
	$('#pl-info-title').text(props.title);
	$('#pl-info-logo').css('background-image',logo);
	$('#pl-info-author').text(props.author);
	$('#pl-info-version').text(props.version);
	$('#pl-info-desc').html(props.require);
}

function pluginBox(_box,_modalbox,_modalboxiframe){
	var self = this;
	var infoloaded = false,
		installed = false,
		active = false,
		custom = null,
		working = false,
		$mb = null,
		$mbi = null,
		id = null;
	
	self.$box = null;
	self.$info = null;
	self.$loader = null;
	self.plugin = null;
	
	self.init = function($box, $modalbox, $modalbox_iframe){
		if (!$box || !$modalbox) return false;
		if (self.$box) return false;//do only once
		$box[0].pluginBox = self;
		self.$box = $box;
		self.$info = $box.find(".info");
		$mb = $modalbox ? $modalbox : null;
		$mbi = $modalbox_iframe ? $modalbox_iframe : null;
		haslogo = $box.find(".logo").css
		//plugin status
		installed	= ! $box.hasClass('install');
		active		= $box.hasClass('active');
		custom		= (function(){
			var $custom = $box.find('.plugin-custom');
			if (!$custom.length) return null;
			return $custom
				.click(function(e){
					e.preventDefault();
					self.openCustom();
				})[0].href;
		})();
		//--- installation
		var plug_url =
		$box
			.find('.plugin-install')
			.click(function(e){
				e.preventDefault();
				//start installation [or uninstall/repair(?)]
				self.install();
			})
			.data('plugin');
		//--- activation
		$box
			.find('.plugin-activate')
			.click(function(e){
				e.preventDefault();
				//start activation/deactivation
				self.activate();
			});
		//--- info
		//set minimal info for this plugin
		self.plugin = {
			url : plug_url
		}
		plug_url = plug_url.match(/\/([a-z0-9]+)\/([a-z0-9]+)\/$/i);
		self.plugin.author = plug_url[1];
		self.plugin.title = plug_url[2];
		self.plugin.idx = plug_url[1]+'/'+plug_url[2];
		//info opener...
		$box
			.find('.plugin-info input')
			.change(self.openInfo);
		//--- hint when working
		self.$loader = $box.find('.loader p');
		return self;
	}
	
	var busy = {
		start: function(what){
			if (!what) what = "occupato...";
			what = self.plugin.author+" "+self.plugin.title + " " + what;
			console.log(what);
			working = true;
			self.$loader.html(what);
			self.$box.addClass('loading');
		},
		end: function(){
			working = false;
			self.$box.removeClass('loading');
		}
	}
	
	function setInstalled(){
		console.log("completing (de)installation for plugin "+self.plugin.author+" > "+self.plugin.title);
		$.post('plugin/toggle-install.php', {p : self.plugin.idx}, null, 'json')
			.done(function(json){
				if (json.error)
					return alert("Errore\n"+json.error);
				installed = json.installed;
				console.log("plugin "+self.plugin.author+" > "+self.plugin.title+" has been "+(installed ? '' : 'un-')+"installed.");
				//toggle css class
				if (installed)
					self.$box.removeClass('install');
				else
					self.$box.addClass('install');
			})
			.fail(function(e){
				console.log(e);
				alert("Ooops!");
			})
			.always(function(){
				busy.end();
			});
	}
	
	self.install = function(){
		if (!self.plugin || working) return false;
		busy.start("installazione in corso");
		$.post(self.plugin.url+'installer.php', {install : !installed}, null, 'json')
			.done(function(json){
				if (json.error){
					busy.end();
					return alert("Errore\n"+json.error);
				}
				setInstalled();
			})
			.fail(function(e){
				console.log(e);
				busy.end();
				alert("Ooops!");
			})
	}
	
	self.activate = function(){
		if (!installed || !self.plugin || working) return false;
		busy.start("attivazione in corso");
		$.post('plugin/toggle-active.php', {p : self.plugin.idx}, null, 'json')
			.done(function(json){
				if (json.error)
					return alert("Errore\n"+json.error);
				active = json.active;
				console.log("plugin "+self.plugin.author+" > "+self.plugin.title+" has been "+(active ? '' : 'de-')+"activated.");
				//toggle css class
				if (active)
					self.$box.addClass('active');
				else
					self.$box.removeClass('active');
			})
			.fail(function(e){
				console.log(e);
				alert("Ooops!");
			})
			.always(function(){
				busy.end();
			});
	}
	
	function printInfoBox(){
		self.$info.html(
			'<h4>'+self.plugin.title+' di <i>'+self.plugin.author+'</i></h4>'+
			'<p class="version" data-version="'+printAttr(self.plugin.version)+'"><code>v.'+printText(self.plugin.version)+'</code></p>'
		);
	}
	
	self.updateInfo = function(callback,fallback){
		if (!self.plugin || working) return false;
		busy.start("ricerca info");
		$.get(self.plugin.url+'director.json', null, null, 'json')
			.done(function(json){
				self.plugin = $.extend( self.plugin, json );
				infoloaded = true;
				printInfoBox();
				callback && callback();
			})
			.fail(function(e){
				console.log(e);
				alert("Ooops!");
				fallback && fallback();
			})
			.always(function(){
				busy.end();
			});
	}
	
	function showInfo(){
		if (!$mb) return false;
		//build info popup:
		pluginSetInfoPop(self.plugin, self.$box.find('.logo').css('background-image'))
		$mb.modalbox('open');
	}
	
	self.openInfo = function(){
		if (!self.plugin || working) return false;
		if (!infoloaded)
			self.updateInfo(showInfo);
		else
			showInfo();
		return self;
	}
	
	function showCustom(){
		if (!$mbi) return false;
		var ifr = $mbi.find('iframe')[0];
		if (ifr.src !== custom) ifr.src = custom;
		$mbi.modalbox('open');
	}
	
	self.openCustom = function(){
		if (!custom || working) return false;
		showCustom();
		return self;
	}
	
	//construct initializer
	if (_box){
		self.init(_box, _modalbox, _modalboxiframe);
	}
	//add box to boxes array, meanwhile set box id
	//the box has id "-box-<ID>", where ID is the index in PluginBoxes, and is stored in self.id
	self.id = (pluginBoxes.push(self) -1);
	self.$box[0].id = '-box-'+self.id;
	
	return self;
}

$.fn.pluginBox = function(){
	var $info_mb = $('#plugin-info');
	var $cust_mb = $('#plugin-custom-page');
	return this.each(function(){
		new pluginBox($(this), $info_mb, $cust_mb);
	});
}

$(function(){
	$('#all-plugins .plugin-box').pluginBox();
	
	//lazy load of plugin info
	ACTIVE_BOXES = $('#all-plugins .info-loading');
	$(this).on('scroll.lazy resize.lazy',checkLoadPlugin);
	checkLoadPlugin();
});
})();