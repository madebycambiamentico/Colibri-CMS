<?php namespace Colibri\Comments ;

$director = [

	//author of the plugin. Must be a valid folder name.
	//It's better to not leave spaces or use strange symbols (generally limit to a-z0-9).
	'author'		=> 'Colibri',
	
	//plugin name. Must be a valid folder name.
	//It's better to not leave spaces or use strange symbols (generally limit to a-z0-9).
	'plugin'		=> 'Comments',
	
	//plugin version
	'version'		=> '1.0.0',
	
	//installer path from plugin folder.
	//The plugin folder must be "/<author>/<plugin>/..."
	'installer'		=> 'setup.php',
	
	//tell manager if you have custom options on this plugin.
	//keep in mind that the option "enable/disable plugin" is already covered in manager.
	'options'		=> [
		//for manager pages: add associative array with this optional keys:
		// - "css" to add custom style.
		// - "js" to add javascript.
		// - "center" to add html content in manager center panel (js too).
		// - "right" to add html content in manager right panel (js too).
		// - "db" to run queries when form is submitted. they will be run before standard queries.
		'editor'			=> [
			"js"				=> 'addon/comments-editor.js',
			"center"			=> 'addon/comments-editor-C.inc.php',
			"right"			=> 'addon/comments-editor-R.inc.php',
			"db"				=> 'addon/comments-editor-db.inc.php'
		],
		//...
		//for a custom option page, use "custom" as key.
		//the page will popup in manager plugins list as iframe.
		'custom'			=> 'addon/comment-options.php'
	],
	
	//call positioning in TEMPLATES
	'template'		=> [
		//for each group, indicate where something has to be loaded
		//groups available: head, style, js, body, ethereal
		//position availables: top, bottom, auto, manual
		'style'			=> [
			'auto' => ['css/comments.css']
		],
		'js'			=> [
			'auto' => ['js/comments.js']
		],
		'body'			=> [
			'bottom' => ['comments.php']
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
		if (false !== @file_put_contents('director.json', str_replace("    ","\t",$json_director), LOCK_EX)){
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
	echo '<pre>' . json_encode($director, JSON_PRETTY_PRINT) . '</pre>';
}
else{
	//include "director.php" returns array $director :)
	return $director;
}

