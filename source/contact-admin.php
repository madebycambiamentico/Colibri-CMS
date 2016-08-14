<?php

/*
 * @author Nereo Costacurta
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

/*
* the user should know that his mail has been sent!
* this script should not be putted in standard delivery mail one...
*/

require_once "config.php";

//control login
$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');

if (!isset(
		$_POST['subject'],
		$_POST['email'],
		$_POST['phone'],
		$_POST['message']
	)
)
	jsonError("Variabili errate");

//control inputs
$subject	= trim($_POST['subject']);
$user_email	= trim($_POST['email']);
$phone	= htmlentities(trim($_POST['phone']));
$msgtxt	= htmlentities(trim($_POST['message']));

if (empty($msgtxt))
	jsonError("No message received, sorry. Type the message before send it.");
if (empty($user_email) && empty($phone))
	jsonError("You need to fill at least one of this fileds: email or phone number");
//email validation
if ($user_email!==''){
	if (filter_var($user_email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('This is not an email');
}

//optional inputs
if (empty($subject)) $subject = "Colibrì System: new communication";
if (empty($phone)) $phone = "no phone number(s) given";


//(autoload class)
$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );

//get email from system:
$admin_name = null;
$admin_email = null;
if ($pdores = $pdo->query("SELECT autore, email, recaptcha_key, recaptcha_secret, titolo FROM sito ORDER BY id DESC LIMIT 1", PDO::FETCH_ASSOC)){
	if($r = $pdores->fetch()){
		
		$website_title = htmlentities($r['titolo'],ENT_QUOTES);
		
		//verify recaptcha...
		if (!empty($r['recaptcha_secret'])){
			
			$ReCaptcha = new \ReCaptcha\ReCaptcha(
				$r['recaptcha_key'],
				$Encrypter->decrypt($r['recaptcha_secret']),
				$_SERVER['REMOTE_ADDR']
			);
			
			if (empty($_POST['g-recaptcha-response'])) jsonError("POST['g-recaptcha-response'] mancante");
			
			$ReCaptcha->validate() or jsonError($ReCaptcha->error);
		}
		
		//set admin email + name
		$admin_name = $r["autore"];
		if ($r['email']){
			$admin_email = $Encrypter->decrypt($r['email']);
			//will check after...
		}
	}
	$pdores->closeCursor();
}
if (!$admin_email || filter_var($admin_email, FILTER_VALIDATE_EMAIL) === false) jsonError("The site administrator didn't set properly his email and cannot be reached.\nWe are sorry for the inconvenience.\nFeel free to report us of this error using any other contact tool, we will be very grateful.");
if (!$admin_name) $admin_name = 'Colibrì System';


//find user if is logged in, then take name and email*
//* (if not already set: the user could prefere to comunicate with different ones)
$user_name = null;
if (isLoggedIn()){
	if ($pdores = $pdo->query("SELECT nome, email FROM utenti WHERE id=".$_SESSION['uid']." LIMIT 1", PDO::FETCH_ASSOC)){
		foreach ($pdores as $r){
			$user_name = $r["nome"];
			if (empty($user_email) && $r['email']){
				$user_email = $Encrypter->decrypt($r['email']);
			}
		}
		$pdores->closeCursor();
	}
}




/**********************
	PHPMailer SENDER
**********************/

require_once __DIR__ . '/database/email-bodies/user-email-sender.inc.php';

//send to admin and user in BCC

$sender = [
	'name' => 'Colibrì e-mail delivery System',
	'email' => 'colibri@delivery.system'
];

$recipients = [
	[//admin
		'name' => empty($admin_name) ? null : $admin_name,
		'email' => $admin_email
	],
	[//user
		'name' => empty($user_name) ? null : $user_name,
		'email' => $user_email
	]
];

$user_email = htmlentities($user_email,ENT_QUOTES);
$user_name = $user_name ? htmlentities($user_name) : "[nessun nome]";
$html =	"<h3>Comunicazione <span style='color:#cc0000'>{$website_title}</span></h3><hr>".
			"<p>Soggetto: <i>".htmlentities($subject)."</i></p>".
			"<p>Da: <b>{$user_name}</b> <a href=\"mailto:{$user_email}\">{$user_email}</a></p>".
			"<p>Telefono: <i>{$phone}</i></p>".
			"<p>Messaggio: {$msgtxt}</p>".
			"<hr><b>Una copia del messaggio verrà recapitata anche all'utente scrivente.</b>".
			"<p style='font-size:smaller'>This is an automatically generated e-mail, please do not reply directly to this message.<br>For any needs, please contact us at <a href='mailto:{$recipients[0]['email']}?subject={$website_title} - YOUR_NAME_HERE'>{$recipients[0]['email']}</a> with subject \"{$website_title} - <b>YOUR NAME</b>\"</p>";

if ( phpmailer_send_email(
	$sender,
	null,									//reply to
	$recipients,
	"Contatto amministratore",		//subject
	$html,
	false,								//die on error?
	false									//BCC ?
))
	jsonSuccess();
else
	jsonError();

?>
