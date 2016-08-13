<?php

require_once __DIR__ . '/../../php/PHPMailer/PHPMailerAutoload.php';
require_once __DIR__ . '/../../php/html2text/html2text.php';



function get_email_subject($type){
	switch($type){
		case 0: return "Account abilitato"; break;
		case 1: return "Account modificato"; break;
		case 2: return "Recuper password"; break;
		case 3: return "Contatto Amministratore del sito"; break;
		default: return $type;
	}
}



/**
* quickly send an email thru phpmailer. recipients can be or CC or BCC (if more than one).
*
*/
function phpmailer_send_email($aSender, $aReplyTo, $aReceiver, $subject, $html, $throw_errors = false, $BCC = false){
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
	if (isset($aReceiver[0]['email'])){
		
		//add first one to direct recipient
		if (isset($aReceiver[$i]['name']))
			$mail->addAddress($aReceiver[0]['email'], $aReceiver[$i]['name']);
		else
			$mail->addAddress($aReceiver[0]['email']);
		
		//add other recipients (CC/BCC)
		for ($i=1; $i<count($aReceiver); $i++){
			if ($BCC) {
				if (isset($aReceiver[$i]['name']))
					$mail->addBCC($aReceiver[$i]['email'], $aReceiver[$i]['name']);
				else
					$mail->addBCC($aReceiver[$i]['email']);
			}
			else {
				if (isset($aReceiver[$i]['name']))
					$mail->addCC($aReceiver[$i]['email'], $aReceiver[$i]['name']);
				else
					$mail->addCC($aReceiver[$i]['email']);
			}
		}
	}
	else{
		$mail->addAddress($aReceiver['email']);
	}

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