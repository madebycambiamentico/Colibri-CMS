<?php namespace Colibri;
// todo... uninstallation...
// SELECT sql FROM sqlite_master where name='articoli_types' and type='table'
// -> CREATE TABLE articoli_types (id INTEGER PRIMARY KEY AUTOINCREMENT, protected BOOLEAN DEFAULT (0), nome TEXT COLLATE NOCASE, remapprefix VARCHAR (30) COLLATE NOCASE)



/**
* USAGE EXAMPLE:
*     $myTrns = new Transaction;
*     // add a custom query:
*     $myTrns->add_query( <full query> );
*     // add a new column into some table
*     $myTrns->add_column( <table>, <column>, <column properties> );
*     //run queries in 1 transaction and check errors
*     if ( $myTrns->run_queries() ){
*         //succesfull operaition...
*     else{
*         //some errors occurred
*         echo implode( "; ", $myTrns->errors );
*     }
*
* USAGE EXAMPLE 2:
*     $myTrns = (new Transaction)
*         ->add_query( <full query> )
*         ->add_column( <table>, <column>, <column properties> );
*     //run queries in 1 transaction and check errors
*     if ( $myTrns->run_queries() ){
*         //succesfull operaition...
*     else{
*         //some errors occurred
*         echo implode( "; ", $myTrns->errors );
*     }
*/



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
	
	public $queries = [];	//will store all subsequent queries
	public $errors = [];		//will store errors
	
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
	* Example: in "ALTER TABLE articoli ADD COLUMN comment_allow BOOLEAN DEFAULT (1);" the parameters will be:
	* ('articoli', 'comment_allow', 'BOOLEAN DEFAULT (1)')
	* 
	* @param string $table The database table name
	* @param string $column The column name to be added to the table.
	*                       the $column is "comment_allow"
	* @param string $properties The column propeties (e.g. "BOOLEAN DEFAULT (1)")
	*
	* @return Transaction
	*/
	public function add_column($table, $column, $properties){
		//skip query if column already exists!
		if (! $this->column_exists($table, $column))
			$this->queries[] = "ALTER TABLE {$table} ADD COLUMN {$column} {$properties};";
		return $this;
	}
	
	
	/**
	* Add article type. New article types are not "protected" (from being erased later)
	*
	* @param string $article The article extended name
	* @param string $map The mapping prefix for the article type: avoid spaces.
	*
	* @return Transaction
	*/
	public function add_articletype($article, $map){
		global $pdo;
		try {
			$res = $pdo->query("SELECT COUNT(*) FROM articoli_types WHERE remapprefix = '{$map}'");
			if ($res->fetch()){
				//skip query
				//$this->errors[] = "Article map type '{$map}' already exists.";
			}
			else{
				$this->queries[] = "INSERT INTO articoli_types (nome, remapprefix) VALUES ('{$article}', '{$map}');";
			}
		}
		catch (Exception $e){
			//boh... error...
			$this->errors[] = "Couldn't add article type '{$article}'";
		}
		$pdo->closeCursor(); //necessary?
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
		//reset errors :)
		$this->errors = [];
		
		global $pdo;
		//begin transaction...
		if (! $pdo->beginTransaction()){
			$this->errors[] = "Couldn't begin transaction...";
			return false;///>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  HOW DO I ABORT TRANSACTION?
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
				break;
			}
		}
		//set foreign keys changes to be evaluated again
		if ( false === $pdo->exec("PRAGMA foreign_keys = on;") ){
			$this->errors[] = "Couldn't restart foreign keys.";
			return false;
		}
		//commit transaction
		$pdo->commit();
		
		if (count( $this->errors )){
			return false;
		}
		else{
			//reset queries :)
			$this->queries = [];
			return true;
		}
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