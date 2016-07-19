/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: login script.
 *
 * @require: [jquery.js >= 1.11.3]
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
 */

function checkform(onsuccess){
	//control name
	var name = $('#my-name').val().trim();
	if (name == ''){
		alert("Inserire il nome!");
		return false;
	}
	//control password
	var pass = $('#my-password').val().trim();
	if (pass.length<4){
		alert("La password è troppo corta!");
		return false;
	}
	//generate hashed password
	var shaObj = new jsSHA("SHA-512", 'TEXT');
	shaObj.update(pass);
	$('#my-hashed-pass').val( shaObj.getHash("HEX") ); //128 CHAR
	//callback on success
	if ($.isFunction(onsuccess)) onsuccess();
	return true;
}

var LOGGING = false;

function log(){
	//prevent multiple login
	if (LOGGING){
		alert("Login già in corso, attendi.");
		return false;
	}
	else LOGGING = true;
	//show loader
	$loader = $('#loader')
		.removeClass('done');
	//send post request
	if ( !checkform(function(){
		$.post('database/login.php',$('#my-login').serialize(),null,'json')
			.success(function(json){
				if (json.error !== false){
					alert("ERRORE\n"+json.error);
					$loader.addClass('done');
					LOGGING = false;
				}
				else
					location.assign('./bacheca?logged');
			})
			.error(function(e){
				alert('Ooops!');
				$loader.addClass('done');
				LOGGING = false;
				console.log(e);
			})
	}) ){
		//check form failed!!!
		LOGGING = false;
		$loader.addClass('done');
	}
}

$(function(){
	$('#my-login').submit(function(e){
		e.preventDefault();
	});
	
	$('#my-login input').keypress(function(e){
		if (e.which == 13){
			log();
			return false;    //<---- Add this line
		}
	});
	
	$('#send-me').click(log);
});