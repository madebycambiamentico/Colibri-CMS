<?php

header('Content-Type: application/json');

require_once "../../../config.php";

//control login
$SessionManager = new \Colibri\SessionManager();
$SessionManager->sessionStart('colibri');
allow_user_from_class(2);

$InstallHelp = new \Colibri\InstallHelper;
$TA = $InstallHelp->create_transaction();

//control if database already setup:
//this template add a table called "youtube", linked to any article.
//if article is deleted, so the youtube video.
$TA->add_query(
"CREATE TABLE IF NOT EXISTS youtube (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	videoid TEXT,
	videostart DECIMAL DEFAULT (0),
	videoend DECIMAL,
	videow INT DEFAULT (560),
	videoh INT DEFAULT (315),
	idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE
)");

if (!$TA->run_queries()){
	jsonError('Colibrì template setup failed: '.implode(", ",$TA->errors));
}
else
	jsonSuccess(["Colibrì template setup suceeded!"]);

?>