<?php
header('Content-Type: application/json');

require_once "config.php";
require_once "functions.inc.php";
require_once $CONFIG['database']['dir']."functions.inc.php";

$SessionManager = new SessionManager();
$SessionManager->sessionStart('colibri');
allowOnlyUntilUserClass(0,true);



$filter = getFilter($_GET['filter']);




/* return path sanitized: relative from upload_dir
 *
 * @return String
**/
function sanitizePath($s=""){
	return preg_replace( "/\/+/", "/", preg_replace( "/^\/+|\.+\/|\/+$/", "", trim($s) ) )."/";
}




//control if it's requested a specific directory
//are allowed only sub-directory of upload_dir
$folder = $CONFIG['upload_dir'];
$subfolder = "";
if (isset($_GET['folder'])){
	$subfolder = sanitizePath($_GET['folder']);
	if ($subfolder !== "/") $folder .= $subfolder;
	else $subfolder = "";
	if (!is_dir($folder)) $folder = $CONFIG['upload_dir'];
}

if ($subfolder){
	if (in_array(pathinfo($folder)['filename'], $CONFIG['hidden_dirs'])) jsonError("La cartella è protetta o inesistente.");
}




//create response...
$response = [
	"uploads_dir" => $CONFIG['upload_dir'],
	"thumbs_dir" => $CONFIG['default_thumb']['dir'],
	"scanned_dir" => $subfolder,
	"folders" => [],
	"files" => []
];

foreach( array_diff(scandir($folder), ['..', '.']) as $k => $f ){
	//directories
	if (is_dir($folder.$f)){
		if (in_array($folder.$f, $CONFIG['hidden_dirs'])) continue;
		$response['folders'][] = $f;
	}
	//files
	else{
		$info = pathinfo($folder.$f);
		/* pathinfo('/www/htdocs/inc/lib.inc.php') returns array:
		* 'dirname'			/www/htdocs/inc
		* 'basename'		lib.inc.php
		* 'extension'		php
		* 'filename'		lib.inc
		*/
		$group = getFileGroup($info['extension']);
		if ($group === false || ($filter!==false && $filter !== $group)) continue;
		
		if ($group === 0){
			//images: get width and height
			list($w, $h) = @getimagesize($folder.$f);
			if (!$w || !$h) continue;
			$response['files'][] = [
				"f" => $info['basename'],
				"e" => $info['extension'],
				"g" => $group,
				"s" => human_filesize($folder.$f),
				"w" => $w,
				"h" => $h
			];
		}
		else{
			//other files
			$response['files'][] = [
				"f" => $info['basename'],
				"e" => $info['extension'],
				"g" => $group,
				"s" => human_filesize($folder.$f)
			];
		}
	}
}

jsonSuccess($response);
?>