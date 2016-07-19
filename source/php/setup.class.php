<?php
/**
 * setup rewrite rules
 *
 * @require config.php
 */

class Setup {

	public $index = 'index.php';
	
	
	/**
	 * Retrieves mod_rewrite-formatted rewrite rules to write to .htaccess.
	 *
	 * Does not actually write to the .htaccess file, but creates the rules for
	 * the process that will.
	 *
	 * @return string
	 */
	public function get_rewrite_rules_apache() {
		global $CONFIG;
		$rules = "<IfModule mod_rewrite.c>\n" .
			"AddType application/x-httpd-php .php\n".
			
			"RewriteEngine On\n" .
			"RewriteBase ".$CONFIG['mbc_cms_dir']."\n" .
			
			// Prevent -f checks on index.php.
			"RewriteRule ^index\.(php|html)$ - [L]\n" .
			
			//if file or dir doesn't exists...
			"RewriteCond %{REQUEST_FILENAME} !-f\n" .
			"RewriteCond %{REQUEST_FILENAME} !-d\n" .
			
			//then index should handle the path.
			"RewriteRule . ".$CONFIG['mbc_cms_dir']."{$this->index} [L]\n" .

		"</IfModule>";
		
		return $rules;
	}

	
	/**
	 * Retrieves permission rules to write to .htaccess for database.
	 *
	 * Does not actually write to the .htaccess file, but creates the rules for
	 * the process that will.
	 *
	 * @return string
	 */
	public function get_permission_rules_db() {
		$rules = "Order Deny,Allow\n" .
					"Deny from all\n" .
					'<Files ~ "\.(php|js|css|log)$">'."\n" .
						"Allow from all\n" .
					"</Files>";
		
		return $rules;
	}
	
	
	/**
	 * Inserts an array of strings into a file (.htaccess ), placing it between
	 * BEGIN and END markers.
	 *
	 * Replaces existing marked info. Retains surrounding
	 * data. Creates file if none exists.
	 *
	 * @since 1.5.0
	 *
	 * @param string       $filename  Filename to alter.
	 * @param string       $marker    The marker to alter.
	 * @param array|string $insertion The new content to insert.
	 * @return bool True on write success, false on failure.
	 */
	public function insert_with_markers( $filename, $marker, $insertion ) {
		if ( ! file_exists( $filename ) ) {
			if ( ! is_writable( dirname( $filename ) ) ) {
				return false;
			}
			if ( ! touch( $filename ) ) {
				return false;
			}
			file_put_contents($filename,"");
		} elseif ( ! is_writeable( $filename ) ) {
			return false;
		}

		if ( ! is_array( $insertion ) ) {
			$insertion = explode( "\n", $insertion );
		}

		$start_marker = "# BEGIN {$marker}";
		$end_marker   = "# END {$marker}";

		$fp = fopen( $filename, 'r+' );
		if ( ! $fp ) {
			return false;
		}

		// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
		flock( $fp, LOCK_EX );

		$lines = array();
		while ( ! feof( $fp ) ) {
			$lines[] = rtrim( fgets( $fp ), "\r\n" );
		}

		// Split out the existing file into the preceeding lines, and those that appear after the marker
		$pre_lines = $post_lines = $existing_lines = array();
		$found_marker = $found_end_marker = false;
		foreach ( $lines as $line ) {
			if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
				$found_marker = true;
				continue;
			} elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
				$found_end_marker = true;
				continue;
			}
			if ( ! $found_marker ) {
				$pre_lines[] = $line;
			} elseif ( $found_marker && $found_end_marker ) {
				$post_lines[] = $line;
			} else {
				$existing_lines[] = $line;
			}
		}

		// Check to see if there was a change
		if ( $existing_lines === $insertion ) {
			flock( $fp, LOCK_UN );
			fclose( $fp );

			return true;
		}

		// Generate the new file data
		$new_file_data = implode( "\n", array_merge(
			$pre_lines,
			array( $start_marker ),
			$insertion,
			array( $end_marker ),
			$post_lines
		) );

		// Write to the start of the file, and truncate it to that length
		fseek( $fp, 0 );
		$bytes = fwrite( $fp, $new_file_data );
		if ( $bytes ) {
			ftruncate( $fp, ftell( $fp ) );
		}
		fflush( $fp );
		flock( $fp, LOCK_UN );
		fclose( $fp );

		return (bool) $bytes;
	}

	
	
	/**
	 * Updates the htaccess file with the current rules if it is writable.
	 *
	 * Always writes to the file if it exists and is writable to ensure that we
	 * blank out old rules.
	 */
	public function save_mod_rewrite_rules() {
		global $CONFIG;
		$htaccess_file = $CONFIG['c_dir'].'.htaccess';

		/*
		 * If the file doesn't already exist check for write access to the directory
		 * and whether we have some rules. Else check for write access to the file.
		 */
		$rules = explode( "\n", $this->get_rewrite_rules_apache() );
		return $this->insert_with_markers( $htaccess_file, 'MadeByCambiamentico', $rules );

		return false;
	}
	
	
	/**
	 * Updates the htaccess file for database protection.
	 *
	 * Always writes to the file if it exists and is writable to ensure that we
	 * blank out old rules.
	 */
	public function save_permission_rules_db() {
		global $CONFIG;
		$htaccess_file = $CONFIG['c_dir'].$CONFIG['database']['dir'].'.htaccess';

		/*
		 * If the file doesn't already exist check for write access to the directory
		 * and whether we have some rules. Else check for write access to the file.
		 */
		$rules = explode( "\n", $this->get_permission_rules_db() );
		return $this->insert_with_markers( $htaccess_file, 'MadeByCambiamentico', $rules );
	}
	
	
	public $check = true;
	
	public function __construct($init = false){
		//TODO...
		if ($init){
			$check = true;
			if ($this->check && $this->check != $this->save_permission_rules_db()) $this->check = false;
			if ($this->check && $this->check != $this->save_mod_rewrite_rules()) $this->check = false;
		}
	}

}
?>