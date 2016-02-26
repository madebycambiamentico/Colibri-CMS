<?php
/*
 * Basic encryption and decryption of a string with PHP mcrypt
 * @use for encription of sensible data. key should be stored elsewhere (see $CONFIG[])
 */

class Encrypter
{
	private $securekey;
	private $iv_size;

	function __construct($textkey)
	{
		$this->iv_size = mcrypt_get_iv_size(
			MCRYPT_RIJNDAEL_128,
			MCRYPT_MODE_CBC
		);
		$this->securekey = hash(
			'sha256',
			$textkey,
			TRUE
		);
	}

	function encrypt($input)
	{
		$iv = @mcrypt_create_iv($this->iv_size);
		//if server is not well configured, the error "cannot open device" could occur,
		//or if you are running in old php on windows,
		//to prevent malfunctions we create a fallback with the common MCRYPT_RAND
		if (!$iv) $iv = mcrypt_create_iv($this->iv_size,MCRYPT_RAND);
		return base64_encode(
			$iv . mcrypt_encrypt(
				MCRYPT_RIJNDAEL_128,
				$this->securekey,
				$input,
				MCRYPT_MODE_CBC,
				$iv
			)
		);
	}

	function decrypt($input)
	{
		$input = base64_decode($input);
		$iv = substr(
			$input,
			0,
			$this->iv_size
		);
		$cipher = substr(
			$input,
			$this->iv_size
		);
		return trim(
			mcrypt_decrypt(
				MCRYPT_RIJNDAEL_128,
				$this->securekey,
				$cipher,
				MCRYPT_MODE_CBC,
				$iv
			)
		);
	}
}


/*
echo '<!DOCTYPE HTML><html><head><meta charset="utf-8"></head><body>';

//use example:
$ENCRYPTER = new Encrypter('secret key');
$original = 'original string 1998';
$encrypted = $ENCRYPTER->encrypt($original); // example "mVo1Bq4b/dXhossrT61+s0Wvp6reQI/uR98AGuGqP/M="
$decrypted = $ENCRYPTER->decrypt($encrypted); // "original string 1998"

echo $original.'<br>'.$encrypted.'<br>'.$decrypted;
*/

?>