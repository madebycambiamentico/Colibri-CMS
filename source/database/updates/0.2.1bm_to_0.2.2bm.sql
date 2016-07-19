--
-- this is an update from 0.2.1 beta m to 0.2.2 beta m.
--

-- temporary disable foreign keys (to prevent error on edits)
PRAGMA foreign_keys = OFF;





-- edit utenti
-- adds the "about" for public users profile.

ALTER TABLE utenti RENAME TO sqlitestudio_temp_table;

CREATE TABLE utenti (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	classe INT DEFAULT (-1) NOT NULL,
	nome VARCHAR (100),
	email TEXT,
	datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP),
	hasimage BOOLEAN DEFAULT (0),
	salt CHAR (128),
	pass CHAR (128),
	passhint TEXT,
	about TEXT COLLATE NOCASE,
	flags INT DEFAULT (0)
);

INSERT INTO utenti (id, classe, nome, email, datacreaz, hasimage, salt, pass, passhint) SELECT id, class, nome, email, datacreaz, hasimage, salt, pass, passhint FROM sqlitestudio_temp_table;






-- edit articles:
-- "lang" is CHAR(5) istead of CHAR(2)
-- "data" become "datacreaz"

ALTER TABLE articoli RENAME TO sqlitestudio_temp_table0;

CREATE TABLE articoli (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP),
	dataedit DATETIME DEFAULT (CURRENT_TIMESTAMP),
	titolo VARCHAR (50),
	remaplink TEXT COLLATE NOCASE,
	corpo TEXT COLLATE NOCASE,
	inbreve TEXT,
	isindex BOOLEAN DEFAULT (0),
	isinmenu BOOLEAN DEFAULT (0),
	idarticolo INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE,
	idalbum INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE,
	idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE,
	idtype INTEGER DEFAULT (1) REFERENCES articoli_types (id) ON DELETE SET NULL ON UPDATE CASCADE,
	isgarbage BOOLEAN DEFAULT (0), idowner INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	ideditor INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	lang VARCHAR (5) DEFAULT ('it'),
	isindexlang BOOLEAN DEFAULT (0),
	idarticololang INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT INTO articoli (id, datacreaz, dataedit, titolo, remaplink, corpo, inbreve, isindex, isinmenu, idarticolo, idalbum, idimage, idtype, isgarbage, idowner, ideditor, lang, isindexlang, idarticololang) SELECT id, data, dataedit, titolo, remaplink, corpo, inbreve, isindex, isinmenu, idarticolo, idalbum, idimage, idtype, isgarbage, idowner, ideditor, lang, isindexlang, idarticololang FROM sqlitestudio_temp_table0;





-- edit sito:
-- added "delivery_quantity" and "delivery_delay", edited "data" -> "dataedit"

ALTER TABLE sito RENAME TO sqlitestudio_temp_table1;

CREATE TABLE sito (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	autore TEXT DEFAULT ('Colibrì System'),
	titolo TEXT DEFAULT ('Il Colibrì'),
	descr TEXT DEFAULT ('Fast and Reliable CMS ever MadeByCambiamentico'),
	motto TEXT DEFAULT ('More than nothing, nothing More'),
	template TEXT DEFAULT ('colibri'),
	email TEXT,
	dataedit DATETIME DEFAULT (CURRENT_TIMESTAMP),
	info TEXT,
	recaptcha_key TEXT,
	recaptcha_secret TEXT,
	multilanguage BOOLEAN DEFAULT (0),
	delivery_quantity INT DEFAULT (3),
	delivery_delay INT DEFAULT (2));

INSERT INTO sito (id, autore, titolo, descr, motto, template, email, dataedit, info, recaptcha_key, recaptcha_secret, multilanguage) SELECT id, autore, titolo, descr, motto, template, email, data, info, recaptcha_key, recaptcha_secret, multilanguage FROM sqlitestudio_temp_table1;




-- insert new table: commenti + treepath (use of closure table) + triggers

CREATE TABLE IF NOT EXISTS commenti (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	content TEXT COLLATE NOCASE,
	idutente INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	idcommento INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE commenti_treepath (
	ancestor INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	descendant INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	depth INTEGER DEFAULT (0),
	PRIMARY KEY (ancestor,descendant)
) WITHOUT ROWID;

CREATE TRIGGER IF NOT EXISTS insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id,new.id);
END;

CREATE TRIGGER IF NOT EXISTS check_comment_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL BEGIN
	SELECT CASE WHEN NOT EXISTS (SELECT id FROM commenti WHERE id = new.idcommento) THEN RAISE (IGNORE) END;
END;

CREATE TRIGGER IF NOT EXISTS insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant, depth)
		SELECT t.ancestor, NEW.id, (t.depth+1) FROM commenti_treepath t WHERE t.descendant = new.idcommento
		UNION ALL
		SELECT NEW.id, NEW.id, 0;
END;





-- emails
CREATE TABLE emails (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	type INT,
	datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP),
	content TEXT NOT NULL ON CONFLICT IGNORE,
	iduser INTEGER REFERENCES utenti (id) ON DELETE CASCADE ON UPDATE CASCADE
);




DROP TABLE sqlitestudio_temp_table1;

DROP TABLE sqlitestudio_temp_table0;

DROP TABLE sqlitestudio_temp_table;

-- re-enable foreign keys
PRAGMA foreign_keys = ON;