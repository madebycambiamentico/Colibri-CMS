<?php

require_once "../config.php";

$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');

if (isset($_GET['redirect'])) goto anchor_redirect;
else goto anchor_json;




anchor_json:

header('Content-Type: application/json');
logout(null,false);
jsonSuccess();




anchor_redirect:

//control login
logout('logout',false);
header('Location: ../login?logout');
?>