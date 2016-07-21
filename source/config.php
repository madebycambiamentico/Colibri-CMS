<?php




//#####################################################################
//#######  C H A N G E  T H E  S E C R E T  S T R I N G  !!!  #########
//#####################################################################


//TODO
//render CONFIG a class or a constant



//-------------------------
function fix_script_url(){
	$script_url = NULL;
	//TODO...
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
	//		$_SERVER['SCRIPT_NAME'] = "/<installation_directory>/index.php"
	//		$_SERVER['SCRIPT_URL'] = "/<installation_directory>/required/path"
	// $CONFIG['mbc_cms_dir'] result in "/" or "/<installation_directory>/"
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
	
	//absolute folder path of calling script.
	//example: "/any_path/to/installation_directory/" if root script is "index.php"
	//example 2: "/any_path/to/installation_directory/database/" if root script is "database/user-edit.php"
	'mbc_cms_dir' => getCMSDir(),
	
	//drive path for this file
	//example: "C:/virtual-hosts/Colibri/test-cms/mbc-cms/"
	'c_dir' => __DIR__ . '/', //do not change!
	
	//----------------- END OF LOCKED VARS -----------------
	
	
	
	
	//------------------ CUSTOMIZABLE VARS -----------------
	
	//database directory and file name.
	'database' => [
		'dir' => 'database/',		//should not be changed: some js scripts rely on this path.
		'name' => 'mbcsqlite3.db'	//can be changed at will, but must be the same as in php/mbc-filemanager/config.php
	],
	
	//secret string to encrypt and decrypt emails. If changed, you should backup the decrypted old emails
	//and restore them with the new key encryption.
	//this feature is not officially supported
	//...
	//##########################################################
	//#######  C H A N G E  T H I S  S T R I N G  !!!  #########
	//##########################################################
	'encrypt' => [
		'secret_key' => '!CHANGE THIS STRING TO ANY RANDOM ONE!',
	]
	
	//-------------- END OF CUSTOMIZABLE VARS --------------

];


//set absolute database path
$CONFIG['database']['path'] = $CONFIG['c_dir'].$CONFIG['database']['dir'].$CONFIG['database']['name'];



//include autoloaders:
require_once( __DIR__ .'/autoloader.php' );								// for non-categorized Colibrì classes
require_once( __DIR__ .'/php/Colibri-Manager/autoloader.php' );	// for Colibrì manager classes (menu, popups etc.)
require_once( __DIR__ .'/php/Colibri-ReCaptcha/autoloader.php' );	// reCaptcha v2 wrapper
require_once( __DIR__ .'/php/Colibri-Website/autoloader.php' );	// base queries for website
require_once( __DIR__ .'/php/RandomLib/autoloader.php' );			// generate casual secure strings


?>