<?php

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
	
	static function custom($tmplt='colibri',$file='noop'){
		$template = self::cpath($tmplt).$file;
		if (!is_file($template)){
			trigger_error("custom template file '{$file}' not found", E_USER_ERROR);
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