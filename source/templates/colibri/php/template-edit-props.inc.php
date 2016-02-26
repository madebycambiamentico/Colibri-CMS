<?php

if (!isset($response)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

class COLIBRI_TEMPLATE_EDIT{

	public $ini = null;
	public $file = null;

	public function __construct($file){
		require_once "ini.class.php";
		$this->ini = INI_FILE::iread($file, true);
		if (!empty($this->ini)) $this->file = $file;
	}
	
	public function update($updates){
		$uini = updateini($updates);
		$ucss = updatecss();
		return ['ini' => $uini, 'css' => $ucss];
	}

	public function updateini($updates){
		if (empty($updates) || empty($this->ini)) return false;
		//map
		if (isset($updates['map'])){
			$this->ini['custom']['map_height'] = $updates['map']['height'];
			$this->ini['custom']['maps_1366'] = $updates['map'][1366] ? 1 : 0;
			$this->ini['custom']['maps_800'] = $updates['map'][800] ? 1 : 0;
			$this->ini['custom']['maps_520'] = $updates['map'][520] ? 1 : 0;
			$this->ini['custom']['map_ext'] = $updates['map']['ext'];
		}
		//marker
		if (isset($updates['marker'])){
			if ($updates['marker']['x'] == $this->ini['custom']['mark_x'] && $updates['marker']['y'] == $this->ini['custom']['mark_y']){
				if (!isset($updates['map'])) return false;
			}
			$this->ini['custom']['mark_x'] = $updates['marker']['x'];
			$this->ini['custom']['mark_y'] = $updates['marker']['y'];
		}
		//write!
		return INI_FILE::iwrite($this->ini, $this->file, true);
	}



	public function updatecss(){
		if (empty($this->ini)) return false;
		//call after updateini
		//return false;
?>
*{margin:0;padding:0;text-decoration:none}
body{font:16px/1.4 'open sans',helvetica,arial,sans-serif;padding:0 0 <?php echo $this->ini['custom']['map_height'] ?>px;background:#0e0e0e url(img/map-1366.<?php echo $this->ini['custom']['map_ext'] ?>) no-repeat center bottom;position:relative}
h1{font-size:40px;font-weight:100;text-transform:capitalize;padding:40px 0 30px;color:#222}
h2{font-size:32px;font-weight:100;padding:32px 0 22px;color:#222}
h3{font-size:22px;font-weight:500;padding:22px 0 12px;color:#222}
p{padding:10px 0}
#main-article ul,.article .desc ul,#main-article ol,.article .desc ol{padding-left:25px}
#wrapper{background:#eee;padding:0 0 100px}
#menu{display:table;border-collapse:collapse;width:100%;background:#eee;border-bottom:5px solid #c00;box-shadow:0 0 30px rgba(0,0,0,0.6);position:relative;z-index:10}
#logo,#menus{display:table-cell;vertical-align:middle}
#logo{width:180px}
#logo .logo{display:block;position:absolute;top:0;left:0;z-index:10;width:140px;height:140px;background:#c00 url(img/logo.png) no-repeat center;margin:20px}
#menus{list-style:none;text-align:right;padding:0 10px;position:relative;line-height:0}
#menus li,#menus li>a{display:inline-block;*display:inline;zoom:1}
#menus>li{padding:12px 0}
#menus li>a{position:relative;color:#444;font-size:13px;font-weight:300;padding:5px 10px;text-transform:uppercase;border-left:1px solid #ccc;line-height:15px}
#menus>li>a:hover{color:#c00}
#menus>li>a::before{content:" ";position:absolute;bottom:0;left:50%;margin:0 0 -12px -5px;width:0;height:0;border-style:solid;border-width:0 10px 10px;border-color:transparent transparent #c00;visibility:hidden}
#menus>li.single>a::before{display:none}
#menus>li:hover>a::before{visibility:visible}
#menus li ul{display:block;position:absolute;z-index:20;right:0;width:100%;margin-top:12px;padding:15px 0;background:#c00;visibility:hidden}
#menus li ul li>a{color:#fff;border:none}
#menus li ul li>a:hover{color:#FFD37B}
#menus li:hover ul{visibility:visible}
#image-spacer{height:100px}
.image-main{position:relative;z-index:0;background:#fff no-repeat center;background-size:cover;max-height:420px;overflow:hidden}
.image-web{width:100%;padding-bottom:35%;background:url(img/rete.png)}
#articles{color:#1d1d1d}
#articles .inside{width:90%;max-width:1200px;margin:0 auto}
#main-article a{color:#c00}
#main-article a:hover{color:#900}
.imgfix img,.imgfix figure{height:auto!important;max-width:100%}
.article-cont{line-height:0;padding-top:100px;text-align:center}
.article-cont .article{display:inline-block;*display:inline;zoom:1;line-height:1.4;width:320px;margin:10px;vertical-align:top}
.article .image{overflow:hidden}
.article .desc{text-align:left}
.article .desc h2{font-size:28px}
.article .desc ::-moz-selection{background:#c00;color:#fff}
.article .desc ::selection{background:#c00;color:#fff}
.article .image,.article .image>a,.article .image>p{width:320px;height:320px;background:#666 no-repeat center}
.article .image>a,.article .image>p{display:block;-webkit-transform:scale(1);transform:scale(1);-webkit-transition:transform .4s;transition:transform .4s}
.article:hover .image>a,.article:hover .image>p{-webkit-transform:scale(1.15);transform:scale(1.15)}
.article .sub-art-goto{margin:10px 0 0;text-align:center;background:#c00}
.article .sub-art-goto a{display:inline-block;*display:inline;zoom:1;color:#fff;background:url(img/link.png) no-repeat 7px center;padding:10px 20px 10px 42px;font-size:15px;text-transform:uppercase}
.article .sub-art-goto:hover{background-color:#a00}
.article .sub-art-goto:active{background-color:#800}
.article .subs-art-goto{text-align:center;background:#333}
.article .subs-art-goto a{display:inline-block;*display:inline;zoom:1;color:#fff;padding:8px 20px;font-size:13px}
.article .subs-art-goto:hover{background-color:#222}
.article .subs-art-goto:active{background-color:#444}
#news{background:#1d1d1d;padding-bottom:100px}
#news .article .desc h3{color:#FFD18C;text-transform:uppercase}
#news .article{background:#333;color:#eee}
#news .article .desc{padding:20px;font-size:smaller}
#news .desc ::-moz-selection{background:#FFD18C;color:#000}
#news .desc ::selection{background:#FFD18C;color:#000}
#news .article .image,#news .article .image>a{height:200px;background-color:#111}
#quotator{padding:200px 0;background:#ddd;color:#000;text-align:center}
#quotator .logo{background:#333 url(img/logo.png) no-repeat center;width:152px;height:152px;margin:20px auto;-webkit-border-radius:76px;border-radius:50%;box-shadow:0 0 10px #888}
#quotator p{padding:0;margin:10px}
#quotator q{word-spacing:2px;font-size:20px;font-style:italic}
#quotator .desc{font-size:13px}
#contactform{position:relative;background:#FF9115;padding:100px 16px 40px;color:#fff}
#contactform h2{max-width:1024px;margin:0 auto;color:#fff}
#contactform h3{color:#fff}
#fluidform{display:table;border-collapse:collapse;width:100%;max-width:1024px;margin:0 auto;-webkit-box-sizing:border-box;box-sizing:border-box}
#fluidform form,#fluidinfo{display:table-cell;vertical-align:top}
#fluidform form{width:50%}
#contactform ul{list-style:none;padding-right:40px}
#contactform li{padding-bottom:20px}
#contactform label{display:none}
#contactform input,select,textarea{font-family:inherit}
#contactform input[type=text],#contactform select,#contactform textarea{display:inline-block;*display:inline;zoom:1;width:100%;padding:10px;font-size:16px;border:none;border-bottom:1px solid #aaa;background-color:#FFD379;box-shadow:2px 2px 3px rgba(0,0,0,0.3) inset;-webkit-box-sizing:border-box;box-sizing:border-box}
#contactform textarea{height:180px;resize:vertical}
#contactform input[type=text]:hover,#contactform select:hover,#contactform textarea:hover{background:#FFE3AB}
#contactform input[type=text]:focus,#contactform select:focus,#contactform textarea:focus{border-bottom-color:#c00}
#fluidinfo{text-align:center;text-shadow:1px 1px 1px #874700}
#info{display:inline-block;*display:inline;zoom:1;text-align:left;font-weight:700;letter-spacing:1px;font:14px/1.2 'open sans',impact,helvetica,arial,sans-serif}
#contactform .btn{position:relative;display:block;width:100%;-webkit-box-sizing:border-box;box-sizing:border-box;margin:10px auto;cursor:pointer;text-decoration:none;text-align:center;font-size:18px;text-transform:uppercase;padding:12px 20px;color:#333;text-shadow:0 1px 1px #fff;background-color:#f0f0f0;background-image:linear-gradient(to bottom,#FFF,#E6E6E6);border:1px solid #B3B3B3;box-shadow:0 1px 0 rgba(255,255,255,0.2) inset,0 1px 2px rgba(0,0,0,0.05);-webkit-border-radius:4px;border-radius:4px;-webkit-user-select:none;-moz-user-select:none;user-select:none}
#contactform .btn.red{color:#fff;text-shadow:0 1px 1px #900;background:#c00;border:none;-webkit-border-radius:0;border-radius:0}
#contactform .btn:hover{background-color:#f9f9f9;background-image:linear-gradient(to bottom,#FFF,#f0f0f0);border-color:#93A9CF}
#contactform .btn:active{top:1px;background-color:#ddd;background-image:linear-gradient(to bottom,#D9E0E4,#eee);border-color:#919FB6}
#contactform .btn.red:hover{background:#a00}
#contactform .btn.red:active{background:#800}
#contactform .confirm{font-size:smaller}
.g-recaptcha div{margin:0 auto;max-width:100%!important}
.g-recaptcha iframe{width:100%!important;-webkit-box-sizing:border-box;box-sizing:border-box}
#seemap{text-align:center;font-size:24px;color:#222;padding:70px 10px;background:url(img/mapmarker-64.png) no-repeat center bottom}
#mapmark{position:absolute;width:64px;height:64px;background:url(img/mapmarker.png) no-repeat center;bottom:<?php echo $this->ini['custom']['mark_y'] ?>px;left:50%;margin-left:<?php echo $this->ini['custom']['mark_x'] ?>px;z-index:-1}
#powered{background:#1D1D1D;color:#aaa;font-size:10px;text-align:center;padding:16px}
#powered .logo,#powered .mbc,#powered .actions a{display:inline-block;*display:inline;zoom:1;vertical-align:middle}
#powered .logo{width:50px;height:50px;background:#eee url(img/colibri.png) no-repeat center;-webkit-border-radius:25px;border-radius:50%;margin:5px}
#powered .mbc{text-align:left}
#powered .mbc b{font-weight:700;color:#fff}
#powered .mbc a{color:#666}
#powered .actions{padding:10px 0 0}
#powered .actions a{color:#fff;background:#111;padding:5px 10px;margin:0 5px}
#powered .actions a:hover{background:#265BB7}
#powered .actions a:active{background:#000}
@media only screen and (max-width:800px) {
<?php if ($this->ini['custom']['maps_800']) echo 'body{background-image:url(img/map-800.'.$this->ini['custom']['map_ext'].')}'; ?>
#menus li{display:block;position:relative}
#menus>li{padding:5px 32px 5px 0}
#menus li>a{border:none}
#menus>li>i{display:block;position:absolute;width:20px;height:20px;top:50%;right:0;margin-top:-10px;background:url(img/more.png) repeat 20px center;cursor:pointer}
#menus>li>i:hover,#menus>li>i:active,#menus>li.open>i{background:url(img/more.png) repeat 0 center}
#menus>li>a::before{margin:0 0 -5px -5px}
#menus li ul{width:90%;margin:5px 5% 0;padding:18px 0}
#menus li.open ul,#menus>li.open>a::before{visibility:visible}
#menus li ul li{line-height:2}
#sub-articles .article{width:290px;margin:30px 8px 50px}
#sub-articles .image,#sub-articles .image>a{width:290px;height:290px}
#fluidform,#fluidform form,#fluidinfo{display:block;width:auto;max-width:none;padding:0}
#contactform h2{text-align:center}
#fluidform form{max-width:400px;margin:0 auto}
#fluidform form ul{padding:0}
#fluidinfo{background:none}
}
@media only screen and (max-width:520px) {
<?php if ($this->ini['custom']['maps_520']) echo 'body{background-image:url(img/map-800.'.$this->ini['custom']['map_ext'].')}'; ?>
#wrapper{padding-bottom:30px}
.image-web{padding-bottom:50%}
#menu,#logo,#menus{display:block;width:auto}
#menus>li{padding-right:0}
#logo{padding:20px 0}
#logo .logo{position:static;margin:0 auto}
#menus{text-align:center}
#articles #sub-articles{width:auto}
.article-cont{padding-top:50px}
.article-cont .article{width:290px;max-width:100%;margin:30px 0 50px}
.article-cont .image,.article-cont .image>a{width:290px;height:290px}
#news{padding-bottom:50px}
#news .article .image,#news .article .image>a{height:190px}
#quotator{padding:100px 0}
}
<?php
		return false;
	}

}

?>