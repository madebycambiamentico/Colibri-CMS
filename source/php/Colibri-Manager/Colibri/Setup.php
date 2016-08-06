<?php namespace Colibri;

/**
* Initialize Colibrì CMS
*
* Write htaccess files, create initial database from the default provided in the installer.
* TODO: test php version, test cURL enabled, test MCRYPT enabled.
*
* @method (public) get_rewrite_rules_apache
* @method (public) get_permission_rules_db
* @method (public) insert_with_markers
* @method (public) save_mod_rewrite_rules
* @method (public) save_permission_rules_db
* @method (public) set_CMS_key
*/

class Setup {

	public $index = 'index.php';
	public $check = true;
	public $errors = [];
	public $logs = [];
	private $is_user_webmaster = false;
	
	
	public function __construct($run_checks_immediately = false){
		//check if user is logged in (user which are not cannot rename DB or update encryption key)
		$this->is_user_webmaster = $this->check_login();
		
		if ($run_checks_immediately){
			$this->add_log("running checks immediately:");
			$this->run_setup();
		}
	}
	
	private function check_login(){
		global $SessionManager;
		if (!isset($SessionManager){
			$SessionManager = new \Colibri\SessionManager();
			$SessionManager->sessionStart('colibri');
		}
		if (isset($_SESSION['uid']) && $_SESSION['uclass'] >= 2){
			return true;
		}
		return false;
	}
	
	private function add_log($string){
		$this->logs[] = date('H:i:s')." - ".$string;
	}
	
	private function add_error($string){
		$this->errors[] = date('H:i:s')." - ".$string;
	}
	
	public function get_errors(){
		if ($this->errors) return implode("<br>",$this->errors);
		else return 'no errors were stored in Setup class...';
	}
	
	public function get_logs(){
		if ($this->logs) return implode("<br>",$this->logs);
		else return 'no logs were stored in Setup class...';
	}
	
	
	public function run_setup(){
		
		if (! $this->check = $this->save_permission_rules_db()){
			$this->add_error("Couldn't edit database <i>.htaccess</i>.<br>The code to inject was:<br><pre>".
								htmlentities($this->get_permission_rules_db()).
								"</pre>");
			return false;
		}
		
		if (! $this->check = $this->save_mod_rewrite_rules()){
			$this->add_error("Couldn't edit main <i>.htaccess</i>.<br>The code to inject was:<br><pre>".
								htmlentities($this->get_rewrite_rules_apache()).
								"</pre>");
			return false;
		}
		
		if (! $this->check = $this->set_CMS_db_name()){
			$this->add_error("Couldn't create <i>encryption_db_name.php</i>.<br>".
								"Check if you have writing permission to <i>/database/</i> cms folder.<br>".
								"Check if the file <i>secret_db_name_template.txt</i> is present in <i>/php/Colibri-Manager/Colibri/</i> directory too.<br>".
								"Verify if the content is corrupted or different from the vanilla one founded at github.");
			return false;
		}
		
		if (! $this->check = $this->set_CMS_key()){
			$this->add_error("Couldn't create <i>encryption_key.php</i>.<br>".
								"Check if you have writing permission to <i>/database/</i> cms folder.<br>".
								"Check if the file <i>secret_key_template.txt</i> is present in <i>/php/Colibri-Manager/Colibri/</i> directory too.<br>".
								"Verify if the content is corrupted or different from the vanilla one founded at github.");
			return false;
		}
	}
	
	
	/**
	* generate mod_rewrite-formatted rules for .htaccess file.
	*
	* the rules are made in order to manage the access of the site via symbolic
	* links (see remapped prefixes and searches by type or date...)
	*
	* @return string
	*/
	private function get_rewrite_rules_apache(){
		$this->add_log("called <code>get_rewrite_rules_apache()</code>");
		global $Config;
		$rules =
		"php_value session.use_strict_mode 1".				"\n".		//disable user to be able to choose session id
		"Options -Indexes".										"\n".		//prevent directory listing
		"<IfModule mod_rewrite.c>".							"\n".		//on any file request which is not a file/dir, redirect to index.php
			"AddType application/x-httpd-php .php".		"\n".
			"RewriteEngine On".									"\n".
			"RewriteBase ".$Config->script_path.			"\n".
			// Prevent -f checks on index.php.
			"RewriteRule ^index\.(php|html)$ - [L]".		"\n".
			//if file or dir doesn't exists...
			"RewriteCond %{REQUEST_FILENAME} !-f".			"\n".
			"RewriteCond %{REQUEST_FILENAME} !-d".			"\n".
			//then index should handle the path.
			"RewriteRule . ".$Config->script_path."{$this->index} [L]". "\n".
		"</IfModule>";
		
		return $rules;
	}

	
	/**
	* generate mod_rewrite-formatted rules for .htaccess file.
	*
	* the rules are made in order to protect the database from being downloaded
	* and to protect the log files during email delivery.
	*
	* @return (string)	htaccess rules
	*/
	private function get_permission_rules_db(){
		$this->add_log("called <code>get_permission_rules_db()</code>");
		$rules = "Order Deny,Allow".					"\n".
					"Deny from all".						"\n".
					'<Files ~ "\.(php|html)$">'.		"\n".
						"Allow from all".					"\n".
					"</Files>";
		
		return $rules;
	}
	
	
	/**
	* Inserts text into a htaccess file, surrounded with comments markers.
	*
	* Replaces existing marked info. Retains surrounding data. Creates file if none exists.
	* inserted data will be surronded by $marker as comment.
	* >>>>>>>>> This script is based on WORDPRESS.
	*
	* @param (string)			$filename	Filename to alter.
	* @param (string)			$marker		The marker to alter.
	* @param (array|string)	$rows			The new content to insert. if string: rows must be divided by "\n"
	*
	* @return (bool)			True on write success, false on failure.
	*/
	private function insert_with_markers( $filename, $marker, $rows ){
		$this->add_log("called <code>insert_with_markers()</code> for file <i>{$filename}</i>");
		//check if filename can be written. create file if not exists.
		if (!file_exists($filename)){
			if (!is_writable(dirname($filename))){
				return false;
			}
			if (!touch($filename)){
				return false;
			}
			file_put_contents($filename,"");
		}
		elseif (!is_writeable($filename)){
			return false;
		}
		
		//create $rows array if needed
		if (!is_array($rows)){
			$rows = explode( "\n", $rows );
		}
		
		//comments markers for start and end inserted code block
		$start_marker = "# BEGIN {$marker}";
		$end_marker   = "# END {$marker}";

		$fp = fopen( $filename, 'r+' );
		if (!$fp ){
			return false;
		}

		// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
		flock( $fp, LOCK_EX );

		//store the current content of the file
		$lines = array();
		while (!feof( $fp )){
			$lines[] = rtrim( fgets( $fp ), "\r\n" );
		}

		// Split out the existing file into the preceeding lines, and those that appear after the marker
		$pre_lines = $post_lines = $existing_lines = array();
		$found_marker = $found_end_marker = false;
		foreach ( $lines as $line ){
			if (!$found_marker && false !== strpos( $line, $start_marker )){
				$found_marker = true;
				continue;
			} elseif (!$found_end_marker && false !== strpos( $line, $end_marker )){
				$found_end_marker = true;
				continue;
			}
			if (!$found_marker ){
				$pre_lines[] = $line;
			} elseif ( $found_marker && $found_end_marker ){
				$post_lines[] = $line;
			} else {
				$existing_lines[] = $line;
			}
		}

		// Check to see if there was a change
		if ( $existing_lines === $rows ){
			flock( $fp, LOCK_UN );
			fclose( $fp );
			return true;
		}

		// Generate the new file data
		$new_file_data = implode( "\n", array_merge(
			$pre_lines,
			array( $start_marker ),
			$rows,
			array( $end_marker ),
			$post_lines
		));

		// Write to the start of the file, and truncate it to that length
		fseek( $fp, 0 );
		$bytes = fwrite( $fp, $new_file_data );
		if ( $bytes ){
			ftruncate( $fp, ftell( $fp ));
		}
		fflush( $fp );
		flock( $fp, LOCK_UN );
		fclose( $fp );

		return (bool) $bytes;
	}

	
	/**
	* Updates the htaccess file with the rewrite rules in installation folder.
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old rules.
	*
	* @see get_rewrite_rules_apache()
	* @see insert_with_markers()
	*
	* @return (true|false)		success on creating the htaccess file
	*/
	public function save_mod_rewrite_rules(){
		$this->add_log("called <code>save_mod_rewrite_rules()</code>");
		$htaccess_file = \CMS_INSTALL_DIR .'/.htaccess';

		//If the file doesn't already exist check for write access to the directory
		//and whether we have some rules. Else check for write access to the file.
		$rules = explode( "\n", $this->get_rewrite_rules_apache());
		
		return $this->insert_with_markers( $htaccess_file, 'Colibri', $rules );
	}
	
	
	/**
	* Updates the htaccess file for database protection.
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old rules.
	*
	* @see get_permission_rules_db()
	* @see insert_with_markers()
	*
	* @return (true|false)		success on creating the htaccess file.
	*/
	public function save_permission_rules_db(){
		$this->add_log("called <code>save_permission_rules_db()</code>");
		global $Config;
		$htaccess_file = $Config->database['dir'] . '.htaccess';

		//If the file doesn't already exist check for write access to the directory
		//and whether we have some rules. Else check for write access to the file.
		$rules = explode( "\n", $this->get_permission_rules_db());
		
		return $this->insert_with_markers( $htaccess_file, 'Colibri', $rules );
	}
	
	
	/**
	* Generate a random key used in encryption
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old code.
	*
	* @see generate_CMS_key()
	*
	* @return (true|false)		success on creating the secret file.
	*/
	public function set_CMS_key(){
		$this->add_log("called <code>set_CMS_key()</code>");
		//check if caller is logged webmaster
		//TODO...
		//template
		global $Config;
		$template_file = __DIR__ . '/secret_key_template.txt';
		$template = @file_get_contents($template_file);
		if (false === $template){
			$this->add_error("Missing secret key template file");
			return false;
		}
		
		//php file that will hold the key
		$secret_file = $Config->database['dir'] . 'encryption_key.php';
		
		//update current db name if already exists the secret file
		if (file_exists($secret_file)){
			//user must be a webmaster to change db name!!!
			if (!$this->is_user_webmaster) return false;
			//update current database name
			include $secret_file; //add CMS_ENCRYPTION_KEY
		}
		
		//generate file
		return $this->generate_CMS_key($secret_file, $template);
	}
	
	
	/**
	* Generate a random key used in encryption
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old code.
	*
	* @see create_new_CMS_key()
	* @see update_old_CMS_key()
	*
	* @return (true|false)		success on creating the secret file.
	*/
	private function generate_CMS_key($file, $template){
		$this->add_log("called <code>generate_CMS_key()</code> for file <i>{$file}</i>");
		
		//check variables
		if (empty($file) || empty($template)) return false;
		
		//check if file can be written. create file if not exists.
		$oldfile = null;
		if (!defined('CMS_ENCRYPTION_KEY')){
			if (false === @file_put_contents($file,"")){
				$this->add_error("Cannot generate secret key php file.");
				return false;
			}
		}
		
		//generate random string
		$RL_factory = new \RandomLib\Factory;
		$RL_generator = $RL_factory->getMediumStrengthGenerator();
		$key = $RL_generator->generateString(64);
		//$this->add_log("generated key: {$key}");
		
		if (!defined('CMS_ENCRYPTION_KEY')){
			return $this->create_new_CMS_key($file, $template, $key);
		}
		else{
			return $this->update_old_CMS_key($file, $template, $key);
		}
	}
	
	
	private function create_new_CMS_key($file, $template, $key){
		$this->add_log("called <code>create_new_CMS_key()</code> for file <i>{$file}</i>");
		//create file / overwrite
		$new_php_code = str_replace('##random_generated_key_here##', $key, $template);
		return !( false === @file_put_contents($file,$new_php_code, LOCK_EX) );
	}
	
	
	private function update_old_CMS_key($file, $template, $key){
		$this->add_log("called <code>update_old_CMS_key()</code> for file <i>{$file}</i>");
		if (!$this->is_user_webmaster ){
			$this->add_error("Only webmaster can change encryption key!");
			return false;
		}
		//update all database before changing file.
		//usin CMS_ENCRYPTION_KEY
		//TODO...
		$this->add_error("<code>update_old_CMS_key()</code> not yet implemented.");
		return true;
	}
	
	
	/**
	* Assign a random name to database
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old code.
	*
	* @see generate_CMS_db_name()
	* @see create_or_rename_db()
	*
	* @return (true|false)		success on creating the secret file.
	*/
	public function set_CMS_db_name(){
		$this->add_log("called <code>set_CMS_db_name()</code>");
		global $Config;
		
		//read template
		$template_file = __DIR__ . '/secret_db_name_template.txt';
		$template = @file_get_contents($template_file);
		if (false === $template){
			$this->add_error("Missing secret database name template file");
			return false;
		}
		
		//this is the php file that will hold the db name
		$secret_file = $Config->database['dir'] . 'encryption_db_name.php';
		
		//update current db name if already exists the secret file
		if (file_exists($secret_file)){
			//user must be a webmaster to change db name!!!
			if (!$this->is_user_webmaster){
				$this->add_error("Only webmaster can change DB name!");
				return false;
			}
			//update current database name
			$this->add_log("loading CMS_DB_NAME");
			include $secret_file; //add CMS_DB_NAME
			$Config->update_db( ['file' => \CMS_DB_NAME] );
			$secret_file = $Config->database['dir'] . 'encryption_db_name.php';
		}
		else{
			//old Colibri version doesn't already have secret_db_name_template.txt...
			//check if database already exists... if exists then only webmaster are allowed to edit.
			if (file_exists($Config->database['src']) && filesize($Config->database['src']) != 0 && !$this->is_user_webmaster){
				clearstatcache();//filesize remains in memory?
				$this->add_error("Only logged webmaster can edit database name.");
				return false;
			}
			clearstatcache();//filesize remains in memory?
		}
		
		//generate/rename file
		$new_db_name = $this->generate_CMS_db_name($secret_file, $template);
		
		//generate/rename database
		if (false !== $new_db_name){
			//update name of existent db, or create new one
			return $this->create_or_rename_db(
				$Config->database['dir'].'mbcsqlite3-default.db',	//default database source for new databases
				$Config->database['src'],									//current database (if exists and not 0-byte)
				$Config->database['dir'].$new_db_name					//new database (or renamed one)
			);
		}
		return false;
	}
	
	
	/**
	* Generate a random database name and create file holding it.
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old code.
	*
	* @see create_new_CMS_key()
	* @see update_old_CMS_key()
	*
	* @return (string|false)		on success return the secred db name. on failure false.
	*/
	private function generate_CMS_db_name($file, $template){
		$this->add_log("called <code>generate_CMS_db()</code> for file <i>{$file}</i>");
		
		//check variables
		if (empty($file) || empty($template)) return false;
		
		//check if file can be written. create file if not exists.
		$old_file = null;
		if (!file_exists($file)){
			if (false === @file_put_contents($file,"")){
				$this->add_error("Cannot generate secret db name php file.");
				return false;
			}
		}
		else{
			//get old file content
			$old_file = @file_get_contents($file);
		}
		
		//generate random string
		$RL_factory = new \RandomLib\Factory;
		$RL_generator = $RL_factory->getMediumStrengthGenerator();
		$key = $RL_generator->generateString(32,'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890$+-_') . ".db";
		//$this->add_log("generated name: {$key}");
		
		return ($this->create_CMS_db_name($file, $template, $key) === false ?
			false :
			$key
		);
	}
	
	
	private function create_CMS_db_name($file, $template, $key){
		$this->add_log("called <code>create_CMS_db_name()</code> for file <i>{$file}</i>");
		//create file / overwrite
		$new_php_code = str_replace('##random_generated_db_name_here##', $key, $template);
		return !( false === @file_put_contents($file,$new_php_code, LOCK_EX) );
	}
	
	
	/**
	* Generate a random database name and create file holding it.
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old code.
	*
	* @param (string) $source_db [optional]		name of default source database to copy
	* @param (string) $old_db_name [optional]		name ofcurrent database to rename
	* @param (string) $new_db_name					name of new/renamed database
	*
	* @return (bool)		success on writing/renaming database
	*/
	private function create_or_rename_db($source_db=null, $old_db_name=null, $new_db_name){
		$this->add_log("called <code>create_or_rename_db()</code> from <i>{$old_db_name}</i> to <i>{$new_db_name}</i>");
		
		//db doesn't exists or is an 0-byte attempt database (created when connecting to a non existent db)
		//------------------------------------
		if (!file_exists($old_db_name) || filesize($old_db_name) == 0){
			$this->add_log("<i>{$old_db_name}</i> doesn't exists.");
			clearstatcache();//filesize remains in memory?
			//check if mbcsqlite-default.db exists.
			if (is_null($source_db) || !is_file($source_db)) return false;
			
			//copy -default.db to new db (or overwrite 0-bytes)
			if ( copy($source_db, $new_db_name) ){
				return true;
			}
			else{
				$this->add_error("Couldn't create new database from <code>{$source_db}</code> to <code>{$new_db_name}</code>. Cannot proceed with installation.");
				return false;
			}
		}
		
		//db already exists: update name
		//------------------------------------
		else{
			$this->add_log("<i>{$old_db_name}</i> already exists.");
			clearstatcache();//filesize remains in memory?
			if ( rename($old_db_name, $new_db_name) ){
				return true;
			}
			else{
				$this->add_error("Couldn't rename database from <code>{$old_db_name}</code> to <code>{$new_db_name}</code>. Cannot proceed with installation.");
				return false;
			}
		}
	}
}
?>