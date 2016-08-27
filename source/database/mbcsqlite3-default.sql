--
-- File generated with SQLiteStudio v3.1.0 on sab ago 27 19:38:21 2016
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: immagini_albums
CREATE TABLE immagini_albums (id INTEGER PRIMARY KEY AUTOINCREMENT, titolo VARCHAR (100), idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE);

-- Table: immagini
CREATE TABLE immagini (id INTEGER PRIMARY KEY AUTOINCREMENT, src TEXT, descr VARCHAR (200), data DATETIME DEFAULT (CURRENT_TIMESTAMP), width INTEGER, height INTEGER);

-- Table: languages
CREATE TABLE languages (code VARCHAR (5) PRIMARY KEY UNIQUE ON CONFLICT REPLACE, name TEXT, supported BOOLEAN DEFAULT (0));

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'af',
                          'Afrikaans',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sq',
                          'Albanian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'am',
                          'Amharic',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-dz',
                          'Arabic (Algeria)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-bh',
                          'Arabic (Bahrain)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-eg',
                          'Arabic (Egypt)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-iq',
                          'Arabic (Iraq)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-jo',
                          'Arabic (Jordan)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-kw',
                          'Arabic (Kuwait)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-lb',
                          'Arabic (Lebanon)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-ly',
                          'Arabic (Libya)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-ma',
                          'Arabic (Morocco)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-om',
                          'Arabic (Oman)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-qa',
                          'Arabic (Qatar)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-sa',
                          'Arabic (Saudi Arabia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-sy',
                          'Arabic (Syria)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-tn',
                          'Arabic (Tunisia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-ae',
                          'Arabic (U.A.E.)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar-ye',
                          'Arabic (Yemen)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ar',
                          'Arabic',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'hy',
                          'Armenian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'as',
                          'Assamese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'az',
                          'Azeri',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'eu',
                          'Basque',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'be',
                          'Belarusian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'bn',
                          'Bengali',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'bs',
                          'Bosnian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'bg',
                          'Bulgarian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'my',
                          'Burmese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ca',
                          'Catalan',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh-cn',
                          'Chinese (China)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh-hk',
                          'Chinese (Hong Kong SAR)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh-mo',
                          'Chinese (Macau SAR)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh-sg',
                          'Chinese (Singapore)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh-tw',
                          'Chinese (Taiwan)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zh',
                          'Chinese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'hr',
                          'Croatian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'cs',
                          'Czech',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'da',
                          'Danish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'div',
                          'Divehi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'nl-be',
                          'Dutch (Belgium)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'nl',
                          'Dutch (Netherlands)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-au',
                          'English (Australia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-bz',
                          'English (Belize)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-ca',
                          'English (Canada)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-cb',
                          'English (Caribbean)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-in',
                          'English (India)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-ie',
                          'English (Ireland)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-jm',
                          'English (Jamaica)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-nz',
                          'English (New Zealand)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-ph',
                          'English (Philippines)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-za',
                          'English (South Africa)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-tt',
                          'English (Trinidad)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-gb',
                          'English (United Kingdom)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-us',
                          'English (United States)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en-zw',
                          'English (Zimbabwe)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'en',
                          'English',
                          1
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'et',
                          'Estonian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fo',
                          'Faeroese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fa',
                          'Farsi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fi',
                          'Finnish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr-be',
                          'Français (Belgique)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr-ca',
                          'Français (Canada)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr-lu',
                          'Français (Luxembourg)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr-mc',
                          'Français (Monaco)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr-ch',
                          'Français (Suisse)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'fr',
                          'Français',
                          1
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'mk',
                          'FYRO Macedonian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'gd',
                          'Gaelic',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ka',
                          'Georgian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'de-at',
                          'Deutsch (Österreich)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'de-li',
                          'Deutsch (Liechtenstein)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'de-lu',
                          'Deutsch (Luxemburg)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'de-ch',
                          'Deutsch (Schweiz)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'de',
                          'Deutsch',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'el',
                          'Greek',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'gn',
                          'Guarani (Paraguay)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'gu',
                          'Gujarati',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'he',
                          'Hebrew',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'hi',
                          'Hindi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'hu',
                          'Hungarian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'is',
                          'Icelandic',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'id',
                          'Indonesian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'it-ch',
                          'Italiano (Svizzera)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'it',
                          'Italiano',
                          1
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ja',
                          'Japanese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'kn',
                          'Kannada',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ks',
                          'Kashmiri',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'kk',
                          'Kazakh',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'km',
                          'Khmer',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'kok',
                          'Konkani',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ko',
                          'Korean',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'kz',
                          'Kyrgyz',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'lo',
                          'Lao',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'la',
                          'Latin',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'lv',
                          'Latvian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'lt',
                          'Lithuanian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ms-bn',
                          'Malay (Brunei)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ms-my',
                          'Malay (Malaysia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ms',
                          'Malay',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ml',
                          'Malayalam',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'mt',
                          'Maltese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'mi',
                          'Maori',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'mr',
                          'Marathi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'mn',
                          'Mongolian (Cyrillic)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ne',
                          'Nepali (India)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'nb-no',
                          'Norwegian (Bokmal)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'nn-no',
                          'Norwegian (Nynorsk)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'no',
                          'Norwegian (Bokmal)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'or',
                          'Oriya',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'pl',
                          'Polish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'pt-br',
                          'Português (Brasil)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'pt',
                          'Português',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'pa',
                          'Punjabi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'rm',
                          'Rhaeto-Romanic',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ro-md',
                          'Romanian (Moldova)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ro',
                          'Romanian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sa',
                          'Sanskrit',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sr',
                          'Serbian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sd',
                          'Sindhi',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'si',
                          'Sinhala',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sk',
                          'Slovak',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ls',
                          'Slovenian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'so',
                          'Somali',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sb',
                          'Sorbian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-ar',
                          'Español (Argentina)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-bo',
                          'Español (Bolivia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-cl',
                          'Español (Chile)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-co',
                          'Español (Colombia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-cr',
                          'Español (Costa Rica)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-do',
                          'Español (República Dominicana)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-ec',
                          'Español (Ecuador)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-sv',
                          'Español (El Salvador)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-gt',
                          'Español (Guatemala)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-hn',
                          'Español (Honduras)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-mx',
                          'Español (Méjico)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-ni',
                          'Español (Nicaragua)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-pa',
                          'Español (Panamá)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-py',
                          'Español (Paraguay)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-pe',
                          'Español (Perú)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-pr',
                          'Español (Puerto Rico)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-us',
                          'Español (Estados Unidos)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-uy',
                          'Español (Uruguay)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es-ve',
                          'Español (Venezuela)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'es',
                          'Español',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sx',
                          'Sutu',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sw',
                          'Swahili',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sv-fi',
                          'Swedish (Finland)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'sv',
                          'Swedish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'syr',
                          'Syriac',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'tg',
                          'Tajik',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ta',
                          'Tamil',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'tt',
                          'Tatar',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'te',
                          'Telugu',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'th',
                          'Thai',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'bo',
                          'Tibetan',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ts',
                          'Tsonga',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'tn',
                          'Tswana',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'tr',
                          'Turkish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'tk',
                          'Turkmen',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'uk',
                          'Ukrainian',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ur',
                          'Urdu',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'uz',
                          'Uzbek',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'vi',
                          'Vietnamese',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'cy',
                          'Welsh',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'xh',
                          'Xhosa',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'yi',
                          'Yiddish',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'zu',
                          'Zulu',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ru-md',
                          'Russian (Moldavia)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ru',
                          'Russian',
                          0
                      );

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

-- Table: youtube
CREATE TABLE youtube (id INTEGER PRIMARY KEY AUTOINCREMENT, videoid TEXT, videostart DECIMAL DEFAULT (0), videoend DECIMAL, videow INT DEFAULT (560), videoh INT DEFAULT (315), idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE ON UPDATE CASCADE);

-- Table: link_album_immagini
CREATE TABLE link_album_immagini (idalbum INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE SET NULL, idimage INTEGER REFERENCES immagini (id) ON DELETE CASCADE ON UPDATE CASCADE);

-- Table: sito
CREATE TABLE sito (id INTEGER PRIMARY KEY AUTOINCREMENT, autore TEXT DEFAULT ('Colibrì System'), titolo TEXT DEFAULT ('Il Colibrì'), descr TEXT DEFAULT ('Fast and Reliable CMS ever MadeByCambiamentico'), motto TEXT DEFAULT ('More than nothing, nothing More'), template TEXT DEFAULT ('colibri'), email TEXT, dataedit DATETIME DEFAULT (CURRENT_TIMESTAMP), info TEXT, default_lang VARCHAR (5) DEFAULT ('en') REFERENCES languages (code) ON DELETE SET DEFAULT ON UPDATE CASCADE, multilanguage BOOLEAN DEFAULT (0), user_subscription BOOLEAN DEFAULT (1), recaptcha_key TEXT, recaptcha_secret TEXT, delivery_quantity INT DEFAULT (3), delivery_delay INT DEFAULT (2));

-- Table: articoli
CREATE TABLE articoli (id INTEGER PRIMARY KEY AUTOINCREMENT, datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP), dataedit DATETIME DEFAULT (CURRENT_TIMESTAMP), titolo VARCHAR (50), remaplink TEXT COLLATE NOCASE, corpo TEXT COLLATE NOCASE, inbreve TEXT, isindex BOOLEAN DEFAULT (0), isinmenu BOOLEAN DEFAULT (0), idarticolo INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE, idalbum INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE, idimage INTEGER REFERENCES immagini (id) ON DELETE SET NULL ON UPDATE CASCADE, idtype INTEGER DEFAULT (1) REFERENCES articoli_types (id) ON DELETE SET NULL ON UPDATE CASCADE, isgarbage BOOLEAN DEFAULT (0), idowner INTEGER REFERENCES "utenti" (id) ON DELETE SET NULL ON UPDATE CASCADE, ideditor INTEGER REFERENCES "utenti" (id) ON DELETE SET NULL ON UPDATE CASCADE, lang VARCHAR (5) DEFAULT ('it'), isindexlang BOOLEAN DEFAULT (0), idarticololang INTEGER REFERENCES articoli (id) ON DELETE SET NULL ON UPDATE CASCADE);

-- Table: articoli_treepath
CREATE TABLE articoli_treepath (
    ancestor   INTEGER REFERENCES articoli (id) ON DELETE CASCADE
                                                ON UPDATE CASCADE,
    descendant INTEGER REFERENCES articoli (id) ON DELETE CASCADE
                                                ON UPDATE CASCADE,
    depth      INTEGER DEFAULT (0),
    PRIMARY KEY (
        ancestor,
        descendant
    )
);

-- Table: emails
CREATE TABLE emails (id INTEGER PRIMARY KEY AUTOINCREMENT, type INT, datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP), content TEXT NOT NULL ON CONFLICT IGNORE, iduser INTEGER REFERENCES "utenti" (id) ON DELETE CASCADE ON UPDATE CASCADE);

-- Table: articoli_types
CREATE TABLE articoli_types (id INTEGER PRIMARY KEY AUTOINCREMENT, protected BOOLEAN DEFAULT (0), nome TEXT COLLATE NOCASE, remapprefix VARCHAR (30) COLLATE NOCASE);

-- Trigger: trigger_prevet_double
CREATE TRIGGER trigger_prevet_double BEFORE INSERT ON link_album_immagini BEGIN SELECT CASE WHEN EXISTS(SELECT idalbum FROM link_album_immagini WHERE idalbum = NEW.idalbum AND idimage = NEW.idimage) THEN RAISE (IGNORE) END; END;

-- Trigger: article_update_parent_to_null
CREATE TRIGGER article_update_parent_to_null
         AFTER UPDATE
            ON articoli
          WHEN NEW.idarticolo IS NULL AND 
               OLD.idarticolo
BEGIN
    DELETE FROM articoli_treepath
          WHERE descendant IN (
                    SELECT descendant
                      FROM articoli_treepath
                     WHERE ancestor = OLD.id
                )
AND 
                ancestor IN (
        SELECT ancestor
          FROM articoli_treepath
         WHERE descendant = OLD.id AND 
               ancestor != descendant
    );
END;

-- Trigger: article_check_exists
CREATE TRIGGER article_check_exists INSERT ON articoli WHEN new.idarticolo IS NOT NULL      BEGIN SELECT RAISE (IGNORE) WHERE NOT EXISTS (SELECT id FROM articoli WHERE id = new.idarticolo); END;

-- Trigger: article_insert_first_branch
CREATE TRIGGER article_insert_first_branch INSERT ON articoli WHEN new.idarticolo IS NULL   BEGIN INSERT INTO articoli_treepath (ancestor, descendant) VALUES (new.id, new.id); END;

-- Trigger: article_insert_tree_path
CREATE TRIGGER article_insert_tree_path INSERT ON articoli WHEN new.idarticolo IS NOT NULL  BEGIN INSERT INTO articoli_treepath (ancestor, descendant, depth) SELECT t.ancestor, NEW.id, (t.depth + 1) FROM articoli_treepath t WHERE t.descendant = new.idarticolo UNION ALL SELECT NEW.id, NEW.id, 0; END;

-- Trigger: article_check_update
CREATE TRIGGER article_check_update BEFORE UPDATE ON articoli BEGIN SELECT RAISE(FAIL, 'Cannot move down this item to its own sub-tree!') 
     WHERE NEW.idarticolo IN (
               SELECT descendant
                 FROM articoli_treepath
                WHERE ancestor = OLD.id
           ); END;

-- Trigger: article_update_parent
CREATE TRIGGER article_update_parent
         AFTER UPDATE
            ON articoli
          WHEN NEW.idarticolo AND 
               OLD.idarticolo AND 
               NEW.idarticolo != OLD.idarticolo
BEGIN
    DELETE FROM articoli_treepath
          WHERE descendant IN (
                    SELECT descendant
                      FROM articoli_treepath
                     WHERE ancestor = OLD.id
                )
AND 
                ancestor IN (
        SELECT ancestor
          FROM articoli_treepath
         WHERE descendant = OLD.id AND 
               ancestor != descendant
    );
    INSERT INTO articoli_treepath (
                                      ancestor,
                                      descendant,
                                      depth
                                  )
                                  SELECT supertree.ancestor,
                                         subtree.descendant,
                                         (supertree.depth + subtree.depth + 1) 
                                    FROM articoli_treepath AS supertree
                                         CROSS JOIN
                                        articoli_treepath AS subtree
                                   WHERE supertree.descendant = NEW.idarticolo AND 
                                         subtree.ancestor = OLD.id;
END;

-- Trigger: article_update_parent_from_null
CREATE TRIGGER article_update_parent_from_null
         AFTER UPDATE
            ON articoli
          WHEN NEW.idarticolo IS NOT NULL AND 
               OLD.idarticolo IS NULL
BEGIN
    INSERT INTO articoli_treepath (
                                      ancestor,
                                      descendant,
                                      depth
                                  )
                                  SELECT supertree.ancestor,
                                         subtree.descendant,
                                         (supertree.depth + subtree.depth + 1) 
                                    FROM articoli_treepath AS supertree
                                         CROSS JOIN
                                         articoli_treepath AS subtree
                                   WHERE supertree.descendant = NEW.idarticolo AND 
                                         subtree.ancestor = OLD.id;
END;

-- View: view_menu
CREATE VIEW view_menu AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    isinmenu AND
    NOT isgarbage
)
    AND art.isinmenu
    AND NOT art.isgarbage
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_menu_1_levels
CREATE VIEW view_menu_1_levels AS SELECT * from articles WHERE NOT isgarbage AND idarticolo IS NULL AND isinmenu;

-- View: view_menu_2_levels
CREATE VIEW view_menu_2_levels AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    isinmenu AND
    NOT isgarbage
)
    AND art.isinmenu
    AND NOT art.isgarbage
    AND tree.depth <= 1
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_available_maps
CREATE VIEW view_available_maps AS select id, remapprefix as 'remap' from articoli_types;

-- View: view_articles
CREATE VIEW view_articles AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    NOT isgarbage
)
AND NOT art.isgarbage
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_all_main_sub_art
CREATE VIEW view_all_main_sub_art AS SELECT a.id as 'parentid', b.id,b.remaplink,b.titolo FROM articoli a
INNER JOIN articoli b ON b.idarticolo = a.id
WHERE a.idtype = 1 AND NOT a.isgarbage AND NOT b.isgarbage AND a.idarticolo IS NULL AND b.idtype = 1
ORDER BY a.id DESC, b.id ASC;

-- View: view_articles_3_levels
CREATE VIEW view_articles_3_levels AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    NOT isgarbage
)
    AND tree.depth <= 2
    AND NOT art.isgarbage
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_menu_3_levels
CREATE VIEW view_menu_3_levels AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    isinmenu AND
    NOT isgarbage
)
    AND art.isinmenu
    AND NOT art.isgarbage
    AND tree.depth <= 2
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_articles_2_levels
CREATE VIEW view_articles_2_levels AS SELECT
    art.*,
    tree.depth,
    GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
    SELECT id FROM articoli WHERE
    idarticolo IS NULL AND
    NOT isgarbage
)
    AND tree.depth <= 1
    AND NOT art.isgarbage
GROUP BY art.id
ORDER BY breadcrumbs;

-- View: view_articles_1_levels
CREATE VIEW view_articles_1_levels AS SELECT * from articles WHERE NOT isgarbage AND idarticolo IS NULL;

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
