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
/**
 * @package Base
 * @subpackage Forms
 */
?>
<div class="content-bare content-board-header content-title-header">
<div class="pageTitle barAlt1">
	Create a new game
</div>
<div class="pageDescription barAlt2">
Start a new game; you decide the name, how long it runs, and how much it's worth.
</div>
</div>
<div class="content content-follow-on">

<form method="post">
<ul class="formlist">

	<li class="formlisttitle">
		Name:
	</li>
	<li class="formlistfield">
		<input type="text" name="newGame[name]" value="" size="30" onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13">
	</li>
	<li class="formlistdesc">
		The name of your game
	</li>
	
	<li class="formlisttitle">
		Phase length: (5 minutes - 10 days)
	</li>
	<li class="formlistfield">
		<select id="phaseMinutes" name="newGame[phaseMinutes]" onChange="
			document.getElementById('wait').selectedIndex = this.selectedIndex; 
			if (this.selectedIndex < 5) {
				$('fixStart').value= 'Yes';
				$('fixStart').disabled= true;
			} else { 
				$('fixStart').value= 'No';
				$('fixStart').disabled= false;
			}
			if (this.selectedIndex == 29) $('phaseHoursText').show(); else $('phaseHoursText').hide();">
		<?php
			$phaseList = array(5,7, 10, 15, 20, 30,
				60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320,
				1440, 2160, 2880, 4320, 5760, 7200, 8640, 10080, 14400, 1440+60, 2880+60*2);

			foreach ($phaseList as $i) {
				$opt = libTime::timeLengthText($i*60);

				print '<option value="'.$i.'"'.($i==1440 ? ' selected' : '').'>'.$opt.'</option>';
			}
		?>
		<option value="0">Custom</option>
		</select>
		<span id="phaseHoursText" style="display:none">
			 - Phase length: <input type="text" id="phaseHours" name="newGame[phaseHours]" value="24" size="4" style="text-align:right;"
			 onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
			 onChange="
			  this.value = parseInt(this.value);
			  if (this.value == 'NaN' ) this.value = 24;
			  if (this.value < 1 ) this.value = 1;
			  if (this.value > 200 ) this.value = 200;
			  document.getElementById('phaseMinutes').selectedIndex = 29;
			  document.getElementById('phaseMinutes').options[29].value = this.value * 60;
			  document.getElementById('wait').selectedIndex = 17;" > hours.
		</span>

	</li>
	<li class="formlistdesc">
		The maximum number of hours allowed for players to discuss and enter orders each phase.<br />
		Longer phase hours means more time to make careful decisions and negotiations, but makes a game take longer. Shorter
		phase hours results in a faster game, but requires that players in the game are available to check the game frequently.<br /><br />
		<strong>Default:</strong> 24 hours/1 day
	</li>

	<li class="formlisttitle">
		Bet size: (2<?php print libHTML::points(); ?> -
			<?php print $User->points.libHTML::points(); ?>)
	</li>
	<li class="formlistfield">
	<div id="betinput">
		<input id="bet" type="text" name="newGame[bet]" size="7" value="<?php print $formPoints ?>" 
			onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
			onChange="
				this.value = parseInt(this.value);
				if (this.value == 'NaN' ) this.value = <?php print $defaultPoints; ?>;
				if (this.value < 2) this.value = 2;
				if (this.value > <?php print $User->points; ?>) this.value = <?php print $User->points; ?>;"
			/> / 
			<input type="button" value="Play unrated game."
				onclick="$('bet').value = '0';
						$('betinput').hide(); $('potType').hide(); $('bet_unrated').show();">
	</div>
	<div id="bet_unrated" style="<?php print libHTML::$hideStyle; ?>" >This is an unrated game.</div>
	</li>
	<li class="formlistdesc">
		The bet required to join this game. This is the amount of points that all players, including you,
		must put into the game's "pot" (<a href="points.php" class="light">read more</a>).<br />
		<?php
			if (isset(Config::$limitBet))
			{
				print 'There are some restrictions how many '.libHTML::points().' are allowed based on how many players are in your game.<br />';
				$first=true;
				foreach (Config::$limitBet as $limit=>$bet)
				{
					if ($first)
					{
						print '('.$limit.'-player variants allow a maximum betsize of '.$bet.libHTML::points().',';
						$first = false;
					}
					else
						print $limit.'-players: '.$bet.libHTML::points().', ';
				}
				print 'variants with more players have no such limit.)';
				print '<br />';
			}
		?>
		<br />

		<strong>Default:</strong> <?php print $defaultPoints.libHTML::points(); ?>
	</li>

<ul class="formlist">
<?php
if( count(Config::$variants)==1 )
{
	foreach(Config::$variants as $variantID=>$variantName) ;

	$defaultVariantName=$variantName;

	print '<input type="hidden" name="newGame[variantID]" value="'.$variantID.'" />';
}
else
{
?>
	<li class="formlisttitle">Variant map/rules:</li>
	<li class="formlistfield">
	
	<script type="text/javascript">
	function setExtOptions(i){
		document.getElementById('countryID').options.length=0;
		switch(i)
		{
			<?php
			$checkboxes=array();
			$first='';
			foreach(Config::$variants as $variantID=>$variantName)
			{
				if (isset(Config::$blockedVariants) && in_array($variantID,Config::$blockedVariants))
					continue;
					
				$Variant = libVariant::loadFromVariantName($variantName);
				$checkboxes[$Variant->fullName] = '<option value="'.$variantID.'"'.(($first=='')?' selected':'').'>'.$Variant->fullName.'</option>';
				if($first=='') {
					$first='"'.$variantID.'"';
					$defaultName=$Variant->fullName;
				}
				print "case \"".$variantID."\":\n";
				print 'document.getElementById(\'desc\').innerHTML = "<a class=\'light\' href=\'variants.php?variantID='.$variantID.'\'>'.$Variant->fullName.'</a><hr style=\'color: #aaa\'>'.$Variant->description.'";'."\n";		
				print "document.getElementById('countryID').options[0]=new Option ('Random','0');";
				for ($i=1; $i<=count($Variant->countries); $i++)
					print "document.getElementById('countryID').options[".$i."]=new Option ('".$Variant->countries[($i -1)]."', '".$i."');";
				print "break;\n";		
			}	
			ksort($checkboxes);	
			?>	
		}
	}
	</script>
	
	<table><tr>
		<td	align="left" width="0%">
			<select name="newGame[variantID]" onChange="setExtOptions(this.value)">
			<?php print implode($checkboxes); ?>
			</select> </td>
		<td align="left" width="100%">
			<div id="desc" style="border-left: 1px solid #aaa; padding: 5px;"></div></td>
	</tr></table>
	</li>
	<li class="formlistdesc">
		Select which type of Diplomacy game you would like to play from a selection of maps and alternate rule settings
		available on this server.<br /><br />

		Click any of the variant names to view the details on the variants page.<br /><br />
		
		<strong>*Please note that 1 vs 1 games will default to a 5 point bet as an unranked game no matter what bet/game type are selected</strong>
		<br /><br />

		<strong>Default:</strong> <?php print $defaultName;?>
	</li>
<?php
}
?>
	<li class="formlisttitle">Country assignment:</li>
	<li class="formlistfield">
		<select id="countryID" name="newGame[countryID]">
		</select>
	</li>

	<li class="formlistdesc">
		Random distribution of each country, or players pick their country (gamecreator gets the selected country).<br /><br />
		<strong>Default:</strong> Random
	</li>
	
	<script type="text/javascript">
	setExtOptions(<?php print $first;?>);
	</script>
	
</ul>

<li class="formlisttitle">
	<img src="images/icons/lock.png" alt="Private" /> Add Invite Code (optional):
</li>
<li class="formlistfield">
	<ul>
		<li>Invite Code: <input type="password" name="newGame[password]" value="" size="30" /></li>
		<li>Confirm: <input type="password" name="newGame[passwordcheck]" value="" size="30" /></li>
	</ul>
</li>
<li class="formlistdesc">
	<strong>This is optional.</strong> If you set this only people who know the password will be able to join.<br /><br />

	<strong>Default:</strong> No password set
</li>

<div class="hr"></div>

<div id="AdvancedSettingsButton">
<ul class="formlist">
	<li class="formlisttitle">
		<a href="#" onclick="$('AdvancedSettings').show(); $('AdvancedSettingsButton').hide(); return false;">
		Open Advanced Settings
		</a>
	</li>
	<li class="formlistdesc">
		Advanced settings allowing extra customization of games for seasoned players, allowing
		different map choices, alternate rules, and non-standard timing options.<br /><br />

		The default settings are fine for <strong>new players</strong>.
	</li>
</ul>
</div>

<div id="AdvancedSettings" style="<?php print libHTML::$hideStyle; ?>">

<h3>Advanced settings</h3>

<ul class="formlist">
	
	<div id="potType">
		<li class="formlisttitle">Pot type:</li>
		<li class="formlistfield">
			<input type="radio" name="newGame[potType]" value="Winner-takes-all" checked > Winner-takes-all (WTA)
			<input type="radio" name="newGame[potType]" value="Points-per-supply-center"> Points-per-supply-center (PPSC)<br />
		</li>
		<li class="formlistdesc">
			Should the winnings be split up according to who has the most supply centers, or should the winner
			get everything (<a href="points.php#ppscwta" class="light">read more</a>).<br /><br />

			<strong>Default:</strong> Winner-takes-all (WTA)
		</li>
	</div>
	
	<li class="formlisttitle">
		Anonymous players:
	</li>
	<li class="formlistfield">
		<input type="radio" name="newGame[anon]" value="No" checked>No
		<input type="radio" name="newGame[anon]" value="Yes">Yes
	</li>
	<li class="formlistdesc">
		If enabled players will not see each others names and user information, players are anonymous until the game ends.<br /><br />

		<strong>Default:</strong> No, players aren't anonymous
	</li>
	
	<li class="formlisttitle">
		Disable in-game messaging:
	</li>
	<li class="formlistfield">
		<input type="radio" name="newGame[pressType]" value="Regular" checked>Allow all<br />
		<input type="radio" name="newGame[pressType]" value="PublicPressOnly">Global messages only, no private chat/press (Public Press)<br />
		<input type="radio" name="newGame[pressType]" value="NoPress">No in-game messaging (Gunboat)<br />
		<input type="radio" name="newGame[pressType]" value="RulebookPress">Per rulebook<br />
	</li>
	<li class="formlistdesc">
		Disable some types of messaging; allow only global in-game messages, or allow no in-game messages.
		(<a href="press.php" class="light">read more</a>)
		<br/><br/> "Per rulebook" means no discussion during builds and retreats as per the original Diplomacy rulebook. In this mode, saved retreats and builds are automatically readied for the next turn.
		<br /><br /><strong>Default:</strong> Allow all
	</li>
	<li class="formlisttitle">
		Draw votes:
	</li>
	<li class="formlistfield">
		<input type="radio" name="newGame[drawType]" value="draw-votes-public" checked>Public draw votes
		<input type="radio" name="newGame[drawType]" value="draw-votes-hidden">Hidden draw votes
	</li>
	<li class="formlistdesc">
		Whether or not draw votes can be seen by the other players. In both modes, the game will be drawn when all players have voted draw. However, if draw votes are 
		hidden then you are the only one who knows whether you have voted to draw or not. 
		<br /><br /><strong>Default:</strong>Public draw votes
	</li>
	
	<li class="formlisttitle">
		Do not process the game on:
	</li>
	<li class="formlistfield">
		<input type="checkbox" name="newGame[noProcess][]" value="1">Mon
		<input type="checkbox" name="newGame[noProcess][]" value="2">Tue
		<input type="checkbox" name="newGame[noProcess][]" value="3">Wed
		<input type="checkbox" name="newGame[noProcess][]" value="4">Thu
		<input type="checkbox" name="newGame[noProcess][]" value="5">Fri
		<input type="checkbox" name="newGame[noProcess][]" value="6">Sat
		<input type="checkbox" name="newGame[noProcess][]" value="0">Sun
	</li>
	<li class="formlistdesc">
		If you do not want this game to process on specific days of the week, then check the appropriate day or days to restrict processing. <br />
		If a current phase falls on any of the selected days it will be extended by 24 hours until a day that is available for processing. However if all players 'ready' their orders the game will process as usual regardless of whether or not the extended 24 hours has been reached. <br />
		Days are processed according to standard CET time.
	
		<br /><br /><strong>Default:</strong> Nothing selected. Process on all days of the week.
	</li>

	<li class="formlisttitle">
		Joining pre-game period length: (5 minutes - 10 days)
	</li>
	<li class="formlistfield">
		<select id="wait" name="newGame[joinPeriod]">
		<?php
			foreach ($phaseList as $i) {
				$opt = libTime::timeLengthText($i*60);
				print '<option value="'.$i.'"'.($i==1440 ? ' selected' : '').'>'.$opt.'</option>';
			}
		?>
		</select>
		- 
		<select id="fixStart" name="newGame[fixStart]">
			<option value="No" selected>Start as soon as enough players have joined.</option>';
			<option value="Yes">Wait for the given starting time and day.</option>';
		</select>
	</li>
	<li class="formlistdesc">
		The amount of time to wait for people to join. For 5 minute games you may want to give players longer than 5 minutes to join.

		<br /><br /><strong>Default:</strong> The same as phase length
	</li>
	
	<li class="formlisttitle">
		Rating requirements:
	</li>
	<script type="text/javascript">
		function changeReliabilitySelect(i){
			if (i > 0) {
				document.getElementById('minPhases').options[0].value = '20';
				document.getElementById('minPhases').options[0].text  = '20+';
				document.getElementById('ReliabilityInput').value = i;
			} else if ( i == '') {
				document.getElementById('minPhases').options[0].value = '20';
				document.getElementById('minPhases').options[0].text  = '20+';
				$('ReliabilityText').show();
				$('ReliabilitySelect').hide();			
			}
			else {
				document.getElementById('minPhases').options[0].value = '0';
				document.getElementById('minPhases').options[0].text  = 'none';
				document.getElementById('ReliabilityInput').value = i;
			}
		}
	</script>
	<li class="formlistfield">
		ReliabilityRating: R
		<span id="ReliabilityText" >
			<input id="ReliabilityInput" type="text" name="newGame[minimumReliabilityRating]" size="2" value="0"
				style="text-align:right;"
				onChange="
					this.value = parseInt(this.value);
					if (this.value == 'NaN' ) this.value = 0;
					if (this.value < 0 ) this.value = 0;
					if (this.value > 100 ) this.value = 100;
					changeReliabilitySelect(this.value)" 
				onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13;">
			or better.
		</span>
		<br>
		Min Phases: <select id="minPhases" name="newGame[minPhases]">
			<option value=0 selected>none</option>
			<option value=50>50+</option>
			<option value=100>100+</option>
			<option value=300>300+</option>
			<option value=600>600+</option>
			</select>
	</li>
	<li class="formlistdesc">
		You can set some requirements that the players for your game need to fulfill.		
		<ul>
			<li><b>Min Rating:</b> The minimum reliability a player must have to join your game.</li>
			<li><b>Min Phases:</b> How many phases a player must have played to join your game.</li>
		</ul>
		This might lead to not enough people able to join your games, so choose your options wisely.<br /><br />
		<strong>Default:</strong> No restrictions:
	</li>

	<li class="formlisttitle">
		NMR policy:
	</li>
	<li class="formlistfield">
		<?php 
			$specialCDturnsTxt = ( Config::$specialCDturnsDefault == 0 ? 'off' : (Config::$specialCDturnsDefault > 99 ? '&infin;' : Config::$specialCDturnsDefault) );
			$specialCDcountTxt = ( Config::$specialCDcountDefault == 0 ? 'off' : (Config::$specialCDcountDefault > 99 ? '&infin;' : Config::$specialCDcountDefault) );
		?>
		
		<input type="hidden" id="specialCDturn"  name="newGame[specialCDturn]"  value="<?php print $specialCDturnsTxt;?>">
		<input type="hidden" id="specialCDcount" name="newGame[specialCDcount]" value="<?php print $specialCDcountTxt;?>">
		
		<select id="NMRpolicy" name="newGame[NMRpolicy]" 
			onChange="
				if (this.value == 'c/c') {
					$('NMRpolicyCustom').show();
					$('NMRpolicyText').hide();
				} else {
					opt = this.value.split('/');
					document.getElementById('specialCDturn').value  = opt[0];
					document.getElementById('specialCDcount').value = opt[1];
					if (opt[0] == 0) opt[0] = 'off'; if (opt[0] > 90) opt[0] = '&infin;'; 
					if (opt[1] == 0) opt[1] = 'off'; if (opt[1] > 90) opt[1] = '&infin;'; 
					document.getElementById('specialCDturnCustom').value  = opt[0];
					document.getElementById('specialCDcountCustom').value = opt[1];
					document.getElementById('NMRpolicyText').innerHTML = ' - Turns: <b>' + opt[0] + '</b> - Delay: <b>' + opt[1] + '</b>';
					$('NMRpolicyCustom').hide();
					$('NMRpolicyText').show();
				}
			">
			<option value="0/0">Off</option>
			<option value="<?php print $specialCDturnsTxt;?>/<?php print $specialCDcountTxt;?>" selected>Default</option>
			<option value="5/2">Committed</option>
			<option value="99/99">Serious</option>
			<option value="c/c">Custom</option>
		</select>
		
		
		<span id="NMRpolicyCustom" style="display:none">
			 - Turns: </b><input 
							type="text" 
							id="specialCDturnCustom" 
							size="2" 
							value='<?php print $specialCDturnsTxt; ?>'
							onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
							onChange="document.getElementById('NMRpolicy').selectedIndex = 4;
								if (this.value == 'off') this.value = 0;
								this.value = parseInt(this.value);
								document.getElementById('specialCDturn').value  = this.value;
								if (this.value > 90) this.value = '&infin;';
								if (this.value == 0) this.value = 'off';"
							>
			 - Delay: </b><input
							type="text"
							id="specialCDcountCustom"
							value = '<?php print $specialCDcountTxt; ?>'
							onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
							onChange="document.getElementById('NMRpolicy').selectedIndex = 4;
								if (this.value == 'off') this.value = 0;
								this.value = parseInt(this.value);
								document.getElementById('specialCDcount').value = this.value;
								if (this.value > 90) this.value = '&infin;';
								if (this.value == 0) this.value = 'off';"
							size="2"
							> 
		</span>
		<span id="NMRpolicyText">
			 - Turns: <b><?php print $specialCDturnsTxt;?></b> - Delay: <b><?php print $specialCDcountTxt;?></b>
		</span>
	</li>
	<li class="formlistdesc">
		This rule will send a players into Civil Disorder (CD) if there are No Moves Received (NMR) from them.
		<ul>
		<li><strong>Turns:</strong> How many turns this action will be in effect for. Be carefull, a turn has up to three phases.
		Example: A two will send the country in CD for the diplomacy, retreat and build phase of the first 2 turns (usually Spring and Autumn).</li>
		<li><strong>Delay:</strong> How much time to advertise and find a replacement player (the current phase will be extended by the current phase length that many times).
		A zero will send the country in CD, but proceed with the turn as usual. Countries with 1 or less SCs will not hold back the game from processing.</li>
		</ul>
		Any value greater 90 will set the value to &infin;, a value of 0 will set this to off.
		<br /><br /><strong>Default:</strong> <?php print $specialCDturnsTxt;?> / <?php print $specialCDcountTxt;?>
	</li>

	<li class="formlisttitle">
		Alternate winning conditions:
	</li>
	<li class="formlistfield"> 
		<b>Target SCs: </b><input type="text" name="newGame[targetSCs]" size="4" value="0"
			onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
			onChange="
				this.value = parseInt(this.value);
				if (this.value == 'NaN' ) this.value = 0;"
		/> (0 = default)<br>
		<b>Max. turns: </b><input type="text" name="newGame[maxTurns]" size="4" value="0"
			onkeypress="if (event.keyCode==13) this.blur(); return event.keyCode!=13"
			onChange="
				this.value = parseInt(this.value);
				if (this.value == 'NaN' ) this.value = 0;
				if (this.value < 4 && this.value != 0) this.value = 4;
				if (this.value > 200) this.value = 200;"
		/> (4 < maxTurns < 200)
	</li>
	<li class="formlistdesc">
		This setting lets you limit how many turns are played and/or how many SCs need to be conquered before a winner is declared.
		Please check the variant-description for infomation about the average turns or the default SCs for a win.<br />
		The winning player is decided by who has the most SCs after that turn's diplomacy phase.
		If 2 or more player have the same SCs at the end of the game, the game checks for the turn before, and so on.
		If player's SC counts are the same throughout the whole game the winner is decided at random.
		<br />A value of "0" (the default) ends the game as usual, as soon as one player reach the default target SCs.
		<br /><br /><strong>Default:</strong> 0 (no fixed game duration / default number of SCs needed)
	</li>

<!-- 
	<li class="formlisttitle">
		Chess Timer:
	</li>
	<li class="formlistfield">
		<b>Hours: </b><input type="text" name="newGame[chessTime]" value="0" size="8">
	</li>
	<li class="formlistdesc">
		If you want a chesstimer you can enter the time each player has on it\'s clock here.
	</li>
-->	
	<li class="formlisttitle">
		Moderated game:
	</li>
	<li class="formlistfield">
		<input type="radio" name="newGame[moderated]" 
			onclick="$('GDoptions').hide();	$('PWReq').hide(); $('PWOpt').show();"
			value="No" checked>No
		<input type="radio" name="newGame[moderated]" value="Yes" 
			onclick="$('GDoptions').show(); $('PWReq').show(); $('PWOpt').hide();"
			<?php if (!$User->DirectorLicense()) print "disabled"; ?> >Yes
	</li>
	<li class="formlistdesc">
		If set to yes you are given extra moderator-powers to manage this game.<br /><br />
		You can force extends, pauses and have many other options running the game.<br />
		If you select Yes, you are not automatically playing in this game, you are the moderator.
		You need to join this game once it's created if you want to play a country.<br />
		If you want to enable the players to choose their countries select any country in the "Country assignment" list. You will still need to join this game once it's created.<br /><br />
		You need to have at least <b>25</b> non-live games with more than 2 players completed and a reliability-rating of <b>R97</b> or better to moderate a game.
		<br /><br />
		<strong>Default:</strong> No, there is no moderator for this game.
	</li>

	<span id="GDoptions" style="<?php print libHTML::$hideStyle; ?>">
		<li class="formlisttitle">
			Game description (required for moderated games):
		</li>
		<li class="formlistfield">
			<TEXTAREA name="newGame[description]" ROWS="4"></TEXTAREA>
		<li class="formlistdesc">
			Please enter a brief description about your game and custom rules here.<br />
		</li>
	</span>
 <!-- 
	<li class="formlisttitle">
		No moves received options:
	</li>
	<li class="formlistfield">
		<input type="radio" name="newGame[missingPlayerPolicy]" value="Normal" checked > Normal<br />
		<input type="radio" name="newGame[missingPlayerPolicy]" value="Wait"> Wait for all players
	</li>
	<li class="formlistdesc">
		What should happen if the end of the turn comes and a player has not submitted any orders?<br /><br />
		
		If set to <strong>Normal</strong> the game will proceed, and after 
		a couple of turns they will go into civil disorder and their country can be taken over by another player.<br /><br />
		
		If set to <strong>Wait for all players</strong> the game will not continue until all players have submitted their orders.<br />
		This avoids any issues caused by 
		someone not submitting their orders on time, but it means that if someone becomes unavailable the game will not continue until they either
		return, or a moderator manually sets them to civil disorder.<br /><br />

		<strong>Default:</strong> Normal
	</li>
-->	
</ul>

</div>

<div class="hr"></div>

<p class="notice">
	<input type="submit" onClick="$('fixStart').disabled = false;" class="form-submit" value="Create">
</p>
</form>
