<?php

/**
* Colibri > Comments installer
*
* edit the database preparing for this plugin (Colibri > Comments)
* - add table "comments"
* - add table "comments_treepath".
* - edit table "sito" to add global on/off variable
* - edit table "articoli" to add local on/off variable
* UN-INSTALLATION: not completely supported
*
* @param (bool) $_POST['install'] [optional] Ask to perform installation or unintallation
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



$InstallHelp = new \Colibri\InstallHelper;



//this plugin doesn't support full uninstallation
if (!$_POST['install'] || $_POST['install']=='false'){
	
	
	$TA = $InstallHelp->create_transaction();
	
	/*******************
	* UNINSTALL TABLES *
	********************/
	
	$TA->add_query("DROP table IF EXISTS commenti;");
	$TA->add_query("DROP table IF EXISTS commenti_treepath;");
	
	/****************
	* REMOVE FIELDS *
	*****************/
	//----------------------------------------------------------
	//not supported. SQLite doesn't have a good support for table editing.
	//only colum addition is easy enough to use.
	//for stability sake (and fast re-installation) there will be left some dirt
	//in tables: articoli, sito
	
	/*********************
	* UNINSTALL TRIGGERS *
	**********************/
	
	$TA->add_query("DROP trigger IF EXISTS comments_insert_first_branch;");
	$TA->add_query("DROP trigger IF EXISTS comments_check_exists;");
	$TA->add_query("DROP trigger IF EXISTS comments_insert_tree_path;");
	$TA->add_query("DROP trigger IF EXISTS comments_update_parent;");
	$TA->add_query("DROP trigger IF EXISTS comments_update_parent_to_null;");
	$TA->add_query("DROP trigger IF EXISTS comments_update_parent_from_null;");
	
	if (!$TA->run_queries()){
		jsonError('Uninstallation failed: '.implode(", ",$TA->errors));
	}
	else
		jsonSuccess();
}







/******************************
* TABLE AND TRIGGERS CREATION *
*******************************/

$TA = $InstallHelp->create_transaction();

//----------------- TABLES --------------------
// Table commenti (1)
$TA->add_query(
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
);");

// Table commenti_treepath (2)
$TA->add_query(
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
);");


//----------------- TRIGGERS --------------------
// Trigger: insert_first_branch (3)
$TA->add_query(
"CREATE TRIGGER IF NOT EXISTS comments_insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id,new.id);
END;");

// Trigger: check_comment_exists (4)
$TA->add_query(
"CREATE TRIGGER IF NOT EXISTS comments_check_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	SELECT CASE WHEN NOT EXISTS (SELECT id FROM commenti WHERE id = new.idcommento) THEN RAISE (IGNORE) END;
END;");

// Trigger: insert_tree_path (5)
$TA->add_query(
"CREATE TRIGGER IF NOT EXISTS comments_insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL
BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant, depth)
		SELECT t.ancestor, NEW.id, (t.depth+1) FROM commenti_treepath t WHERE t.descendant = new.idcommento
		UNION ALL
		SELECT NEW.id, NEW.id, 0;
END;");

// Trigger: comments_update_parent (6)
$TA->add_query(
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
END;");

// Trigger: comments_update_parent_to_null (7)
$TA->add_query(
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
END;");

//  Trigger: comments_update_parent_from_null (7)
$TA->add_query(
"CREATE TRIGGER comments_update_parent_from_null AFTER UPDATE ON commenti WHEN
NEW.idcommento IS NOT NULL AND OLD.idcommento IS NULL
BEGIN
INSERT INTO commenti_treepath (ancestor, descendant, depth)
SELECT supertree.ancestor, subtree.descendant, (supertree.depth + subtree.depth + 1)
FROM commenti_treepath AS supertree
CROSS JOIN commenti_treepath AS subtree
WHERE supertree.descendant = NEW.idcommento
AND subtree.ancestor = OLD.id;
END;");


if (!$TA->run_queries()){
	jsonError('Installation failed: '.implode(", ",$TA->errors));
}
else
	jsonSuccess();







/********************************
* EDIT CURRENT DATABASE COLUMNS *
*********************************/

$TA = $InstallHelp->create_transaction();
$TA->add_column('sito', 'comment_allow', 'BOOLEAN DEFAULT (1)');
$TA->add_column('sito', 'comment_class', 'INT DEFAULT (-1)');
$TA->add_column('articoli', 'comment_allow', 'BOOLEAN DEFAULT (1)');


if (!$TA->run_queries() && !empty($TA->queries())){
	jsonError('Installation failed: '.implode(", ",$TA->errors));
}
else
	jsonSuccess();

?>