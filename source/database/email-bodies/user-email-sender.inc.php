<?php

require_once __DIR__ . '/../../php/PHPMailer/PHPMailerAutoload.php';
require_once __DIR__ . '/../../php/html2text/html2text.php';



function get_email_subject($type){
	switch($type){
		case 0: return "Account abilitato"; break;
		case 1: return "Account modificato"; break;
		default: return $type
	}
}




function phpmailer_send_email($aSender, $aReplyTo, $aReceiver, $subject, $html, $throw_errors = false){
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';
	
	//default: php's mail()
	//to use SMTP etc. see https://github.com/PHPMailer/PHPMailer
	$mail->isMail();

	//FROM Colibrì System
	$mail->setFrom($aSender['email'], $aSender['name']);
	if (!empty($aReplyTo['email'])){
		if (empty($aReplyTo['email']))
			$mail->addReplyTo($aReplyTo['email']);
		else
			$mail->addReplyTo($aReplyTo['email'], $aReplyTo['name']);
	}

	//TO user
	$mail->addAddress($aReceiver['email']);

	//e-mail
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $html;
	$mail->AltBody = Html2Text\Html2Text::convert($html);
	
	//----------------------------------
	//debug - REMOVE ME
	//@file_put_contents('debug.log', "Try to send '{$subject}' to {$aReceiver['email']}, with sender {$aSender['email']} ({$aSender['name']}) \n", FILE_APPEND | LOCK_EX);
	//end debug*/
	//----------------------------------

	//returns... or die error die!
	if (!$mail->send()) {
		if ($throw_errors) die( 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo );
		else{
			unset($mail);
			return false;
		}
	}
	else{
		unset($mail);
		return true;
	}
}

?>