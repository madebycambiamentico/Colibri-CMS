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
*/

class Setup {

	public $index = 'index.php';
	public $check = true;
	
	
	public function __construct($init = false){
		//TODO...
		if ($init){
			if ($this->check && $this->check != $this->save_permission_rules_db())
				$this->check = false;
			if ($this->check && $this->check != $this->save_mod_rewrite_rules())
				$this->check = false;
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
	public function get_rewrite_rules_apache(){
		global $CONFIG;
		$rules =
		"<IfModule mod_rewrite.c>".							"\n".
			"AddType application/x-httpd-php .php".		"\n".
			"RewriteEngine On".									"\n".
			"RewriteBase ".$CONFIG['mbc_cms_dir'].			"\n".
			// Prevent -f checks on index.php.
			"RewriteRule ^index\.(php|html)$ - [L]".		"\n".
			//if file or dir doesn't exists...
			"RewriteCond %{REQUEST_FILENAME} !-f".			"\n".
			"RewriteCond %{REQUEST_FILENAME} !-d".			"\n".
			//then index should handle the path.
			"RewriteRule . ".$CONFIG['mbc_cms_dir']."{$this->index} [L]". "\n".
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
	public function get_permission_rules_db(){
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
	* This script is based on wordpress.
	*
	* @param (string)			$filename	Filename to alter.
	* @param (string)			$marker		The marker to alter.
	* @param (array|string)	$rows			The new content to insert. if string: rows must be divided by "\n"
	*
	* @return (bool)			True on write success, false on failure.
	*/
	public function insert_with_markers( $filename, $marker, $rows ){
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
	*/
	public function save_mod_rewrite_rules(){
		global $CONFIG;
		$htaccess_file = $CONFIG['c_dir'].'.htaccess';

		//If the file doesn't already exist check for write access to the directory
		//and whether we have some rules. Else check for write access to the file.
		$rules = explode( "\n", $this->get_rewrite_rules_apache());
		return $this->insert_with_markers( $htaccess_file, 'ColibrìCMS', $rules );

		return false;
	}
	
	
	/**
	* Updates the htaccess file for database protection.
	*
	* Always writes to the file if it exists and is writable to ensure that we
	* blank out old rules.
	*/
	public function save_permission_rules_db(){
		global $CONFIG;
		$htaccess_file = $CONFIG['c_dir'].$CONFIG['database']['dir'].'.htaccess';

		//If the file doesn't already exist check for write access to the directory
		//and whether we have some rules. Else check for write access to the file.
		$rules = explode( "\n", $this->get_permission_rules_db());
		return $this->insert_with_markers( $htaccess_file, 'ColibrìCMS', $rules );
	}

}
?>