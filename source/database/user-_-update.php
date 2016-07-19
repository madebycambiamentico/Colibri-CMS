<?php
header('Content-Type: application/json');

require_once "functions.inc.php";
require_once "../php/encrypter.class.php";
$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);



function getUserClassName($i){
	switch($i){
		case 0: return "Ospite";
		case 1: return "Amministratore";
		default: return "Webmaster";
	}
}





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
	$path = __DIR__ . '/../php/RandomLib-1.1.0/lib/' . $path . '.php';
	if (file_exists($path)) {
		require_once $path;
	}
});

$RL_factory = new RandomLib\Factory;

// WHICH ONE GENERATOR SHOULD I USE?
// uncomment [ONLY] one of this lines if you like a more/less strong alghorithm
//$RL_generator = $RL_factory->getLowStrengthGenerator();
$RL_generator = $RL_factory->getMediumStrengthGenerator();
//$RL_generator = $RL_factory->getHighStrengthGenerator();

function getNewPassword($length=16, $chars='0123456789@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-'){
	global $RL_generator;
	//create random password
	$password = $RL_generator->generateString($length,$chars);
	//this is the password as sended via form by a user
	$hashed_password = hash('sha512', $password);
	//password stored in database are hashed with salt
	$database_random_salt = hash('sha512', $RL_generator->generateString(32));
	$database_password = hash('sha512', $hashed_password.$database_random_salt);
	//return cleared password, and database values:
	return ['plain_pass' => $password, 'db_pass' => $database_password, 'db_salt' => $database_random_salt];
}

//----------------------------------------------------
//					RANDOMLIB + SECURITYLIB*/
//----------------------------------------------------






//controllo generale variabili
if (!isset($_POST['user_waiter']))
	jsonError('Variabili errate');




$idsToDel = [];

$idsToAdd = [
	//0 - 1 are the available classes
	0 => [],
	1 => [],
	2 => []
];

foreach ($_POST['user_waiter'] as $id => $user){
	switch ($user['action']){
		
		//--------------------- accept
		case 1:
			//control classe 0-1-2
			$class = intval($user['class'],10);//min(max(intval($user['class'],10), 0), 2);
			if ($class < 0 || $class > 2) break;
			//verify admin/webmaster rights
			if ($_SESSION['uclass'] == 1 && $class > 1) break;
			//add user to updater array, set id to secure ones.
			$idsToAdd[$class][] = intval($id,10);
		break;
		
		//--------------------- remove
		case 2:
			$idsToDel[] = intval($id,10);
		break;
	}
}





//--------------------------
//remove users
//(no need to check if they exists)
//--------------------------

if (!empty($idsToDel)){
	$pdores = $pdo->query("DELETE FROM utenti WHERE id IN (".implode(',',$idsToDel).") AND classe = -1") or
		jsonError('Errore durante rimozione utenti [query]');
	/*if (!$pdores->rowCount())
		jsonError("Errore durante rimozione utenti.");*/
}



//"add" users
$users = [];

//--------------------------
//control ids to be changed
//--------------------------

foreach ($idsToAdd as $class => $ids){
	if (!empty($ids)){
		//store "id" => ["class" => 0...2]
		$addedInThisClass = array_fill_keys( $ids, ['class' => $class] );
		$users += $addedInThisClass; // array + array -> preserves numeric keys
	}
}

//request ids and check the class is -1
$idsToCheck = array_keys($users);
$pdores = $pdo->query("SELECT id FROM utenti WHERE id IN (".implode(',',$idsToCheck).") AND classe = -1", PDO::FETCH_NUM) or
	jsonError('Errore durante check utenti [query]');
$idsToEdit = $pdores->fetchAll(PDO::FETCH_COLUMN, 0);

//die( json_encode($idsToEdit) );

//remove ids which don't meet the prerequisites
if (empty($idsToEdit)){
	$users = [];
}
elseif (count($idsToEdit) != count($idsToCheck) ){
	foreach($idsToCheck as $id){
		if (!in_array($id, $idsToEdit)){
			$cl = $users[$id]['class'];
			$index = array_search($id, $idsToAdd[$cl]);
			unset( $users[$id], $idsToAdd[$cl][$index] );
		}
	}
}
unset($idsToCheck);


//----------------------------
//		change waiting users
//----------------------------

//set new class for waiting users
if (!empty($idsToEdit)){
	
	//update class ---------------------------------------------------------
	foreach ($idsToAdd as $class => $ids){
		if (!empty($ids)){
			$pdores = $pdo->query("UPDATE utenti SET classe = {$class}, about = '' WHERE id IN (".implode(',',$ids).")") or
				jsonError('Errore durante aggiunta utenti [query]');
			/*if (!$pdores->rowCount())
				jsonError("Errore durante aggiunta utente classe {$class}.");*/
		}
	}
	unset($idsToAdd);
	
	
	// (1) create a new password -------------------------------------------
	$pdostat = $pdo->prepare("UPDATE utenti SET pass=?, salt=? WHERE id = ?") or
		jsonError("Errore durante creazione password utenti [prepare]");
	//create password for every new user
	foreach ($users as $id => &$user){
		//generate secure password:
		$psw = getNewPassword();
		//store clear plaintext password
		$user['pass'] = $psw['plain_pass'];
		if (!$pdostat->execute([$psw['db_pass'], $psw['db_salt'], $id]))
			jsonError("Errore durante creazione password utente {$id} [execute]");
		if (!$pdostat->rowCount())
			jsonError("Errore durante creazione password utente {$id}.");
	}
	unset($user);
	
	
	// (2) search all owners' emails ---------------------------------------
	$pdores = $pdo->query("SELECT id,nome,email FROM utenti WHERE id IN (".implode(',',$idsToEdit).")", PDO::FETCH_ASSOC) or
		jsonError('Errore durante ricerca email utente [query]');
	while ($r = $pdores->fetch()){
		$users[$r['id']]['email'] = $ENCRYPTER->decrypt($r['email']);
		$users[$r['id']]['name'] = $r['nome'];
	}
	
	
	// (3) send password to owners' email addresses: -----------------------
	//TODO... and... please change the name of this file...
	define('EMAIL_TYPE',0);
	define('DELIVERY_URL', $CONFIG['domain'].$CONFIG['mbc_cms_dir'].'email-bodies/delivery.php' );
	include __DIR__ . "/email-bodies/user-email-storer.inc.php";
	
	
	//clear unwanted info (email) ------------------------------------------
	foreach ($users as $id => &$user){
		unset($user['pass'], $user['email'], $user['name']);
	}
	unset($user);
}


jsonSuccess([
	'deleted' => $idsToDel,
	'accepted' => $users
]);

?>