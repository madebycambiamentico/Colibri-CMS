<?php
if (!isset($_GET['id'])) die;


require_once "../../config.php";
$Encrypter = new \Colibri\Encrypter( CMS_ENCRYPTION_KEY );

//control login
$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(2,true);



$pdores = $pdo->query("SELECT * FROM emails WHERE iduser = ".intval($_GET['id'],10)." ORDER BY datacreaz DESC LIMIT 1", PDO::FETCH_ASSOC) or
	die("Errore durante ricerca email utente {$_GET['id']} [query]");
$email = $pdores->fetch() or
	die("Nessuna mail per l'utente {$_GET['id']}");

echo $Encrypter->decrypt($email['content']);

?>