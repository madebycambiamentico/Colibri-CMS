<?php namespace Colibri\Comments ;

die('Already done!');

require '../../../config.php';

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2); // only webmasters are meant to edit database.


$director = [

	'author'		=> 'Colibri',
	'title'		=> 'Highlightjs',
	'version'		=> '1.0.0',
	
	//tell manager if you have custom options on this plugin.
	'options'		=> [
		/*
		for manager pages: add associative array with this optional keys:
		- "css" to add custom style links.
		- "js" to add javascript links.
		- "center" to add html content in manager center panel (js too).
		- "right" to add html content in manager right panel (js too).
		- "db" to edit standard queries. you must read the Colibri code to know how to utilize that queries.
		- "postdb" to run queries when all standard has already executed.
		*/
		'editor'			=> [
			"right"			=> 'manager/highlight-R.inc.php',
			"db"				=> 'manager/highlight-db.inc.php'
		],
		//...
		//for a custom standalone option page, use "custom" as key.
		//the page will popup in manager plugins list as iframe.
		'custom'			=> 'manager/highlight-custom.php'
	],
	
	//template has more options. You can set where to put code and the preferred position.
	'template'		=> [
		//for each group, indicate where something has to be loaded
		//groups available: head, style, js, body, ethereal
		//position availables: top, bottom, auto, manual
		'style'			=> [
			'auto' => [
				'//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/styles/default.min.css'
			]
		],
		'js'			=> [
			'auto' => [
				'//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/highlight.min.js'
			]
		]
	],
	
	//comment on the plugin, alert about dependencies. html formatted!
	'require'		=> "<i>Colibrì Highlightjs plugin &copy;2016</i> will highlight code on your pages.<br>".
							"Default library is hosted by <a href='//cdnjs.cloudflare.com/'>cdnjs.cloudflare.com</a>, but you can change this option to local files in the customization page of this plugin."

];


if (isset($_GET['json']))
{
	//want json
	header('Content-Type: application/json');
	if (isset($_GET['create']) || isset($_GET['reset'])){
		//want to create director.json (or reset its content)
		$json_director = json_encode($director, JSON_PRETTY_PRINT);
		if (false !== @file_put_contents('director.json', $json_director, LOCK_EX)){
			echo $json_director;
		}
		else{
			echo json_encode(['error' => "Impossibile creare director.json"]);
		}
	}
	else{
		echo json_encode($director);
	}
}
elseif (isset($_GET['print']))
{
	//for debug you want to see the result
	echo '<pre>' . htmlentities(json_encode($director, JSON_PRETTY_PRINT)) . '</pre>';
}
else{
	//include "director.php" gives you the array $director :)
	return $director;
}

