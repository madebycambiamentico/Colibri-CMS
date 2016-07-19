<?php

/**
* @summary: send emails stored in database.
*
* @description: the Colibrì CMS offer a set of email delivery options:
* 1. manual / with cronjob: the emails are stored, but sent only manually calling "delivery.php".
* 2.1 (experimental feature) automatic with N emails per M second:
*		as soon as emails are stored, delivery.php start to send them at intervals of M seconds, in groups of N emails.
*		need to be configured by webmaster, according to server email delivery limitation.
* 2.2 (not safe) automatic all in one: as soon as the email are stored, the script try to send them all in a single script execution.
*		if timeout fatal error occurs, emails cannot be erased and the next time will be re-sent!!!
* Anyway, most of the servers have:
*	- a sent mail limit per minutes/seconds/...
*	- a script execution time limit which you can't edit even with "set_time_limit($seconds)"
*	- and most likely, if you send many many emails you'll be blocked as spammer!
* a handful of things to deal with... eh?
*
* @see class DeliveryCheck (delivery.check.inc.php)
*
* @license GPLv3
* @author Nereo Costacurta
* @copyright: (C)2016 nereo costacurta
*/

ignore_user_abort(true);

set_time_limit(0); //NOT RELIABLE! can be overridden by the server settings

echo "Colibrì delivery service started.\n";






//----------------------------------
/*/debug - REMOVE ME
if (isset($_GET['debug'])){
	@file_put_contents('debug.log', date('c')." script stopped for debug\n", FILE_APPEND | LOCK_EX);
	die;
}
//end debug*/
//----------------------------------


//----------------------------------
/*/debug - REMOVE ME
@file_put_contents('debug.log', date('c')." script initialized".(isset($_GET['autoupdate']) ? " (by php update)" : "")."\n", FILE_APPEND | LOCK_EX);
//end debug*/
//----------------------------------




require_once "../functions.inc.php";
require_once '../../php/encrypter.class.php';
	$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );
require_once 'async_curl.fn.php';






//define delivery properties: n. of emails {$quantity} to sent at intervals of {$delay} seconds
//define site owner email and name.

//default sender properties:
$sender = [
	'name' => 'Colibrì e-mail delivery System',
	'email' => 'colibri@delivery.system'
];
$replyto = [
	'name' => '',
	'email' => ''
];

//get site properties (delivery and emails)
$pdores = $pdo->query('SELECT autore, email, delivery_quantity, delivery_delay FROM sito ORDER BY id DESC LIMIT 1', PDO::FETCH_ASSOC) or
	die("Errore durante ricerca proprietà del sito [query]");
if ($r = $pdores->fetch()){
	//sender properties
	if ($r['autore']) $replyto['name']		= $r['autore'];
	if ($r['email'])  $replyto['email']		= $r['email'];
	//delivery properties
	$quantity	= $r['delivery_quantity'];		//e.g. 3 emails at time...
	$delay		= $r['delivery_delay'];			//e.g. ...every 5 seconds
}
else
	die("Le proprietà del sito non sono state inserite!");

//manual delivery: block this script if called by a php script other than self
if (isset($_GET['autoupdate']) && !$delay)
	die('The script is not enabled: call it manually or by a cron job.');


/*/----------------------------------
//debug - REMOVE ME
$quantity = isset($_GET['n']) ? max(0,intval($_GET['n'],10)) : false;
$delay = isset($_GET['t']) ? max(0,intval($_GET['t'],10)) : false;
echo "qt = {$quantity}, dt = {$delay}<br>";
//end debug*/
//----------------------------------







//**************************************
//		CHECK IF ANY OTHER DELIVERY
//		SCRIPT HAS ALREADY STARTED OR
//		THE DELAY IS NOT ENOUGH...
//**************************************

define('DELIVERY_URL', $CONFIG['domain'].$CONFIG['mbc_cms_dir'].'delivery.php'/*.'?debug=1'*/ );

require_once "delivery.check.inc.php";
$delivery_checker = new DeliveryCheck( false, true );
if ($delivery_checker->check_start() !== true)
	die('AN ERROR OCCURRED: '.$delivery_checker->last_error);




define('MAX_USER_FLAGS',3);

if ($quantity){
	//search oldest {quantity} emails
	$query = "SELECT e.content, e.id, e.type, e.iduser, u.email FROM
	emails e LEFT JOIN utenti u
	WHERE e.iduser = u.id AND u.flags < ".MAX_USER_FLAGS."
	ORDER BY e.datacreaz ASC
	LIMIT {$quantity}";
}
else{
	$query = "SELECT e.content, e.id, e.type, e.iduser, u.email FROM
	emails e LEFT JOIN utenti u
	WHERE e.iduser = u.id AND u.flags < ".MAX_USER_FLAGS."
	ORDER BY e.datacreaz ASC";
}


//**************************************
//					EMAIL SENDER
//**************************************
require_once "user-email-sender.inc.php";


//search email to be sent:
$pdores = $pdo->query($query, PDO::FETCH_ASSOC) or die("Errore durante ricerca email utenti [query]");
//keep count of found emails (not the sent ones!)
$n_emails = 0;
//keep id of email sent/failed
$sent_emails = [];
$failed_emails = [];
//send them all!
while ($r = $pdores->fetch()){
	//define email properties
	$receiver = [
		'email'			=> $ENCRYPTER->decrypt($r['email']),
		'email_id'		=> $r['id'],
		'user_id'		=> $r['iduser']
	];
	
	$subject = get_email_subject($r['type']);
	
	//debug --------------------------
	//echo "sending email to <i>{$receiver['email']}</i> #{$r['id']}<br>";
	//end debug ----------------------
	
	$html = $ENCRYPTER->decrypt($r['content']);
	//send mail, then update user status deleting that email
	//(?) if email fail to send, increase a flag to that user. if flag > 3 block user (?)
	if ( true === phpmailer_send_email($sender, $replyto, $receiver, $subject, $html) ){
		$sent_emails[] = $receiver['email_id'];
	}
	else{
		$failed_emails[] = $receiver['email_id'];
	}
	
	$n_emails++;
	
	//keep alive every 5 emails sent
	if ($n_emails % 5 == 0)
		if ($delivery_checker->keep_alive() !== true) break;
}





//delete emails sent:
if (count($sent_emails)){
	$pdores = $pdo->query("DELETE FROM emails WHERE id in (".implode(',',$sent_emails).")") or
		die("Impossibile rimuovere le email [query]");
}

//flag potentially corrupted users:
if (count($failed_emails)){
	$pdores = $pdo->query("UPDATE utenti SET flags = flags+1 WHERE id IN
		(SELECT iduser FROM emails WHERE id IN (".implode(',',$failed_emails)."))") or
		die("Impossibile aggiungere flags agli utenti [query]");
}




//debug --------------------------
//echo "n emails = {$n_emails}; sent = ".count($sent_emails)."; failed = ".count($failed_emails)."<br>";
//end debug ----------------------





//do pseudo-cron job, calling again this script after $delay

if ($delay && $quantity && $n_emails >= $quantity){
	//----------------------------------------------------
	//					/*RANDOMLIB + SECURITYLIB
	//----------------------------------------------------
	spl_autoload_register(function ($class) {
		$nslen = strlen(__NAMESPACE__);
		if (substr($class, 0, $nslen) != __NAMESPACE__) {
			//Only autoload libraries from this package
			return;
		}
		$path = substr(str_replace('\\', '/', $class), $nslen);
		$path = __DIR__ . '/../../php/RandomLib-1.1.0/lib/' . $path . '.php';
		if (file_exists($path)) {
			require_once $path;
		}
	});
	$RL_factory = new RandomLib\Factory;
	// WHICH ONE GENERATOR SHOULD I USE?
	// uncomment [ONLY] one of this lines if you like a more/less strong alghorithm
	$RL_generator = $RL_factory->getLowStrengthGenerator();
	//$RL_generator = $RL_factory->getMediumStrengthGenerator();
	//$RL_generator = $RL_factory->getHighStrengthGenerator();
	$script_id = $RL_generator->generateString(32);
	$delivery_checker->close(count($sent_emails), count($failed_emails), $script_id);
	
	sleep($delay);
	async_curl( DELIVERY_URL.'?loopid='.$script_id ) or die("cURL failed on link ".DELIVERY_URL);
	exit("The cURL will call again this script in {$delay} seconds. This action is not visible in your browser. See you!");
}
else
	$delivery_checker->close(count($sent_emails), count($failed_emails), 0);
	die(
		$delay ?
		"No more email to be sent! Hurray!" :
		"Manual delivery option is set. This script won't run again by itself!"
	);

?>