<?php

//phisycal Colibrì root directory (where config.php is)
define( 'FM_INSTALL_DIR', __DIR__ );

/************************************
 INSTANTIATE THE CONFIGURATION CLASS
 AND START DATABASE CONNECTION
************************************/

//load database connection and standard Colibrì $Config
require_once '../../config.php';

//extend $Config with file manager properties
require_once 'config-extend.php';

require_once FM_INSTALL_DIR . '/functions.inc.php';

?>