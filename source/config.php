<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}


//-------------------------
function fix_script_url(){
	$script_url = NULL;
	//to do:
	//what order? SCRIPT_URL, REQUEST_URI, REDIRECT_URL?
	//to be determined... not entirely sure
	//and by the way: SCRIPT_URL is what I think it is?
	
	if (!empty($_SERVER['SCRIPT_URL']))   
		$script_url = $_SERVER['SCRIPT_URL'];
	elseif (!empty($_SERVER['REQUEST_URI'])) {
		$p = parse_url($_SERVER['REQUEST_URI']);
		$script_url = $p['path'];
	}
	elseif (!empty($_SERVER['REDIRECT_URL'])) 
		$script_url = $_SERVER['REDIRECT_URL'];
	else
		die('Cannot determine $_SERVER["SCRIPT_URL"].');

	$_SERVER['SCRIPT_URL'] = $script_url;
	return $script_url;
}
fix_script_url();
//-------------------------

function getDomain(){
	return (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
}

function getCMSDir(){
	//----------- find current directory url: ------------
	// !!! WARNING !!! WORKS ONLY FOR FILES WITHIN INSTALLATION DIRECTORY FOLDER, NOT SUB-FOLDERS!!!
	// if requested "http://www.your_site.com/[installation_directory]/required/path"
	// should find something like:
	//   $_SERVER['SCRIPT_NAME'] = "/[installation_directory]/index.php"
	//   $_SERVER['SCRIPT_URL'] = "/[installation_directory]/required/path"
	// $CONFIG['mbc_cms_dir'] result in "/" or "/[installation_directory]/"
	$prefix = dirname($_SERVER['SCRIPT_NAME']);  // "/wathever/index.php" -> "/wathever", or "/whatever" -> "/"
	if ($prefix !== '/'){
		$prefix .= '/';
		$length = strlen($prefix);
		$item = $_SERVER['SCRIPT_URL']; // /wathever/anythingelse/boh.boh
		// check if there is a match; if not, decrease the prefix by one character at a time
		while ($length && substr($item, 0, $length)!==$prefix) {
			$length--;
			$prefix = substr($prefix, 0, -1);
		}
		if (!$length) {
			return "/";
		}
		else{
			return $prefix;
		}
	}
	return $prefix;
}



$CONFIG = [
	//------------- LOCKED VARS: DO NOT CHANGE -------------
	//domain
	//example: "http://localhost:8080"
	'domain' => getDomain(),
	//absolute path for main directory
	//example: "/any_path/to/installation_directory/"
	'mbc_cms_dir' => getCMSDir(),
	//drive path for this file
	//example: "C:/virtual-hosts/Colibri/test-cms/mbc-cms/"
	'c_dir' => str_replace("\\","/",__DIR__).'/',
	//----------------- END OF LOCKED VARS -----------------
	
	
	//------------- CUSTOMIZABLE VARS -------------
	//database directory and file name.
	'database' => [
		'dir' => 'database/',		//should not be changed: some js scripts rely on this path.
		'name' => 'mbcsqlite3.db'	//can be changed at will, but must be the same as in php/mbc-filemanager/config.php
	],
	//secret string to encrypt and decrypt emails. If changed, you should backup the decrypted old emails
	//and restore them with the new key encryption.
	//this feature is not officially supported
	'encrypt' => [
		###/SECRETEY###
		'secret_key' => '!CHANGE THIS STRING TO ANY RANDOM ONE!',
		###SECRETEY/###
	]
	//----------- END OF CUSTOMIZABLE VARS -----------
];


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