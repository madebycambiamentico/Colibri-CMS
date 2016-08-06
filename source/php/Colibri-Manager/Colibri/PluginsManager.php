<?php namespace Colibri;

/**
* manages active plugins in the manager page code.
*
* TODO...
* (?) plugins could have an "importance" flag to use as ordinator. for example an importance of 0 is the lowest,
* so the plugin will be loaded past all. This means however that the last parsing of any variable (if any) will be
* made by that last plugin. (?)
* -----------------------------------------
* PLUGINS ARE PAGE-BASED. THIS CLASS PARSE ONLY A SET OF PLUGIN FOR A GIVEN PAGE!
* Available pages are stored in "/manager/". The key name for every page is the file name (without extension)
* Exception is the quick edit popup in articles list (TODO).
* - albums
* - dashboard
* - editor
* - options
* - profile
* - profiles-manager
* - ... TODO (popups...)
* -----------------------------------------
* plugins are divided in "groups": style, js, center, right, db and postdb.
* - "style" add stylesheet before the </head>, after standard page styles.
* - "js" add deferred script links before </body>, after jQuery.
* - "popup" add html to be used for popups. Designers should use \Colibri\Popups class ready templates, but custom html is permitted.
* - "center" add html into the center panel in manager pages.
* - "right" add html into the right panel in manager pages (or bottom for small screens).
* - "db" to edit standard queries. you must read the Colibri code to know how to utilize that queries.
* - "postdb" to add database queries of your own when standard queries has succeded
*
* @author Nereo Costacurta (http://colibricms.altervista.org)
*/

class PluginsManager{
	
	public $available = null;		//list of plugins to be loaded. it is an associative array "<plugin name>" => [ <properties> ]
	
	public $style			= [];		//list of plugins that add styles
	public $js				= [];		//list of plugins that add scripts
	public $popup			= [];		//list of plugins that add popups
	public $center			= [];		//list of plugins that add something in center panel
	public $right			= [];		//list of plugins that add something in right panel
	public $db				= [];		//list of plugins that do something in database when form is submitted
	public $postdb			= [];		//list of plugins that do something in database when form is submitted
	
	public $positions			= ['js', 'style', 'popup', 'center', 'right', 'db', 'postdb'];	//existent positions
	
	
	
	/**
	* call parse_plugins_folder() and save (not an option) available plugins in list.json
	* can set the filtered list of plugins for a specific page meanwhile.
	*
	* @param (bool) $skip_update_list [optional]	If false list.json won't be updated.
	* @param (string) $init_page [optional]		Tell to fetch plugin properties for a manager page. if empty nothing will be fetched.
	*															Name of manager page is founded in index.php, in english.
	* @param (array) $filter [optional]				Same as $filter parameter in fetch_plugins()
	*/
	function __construct($update_json_list=true, $init_page=null, $filter=null){
		if ($update_json_list){
			$this->parse_plugins_folder($init_page, $filter, false);
		}
		elseif ($init_page){
			$this->fetch_plugins($init_page, $filter);
		}
	}
	
	
	
	private function reset_plugins(){
		$this->plugins		= [];
			$this->js		= [];
			$this->style	= [];
			$this->center	= [];
			$this->right	= [];
			$this->db		= [];
	}
	
	
	
	/**
	* parse plugins folder and save plugin list
	*
	* @param (string) $page [optional]	tell to fetch plugin properties for a manager page. if empty nothing will be fetched.
	*												name of manager page is founded in index.php, in english.
	* @param (bool) $skip_update_list [optional] If false list.json won't be updated.
	*/
	public function parse_plugins_folder($page=null, $filter=null, $skip_update_list=false){
		if (!empty($filter)){
			$filter = array_merge(
				["installed" => true, "active" => true],
				$filter
			);
			if (!$filter["installed"]) $filter["active"] = false;
		}
		//plugins can be ACTIVE or DEACTIVATED, INSTALLED or NOT INSTALLED.
		//current json containing installed + status of plugins
		$plugins_json = $this->get_plugins_status();
		$this->available = [];
		//reset plugins if you have to fetch (again)
		if ($page) $this->reset_plugins();
		//-- authors:
		$authors = array_slice( scandir(\CMS_INSTALL_DIR . "/plugin/"), 2 );
		foreach ($authors as $i => $af){
			if (!is_dir(\CMS_INSTALL_DIR . "/plugin/" . $af)){
				unset($authors[$i]);
				continue;
			}
			//-- plugins from that author:
			$a_dir = \CMS_INSTALL_DIR . "/plugin/{$af}";
			$plugins = array_slice( scandir($a_dir), 2 );
			foreach ($plugins as $j => $pf){
				$ap_dir = $a_dir.'/'.$pf;
				if (!is_dir($ap_dir)){
					unset($plugins[$j]);
					continue;
				}
				//add plugin into list...
				$director_json = "{$ap_dir}/director.json";
				if (is_file($director_json) && ($director_json = file_get_contents($director_json)) && ($director_json = json_decode($director_json,true))){
					//conformed plugin:
					$plugin_name = "{$af}/{$pf}";
					//-- will update list.json
					$this->available[$plugin_name] = [
						"installed" => (isset($plugins_json[$plugin_name]) && $plugins_json[$plugin_name]['installed']),
						"active" => (isset($plugins_json[$plugin_name]) && $plugins_json[$plugin_name]['active']),
						"custom" => (isset($director_json['options']['custom']) ? $director_json['options']['custom'] : '')
					];
					//-- fetch plugin for inclusions in manager page
					if (
						!is_null($filter) &&
						$this->available[$plugin_name]['installed'] !== $filter['installed'] &&
						$this->available[$plugin_name]['active'] !== $filter['active']
					){
						continue;
					}
					if ($page && 'custom' !== $page){
						//fetch plugin components for page $page.
						if (isset($director_json['options'][$page])){
							foreach ($director_json['options'][$page] as $pos => $url){
								$this->{$pos}[] = $plugin_name.'/'.$url;
							}
						}
					}
				}
			}
		}
		//save current available plugins
		//they are stored as: "<author>/<plugin>" => ['intalled' => (bool), 'active' => (bool)]
		if (!$skip_update_list)
			$this->update_plugins_status();
	}
	
	
	
	/**
	* get current saved list.json which contains plugin status.
	*
	* @param (bool) $update_available [optional] If true update $this->available from list.json. default: true.
	*
	* @return (array)		List of "<plugin path>" => <properties>
	*							properties are: "custom" (the standalone page for that plugin), "installed" and "active"
	*/
	public function get_plugins_status($update_available=true){
		//plugins can be ACTIVE or DEACTIVATED, INSTALLED or NOT INSTALLED.
		//search in current json
		$plugins_json = \CMS_INSTALL_DIR . "/plugin/list.json";
		if (is_file($plugins_json)){
			if ($plugins_json = @file_get_contents($plugins_json)){
				//get json in array mode
				if ($plugins_json = json_decode($plugins_json,true)){
					if ($update_available)
						$this->available = $plugins_json;
					return $plugins_json;
				}
				else{
					if ($update_available)
						$this->available = [];
					return [];
				}
			}
		}
		return [];
	}
	
	
	
	/**
	* write/overwrite list.json which contains plugin status.
	*
	* @param (array) $status_list [optional] If none provided the list is set to $this->available
	*
	* @return (bool) Success on writing list.json
	*/
	public function update_plugins_status($status_list=[]){
		//plugins can be ACTIVE or DEACTIVATED, INSTALLED or NOT INSTALLED.
		
		//fallback to $this->available if empty variable.
		if (empty($status_list)){
			if (is_null($this->available))
				$status_list = $this->available = $this->get_plugins_status();
			else
				$status_list = $this->available;
		}
		
		//put content into list.json
		$plugins_json = \CMS_INSTALL_DIR . "/plugin/list.json";
		if (false !== @file_put_contents($plugins_json, json_encode($status_list,JSON_PRETTY_PRINT), LOCK_EX))
			return true;
		else
			return false;
	}
	
	
	
	/**
	* store registered plugins in memory
	*
	* @param (string) $page			Manager page to search for plugins
	* @param (string) $filter		Filters to apply: "installed", "active". default are both true.
	*										Careful! If "active" filter is false => plugins fetched are only the de-activated ones!!!
	*										(same goes for "installed" filter)
	*/
	public function fetch_plugins($page, $filter=null){
		if (!empty($filter)){
			$filter = array_merge(
				["installed" => true, "active" => true],
				$filter
			);
			if (!$filter["installed"]) $filter["active"] = false;
		}
		//check if available plugins initialized
		if (is_null($this->available))
			$this->available = $this->get_plugins_status();
		//reset plugins if you have to fetch (again)
		$this->reset_plugins();
		//current json containing installed + status of plugins
		$plugins_json = $this->get_plugins_status();
		
		foreach ($plugins_json as $plugin_name => $prop){
			if (
				!is_null($filter) &&
				$this->available[$plugin_name]['installed'] !== $filter['installed'] &&
				$this->available[$plugin_name]['active'] !== $filter['active']
			){
				continue;
			}
			$director_json = \CMS_INSTALL_DIR . "/plugin/{$plugin_name}/director.json";
			if (is_file($director_json) && ($director_json = file_get_contents($director_json)) && ($director_json = json_decode($director_json,true))){
				//fetch plugin components for page $page.
				if (isset($director_json['options'][$page])){
					foreach ($director_json['options'][$page] as $pos => $url){
						$this->{$pos}[] = $plugin_name.'/'.$url;
					}
				}
			}
		}
	}
	
	
	
	/**
	* includes a plugin component to be included
	*
	* @param (str) $plugin						The plugin folder (path from plugin directory)
	* @param (str) $position					One of the group names in $this->positions
	* @param (str) $url							File to include (path from the plugin directory)
	*
	* @return (bool)  Success of plugin inclusion.
	*/
	public function load_single_plugin( $pos, $plugin_url ){
		global $Config;
		switch ($pos){
			case 'js': echo '<script src="'.$Config->script_path . 'plugin/' . htmlentities($plugin_url).'" defer></script>'; break;
			case 'style': echo '<stylesheet type="text/css" href="'.$Config->script_path . 'plugin/' . htmlentities($plugin_url).'" defer></script>'; break;
			default:
				return @include \CMS_INSTALL_DIR . '/plugin/' . $plugin_url;
		}
		return true;
	}
	
	
	
	/**
	* includes all plugins to be run at certain position
	*
	* @see load_single_plugin()
	*
	* @param (str) $position		One of the group names in $this->positions
	*/
	public function run_plugins( $position ){
		if (isset($this->{$position})){
			foreach($this->{$position} as $plugin_url){
				$this->load_single_plugin( $position, $plugin_url );
			}
		}
	}
}

?>