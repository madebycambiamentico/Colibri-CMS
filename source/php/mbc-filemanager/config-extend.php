<?php

/**
* Extend the default $Config with FM properties
*
* Utilize "/php/Config.class.php" and "/config.php".
* Description in comment are meant to edit the mbc-filemanager plugin standalone. Editing this version
* which is integrated in Colibrì System can cause malfunction in the CMS environment.
*
* @see ColibriConfig
*/

$Config->FM = [
	"upload_dir" => "../../uploads/",	//relative path from filemanager dir. MUST end with "/".
													//all folders must be lowercase
													//Do not set as sub-path of default_thumb in any of your dreams!
	"hidden_dirs" => [],						//array of sub-directories to hide
													//(all folders must be lowercase)
	"default_thumb" => [
		'dir' => "../../img/thumbs/",		//relative path from filemanager dir. MUST end with "/".
													//all folders must be lowercase
													//Do not set as sub-path of upload_dir if not set in exceptions (hidden_dirs)!
		/* ---------- setup hidden thumb dir ----------
		* if - for example - you want to put ["default_thumb"] into the ["upload_dir"]
		* then you have to set default_thumb to "./setup-example/uploads/thumbs/"
		* and add "thumbs" to ["hidden_dirs"] list.
		*/
		'sizes' => [122,91],				//do not change!
		'resize' => 'crop',				//do not change!
			/* ---------- resize options: ----------
			* 0 / exact			image stretched to new dimensions
			* 1 / portrait		image ratio preserved to new height. width automatically calculated
			* 2 / landscape	image ratio preserved to new width. height automatically calculated
			* 3 / auto			image resized to best fit... 0 / 1 / 2... who knows.
			* 4 / crop			image resized and fitted in crop dimensions - image will not be stretched
			* see image magician php for more options
			*/
		'filters' => [],					//(optional)
			/* ---------- available filters: ----------
			* vintage
			* greyScale / greyScaleEnhanced / greyScaleDramatic
			* blackAndWhite
			* sepia
			* negative
			*
			* complex filters... TODO. for now only non-argumented filters
			* see image magician php for more options description.
			*/
		'quality' => 95					//(optional) 1 to 100
	],
	

	"custom_thumbs" => [],		//you can add custom thumbs as an array.
										//every item of this array must have the same structure you see in ['default_thumb']
	
	'allowed_ext' => [
		//Images
		'img'			=> ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'],
		//Files
		'file'		=> ['doc', 'docx', 'rtf',
							'txt', 'log',
							'pdf',
							'xls', 'xlsx', 'csv',
							'html', 'xhtml', 'xml',
							'sql', 'sqlite', 'db',
							'ppt', 'pptx',
							'odt', 'ots', 'ott', 'odb', 'odg', 'otp', 'otg', 'odf', 'ods', 'odp',
							'css', 'js',
							'ade', 'adp', 'mdb', 'accdb',
							'fla',
							'psd', 'ai'],
		//Video
		'video'		=> ['mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "webm", "flv"],
		//Audio
		'music'		=> ['mp3', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav'],
		//Archives
		'archives'	=> ['zip', 'rar', 'gz', 'tar',
							'iso', 'dmg'],
	],
	
	'max_file_size' => [					//max allowed upload size in MegaByte [MB]
		'img'			=> 20,				//see ['allowed_ext'] for complete set of extension for each group.
		'file'		=> 100,
		'video'		=> 100,
		'music'		=> 100,
		'archives'	=> 100,
	],
	
	'allow_overwrite_file' => true	//if false will silently skip any upload which would overwrite existent uploaded files.
];






//---------------------------------------------
// TRANSFORM PROPERTIES TO UTILIZE DIRECTLY IN
// FILE MANAGER FUNCTIONS
//---------------------------------------------


//hidden dirs to complete relative path
foreach($Config->FM['hidden_dirs'] as &$d){
	$d = $Config->FM['upload_dir'] . $d;
}
unset($d);

//max file sizes to bytes
foreach($Config->FM['max_file_size'] as &$size){
	$size *= 1e6;
}
unset($size);

?>