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





/*
--
-- File generated with SQLiteStudio v3.1.0 on mer ago 24 21:05:31 2016
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: commenti
CREATE TABLE commenti (id INTEGER PRIMARY KEY AUTOINCREMENT, content TEXT COLLATE NOCASE, idutente INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE, idcommento INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE, idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE, datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP), popularity INT DEFAULT (0), name TEXT, email TEXT, website TEXT);

-- Trigger: comments_insert_first_branch
CREATE TRIGGER comments_insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL  BEGIN INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id, new.id); END;

-- Trigger: comments_insert_tree_path
CREATE TRIGGER comments_insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL   BEGIN INSERT INTO commenti_treepath (ancestor, descendant, depth) SELECT t.ancestor, NEW.id, (t.depth + 1) FROM commenti_treepath t WHERE t.descendant = new.idcommento UNION ALL SELECT NEW.id, NEW.id, 0; END;

-- Trigger: comments_update_parent
CREATE TRIGGER comments_update_parent AFTER UPDATE ON commenti WHEN NEW.idcommento
AND
OLD.idcommento
and
NEW.idcommento != OLD.idcommento   BEGIN 
DELETE FROM commenti_treepath
WHERE descendant IN (SELECT descendant
                     FROM commenti_treepath
                     WHERE ancestor = OLD.id)
    AND ancestor IN (SELECT ancestor
                     FROM commenti_treepath
                     WHERE descendant = OLD.id
                     AND ancestor != descendant);
INSERT INTO commenti_treepath (ancestor, descendant, depth)
SELECT supertree.ancestor, subtree.descendant, (supertree.depth + subtree.depth + 1)
FROM commenti_treepath AS supertree
CROSS JOIN commenti_treepath AS subtree
WHERE supertree.descendant = NEW.idcommento
AND subtree.ancestor = OLD.id; END;

-- Trigger: comments_update_parent_to_null
CREATE TRIGGER comments_update_parent_to_null AFTER UPDATE ON commenti WHEN NEW.idcommento IS NULL
AND
OLD.idcommento   BEGIN DELETE FROM commenti_treepath
WHERE descendant IN (SELECT descendant
                     FROM commenti_treepath
                     WHERE ancestor = OLD.id)
    AND ancestor IN (SELECT ancestor
                     FROM commenti_treepath
                     WHERE descendant = OLD.id
                     AND ancestor != descendant); END;

-- Trigger: comments_update_parent_from_null
CREATE TRIGGER comments_update_parent_from_null AFTER UPDATE ON commenti WHEN NEW.idcommento IS NOT NULL
AND
OLD.idcommento IS NULL          BEGIN INSERT INTO commenti_treepath (ancestor, descendant, depth)
SELECT supertree.ancestor, subtree.descendant, (supertree.depth + subtree.depth + 1)
FROM commenti_treepath AS supertree
CROSS JOIN commenti_treepath AS subtree
WHERE supertree.descendant = NEW.idcommento
AND subtree.ancestor = OLD.id; END;

-- Trigger: comments_check_exists
CREATE TRIGGER comments_check_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL   BEGIN SELECT RAISE (IGNORE) WHERE NOT EXISTS (SELECT id FROM commenti WHERE id = new.idarticolo); END;

-- Trigger: comments_check_update
CREATE TRIGGER comments_check_update BEFORE UPDATE ON commenti BEGIN select RAISE(FAIL,'Cannot move down this item to its own sub-tree!') WHERE NEW.idcommento IN (SELECT descendant FROM commenti_treepath WHERE ancestor = OLD.id); END;

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

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






//this plugin doesn't support full uninstallation
if (!$_POST['install'] || $_POST['install']=='false'){

	/****************
	* REMOVE FIELDS *
	*****************/
	//----------------------------------------------------------
	//not supported. SQLite doesn't have a good support for table editing.
	//only colum addition is easy enough to use.
	//for stability sake (and fast re-installation) there will be left some dirt
	//in tables: articoli, sito
	
	
	/*******************
	* UNINSTALL TABLES *
	********************/

	//----------------------------------------------------------
	//try to delete "commenti" table (and triggers)
	try {
		$queries =
			"PRAGMA foreign_keys = off;".
			"DROP table commenti;".
			"PRAGMA foreign_keys = on;";
		$pdo->beginTransaction();
		if ( false === $pdo->exec($queries) )
			jsonError("Table <commenti> couldn't be dropped!");
		$pdo->commit();
		
	} catch (Exception $e) {
		jsonError("Table <commenti> not found!");
	}


	//----------------------------------------------------------
	//try to delete "commenti_treepath" table
	try {
		$queries =
			"PRAGMA foreign_keys = off;".
			"DROP table commenti_treepath;".
			"PRAGMA foreign_keys = on;";
		$pdo->beginTransaction();
		if ( false === $pdo->exec($queries) )
			jsonError("Table <commenti> couldn't be dropped!");
		$pdo->commit();
		
	} catch (Exception $e) {
		jsonError("Table <commenti> not found!");
	}
	
	jsonSuccess();
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
"CREATE TRIGGER IF NOT EXISTS comments_insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id,new.id);
END;";

// Trigger: check_comment_exists (4)
$queries[] =
"CREATE TRIGGER IF NOT EXISTS comments_check_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	SELECT CASE WHEN NOT EXISTS (SELECT id FROM commenti WHERE id = new.idcommento) THEN RAISE (IGNORE) END;
END;";

// Trigger: insert_tree_path (5)
$queries[] =
"CREATE TRIGGER IF NOT EXISTS comments_insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant, depth)
		SELECT t.ancestor, NEW.id, (t.depth+1) FROM commenti_treepath t WHERE t.descendant = new.idcommento
		UNION ALL
		SELECT NEW.id, NEW.id, 0;
END;";

// Trigger: comments_update_parent (6)
$queries[] =
"CREATE TRIGGER comments_update_parent AFTER UPDATE ON commenti WHEN
NEW.idcommento AND OLD.idcommento and NEW.idcommento != OLD.idcommento
BEGIN 
DELETE FROM commenti_treepath
WHERE descendant IN (SELECT descendant
                     FROM commenti_treepath
                     WHERE ancestor = OLD.id)
    AND ancestor IN (SELECT ancestor
                     FROM commenti_treepath
                     WHERE descendant = OLD.id
                     AND ancestor != descendant);
INSERT INTO commenti_treepath (ancestor, descendant, depth)
SELECT supertree.ancestor, subtree.descendant, (supertree.depth + subtree.depth + 1)
FROM commenti_treepath AS supertree
CROSS JOIN commenti_treepath AS subtree
WHERE supertree.descendant = NEW.idcommento
AND subtree.ancestor = OLD.id;
END;";

// Trigger: comments_update_parent_to_null (7)
$queries[] =
"CREATE TRIGGER comments_update_parent_to_null AFTER UPDATE ON commenti WHEN
NEW.idcommento IS NULL AND OLD.idcommento
BEGIN DELETE FROM commenti_treepath
WHERE descendant IN (SELECT descendant
                     FROM commenti_treepath
                     WHERE ancestor = OLD.id)
    AND ancestor IN (SELECT ancestor
                     FROM commenti_treepath
                     WHERE descendant = OLD.id
                     AND ancestor != descendant);
END;";

//  Trigger: comments_update_parent_from_null (7)
$queries[] =
"CREATE TRIGGER comments_update_parent_from_null AFTER UPDATE ON commenti WHEN
NEW.idcommento IS NOT NULL AND OLD.idcommento IS NULL
BEGIN
INSERT INTO commenti_treepath (ancestor, descendant, depth)
SELECT supertree.ancestor, subtree.descendant, (supertree.depth + subtree.depth + 1)
FROM commenti_treepath AS supertree
CROSS JOIN commenti_treepath AS subtree
WHERE supertree.descendant = NEW.idcommento
AND subtree.ancestor = OLD.id;
END;"

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