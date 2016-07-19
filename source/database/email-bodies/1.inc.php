<?php

/**
 * @description: generate email for new enabled user. It contain's email and password.
 *
 * @thanks
 * - https://www.sitepoint.com/how-to-code-html-email-newsletters/
 * - https://www.putsmail.com/
 */

function create_email($userinfo, $id, $encrypt=false){
	
	global $CONFIG;
	$cms_dir = $CONFIG['domain'].str_replace('database/','',$CONFIG['mbc_cms_dir']);
	
	ob_start();


//-----------------------------------------------
//		START BUFFERING THE HTML FOR EMAIL
//-----------------------------------------------

?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
</head>

<body>
<style type="text/css">
	body,html{margin:0;padding:0;}
	@media (max-width:720px){
		.colibri_bkg{
			padding-top:0px !important;
			padding-bottom:0px !important;
		}
	}
</style>

<div class="colibri_bkg" style="width:100%;text-align:center;background:#e8e8e8;font-family:'open sans',roboto,helvetica,arial,sans-serif;font-size:14px;line-height:1.4;padding-top:16px;padding-bottom:16px;">
	<table align="center" style="border-collapse:collapse;table-layout:fixed;max-width:720px;margin-right:auto;margin-left:auto;background:#fafafa;border:1px solid #cccccc">
	<tbody>
	
		<tr><td align="center" style="vertical-align:middle;padding:4px;padding-top:40px;">
			<img align="center" src="<?php echo $cms_dir ?>img/users/<?php echo $userinfo['img'] ? $id : 'default'; ?>/face-128.png" style="width:128px;height:128px;border:1px solid #ccc;border-radius:50%;background:#ffffff;">
		</td></tr>
		
		<tr><td align="center" style="vertical-align:middle;padding:4px;padding-bottom:100px;">
			<h3 style="font-size:18px;font-weight:100;">Aggiornamento profilo di</h3>
			<h2 style="font-size:24px;font-weight:bold;"><?php echo htmlentities($userinfo['name']) ?></h2>
			<p style="padding:4px 16px;">
				Lo staff di <a href="<?php echo htmlentities($CONFIG['domain'],ENT_QUOTES) ?>" target="_blank"><?php echo htmlentities($_SERVER['HTTP_HOST']) ?></a> segnala che è stata assegnata al tuo profilo la classe di <b><?php
					switch($userinfo['class']){
						case 0: echo 'ospite'; break;
						case 1: echo 'amministratore'; break;
						case 2: echo 'webmaster'; break;
						default:
					}
				?></b>.
			</p>
			<p>Per ulteriori informazioni e chiarimenti contattare il <a href="<?php echo htmlentities($CONFIG['domain'],ENT_QUOTES) ?>" target="_blank">webmaster</a>.</p>
		</td></tr>
		
		<tr><td style="vertical-align:middle;padding:4px;background:#2d2d2d;color:#ffffff;">
			<p style="padding:4px 16px;text-align:left;">
			This message (including any attachments) may contain confidential, proprietary, privileged and/or private 
			information. The information is intended to be for the use of the individual or entity designated above. If 
			you are not the intended recipient of this message, please notify the sender immediately, and delete the 
			message and any attachments. Any disclosure, reproduction, distribution or other use of this message or 
			any attachments by an individual or entity other than the intended recipient is prohibited. 
			</p>
			<table align="center" style="border-collapse:collapse;table-layout:fixed;">
			<tbody><tr>
				<th style="vertical-align:middle;padding:4px;text-align:right;"><a href="http://colibricms.altervista.org/" target="_blank"><img src="<?php echo $cms_dir ?>img/logo/colibri-icon-white.png" style="width:47px;height:32px;vertical-align: middle;"></a></th>
				<td style="vertical-align:middle;padding:4px;">|</td>
				<td style="vertical-align:middle;padding:4px;text-align:left;">Colibrì CMS System, &copy; 2016</td>
			</tr></tbody>
			</table>
		</td></tr>
		
	</tbody>
	</table>
</div>

</body>

</html><?php

//-----------------------------------------------
//		END BUFFERING THE HTML FOR EMAIL
//-----------------------------------------------


	if ($encrypt){
		global $ENCRYPTER;
		return $ENCRYPTER->encrypt( ob_get_clean() );
	}
	else
		return ob_get_clean();
}





/*/TEST
	require '../../php/encrypter.class.php';
	$ENCRYPTER = new Encrypter('secret key');
	$CONFIG = ['domain' => "http://colibricms.altervista.org", 'mbc_cms_dir' => '/'];
	$encrypted_email = create_email(['name' => "Nèreo C", 'pass' => "trallallero trallallà", 'class' => 0], true);
	echo $ENCRYPTER->decrypt($encrypted_email);
//*/

?>