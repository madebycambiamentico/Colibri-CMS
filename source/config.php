<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}

$CONFIG = [
	//------------- LOCKED VARS: DO NOT CHANGE -------------
	//domain -- example: "http://localhost:8080"
	'domain' => (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://':'http://' ).$_SERVER['HTTP_HOST'],
	//absolute path for main directory -- example: "/test-cms/mbc-cms/"
	'mbc_cms_dir' => '/'.str_replace("\\","/",substr( __DIR__, strlen( $_SERVER[ 'DOCUMENT_ROOT' ] ) )).'/',
	//drive path for this file -- example: "C:/virtual-hosts/Colibri/test-cms/mbc-cms/"
	'c_dir' => str_replace("\\","/",__DIR__).'/',	//do not change!
	//----------------- END OF LOCKED VARS -----------------
	
	
	//------------- CUSTOMIZABLE VARS -------------
	//database directory and file name.
	//if you change this, please change accordingly in mbc-filemanager config file.
	'database' => [
		'dir' => 'database/',
		'name' => 'mbcsqlite3.db'
	],
	/* the next values are used to de/en-crypt emails as a failsafe if database is stolen
	* or visible for some reason (.htaccess not set properly...)
	* IF this file (config.php) can be viewed RAW, the emails will be unsecured
	* IF you change this values and the database is already populated by users,
	* all emails could no longer be decrypted and will be invalidated.
	* in this last case you should decrype and re-encrypt the mails using some sort of script.
	*/
	'encrypt' => [
		'secret_key' => 'my very secret key #666',		//change this variable to random string!
	]
	//----------- END OF CUSTOMIZABLE VARS -----------
];

//if we are in main server folder, make directory correction
if ($CONFIG['mbc_cms_dir']==="//") $CONFIG['mbc_cms_dir']="/";

//set absolute database path
$CONFIG['database']['path'] = $CONFIG['c_dir'].$CONFIG['database']['dir'].$CONFIG['database']['name'];


/**
 * Autoloader for any class
 * 
 * // istead of write:
 * include_once("./php/foo.class.php");
 * include_once("./php/bar.class.php");
 * ...
 * 
 * // the right classes will be loaded automatically on call
 * // with no need to explicitly include file!!!
 * $foo = new Foo;
 * BAR::dosomething();
 */

function mbc_autoload($pClassName) {
	global $CONFIG;
	include_once $CONFIG['c_dir'].'php/' . strtolower($pClassName).'.class.php';
}
spl_autoload_register("mbc_autoload");


?>