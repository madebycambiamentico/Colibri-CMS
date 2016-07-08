<?php

require_once("config.php");
require_once $CONFIG['database']['dir']."functions.inc.php";

require_once "php/queries.class.php";
require_once "php/templates.class.php";




//--------------------------------------------------------
function STOPFORDEBUG($die=true){
	global $requestedURL, $pathPieces, $mylang, $CONFIG;
	
	echo 'LANG : '.$mylang;
	
	echo '<br>a) $requestedURL:<pre>'.print_r($requestedURL,true).'</pre>';
	
	echo '<br>b) $pathPieces:<pre>'.print_r($pathPieces,true).'</pre>';
	
	echo '<br>c) $CONFIG["database"]:<pre>'.print_r($CONFIG['database'],true).'</pre>';
	echo '<br>c) $CONFIG[...]:<pre>'.
		'domain: '.print_r($CONFIG['domain'],true).'<br>'.
		'mbc_cms_dir: '.print_r($CONFIG['mbc_cms_dir'],true).'<br>'.
		'c_dir: '.print_r($CONFIG['c_dir'],true).
	'</pre>';
	
	echo '<br>d) $_SERVER:<pre>'.print_r($_SERVER,true).'</pre>';
	
	$die and die();
}
//--------------------------------------------------------

function noPageFound($str='Questa pagina non esiste.'){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	//STOPFORDEBUG(false);
	global $CONFIG,$web,$templatepath;
	if ($not_found_page = TEMPLATES::custom($web['template'],'not-found.php',true)){
		include $not_found_page;
		die('<!-- ERROR: '.htmlentities($str).' -->');
	}
	die($str);
}

//-----------------------------------
//should be fetched from database...
$known_langs = ['it','en','de','fr'];
//should contain 2-letters or 5-letters (e.g.: "en", "en-UK")
//-----------------------------------

function setPreferredLanguage($lang='it'){
	setcookie('lang', $lang, time()+(86400*7), "/", "", false, false); // 86400 = 1 day, for all domain directory, no domain restriction, no SSL, js can edit
}

function detectPreferredLanguage(){
	global $known_langs;
	
	if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], $known_langs))
		return $_COOKIE['lang'];
	
	$user_pref_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

	//set default language, in case neither one of the active ones are available.
	$lang = $known_langs[0];
	
	foreach($user_pref_langs as $idx => $lang) {
		$lang = substr($lang, 0, 2);
		if (in_array($lang, $known_langs)) {
			break;
		}
	}
	setPreferredLanguage($lang);
	return $lang;
}



//global variables
$pageid			= null;
$page				= null;
$web				= null;
$templatepath	= null;
$mylang			= detectPreferredLanguage();

//echo '<pre>'.print_r($_SERVER,true).'</pre>'; die;

//***************************************
//				redirect to page...
//***************************************
if (isset($_SERVER['REDIRECT_URL'])){
	
	if ($CONFIG['mbc_cms_dir']==='/')
		$requestedURL = substr($_SERVER['SCRIPT_URL'],1);
	else
		$requestedURL = str_ireplace($CONFIG['mbc_cms_dir'],"",$_SERVER['SCRIPT_URL']);
	$pathPieces = explode("/",$requestedURL);
	
	//STOPFORDEBUG();
	
	//------------------------ default pages / manager ------------------------
	//should be translated if any other language is used
	switch ($pathPieces[0]){
		case '':
			goto anchor_main; break;
		case 'signin':
		case 'iscriviti':
			exit("Questa operazione non &egrave; abilitata."); break;
		case 'login':
		case 'accedi':
			include('manager/login.php'); exit; break;
		case 'nuovo':
		case 'new':
		case 'editor':
			include('manager/editor.php'); exit; break;
		case 'albums':
			include('manager/albums.php'); exit; break;
		case 'opzioni':
		case 'options':
			include('manager/options.php'); exit; break;
		case 'profile':
		case 'profilo':
			include('manager/profile.php'); exit; break;
		case 'articles':
		case 'articoli':
			include('manager/articles.php'); exit; break;
		case 'dashboard':
		case 'bacheca':
			include('manager/bacheca.php'); exit; break;
		//default: continue script :)
	}
	
	$fixPathPieces = [];
	foreach ($pathPieces as $pp){
		if ($pp) $fixPathPieces[] = $pp;
	}
	
	//------------------------ articles or search results ------------------------
	
	//search website properties
	$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
	if (!$web = $pdostat->fetch(PDO::FETCH_ASSOC))
		noPageFound('Database corrotto [cod 001]');
	$pdostat->closeCursor();
	$templatepath = TEMPLATES::path($web['template']);
	if (!$web['multilanguage']) $mylang = null;
	
	
	
	
	if ($mylang && in_array($fixPathPieces[0],$known_langs)){
		$mylang = array_shift($fixPathPieces);
		setPreferredLanguage($mylang);
		if (empty($fixPathPieces)) goto anchor_main;
	}
	
	
	define('ISINDEX',false);
	
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
			$pdostat = ARTQUERY::query('byMap', [true, true, $mylang], [$map, $artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata [cod 003]');
			$pdostat->closeCursor();
			$pageid = $page['id'];
			
			//open single page template.
			require TEMPLATES::single($pageid, $page['idtype'], $web['template']);
			exit;
		}
		else{
			//---------------------------------------------
			//all articles by creation date + edit date
			$pdostat = ARTQUERY::query('byDate', [false, $mylang], [$artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetchAll(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata [cod 004]');
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
		
		$pdostat = ARTQUERY::query('byMap', [false, true, $mylang], [$map]);
		if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
			noPageFound('Nessuna pagina trovata [cod 005] ('.$map.')/'.$mylang);
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
//open main template if no mapped request set:
anchor_main:


//search website properties
$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
if (!$web = $pdostat->fetch())
	noPageFound('Database corrotto [cod 006]');
$pdostat->closeCursor();
$templatepath = TEMPLATES::path($web['template']);


//---------------------
//required translation:
//---------------------
//if index with "translate" GET request, then redirect to translated page (if exists), or redirect to home in other case.
if ($mylang && isset($_GET['translate'])){
	$translate = intval($_GET['translate'],10);
	$pdores = $pdo->query("SELECT isindex, isindexlang, remaplink FROM articoli WHERE lang='{$mylang}' AND idarticololang={$translate} LIMIT 1",PDO::FETCH_ASSOC) or
		noPageFound('Error querying translated page [cod 002]');
	//-------------------------
	//translated page available
	if ($r = $pdores->fetch()){
		//for mapped pages (no index) reload page...
		if (!($r['isindex'] || $r['isindexlang'])){
			header('Location: '.$CONFIG['mbc_cms_dir'].$r['remaplink']);
			exit;
		}
		//if this is an index page, then do not reload locations (it is already doing this in this very script :) )
	}
	//-----------------------------
	//translated page not available
	else{
		/*header('Location: '.$CONFIG['mbc_cms_dir']);
		exit;*/
		noPageFound('Nessuna traduzione di pagina trovata [cod 002/2]');
	}
}




define('ISINDEX',true);

//search for index page
//(?)else fill with dummy empty array(?)
$pdostat = ARTQUERY::query('index',[true, $mylang]);
if ($page = $pdostat->fetch()){
	$pdostat->closeCursor();
	$pageid = $page['id'];
}
elseif ($mylang){
	//fallback to standard index if not available in that language
	$pdostat = ARTQUERY::query('index',[true]);
	if ($page = $pdostat->fetch()){
		$pdostat->closeCursor();
		$pageid = $page['id'];
		$mylang = $page['lang'];
		setPreferredLanguage($mylang);
	}
	else{
		noPageFound('Pagina index mancante! [cod 007/lang]');
	}
}
else{
	noPageFound('Pagina index mancante! [cod 007/no-lang]');
}
$pdostat->closeCursor();
require TEMPLATES::main($web['template']);

?>