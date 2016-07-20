<?php namespace ReCaptcha;

/**
* error or other codes translation.
*
* @copyright  Copyright (C)2016 Nereo Costacurta
* @license    GPLv3
*/
class Translate{
	private static		$lang = 'en'; //current language
	private static		$supported_lang = ['en','it']; //current supported languages
	
	//all translations
	public static		$translation = [
		'en' => [
			'NO_RECAPTCHA'			=> "This site didn't set correctly reCAPTCHA, we are sorry.",
			'NO_RECAPTCHA_RES'	=> "Missing reCAPTCHA user response. Please, prove you are not a bot, validating the reCAPTCHA widget.",
			'NO_SECRET'				=> 'The secret parameter is missing.',
			'INVALID_SECRET'		=> 'The secret parameter is invalid or malformed.',
			'NO_RESPONSE'			=> 'The response parameter is missing.',
			'INVALID_RESPONSE'	=> 'The response parameter is invalid or malformed.',
			'INVALID_JSON'			=> 'reCAPTCHA json parsing failed (invalid json).',
			'SPAMMER'				=> "reCAPTCHA thinks you are a spammer..."
		],
		'it' => [
			'NO_RECAPTCHA'			=> "Questo sito non ha implementato correttamente reCAPTCHA. Siamo spiacenti.",
			'NO_RECAPTCHA_RES'	=> "Risposta reCAPTCHA mancante. Per favore, prova di non essere un bot, validando il widget reCAPTCHA",
			'NO_SECRET'				=> 'Parametro segreto mancante.',
			'INVALID_SECRET'		=> 'Parametro segreto non valido o malformato.',
			'NO_RESPONSE'			=> 'Parametro risposta mancante.',
			'INVALID_RESPONSE'	=> 'Parametro risposta non valido o malformato.',
			'INVALID_JSON'			=> 'Fallita analisi del json reCAPTCHA (non valido)',
			'SPAMMER'				=> "reCAPTCHA pensa che tu sia uno spammer..."
		]
	];
	
	
	/**
	* change language.
	*
	* if language is not recognize fallback to english (or previous set language)
	*
	* @param (string) $lg [optional]		translations language. default: 'en'.
	*/
	static function set_lang($lg='en'){
		if ( in_array($lg, self::$supported_lang) )
			self::$lang = $lg;
	}
	
	
	/**
	* returns the translated error or alert.
	*
	* if language is not recognize or translation is not completed,, fallback to english
	*
	* @param (string) $code		see codes in $translation
	*/
	static function error($code){
		//error in that language
		if (isset(self::$translation[self::$lang][$code]))
			return self::$translation[self::$lang][$code];
		//fallback in english error
		elseif (self::$lang !== 'en' && isset(self::$translation['en'][$code]))
			return self::$translation['en'][$code];
		else
			return 'Unknow error.';
	}
}

?>