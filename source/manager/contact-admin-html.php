<?php

function getHtmlEmail(
	$first="email copy from Colibrì System",
	$title="Colibrì System",
	$content="Hello World",
	$link=["http://cambiamentico.altervista.org/colibri","Colibrì Homepage"],
	$sign="Colibrì System",
	$foot="Thank you for using Colibrì CMS"){
	//src="cid:logo"
	ob_start();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge" /><!--<![endif]-->
	<?php echo '<meta name="viewport" content="width=device-width,initial-scale=1'.
	(preg_match("/Android [12]\.[0-9]/",$_SERVER['HTTP_USER_AGENT']) ? ',maximum-scale=1,user-scalable=no' : '').'">' ?>
			<title> </title>
		<style type="text/css">
.wrapper a:hover {
	text-decoration: none !important;
}
.btn a:hover,
.footer__links a:hover {
	opacity: 0.8;
}
.wrapper .footer__share-button:hover {
	color: #ffffff !important;
	opacity: 0.8;
}
a[x-apple-data-detectors] {
	color: inherit !important;
	text-decoration: none !important;
	font-size: inherit !important;
	font-family: inherit !important;
	font-weight: inherit !important;
	line-height: inherit !important;
}
.column {
	font-size: 14px;
	line-height: 21px;
	padding: 0;
	text-align: left;
	vertical-align: top;
}
.mso .font-avenir,
.mso .font-cabin,
.mso .font-open-sans,
.mso .font-ubuntu {
	font-family: sans-serif !important;
}
.mso .font-bitter,
.mso .font-merriweather,
.mso .font-pt-serif {
	font-family: Georgia, serif !important;
}
.mso .font-lato,
.mso .font-roboto {
	font-family: Tahoma, sans-serif !important;
}
.mso .font-pt-sans {
	font-family: "Trebuchet MS", sans-serif !important;
}
.mso .footer p {
	margin: 0;
}
@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2/1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
	.fblike {
		background-image: url(https://i7.createsend1.com/static/eb/master/13-the-blueprint-3/images/fblike@2x.png) !important;
	}
	.tweet {
		background-image: url(https://i8.createsend1.com/static/eb/master/13-the-blueprint-3/images/tweet@2x.png) !important;
	}
	.linkedinshare {
		background-image: url(https://i1.createsend1.com/static/eb/master/13-the-blueprint-3/images/lishare@2x.png) !important;
	}
	.forwardtoafriend {
		background-image: url(https://i9.createsend1.com/static/eb/master/13-the-blueprint-3/images/forward@2x.png) !important;
	}
}
@media only screen and (max-width: 620px) {
	.wrapper h2,
	.wrapper .size-18,
	.wrapper .size-20 {
		font-size: 17px !important;
		line-height: 26px !important;
	}
	.wrapper .size-22 {
		font-size: 18px !important;
		line-height: 26px !important;
	}
	.wrapper .size-24 {
		font-size: 20px !important;
		line-height: 28px !important;
	}
	.wrapper h1,
	.wrapper .size-26 {
		font-size: 22px !important;
		line-height: 31px !important;
	}
	.wrapper .size-28 {
		font-size: 24px !important;
		line-height: 32px !important;
	}
	.wrapper .size-30 {
		font-size: 26px !important;
		line-height: 34px !important;
	}
	.wrapper .size-32 {
		font-size: 28px !important;
		line-height: 36px !important;
	}
	.wrapper .size-34,
	.wrapper .size-36 {
		font-size: 30px !important;
		line-height: 38px !important;
	}
	.wrapper .size-40 {
		font-size: 32px !important;
		line-height: 40px !important;
	}
	.wrapper .size-44 {
		font-size: 34px !important;
		line-height: 43px !important;
	}
	.wrapper .size-48 {
		font-size: 36px !important;
		line-height: 43px !important;
	}
	.wrapper .size-56 {
		font-size: 40px !important;
		line-height: 47px !important;
	}
	.wrapper .size-64 {
		font-size: 44px !important;
		line-height: 50px !important;
	}
	.divider {
		Margin-left: auto !important;
		Margin-right: auto !important;
	}
	.btn a {
		display: block !important;
		font-size: 14px !important;
		line-height: 24px !important;
		padding: 12px 10px !important;
		width: auto !important;
	}
	.btn--shadow a {
		padding: 12px 10px 13px 10px !important;
	}
	.image img {
		height: auto;
		width: 100%;
	}
	.layout,
	.column,
	.preheader__webversion,
	.header td,
	.footer,
	.footer__left,
	.footer__right,
	.footer__inner {
		width: 320px !important;
	}
	.preheader__snippet,
	.layout__edges {
		display: none !important;
	}
	.preheader__webversion {
		text-align: center !important;
	}
	.header__logo {
		Margin-left: 20px;
		Margin-right: 20px;
	}
	.layout--full-width {
		width: 100% !important;
	}
	.layout--full-width tbody,
	.layout--full-width tr {
		display: table;
		Margin-left: auto;
		Margin-right: auto;
		width: 320px;
	}
	.column,
	.layout__gutter,
	.footer__left,
	.footer__right {
		display: block;
		Float: left;
	}
	.footer__inner {
		text-align: center;
	}
	.footer__links {
		Float: none;
		Margin-left: auto;
		Margin-right: auto;
	}
	.footer__right p,
	.footer__share-button {
		display: inline-block;
	}
	.layout__gutter {
		font-size: 20px;
		line-height: 20px;
	}
	.layout--no-gutter.layout--has-border:not(.layout--full-width),
	.layout--has-gutter.layout--has-border .column__background {
		width: 322px !important;
	}
	.layout--has-gutter.layout--has-border {
		left: -1px;
		position: relative;
	}
}
@media only screen and (max-width: 320px) {
	.border {
		display: none;
	}
	.layout--no-gutter.layout--has-border:not(.layout--full-width),
	.layout--has-gutter.layout--has-border .column__background {
		width: 320px !important;
	}
	.layout--has-gutter.layout--has-border {
		left: 0 !important;
	}
}
</style>
<style type="text/css">
body,.wrapper{background-color:#171e24}.wrapper h1{color:#80bfc4}.wrapper h2{color:#80bfc4}.wrapper h3{color:#80bfc4}.wrapper a{color:#c5dee0}.wrapper a:hover{color:#83b8bc !important}.column,.column__background td{color:#e0dce0}.column,.column__background td{font-family:Ubuntu,sans-serif}.mso .column,.mso .column__background td{font-family:sans-serif !important}.border{background-color:#000}.layout--no-gutter.layout--has-border:not(.layout--full-width),.layout--has-gutter.layout--has-border .column__background,.layout--full-width.layout--has-border{border-top:1px solid #000;border-bottom:1px solid #000}.wrapper blockquote{border-left:4px solid #000}.divider{background-color:#000}.wrapper .btn a{color:#171e24}.wrapper .btn a{font-family:Ubuntu,sans-serif}.mso .wrapper .btn a{font-family:sans-serif !important}.wrapper .btn a:hover{color:#171e24 !important}.btn--flat a,.btn--shadow 
a,.btn--depth a{background-color:#80bfc4}.btn--ghost a{border:1px solid #80bfc4}.preheader--inline,.footer__left{color:#fff}.preheader--inline,.footer__left{font-family:Ubuntu,sans-serif}.mso .preheader--inline,.mso .footer__left{font-family:sans-serif !important}.wrapper .preheader--inline a,.wrapper .footer__left a{color:#fff}.wrapper .preheader--inline a:hover,.wrapper .footer__left a:hover{color:#fff !important}.header__logo{color:#41637e}.header__logo{font-family:Avenir,sans-serif}.mso .header__logo{font-family:sans-serif !important}.wrapper .header__logo a{color:#41637e}.wrapper .header__logo a:hover{color:#7096b5 !important}.footer__share-button{background-color:#0c0f12}.footer__share-button{font-family:Ubuntu,sans-serif}.mso .footer__share-button{font-family:sans-serif !important}.layout__separator--inline{font-size:20px;line-height:20px;mso-line-height-rule:exactly}
</style>
</head>
<!--[if mso]>
	<body class="mso">
<![endif]-->
<!--[if !mso]><!-->
	<body class="full-padding" style="margin: 0;-webkit-text-size-adjust: 100%;background-color: #171e24;">
<!--<![endif]-->
		<div class="wrapper" style="background-color: #171e24;">
			<table style="border-collapse: collapse;table-layout: fixed;color: #fff;font-family: Ubuntu,sans-serif;" align="center">
				<tbody><tr>
					<td class="preheader__snippet" style="padding: 10px 0 5px 0;vertical-align: top;" width="300">
						<p style="Margin-top: 0;Margin-bottom: 0;font-size: 12px;line-height: 19px;"><?php echo htmlentities($first) ?></p>
					</td>
					<td class="preheader__webversion" style="text-align: right;padding: 10px 0 5px 0;vertical-align: top;" width="300">
						
					</td>
				</tr>
			</tbody></table>
			<table class="header" style="border-collapse: collapse;table-layout: fixed;Margin-left: auto;Margin-right: auto;" align="center">
				<tbody><tr>
					<td style="padding: 0;" width="600">
						<div class="header__logo emb-logo-margin-box" style="font-size: 26px;line-height: 32px;Margin-top: 6px;Margin-bottom: 20px;color: #41637e;font-family: Avenir,sans-serif;">
							<div class="logo-center" style="font-size:0px !important;line-height:0 !important;" align="center" id="emb-email-header"><img style="border: 0;width: 47px; height:32px" src="../img/logo/colibri-icon-white.png" alt="" width="47" height="32" /></div>
						</div>
					</td>
				</tr>
			</tbody></table>
			<table class="layout layout--no-gutter" style="border-collapse: collapse;table-layout: fixed;Margin-left: auto;Margin-right: auto;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #212a32;" align="center">
				<tbody><tr>
					<td class="column" style="font-size: 14px;line-height: 21px;padding: 0;text-align: left;vertical-align: top;color: #e0dce0;font-family: Ubuntu,sans-serif;" width="600">
		
						<div style="Margin-left: 20px;Margin-right: 20px;">
			<h1 class="size-30" style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;font-size: 30px;line-height: 38px;color: #80bfc4;text-align: center;"><?php echo htmlentities($title) ?></h1><p class="size-18" style="Margin-top: 20px;Margin-bottom: 20px;font-size: 18px;line-height: 26px;text-align: center;"><span style="text-align:center"><?php echo nl2br(htmlentities($content)) ?></span></p>
		</div>
		
						<div style="Margin-left: 20px;Margin-right: 20px;">
			<div style="line-height:12px;font-size:1px">&nbsp;</div>
		</div>
		
						<div style="Margin-left: 20px;Margin-right: 20px;Margin-bottom: 24px;">
			<div class="btn btn--flat" style="text-align:center;">
				<![if !mso]><a style="border-radius: 4px;display: inline-block;font-weight: bold;text-align: center;text-decoration: none !important;transition: opacity 0.1s ease-in;color: #171e24;background-color: #80bfc4;font-family: Ubuntu, sans-serif;font-size: 14px;line-height: 24px;padding: 12px 35px;" href="<?php echo htmlentities($link[0],ENT_QUOTES) ?>" data-width="128"><?php echo htmlentities($link[1]) ?></a><![endif]>
			<!--[if mso]><p style="line-height:0;margin:0;">&nbsp;</p><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="http://test.com" style="width:198px" arcsize="9%" fillcolor="#80BFC4" stroke="f"><v:textbox style="mso-fit-shape-to-text:t" inset="0px,11px,0px,11px"><center style="font-size:14px;line-height:24px;color:#171E24;font-family:sans-serif;font-weight:bold;mso-line-height-rule:exactly;mso-text-raise:4px">Our guide to denim</center></v:textbox></v:roundrect><![endif]--></div>
		</div>
		
					</td>
				</tr>
			</tbody></table>
	
			<div style="font-size: 20px;line-height: 20px;mso-line-height-rule: exactly;">&nbsp;</div>
		
			<table class="footer" style="border-collapse: collapse;table-layout: fixed;Margin-right: auto;Margin-left: auto;border-spacing: 0;" width="600" align="center">
				<tbody><tr>
					<td style="padding: 0 0 40px 0;">
						<table class="footer__right" style="border-collapse: collapse;table-layout: auto;border-spacing: 0;" align="right">
							<tbody><tr>
								<td class="footer__inner" style="padding: 0;">
									
									
									
									
								</td>
							</tr>
						</tbody></table>
						<table class="footer__left" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;color: #fff;font-family: Ubuntu,sans-serif;" width="400">
							<tbody><tr>
								<td class="footer__inner" style="padding: 0;font-size: 12px;line-height: 19px;">
									
									<div>
										<div><?php echo htmlentities($sign) ?></div>
									</div>
									<div class="footer__permission" style="Margin-top: 18px;">
										<div><?php echo htmlentities($foot) ?></div>
									</div>
								</td>
							</tr>
						</tbody></table>
					</td>
				</tr>
			</tbody></table>
		</div>
	
</body></html><?php

	return ob_get_clean();
}


//test:
echo getHtmlEmail();
?>