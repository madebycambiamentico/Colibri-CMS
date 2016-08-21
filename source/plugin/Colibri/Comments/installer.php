<?php

/**
* Colibri > Comments installer
*
* edit the database preparing for this plugin (Colibri > Comments)
* - add table "comments" and "comments_treepath".
* - edit table "sito" to add global on/off variable
* - edit table "articoli" to add local on/off variable
* UN-INSTALLATION: not supported
*
* @param (bool) $_POST['install'] [optional] Ask to perform installation or unintallation
*
* @version 1.0.0
*
* @author Nereo Costacurta
*/

header('Content-Type: application/json');

require '../../../config.php';


//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2); // only webmasters are meant to edit database.


//control variables
if (!isset($_POST['install']))
	jsonError("Missing variables");






//this plugin doesn't support uninstallation
if (!$_POST['install'] || $_POST['install']=='false'){
	jsonError("This plugin doesn't support uninstallation.");
}







/******************************
* TABLE AND TRIGGERS CREATION *
*******************************/


$queries = ["PRAGMA foreign_keys = off;"];


//----------------- TABLES --------------------
// Table commenti (1)
$queries[] =
"CREATE TABLE IF NOT EXISTS commenti (
    id         INTEGER  PRIMARY KEY AUTOINCREMENT,
    content    TEXT     COLLATE NOCASE,
    idutente   INTEGER  REFERENCES utenti (id) ON DELETE SET NULL
                                               ON UPDATE CASCADE,
    idcommento INTEGER  REFERENCES commenti (id) ON DELETE CASCADE
                                                 ON UPDATE CASCADE,
    idarticolo INTEGER  REFERENCES articoli (id) ON DELETE CASCADE
                                                 ON UPDATE CASCADE,
    datacreaz  DATETIME DEFAULT (CURRENT_TIMESTAMP),
    popularity INT      DEFAULT (0),
    name       TEXT,
    email      TEXT,
    website    TEXT
);";

// Table commenti_treepath (2)
$queries[] =
"CREATE TABLE IF NOT EXISTS commenti_treepath (
    ancestor   INTEGER REFERENCES commenti (id) ON DELETE CASCADE
                                                ON UPDATE CASCADE,
    descendant INTEGER REFERENCES commenti (id) ON DELETE CASCADE
                                                ON UPDATE CASCADE,
    depth      INTEGER DEFAULT (0),
    PRIMARY KEY (
        ancestor,
        descendant
    )
);";


//----------------- TRIGGERS --------------------
// Trigger: insert_first_branch (3)
$queries[] =
"CREATE TRIGGER IF NOT EXISTS insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id,new.id);
END;";

// Trigger: check_comment_exists (4)
$queries[] =
"CREATE TRIGGER IF NOT EXISTS check_comment_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	SELECT CASE WHEN NOT EXISTS (SELECT id FROM commenti WHERE id = new.idcommento) THEN RAISE (IGNORE) END;
END;";

// Trigger: insert_tree_path (5)
$queries[] =
"CREATE TRIGGER IF NOT EXISTS insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant, depth)
		SELECT t.ancestor, NEW.id, (t.depth+1) FROM commenti_treepath t WHERE t.descendant = new.idcommento
		UNION ALL
		SELECT NEW.id, NEW.id, 0;
END;";


$queries[] = "PRAGMA foreign_keys = on;";


if (! $pdo->beginTransaction()) jsonError("beginTransaction failed");
	foreach ($queries as $k => $query){
		if ( false === $pdo->exec($queries) )
			jsonError("Unable to edit database tables and triggers (query no.{$k})");
	}
$pdo->commit();







/********************************
* EDIT CURRENT DATABASE COLUMNS *
*********************************/


//----------------------------------------------------------
//check if comment_allow column exists in "sito" table
//this is the "weak" option to disable comments. this does NOT disable the plugin.
//comments already saved will be displayed anyway.
try {
	
	$pdo->query('SELECT comment_allow FROM sito LIMIT 1');
	
} catch (Exception $e) {
	//when columns doesn't exists, edit that table!
	$queries =
		"PRAGMA foreign_keys = off;".
		"ALTER TABLE sito ADD COLUMN comment_allow BOOLEAN DEFAULT (1);".
		"PRAGMA foreign_keys = on;";
	$pdo->beginTransaction();
	if ( false === $pdo->exec($queries) )
		jsonError("Unable to add 'comment_allow' into 'sito'");
	$pdo->commit();
}



//----------------------------------------------------------
//check if comment_class column exists in "sito" table
//this is the "weak" option to disable comments. this does NOT disable the plugin.
//comments already saved will be displayed anyway.
try {
	
	$pdo->query('SELECT comment_class FROM sito LIMIT 1');
	
} catch (Exception $e) {
	//when columns doesn't exists, edit that table!
	$queries =
		"PRAGMA foreign_keys = off;".
		"ALTER TABLE sito ADD COLUMN comment_class INT DEFAULT (-1);".
		"PRAGMA foreign_keys = on;";
	$pdo->beginTransaction();
	if ( false === $pdo->exec($queries) )
		jsonError("Unable to add 'comment_class' into 'sito'");
	$pdo->commit();

}



//----------------------------------------------------------
//check if comment_allow column exists in "articoli" table
//this will override the global flag.
try {
	
	$pdo->query('SELECT comment_allow FROM articoli LIMIT 1');
	
} catch (Exception $e) {
	//when columns doesn't exists, edit that table!
	$queries =
		"PRAGMA foreign_keys = off;".
		"ALTER TABLE articoli ADD COLUMN comment_allow BOOLEAN DEFAULT (1);".
		"PRAGMA foreign_keys = on;";
	$pdo->beginTransaction();
	if ( false === $pdo->exec($queries) )
		jsonError("Unable to add 'comment_allow' into 'articoli'");
	$pdo->commit();

}



jsonSuccess();

?>