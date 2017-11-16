<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('IN_CODE') or die('This script can not be run by itself.');

?>
<h2>No Multiaccounting</h2>
<p>
	The account registered the first time you were here is <b>permanently</b> yours.<br>
	You may <b>not change to another under any circumstances</b>, or else you will get <b>banned</b>.
	<ul>
		<li>If you forgot your password, use the lost password finder <a href="logon.php?forgotPassword=1">here</a>.</li>
		<li>If you forgot your username, use the username recovery <a href="logon.php?resendUsername=1">here</a>.</li>
		<li>If you forgot both, first use the username recovery <a href="logon.php?resendUsername=1">here</a>, and than the lost password finder <a href="logon.php?forgotPassword=1">here</a>.</li>
		<li>If you are still unable to log in, contact the mods here: <a href="mailto:<?php print (isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail)?>"><?php print (isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail)?></a></li>
	</ul>
</p>
<h2>No Metagaming</h2>
<p>
	You can't make alliances <b>for reasons outside a game</b>, such as because you are friends, relatives or in return for a favour in another game.&nbsp; This is known as metagaming and is against the rules because it gives an unfair advantage to those involved.&nbsp; If you are worried that you can't stab someone because you want to stay friends, then that's fair enough but you can't join a game with them.
</p>

<h2>Register and Anti-bot Validation</h2>

<form method="post" action="register.php">

	<ul class="formlist">

		<li class="formlisttitle">Rules check</li>
		<li class="formlistfield"><input type="text" name="rulesValidate" size="50" value="<?php
		        if ( isset($_REQUEST['rulesValidate'] ) )
					print $_REQUEST['rulesValidate'];
		        ?>"></li>
		<li class="formlistdesc">
			Please enter the two things that are really not allowed (see the last two paragraphs) on this server in the text field above to prove you did read and understand these.
		</li>
		
		<li class="formlisttitle">Anti-script code</li>
		<li class="formlistfield">
		        <img alt="EasyCaptcha image" src="<?php print STATICSRV; ?>contrib/easycaptcha.php" /><br />
		        <input type="text" name="imageText" />
		</li>
		<li class="formlistdesc">
			By entering the above code you protect our forum from spam-bots and other scripts
		</li>

		<li class="formlisttitle">E-mail address</li>
		<li class="formlistfield"><input type="text" name="emailValidate" value="<?php
		        if ( isset($_REQUEST['emailValidate'] ) )
					print $_REQUEST['emailValidate'];
		        ?>"></li>
		<li class="formlistdesc">
			By making sure every user has a real e-mail address we stop cheaters from creating many users for themselves.
			This will <strong>not</strong> be spammed or released, it is only used to validate you
		</li>
</ul>

<div class="hr"></div>

<p class="notice">
	<input type="submit" class="form-submit" value="This is my first and only account, please validate me">
</p>
</form>