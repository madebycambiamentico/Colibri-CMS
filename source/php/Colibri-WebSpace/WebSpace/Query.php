<?php namespace WebSpace;

class Query{
	//type: 0 = query, 1 = prepare
	
	static function byId($id=0, $fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		$lang_filter = $lang ? " AND lang = '{$lang}'" : '';
		return [
			'query' => "SELECT a.*, {$imageselect} FROM articoli a
		LEFT JOIN immagini b ON a.idimage = b.id
		WHERE a.id = {$id} AND NOT a.isgarbage {$lang_filter}",
			'type' => 0 //query
		];
	}
	
	static function byMap($costraindate=false, $fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.remaplink = ? ".
			($costraindate ? "AND (A.datacreaz LIKE ? OR A.dataedit LIKE ?) " : "").
			($lang ? " AND A.lang = '{$lang}' " : '').
			"AND NOT A.isgarbage
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function byTitle($fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $lang ? " AND A.lang = '{$lang}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.titolo = ? AND NOT A.isgarbage {$lang_filter}
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function byType($type=1, $limit=3, $fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $lang ? " AND A.lang = '{$lang}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.idtype = {$type} AND NOT A.isgarbage {$lang_filter}
		ORDER BY A.dataedit DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function byDate($fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $lang ? " AND A.lang = '{$lang}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE (A.datacreaz LIKE ? OR A.dataedit LIKE ?) AND NOT A.isgarbage {$lang_filter}
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	static function subArt($parentid=1, $type=1, $limit=3, $fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $lang ? " AND A.lang = '{$lang}'" : '';
		return [
			'query' => "SELECT A.remaplink, A.titolo, A.inbreve, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.idtype = {$type} AND A.idarticolo = {$parentid} AND NOT A.isgarbage {$lang_filter}
		ORDER BY A.dataedit DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function getAlbum($albid=0, $fullimage=false){
		$imageselect = $fullimage ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		return [
			'query' => "SELECT {$imageselect} FROM immagini B
		INNER JOIN link_album_immagini C ON C.idalbum = {$albid} AND C.idimage = B.id
		ORDER BY B.data DESC",
			'type' => 0 //query
		];
	}
	
	static function menu($lang=''){
		if (!$lang){
			return [
				'query' => "SELECT * FROM view_menu",
				'type' => 0 //query
			];
		}
		else{
			return [
				'query' => "SELECT a.id as 'parentid', b.id,b.remaplink,b.titolo FROM articoli a
			INNER JOIN articoli b ON b.idarticolo = a.id OR (b.idarticolo IS NULL AND b.id = a.id)
			WHERE a.isinmenu AND NOT a.isgarbage AND NOT b.isgarbage AND a.idarticolo IS NULL
				AND a.lang='{$lang}' AND b.lang='{$lang}'
			ORDER BY parentid DESC, b.idarticolo ASC",
				'type' => 0 //query
			];
		}
	}
	
	static function subMainArts($lang=''){
		if (!$lang){
			return [
				'query' => "SELECT * FROM view_all_main_sub_art",
				'type' => 0 //query
			];
		}
		else{
			return [
				'query' => "SELECT a.id as 'parentid', b.id,b.remaplink,b.titolo FROM articoli a
			INNER JOIN articoli b ON b.idarticolo = a.id
			WHERE a.idtype = 1 AND NOT a.isgarbage AND NOT b.isgarbage AND a.idarticolo IS NULL AND b.idtype = 1
				AND a.lang={$lang} AND b.lang='{$lang}'
			ORDER BY a.id DESC, b.id ASC",
				'type' => 0 //query
			];
		}
	}
	
	static function mainArts($limit=0, $fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		$lang_filter = $lang ? " AND a.lang = '{$lang}'" : '';
		return [
			'query' => "SELECT a.*, {$imageselect} FROM articoli a
		LEFT JOIN immagini b ON a.idimage = b.id
		WHERE a.idtype = 1 AND NOT a.isgarbage AND a.idarticolo IS NULL AND NOT a.isindex {$lang_filter}
		ORDER BY a.id DESC".
		($limit ? " LIMIT {$limit}" : ''),
			'type' => 0 //query
		];
	}
	
	static function index($fullimage=false, $lang=''){
		$imageselect = $fullimage ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		if (!$lang){
			return [
				'query' => "SELECT a.*, {$imageselect} FROM articoli a
			LEFT JOIN immagini b ON a.idimage = b.id
			WHERE a.isindex AND NOT a.isgarbage
			ORDER BY a.dataedit DESC LIMIT 1",
				'type' => 0 //query
			];
		}
		else{
			return [
				'query' => "SELECT a.*, {$imageselect} FROM articoli a
			LEFT JOIN immagini b ON a.idimage = b.id
			WHERE a.isindexlang AND a.lang='{$lang}' AND NOT a.isgarbage
			ORDER BY a.dataedit DESC LIMIT 1",
				'type' => 0 //query
			];
		}
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
			//NB - \PDO has the prefix "\" to access a global defined class
			$pdostat = $pdo->query($query['query'],\PDO::FETCH_ASSOC) or trigger_error("Errore durante {$action} {$subject} [query]", E_USER_ERROR);
			return $pdostat;
		}
	}
}

?>