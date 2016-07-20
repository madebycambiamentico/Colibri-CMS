<?php

/*
 * @template Colibrì 2016 v.1.0
 * contact form -- this is not a required file for standard templates.
 * @author Nereo Costacurta
 *
 * @require: /index.php (this is not a standalone page!)
 *
 * @license GPLv3
 * @copyright: (C)2016 nereo costacurta
**/

if (!isset($web)){
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	die;
}

?>
<div id="contactform">
	<h2>Contacts</h2>
	<div id="fluidform">
		<form data-action="<?php echo LINK::file('contact-admin.php') ?>" action="<?php echo $templatepath.'nojs-contact.php' ?>" method="POST">
			<ul>
				<li>
					<label for="cf-subject">Subject: </label><input id="cf-subject" type="text" name="subject" placeholder="Subject">
				</li>
				<li>
					<label for="cf-phone">Phone number (+39): </label><input id="cf-phone" type="text" name="phone" placeholder="Phone number (+39)">
				</li>
				<li>
					<label for="cf-email">Email: </label><input id="cf-email" type="text" name="email" placeholder="Email">
				</li>
				<li>
					<label for="cf-message">Message</label><textarea id="cf-message" type="text" name="message" placeholder="Message"></textarea>
				</li>
				<li>
					<?php
						$reCaptcha = new \ReCaptcha\ReCaptcha($web['recaptcha_key']);
						$reCaptcha->get_browser_widget($mylang ? $mylang : 'en');
					?>
				</li>
				<li class="confirm">A confirmation email will be sent to your email (if provided). We read carefully every message we receive.<br><label><input type="checkbox" name="sendconfirm" value="1" checked> inviami conferma email</label></li>
				<li>
					<input type="submit" class="btn red">
				</li>
			</ul>
		</form>
		<div id="fluidinfo">
			<div id="info">
				<h3>Info</h3>
				<?php echo nl2br(htmlentities($web['info'])); ?>
			</div>
		</div>
	</div>
	<div id="seemap">Map View</div>
</div>