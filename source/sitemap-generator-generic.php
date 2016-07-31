<?php

require_once "config.php";


//include sitemap generator from template, if it has a custom one...
$pdostat = $pdo->query("SELECT template FROM sito ORDER BY id DESC LIMIT 1",PDO::FETCH_ASSOC);
if ($r = $pdostat->fetch()){
	$template = CMS_INSTALL_DIR . "/templates/{$r['template']}/";
	$pdostat->closeCursor();
	if (file_exists($template.'php/sitemap-generator-custom.php')){
		include $template.'php/sitemap-generator-custom.php';
		exit;
	}
	if (file_exists($template.'sitemap-generator-custom.php')){
		include $template.'sitemap-generator-custom.php';
		exit;
	}
}
else
	exit('Database corrotto [cod 001]');



class DOMDocumentValidator {
	private $_delegate;
	private $_validationErrors;
	
	public function __construct (DOMDocument $pDocument) {
		$this->_delegate = $pDocument;
		$this->_validationErrors = array();
	}
	
	public function __call ($pMethodName, $pArgs) {
		if ($pMethodName == "validate") {
			$eh = set_error_handler(array($this, "onValidateError"));
			$rv = $this->_delegate->validate();
			if ($eh) {
				set_error_handler($eh);
			}
			return $rv;
		}
		else {
			return call_user_func_array(array($this->_delegate, $pMethodName), $pArgs);
		}
	}
	public function __get ($pMemberName) {
		if ($pMemberName == "errors") {
			return $this->_validationErrors;
		}
		else {
			return $this->_delegate->$pMemberName;
		}
	}
	public function __set ($pMemberName, $pValue) {
		$this->_delegate->$pMemberName = $pValue;
	}
	public function onValidateError ($pNo, $pString, $pFile = null, $pLine = null, $pContext = null) {
		$this->_validationErrors[] = preg_replace("/^.+: */", "", $pString);
	}
}










/*
google sitemap generator for Colibrì CMS.
*/


$domtree = new DOMDocumentValidator( new DOMDocument('1.0', 'UTF-8') );

/* create the root element of the xml tree */
$xmlRoot = $domtree->appendChild( $domtree->createElement("urlset") );

$attributes = [
	[ $domtree->createAttribute("xmlns"), "http://www.sitemaps.org/schemas/sitemap/0.9" ],
	[ $domtree->createAttribute("xmlns:xhtml"), "http://www.w3.org/1999/xhtml" ],
	[ $domtree->createAttribute("xmlns:image"), "http://www.google.com/schemas/sitemap-image/1.1" ],
	[ $domtree->createAttribute("xmlns:video"), "http://www.google.com/schemas/sitemap-video/1.1" ]
];
foreach ($attributes as $a){
	$a[0]->value = $a[1];
	$xmlRoot->appendChild($a[0]);
}

//foreach articles: add URL, image, video...

$pdostat = $pdo->query("SELECT
	(a.isindex OR a.isindexlang) as 'isindex', a.titolo, a.idarticololang, a.remaplink, a.lang, (a.id=a.idarticololang) as 'canonical',
	b.width, b.height, b.src as 'image'
	FROM articoli a
	LEFT JOIN immagini b ON a.idimage = b.id
	WHERE NOT a.isgarbage
	ORDER BY idtype, idarticololang, canonical DESC",PDO::FETCH_ASSOC) or
		trigger_error("impossibile recuperare articoli [query]", E_USER_ERROR);


$url = null;
$loc = null;
$xhtml = null;
$image = null;
$remapped = null;
$http = $Config->domain . $Config->script_path;

while ($r = $pdostat->fetch()){
	if ($r['canonical']){
		//create canonical element
		$remapped = $r['isindex'] ? '' : htmlentities($r['remaplink'],ENT_QUOTES).'/';
		$url = $xmlRoot->appendChild( $domtree->createElement("url") );
		$url->appendChild( $domtree->createElement("loc",$http.$remapped) );
		//add image, video...
		if ($r['image']){
			$image = $url->appendChild( $domtree->createElement("image:image") );
				$image->appendChild( $domtree->createElement("image:loc",$http.$r['image']) );
				$image->appendChild( $domtree->createElement("image:caption",htmlentities($r['titolo'],ENT_QUOTES)) );
		}
		//add alternate self
		$xhtml = $url->appendChild( $domtree->createElement('xhtml:link') );
		$attributes = [
			[ $domtree->createAttribute("rel"), "alternate" ],
			[ $domtree->createAttribute("hreflang"), $r['lang'] ],
			[ $domtree->createAttribute("href"), $http.$r['lang'].'/'.$remapped ]
		];
		foreach ($attributes as $a){
			$a[0]->value = $a[1];
			$xhtml->appendChild($a[0]);
		}
	}
	elseif ($r['idarticololang'] && $url){
		//add alternate to canonical element previously generated
		$remapped = $r['isindex'] ? '' : htmlentities($r['remaplink'],ENT_QUOTES).'/';
		$xhtml = $url->appendChild( $domtree->createElement('xhtml:link') );
		$attributes = [
			[ $domtree->createAttribute("rel"), "alternate" ],
			[ $domtree->createAttribute("hreflang"), $r['lang'] ],
			[ $domtree->createAttribute("href"), $http.$r['lang'].'/'.$remapped ]
		];
		foreach ($attributes as $a){
			$a[0]->value = $a[1];
			$xhtml->appendChild($a[0]);
		}
	}
	else{
		//create normal element
		$remapped = $r['isindex'] ? '' : htmlentities($r['remaplink'],ENT_QUOTES).'/';
		$url = $xmlRoot->appendChild( $domtree->createElement("url") );
		$url->appendChild( $domtree->createElement("loc",$http.$remapped) );
		//add image, video...
		if ($r['image']){
			$image = $url->appendChild( $domtree->createElement("image:image") );
				$image->appendChild( $domtree->createElement("image:loc",$http.$r['image']) );
				$image->appendChild( $domtree->createElement("image:caption",htmlentities($r['titolo'],ENT_QUOTES)) );
		}
		
		$url = null;
	}
}



/*
example from google:

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
	<url> 
		<loc>http://www.example.com/foo.html</loc>
	
		<image:image>
			<image:loc>http://example.com/image.jpg</image:loc>
			<image:caption>Dogs playing poker</image:caption>
		</image:image>
	
		<video:video>
			<video:content_loc>
				http://www.example.com/video123.flv
			</video:content_loc>
			<video:player_loc allow_embed="yes" autoplay="ap=1">
				http://www.example.com/videoplayer.swf?video=123
			</video:player_loc>
			<video:thumbnail_loc>
				http://www.example.com/thumbs/123.jpg
			</video:thumbnail_loc>
			<video:title>Grilling steaks for summer</video:title>
			<video:description>
				Cook the perfect steak every time.
			</video:description>
		</video:video>
	
		<xhtml:link
			rel="alternate"
			hreflang="de"
			href="http://www.example.com/deutsch/"
			/>
		<xhtml:link
			rel="alternate"
			hreflang="de-ch"
			href="http://www.example.com/schweiz-deutsch/"
			/>
		<xhtml:link
			rel="alternate"
			hreflang="en"
			href="http://www.example.com/english/"
			/>
	</url>
</urlset>
*/



/*
$isValid = $domtree->validate();
if (!$isValid) {
	print_r($domtree->errors);
	exit;
}
*/


header('Content-type: text/plain');
if ($domtree->save(CMS_INSTALL_DIR . '/sitemap.xml'))
	echo "Sitemap succesfully updated!";
else
	echo "An error occurred during sitemap generation..."


?>