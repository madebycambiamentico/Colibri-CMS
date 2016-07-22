--
-- File generated with SQLiteStudio v3.x
--
-- Text encoding used: windows-1252
--



PRAGMA foreign_keys = off;
BEGIN TRANSACTION;



-- Table: articoli_types
CREATE TABLE articoli_types (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	protected BOOLEAN DEFAULT (0),
	nome TEXT COLLATE NOCASE,
	remapprefix VARCHAR (30) COLLATE NOCASE
);
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



-- Table: languages
CREATE TABLE languages (
	code VARCHAR (5) PRIMARY KEY UNIQUE ON CONFLICT IGNORE,
	name TEXT,
	supported BOOLEAN DEFAULT false
);
INSERT INTO languages (code, name, supported) VALUES
	('af', 'Afrikaans', 0),
	('sq', 'Albanian', 0),
	('am', 'Amharic', 0),
	('ar-dz', 'Arabic (Algeria)', 0),
	('ar-bh', 'Arabic (Bahrain)', 0),
	('ar-eg', 'Arabic (Egypt)', 0),
	('ar-iq', 'Arabic (Iraq)', 0),
	('ar-jo', 'Arabic (Jordan)', 0),
	('ar-kw', 'Arabic (Kuwait)', 0),
	('ar-lb', 'Arabic (Lebanon)', 0),
	('ar-ly', 'Arabic (Libya)', 0),
	('ar-ma', 'Arabic (Morocco)', 0),
	('ar-om', 'Arabic (Oman)', 0),
	('ar-qa', 'Arabic (Qatar)', 0),
	('ar-sa', 'Arabic (Saudi Arabia)', 0),
	('ar-sy', 'Arabic (Syria)', 0),
	('ar-tn', 'Arabic (Tunisia)', 0),
	('ar-ae', 'Arabic (U.A.E.)', 0),
	('ar-ye', 'Arabic (Yemen)', 0),
	('ar', 'Arabic', 0),
	('hy', 'Armenian', 0),
	('as', 'Assamese', 0),
	('az', 'Azeri', 0),
	('eu', 'Basque', 0),
	('be', 'Belarusian', 0),
	('bn', 'Bengali', 0),
	('bs', 'Bosnian', 0),
	('bg', 'Bulgarian', 0),
	('my', 'Burmese', 0),
	('ca', 'Catalan', 0),
	('zh-cn', 'Chinese (China)', 0),
	('zh-hk', 'Chinese (Hong Kong SAR)', 0),
	('zh-mo', 'Chinese (Macau SAR)', 0),
	('zh-sg', 'Chinese (Singapore)', 0),
	('zh-tw', 'Chinese (Taiwan)', 0),
	('zh', 'Chinese', 0),
	('hr', 'Croatian', 0),
	('cs', 'Czech', 0),
	('da', 'Danish', 0),
	('div', 'Divehi', 0),
	('nl-be', 'Dutch (Belgium)', 0),
	('nl', 'Dutch (Netherlands)', 0),
	('en-au', 'English (Australia)', 0),
	('en-bz', 'English (Belize)', 0),
	('en-ca', 'English (Canada)', 0),
	('en-cb', 'English (Caribe)', 0),
	('en-in', 'English (India)', 0),
	('en-ie', 'English (Ireland)', 0),
	('en-jm', 'English (Jamaica)', 0),
	('en-nz', 'English (New Zealand)', 0),
	('en-ph', 'English (Philippines)', 0),
	('en-za', 'English (South Africa)', 0),
	('en-tt', 'English (Trinidad)', 0),
	('en-gb', 'English (United Kingdom)', 0),
	('en-us', 'English (United States)', 0),
	('en-zw', 'English (Zimbabwe)', 0),
	('en', 'English', 0),
	('et', 'Estonian', 0),
	('fo', 'Faeroese', 0),
	('fa', 'Farsi', 0),
	('fi', 'Finnish', 0),
	('fr-be', 'Français (Belgique)', 0),
	('fr-ca', 'Français (Canada)', 0),
	('fr-lu', 'Français (Luxembourg)', 0),
	('fr-mc', 'Français (Monaco)', 0),
	('fr-ch', 'Français (Suisse)', 0),
	('fr', 'Français', 0),
	('mk', 'FYRO Macedonian', 0),
	('gd', 'Gaelic', 0),
	('ka', 'Georgian', 0),
	('de-at', 'Deutsch (Österreich)', 0),
	('de-li', 'Deutsch (Liechtenstein)', 0),
	('de-lu', 'Deutsch (Luxemburg)', 0),
	('de-ch', 'Deutsch (Schweiz)', 0),
	('de', 'Deutsch', 0),
	('el', 'Greek', 0),
	('gn', 'Guarani (Paraguay)', 0),
	('gu', 'Gujarati', 0),
	('he', 'Hebrew', 0),
	('hi', 'Hindi', 0),
	('hu', 'Hungarian', 0),
	('is', 'Icelandic', 0),
	('id', 'Indonesian', 0),
	('it-ch', 'Italiano (Svizzera)', 0),
	('it', 'Italiano', 0),
	('ja', 'Japanese', 0),
	('kn', 'Kannada', 0),
	('ks', 'Kashmiri', 0),
	('kk', 'Kazakh', 0),
	('km', 'Khmer', 0),
	('kok', 'Konkani', 0),
	('ko', 'Korean', 0),
	('kz', 'Kyrgyz', 0),
	('lo', 'Lao', 0),
	('la', 'Latin', 0),
	('lv', 'Latvian', 0),
	('lt', 'Lithuanian', 0),
	('ms-bn', 'Malay (Brunei)', 0),
	('ms-my', 'Malay (Malaysia)', 0),
	('ms', 'Malay', 0),
	('ml', 'Malayalam', 0),
	('mt', 'Maltese', 0),
	('mi', 'Maori', 0),
	('mr', 'Marathi', 0),
	('mn', 'Mongolian (Cyrillic)', 0),
	('ne', 'Nepali (India)', 0),
	('nb-no', 'Norwegian (Bokmal)', 0),
	('nn-no', 'Norwegian (Nynorsk)', 0),
	('no', 'Norwegian (Bokmal)', 0),
	('or', 'Oriya', 0),
	('pl', 'Polish', 0),
	('pt-br', 'Português (Brasil)', 0),
	('pt', 'Português', 0),
	('pa', 'Punjabi', 0),
	('rm', 'Rhaeto-Romanic', 0),
	('ro-md', 'Romanian (Moldova)', 0),
	('ro', 'Romanian', 0),
	('ru-md', 'русский (Молдова)', 0),
	('ru', 'русский', 0),
	('sa', 'Sanskrit', 0),
	('sr', 'Serbian', 0),
	('sd', 'Sindhi', 0),
	('si', 'Sinhala', 0),
	('sk', 'Slovak', 0),
	('ls', 'Slovenian', 0),
	('so', 'Somali', 0),
	('sb', 'Sorbian', 0),
	('es-ar', 'Español (Argentina)', 0),
	('es-bo', 'Español (Bolivia)', 0),
	('es-cl', 'Español (Chile)', 0),
	('es-co', 'Español (Colombia)', 0),
	('es-cr', 'Español (Costa Rica)', 0),
	('es-do', 'Español (República Dominicana)', 0),
	('es-ec', 'Español (Ecuador)', 0),
	('es-sv', 'Español (El Salvador)', 0),
	('es-gt', 'Español (Guatemala)', 0),
	('es-hn', 'Español (Honduras)', 0),
	('es-mx', 'Español (Méjico)', 0),
	('es-ni', 'Español (Nicaragua)', 0),
	('es-pa', 'Español (Panamá)', 0),
	('es-py', 'Español (Paraguay)', 0),
	('es-pe', 'Español (Perú)', 0),
	('es-pr', 'Español (Puerto Rico)', 0),
	('es-us', 'Español (Estados Unidos)', 0),
	('es-uy', 'Español (Uruguay)', 0),
	('es-ve', 'Español (Venezuela)', 0),
	('es', 'Español', 0),
	('sx', 'Sutu', 0),
	('sw', 'Swahili', 0),
	('sv-fi', 'Swedish (Finland)', 0),
	('sv', 'Swedish', 0),
	('syr', 'Syriac', 0),
	('tg', 'Tajik', 0),
	('ta', 'Tamil', 0),
	('tt', 'Tatar', 0),
	('te', 'Telugu', 0),
	('th', 'Thai', 0),
	('bo', 'Tibetan', 0),
	('ts', 'Tsonga', 0),
	('tn', 'Tswana', 0),
	('tr', 'Turkish', 0),
	('tk', 'Turkmen', 0),
	('uk', 'Ukrainian', 0),
	('ur', 'Urdu', 0),
	('uz', 'Uzbek', 0),
	('vi', 'Vietnamese', 0),
	('cy', 'Welsh', 0),
	('xh', 'Xhosa', 0),
	('yi', 'Yiddish', 0),
	('zu', 'Zulu', 0);





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
	isgarbage BOOLEAN DEFAULT (0),
	idowner INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	ideditor INTEGER REFERENCES utenti (id) ON DELETE SET NULL ON UPDATE CASCADE,
	lang VARCHAR (5) DEFAULT ('it'),
	isindexlang BOOLEAN DEFAULT (0),
	idarticololang INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE
);





-- Table: emails
CREATE TABLE emails (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	type INT,
	datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP),
	content TEXT NOT NULL ON CONFLICT IGNORE,
	iduser INTEGER REFERENCES utenti (id) ON DELETE CASCADE ON UPDATE CASCADE
);





-- Table: immagini
CREATE TABLE immagini (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	src TEXT,
	descr VARCHAR (200),
	data DATETIME DEFAULT (CURRENT_TIMESTAMP),
	width INTEGER,
	height INTEGER
);




-- Table: immagini_albums
CREATE TABLE immagini_albums (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	titolo VARCHAR (100),
	idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE
);




-- Table: link_album_immagini
CREATE TABLE link_album_immagini (
	idalbum INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE SET NULL,
	idimage INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE CASCADE
);

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
	default_lang VARCHAR (5) DEFAULT ('en') REFERENCES languages (code) ON DELETE SET DEFAULT ON UPDATE CASCADE,
	multilanguage BOOLEAN DEFAULT (0),
	user_subscription BOOLEAN DEFAULT (1),
	recaptcha_key TEXT,
	recaptcha_secret TEXT,
	delivery_quantity INT DEFAULT (3),
	delivery_delay INT DEFAULT (2)
);
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

-- View: view_template_maps
CREATE VIEW view_template_maps AS SELECT id, remapprefix as remap FROM articoli_types;










COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

