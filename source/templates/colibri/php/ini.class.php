<?php
class INI_FILE{
	
	//this function create an ini file.
	//inputs are not sanitized, be aware!
	//inputs are wrapped in quotes to prevent problems with the php parser later on.
	static function iwrite($assoc_arr, $path, $has_sections=false) { 
		$content = "";
		//print sections too
		if ($has_sections) { 
			foreach ($assoc_arr as $key=>$elem) { 
				$content .= "[".$key."]\n"; 
				foreach ($elem as $key2=>$elem2) { 
					if(is_array($elem2)) { 
						for($i=0;$i<count($elem2);$i++) 
							$content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
					} 
					else if($elem2=="")
						$content .= $key2." = \n"; 
					else
						$content .= $key2." = \"".$elem2."\"\n"; 
				} 
			} 
		}
		//print without sections
		else { 
			foreach ($assoc_arr as $key=>$elem) { 
				if(is_array($elem)) { 
					for($i=0;$i<count($elem);$i++) { 
						$content .= $key."[] = \"".$elem[$i]."\"\n"; 
					} 
				} 
				else if($elem=="")
					$content .= $key." = \n"; 
				else
					$content .= $key." = \"".$elem."\"\n"; 
			} 
		} 

		//create file
		if (!$handle = fopen($path, 'w')) { 
			return false; 
		}
		$success = fwrite($handle, $content);
		fclose($handle); 

		return $success; 
	}
	
	//this function is here only for have all in one handler...
	static function iread($file, $with_sections=false){
		return parse_ini_file($file, $with_sections);
	}
}
?>