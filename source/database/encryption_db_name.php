<?php

/**
* Hold a random generated database name
*
* This file should be named "/database/encryption_db_name.php".
* Since the database is a file and could be stolen in case .htaccess is corrupted or disabled in any
* manner, giving it a random name make difficult to an hacker to guess the filename to download.
* ---------------------------------------------------------------------------------------------------
* The content of this PHP file is generated from setup process /php/Colibri-Manager/Setup.php.
* Template for this file is located in /php/Colibri-Manager/secret_key_template.txt and must be left
* intact. Never ever change that file, really. 
* ---------------------------------------------------------------------------------------------------
* Do not edit this PHP file in any way other than running the Setup.php again, else your database (if
* exists and/or is already populated) will have undecryptable fields which can cause malfunctioning
* of the entire website and CMS functions in general.
* ---------------------------------------------------------------------------------------------------
*
* @since 0.5.3 beta m
*
* @see /php/Colibri-Manager/Setup.php
*/

//this is not a standalone file
if (!defined('CMS_INSTALL_DIR')){ header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); die; }

if (defined("CMS_DB_NAME")) die("CMS_DB_NAME collision. Some script has already set that constant!");

define(
	"CMS_DB_NAME",
	/*DB START*/'mbcsqlite3.db'/*DB END*/
);

?>