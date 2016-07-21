<?php

//phisycal Colibrì root directory (where config.php is)
define( 'CMS_INSTALL_DIR', __DIR__ );

//define constant CMS_ENCRYPTION_KEY
require_once CMS_INSTALL_DIR . '/database/encryption_key.php';

//autoloader for non-categorized Colibrì classes
require_once CMS_INSTALL_DIR . '/autoloader.php';

/***********************************
 INSTANTIATE THE CONFIGURATION CLASS
 AND START DATABASE CONNECTION
************************************/

$Config = new ColibriConfig;

require_once CMS_INSTALL_DIR . '/database/functions.inc.php';

?>