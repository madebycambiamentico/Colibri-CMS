<?php namespace Colibri;

/**
* Basic encryption and decryption of a string with php MCRYPT library
*
* This method is not "secure" for storing password.
* The IV is stored along with the encrypted string in order to be able to decrypt in the future.
*
* @method (public) set_cipher_n_mode
* @method (public) set_key
* @method (public) encrypt
* @method (public) decrypt
*/

class LanguageDetector {
	
	public $lang = 'en';			//language to be used
	public $supported = [];		//supported languages in website
	public $known = [];			//known languages by the user
	
	
	
	function __construct($default=null){
		$this->supported	= $this->get_supported_language_codes();
		$this->known		= $this->parse_HTTP_ACCEPT_LANGUAGE();
		$this->lang			= $this->get_preferred_language(null, $default, true);
	}
	
	
	
	/**
	* get supported languages from database
	*
	* @return (array)		array of supported language codes.
	*/
	function get_supported_language_codes(){
		global $pdo;
		$codes = [];
		//search for supported languages...
		$pdores = $pdo->query("SELECT code FROM languages WHERE supported",\PDO::FETCH_NUM);
		while ($r = $pdores->fetch()){
			$codes[] = $r[0];
		}
		return $codes;
	}
	
	
	
	/**
	* print html options of supported languages
	*/
	function print_supported_language_options($supported=null, $selected=null){
		global $pdo;
		$codes = [];
		//search for supported languages...
		$pdores = $pdo->query("SELECT code, name FROM languages WHERE supported",\PDO::FETCH_ASSOC);
		while ($r = $pdores->fetch()){
			echo "<option value='{$r['code']}'".($selected === $r['code'] ? ' selected' : '').">{$r['name']}</option>";
		}
		return $codes;
	}
	
	
	
	/**
	* parse $_SERVER['HTTP_ACCEPT_LANGUAGE'] to get an associative array (or simply ordered)
	*
	* @author Jesse Skinner (http://www.thefutureoftheweb.com/blog/use-accept-language-header)
	*
	* @param $associative (bool) [optional]		If false will return a ordered list of codes.
	*															Else will return a ordered list with code associated to preference weight
	*
	* @return (array)		Array with associative code <-> preference, like "it-IT" => 0.8
	*							Order of the array is from high preference to lowest.
	*/
	function parse_HTTP_ACCEPT_LANGUAGE($simplified = false){
		$known = [];
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			//break up string into pieces (languages and q factors)
			//example of $_SERVER['HTTP_ACCEPT_LANGUAGE'] content: "it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3"
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
			if (count($lang_parse[1])) {
				//create a list like "en" => 0.8
				$known = array_combine($lang_parse[1], $lang_parse[4]);
				//set default to 1 for any without q factor
				foreach ($known as $lang => $val) {
					if ($val === '') $known[$lang] = 1;
				}
				//sort list based on value
				arsort($known, SORT_NUMERIC);
			}
		}
		
		if ($simplified)
			return $this->simplified_known_languages($known);
		else
			return $known;
	}
	
	
	
	/**
	* simplify array code <-> preferenxe to list of codes.
	*
	* @param $known (array) [optional]		Array with associative code <-> preference, like "it-IT" => 0.8
	*
	* @return (array)		Array of language codes.
	*/
	function simplified_known_languages($known=null){
		if (empty($known)) $known = &$this->known;
		$spartan_langs = [];
		foreach ($known as $code => $w){
			$spartan_langs[] = $code;
		}
		return $spartan_langs;
	}
	
	
	
	/**
	* find match in ordered preferred languages from the list of supported ones
	*
	* @see parse_HTTP_ACCEPT_LANGUAGE(false)
	*
	* @param (array) $known [optional]	user's known languages (string)code => (float)importance
	* @param (array) $supported			supported languages by the site
	* @param (string) $default				fallback if none of the user's languages matches the supported ones. default: 'en' (english)
	*
	* @return (string)		supported language
	*/
	function filter_known_langs($known=null, $supported=null, $default=null){
		// look through sorted list and use first one that matches our languages
		if (empty($known)) $known = &$this->known;
		if (empty($supported)) $supported = &$this->supported;
		if (empty($default)) $default = $this->supported[0];
		foreach ($known as $lang => $w){
			//check if language is supported
			if (in_array($lang, $supported)){
				return $lang;
			}
			//fallback to non-country-based language if char(5).
			else{
				if (strlen($lang) === 5){
					$lang = substr($lang, 0, 2);
					if (in_array($lang, $supported)){
						return $lang;
					}
				}
			}
		}
		return $default;
	}
	
	
	
	/**
	* store preferred language in $this->lang, and into COOKIE "lang" for a week
	*
	* @param (string) $lang [optional]		the language. must be in known languages. default: 'it'.
	*/
	function store_preferred_language($lang='it'){
		if (!$lang) return false;
		$this->lang = $lang;
		setcookie(
			'lang',					//cookie name
			$lang,					//cookie value
			time()+(86400*7),		// 86400 = 1 day
			"/",						//for all domain directory
			"",						//no domain restriction
			false,					//no SSL
			false						//js can edit
		);
	}
	
	
	
	/**
	* detect current language of the user
	*
	* if set_preferred_language() has already been run, check COOKIE "lang".
	* otherwise try to read $_SERVER['HTTP_ACCEPT_LANGUAGE'] if no custom list is provided
	* if language is not supported, fallback to default language (to be set)
	*
	* @param (string) $supported [optional]		array of supported languages.
	* @param (string) $default [optional]			the code in database table `languages`.
	* @param (string) $store_result [optional]	if true the preference will be stored in "lang" cookie.
	*/
	function get_preferred_language($supported=null, $default=null, $store_result=true){
		
		if (empty($supported)) $supported = &$this->supported;
		if (empty($default)) $default = &$this->lang;
		
		//COOKIE value is preferred.
		if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], $supported)) return $_COOKIE['lang'];
		
		//set default language.
		$lang = $this->filter_known_langs( null, $supported, $default );
		
		//store result
		if ($store_result) $this->store_preferred_language( $lang );
		
		return $lang;
	}
}

?>