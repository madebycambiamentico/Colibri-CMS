<?php namespace WebSpace;

/**
* manages active plugins in the website code.
*
* TODO...
* plugins should have an "importance" flag to use as ordinator. for example an importance of 0 is the lowest,
* so the plugin will be loaded past all. This means however that the last parsing of any variable (if any) will be
* made by that last plugin. flags can be global (for groups) or local (for positions) [read ahead].
* -----------------------------------------------------------------------------
* IT IS THE TEMPLATE DESIGNER DUTY TO DO THE CORRECT IMPLEMENTATION OF THIS PLUGIN MANAGER!!!
* -----------------------------------------
* plugins are divided in "groups": head, style, js, body, ethereal.
* (the next suggestions are to be considered as coding standard to be followed)
* - "head" should add html into the <head>
* - "style" should add stylesheet into the <head>
* - "js" should add script links into the <head> or in the <body>
* - "body" should add html into the <body>
* - "ethereal" should do nothing but run before or after all the html.
* -----------------------------------------
* each group can be separated into "positions": top, bottom, auto, manual
* - generally top will add something at the beginning of a group
* - generally bottom will add something at the end of a group
* - generally auto will add something at the convenient/default place in a group
* - manual stand for not put anything but expect a manual call in the template code
* ---------
* - top:
*   1. in "head" means before <title> and <meta> tags
*   2. in "style" means nothing (*will be ignored*)
*   3. in "js" means the script will be loaded in the head, after the style properties. In this case be sure the
*      theme properly load before all the dependecies you need (e.g. jquery)
*   4. in "body" means that all content will be parsed right after the <body> tag
*   5. in "ethereal" means that all content will be parsed before anything is printed (before <!DOCTYPE>)
* ---------
* - bottom:
*   1. in "head" means before the </head> closure
*   2. in "style" means nothing (*will be ignored*)
*   3. in "js" means the script will be loaded right before the </body> closure
*   4. in "body" means that all content will be parsed right before the </body> closure, but before scripts.
*   5. in "ethereal" means that all content will be parsed after everything is printed out (after </html>)
* ---------
* - auto:
*   1. in "head" means between <meta> and <script> (or <style>) tags
*   2. in "style" means before the </head> closure and <script> tag
*   3. in "js" is the same as 'bottom'
*   4. in "body" is the same as 'bottom'.
*   5. in "ethereal" is the same as 'top'.
* ---------
* - manual:
*   1. no actions are taken by the PluginsManager. It's the template which manage positions and calls.
*
* @author Nereo Costacurta (http://colibricms.altervista.org)
*/

class PluginsManager{
	
	private $plugins			= null;	//list of plugins to be loaded. it is an associative array "<plugin name>" => [ <properties> ]
		private $head			= null;	//list of plugins that do something in <head>
		private $style			= null;	//list of plugins that add styles
		private $js				= null;	//list of plugins that add scripts
		private $body			= null;	//list of plugins that do something in <body>
		private $ethereal		= null;	//list of plugins that do something not-directly html-related
	
	public $groups			= ['head', 'style', 'js', 'body', 'ethereal'];	//existent groups
	public $positions		= ['top', 'bottom', 'auto', 'manual'];				//existent positions
	
	
	
	function __construct(){
		//supported 'top', 'bottom', 'auto', 'manual'
		$this->ethereal = ($this->body = ($this->js = ($this->head = [
			'top' => [], 'bottom' => [], 'auto' => [], 'manual' => []
		])));
		//supported 'auto', 'manual'
		$this->style = [
			'auto' => [], 'manual' => []
		];
		//load active plugins
		$this->load_plugins();
	}
	
	
	
	/**
	* store active plugins in memory
	*
	* plugins must be registered and activated. it will be stored the folder path to the plugin.
	* plugin may have multiple folder structure. loaders should be marked in their json section (TODO)
	* If a plugin has been marked as "only_manual", then this will override all the plugin directives
	* on their positions. this plugins can still be called by the load function.
	*/
	private function load_plugins(){
		//TODO...
		//search in database or fetch a generated json?
	}
	
	
	
	/**
	* includes plugins to be run
	*
	* Any plugin loader should be placed in "/plugins/<plugin_path>/<group>_<position>.fn.php".
	* -- For example the default comment plugin could be in: "/plugins/Colibri/comments/body-auto.fn.php".
	* Generic plugins that don't require a positioning, position can be omitted in file name
	* -- For example the default comment plugin is in: "/plugins/Colibri/comments/body.fn.php".
	*
	* @param (str) $plugin						The plugin name (path from plugin directory) to be loaded
	* @param (str) $group						One of the group names in $this->groups
	* @param (str) $pos [optional]			One of the position names in $this->positions.
	*													If null check "non-positioning" plugin file. $fallback should be true. default: null
	* @param (bool) $fallback [optional]	If true allow fallback to "non-positioning" plugin file. default: true
	*
	* @return (bool)  Success of plugin inclusion.
	*/
	public function load_single_plugin( $plugin, $group, $pos = null, $fallback = true ){
		$plugin_file = CMS_INSTALL_DIR . '/plugins/' . $plugin . '/' . $group . '-' . $pos . '.fn.php';
		if ($pos && is_file($plugin_file)){
			return (false === @include_once($plugin_file));
		}
		elseif ($allow_fallback){
			$plugin_file = CMS_INSTALL_DIR . '/plugins/' . $plugin . '/' . $group . '.fn.php';
			if (is_file($plugin_file)){
				return (false === @include_once($plugin_file));
			}
			else
				return false;
		}
		else
			return false;
	}
	
	
	
	/**
	* includes all plugins to be run at certain position
	*
	* Any plugin loader should be placed in "/plugins/<plugin_path>/<group>_<position>.fn.php".
	* -- For example the default comment plugin could be in: "/plugins/Colibri/comments/body-auto.fn.php"
	*    In this case <plugin_path> = "Colibri/comments", <group> = "body" and <position> = "auto".
	*
	* @see load_single_plugin()
	*
	* @param (str) $group		One of the group names in $this->groups
	* @param (str) $pos			One of the position names in $this->positions
	*/
	public function run_plugins( $group, $pos ){
		if (isset($this->{$group}[$pos])){
			foreach($this->{$group}[$pos] as $plugin){
				$this->load_single_plugin( $plugin, $group, $pos );
			}
		}
	}
}

?>