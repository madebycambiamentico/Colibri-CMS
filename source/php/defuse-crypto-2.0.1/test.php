<?php

require_once('defuse-crypto.phar');
//Crypto, File, Key, and KeyProtectedByPassword classes should be loaded

use \Defuse\Crypto\Crypto;

$secretkey = "tralallero";
$originalstring = "this is the original string!!!";


if (class_exists('\Defuse\Crypto\Crypto'))
	echo "defuse-crypto.phar has been loaded!\n";
else
	die("Crypto class not exists...");

$encypted_hex = Crypto::encryptWithPassword($originalstring, $secretkey, false); //hex
echo "HEX: {$encypted_hex}\n";

?>