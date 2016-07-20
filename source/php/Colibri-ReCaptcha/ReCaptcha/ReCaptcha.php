<?php namespace ReCaptcha;

/**
* wrapper for reCAPTCHA v2.
*
* allow to display widget client-side AND validate user response server-side.
* for translations in other languages than english, see Translate class.
*
* @see Translate.php
*
* @copyright  Copyright (C)2016 Nereo Costacurta
* @license    GPLv3
*/
class ReCaptcha{

	public	$recaptcha_url;		//url of google recaptcha validator
	public	$recaptcha_js_api;	//url of google recaptcha js api
	public	$ip;						//ip of user request (optional)
	public	$lang;					//preferred language (optional)
	public	$public_key;			//recaptcha public key
	private	$secret_key;			//recaptcha secret key
	
	public	$error;						//(string) holds errors, divided by new line
	public	$last_captcha_result;	//(array) holds the last result from recaptcha validator json
	
	
	
	
	/**
	* set default values or inject new values.
	*
	* @see set_opt_array
	*
	* @param (string|array) $pk [optional]		if (string) evaluates as public key. if (array) will trigger set_opt_array()
	* @param (string) $sk [optional]				secret recaptcha key
	* @param (string) $ip [optional]				remote IP address of user solving recaptcha
	*/
	function __construct($pk=null, $sk=null, $ip=null){
		$this->recaptcha_url		= 'https://www.google.com/recaptcha/api/siteverify';
		$this->recaptcha_js_api	= 'https://www.google.com/recaptcha/api/siteverify';
		
		//if $pk is an array of properties... update them
		if (is_array($pk)){
			//set default values
			$this->public_key		= null;
			$this->secret_key		= null;
			$this->ip				= null;
			$this->lang				= 'en';
			//set custom values
			$this->set_opt_array($pk);
			//update language
			Translate::set_lang($this->lang);
		}
		//classic values setting
		else{
			$this->public_key		= $pk;
			$this->secret_key		= $sk;
			$this->ip				= $ip;
			$this->lang				= 'en';
		}
		
		$this->error					= '';
		$this->last_captcha_result	= [];
	}
	
	
	
	/**
	* inject values all at once, given an array.
	*
	* accept an associative array which keys represent the class variable name.
	* changes to $lang will change translation accordingly.
	* function overwrite is not allowed and will be skipped. Allowed keys are:
	* recaptcha_url, recaptcha_js_api, ip, lang, public_key, secret_key, error*, last_captcha_result*
	* (*) should not be changed if not to be resetted.
	*
	* @param (array) $a [optional]	associative array which keys represent the class variable name
	*/
	public function set_opt_array($a){
		if (empty($a)) return;
		foreach($a as $k => $val)
			!method_exists($this,$k) && isset($this->{$k}) && $this->{$k} = $val;
		//update language (if modified)
		Translate::set_lang($this->lang);
	}
	
	
	
	public function get_browser_widget($lang=null){
		echo	'<script src="https://www.google.com/recaptcha/api.js'.($this->lang!='en' ? '?hl='.$lang : '').'" async defer></script>'.
				'<div class="g-recaptcha" data-sitekey="'.htmlentities($this->public_key,ENT_QUOTES).'"></div>';
	}
	
	
	
	/**
	* validate user response against reCaptcha v2 anti-spam filter.
	*
	* cURL extension must be enabled. if param $response is empty, the script will automatically check if POST 'g-recaptcha-response'
	* is present. Any error is stored in $error. if recaptcha json is correctly parsed, the content will be available in $last_captcha_result.
	*
	* @see Translate
	* @see cURL library
	*
	* @param (string|array) $pk [optional]		if (string) evaluates as public key. if (array) will trigger set_opt_array()
	* @param (string) $sk [optional]				secret recaptcha key
	* @param (string) $ip [optional]				remote IP address of user solving recaptcha
	*
	* @return (false|true)		false in case of: curl error, missing parameters, invalid response.
	*									true if successfull response or recaptcha properties are not initialized.
	*/
	public function validate($response=null){
		//(0)
		//check if i can use cURL, else fail
		if (!function_exists('curl_version')) return false;
		//(1)
		//check response...
		if (!$response){
			if (!isset($_POST['g-recaptcha-response'])){
				if (!$this->public_key || !$this->secret_key){
					//public and/or secret key are not set ( the site owner didn't enable recaptcha )
					$this->error = Translate::error('NO_RECAPTCHA');
					return true;
				}
				else{
					//public and/or secret key are set, but there's no recaptcha response
					$this->error = Translate::error('NO_RECAPTCHA_RES');
					return false;
				}
			}
			else{
				//automatic set recaptcha response from POST request
				$reponse = $_POST['g-recaptcha-response'];
			}
		}
		//(2)
		//set recaptcha parameters
		// - secret key + response
		$params = [
			'secret'		=> $this->secret_key,
			'response'	=> $response
		];
		// - remote ip of user (optional)
		if (!empty($this->ip)){
			$params['remoteip'] = $this->ip;
		}
		//(3)
		//check recaptcha
		$ch = curl_init($this->recaptcha_url);
		curl_setopt_array($ch, [
			CURLOPT_HEADER				=> false,
			CURLOPT_TIMEOUT			=> 4,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_POST				=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_POSTFIELDS		=> http_build_query([
					'secret'		=> $this->secret_key,
					'response'	=> $reponse
				])
		]);
		$captcha_res = curl_exec($ch);
		//(4)
		//return success
		return $this->decode_recaptcha_result($captcha_res, $ch);
	}
	
	
	private function get_recaptcha_error($s_error){
		switch($s_error){
			case 'missing-input-secret': 		return Translate::error('NO_SECRET'); break;
			case 'invalid-input-secret': 		return Translate::error('INVALID_SECRET'); break;
			case 'missing-input-response': 	return Translate::error('NO_RESPONSE'); break;
			case 'invalid-input-response': 	return Translate::error('INVALID_RESPONSE'); break;
			default: return 'Unknow reCAPTCHA error';
		}
	}
	
	
	private function decode_recaptcha_result($res, $ch){
		//error with cURL library...
		if (false === $res){
			$this->error = curl_error();
			return false;
		}
		//json response to associative array
		$a_res = @json_decode($res, true);
		//invalid json...?
		if (!$a_res){
			$this->error = Translate::error('INVALID_JSON');
			return false;
		}
		$this->last_captcha_result = $a_res;
		//check success
		/*
		{
			"success": true|false,
			"challenge_ts": timestamp,  // timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
			"hostname": string,         // the hostname of the site where the reCAPTCHA was solved
			"error-codes": [...]        // optional
		}
		*/
		if (false === $a_res['success']){
			//store recaptcha error (if any)
			if (!empty($a_res['error-codes']) && is_array($a_res['error-codes'])){
				$errors = [];
				foreach($a_res['error-codes'] as $code){
					$errors[] = $this->get_recaptcha_error($code);
				}
				$this->error = implode("\n",$errors);
			}
			else{
				$this->error = Translate::error('SPAMMER');
			}
			return false;
		}
		return true;
	}

}

?>