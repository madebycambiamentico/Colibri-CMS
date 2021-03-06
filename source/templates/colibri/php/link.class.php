<?php

/*
 * @template Colibrì 2016 v.1.0
 * custom classes used in template "colibrì" -- this is not a required file for standard templates.
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/


class Links{
	//THIS CLASS RELY ON THE FACT THAT IT WILL BE USED INSIDE THE
	//MAPPED ARTICLES INCLUDED BY ROOT SCRIPT >>>INDEX.PHP<<<
	
	//when using mod_rewrite, relative path are not fitting well..
	//so istead this function return the absolute path of the installed
	//CMS and add the relative path.
	static function file($filepath){
		global $Config;
		return $Config->script_path . $filepath;
	}
	//shorthand Links::file() for uploaded files with filemanager.
	static function uploaded($filepath){
		return self::file('uploads/'.$filepath);
	}
	//shorthand Links::file() for automatic created thumbs
	static function thumb($filepath){
		return self::file('img/thumbs/'.$filepath);
	}
	
	static function stylesheet($s){
		echo '<link rel="stylesheet" type="text/css" href="' . \WebSpace\Template::$path . $s.'">';
	}
	
	static function script($s){
		echo '<script src="'. \WebSpace\Template::$path . $s.'"></script>';
	}
	
	static function getJQuery(){
		//for debug offline... remove this lines in production site 
		$local = strpos(CMS_INSTALL_DIR,'C:') === 0;
		if (!$local):
?>
<!--[if lte IE 8]>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
<![endif]-->
<!--[if gt IE 8]><!-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<!--<![endif]-->
<?php
		else:
?>
<!--[if lte IE 8]>
<script src="<?php echo Links::file('js/jquery/jquery-1.11.3.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
<![endif]-->
<!--[if gt IE 8]><!--><script src="<?php echo Links::file('js/jquery/jquery-3.1.0.min.js') ?>"></script><!--<![endif]-->
<?php
		endif;
	}
};

?>