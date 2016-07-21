<?php

if (version_compare(phpversion(), '5.4.0', '<')) {
	trigger_error('You are running a version of PHP not supported (too old). Minimum requirements are <b>PHP 5.4.0</b>', E_USER_ERROR);
}






//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// PROBLEMA: la configurazione del database dev'essere la stessa che nel config.php della cartella principale...
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!






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


function getFMDir(){
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
	//domain -- example: "http://localhost:8080"
	'domain' => getDomain(),
	//absolute path for main directory -- example: "/test-cms/mbc-cms/"
	'filemanager_dir' => getFMDir(),
	//drive path for this file -- example: "C:/virtual-hosts/Colibri/test-cms/mbc-cms/"
	'c_dir' => __DIR__ . DIRECTORY_SEPARATOR,	//do not change!
	//----------------- END OF LOCKED VARS -----------------

	"upload_dir" => "../../uploads/",	//relative path from filemanager dir. MUST end with "/".
													//all folders must be lowercase
													//Do not set as sub-path of default_thumb in any of your dreams!
	
	"hidden_dirs" => [],						//array of sub-directories to hide
													//(all folders must be lowercase)
	
	//set unique place to define database dir... should be the same as in config.php in CMS dir.
	'database' => [
		'dir' => '../../database/',
		'name' => 'mbcsqlite3.db'
	],
	
	"default_thumb" => [
		'dir' => "../../img/thumbs/",			//relative path from filemanager dir. MUST end with "/".
														//all folders must be lowercase
														//Do not set as sub-path of upload_dir if not set in exceptions (hidden_dirs)!
		/* ---------- setup hidden thumb dir ----------
		* if - for example - you want to put  default_thumb  into the  upload_dir
		* then you have to set default_thumb to "./setup-example/uploads/thumbs/"
		* and set hidden_dirs to ["thumbs"]
		*/
		'sizes' => [122,91],				//do not change!
		'resize' => 'crop',				//do not change!
			/* ---------- resize options: ----------
			* 0 / exact			image stretched to new dimensions
			* 1 / portrait		image ratio preserved to new height. width automatically calculated
			* 2 / landscape	image ratio preserved to new width. height automatically calculated
			* 3 / auto			image resized to best fit... 0 / 1 / 2... boh.
			* 4 / crop			image resized and fitted in crop dimensions. image will not be stretched!
			* see image magician php for more options
			*/
		'filters' => [],					//(optional)
			/* ---------- filters available: ----------
			* vintage
			* greyScale / greyScaleEnhanced / greyScaleDramatic
			* blackAndWhite
			* sepia
			* negative
			*
			* complex filters... TODO. for now only non-argumented filters
			*
			* see image magician php for more options description.
			*/
		'quality' => 95					//optional: default = 100.
	],
	
	//you can add custom thumbs as an array. every item of this array must be in the form viewed in "default_thumb"
	"custom_thumbs" => [],
	
	'allowed_ext' => [
		//Images
		'img'			=> ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'],
		//Files
		'file'		=> ['doc', 'docx', 'rtf',
							'txt', 'log',
							'pdf',
							'xls', 'xlsx', 'csv',
							'html', 'xhtml', 'xml',
							'sql', 'sqlite', 'db',
							'ppt', 'pptx',
							'odt', 'ots', 'ott', 'odb', 'odg', 'otp', 'otg', 'odf', 'ods', 'odp',
							'css', 'js',
							'ade', 'adp', 'mdb', 'accdb',
							'fla',
							'psd',
							'ai'],
		//Video
		'video'		=> ['mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "webm",
							"flv"],
		//Audio
		'music'		=> ['mp3', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav'],
		//Archives
		'archives'	=> ['zip', 'rar', 'gz', 'tar',
							'iso', 'dmg'],
	],
	
	'max_file_size' => [//size in megabyte
		'img'			=> 20,
		'file'		=> 100,
		'video'		=> 100,
		'music'		=> 100,
		'archives'	=> 100,
	],
	
	'allow_overwrite_file' => true
];





/* do not change this */
//database path
$CONFIG['database']['rel_url'] = $CONFIG['database']['dir'].$CONFIG['database']['name'];
$CONFIG['database']['path'] = $CONFIG['database']['rel_url'];
//hidden dirs to complete relative path
foreach($CONFIG['hidden_dirs'] as &$d){
	$d = $CONFIG['upload_dir'] . $d;
}
//max file sizes to bytes
foreach($CONFIG['max_file_size'] as &$size){
	$size *= 1e6;
}
unset($d);


/**
 * Autoloader for any class
 * 
 * istead of write
 * include_once("./include/myClass.php");
 * include_once("./include/myFoo.php");
 * include_once("./include/myBar.php");
 * 
 * the right classes will be loaded automatically on call
 * classes must be placed like this: "./classes/lowername.class.php"
 */

function mbc_autoload($pClassName) {
	global $CONFIG;
	include_once($CONFIG['c_dir'] . 'classes/' . strtolower($pClassName).'.class.php');
}
spl_autoload_register("mbc_autoload");

require_once "functions.inc.php";

?>