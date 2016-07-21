<?php

if (!isset($response)){
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	die;
}

class COLIBRI_TEMPLATE_EDIT{

	public $ini = null;
	public $file = null;

	public function __construct($file){
		require_once "ini.class.php";
		$this->ini = INI_FILE::iread($file, true);
		if (!empty($this->ini)) $this->file = $file;
	}
	
	public function update($updates){
		$uini = updateini($updates);
		$ucss = updatecss();
		return ['ini' => $uini, 'css' => $ucss];
	}

	public function updateini($updates){
		if (empty($updates) || empty($this->ini)) return false;
		//map
		if (isset($updates['map'])){
			$this->ini['custom']['map_height'] = $updates['map']['height'];
			$this->ini['custom']['maps_1366'] = $updates['map']['map_1366'] ? 1 : 0;
			$this->ini['custom']['maps_800'] = $updates['map']['map_800'] ? 1 : 0;
			$this->ini['custom']['maps_520'] = $updates['map']['map_520'] ? 1 : 0;
			$this->ini['custom']['map_ext'] = $updates['map']['ext'];
		}
		//marker
		if (isset($updates['marker'])){
			if ($updates['marker']['x'] == $this->ini['custom']['mark_x'] && $updates['marker']['y'] == $this->ini['custom']['mark_y']){
				if (!isset($updates['map'])) return false;
			}
			$this->ini['custom']['mark_x'] = $updates['marker']['x'];
			$this->ini['custom']['mark_y'] = $updates['marker']['y'];
		}
		//write!
		return INI_FILE::iwrite($this->ini, $this->file, true);
	}



	public function updatecss(){
		if (empty($this->ini)) return false;
		//call after updateini
		//todo...
		return false;
	}

}

?>