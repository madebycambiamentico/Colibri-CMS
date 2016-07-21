<?php

/**
* Set custom thumbnail sizes.
*
* Will add different sizes to uploaded images.
* how to use? search for "php image magician" and study "mbc-filemanager/config.php"
*
* @see /php/mbc-filemanager/config.php
*/

$Config->FM["custom_thumbs"] = [
	// ??? x 300 for galleries
	[
		'dir' => "300/",				//sub-directory of thumb directory. MUST end with "/".
		'sizes' => [300,300],		//landscape needs width, portrait needs height!
											//the result is inverse (for portrait cut you have landscape images... i think... not clear)
		'resize' => 'portrait',
		'filters' => [],
		'quality' => 90
	],
	// 320 x 320 squares
	[
		'dir' => "320x320/",
		'sizes' => [320,320],
		'resize' => 'crop',
		'filters' => [],
		'quality' => 90
	],
	// 320 x 200 rects
	[
		'dir' => "320x200/",
		'sizes' => [320,200],
		'resize' => 'crop',
		'filters' => [],
		'quality' => 90
	],
	// landscape main images
	[
		'dir' => "L1024/",
		'sizes' => [1024,360],
		'resize' => 'crop',
		'filters' => ['greyScaleEnhanced'],
		'quality' => 75
	],
	// landscape main images - tablets
	[
		'dir' => "L768/",
		'sizes' => [768,270],
		'resize' => 'crop',
		'filters' => ['greyScaleEnhanced'],
		'quality' => 75
	],
	// landscape main images - mobile
	[
		'dir' => "L520/",
		'sizes' => [520,260],
		'resize' => 'crop',
		'filters' => ['greyScaleEnhanced'],
		'quality' => 75
	]
];

?>