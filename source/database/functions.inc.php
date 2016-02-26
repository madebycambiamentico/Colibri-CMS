<?php

/**
 * setup rewrite rules
 *
 * @require config.php loaded
 */
if (!isset($CONFIG)){
	require_once '../config.php';
}



//open database connection
$pdo = new PDO('sqlite:'.$CONFIG['database']['path']) or die("cannot open the database");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



/**
* check logged in.
* if logged in return user class (>=1), else return false.
* if $maxuserclass is given, return FALSE if user class > $maxuserclass, else return user class (>=1)
*
* @param		(int)(optional)
* @return bool | int
*/
function isLoggedIn($maxuserclass=0){
	if (!$maxuserclass)
		return isset($_SESSION['uid']) ? $_SESSION['uclass'] : false;
	else{
		if (!isset($_SESSION['uid'])) return false;
		return $maxuserclass <= $_SESSION['uclass'] ? $_SESSION['uclass'] : false;
	}
}

//logout + should invalidate old session 
function logout($get='',$stopScript=true){
	global $SessionManager, $CONFIG;
	$SessionManager->regenerateSession();
	session_destroy();
	if ($stopScript) goToPage( $CONFIG['mbc_cms_dir'].'login.php'.($get !== '' ? '?'.$get : '') );
}

//go to login page, set custom GET
function goToPage($path='/'){
	closeConnection();
	if (!headers_sent()) {
		header('Location: '.$path);
	}
	die();
}

//move user to home if not allowed in, clear session for not logged in, allow for $maxuserclass.
//if $maxuserclass == 0 then everybody logged is allowed (no class check)
function allowOnlyUntilUserClass(
	$maxuserclass=0,
	$jsonError=false,
	$errorNOLOG="La sessione è scaduta. Effettua nuovamente l'accesso.",
	$errorLOG="Non possiedi i privilegi necessari per effettuare questa operazione."
){
	if ($jsonError){
		if (!isLoggedIn())
			jsonErrorLogout($errorNOLOG);
		if ($maxuserclass > $_SESSION['uclass'] && $maxuserclass > 0)
			jsonError($errorLOG);
	}
	else{
		global $CONFIG;
		if (!isLoggedIn())
			logout();
		if ($maxuserclass > $_SESSION['uclass'] && $maxuserclass > 0)
			goToPage( $CONFIG['mbc_cms_dir'].'bacheca.php' );
	}
}




//common functions
//TODO
function closeConnection(){
	global $pdo;
	if (isset($pdo)) unset($pdo);
}

function jsonError($error="unknow"){
	closeConnection();
	die(json_encode(["error" => $error]));
}

function jsonSuccess($success=["success" => true]){
	closeConnection();
	exit(json_encode(array_merge(["error" => false],$success)));
}

function jsonErrorLogout($error="unknow"){
	global $SessionManager;
	$SessionManager->regenerateSession();
	session_destroy();
	jsonError($error);
}

?>