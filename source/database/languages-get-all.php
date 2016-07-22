<?php
header('Content-Type: application/json');

require_once "../config.php";

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(1,true);

$pdores = $pdo->query("SELECT code as 'c', name as 'n', supported as 'x' ".
							"FROM languages ORDER BY name ASC",
							PDO::FETCH_ASSOC) or
								jsonError("Errore durante ricerca linguaggi [query]");

jsonSuccess( ['languages' => $pdores->fetchAll()] );


?>