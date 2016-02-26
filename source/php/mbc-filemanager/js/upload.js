/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: fallback for dropzone, ajax upload handler.
 *
 * @require: [jquery.js >= 1.11.3], ./manager.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
 */

var regexpTypeAllowed = (function(){
	var regexp = "\\.(";
	var regs = new Array(MANAGER_OPTIONS.allowedExt.length);
	$.each(MANAGER_OPTIONS.allowedExt,function(i,v){
		regs[i] = v.join("|");
	});
	regexp += regs.join("|") + ")$";
	return new RegExp(regexp,"i");
})();

function onUploadedFiles(json){
	if (json.error !== false){
		alert('ERRORE: '+json.error);
		return false;
	}
	OPENED_FOLDERS[ACTIVE_FOLDER].addFiles(json.done);
}

function formFallback(){
	//correct previous variables...
	myDropzone = {
		files : [],
		removeAllFiles : $.noop
	};
	$("head").append("<link href='js/dropzone-4.2.0/dist/fallback.css' type='text/css' rel='stylesheet'>");
	$.getScript("js/dropzone-4.2.0/dist/min/jquery.form.js")
		.done(function(){
			//initialize plugin
			var status = $('<div class="fallback-res">');
			var progress = $('<div class="fallback-progress">');
			var bar = $('<div class="fallback-bar">');
			var percent = $('<div class="fallback-perc">');
			$('#my-dropzone')
				.append( status )
				.append( progress.append(bar).append(percent) )
				.ajaxForm({
					dataType: 'json',
					beforeSend: function() {
						progress.addClass('uploading');
						status.toggle();
						bar.css('width','0%');
						percent.html('0%');
					},
					uploadProgress: function(event, position, total, percentComplete) {
						var pVel = percentComplete + '%';
						bar.width(pVel);
						percent.text(pVel);
					},
					success: function(responseText) {
						onUploadedFiles(responseText);
						status.html("<p>Upload completato</p>")
					},
					error: function(jQueryObj){
						status.html(jQueryObj.responseText).toggle();
						alert("Ooops!");
					}
				})
		})
		.fail(function(e){
			console.log(e)
		})
}

var myDropzone;
Dropzone.options.myDropzone = {
	paramName: "files[]", // The name that will be used to transfer the file
	maxFilesize: 100, // MB
	//forceFallback : true,//debug IE 8
	fallback : formFallback,
	accept: function(file, done) {
		if (!file.name.match(regexpTypeAllowed)){
			done("Non è possibile caricare questo tipo di file.");
		}
		else
			done();
	},
	dictDefaultMessage : '<p><i class="cloud"></i>Clicca o trascina qui i file da caricare!</p>',
	init: function() {
		myDropzone = this;
		this.on("success", function(file, res){
				onUploadedFiles(res);
			})
			.on("complete", function(file, res){
				//$('#upload-files').modalbox();
			})
			.on("addedfile", function(file) {
				if (!file.type.match(/image.*/)) {
					match = file.name.match(regexpTypeAllowed);
					if (!match){
						console.log(file.name + " is not in allowed types")
						myDropzone.removeFile(file);
					}
					else{
						myDropzone.emit("thumbnail", file, "img/void.png", 'icon i-'+match[1]);
					}
				}
			})
	}
};