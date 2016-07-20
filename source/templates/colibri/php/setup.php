<?php
header('Content-Type: application/json');

require_once "../../../config.php";
require_once "../../../".$CONFIG['database']['dir']."functions.inc.php";

//control login
require_once "../../../php/sessionmanager.class.php";
$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(1);

//control if database already setup:
//this template add a table called "youtube", linked to any article.
//if article is deleted, so the youtube video.
$pdostat = $pdo->query('CREATE TABLE IF NOT EXISTS youtube (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	videoid TEXT,
	videostart DECIMAL DEFAULT (0),
	videoend DECIMAL,
	videow INT DEFAULT (560),
	videoh INT DEFAULT (315),
	idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE
)',PDO::FETCH_ASSOC) or jsonError("Coudn't create youtube table");

jsonSuccess(["Database succesfully updated!"]);
?>