<?php namespace Colibri;
// todo... uninstallation...
// SELECT sql FROM sqlite_master where name='articoli_types' and type='table'
// -> CREATE TABLE articoli_types (id INTEGER PRIMARY KEY AUTOINCREMENT, protected BOOLEAN DEFAULT (0), nome TEXT COLLATE NOCASE, remapprefix VARCHAR (30) COLLATE NOCASE)



/**
* Run a set of queries in unique transaction.
* If something goes wrong the database will be restored as before the attempted queries.
* With this function you can easily install template/plugin dependencies or update the database.
*
* Methods:
* - add_query(...) : store an SQL query
* - add_column(...) : same as add_query(), but shorthand for adding columns
* - run_queries() : execute all the stored queries in one transaction.
*/
class Transaction{
	
	public $queries = [];
	public $errors = [];
	
	/**
	* store a query to be executed when run_queries() is called
	*
	* @param string $str The SQL query
	*
	* @return Transaction
	*/
	public function add_query($str){
		$this->queries[] = $str;
		return $this;
	}
	
	
	/**
	* check if column exists
	*
	* @param string $table The database table name
	* @param string $column The database table column name
	*
	* @return bool
	*/
	public function column_exists($table, $column){
		global $pdo;
		try {
			$pdo->query("SELECT {$column} FROM {$table} LIMIT 1");
		}
		catch (Exception $e){
			return false;
		}
		$pdo->closeCursor(); //necessary?
		return true;
	}
	
	
	/**
	* Store a query to be executed when run_queries() is called, this method is a shorthand for adding a column.
	* PLUS - it checks if the column is already present: in this case the query is skipped.
	* 
	* @param string $table The database table name
	* @param string $column The SQL query part from the column name to all its properties.
	*                       Example: in "ALTER TABLE articoli ADD COLUMN comment_allow BOOLEAN DEFAULT (1);"
	*                       the $column is "comment_allow BOOLEAN DEFAULT (1)"
	*
	* @return Transaction
	*/
	public function add_column($table, $column){
		//skip query if column already exists!
		if (! $this->column_exists($table, $column))
			$this->queries[] = "ALTER TABLE {$table} ADD COLUMN {$column};";
		return $this;
	}
	
	
	/**
	* run all the stored queries in unique transaction.
	*
	* @return bool
	*/
	public function run_queries(){
		if (empty($this->queries)){
			$this->errors[] = "No queries to be run.";
			return false;
		}
		
		global $pdo;
		//begin transaction...
		if (! $pdo->beginTransaction()){
			$this->errors[] = "Couldn't begin transaction...";
			return false;
		}
		//prevent foreign keys changes to be evaluated
		if ( false === $pdo->exec("PRAGMA foreign_keys = off;") ){
			$this->errors[] = "Couldn't shut down foreign keys.";
			return false;
		}
		//run (virtually) every query
		foreach ($this->queries as $k => $query){
			if ( false === $pdo->exec($query) ){
				$this->errors[] = "Couldn't execute the query n.{$k}: \"{$query}\"";
				return false;
			}
		}
		//set foreign keys changes to be evaluated again
		if ( false === $pdo->exec("PRAGMA foreign_keys = on;") ){
			$this->errors[] = "Couldn't restart foreign keys.";
			return false;
		}
		//commit transaction
		$pdo->commit();
		//reset :)
		$this->queries = [];
		
		return true;
	}
}






/**
* Hold multiple transactions (if you need it...)
*/
class InstallHelper{
	public $transactions = [];
	public $errors = [];
	
	function __construct(){
		//todo...
	}
	
	
	/**
	* create and store a transaction class
	*
	* @param string|int $id (optional) unique identifier for this transaction
	*
	* @see Transaction()
	* @return Transaction
	*/
	public function create_transaction($id = null){
		global $pdo;
		if (is_null($id)) $id = count($this->transactions);
		$this->transactions[$id] = new Transaction;
		return $this->transactions[$id];
	}
	
	
	/**
	* run all initialized transactions
	*
	* @return bool Whether all transaction have been succesfully completed.
	*/
	public function run_transactions(){
		$this->errors = [];
		foreach( $this->transactions as $id => $tr ){
			if (!$tr->run_queries()){
				$this->errors[] = "Transaction '{$id}' couldn't be completed:<br>- ".implode("<br>- ",$tr->errors);
			}
		}
	}
}

?>