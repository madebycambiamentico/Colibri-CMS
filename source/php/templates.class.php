<?php

/*

this class includes template for:
- single article
- multiple article (search results)
- main page (index)

------------------------------------------------------------
for SINGLE the script search for this files, in order:
	- "for_id_XXX.php"	: customized page for article with id XXX. XXX = integer
	- "single_YYY.php"	: customized page for YYY types. YYY = integer
	- "single.php"			: generic page for any type of article.
	- otherwise fails

for MULTIPLE the script search for this files, in order:
	- "multi_YYY.php"		: customized page for YYY types. YYY = integer
	- "multi.php"			: generic page for any type of article.
	- otherwise fails

for MAIN the files parsed are:
	- main.php
	- otherwise fails

------------------------------------------------------------
TODO: YYY (type) should be STRING istead of INTEGER,
according to "articoli_types.remapprefix" field on database.
e.g.:
	- "single_1.php" should become "single_.php"
	- "single_2.php" should become "single_news.php"
	- "single_3.php" should become "single_links.php"
	- etc.

*/

class TEMPLATES{
	
	//for single pages
	static function single($id=0, $type=0, $tmplt='colibri'){
		$path = self::cpath($tmplt);
		$template = "{$path}/for_id_{$id}.php";
		if (!is_file($template)){
			$template = "{$path}/single_{$type}.php";
			if (!is_file($template)){
				$template = "{$path}/single.php";
				if (!is_file($template))
					trigger_error("template 'single.php' not found", E_USER_ERROR);
			}
		}
		return $template;
	}
	
	//for multiple pages (eg. search result)
	static function multi($type=0, $tmplt='colibri'){
		$path = self::cpath($tmplt);
		$template = "{$path}/multi_{$type}.php";
		if (!is_file($template)){
			$template = "{$path}/multi.php";
			if (!is_file($template))
				trigger_error("template 'multi.php' not found", E_USER_ERROR);
		}
		return $template;
	}
	
	//for main page
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
	
	static function path($tmplt='colibri'){
		global $CONFIG;
		return $CONFIG['mbc_cms_dir']."templates/{$tmplt}/";
	}
	static function cpath($tmplt='colibri'){
		global $CONFIG;
		return $CONFIG['c_dir']."templates/{$tmplt}/";
	}
}

?>