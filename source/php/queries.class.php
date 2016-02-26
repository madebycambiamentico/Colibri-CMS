<?php

class ARTQUERY{
	//type: 0 = query, 1 = prepare
	
	static function byId($id=0, $fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT articoli.*, {$imageselect} FROM articoli
		LEFT JOIN immagini ON articoli.idimage = immagini.id
		WHERE articoli.id = {$id} AND NOT articoli.isgarbage",
			'type' => 0 //query
		];
	}
	
	static function byMap($costraindate=false, $fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT articoli.*, {$imageselect} FROM articoli
		LEFT JOIN immagini ON articoli.idimage = immagini.id
		WHERE articoli.remaplink = ? ".
			($costraindate ? "AND (articoli.data LIKE ? OR articoli.dataedit LIKE ?) " : "").
			"AND NOT articoli.isgarbage
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function byTitle($fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT articoli.*, {$imageselect} FROM articoli
		LEFT JOIN immagini ON articoli.idimage = immagini.id
		WHERE articoli.titolo = ? AND NOT articoli.isgarbage
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function byType($type=1, $limit=3, $fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT articoli.*, {$imageselect} FROM articoli
		LEFT JOIN immagini ON articoli.idimage = immagini.id
		WHERE articoli.idtype = {$type} AND NOT articoli.isgarbage
		ORDER BY articoli.dataedit DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function byDate($fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT articoli.*, {$imageselect} FROM articoli
		LEFT JOIN immagini ON articoli.idimage = immagini.id
		WHERE (articoli.data LIKE ? OR articoli.dataedit LIKE ?) AND NOT articoli.isgarbage
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function subArt($parentid=1, $type=1, $limit=3, $fullimage=false){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		return [
			'query' => "SELECT A.remaplink, A.titolo, A.inbreve, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.idtype = {$type} AND A.idarticolo = {$parentid} AND NOT A.isgarbage
		ORDER BY A.dataedit DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function getAlbum($albid=0, $fullimage=false){
		$imageselect = $fullimage ?
			"immagini.width, immagini.height, immagini.src, immagini.descr" :
			"immagini.src";
		return [
			'query' => "SELECT {$imageselect} FROM immagini
		INNER JOIN link_album_immagini ON link_album_immagini.idalbum = {$albid} AND link_album_immagini.idimage = immagini.id
		ORDER BY immagini.data DESC",
			'type' => 0 //query
		];
	}
	
	static function menu(){
		return [
			'query' => "SELECT * FROM view_menu",
			'type' => 0 //query
		];
	}
	
	static function subMainArts(){
		return [
			'query' => "SELECT * FROM view_all_main_sub_art",
			'type' => 0 //query
		];
	}
	
	static function mainArts($limit=0, $fullimage=false){
		$imageselect = $fullimage ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		return [
			'query' => "SELECT a.*, {$imageselect} FROM articoli a
		LEFT JOIN immagini b ON a.idimage = b.id
		WHERE a.idtype = 1 AND NOT a.isgarbage AND a.idarticolo IS NULL AND NOT a.isindex
		ORDER BY a.id DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function index($fullimage=false){
		$imageselect = $fullimage ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		return [
			'query' => "SELECT a.*, {$imageselect} FROM articoli a
		LEFT JOIN immagini b ON a.idimage = b.id
		WHERE a.isindex AND NOT a.isgarbage
		ORDER BY a.dataedit DESC LIMIT 1",
			'type' => 0 //query
		];
	}
	
	
	
	/*
	* run the query or prepared statement (if given function exists)
	* @return pdo statement to fetch
	*/
	static function query($func='noop', $funcparams=[], $queryparams=[], $action='ricerca', $subject='articolo'){
		
		//control if function within this class
		if (!is_callable("self::{$func}")) return false;
		//create query
		$query = call_user_func_array("self::{$func}", $funcparams);
		//echo "<pre>".print_r($query,true)."</pre>";
		
		//run the sql request
		global $pdo;
		if ($query['type'] == 1){
			//PREPARED STATEMENT
			$pdostat = $pdo->prepare($query['query']) or trigger_error("Errore durante {$action} {$subject} [prepare]", E_USER_ERROR);
			if (!$pdostat->execute($queryparams)) trigger_error("Errore durante {$action} {$subject} [execute]", E_USER_ERROR);
			return $pdostat;
		}
		else{
			//QUERY STATEMENT
			$pdostat = $pdo->query($query['query'],PDO::FETCH_ASSOC) or trigger_error("Errore durante {$action} {$subject} [query]", E_USER_ERROR);
			return $pdostat;
		}
	}
}

?>