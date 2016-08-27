<?php

//this is not a standalone script.
if (!isset($Config)){
	trigger_error('functions.inc.php need to be included by config.php!', E_USER_ERROR);
}

if (!file_exists($Config->database['src'])){
	trigger_error('You must run setup.php before all!'.$Config->database['src'], E_USER_ERROR);
}



//open database connection
$pdo = new PDO('sqlite:'.$Config->database['src']) or die("cannot open the database");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



/**
* check if user is logged in.
*
* if logged in return user class, else return false.
* if $min_user_class is given, success if user class >= $maxuserclass.
*
* @param	(int)[optional]	$min_user_class
*
* @return (bool|int)			User class if success, false otherwise.
*/
function isLoggedIn($min_user_class=0){
	if (!$min_user_class)
		return isset($_SESSION['uid']) ? $_SESSION['uclass'] : false;
	else{
		if (!isset($_SESSION['uid'])) return false;
		return $min_user_class <= $_SESSION['uclass'] ? $_SESSION['uclass'] : false;
	}
}



/**
* logout + invalidate old session + redirect to login
*
* @param (string) $get			Append GET request to redirect path
* @param (bool) $stopScript	Redirect to another page (or stop script if header already sent)
*/
function logout($get='',$stopScript=true){
	global $SessionManager, $Config;
	$SessionManager->regenerateSession();
	session_destroy();
	if ($stopScript)
		goToPage( $Config->script_path . 'login' . ($get !== '' ? '?'.$get : '') );
}



/**
* redirect to other page. header must not be already sent.
*/
function goToPage($path='/'){
	closeConnection();
	if (!headers_sent()) {
		header('Location: '.$path);
	}
	die;
}


/**
* Block users whom haven't at least X class (or aren't logged)
*
* move user to dashboard if logged but not allowed in.
* for non-logged users: they will be redirect to homepage, and session will be cleared
* classes are as follow:
* (2)  is the most important user (webmaster)
* (1)  is the admin
* (0)  is the guest
* (-1) is a not-already-accepted user
*
* @see isLoggedIn
* @see jsonError
* @see logout
* @see goToPage
*
* @param (int) $min_user_class				minimum class to access the page. default: 0
* @param (bool) $jsonError						if true will print the json with error. Else user will be redirected. default: false
* @param (string) $errorNOLOG [optional]	display error if not logged in
* @param (string) $errorLOG [optional]		display error if class not high enough
*
* @return (bool)
*/
function allow_user_from_class(
	$min_user_class	= 0,
	$jsonError			= false,
	$errorNOLOG			= "La sessione è scaduta. Effettua nuovamente l'accesso.",
	$errorLOG			= "Non possiedi i privilegi necessari per effettuare questa operazione."
){
	if ($jsonError){
		if (false === isLoggedIn())
			jsonErrorLogout($errorNOLOG);
		if ($_SESSION['uclass'] < $min_user_class)
			jsonError($errorLOG);
	}
	else{
		global $Config;
		if (false === isLoggedIn())
			//clear session
			logout();
		if ($_SESSION['uclass'] < $min_user_class)
			//istead of blocking page, the not-allowed user is redirected to dashboard
			goToPage( $Config->script_path . 'bacheca' );
	}
}





//---------------------------
//common functions
//---------------------------



/**
* Close sqlite (pdo) connection, if active.
*/
function closeConnection(){
	global $pdo;
	if (isset($pdo)) unset($pdo);
}


/**
* stop script with a json-ish error
*
* @param (string) $error [optional]		What to print in error value
*/
function jsonError($error="unknow"){
	closeConnection();
	die(json_encode(["error" => $error]));
}


/**
* stop script with a json-ish success
*
* json will contain default "error" => false + merged success array.
* success array can contain anything you want.
*
* @param (array) $success [optional]		What to print in error value
*/
function jsonSuccess($success=["success" => true]){
	closeConnection();
	exit(json_encode(array_merge(["error" => false],$success)));
}


/**
* stop script with a json-ish error + logout
*
* @param (array) $success [optional]		What to print in error value
*/
function jsonErrorLogout($error="unknow"){
	global $SessionManager;
	$SessionManager->regenerateSession();
	session_destroy();
	jsonError($error);
}

?>