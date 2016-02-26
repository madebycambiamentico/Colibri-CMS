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
		if (!message) return failsend("Type the message before send it.");
		if (!email && !phone) return failsend("You need to fill at least one of this fileds: email or phone number");
		if (email){
			if (!/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i.test(email)){
				if (!confirm("The email could be invalid. Proceed anyway?")) return failsend();
			}
		}
		//send request
		$.post($(form).data('action'), {email: email, subject: subject, phone: phone, message: message}, null, 'json')
			.success(function(json){
				if (json.error != false) return alert("ERRORE:\n"+json.error);
				form['subject'].value = form['message'].value = '';
				//block further submit:
				alert("Your message has been sent. Thank you!");
			})
			.error(function(e){
				console.log(e);
				alert('Oooops!');
			})
			.always(function(){
				//unlock form
				SENDING = false;
			})
	})
});