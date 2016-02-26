<?php

require_once "config.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');

if (!isset(
	$_POST['subject'],
	$_POST['email'],
	$_POST['phone'],
	$_POST['message'])
)
	jsonError("Variabili errate");

//control inputs
$subject	= trim($_POST['subject']);
$email	= trim($_POST['email']);
$phone	= trim($_POST['phone']);
$msgtxt	= trim($_POST['message']);

if (empty($msgtxt))
	jsonError("No message received, sorry. Type the message before send it.");
if (empty($email) && empty($phone))
	jsonError("You need to fill at least one of this fileds: email or phone number");
//email validation
if ($email!==''){
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		jsonError('This is not an email');
}

//optional inputs
if (empty($subject)) $subject = "Colibrì System: new communication";
if (empty($phone)) $phone = "no phone number(s) given";



//get email from system:
$adminname = null;
$adminemail = null;
if ($pdores = $pdo->query("SELECT autore,email FROM sito ORDER BY id DESC LIMIT 1", PDO::FETCH_ASSOC)){
	foreach ($pdores as $r){
		$adminname = $r["autore"];
		if ($r['email']){
			$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
			$adminemail = $ENCRYPTER->decrypt($r['email']);
		}
	}
	$pdores->closeCursor();
}
if (!$adminemail) jsonError("The site administrator didn't set properly his email and cannot be reached.\nWe are sorry for the inconvenience.\nFeel free to report us of this error using any other contact tool, we will be very grateful.");
if (!$adminname) $adminname = 'Colibrì System';


//find user if is logged in, then take name and email*
//* (if not already set: the user could prefere to comunicate with different ones)
$sendername = null;
if (isLoggedIn()){
	if ($pdores = $pdo->query("SELECT nome, email FROM utenti WHERE id=".$_SESSION['uid']." LIMIT 1", PDO::FETCH_ASSOC)){
		foreach ($pdores as $r){
			$sendername = $r["nome"];
			if (empty($email) && $r['email']){
				$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
				$email = $ENCRYPTER->decrypt($r['email']);
			}
		}
		$pdores->closeCursor();
	}
}




/**********************
	PHPMailer SENDER
**********************/

require 'php/PHPMailer-5.2.14/PHPMailerAutoload.php';

//send to admin...

$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->IsMail();

if (!empty($email)){
	if ($sendername){
		$mail->setFrom($email, $sendername);
		$mail->addReplyTo($email, $sendername);
	}
	else{
		$mail->setFrom($email);
		$mail->addReplyTo($email);
	}
}
else{
	$mail->setFrom('info@'.$_SERVER['HTTP_HOST'],'Colibrì System');
}

$mail->addAddress($adminemail, $adminname);

$mail->Subject = $subject;

$mail->Body    = "Phone Number: {$phone}\n\n".$msgtxt;

if(!$mail->send()) {
	jsonError( 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo );
} else {
	if (!empty($email)){
		//try to send copy to sender :)
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		$mail->IsMail();
		$mail->setFrom('info@'.$_SERVER['HTTP_HOST'], $adminname);
		$mail->Subject = 'Email copy from '.$_SERVER['HTTP_HOST'];
		if ($sendername)
			$mail->addAddress($email, $sendername);
		else
			$mail->addAddress($email);
		$mail->Body    = "This is your sent message copy:\n\n".
			"Phone Number: {$phone}\n\n{$msgtxt}".
			"\n\nThank you for contacting us.\n{$adminname}";
		if(!$mail->send()) {
			jsonError( "The message has been sent, but we couldn't reach your email: ".$mail->ErrorInfo );
		} else {
			jsonSuccess();
		}
	}
	jsonSuccess();
}

?>