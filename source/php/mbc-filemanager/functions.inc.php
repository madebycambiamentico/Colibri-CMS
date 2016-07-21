<?php

/* retrieve filter for file search
 *
 * @return Int | Bool(false)
 * in order: 'img' | 'file' | 'video' | 'music' | 'archives' | false
**/
function getFilter($filter){
	if (isset($filter)){
		switch ((string) $filter){
			case '0': case 'img': case 'image': case 'images':
				return 0;
			break;
			case '1': case 'file': case 'files':
				return 1;
			break;
			case '2': case 'media': case 'video': case 'videos':
				return 2;
			break;
			case '3': case 'music': case 'audio': case 'audios':
				return 3;
			break;
			case '4': case 'archives': case 'zip': case 'misc': case 'miscellaneous':
				return 4;
			break;
			default:
				return false;
		}
	}
	return false;
}


/* retrieve filter for file search
 *
 * @return String | Bool(false)
 * in order: 'img' | 'file' | 'video' | 'music' | 'archives' | false
**/
function getGroupFilter($filter){
	if (isset($filter)){
		switch ((string) $filter){
			case '0': case 'img': case 'image': case 'images':
				return 'img';
			break;
			case '1': case 'file': case 'files':
				return 'file';
			break;
			case '2': case 'media': case 'video': case 'videos':
				return 'video';
			break;
			case '3': case 'music': case 'audio': case 'audios':
				return 'music';
			break;
			case '4': case 'archives': case 'zip': case 'misc': case 'miscellaneous':
				return 'archives';
			break;
			default:
				return false;
		}
	}
	return false;
}


/* return file group from file extension
 *
 * @param String
 *
 * @return String | Bool(false)
**/
function getFileGroup($ext){
	global $Config;
	$count = 0;
	foreach ($Config->FM['allowed_ext'] as $allowed){
		if (in_array($ext,$allowed)) return $count;
		$count++;
	}
	return false;
}


/**
 * Give readable file weigth (ex. 65M, 34K)
 *
 * @param	object	$file
 * @param	int		$decimals
 *
 * @return string
 */
function human_filesize($file, $decimals = 2){
	$bytes = @filesize($file);
	if (!$bytes) return 0;
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}



/**
 * Cleanup filename
 *
 * @param  string  $str
 * @param  bool    $transliteration
 * @param  bool    $convert_spaces
 * @param  string  $replace_with
 * @param  bool    $is_folder
 *
 * @return string
 */
function fix_filename($str, $transliteration, $convert_spaces = false, $replace_with = "_", $is_folder = false){
	//preg_replace("([^\w\s\d\.\-_~,;:\[\]\(\]]|[\.]{2,})", '', $file)
	if ($convert_spaces) $str = str_replace(' ', $replace_with, $str);

	if ($transliteration){
		if (function_exists('transliterator_transliterate')){
			 $str = transliterator_transliterate('Accents-Any', utf8_encode($str));
		}
		else{
			$str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
		}
		$str = preg_replace("/[^a-zA-Z0-9\.\[\]_| -]/", '', $str);
	}

	$str = str_replace(array( '"', "'", "/", "\\" ), "", $str);
	$str = strip_tags($str);

	// Empty or incorrectly transliterated filename.
	// Here is a point: a good file UNKNOWN_LANGUAGE.jpg could become .jpg in previous code.
	// So we add that default 'file' name to fix that issue.
	if (strpos($str, '.') === 0 && $is_folder === false) $str = 'file' . $str;

	return trim($str);
}



/**
 * Correct strtoupper handling
 *
 * @param  string  $str
 *
 * @return  string
 */
function fix_strtoupper($str){
	if (function_exists('mb_strtoupper')) return mb_strtoupper($str);
	return strtoupper($str);
}

/**
 * Correct strtolower handling
 *
 * @param  string  $str
 *
 * @return  string
 */
function fix_strtolower($str){
	if (function_exists('mb_strtolower')) return mb_strtolower($str);
	return strtolower($str);
}



function fix_path($path, $transliteration, $convert_spaces = false, $replace_with = "_"){
	$path = str_replace(array('../','./'), "", $path);
	$path = preg_replace("/^\/+/","",$path);//remove multiple //// at beginning
	$path = preg_replace("/\/+/","/",$path);//multiple /// to /
	if (empty($path)) return "";
	
	$path = fix_strtolower($path,'UTF-8');
	
	$info = pathinfo($path);
	$tmp_path = $info['dirname'];
	$str = fix_filename($info['filename'], $transliteration, $convert_spaces, $replace_with);
	
	if ($tmp_path != "" && $tmp_path != ".") return $tmp_path.'/'.$str;
	
	else return $str;
}




/**
 * Recursive delete directory
 *
 * @param  string  $dir
 *
 * @return  bool
 */
function deleteDir($dir){
	if ( ! file_exists($dir)) return true;
	//delete file
	if ( ! is_dir($dir)) return unlink($dir);
	//find all sub-directories / files
	$items = array_diff(scandir($dir), array('.','..'));
	//recursive delete
	foreach ( $items as $item ){
		if ( ! deleteDir($dir . DIRECTORY_SEPARATOR . $item)) return false;
	}
	return rmdir($dir);
}




/**
 * Check if memory is enough to process image
 *
 * @param  string  $img
 * @param  int     $max_breedte
 * @param  int     $max_hoogte
 *
 * @return bool
 */
function image_check_memory_usage($img, $max_breedte, $max_hoogte){
	if (file_exists($img)){
		$K64 = 65536; // number of bytes in 64K
		$memory_usage = memory_get_usage();
		$memory_limit = abs(intval(str_replace('M', '', ini_get('memory_limit')) * 1024 * 1024));
		$image_properties = getimagesize($img);
		$image_width = $image_properties[0];
		$image_height = $image_properties[1];
		
		if (isset($image_properties['bits'])) 
			$image_bits = $image_properties['bits']; 
		else 
			$image_bits = 0;
		
		$image_memory_usage = $K64 + ($image_width * $image_height * ($image_bits) * 2);
		$thumb_memory_usage = $K64 + ($max_breedte * $max_hoogte * ($image_bits) * 2);
		$memory_needed = intval($memory_usage + $image_memory_usage + $thumb_memory_usage);

		if ($memory_needed > $memory_limit){
			ini_set('memory_limit', (intval($memory_needed / 1024 / 1024) + 5) . 'M');
			if (ini_get('memory_limit') == (intval($memory_needed / 1024 / 1024) + 5) . 'M')
				return true;
			else
				return false;
		}
		else
			return true;
	}
	else
		return false;
}