<?php namespace Colibri\Comments ;

die('Already done!');

require '../../../config.php';

//control login
$SessionManager = new \Colibri\SessionManager;
$SessionManager->sessionStart('colibri');
allow_user_from_class(2); // only webmasters are meant to edit database.


$director = [

	/*
	author of the plugin. Must be a valid folder name.
	It's better not to leave spaces or use strange symbols (generally limit to a-z0-9).
	As general rule:
	- you should not use accented chars: ò à ù è é ì etc.
	- never use this chars: \ / : * ? # ^ % $ ' " ! | < >
	*/
	'author'		=> 'Colibri',
	
	//plugin name. Must be a valid folder name.
	//see author for chars allowed...
	'title'		=> 'Comments',
	
	//plugin version
	'version'		=> '1.2.0',
	
	//tell manager if you have custom options on this plugin.
	//keep in mind that the option "enable/disable plugin" is already covered in manager.
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
			"right"			=> 'manager/comments-editor-R.inc.php',
			"db"				=> 'manager/comments-editor-db.inc.php'
		],
		'options'		=> [
			"right"			=> 'manager/comments-options-R.inc.php',
			"db"				=> 'manager/comments-options-db.inc.php'
		],
		//...
		//for a custom standalone option page, use "custom" as key.
		//the page will popup in manager plugins list as iframe.
		'custom'			=> 'manager/comments-custom.php'
	],
	
	//template has more options. You can set where to put code and the preferred position.
	'template'		=> [
		//for each group, indicate where something has to be loaded
		//groups available: head, style, js, body, ethereal
		//position availables: top, bottom, auto, manual
		'ethereal'		=> [
			'top' => ['template/comments-session.inc.php']
		],
		'style'			=> [
			'auto' => [
				'/css/modalbox.min.css',
				'TinyEditor/style.min.css',
				'css/comments.dark.min.css'
			]
		],
		'js'			=> [
			'auto' => [
				//modal box (from Colibrì)
				'/js/simple-modal-box.min.js',
				
				//TinyEditor - should be loaded dynamically on need.
				// 'TinyEditor/tinyeditor.colibri.js',
				
				//main script
				'js/comments.min.js'
			]
		],
		'body'			=> [
			'auto' => ['template/comments.inc.php'],
			'bottom' => ['template/comments.modalbox.inc.php']
		]
	],
	
	//comment on the plugin, alert about dependencies. html formatted!
	'require'		=> "<i>Colibrì Comment plugin &copy;2016</i> will enable users to comment all your pages.<br>".
							"In the options you can choose if comments can be made by everyone, by users only, and ".
							"even the minimum class of users that are allowed to comment.<br>".
							"<b>The plugins require jquery 1 or 2, latest version!</b>"

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

