<?php namespace WebSpace;

/**
* This class is used to get the result of common queries for template purpose, like getting menu, main articles...
*     //use example:
*     $array = \Colibri\Query::query('<method>', <options>, ...);
*     while ($array->fetch()){ ... }
* where <method> is one of the following:
* - 'byId': search by unique id (1 result),
* - 'byMap': search by unique map (1 result),
* - 'byTitle': search article by unique title (1 result),
* - 'byType': search by article type (LOT OF result),
* - 'byDate': search by article date of creation/edit (multiple result);
* - 'subArts': search articles children by 1 level of depth (multiple results);
* - 'menu': search menu items on variable depth (multiple ORDERED results);
* - 'index': search index article (1 result);
* - 'album': search images for that album (multiple result);
*/
class Query{
	//n.d.r.: type: 0 = query, 1 = prepare
	
	
	/**
	* method "byId": search by id, optional image and filtering by language.
	*
	* @param array $options containing associative variables:
	*    @param int  'id'        => the id of the article
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function byId($options = []){
		//extend option array
		$o = array_merge(['id' => 0, 'fullimage' => false, 'lang' => null], $options);
		//control options...
		if (!$o['id']) return [];
		//create query...
		$imageselect = $o['fullimage'] ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		$lang_filter = $o['lang'] ? " AND lang = '{$o['lang']}'" : '';
		return [
			'query' => "SELECT a.*, {$imageselect} FROM articoli a
		LEFT JOIN immagini b ON a.idimage = b.id
		WHERE a.id = {$o['id']} AND NOT a.isgarbage {$lang_filter}",
			'type' => 0 //query
		];
	}
	
	
	/**
	* method "byMap": search by remap link, optional date, image and filtering by language.
	* this is a prepared statement: needs more params
	*
	* @param array $options containing associative variables:
	*    @param bool 'datecostrain' => (optional) filter by creation/edit date (can be in form "YYYY%", "YYYY-MM%", "YYYY-MM-DD%"). default: true;
	*    @param bool 'fullimage'    => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'         => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function byMap($options = []){
		//extend option array
		$o = array_merge(['datecostrain' => true, 'fullimage' => false, 'lang' => null], $options);
		//control options...
		//*no controls*
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.remaplink = ? ".
			($costraindate ? "AND (A.datacreaz LIKE ? OR A.dataedit LIKE ?) " : "").
			($lang ? " AND A.lang = '{$o['lang']}' " : '').
			"AND NOT A.isgarbage
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	
	/**
	* method "byTitle": search by title, optional image and filtering by language.
	* this is a prepared statement: needs more params.
	* <title> is an unique string.
	*
	* @param array $options containing associative variables:
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function byTitle($options = []){
		//extend option array
		$o = array_merge(['fullimage' => false, 'lang' => null], $options);
		//control options...
		//*no controls*
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $o['lang'] ? " AND A.lang = '{$o['lang']}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.titolo = ? AND NOT A.isgarbage {$lang_filter}
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	
	/**
	* method "byType": search by article type, optional results limit, image and filtering by language.
	*
	* @param array $options containing associative variables:
	*    @param int  'type'      => (optional) the article type id. default: 1 (main);
	*    @param int  'limit'     => (optional) limit of the query. if 0 no limit are applied. default: 0;
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function byType($options = []){
		//extend option array
		$o = array_merge(['type' => 1, 'limit' => 0, 'fullimage' => false, 'lang' => null], $options);
		//control options...
		if (!$o['type']) return [];
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $o['lang'] ? " AND A.lang = '{$o['lang']}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.idtype = {$o['type']} AND NOT A.isgarbage {$lang_filter}
		ORDER BY A.dataedit DESC".
		($o['limit'] ? " LIMIT {$o['limit']}" : ''),
			'type' => 0 //query
		];
	}
	
	
	/**
	* method "byDate": search by remap link, optional date, image and filtering by language.
	* this is a prepared statement: needs more params.
	* <date> can be in form "YYYY%", "YYYY-MM%", "YYYY-MM-DD%" for ranging all year, a month, or a specific day.
	*
	* @param array $options containing associative variables:
	*    @param bool 'fullimage'    => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'         => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function byDate($options = []){
		//extend option array
		$o = array_merge(['fullimage' => false, 'lang' => null], $options);
		//control options...
		//*no controls*
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$lang_filter = $o['lang'] ? " AND A.lang = '{$o['lang']}'" : '';
		return [
			'query' => "SELECT A.*, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE (A.datacreaz LIKE ? OR A.dataedit LIKE ?) AND NOT A.isgarbage {$lang_filter}
		LIMIT 1",
			'type' => 1 //prepared
		];
	}
	
	
	/**
	* method "arts": search articles, filtered by optional article type, results limit, image and filtering by language.
	*
	* @param array $options containing associative variables:
	*    @param int  'parentid'  => (optional) the parent article id.
	*    @param int  'type'      => (optional) the child article type id. if 0 no type filter si applied. default: 1 (main);
	*    @param int  'level'     => (optional) depth level of the articles. if 0 no depth filter is applied. default: 2;
	*    @param int  'limit'     => (optional) limit of the query. if 0 no limit are applied. default: 0;
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function arts($options = []){
		//extend option array
		$o = array_merge(['parentid' => 0, 'skipids' => [], 'type' => 1, 'level' => 2, 'limit' => 0, 'fullimage' => false, 'lang' => null], $options);
		//control options...
		//*no controls*
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$skip_ancestors = empty($o['skipids']) ? '' : (
			count($o['skipids'])===1 ?
				"AND id != {$o['skipids'][0]}" :
				"AND id NOT IN (".implode(',',$o['skipids']).")"
		);
		$ancestor_filter = "tree.ancestor " .
			($o['parentid'] ? "= {$o['parentid']} AND tree.descendant != {$o['parentid']}" :
			"IN (
				SELECT id FROM articoli WHERE
				idarticolo IS NULL
				AND NOT isgarbage
				".($o['type'] ? "AND idtype = {$o['type']}" : '')."
				".($o['lang'] ? "AND lang = '{$o['lang']}'" : '')."
				{$skip_ancestors}
			)");
		$type_filter = $o['type'] ? "AND art.idtype = {$o['type']}" : '';
		$lang_filter = $o['lang'] ? "AND art.lang = '{$o['lang']}'" : '';
		$depth_filter = $o['level'] ? "AND tree.depth < {$o['level']}" : '';
		return [
			'query' => "
SELECT
	art.*,
	{$imageselect},
	tree.depth,
	GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
LEFT JOIN immagini AS B ON art.idimage = B.id
WHERE
	{$ancestor_filter}
	AND NOT art.isgarbage
	{$type_filter}
	{$lang_filter}
	{$depth_filter}
GROUP BY art.id
ORDER BY breadcrumbs",
			'type' => 0 //query
		];
	}
	
	
	/**
	* method "subArts": search children (1 level) of an article, filtered by optional article type, results limit, image and filtering by language.
	*
	* @param array $options containing associative variables:
	*    @param int  'parentid'  => the parent article id.
	*    @param int  'type'      => (optional) the child article type id. if 0 no type filter si applied. default: 1 (main);
	*    @param int  'limit'     => (optional) limit of the query. if 0 no limit are applied. default: 0;
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function subArts($options = []){
		//extend option array
		$o = array_merge(['parentid' => 0, 'type' => 1, 'limit' => 0, 'fullimage' => false, 'lang' => null], $options);
		//control options...
		if (!$o['parentid']) return [];
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		$type_filter = $o['type'] ? " AND A.idtype = '{$o['type']}'" : '';
		$lang_filter = $o['lang'] ? " AND A.lang = '{$o['lang']}'" : '';
		return [
			'query' => "SELECT A.remaplink, A.titolo, A.inbreve, {$imageselect} FROM articoli A
		LEFT JOIN immagini B ON A.idimage = B.id
		WHERE A.idarticolo = {$o['parentid']} AND NOT A.isgarbage {$type_filter} {$lang_filter}
		ORDER BY A.dataedit DESC".
		($o['limit'] ? " LIMIT {$o['limit']}" : ''),
			'type' => 0 //query
		];
	}
	
	
	/**
	* method "menu": get menu.
	*
	* @param array $options containing associative variables:
	*    @param int 'level'      => (optional) depth level of the menu. if 0 no depth filter is applied. default: 0;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function menu($options = []){
		//extend option array
		$o = array_merge(['level' => 0, 'lang' => null], $options);
		//control options...
		if (!$o['level'] < 0) return [];
		//create query...
		if (!$o['lang']){
			switch ($o['level']){
				case 0:
					return [
						'query' => "SELECT * FROM view_menu",
						'type' => 0 //query
					];
				break;
				default:
					if (in_array($o['level'],[1,2,3]))
						return [
							'query' => "SELECT * FROM view_menu_{$o['level']}_levels",
							'type' => 0 //query
						];
					else
						return [
							'query' => "
SELECT
	art.*,
	tree.depth,
	GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
	SELECT id FROM articoli WHERE
	idarticolo IS NULL
	AND isinmenu
	AND NOT isgarbage
)
	AND art.isinmenu
	AND NOT art.isgarbage
	AND tree.depth < {$o['level']}
GROUP BY art.id
ORDER BY breadcrumbs",
							'type' => 0 //query
						];
				break;
			}
		}
		else{
			$depth_filter = $o['level'] ? "AND tree.depth < {$o['level']}" : '';
			return [
				'query' => "
SELECT
	art.*,
	tree.depth,
	GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM articoli AS art
JOIN articoli_treepath AS tree ON art.id = tree.descendant
JOIN articoli_treepath AS crumbs ON crumbs.descendant = tree.descendant
WHERE tree.ancestor IN (
	SELECT id FROM articoli WHERE
	lang = '{$o['lang']}'
	AND idarticolo IS NULL
	AND isinmenu
	AND NOT isgarbage
)
	AND lang = '{$o['lang']}'
	AND art.isinmenu
	AND NOT art.isgarbage
	{$depth_filter}
GROUP BY art.id
ORDER BY breadcrumbs",
				'type' => 0 //query
			];
		}
	}
	
	
	/**
	* method "index": search website index, image and filtering by language.
	*
	* @param array $options containing associative variables:
	*    @param bool 'fullimage' => (optional) whether to get the article main image properties (H, W, descr). default: false;
	*    @param bool 'lang'      => (optional) filter by language. default: null;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function index($options = []){
		//extend option array
		$o = array_merge(['fullimage' => false, 'lang' => null], $options);
		//control options...
		//*no controls*
		//create query...
		$imageselect = $o['fullimage'] ?
			"b.width, b.height, b.src, b.descr" :
			"b.src";
		if (!$o['lang']){
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
			WHERE (a.isindex OR a.isindexlang) AND a.lang='{$o['lang']}' AND NOT a.isgarbage
			ORDER BY a.dataedit DESC LIMIT 1",
				'type' => 0 //query
			];
		}
	}
	
	
	/**
	* method "album": get album image from id.
	*
	* @param array $options containing associative variables:
	*    @param int  'id'        => the id of the album
	*    @param bool 'fullimage' => (optional) whether to get the full image properties (H, W, descr). default: true;
	*
	* @return array containing results to fetch on an associative array.
	*/
	static function album($options = []){
		//extend option array
		$o = array_merge(['id' => 0, 'fullimage' => true], $options);
		//control options...
		if (!$o['parentid']) return [];
		//create query...
		$imageselect = $o['fullimage'] ?
			"B.width, B.height, B.src, B.descr" :
			"B.src";
		return [
			'query' => "SELECT {$imageselect} FROM immagini B
		INNER JOIN link_album_immagini C ON C.idalbum = {$o['id']} AND C.idimage = B.id
		ORDER BY B.data DESC",
			'type' => 0 //query
		];
	}
	
	
	/*
	* run the query or prepared statement (if given function exists)
	*
	* @param string $func        the <method> name to be called
	* @param array  $funcparams  the associative array that will be used as parameter in the <method> chosen
	* @param array  $queryparams is a plain array containing the parameters to be used in prepared statement (if needed)
	* @param string $action      on error will be the failed action name (searching, finding...)
	* @param string $subject     on error will be the failed action subject name (articles, menues...)
	*
	* @return pdo statement to be fetched
	*/
	static function query($func='noop', $funcparams=[], $queryparams=[], $action='ricerca', $subject='articolo'){
		
		//control if function within this class
		if (!is_callable("self::{$func}")) return false;
		//create query
		$query = call_user_func("self::{$func}", $funcparams);
		//echo "<pre style='line-height:1.1;text-align:left'>".print_r($query,true)."</pre>";
		
		//run the sql request
		//NB - \PDO has the prefix "\" to access a global defined class, since this class is called from a function scope.
		global $pdo;
		if ($query['type'] == 1){
			//------------------
			//PREPARED STATEMENT
			$pdostat = $pdo->prepare($query['query'],\PDO::FETCH_ASSOC)
				or trigger_error("Errore durante {$action} {$subject} [prepare]", E_USER_ERROR);
			if (!$pdostat->execute($queryparams))
				trigger_error("Errore durante {$action} {$subject} [execute]", E_USER_ERROR);
			return $pdostat;
		}
		else{
			//---------------
			//QUERY STATEMENT
			$pdostat = $pdo->query($query['query'],\PDO::FETCH_ASSOC)
				or trigger_error("Errore durante {$action} {$subject} [query]", E_USER_ERROR);
			return $pdostat;
		}
	}
}

?>