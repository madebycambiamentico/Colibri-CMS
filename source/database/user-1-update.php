<?php
header('Content-Type: application/json');

require_once "functions.inc.php";
require_once "../php/encrypter.class.php";
$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true);



function getUserClassName($i){
	switch($i){
		case 0: return "Ospite";
		case 1: return "Amministratore";
		default: return "Webmaster";
	}
}


//controllo generale variabili
if (!isset($_POST['user_admin']))
	jsonError('Variabili errate');


$idsToDel = [];

$idsToAdd = [
	//0 - 1 are the available classes
	0 => [],
	1 => [],
	2 => []
];

foreach ($_POST['user_admin'] as $id => $user){
	switch ($user['action']){
		
		//--------------------- accept
		case 1:
			//control classe 0-1-2
			$class = intval($user['class'],10);//min(max(intval($user['class'],10), 0), 2);
			if ($class < 0 || $class > 2 || $class == 1) break;
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
	$pdores = $pdo->query("DELETE FROM utenti WHERE id IN (".implode(',',$idsToDel).") AND classe = 1") or
		jsonError('Errore durante rimozione utenti [query]');
	/*if (!$pdores->rowCount())
		jsonError("Errore durante rimozione utenti.");*/
}



//edit users
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

//request ids and check the class is 1
$idsToCheck = array_keys($users);
$pdores = $pdo->query("SELECT id FROM utenti WHERE id IN (".implode(',',$idsToCheck).") AND classe = 1", PDO::FETCH_NUM) or
	jsonError('Errore durante check utenti [query]');
$idsToEdit = $pdores->fetchAll(PDO::FETCH_COLUMN, 0);

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
//		change admin users
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
	
	
	// (2) search all owners' emails ---------------------------------------
	$pdores = $pdo->query("SELECT id,nome,email,hasimage FROM utenti WHERE id IN (".implode(',',$idsToEdit).")", PDO::FETCH_ASSOC) or
		jsonError('Errore durante ricerca email utente [query]');
	while ($r = $pdores->fetch()){
		$users[$r['id']]['email'] = $ENCRYPTER->decrypt($r['email']);
		$users[$r['id']]['img'] = $r['hasimage'];
		$users[$r['id']]['name'] = $r['nome'];
	}
	
	
	// (3) send password to owners' email addresses: -----------------------
	//TODO... and please change the name of this file...
	define('EMAIL_TYPE',1);
	define('DELIVERY_URL', $CONFIG['domain'].$CONFIG['mbc_cms_dir'].'email-bodies/delivery.php' );
	include __DIR__ . "/email-bodies/user-email-storer.inc.php";
	
	
	//clear unwanted info (email) ------------------------------------------
	foreach ($users as $id => &$user){
		unset($user['email'], $user['name']);
	}
	unset($user);
}


jsonSuccess([
	'deleted' => $idsToDel,
	'accepted' => $users
]);

?>