/**
* @author: nereo costacurta
*
* @project: colibrì CMS | madebycambiamentico
* @description: signin script.
*
* @require: [jquery.js >= 1.11.3]
*
* @license: GPLv3
* @copyright: (C)2016 nereo costacurta
*/

$(function(){
	function checkform(onsuccess){
		//control name
		var name = $('#my-name').val().trim();
		if (name == ''){
			alert("Inserire il nome!");
			return false;
		}
		//callback on success
		if ($.isFunction(onsuccess)) onsuccess();
		return true;
	}

	var LOGGING = false;

	function log(){
		//prevent multiple login
		if (LOGGING){
			alert("Registrazione già in corso, attendi.");
			return false;
		}
		else LOGGING = true;
		//show loader
		$loader = $('#loader')
			.removeClass('done');
		//send post request
		if ( !checkform(function(){
			$.post('database/user-request.php',$('#my-login').serialize(),null,'json')
				.done(function(json){
					console.log(json);
					if (json.error !== false){
						alert("ERRORE\n"+json.error);
						$loader.addClass('done');
						LOGGING = false;
					}
					else
						location.assign('./');
				})
				.fail(function(e){
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
})();