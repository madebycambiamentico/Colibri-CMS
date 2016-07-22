/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: personal profile editor (image, password, email).
 *
 * @require: [jquery.js >= 1.11.3], common.js, simple-modal-box.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

(function(){
	
	var UPLOADING = false;
	
	function onUploadedImage(json){
		if (json.image)
			$('#pf-image').css('background-image','url("img/users/'+printAttr(json.image)+'256.png?'+(new Date).getTime()+'")');
		else
			$('#pf-image').css('background-image','url("img/users/default/face-256.png")');
		//close modalbox
		$('#upload-files').modalbox('off');
	}
	
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
				if (UPLOADING) return false;
				UPLOADING = true;
				progress.addClass('uploading');
				status.hide();
				bar.css('width','0%');
				percent.html('0%');
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var pVel = percentComplete + '%';
				bar.width(pVel);
				percent.text(pVel);
			},
			success: function(json) {
				//console.log(json);
				if (json.error !== false) return alert("ERRORE\n"+json.error);
				onUploadedImage(json);
				UPLOADING = false;
			},
			error: function(jQueryObj){
				//console.log(jQueryObj);
				status.html(jQueryObj.responseText).show();
				alert("Ooops!");
				UPLOADING = false;
			}
		});
	$('#ui-file').change(function(){
		var filename = $(this).val();
		if (filename) $('#onupload').text(filename);
		else $('#onupload').text('nessun file selezionato');
	});


	function checkPass(onsuccess){
		//control password
		var passold = $('#my-password').val().trim();
		var passnew = $('#my-password-n').val().trim();
		if (passnew.length<8){
			alert("La password è troppo corta!");
			return false;
		}
		var passrep = $('#my-password-nr').val().trim();
		if (passnew != passrep){
			alert("La password è troppo corta!");
			return false;
		}
		//generate hashed password
		//old
		var shaObj = new jsSHA("SHA-512", 'TEXT');
		shaObj.update(passold);
		var p0 = shaObj.getHash("HEX");
		$('#my-hashed-pass').val( p0 ); //128 CHAR
		//new
		shaObj = new jsSHA("SHA-512", 'TEXT');
		shaObj.update(passnew);
		var p1 = shaObj.getHash("HEX");
		$('#my-hashed-pass-new').val( shaObj.getHash("HEX") ); //128 CHAR
		//callback on success
		if ($.isFunction(onsuccess)) onsuccess(p0,p1);
		return true;
	}

	var PW;
	function PasswordMeter(){
		var self = this;
		self.scorePassword = function(pass) {
			var score = 0;
			if (!pass)
				return score;
			// award every unique letter until 5 repetitions
			var letters = new Object();
			for (var i=0; i<pass.length; i++) {
				letters[pass[i]] = (letters[pass[i]] || 0) + 1;
				score += 5.0 / letters[pass[i]];
			}
			// bonus points for mixing it up
			var variations = {
				digits: /\d/.test(pass),
				lower: /[a-z]/.test(pass),
				upper: /[A-Z]/.test(pass),
				nonWords: /\W/.test(pass),
			}
			variationCount = 0;
			for (var check in variations) {
				variationCount += (variations[check] == true) ? 1 : 0;
			}
			score += (variationCount - 1) * 10;
			return parseInt(score);
		}
		
		self.textPassStrength = function(score) {
			if (score > 80)
				return "forte";
			if (score > 60)
				return "buona";
			if (score >= 30)
				return "debole";
			if (score == 0)
				return '';
			return "debolissima";
		}
		
		var percentColors = [
			{ pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
			{ pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
			{ pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } }
		];

		self.getColorForPercentage = function(pct) {
			if (!pct) return 'inherit';
			for (var i = 1; i < percentColors.length - 1; i++) {
				if (pct < percentColors[i].pct) {
					break;
				}
			}
			var lower = percentColors[i - 1];
			var upper = percentColors[i];
			var range = upper.pct - lower.pct;
			var rangePct = (pct - lower.pct) / range;
			var pctLower = 1 - rangePct;
			var pctUpper = rangePct;
			var color = {
				r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
				g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
				b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
			};
			return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
			// or output as hex if preferred
		}
		
		self.input = null;
		self.meter = null;
		self.initMeter = function($input, $meter){
			self.input = $input;
			self.meter = $meter;
			$input.keyup(function(){
				var psw = $(this).val();
				var score = Math.min(self.scorePassword(psw),100);
				var color = self.getColorForPercentage(score/100);
				$meter
					.css({
						backgroundColor : color,
						width : score+'%'
					})
					.text( self.textPassStrength(score) );
			})
			return self;
		}
		
		return self;
	}

	function savePass(p0,p1){
		//console.log('---- savePass ----');
		BUSY.start();
		$.post('database/user-edit.php',{action:'pass', p0: p0, p1: p1, hint: $('#my-password-hint').val()},null,'json')
			.done(function(json){
				//console.log(json);
				if (json.error) return alert("ERRORE:\n"+json.error);
				alert("Password modificata correttamente");
				//clear inputs...
				$('#all-pass-inputs input').val('');
				PW.input.keyup();
			})
			.fail(function(e){
				console.log(e);
				alert("Oooops!");
			})
			.always(function(){
				//unlock form...
				BUSY.end();
			});
	}

	function deleteImage(){
		//console.log('---- deleteImage ----');
		if (!confirm("Sei sicuro di voler rimuovere la tua immagine?\nVerrà sostituita dal profilo standard.")) return false;
		BUSY.start();
		$.get('database/user-image.php',{remove:true},null,'json')
			.done(function(json){
				//console.log(json);
				if (json.error) return alert("ERRORE:\n"+json.error);
				onUploadedImage(json)
			})
			.fail(function(e){
				console.log(e);
				alert("Oooops!");
			})
			.always(function(){
				//unlock form...
				BUSY.end();
			});
	}

	function checkEmail(onsuccess){
		var eold = $('#my-email').val();
		var enew = $('#my-email-new').val();
		//simply check for validity
		if (enew !== ''){
			if (!/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i.test(enew))
				if (!confirm("ATTENZIONE!!!\nNon sembra una mail quella che hai appena inserito... procedere comunque?"))
					return false;
		}
		else if (!confirm("ATTENZIONE!!!\nNon hai inserito la nuova email: in questo modo verrà rimossa definitivamente\ne non potrai più essere contattato o recuperare i tuoi dati. Procedere comunque?"))
			return false;
		//callback on success
		if ($.isFunction(onsuccess)) onsuccess(eold,enew);
		return true;
	}

	function saveEmail(e0,e1){
		//console.log('---- saveEmail ----');
		BUSY.start();
		$.post('database/user-edit.php',{action:'email', e0: e0, e1: e1},null,'json')
			.done(function(json){
				//console.log(json);
				if (json.error) return alert("ERRORE:\n"+json.error);
				else{
					alert("e-mail modificata correttamente");
					$('#my-email-hint').text(json.email);
					//clear inputs...
					$('#all-email-inputs input').val('');
				}
			})
			.fail(function(e){
				console.log(e);
				alert("Oooops!");
			})
			.always(function(){
				//unlock form...
				BUSY.end();
			});
	}

	$(function(){
		$('#savePass').click(function(){
			checkPass(savePass);
		});
		
		$('#saveEmail').click(function(){
			checkEmail(saveEmail);
		});
		
		$('#pf-image b').click(function(){
			$('#upload-files').modalbox();
		});
		
		PW = (new PasswordMeter()).initMeter($('#my-password-n'), $('#passmeter p'));
		
		$('#saveImage').click(function(){
			$('#my-dropzone').submit();
		});
		$('#deleteImage').click(deleteImage);
		
		$('#upload-files').modalbox({maxH:300},null,true);
	});


})();