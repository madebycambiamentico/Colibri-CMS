<?php

require_once("config.php");
require_once $CONFIG['database']['dir']."functions.inc.php";

require_once "php/queries.class.php";
require_once "php/templates.class.php";

//die('<pre>'.print_r($_SERVER,true).'</pre>');

function noPageFound($str='Questa pagina non esiste.'){
	global $requestedURL, $pathPieces;
	
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	echo $str;
		echo '<br>a)<pre>'.print_r($requestedURL,true).'</pre>';
		echo 'b)<pre>'.print_r($pathPieces,true).'</pre>';
		global $CONFIG; echo 'c)<pre>'.print_r($CONFIG['database'],true).'</pre>';
		echo 'd)<pre>'.print_r($_SERVER,true).'</pre>';
	die;
}


function fix_script_url(){
	$script_url = NULL;
	//to do:
	//what order? SCRIPT_URL, REQUEST_URI, REDIRECT_URL?
	//to be determined... not entirely sure
	//and by the way: SCRIPT_URL is what I think it is?
	
	if (!empty($_SERVER['SCRIPT_URL']))   
		$script_url = $_SERVER['SCRIPT_URL'];
	elseif (!empty($_SERVER['REQUEST_URI'])) {
		$p = parse_url($_SERVER['REQUEST_URI']);
		$script_url = $p['path'];
	}
	elseif (!empty($_SERVER['REDIRECT_URL'])) 
		$script_url = $_SERVER['REDIRECT_URL'];
	else
		die('Cannot determine $_SERVER["SCRIPT_URL"].');

	$_SERVER['SCRIPT_URL'] = $script_url;
	return $script_url;
}


//global variables
$pageid			= null;
$page				= null;
$web				= null;
$templatepath	= null;

//echo '<pre>'.print_r($_SERVER,true).'</pre>'; die;

//***************************************
//				redirect to page...
//***************************************
if (isset($_SERVER['REDIRECT_URL'])){
	define('ISINDEX',false);
	
	fix_script_url();
	$requestedURL = str_ireplace($CONFIG['mbc_cms_dir'],"",$_SERVER['SCRIPT_URL']);
	$pathPieces = explode("/",$requestedURL);
	
	//------------------------ default pages / manager ------------------------
	//should be translated if any other language is used
	switch ($pathPieces[0]){
		case '': goto anchor_main; break;
		case 'signin':
		case 'iscriviti':
			exit("Questa operazione non &egrave; abilitata."); break;
		case 'login':
		case 'accedi':
			include('login.php'); exit; break;
		case 'nuovo-articolo':
		case 'new-article':
		case 'nuovo':
		case 'new':
		case 'editor':
			include('editor.php'); exit; break;
		case 'albums':
			include('albums.php'); exit; break;
		case 'options':
			include('options.php'); exit; break;
		case 'profile':
		case 'profilo':
			include('profile.php'); exit; break;
		case 'articles':
		case 'articoli':
			include('articles.php'); exit; break;
		case 'dashboard':
		case 'bacheca':
			include('bacheca.php'); exit; break;
	}
	
	$fixPathPieces = [];
	foreach ($pathPieces as $pp){
		if ($pp) $fixPathPieces[] = $pp;
	}
	
	//------------------------ articles or search results ------------------------
	
	//search website properties
	$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
	if (!$web = $pdostat->fetch(PDO::FETCH_ASSOC))
		noPageFound('Nessuna pagina trovata');
	$pdostat->closeCursor();
	$templatepath = TEMPLATES::path($web['template']);
	
	
	
	//if first 3 things are numbers, search for articles with this creation date.
	if (count($fixPathPieces) >= 3){
		
		//control if there's really a date, else probably a wrong url
		$artDate = $fixPathPieces[0].'-'.$fixPathPieces[1].'-'.$fixPathPieces[2];
		if (!preg_match("/\d{4}-\d{2}-\d{2}/",$artDate))
			noPageFound();
		
		//search article(s)
		if (isset($fixPathPieces[3])){
			//---------------------------------------------
			//strict map + creation date + edit date + full image
			$map = $fixPathPieces[3] . (isset($fixPathPieces[4]) ? '/'.$fixPathPieces[4] : '');
			$pdostat = ARTQUERY::query('byMap', [true, true], [$map, $artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata');
			$pdostat->closeCursor();
			$pageid = $page['id'];
			
			//open single page template.
			require TEMPLATES::single($pageid, $page['idtype'], $web['template']);
			exit;
		}
		else{
			//---------------------------------------------
			//all articles by creation date + edit date
			$pdostat = ARTQUERY::query('byDate', [], [$artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetchAll(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata');
			$pdostat->closeCursor();
			$pageid = $page['id'];
			
			//open multiple page template.
			require TEMPLATES::multi();
			exit;
		}
	}
	else{
		//---------------------------------------------
		//strict map + full image
		$map = $fixPathPieces[0] . (isset($fixPathPieces[1]) ? '/'.$fixPathPieces[1] : '');
		
		$pdostat = ARTQUERY::query('byMap', [false, true], [$map]);
		if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
			noPageFound('Nessuna pagina trovata');
		$pdostat->closeCursor();
		$pageid = $page['id'];
		
		//open single page template.
		require TEMPLATES::single($pageid, $page['idtype'], $web['template']);
		exit;
	}
}


//***************************************
//					main page...
//***************************************
//open main template if no request set:
anchor_main:

define('ISINDEX',true);

//search website properties
$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
if (!$web = $pdostat->fetch())
	noPageFound('Nessuna pagina trovata');
$pdostat->closeCursor();
$templatepath = TEMPLATES::path($web['template']);

//search for index page
//(?)else fill with dummy empty array(?)
$pdostat = ARTQUERY::query('index',[true]);
if ($page = $pdostat->fetch()){
	$pdostat->closeCursor();
	$pageid = $page['id'];
}
else{
	$page = [
		'remaplink' => '',
		'src' => null,
		'titolo' => $web['titolo'],
		'inbreve' => $web['descr'],
		'corpo' => '<p><q>'.$web['motto'].'</q></p>'.
						'<div>'.$web['descr'].'</div>'
	];
	$pageid = 0;
}
$pdostat->closeCursor();
require TEMPLATES::main($web['template']);

?>