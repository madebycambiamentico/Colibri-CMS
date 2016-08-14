<?php

require_once("config.php");


/**
* debug mode if cms doesn't behave as it should
*
* put this function anywhere to print server and cms variables.
* output content of $requestedURL, $pathPieces, CMS_LANGUAGE, $Config, $web and $_SERVER.
*
* @param (bool) $die		will stop script execution if true.
*/
function STOPFORDEBUG($die=true){
	global $requestedURL, $pathPieces, $Language, $Config, $web;
	//header('Content-Type: text/plain; charset=utf-8');
	$i=0;
	echo 			(++$i).') $Language:<pre>'.print_r($Language,true).'</pre>';
	
	echo '<br>'.(++$i).') $requestedURL:<pre>'.print_r($requestedURL,true).'</pre>';
	
	echo '<br>'.(++$i).') $pathPieces:<pre>'.print_r($pathPieces,true).'</pre>';
	
	echo '<br>'.(++$i).') $Config:<pre>'.print_r($Config,true).'</pre>';
	
	echo '<br>'.(++$i).') $web:<pre>'.print_r($web,true).'</pre>';
	
	echo '<br>'.(++$i).') $_SERVER:<pre>'.print_r($_SERVER,true).'</pre>';
	
	if ($die) die;
}



/**
* show custom 404 error or stop script printing given string.
*
* page 404 custom file (optional) should be placed in "/templates/<template_name>/not-found.php"
* if "not-found.php" is provided, a comment with $str content will be available at the bottom of the HTML code.
*
* @param (bool) $str [optional]		warning error on no page found.
*/
function noPageFound($str='Questa pagina non esiste.'){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	//STOPFORDEBUG(false);
	global $web;
	//echo \WebSpace\Template::custom($web['template'],'not-found.php',true);
	if ($not_found_page = \WebSpace\Template::custom($web['template'],'not-found.php',true)){
		include $not_found_page;
		die('<!-- ERROR: '.htmlentities($str).' -->');
	}
	die($str);
}


/**
* Get manager page path.
*
* @param (string) $page name of the page to acquire
*/
function get_manager_page($page){
	//define language
	global $Language;
	define('CMS_LANGUAGE', ($Language ? $Language->lang : null) );
	//return page path
	return CMS_INSTALL_DIR . "/manager/{$page}.php";
}



//global variables
$pageid			= null;		//article id
$page				= null;		//article, complete database result
$web				= null;		//content of `sito` database table
$Language		= null;		//contains preferred language (not null if site flagged multilanguage)


//STOPFORDEBUG();


//------------------------ search website properties ------------------------
$pdostat = $pdo->query("SELECT * FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
if (!$web = $pdostat->fetch(PDO::FETCH_ASSOC))
	noPageFound('Database corrotto [cod 001]');
$pdostat->closeCursor();

//set preferred language
if ($web['multilanguage']){
	$Language = new \Colibri\LanguageDetector( $web['default_lang'] );
}

//set template url (absolute, no domain)
\WebSpace\Template::set_path_url($web['template']);



//***************************************
// page has been redirected
// (not for index.php aka "/")
//***************************************

if (isset($_SERVER['REDIRECT_URL'])){
	
	if ($Config->script_path === '/')
		$requestedURL = substr($_SERVER['SCRIPT_URL'],1);
	else
		$requestedURL = str_ireplace($Config->script_path,"",$_SERVER['SCRIPT_URL']);
	$pathPieces = explode("/",rtrim($requestedURL, '/'));
	
	
	//STOPFORDEBUG();
	
	
	//------------------------ default pages / manager ------------------------
	//should be translated if any other language is used
	switch ($pathPieces[0]){
		case '':
			goto anchor_main; break;
		case 'signin':
		case 'iscriviti':
			require get_manager_page("signin"); exit;
		case 'login':
		case 'accedi':
			require get_manager_page("login"); exit;
		case 'editor':
		case 'nuovo':
			require get_manager_page("editor"); exit;
		case 'albums':
			require get_manager_page("albums"); exit;
		case 'options':
		case 'opzioni':
			require get_manager_page("options"); exit;
		case 'profile':
		case 'profilo':
			require get_manager_page("profile"); exit;
		case 'profiles':
		case 'profili':
			require get_manager_page("profiles-manager"); exit;
		case 'articles':
		case 'articoli':
			require get_manager_page("articles"); exit;
		case 'dashboard':
		case 'bacheca':
			require get_manager_page("dashboard"); exit;
		case 'plugins':
			require get_manager_page("plugins"); exit;
		//default: continue script :)
	}
	
	$fixPathPieces = [];
	foreach ($pathPieces as $pp){
		if ($pp) $fixPathPieces[] = $pp;
	}
	
	
	//override detected language if searching with another /<code>/...
	if ($Language && in_array($fixPathPieces[0],$Language->supported)){
		
		$Language->store_preferred_language( array_shift($fixPathPieces) );
		
		if (empty($fixPathPieces)) goto anchor_main;
	}
	
	/*****************************************************************
	 Here is defined the constant CMS_LANGUAGE, which is a shortcut to
	 $Language->lang in case of multilanguage site (otherwise is null)
	******************************************************************/
	define('CMS_LANGUAGE', ($Language ? $Language->lang : null) );
	
	
	//if first 3 things are numbers, search for articles with this creation date.
	if (count($fixPathPieces) >= 3){
		
		//control if there's really a date, else probably a wrong url
		$artDate = $fixPathPieces[0].'-'.$fixPathPieces[1].'-'.$fixPathPieces[2];
		if (!preg_match("/\d{4}-\d{2}-\d{2}/",$artDate))
			noPageFound("Richiesta data non corretta");
		
		//search article(s)
		if (isset($fixPathPieces[3])){
			//---------------------------------------------
			//strict map + creation date + edit date + full image
			$map = $fixPathPieces[3] . (isset($fixPathPieces[4]) ? '/'.$fixPathPieces[4] : '');
			$pdostat = \WebSpace\Query::query('byMap', [true, true, CMS_LANGUAGE], [$map, $artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata [cod 003]');
			$pdostat->closeCursor();
			$pageid = $page['id'];
			
			//open single page template.
			require \WebSpace\Template::single($pageid, $page['idtype'], $web['template']);
			exit;
		}
		else{
			//---------------------------------------------
			//all articles by creation date + edit date
			$pdostat = \WebSpace\Query::query('byDate', [false, CMS_LANGUAGE], [$artDate.'%', $artDate.'%']);
			if (!$page = $pdostat->fetchAll(PDO::FETCH_ASSOC))
				noPageFound('Nessuna pagina trovata [cod 004]');
			$pdostat->closeCursor();
			$pageid = $page['id'];
			
			//open multiple page template.
			require \WebSpace\Template::multi();
			exit;
		}
	}
	else{
		//---------------------------------------------
		//strict map + full image
		$map = $fixPathPieces[0] . (isset($fixPathPieces[1]) ? '/'.$fixPathPieces[1] : '');
		
		$pdostat = \WebSpace\Query::query('byMap', [false, true, CMS_LANGUAGE], [$map]);
		if (!$page = $pdostat->fetch(PDO::FETCH_ASSOC))
			noPageFound('Nessuna pagina trovata [cod 005] ('.$map.')/'.CMS_LANGUAGE);
		$pdostat->closeCursor();
		$pageid = $page['id'];
		
		//open single page template.
		require \WebSpace\Template::single($pageid, $page['idtype'], $web['template']);
		exit;
	}
}


//***************************************
//					main page...
//***************************************
//open main template if no mapped request set:
anchor_main:

/*****************************************************************
 Here is defined the constant CMS_LANGUAGE, which is a shortcut to
 $Language->lang in case of multilanguage site (otherwise is null)
******************************************************************/
define('CMS_LANGUAGE', ($Language ? $Language->lang : null) );


//---------------------
//translation request:
//---------------------
//if index with "translate" GET request, then redirect to translated page (if exists), or redirect to home in other case.
if (CMS_LANGUAGE && isset($_GET['translate'])){
	$translate = intval($_GET['translate'],10);
	$pdores = $pdo->query("SELECT isindex, isindexlang, remaplink FROM articoli WHERE lang='".CMS_LANGUAGE."' AND idarticololang={$translate} LIMIT 1",PDO::FETCH_ASSOC) or
		noPageFound('Error querying translated page [cod 002]');
	//-------------------------
	//translated page available
	if ($r = $pdores->fetch()){
		//for mapped pages (no index) reload page...
		if (!($r['isindex'] || $r['isindexlang'])){
			header('Location: '.$Config->script_path. CMS_LANGUAGE.'/'. $r['remaplink']);
			exit;
		}
		//if this is an index page, then do not reload locations (it is already doing this in this very script :) )
	}
	//-----------------------------
	//translated page not available
	else{
		noPageFound('Nessuna traduzione di pagina trovata [cod 002/2]');
	}
}


//search for index page
//(?)else fill with dummy empty array(?)
$pdostat = \WebSpace\Query::query('index',[true, CMS_LANGUAGE]);
if ($page = $pdostat->fetch()){
	$pdostat->closeCursor();
	$pageid = $page['id'];
}
elseif (!is_null(CMS_LANGUAGE)){
	noPageFound('Pagina index mancante per il tuo linguaggio!<br>Il webmaster deve ancora impostare il primo articolo con spunta "index". [cod 007/lang]');
}
else{
	noPageFound('Pagina index mancante! [cod 007/no-lang]');
}
$pdostat->closeCursor();


//STOPFORDEBUG(true);


require \WebSpace\Template::main( $web['template'] );

?>