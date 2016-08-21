--
-- File generated with SQLiteStudio v3.1.0 on dom ago 21 16:30:25 2016
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: languages
DROP TABLE IF EXISTS languages;

CREATE TABLE languages (
    code      VARCHAR (5) PRIMARY KEY
                          UNIQUE ON CONFLICT REPLACE,
    name      TEXT,
    supported BOOLEAN     DEFAULT (0) 
);

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
                          '??????? (???????)',
                          0
                      );

INSERT INTO languages (
                          code,
                          name,
                          supported
                      )
                      VALUES (
                          'ru',
                          '???????',
                          0
                      );


-- Table: link_album_immagini
DROP TABLE IF EXISTS link_album_immagini;

CREATE TABLE link_album_immagini (
    idalbum INTEGER REFERENCES immagini (id) ON DELETE CASCADE
                                             ON UPDATE SET NULL,
    idimage INTEGER REFERENCES immagini (id) ON DELETE CASCADE
                                             ON UPDATE CASCADE
);


-- Table: youtube
DROP TABLE IF EXISTS youtube;

CREATE TABLE youtube (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    videoid    TEXT,
    videostart DECIMAL DEFAULT (0),
    videoend   DECIMAL,
    videow     INT     DEFAULT (560),
    videoh     INT     DEFAULT (315),
    idarticolo INTEGER REFERENCES articoli (id) ON DELETE CASCADE
                                                ON UPDATE CASCADE
);


-- Table: utenti
DROP TABLE IF EXISTS utenti;

CREATE TABLE utenti (
    id        INTEGER       PRIMARY KEY AUTOINCREMENT,
    classe    INT           DEFAULT (-1) 
                            NOT NULL,
    nome      VARCHAR (100),
    email     TEXT,
    datacreaz DATETIME      DEFAULT (CURRENT_TIMESTAMP),
    hasimage  BOOLEAN       DEFAULT (0),
    salt      CHAR (128),
    pass      CHAR (128),
    passhint  TEXT,
    about     TEXT          COLLATE NOCASE,
    flags     INT           DEFAULT (0) 
);

INSERT INTO utenti (
                       id,
                       classe,
                       nome,
                       email,
                       datacreaz,
                       hasimage,
                       salt,
                       pass,
                       passhint,
                       about,
                       flags
                   )
                   VALUES (
                       1,
                       2,
                       'admin',
                       NULL,
                       '2016-07-16 16:30:20',
                       1,
                       '0c1c34d1ec3ca8aa3d41597cfc417200612d1f1b98f0a26eb8fd3b65fcba0814dcdd7fc129f2c2817da2dadaef4fc77cec551203b4192614bead3fcb3c119448',
                       '63b069e3cc4cac9bffd076397827ce810b4aca778a816dddf449106eedaf8e8799133ccdc88e0f5b3293d4cde8b63d65be525b2e5f8574674132117aecbefa39',
                       'default password:
colibrì cms',
                       NULL,
                       0
                   );


-- Table: sito
DROP TABLE IF EXISTS sito;

CREATE TABLE sito (
    id                INTEGER     PRIMARY KEY AUTOINCREMENT,
    autore            TEXT        DEFAULT ('Colibrì System'),
    titolo            TEXT        DEFAULT ('Il Colibrì'),
    descr             TEXT        DEFAULT ('Fast and Reliable CMS ever MadeByCambiamentico'),
    motto             TEXT        DEFAULT ('More than nothing, nothing More'),
    template          TEXT        DEFAULT ('colibri'),
    email             TEXT,
    dataedit          DATETIME    DEFAULT (CURRENT_TIMESTAMP),
    info              TEXT,
    default_lang      VARCHAR (5) DEFAULT ('en') 
                                  REFERENCES languages (code) ON DELETE SET DEFAULT
                                                              ON UPDATE CASCADE,
    multilanguage     BOOLEAN     DEFAULT (0),
    user_subscription BOOLEAN     DEFAULT (1),
    recaptcha_key     TEXT,
    recaptcha_secret  TEXT,
    delivery_quantity INT         DEFAULT (3),
    delivery_delay    INT         DEFAULT (2) 
);

INSERT INTO sito (
                     id,
                     autore,
                     titolo,
                     descr,
                     motto,
                     template,
                     email,
                     dataedit,
                     info,
                     default_lang,
                     multilanguage,
                     user_subscription,
                     recaptcha_key,
                     recaptcha_secret,
                     delivery_quantity,
                     delivery_delay
                 )
                 VALUES (
                     1,
                     'M.B.C. (Nereo Costacurta)',
                     'Il Colibrì',
                     'Fast and Reliable CMS ever MadeByCambiamentico',
                     'More than nothing, nothing More',
                     'colibri',
                     '',
                     '2016-02-20 14:30:56',
                     'Released under GPL v.3 license.



Colibrì CMS by Nereo Costacurta



MadeByCambiamentico!









(BANG!)',
                     'it',
                     '',
                     1,
                     '',
                     '',
                     2,
                     3
                 );


-- Table: immagini_albums
DROP TABLE IF EXISTS immagini_albums;

CREATE TABLE immagini_albums (
    id      INTEGER       PRIMARY KEY AUTOINCREMENT,
    titolo  VARCHAR (100),
    idimage INTEGER       REFERENCES immagini (id) ON DELETE SET NULL
                                                   ON UPDATE CASCADE
);


-- Table: articoli_types
DROP TABLE IF EXISTS articoli_types;

CREATE TABLE articoli_types (
    id          INTEGER      PRIMARY KEY AUTOINCREMENT,
    protected   BOOLEAN      DEFAULT (0),
    nome        TEXT         COLLATE NOCASE,
    remapprefix VARCHAR (30) COLLATE NOCASE
);

INSERT INTO articoli_types (
                               id,
                               protected,
                               nome,
                               remapprefix
                           )
                           VALUES (
                               1,
                               1,
                               'pagine principali',
                               ''
                           );

INSERT INTO articoli_types (
                               id,
                               protected,
                               nome,
                               remapprefix
                           )
                           VALUES (
                               2,
                               1,
                               'news',
                               'news'
                           );

INSERT INTO articoli_types (
                               id,
                               protected,
                               nome,
                               remapprefix
                           )
                           VALUES (
                               3,
                               1,
                               'links',
                               'links'
                           );


-- Table: articoli
DROP TABLE IF EXISTS articoli;

CREATE TABLE articoli (
    id             INTEGER      PRIMARY KEY AUTOINCREMENT,
    datacreaz      DATETIME     DEFAULT (CURRENT_TIMESTAMP),
    dataedit       DATETIME     DEFAULT (CURRENT_TIMESTAMP),
    titolo         VARCHAR (50),
    remaplink      TEXT         COLLATE NOCASE,
    corpo          TEXT         COLLATE NOCASE,
    inbreve        TEXT,
    isindex        BOOLEAN      DEFAULT (0),
    isinmenu       BOOLEAN      DEFAULT (0),
    idarticolo     INTEGER      REFERENCES articoli (id) ON DELETE SET NULL
                                                         ON UPDATE CASCADE,
    idalbum        INTEGER      REFERENCES immagini (id) ON DELETE SET NULL
                                                         ON UPDATE CASCADE,
    idimage        INTEGER      REFERENCES immagini (id) ON DELETE SET NULL
                                                         ON UPDATE CASCADE,
    idtype         INTEGER      DEFAULT (1) 
                                REFERENCES articoli_types (id) ON DELETE SET NULL
                                                               ON UPDATE CASCADE,
    isgarbage      BOOLEAN      DEFAULT (0),
    idowner        INTEGER      REFERENCES utenti (id) ON DELETE SET NULL
                                                       ON UPDATE CASCADE,
    ideditor       INTEGER      REFERENCES utenti (id) ON DELETE SET NULL
                                                       ON UPDATE CASCADE,
    lang           VARCHAR (5)  DEFAULT ('it'),
    isindexlang    BOOLEAN      DEFAULT (0),
    idarticololang INTEGER      REFERENCES articoli (id) ON DELETE SET NULL
                                                         ON UPDATE CASCADE
);


-- Table: emails
DROP TABLE IF EXISTS emails;

CREATE TABLE emails (
    id        INTEGER  PRIMARY KEY AUTOINCREMENT,
    type      INT,
    datacreaz DATETIME DEFAULT (CURRENT_TIMESTAMP),
    content   TEXT     NOT NULL ON CONFLICT IGNORE,
    iduser    INTEGER  REFERENCES utenti (id) ON DELETE CASCADE
                                              ON UPDATE CASCADE
);


-- Table: immagini
DROP TABLE IF EXISTS immagini;

CREATE TABLE immagini (
    id     INTEGER       PRIMARY KEY AUTOINCREMENT,
    src    TEXT,
    descr  VARCHAR (200),
    data   DATETIME      DEFAULT (CURRENT_TIMESTAMP),
    width  INTEGER,
    height INTEGER
);


-- Trigger: trigger_prevet_double
DROP TRIGGER IF EXISTS trigger_prevet_double;
CREATE TRIGGER trigger_prevet_double
        BEFORE INSERT
            ON link_album_immagini
BEGIN
    SELECT CASE WHEN EXISTS (
                   SELECT idalbum
                     FROM link_album_immagini
                    WHERE idalbum = NEW.idalbum AND 
                          idimage = NEW.idimage
               )
           THEN RAISE(IGNORE) END;
END;


-- View: view_menu
DROP VIEW IF EXISTS view_menu;
CREATE VIEW view_menu AS
    SELECT a.id AS parentid,
           b.id,
           b.remaplink,
           b.titolo
      FROM articoli a
           INNER JOIN
           articoli b ON b.idarticolo = a.id OR 
                         (b.idarticolo IS NULL AND 
                          b.id = a.id) 
     WHERE a.isinmenu AND 
NOT        a.isgarbage AND 
NOT        b.isgarbage AND 
           a.idarticolo IS NULL
     ORDER BY parentid DESC,
              b.idarticolo ASC;


-- View: view_all_main_sub_art
DROP VIEW IF EXISTS view_all_main_sub_art;
CREATE VIEW view_all_main_sub_art AS
    SELECT a.id AS parentid,
           b.id,
           b.remaplink,
           b.titolo
      FROM articoli a
           INNER JOIN
           articoli b ON b.idarticolo = a.id
     WHERE a.idtype = 1 AND 
NOT        a.isgarbage AND 
NOT        b.isgarbage AND 
           a.idarticolo IS NULL AND 
           b.idtype = 1
     ORDER BY a.id DESC,
              b.id ASC;


-- View: view_template_maps
DROP VIEW IF EXISTS view_template_maps;
CREATE VIEW view_template_maps AS
    SELECT id,
           remapprefix AS remap
      FROM articoli_types;


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
