<?php
if (!isset($_GET['id'])) die;


require_once "../functions.inc.php";
require_once '../../php/encrypter.class.php';
$ENCRYPTER = new Encrypter( $CONFIG['encrypt']['secret_key'] );

//control login
$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true);



$pdores = $pdo->query("SELECT * FROM emails WHERE iduser = ".intval($_GET['id'],10)." ORDER BY datacreaz DESC LIMIT 1", PDO::FETCH_ASSOC) or
	die("Errore durante ricerca email utente {$_GET['id']} [query]");
$email = $pdores->fetch() or
	die("Nessuna mail per l'utente {$_GET['id']}");

echo $ENCRYPTER->decrypt($email['content']);

?>