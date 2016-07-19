/**
 * @author: nereo costacurta
 *
 * @project: colibrì template | madebycambiamentico
 * @description: menu, css3 support, send mail.
 *
 * @require: [jquery.js >= 1.11.3]
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/
if (!('console' in window)){console={log:function(s){alert(s)}}}

$(function(){
	//menu
	var OPENEDMENU = 0;
	$('#menus i').click(function(){
		var id = $(this).data('id');
		var li = $('#menu-'+id);
		if (OPENEDMENU == id){
			li.toggleClass('open');
		}
		else{
			$('#menu-'+OPENEDMENU).removeClass('open');
			li.addClass('open');
			OPENEDMENU = id;
		}
	});
	//email
	var SENDING = false;
	function failsend(s){
		if (s) alert(s);
		SENDING = false;
		return false;
	}
	$('#contactform form').submit(function(e){
		e.preventDefault();
		//lock form
		if (SENDING) return false; else SENDING = true;
		var form = this;
		//control inputs
		var email = form['email'].value.trim(),
			subject = form['subject'].value.trim(),
			phone = form['phone'].value.trim(),
			message = form['message'].value.trim();
		if (!message) return failsend("Scrivi il messaggio prima di inviare la mail!");
		if (!email && !phone) return failsend("E' necessario riempire almeno uno di questi campi:\n- telefono\n- email");
		if (email){
			if (!/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i.test(email)){
				if (!confirm("L'email immessa non sembra essere valida, continuare lo stesso?")) return failsend();
			}
		}
		//send request
		$.post($(form).data('action'), $(form).serialize(), null, 'json')
			.success(function(json){
				if (json.error != false){
					if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
					return alert("ERRORE:\n"+json.error);
				}
				form.reset();
				alert("Il messaggio è stato spedito.\nGrazie! :)");
				//block further submit: TODO...
			})
			.error(function(e){
				console.log(e);
				alert('Oooops! Non siamo riusciti a spedire il messaggio... prova più tardi.');
			})
			.always(function(){
				//unlock form
				SENDING = false;
			})
	})
});