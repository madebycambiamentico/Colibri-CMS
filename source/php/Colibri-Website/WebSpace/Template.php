<?php namespace WebSpace;

/**
* get page template for your website.
*
* this script will try to return the right template file path from the provided article type.
* if any of the files are founded will throw an exception.
* the categories allowed are:
* - single article
* - multiple article (e.g. search results)
* - main page (index)
* -----------------------------------------------------------------------------------------------------
* for SINGLE, the script search for this files, in order:
*	- "for_id_XXX.php"	: customized page for article with id XXX.		XXX = integer
*	- "single_MMM.php"	: customized page for MMM types.						MMM = string (the remapprefix)
*	- "single_YYY.php"	: [deprecated] customized page for YYY types.	YYY = integer
*	- "single.php"			: generic page for any type of article.
*	- otherwise fails
* -----------------------------------------------------------------------------------------------------
* for MULTIPLE, the script search for this files, in order:
*	- "single_MMM.php"	: customized page for MMM types.						MMM = string (the remapprefix)
*	- "multi_YYY.php"		: [deprecated] customized page for YYY types.	YYY = integer
*	- "multi.php"			: generic page for any type of article.
*	- otherwise fails
* -----------------------------------------------------------------------------------------------------
* for MAIN, the files parsed are:
*	- main.php
*	- otherwise fails
* -----------------------------------------------------------------------------------------------------
*
* @copyright	(C)2016 Nereo Costacurta
* @license		GPLv3
*/

class Template{
	
	static $maps = [];	// will contain array of (int)idtype => (string)remapprefix
	
	
	/**
	* return the single page template path
	*
	* @see cpath
	*
	* @param (int) $id			[optional]	id of the article
	* @param (int) $type			[optional]	type of the article(s)
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the file path
	*/
	static function single($id=0, $type=0, $tmplt='colibri'){
		$path = self::cpath($tmplt);
		$template = "{$path}/for_id_{$id}.php";
		if (!is_file($template)){
			isset(self::$maps[$type]) && $template = "{$path}/single_".self::$maps[$type].".php";
			if (!is_file($template)){
				$template = "{$path}/single_{$type}.php";//deprecated since 0.3.2
				if (!is_file($template)){
					$template = "{$path}/single.php";
					if (!is_file($template))
						trigger_error("template 'single.php' not found", E_USER_ERROR);
				}
			}
		}
		return $template;
	}
	
	
	/**
	* return the multiple page template path
	*
	* @see cpath
	*
	* @param (int) $type			[optional]	type of the article(s)
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the file path
	*/
	static function multi($type=0, $tmplt='colibri'){
		$path = self::cpath($tmplt);
		if (isset(self::$maps[$type]))
			$template = "{$path}/multi_".self::$maps[$type].".php";
		else
			$template = "{$path}/multi_{$type}.php";//deprecated since 0.3.2
		if (!is_file($template)){
			!isset(self::$maps[$type]) && $template = "{$path}/multi_{$type}.php";//deprecated since 0.3.2
			if (!is_file($template)){
				$template = "{$path}/multi.php";
				if (!is_file($template))
					trigger_error("template 'multi.php' not found", E_USER_ERROR);
			}
		}
		return $template;
	}
	
	
	/**
	* return the main page (index) template path
	*
	* @see cpath
	*
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the file path
	*/
	static function main($tmplt='colibri'){
		$path = self::cpath($tmplt);
		$template = $path;
		if (!is_file($template)){
			$template = "{$path}/main.php";
			if (!is_file($template))
				trigger_error("template 'index.php' or 'main.php' not found", E_USER_ERROR);
		}
		return $template;
	}
	
	
	/**
	* return a generic file path in the current template
	*
	* @see cpath
	*
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the file path
	*/
	static function custom($tmplt='colibri',$file='noop',$skipdeath=false){
		$template = self::cpath($tmplt).$file;
		if (!is_file($template)){
			if ($skipdeath){
				return false;
			}
			else{
				trigger_error("custom template file '{$file}' not found", E_USER_ERROR);
			}
		}
		return $template;
	}
	
	
	/**
	* get the template folder relative path (from installation folder)
	*
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the folder path
	*/
	static function path($tmplt='colibri'){
		global $Config;
		return $Config->script_path."templates/{$tmplt}/";
	}
	
	
	/**
	* get the template folder HDD path
	*
	* @param (string) $tmplt	[optional]	template folder. default "colibri"
	*
	* @return (string)	the folder path
	*/
	static function cpath($tmplt='colibri'){
		return \CMS_INSTALL_DIR ."/templates/{$tmplt}/";
	}
}


//get remapprefix to be used when searching a template page...
if (isset($Config) && isset($pdo)){
	if ($pdores = $pdo->query("SELECT * FROM view_template_maps", PDO::FETCH_ASSOC)){
		$maps = [];
		while ($r = $pdores->fetch()){
			$maps[$r['id']] = $r['remap'];
		}
		$pdores->closeCursor();
		Template::$maps = $maps;
	}
}

?>