<?php namespace WebSpace;

/**
* manages active plugins in the website code.
*
* TODO...
* (?) plugins could have an "importance" flag to use as ordinator. for example an importance of 0 is the lowest,
* so the plugin will be loaded past all. This means however that the last parsing of any variable (if any) will be
* made by that last plugin. (?)
* -----------------------------------------------------------------------------
* IT'S THE TEMPLATE DESIGNER DUTY TO DO THE CORRECT IMPLEMENTATION OF THIS PLUGIN MANAGER!!!
* -----------------------------------------
* GROUPS
*********
* plugins are divided in "groups": head, style, js, body, ethereal.
* (the next suggestions are to be considered as coding standard to be followed)
* - "head" should add html into the <head>
* - "style" should add stylesheet into the <head>.
*     - You should provide the file relative path from plugin folder.
*     - If you provide an url be sure to start it with "http"
* - "js" should add script links into the <head> or in the <body>.
*    (see notes on "style")
* - "body" should add html into the <body>
* - "ethereal" should do nothing but run before or after all the html.
* -----------------------------------------
* POSITIONS
************
* each group can be separated into "positions": top, bottom, auto, manual
* - generally top will add something at the beginning of a group
* - generally bottom will add something at the end of a group
* - generally auto will add something at the convenient/default place in a group
* - manual (OR ANYTHING ELSE) stands for not put anything but expect a manual call in the template code.
*   You can access manually governed plugins thru:
*   $this->plugins["<author>/<plugin>"]['template']["<group>"]['manual'];
*   where <author> is the author (e.g. "Colibri"), <plugin> is the plugin title (e.g. "Comments").
*   <group> is one in the groups described above.
* ---------
* - top:
*   1. in "head" means after <title> and before <meta> tags
*   2. in "style" means right after the standard template style blocks
*   3. in "js" means the script will be loaded in the head, after the style properties. In this case be sure the
*      theme properly load before all the dependecies you need (e.g. jquery)
*   4. in "body" means that all content will be parsed right after the <body> tag
*   5. in "ethereal" means that all content will be parsed before anything is printed (before <!DOCTYPE>)
* ---------
* - bottom:
*   1. in "head" means before the </head> closure
*   2. in "style" means after all the style blocks
*   3. in "js" means the script will be loaded right before the </body> closure
*   4. in "body" means that all content will be parsed right before the </body> closure, but before scripts.
*   5. in "ethereal" means that all content will be parsed after everything is printed out (after </html>)
* ---------
* - auto:
*   1. in "head" means between <meta> and <script> (after template standard <style>) tags
*   2. in "style" means after the "top" styles.
*   3. in "js" means before the "bottom" scripts.
*   4. in "body" means before "bottom", where the template designer place the call.
*   5. in "ethereal" means after "top".
* ---------
* - manual:
*   1. no actions are taken by the PluginsManager. It's the template which manage positions and calls.
*      So the template has hardcoded calls to plugins which have to be already intalled.
* -----------------------------------------
* Positions should contain an array of relative path from plugin folder, for each file to be loaded in that position.
* Exceptions are the groups "style" and "js" in which:
* - you can specify any external url starting with "http(s)://", or
* - you can specify an existing file from *installation* folder using url starting with "/".
*
* @author Nereo Costacurta (http://colibricms.altervista.org)
*/

class PluginsManager{
	
	public $plugins			= null;	//list of plugins to be loaded. it is an associative array "<plugin name>" => [ <properties> ]
		private $head			= null;	//list of plugins that do something in <head>
		private $style			= null;	//list of plugins that add styles
		private $js				= null;	//list of plugins that add scripts
		private $body			= null;	//list of plugins that do something in <body>
		private $ethereal		= null;	//list of plugins that do something not-directly html-related
	
	public $groups			= ['head', 'style', 'js', 'body', 'ethereal'];	//existent groups
	public $positions		= ['top', 'bottom', 'auto', 'manual'];				//existent positions
	
	
	
	function __construct(){
		//init empty arrays
		$this->reset_plugins();
		//load active plugins
		$this->fetch_plugins();
	}
	
	
	private function reset_plugins(){
		//supported 'top', 'bottom', 'auto', 'manual'
		$this->ethereal =
		($this->body =
		($this->js =
		($this->head =
		($this->style =
		[
			'top' => [], 'bottom' => [], 'auto' => []//, 'manual' => []
		]
		))));
	}
	
	
	
	/**
	* store active plugins in memory
	*/
	private function fetch_plugins(){
		//get active plugins in list.json
		global $Config;
		$listjson = \CMS_INSTALL_DIR . "/plugin/list.json";
		if (is_file($listjson)){
			//control json
			$listjson = @file_get_contents($listjson);
			if (false === $listjson) die("list.json not readable");
			$listjson = json_decode($listjson,true);
			if (false === $listjson) die("list.json corrupted");
			//filter only active plugins
			foreach ($listjson as $plug_folder => &$plug){
				if (!$plug['active'] || !$plug['installed']){
					unset($plug);
					continue;
				}
				else{
					//distribute plugin positions
					$plugin_dir = \CMS_INSTALL_DIR . "/plugin/{$plug_folder}/";
					$pluginjson = @file_get_contents($plugin_dir . "director.json");
					//control json
					if (false === $pluginjson){
						unset($plug);
						continue;
					}
					$pluginjson = json_decode($pluginjson,true);
					if (false === $pluginjson){
						unset($plug);
						continue;
					}
					//control if plugin does something in the template
					if (!isset($pluginjson['template'])){
						unset($plug);
						continue;
					}
					$plug['template'] = $pluginjson['template'];
					foreach ($pluginjson['template'] as $group => $positions){
						if (isset($this->{$group})){
							foreach($positions as $pos => $files){
								//manual or cusom position can be accessed through $this->plugins.
								if (!isset($this->{$group}[$pos])) continue;
								//when standard positions are distributed in the dedicated container.
								switch($group){
									//css and js will be added with <stylesheet> and <script> tags.
									case 'js': case 'style':
										foreach ($files as $file){
											//external url (http://...)
											if ( preg_match("#^http[s]?://#",$file) )
												$this->{$group}[$pos][] = $file;
											//local url (rel. path from installation dir)
											elseif ($file[0] === '/'){
												if ($file[1] === '/')
													$this->{$group}[$pos][] = $file;
												else
													$this->{$group}[$pos][] = $Config->script_path . substr($file,1);
											}
											//local url (rel. path from plugin dir)
											else
												$this->{$group}[$pos][] = $Config->script_path . "plugin/{$plug_folder}/" . $file;
										}
									break;
									//other files are INCLUDED (should be php files)
									default:
										foreach ($files as $file){
											$this->{$group}[$pos][] = $plugin_dir . $file;
										}
								}
							}
						}
						else{
							//plugins uses groups not standard... should I allow this?
							//doesn't make sense if not to put lot of manual things in one place...
							//but still doesn't seems a good choice to me.
							unset($plug);
							continue;
						}
					}
				}
			}
		}
		//update $this->plugins
		if (is_array($listjson))
			$this->plugins = $listjson;
		else
			$this->plugins = [];
		
		//die('<pre>'.print_r($this,true).'</pre>');
	}
	
	
	
	/**
	* print css stylesheet link
	*/
	public function print_css($css){
		echo '<link rel="stylesheet" type="text/css" href="'.htmlentities($css,ENT_QUOTES).'">';
	}
	
	
	/**
	* print js script link
	*/
	public function print_script($script){
		echo '<script src="'.htmlentities($script,ENT_QUOTES).'" defer></script>';
	}
	
	
	
	/**
	* includes all plugins to be run at certain position
	*
	* @param (str) $group		One of the group names in $this->groups
	* @param (str) $pos			One of the position names in $this->positions
	*/
	public function run_plugins( $group, $pos ){
		if (isset($this->{$group}[$pos])){
			switch ($group){
				case 'style':
					foreach($this->{$group}[$pos] as $plugin){
						$this->print_css( $plugin );
					}
				break;
				case 'js':
					foreach($this->{$group}[$pos] as $plugin){
						$this->print_script( $plugin );
					}
				break;
				default:
					foreach($this->{$group}[$pos] as $plugin){
						include( $plugin );
					}
			}
		}
	}
}

?>