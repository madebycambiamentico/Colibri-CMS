<?php

class ColibriConfig{
	//constants - static
	const VERSION				= "0.5.3";		//current Colibry System Version
	const RELEASE				= "&beta;m";	//release (alpha, beta, multilang, release candidate...)
	//variables
	public $domain				= "";				//site domain. will be detected automatically
	public $script_path		= "/"; 			//absolute path to caller script. will be detected automatically
	//-----------------------------------------------------------
	// If you want to change the database properties, be sure to
	// extend same properties in /php/mbc-filemanager/config.php
	//-----------------------------------------------------------
	public $database			= [
		'folder'		=> '/database/',			//database folder relative path form installation dir (e.g. /database/custom/). default: "/database/"
		'file'		=> 'mbcsqlite3.db',		//database file name (e.g. myDB.db). default: "mbcsqlite3.db".
														//from Colibrì 0.5.4 will be overwritten by random name.
		'dir'			=> null,						//will be determined by __DIR__ + folder
		'src'			=> null						//will be determined by __DIR__ + folder + name
	];
	
	/**
	* initialize the config class
	*
	* set script path, database installation directory,
	* and register all autoloaders for common plugins.
	*/
	function __construct(){
		$this->fix_script_url();
		$this->domain = $this->get_domain();
		$this->script_path = $this->get_current_script_path();
		if (defined('CMS_DB_NAME')) $this->update_db(['file' => CMS_DB_NAME]);
		else $this->update_db();
		$this->register_autoloaders();
	}
	
	
	/**
	* override database properties and update 'dir' and 'src'
	*/
	public function update_db($options=[]){
		foreach ($options as $key => $dbval){
			if (isset($this->database[$key]))
				$this->database[$key] = $dbval;
		}
		$this->database['dir'] = CMS_INSTALL_DIR . $this->database['folder'];
		$this->database['src'] = $this->database['dir'] . $this->database['file'];
	}
	
	
	/**
	* create the $_SERVER['SCRIPT_URL'] if not present
	*
	* @return (string)	The content of $_SERVER['SCRIPT_URL'] (original or derivated)
	*/
	private function fix_script_url(){
		$script_url = NULL;
		//TODO...
		//what order? SCRIPT_URL, REQUEST_URI, REDIRECT_URL?
		//to be determined... not entirely sure
		//and by the way: SCRIPT_URL is what I think it is?
		
		if (!empty($_SERVER['SCRIPT_URL']))   
			return $_SERVER['SCRIPT_URL'];
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
	
	
	/**
	* detect the caller script absolute path from installation directory.
	*
	* the result does NOT include the domain (e.g. "http://website.com").
	* for example, calling "/<installation-dir>/index.php" --> "/<installation-dir>/" (or "/" if installed in web root)
	* if istead the script called is "/<installation-dir>/database/user-edit.php" --> "/<installation-dir>/database/".
	* NB - if you know where you are, you can use the 'href' and 'src' with relative path to access parent folders (add "../").
	*
	* @return (string)	The absolute caller script path
	*/
	private function get_current_script_path(){
		$prefix = dirname($_SERVER['SCRIPT_NAME']); // "/wathever/page.php" --> "/wathever", or "/whatever/foo/bar" --> "/" (index.php)
		if ($prefix !== '/'){
			$prefix .= '/';
			$length = strlen($prefix);
			$item = $_SERVER['SCRIPT_URL']; // /wathever/anythingelse/boh.boh
			// check if there is a match; if not, decrease the prefix by one character at a time
			while ($length && substr($item, 0, $length)!==$prefix) {
				$length--;
				$prefix = substr($prefix, 0, -1);
			}
			if (!$length)
				return "/";
			else
				return $prefix;
		}
		return $prefix;
	}
	
	
	/**
	* detect domain.
	*
	* @return (string)	your site domain (e.g. "http://website.com").
	*/
	private function get_domain(){
		return (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
	}
	
	
	/**
	* load autoloader for classes used in Colibrì CMS
	*/
	private function register_autoloaders(){
		require_once( CMS_INSTALL_DIR . '/php/Colibri-Manager/autoloader.php' );	// for Colibrì manager classes (menu, popups etc.)
		require_once( CMS_INSTALL_DIR . '/php/Colibri-ReCaptcha/autoloader.php' );	// reCaptcha v2 wrapper
		require_once( CMS_INSTALL_DIR . '/php/Colibri-Website/autoloader.php' );	// base queries for website
		require_once( CMS_INSTALL_DIR . '/php/RandomLib/autoloader.php' );			// generate casual secure strings
		//NB - phpMailer must be loaded manually, since it is not a common used class here in Colibrì
	}

}

?>