<?php
/*
 * @Encrypter class: 
 * Basic encryption and decryption of a string with php MCRYPT
 * @use for encription of sensible data. key should be stored safely (see $CONFIG[])
**/

class Encrypter
{
	private $securekey;//hashed!
	private $iv_size;
	private $CIPHER;
	private $MODE;


	function __construct($textkey = '', $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
		//set cipher and mode
		if (!empty($textkey) && !empty($mode))
			$this->set_cipher_n_mode($cipher, $mode);
		else
			$this->set_cipher_n_mode();
		//set secret key
		if (!empty($textkey)) $this->set_key($textkey);
	}


	function set_cipher_n_mode($cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
		//get the needed IV length for (cipher U mode)
		//default: chipher = MCRYPT_RIJNDAEL_128 and mode = 'cbc' => IV size is 16
		$this->CIPHER		= $cipher;
		$this->MODE			= $mode;
		$this->iv_size		= mcrypt_get_iv_size($cipher, $mode);
	}


	function set_key($textkey){
		//create hashed key, return raw output (bytes)
		$this->securekey = hash('sha256', $textkey, TRUE);
	}


	/*
	 * @encrypt: encrypt a string
	 * @return encrypted string base64
	 * @inputs (string)$input
	**/
	function encrypt($input){
		//storing an empty input is really stupid.
		if (empty($input)) return '';
		//create a random vector IV (bytes)
		// * from 5.4 the algo should be DEV_RAND.
		// * if server is not well configured we cannot access "dev/rand" functions, throwing the error "cannot open device"
		// * so as fallback use MCRYPT_RAND (this happened with Aruba, php5.6,, 2016)
		$iv = @mcrypt_create_iv($this->iv_size) or
				@mcrypt_create_iv($this->iv_size, MCRYPT_RAND) or
				trigger_error("<b>Error:</b> environment exception: your server is not configured to encrypt strings with <b>MCRYPT</b>.", E_USER_ERROR);
		//to store byte string: encode in base64 (ascii?)! Stored string contains: IV + encrypted string
		return base64_encode(
			//mcrypt_encrypt(cipher, key, data, mode, iv)
			$iv . mcrypt_encrypt(
				$this->CIPHER,
				$this->securekey,
				$input,
				$this->MODE,
				$iv
			)
		);
	}


	/*
	 * @decrypt: decrypt a base64 string previously created with @encrypt
	 * @return decrypted string
	 * @inputs (string)$input
	**/
	function decrypt($input){
		//empty input throw error (IV of 0 size)
		if (empty($input)) return '';
		//base64 to byte
		$input = base64_decode($input);
		//get IV (first part of input string)
		$iv = substr($input, 0, $this->iv_size);// = 16 bytes
		//get encrypted string (last part of input string)
		$input = substr($input, $this->iv_size);
		//decrypt cipher
		return trim(
			//mcrypt_decrypt(cipher, key, data, mode, iv)
			mcrypt_decrypt(
				$this->CIPHER,
				$this->securekey,
				$input,
				$this->MODE,
				$iv
			)
		);
	}
}


/*
//TEST
echo '<!DOCTYPE HTML><html>
	<head>
		<title>MCRYPT test</title>
		<meta charset="utf-8">
		<style type="text/css">
			body{font:15px/1.5 helvetica,arial,sans-serif}
		</style>
	</head>
	<body>';

$ENCRYPTER = new Encrypter('secret key');
$original = "M'illumino d'immenso [P@scoli]";
$encrypted = $ENCRYPTER->encrypt($original); // example "mVo1Bq4b/dXhossrT61+s0Wvp6reQI/uR98AGuGqP/M="
$decrypted = $ENCRYPTER->decrypt($encrypted); // "original string 1998"

echo '<i>Original string</i>: <b>'.htmlentities($original).'</b><br><br>'.
		'<b>Encripted</b>: '. $encrypted.'<br>'.
		'<b>Decrypted</b>: '.htmlentities($decrypted).
	'</body>';
//*/

?>