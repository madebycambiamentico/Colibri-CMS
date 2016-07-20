
--
-- File generated with SQLiteStudio v3.0.7 on lun lug 18 23:39:13 2016
--
-- Text encoding used: windows-1252
--



PRAGMA foreign_keys = off;
BEGIN TRANSACTION;



-- Table: articoli_types
CREATE TABLE articoli_types (id INTEGER PRIMARY KEY AUTOINCREMENT, protected BOOLEAN DEFAULT (0), nome TEXT COLLATE NOCASE, remapprefix VARCHAR (30) COLLATE NOCASE);
INSERT INTO articoli_types (id, protected, nome, remapprefix) VALUES (1, 1, 'pagine principali', '');
INSERT INTO articoli_types (id, protected, nome, remapprefix) VALUES (2, 1, 'news', 'news');
INSERT INTO articoli_types (id, protected, nome, remapprefix) VALUES (3, 1, 'links', 'links');



-- Table: commenti
CREATE TABLE commenti (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	content TEXT COLLATE NOCASE,
	idutente INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	idcommento INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE
);




-- Table: commenti_treepath
CREATE TABLE commenti_treepath (
	ancestor INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	descendant INTEGER REFERENCES commenti (id) ON DELETE CASCADE ON UPDATE CASCADE,
	depth INTEGER DEFAULT (0),
	PRIMARY KEY (ancestor,descendant)
) WITHOUT ROWID;




-- Trigger: insert_first_branch
CREATE TRIGGER insert_first_branch AFTER INSERT ON commenti WHEN new.idcommento IS NULL BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant) VALUES (new.id,new.id);
END;

-- Trigger: check_comment_exists
CREATE TRIGGER check_comment_exists BEFORE INSERT ON commenti WHEN new.idcommento IS NOT NULL BEGIN
	SELECT CASE WHEN NOT EXISTS (SELECT id FROM commenti WHERE id = new.idcommento) THEN RAISE (IGNORE) END;
END;

-- Trigger: insert_tree_path
CREATE TRIGGER insert_tree_path AFTER INSERT ON commenti WHEN new.idcommento IS NOT NULL BEGIN
	INSERT INTO commenti_treepath (ancestor, descendant, depth)
		SELECT t.ancestor, NEW.id, (t.depth+1) FROM commenti_treepath t WHERE t.descendant = new.idcommento
		UNION ALL
		SELECT NEW.id, NEW.id, 0;
END;





-- Table: articoli
CREATE TABLE articoli (id INTEGER PRIMARY KEY AUTOINCREMENT, datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP), dataedit DATETIME DEFAULT (CURRENT_TIMESTAMP), titolo VARCHAR (50), remaplink TEXT COLLATE NOCASE, corpo TEXT COLLATE NOCASE, inbreve TEXT, isindex BOOLEAN DEFAULT (0), isinmenu BOOLEAN DEFAULT (0), idarticolo INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE, idalbum INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE, idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE, idtype INTEGER DEFAULT (1) REFERENCES articoli_types (id) ON DELETE SET NULL ON UPDATE CASCADE, isgarbage BOOLEAN DEFAULT (0), idowner INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE, ideditor INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE, lang VARCHAR (5) DEFAULT ('it'), isindexlang BOOLEAN DEFAULT (0), idarticololang INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE);





-- Table: emails
CREATE TABLE emails (id INTEGER PRIMARY KEY AUTOINCREMENT, type INT, datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP), content TEXT NOT NULL ON CONFLICT IGNORE, iduser INTEGER REFERENCES utenti (id) ON DELETE CASCADE ON UPDATE CASCADE);





-- Table: immagini
CREATE TABLE immagini (id INTEGER PRIMARY KEY AUTOINCREMENT, src TEXT, descr VARCHAR (200), data DATETIME DEFAULT (CURRENT_TIMESTAMP), width INTEGER, height INTEGER);




-- Table: immagini_albums
CREATE TABLE immagini_albums (id INTEGER PRIMARY KEY AUTOINCREMENT, titolo VARCHAR (100), idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE);




-- Table: link_album_immagini
CREATE TABLE link_album_immagini (idalbum INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE SET NULL, idimage INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE CASCADE);

-- Trigger: trigger_prevet_double
CREATE TRIGGER trigger_prevet_double BEFORE INSERT ON link_album_immagini BEGIN SELECT CASE WHEN EXISTS(SELECT idalbum FROM link_album_immagini WHERE idalbum = NEW.idalbum AND idimage = NEW.idimage) THEN RAISE (IGNORE) END; END;





-- Table: sito
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
INSERT INTO sito (id, autore, titolo, descr, motto, template, email, dataedit, info, recaptcha_key, recaptcha_secret, multilanguage, delivery_quantity, delivery_delay) VALUES (1, 'Colibrì CMS System', 'Il Colibrì', 'Fast and Reliable CMS ever MadeByCambiamentico', 'More than nothing, nothing More', 'colibri', '', '2016-02-20 14:30:56', 'Colibri-CMS System', '', '', 0, 0, 0);




-- Table: utenti
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
-- default user: "admin", password: "admin"
INSERT INTO utenti (id, classe, nome, email, datacreaz, hasimage, salt, pass, passhint, about, flags) VALUES (1, 2, 'admin', NULL, '2016-07-16 16:30:20', 0, 'fc1ac4c04d8cdc29f95db03d2f597ba4cf32d8b7f6ed6dd4fbef95ff4a76a8c0a9e7dee2791a05250527d0af2b69fee88afcffe465841637f5d8968096d0235c', 'eaa7b8c19eb82272450153bf16a52d645aec6748c72a73ded1e90431fca1cc6232ad05915200de85e3c7fcb78ca493683d337b08953f6d1d0bb4631868dc9172', NULL, NULL, 0);










-- View: view_all_main_sub_art
CREATE VIEW view_all_main_sub_art AS SELECT a.id as 'parentid', b.id,b.remaplink,b.titolo FROM articoli a
INNER JOIN articoli b ON b.idarticolo = a.id
WHERE a.idtype = 1 AND NOT a.isgarbage AND NOT b.isgarbage AND a.idarticolo IS NULL AND b.idtype = 1
ORDER BY a.id DESC, b.id ASC;

-- View: view_menu
CREATE VIEW view_menu AS SELECT a.id as 'parentid', b.id,b.remaplink,b.titolo FROM articoli a
INNER JOIN articoli b ON b.idarticolo = a.id OR (b.idarticolo IS NULL AND b.id = a.id)
WHERE a.isinmenu AND NOT a.isgarbage AND NOT b.isgarbage AND a.idarticolo IS NULL
ORDER BY parentid DESC, b.idarticolo ASC;

-- View: view_template_maps
CREATE VIEW view_template_maps AS SELECT id, remapprefix as remap FROM articoli_types;










COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

