<?php

class FMConfig extends ColibriConfig{
	//constants - static
	const FM_VERSION		= "1.1";			//current Colibry System Version
	const FM_RELEASE		= "";				//release (alpha, beta, multilang, release candidate...)
	//---------------------------------------------------------
	// this setting are all editable. Pay attention on all the
	// comments which explain dangers and how to use variables
	//---------------------------------------------------------
	public $settings		= [
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
			* if - for example - you want to put  default_thumb  into the  upload_dir
			* then you have to set default_thumb to "./setup-example/uploads/thumbs/"
			* and set hidden_dirs to ["thumbs"]
			*/
			'sizes' => [122,91],				//do not change!
			'resize' => 'crop',				//do not change!
				/* ---------- resize options: ----------
				* 0 / exact			image stretched to new dimensions
				* 1 / portrait		image ratio preserved to new height. width automatically calculated
				* 2 / landscape	image ratio preserved to new width. height automatically calculated
				* 3 / auto			image resized to best fit... 0 / 1 / 2... boh.
				* 4 / crop			image resized and fitted in crop dimensions. image will not be stretched!
				* see image magician php for more options
				*/
			'filters' => [],					//(optional)
				/* ---------- filters available: ----------
				* vintage
				* greyScale / greyScaleEnhanced / greyScaleDramatic
				* blackAndWhite
				* sepia
				* negative
				*
				* complex filters... TODO. for now only non-argumented filters
				*
				* see image magician php for more options description.
				*/
			'quality' => 95					//optional: default = 100.
		],
		
		//you can add custom thumbs as an array. every item of this array must be in the form viewed in "default_thumb"
		"custom_thumbs" => [],
		
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
								'psd',
								'ai'],
			//Video
			'video'		=> ['mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "webm",
								"flv"],
			//Audio
			'music'		=> ['mp3', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav'],
			//Archives
			'archives'	=> ['zip', 'rar', 'gz', 'tar',
								'iso', 'dmg'],
		],
		
		'max_file_size' => [//size in MegaByte [MB]
			'img'			=> 20,
			'file'		=> 100,
			'video'		=> 100,
			'music'		=> 100,
			'archives'	=> 100,
		],
		
		'allow_overwrite_file' => true
	];
	
	
	function __construct($initialize = true){
		
		//initialize the parent constructor
		parent::__construct();
		
		//run FM manager constructor?
		if ($initialize){
			//hidden dirs to complete relative path
			foreach($this->settings['hidden_dirs'] as &$d){
				$d = $this->settings['upload_dir'] . $d;
			}
			unset($d);
			//max file sizes to bytes
			foreach($this->settings['max_file_size'] as &$size){
				$size *= 1e6;
			}
			unset($size);
		}
	}

}

?>