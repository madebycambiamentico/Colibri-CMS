<?php

/**
* @summary: store emails to be sent in database.
*
* @description: after storing encrypted emails, this script will call "delivery.php" in order to start delivery service (if enabled).
*					it is called by this pages: "user-_-update.php", "database/user-0-update.php", "database/user-1-update.php", "database/user-2-update.php"
*
* @link "delivery.php"
* @global (string) EMAIL_TYPE		0 = accepted profile, 1 = updated profile... TODO
* @global (string) DELIVERY_URL	url of delivery service script
*
* @license GPLv3
* @author Nereo Costacurta
* @copyright: (C)2016 nereo costacurta
*/

if (!defined('EMAIL_TYPE') || !isset($Config)){ header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); die; }

require_once __DIR__ . "/async_curl.fn.php";

//include email body generator
require_once __DIR__ . "/".EMAIL_TYPE.".inc.php";


switch(EMAIL_TYPE){
	
	case 0:// accepted profile
		//check variables
		if (empty($users)) break;
		//store encrypted emails
		$pdostat = $pdo->prepare("INSERT INTO emails (type, iduser, content) VALUES (?,?,?)") or
			jsonError("Errore durante creazione email utente [prepare]");
		foreach ($users as $id => $u){
			$email = create_email($u,true);
			if (!$email) continue;
			if (!$pdostat->execute([EMAIL_TYPE, $id, $email]))
				jsonError("Errore durante creazione email utente {$id} [execute]");
		}
		//start delivery :)
		async_curl(DELIVERY_URL .'?autoupdate=1&et=0')
			or jsonError('Unable to call delivery.php!!! '.DELIVERY_URL.'?autoupdate=1&et=0');
	break;
	
	case 1:// profile has been modified by admins
		//check variables
		if (empty($users)) break;
		$pdostat = $pdo->prepare("INSERT INTO emails (type, iduser, content) VALUES (?,?,?)") or
			jsonError("Errore durante creazione email utente [prepare]");
		//store encrypted emails
		foreach ($users as $id => $u){
			$email = create_email($u,$id,true);
			if (!$email) continue;
			if (!$pdostat->execute([EMAIL_TYPE, $id, $email]))
				jsonError("Errore durante creazione email utente {$id} [execute]");
		}
		//start delivery :)
		async_curl(DELIVERY_URL .'?autoupdate=1&et=1')
			or jsonError('Unable to call delivery.php!!! '.DELIVERY_URL.'?autoupdate=1&et=1');
	break;
	
	case 2:// lost your password?
		
	break;
	
	case 3:// contact site owner
		
	break;
}


?>