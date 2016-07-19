/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: accept, remove, edit class of every user.
 *
 * @require: [jquery.js >= 1.11.3], ./common.js
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

(function(){
	//+++++++++++++++++++++++++++++++++++++
	
	var USERCLASS = 1;

	
	//set visibility of "<p>there are no profiles with this class</p>",
	//depending on presence of profiles under every page section
	function checkEmptyProfileCont(){
		$('#user_waiters, #user_guests, #user_admins, #user_webmasters')
			.each(function(){
				if ($(this).children('[id^="profile-"]').length)
					$(this).removeClass('has-no-pfs');
				else
					$(this).addClass('has-no-pfs');
			});
	}

	
	//generic profile creation for:
	// GUESTS
	// ADMINS
	// WEBMASTERS
	function createProfile(id, name, hasimage, fromClass, toClass){
		if (fromClass == toClass) return false;
		console.log("I'm going to create profile: "+name+" #"+id+" class  "+fromClass+" -> "+toClass);
		var inputName = '', $idCont = null;
		switch(toClass){
			case 0://guest
				inputName = 'user_guest';
			break;
			case 1://admin
				inputName = 'user_admin';
			break;
			case 2://webmaster
				inputName = 'user_webmaster';
			break;
			default:
				//leave it as it is. should not happen.
				return false;
		}
		$contPosition = $('#'+inputName+'s .no-pfs');
		inputName += '['+id+']'; //like "user_xxxxx[5]"; i must add e.g. "[action]" to complete the input name.
		var strElement = (
		'<div class="inputs maxi aligned" id="profile-'+toClass+'-'+id+'">'+
			'<table class="profile-edit"><tr>'+
				'<td class="pf-td-img">'+
					'<div class="pf-img _64" style="background-image:url(img/users/'+(hasimage ? id : 'default')+'/face-64.png)"></div>'+
					'<div class="tools"><p>'+
						'<input type="radio" id="u_act_'+id+'_1" name="'+inputName+'[action]" value="1" checked><label for="u_act_'+id+'_1"><b class="sicon"><i class="v"></i></b></label>'+
						'<input type="radio" id="u_act_'+id+'_2" name="'+inputName+'[action]" value="2"><label title="Rifiuta" for="u_act_'+id+'_2"><b class="sicon"><i class="x"></i></b></label>'+
					'</p></div>'+
				'</td>'+
				'<td class="pf-td-tools">'+
					'<h4 class="pf-name">'+printText(name)+'</h4>'+
					(USERCLASS == 2 || toClass == 0 ?
						'<h4>Assegna classe:</h4>'+
						'<p><select name="'+inputName+'[class]">'+
							//class 1 can edit this if the target is a guest! (else can do nothing)
							'<option value="0"'+(toClass==0 ? ' selected' : '')+'>Ospite</option>'+
							'<option value="1"'+(toClass==1 ? ' selected' : '')+'>Amministratore</option>'+
							//only class 2 can edit webmasters...
							(USERCLASS == 2 ?
								'<option value="2"'+(toClass==2 ? ' selected' : '')+'>Webmaster</option>'
							: '' )+
						'</select></p>'
					: '' )+
				'</td>'+
			'</tr></table>'+
		'</div>'
		);
		//remove previous profile element
		$('#profile-'+fromClass+'-'+id).remove();
		//add new element
		$contPosition.before(strElement);
		//if webmaster edit, reflect changes on small right-panel images.
		updateSmallWebmaster(id, name, hasimage, fromClass, toClass);
	}
	
	
	function updateSmallWebmaster(id, name, hasimage, fromClass, toClass){
		if (toClass == 2){
			//add webmaster
			$('#small_webmasters').append('<div id="small_webmaster_'+id+'" class="pf-img _32" style="background-image:url(\'img/users/'+(hasimage ? id : 'default')+'/face-32.png\')" title="'+printAttr(name)+'"></div>');
		}
		else if (fromClass == 2){
			//remove webmaster
			$('#small_webmaster_'+id).remove();
		}
	}
	
	
	
	
	function editProfiles(
		selectorCont,						//(str )e.g. "div#user_guests" or "#user_guests"
		uClass,								//(int) user class: -1 ... 2
		postPage,							//(str) e.g. "user-0-update.php"
		optionalNameForConsole			//(str) e.g. "GUEST"
	){
		console.log('sending '+optionalNameForConsole+' profiles to be updated...');
		var data = $(selectorCont+' input, '+selectorCont+' select').serialize();
		if (data === '') return false;
		BUSY.start();
		$.post('database/'+postPage,data,null,'json')
			.done(function(json){
				console.log(json);
				if (json.error) return alert("ERRORE:\n"+json.error);
				//deleted profiles:
				$.each(json.deleted,function(k,id){
					$('#profile-'+uClass+'-'+id).remove();
				});
				//accepted profiles:
				$.each(json.accepted,function(id,info){
					var name = $('#profile-'+uClass+'-'+id+' h4.pf-name').html();
					var hasimage = uClass<0 ? false : $('#profile-'+uClass+'-'+id+' .pf-img').css('background').indexOf('/default/') != -1;
					createProfile(id,name,hasimage, uClass,info.class);
				});
			})
			.fail(function(e){
				console.log(e);
				alert("Oooops!");
			})
			.always(function(){
				console.log(optionalNameForConsole+" profiles updated!");
				checkEmptyProfileCont();
				BUSY.end();
			});
		return true;
		
	}
	
	
	
	//general setup
	$(function(){
		
		USERCLASS = $('#user_webmasters').length ? 2 : 1;
		
		checkEmptyProfileCont();
		
		//admins can:
		// - raise profiles up to admin level.
		// - delete / declass profiles up to guest level
		$('#saveNewUsers').click(function(){
			editProfiles('#user_waiters', -1, "user-_-update.php", "WAITING");
		});
		$('#saveGuestUsers').click(function(){
			editProfiles('#user_guests', 0, "user-0-update.php", "GUEST");
		});
		//webmasters can:
		// - raise profiles up to webmaster level.
		// - delete / declass profiles up to webmaster level
		if (USERCLASS == 2){
			$('#saveAdminUsers').click(function(){
				editProfiles('#user_admins', 1, "user-1-update.php", "ADMIN");
			});
			$('#saveWebmasterUsers').click(function(){
				if (!confirm("Attenzione! Se vengono rimossi tutti i webmaster, l'unico modo per poter gestire gli amministratori e il template del sito è aggiungere manualmente un webmaster al database!\n\nCONTINUARE COMUNQUE?")) return false;
				//for now webmasters can commit harakiri
				editProfiles('#user_webmasters', 2, "user-2-update.php", "WEBMASTER");
			});
		}
	})

	//+++++++++++++++++++++++++++++++++++++
})();