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

/**
 * @package Base
 */

require_once('header.php');

require_once(l_r('gamesearch/search.php'));
require_once(l_r('pager/pagergame.php'));
require_once(l_r('objects/game.php'));
require_once(l_r('gamepanel/game.php'));
require_once(l_r('lib/reliability.php'));		 

if ( isset($_REQUEST['userID']) && intval($_REQUEST['userID'])>0 )
{
	$userID = (int)$_REQUEST['userID'];
}
elseif( isset($_REQUEST['searchUser']) )
{
	libAuth::resourceLimiter('user search',5);

	if( !is_array($_REQUEST['searchUser']) )
		throw new Exception(l_t("Invalid search data submitted."));

	$searchUser = $_REQUEST['searchUser'];

	$searchUserValid=array();
	if ( isset($searchUser['id']) && $searchUser['id'] && strlen($searchUser['id']) )
		$searchUserValid['id'] = (int)$searchUser['id'];

	if ( isset($searchUser['username']) && $searchUser['username'] && strlen($searchUser['username']) )
		$searchUserValid['username'] = $DB->escape($searchUser['username']);

	if ( isset($searchUser['email']) && $searchUser['email'] && strlen($searchUser['email']) )
		$searchUserValid['email'] = $DB->escape($searchUser['email']);

	unset($searchUser);

	$whereSQL=array();
	foreach($searchUserValid as $searchFieldName=>$searchFieldValue)
	{
		if( $searchFieldName == 'id' )
			$whereSQL[] = $searchFieldName." = ".$searchFieldValue;
		else
			$whereSQL[] = $searchFieldName." LIKE '".$searchFieldValue."'";
	}

	$userID=false;

	if( count($whereSQL) )
	{
		list($foundUserID) = $DB->sql_row("SELECT id FROM wD_Users WHERE ".implode(' OR ', $whereSQL)." LIMIT 1");
		if( !isset($foundUserID) || !$foundUserID )
		{
			$searchReturn = l_t('No users found matching the given search parameters.');
		}
		else
		{
			$searchReturn = l_t('Matching user found!');
			$userID=$foundUserID;
		}
	}
}
else
{
	$userID = false;
}

if ( !$userID )
{
	libHTML::starthtml(l_t('Search for user'));

	print libHTML::pageTitle(l_t('Search for user'),l_t('Search for a user using either their ID, username, e-mail address, or any combination of the three.'));
	?>

	<?php if( isset($searchReturn) ) print '<p class="notice">'.$searchReturn.'</p>'; ?>

	<form action="profile.php" method="post">
	<ul class="formlist">

		<li class="formlisttitle"><?php print l_t('ID number:'); ?></li>
		<li class="formlistfield">
			<input type="text" name="searchUser[id]" value="" size="10">
		</li>
		<li class="formlistdesc">
			<?php print l_t('The user\'s ID number.'); ?>
		</li>

		<li class="formlisttitle"><?php print l_t('Username:'); ?></li>
		<li class="formlistfield">
			<input type="text" name="searchUser[username]" value="" size="30">
		</li>
		<li class="formlistdesc">
			<?php print l_t('The user\'s username (This isn\'t case sensitive, but otherwise it must match exactly.)'); ?>
		</li>

		<li class="formlisttitle"><?php print l_t('E-mail address:'); ?></li>
		<li class="formlistfield">
			<input type="text" name="searchUser[email]" value="" size="50">
		</li>
		<li class="formlistdesc">
			<?php print l_t('The user\'s e-mail address (This also isn\'t case sensitive, but otherwise it must match exactly.)'); ?>
		</li>
	</ul>

	<div class="hr"></div>

	<p class="notice">
		<input type="submit" class="form-submit" value="<?php print l_t('Search'); ?>">
	</p>
	</form>

	</div>
	<?php
	libHTML::footer();
}
else
{
	try
	{
		$UserProfile = new User($userID);
	}
	catch (Exception $e)
	{
		libHTML::error(l_t("Invalid user ID given."));
	}
}

if ( ! $UserProfile->type['User'] && !$UserProfile->type['Banned'] )
{
	$message = l_t('Cannot display profile: The specified account #%s is not an active user;',$userID).' ';
	if( $UserProfile->type['Guest'] )
		$message .= l_t('it\'s a guest account, used by unregistered people to '.
			'view the server without interacting.');
	elseif( $UserProfile->type['System'] )
		$message .= l_t('it\'s a system account, without a real human using it.');
	else
		$message .= l_t('in fact I\'m not sure what this account is...');

	foreach($UserProfile->type as $name=>$on)
	{
		if ( $on )
			$message .= l_t($name).', ';
	}
	libHTML::error($message);
}


libHTML::starthtml();

print '<div class="content">';

if( isset($searchReturn) )
	print '<p class="notice">'.$searchReturn.'</p>';

if ( isset($_REQUEST['detail']) )
{
	print '<p>(<a href="profile.php?userID='.$UserProfile->id.'">'.l_t('Back').'</a>)</p>';

	switch($_REQUEST['detail'])
	{
		case 'threads':
			$dir=User::cacheDir($UserProfile->id);
			if( file_exists($dir.'/profile_threads.html') )
				print file_get_contents($dir.'/profile_threads.html');
			else
			{
				libAuth::resourceLimiter('view threads',20);

				$tabl = $DB->sql_tabl("SELECT id, subject, message, timeSent FROM wD_ForumMessages
					WHERE fromUserID = ".$UserProfile->id." AND type='ThreadStart'
					ORDER BY timeSent DESC");

				$buf = '<h4>'.l_t('Threads posted:').'</h4>
					<ul>';
				while(list($id,$subject,$message, $timeSent)=$DB->tabl_row($tabl))
				{
					$buf .= '<li><em>'.libTime::text($timeSent).'</em>:
						<a href="forum.php?threadID='.$id.'">'.$subject.'</a><br />'.
						$message.'</li>';
				}
				$buf .= '</ul>';

				file_put_contents($dir.'/profile_threads.html', $buf);
				print $buf;
			}
			break;

		case 'replies':
			$dir=User::cacheDir($UserProfile->id);
			if( file_exists($dir.'/profile_replies.html') )
				print file_get_contents($dir.'/profile_replies.html');
			else
			{
				libAuth::resourceLimiter('view replies',20);

				$tabl = $DB->sql_tabl("SELECT f.id, a.id, a.subject, f.message, f.timeSent
					FROM wD_ForumMessages f INNER JOIN wD_ForumMessages a ON ( f.toID = a.id )
					WHERE f.fromUserID = ".$UserProfile->id." AND f.type='ThreadReply'
					ORDER BY f.timeSent DESC");

				$buf = '<h4>'.l_t('Replies:').'</h4>
					<ul>';
				while(list($id,$threadID,$subject, $message, $timeSent)=$DB->tabl_row($tabl))
				{
					$buf .= '<li><em>'.libTime::text($timeSent).'</em>: <a href="forum.php?threadID='.$threadID.'#'.$id.'">Re: '.$subject.'</a><br />'.
						$message.'</li>';
				}
				$buf .= '</ul>';

				file_put_contents($dir.'/profile_replies.html', $buf);
				print $buf;
			}
			break;

		case 'civilDisorders':
			if ( $User->type['Moderator'] || $User->id == $UserProfile->id ) {

				$tabl = $DB->sql_tabl("SELECT g.name, c.countryID, c.turn, c.bet, c.SCCount, c.gameId, c.forcedByMod
					FROM wD_CivilDisorders c INNER JOIN wD_Games g ON ( c.gameID = g.id )
					WHERE c.userID = ".$UserProfile->id . ($User->type['Moderator'] ? '' : ' AND c.forcedByMod = 0'));
	
				print '<h4>'.l_t('Civil disorders:').'</h4>
					<ul>';
					
				if ($DB->last_affected() == 0) {
					print l_t('No civil disorders found for this profile.');
				}

				while(list($name, $countryID, $turn, $bet, $SCCount,$gameID,$forcedByMod)=$DB->tabl_row($tabl))
				{
					print '<li>
					'.l_t('Game:').' <strong><a href="board.php?gameID='.$gameID.'">'.$name.'</a></strong>,
						'.l_t('country #:').' <strong>'.$countryID.'</strong>,
						'.l_t('turn:').' <strong>'.$turn.'</strong>,
						'.l_t('bet:').' <strong>'.$bet.'</strong>,
						'.l_t('supply centers:').' <strong>'.$SCCount.'</strong>';
						if ( $User->type['Moderator'] ) print ','.l_t('ignored:').' <strong>'.$forcedByMod.'</strong>';
						print '</li>';
				}
				print '</ul>';

				$tabl = $DB->sql_tabl("SELECT c.countryID, c.turn, c.bet, c.SCCount, c.gameId, c.forcedByMod
					FROM wD_CivilDisorders c LEFT JOIN wD_Games g ON c.gameID = g.id
					WHERE g.id is null AND c.userID = ".$UserProfile->id . ($User->type['Moderator'] ? '' : ' AND c.forcedByMod = 0'));
					
				if ($DB->last_affected() != 0) {
					print '<h4>'.l_t('Cancelled civil disorders:').'</h4><ul>';
					while(list($countryID, $turn, $bet, $SCCount,$gameID,$forcedByMod)=$DB->tabl_row($tabl))
					{
						print '<li>
						'.l_t('Game:').' <strong>'.$gameID.'</strong>,
							'.l_t('country #:').' <strong>'.$countryID.'</strong>,
							'.l_t('turn:').' <strong>'.$turn.'</strong>,
							'.l_t('bet:').' <strong>'.$bet.'</strong>,
							'.l_t('supply centers:').' <strong>'.$SCCount.'</strong>';
							if ( $User->type['Moderator'] ) print ','.l_t('ignored:').' <strong>'.$forcedByMod.'</strong>';
							print '</li>';
					}
					print "</ul>";
				}
				if ($UserProfile->deletedCDs != 0) {
					print 'Additionally, there are ' . $UserProfile->deletedCDs . ' deleted CDs for this account (eg, self CD positions retaken by this user).';
				}
				print '<h4>'.l_t('NMRs:').'</h4><ul>';
				$tabl = $DB->sql_tabl("SELECT n.gameID, n.countryID, n.turn, n.bet, n.SCCount, g.name FROM wD_NMRs n LEFT JOIN wD_Games g ON n.gameID = g.id WHERE n.userID = ".$UserProfile->id);
				if ($DB->last_affected() != 0) {
					while(list($gameID, $countryID, $turn, $bet, $SCCount, $name)=$DB->tabl_row($tabl))
					{                                          
						print '<li>';
						if ($name != '') {
							print l_t('Game:').' <strong><a href="board.php?gameID='.$gameID.'">'.$name.'</a></strong> ';
						} else {	
							print l_t('Game:').' <strong>'.$gameID.' '.l_t('(Cancelled)').'</strong> ';
						}
						print l_t('country #:').' <strong>'.$countryID.'</strong>,
							'.l_t('turn:').' <strong>'.$turn.'</strong>,
							'.l_t('bet:').' <strong>'.$bet.'</strong>,
							'.l_t('supply centers:').' <strong>'.$SCCount.'</strong>
                                                 	</li>';
					}
				} else {
					print l_t('No NMRs found for this profile.');
				}
				print '</ul>';

			} else {
                         	print l_t('You do not have permission to view this page.');
			}

			break;

		case 'reports':
			if ( $User->type['Moderator'] )
			{
				require_once(l_r('lib/modnotes.php'));
				libModNotes::checkDeleteNote();
				libModNotes::checkInsertNote();
				print libModNotes::reportBoxHTML('User', $UserProfile->id);
				print libModNotes::reportsDisplay('User', $UserProfile->id);
			}
		break;

		case 'relations':
			require_once('lib/relations.php');
			libRelations::checkRelationsChange();
			print libRelations::reportsDisplay($UserProfile->id);
		break;
	}

	print '</div>';
	libHTML::footer();
}

print '<div><div class="rightHalf">
		';

$rankingDetails = $UserProfile->rankingDetails();

$showAnon = ($UserProfile->id == $User->id || $User->type['Moderator']);

print '<ul class="formlist">';

print '<li><strong>'.l_t('Rank:').'</strong> '.$rankingDetails['rank'].'</li>';

if ( $rankingDetails['position'] < $rankingDetails['rankingPlayers'] )
	print '<li><strong>'.l_t('Position:').'</strong> '.$rankingDetails['position'].' / '.
		$rankingDetails['rankingPlayers'].' '.l_t('(top %s%%)',$rankingDetails['percentile']).'</li>';

print '<li><strong>'.l_t('Available points:').'</strong> '.$UserProfile->points.' '.libHTML::points().'</li>';

print '<li><strong>'.l_t('Points in play:').'</strong> '.($rankingDetails['worth']-$UserProfile->points-($showAnon ? 0 : $rankingDetails['anon']['points'])).' '.libHTML::points().'</li>';

print '<li><strong>'.l_t('Total points:').'</strong> '.$rankingDetails['worth'].' '.libHTML::points().'</li>';

/**
 * Add possibility for mods to write down notes
 */
if ( $User->type['Moderator'])
{

	// Edit the note of the group
	if (isset($_REQUEST['EditNote']))
	{
		$DB->sql_put("DELETE FROM wD_ModeratorNotes WHERE linkIDType='User' AND type='PrivateNote' AND linkID=".$UserProfile->id);			

		// turn modalert on or off
		if (isset($_REQUEST['alert']))
		{
			$DB->sql_put("UPDATE wD_Users SET type = CONCAT_WS(',',type,'ModAlert') WHERE id = ".$UserProfile->id);
			$UserProfile->type['ModAlert'] = true;
		}
		else
		{
			$DB->sql_put("UPDATE wD_Users SET type = REPLACE(type,'ModAlert','') WHERE id = ".$UserProfile->id);		
			$UserProfile->type['ModAlert'] = false;
		}
		
		$notes=$DB->msg_escape($_REQUEST['EditNote'],false);
		if ($notes == '')
		{
			if ($UserProfile->type['ModAlert'])
			{
				$DB->sql_put("UPDATE wD_Users SET type = REPLACE(type,'ModAlert','') WHERE id = ".$UserProfile->id);					
				$UserProfile->type['ModAlert'] = false;
			}
		}
		else
		{
		
			$notes = preg_replace('#(modforum.php.viewthread[:= _]?)([0-9]+)#i',
				'<a href="modforum.php?viewthread=\2#\2" class="light">\1\2</a>',$notes);
			$notes = preg_replace('#(modforum.php.threadID[:= _]?)([0-9]+)#i',
				'<a href="modforum.php?threadID=\2#\2" class="light">\1\2</a>',$notes);

			$DB->sql_put("INSERT INTO wD_ModeratorNotes SET 
				note='".$notes."',
				linkID=".$UserProfile->id.",
				timeSent=".time().",
				fromUserID='".$User->id."',
				type='PrivateNote',
				linkIDType='User'");
		}
	}
	
	list($notes)=$DB->sql_row("SELECT note FROM wD_ModeratorNotes WHERE linkIDType='User' AND linkID=".$UserProfile->id);
	
	if ($notes== '')
		print '<li>&nbsp;</li>
		<b>ModNotes<span id="EditNoteButton"> (<a href="#" onclick="$(\'EditNoteBox\').show(); $(\'EditNoteButton\').hide(); return false;">Add</a>)</span>:</b>
		<span id="EditNoteBox" style="display:none;">
			<TABLE>
				<TD style="border: 1px solid #666">
					<form method="post" style="display:inline;">
						<textarea name="EditNote" style="width:100%;height:200px"></textarea><br />
						<TABLE>
							<TD><input type="checkbox" name="alert" value="on" '.($UserProfile->type['ModAlert'] ? 'checked="checked"':'').'> ModAlert</TD>
							<TD align="right"><input type="Submit" value="Submit" /></TD>
						</TABLE>
					</form>				
				</TD>
			</TABLE>
		</span>';
	else
		print '<li>&nbsp;</li>'.($UserProfile->type['ModAlert'] ? libHTML::alert() : '').'
		<b>ModNotes<span id="EditNoteButton"> (<a href="#" onclick="$(\'EditNoteBox\').show(); $(\'EditNoteText\').hide(); $(\'EditNoteButton\').hide(); return false;">Edit</a>)</span>: '.($UserProfile->type['ModAlert'] ? libHTML::alert() : '').'</b>
		<TABLE>
			<TD style="border: 1px solid #666">
				<span id="EditNoteText">'.$notes.'</span>
				<span id="EditNoteBox" style="display:none;">
					<form method="post" style="display:inline;">
						<textarea name="EditNote" style="width:100%;height:200px">'.str_ireplace("</textarea>", "<END-TA-DO-NOT-EDIT>", str_ireplace("<br />", "\n",
							preg_replace('#<a href..modforum.php.viewthread.*class..light.>(.*)</a>#Ui','\1',$notes))).'</textarea><br />
						<TABLE>
							<TD><input type="checkbox" name="alert" value="on" '.($UserProfile->type['ModAlert'] ? 'checked="checked"':'').'> ModAlert</TD>
							<TD align="right"><input type="Submit" value="Submit" /></TD>
						</TABLE>
					</form>				
				</span>
			</TD>
		</TABLE>';
}

if( $UserProfile->type['DonatorPlatinum'] )
	$donatorMarker = libHTML::platinum().' - <strong>'.l_t('Platinum').'</strong>';
elseif( $UserProfile->type['DonatorGold'] )
	$donatorMarker = libHTML::gold().' - <strong>'.l_t('Gold').'</strong>';
elseif( $UserProfile->type['DonatorSilver'] )
	$donatorMarker = libHTML::silver().' - '.l_t('Silver');
elseif( $UserProfile->type['DonatorBronze'] )
	$donatorMarker = libHTML::bronze().' - '.l_t('Bronze');
else
	$donatorMarker = false;

if( $donatorMarker )
	print '<li>&nbsp;</li><li><strong>'.l_t('Donator:').'</strong> '.$donatorMarker.'</li>';

if( $UserProfile->type['DevGold'] )
	$donatorMarker = libHTML::devgold().' - <strong>Gold</strong>';
elseif( $UserProfile->type['DevSilver'] )
	$donatorMarker = libHTML::devsilver().' - Silver';
elseif( $UserProfile->type['DevBronze'] )
	$donatorMarker = libHTML::devbronze().' - Bronze';
else
	$donatorMarker = false;

if( $donatorMarker )
	print '<li>&nbsp;</li><li><strong>Developer:</strong> '.$donatorMarker.'</li>';
	
print '<li>&nbsp;</li>';


list($posts) = $DB->sql_row(
	"SELECT SUM(gameMessagesSent) FROM wD_Members m
	WHERE m.userID = ".$UserProfile->id);
if( is_null($posts) ) $posts=0;
print '<li><strong>'.l_t('Game messages:').'</strong> '.$posts.'</li>';

print '<li>&nbsp;</li>';
$total = 0;
$includeStatus=array('Won','Drawn','Survived','Defeated','Abandoned','Left');
foreach($rankingDetails['stats'] as $name => $status)
{
	if ( !in_array($name, $includeStatus) ) continue;

	$total += $status;
	if (!$showAnon && isset($rankingDetails['anon'][$name]))
		$total -= $rankingDetails['anon'][$name];
}

if (isset($rankingDetails['stats']['Playing']))
{
	$playing = $rankingDetails['stats']['Playing'];
	if (!$showAnon && isset($rankingDetails['anon']['Playing']))
		$playing -= $rankingDetails['anon']['Playing'];
}

if( $total || (isset($playing) && $playing) )
{
	print '<li><strong>'.l_t('Game stats:').'</strong> <ul class="gamesublist">';

	foreach($rankingDetails['stats'] as $name => $status)
	{
		if ( !in_array($name, $includeStatus) ) continue;

		print '<li>'.l_t($name.': <strong>%s</strong>',$status);
		if ($total > 0) print ' ( '.round(($status/$total)*100).'% )';
		print '</li>';
	}

	if ($total)
		print '<li>'.l_t('Total (finished): <strong>%s</strong>',$total).'</li>';

	foreach($rankingDetails['stats'] as $name => $status)
	{
		if ( in_array($name, $includeStatus) ) continue;

		if (!$showAnon && isset($rankingDetails['anon'][$name]))
			$status -= $rankingDetails['anon'][$name];
		print '<li>'.l_t($name.': <strong>%s</strong>',$status).'</li>';
	}

	print '<li>'.l_t('No moves received / received:').' <strong>'.$UserProfile->nmrCount.'/'.$UserProfile->phaseCount.'</strong></li>';
	print '<li>'.l_t('Reliability rating:').' <strong>'.round($UserProfile->reliabilityRating).'%</strong>';
	if( $User->type['Moderator'] || $User->id == $UserProfile->id )
	{
		print ' <a class="light" href="profile.php?detail=civilDisorders&userID='.$UserProfile->id.'">'.l_t('breakdown').'</a>';
	}                                                                                                         
	print '</li>';
	
	print '<li>'.l_t('Total (finished): <strong>%s</strong>',$total).'</li>';

	if ( $rankingDetails['takenOver'] )
		print '<li>'.l_t('Left and taken over: <strong>%s</strong>',$rankingDetails['takenOver']).
			'(<a href="profile.php?detail=civilDisorders&userID='.$UserProfile->id.'">'.l_t('View details').'</a>)</li>';

	print '</ul></li>';
}
print '<li>&nbsp;</li>';

print '<style type="text/css"> .tooltip { position: absolute; display: none; background-color: #eaeaea;} </style>
		<div id="RRtooltip" class="tooltip">RR = ((noNMR + noCD)/2)^3</div>
		<div id="Itooltip" class="tooltip">Integrity = CDtakeovers - NMRs * 0.2 - CDs * 0.6</div>
		<script type="text/javascript">
			document.onmousemove = updateWMTT;
			function updateWMTT(e) {
				if (wmtt != null && wmtt.style.display == "block") {
					x = (e.pageX ? e.pageX : window.event.x) + wmtt.offsetParent.scrollLeft - wmtt.offsetParent.offsetLeft;
					y = (e.pageY ? e.pageY : window.event.y) + wmtt.offsetParent.scrollTop - wmtt.offsetParent.offsetTop;
					wmtt.style.left = (x + 20) + "px";
					wmtt.style.top = (y - 20) + "px";
				}
			}
		    function showRR() {
				wmtt = document.getElementById("RRtooltip");
				wmtt.style.display = "block"
			}
		    function showI() {
				wmtt = document.getElementById("Itooltip");
				wmtt.style.display = "block"
			}
			function hideWMTT() {
				wmtt.style.display = "none";
			}
		</script>';
		
print '<li><strong>'.l_t('Reliability stats: ').'</strong> <ul class="gamesublist">';
print '<li>Reliability: 
	<a onmouseover="showRR();"; onmouseout="hideWMTT();" href="#">
    <strong>'.libReliability::getGrade($UserProfile).'</strong></a></li>';
print '<li>NoNMR: <strong>'.libReliability::noNMRrating($UserProfile).'%</strong> (<strong>'.$UserProfile->missedMoves.'</strong> missed phases out of <strong>'.$UserProfile->phasesPlayed.'</strong>)</li>';
print '<li>NoCD: <strong>'.libReliability::noCDrating($UserProfile).'%</strong> (<strong>'.$UserProfile->gamesLeft.'</strong> abandoned games out of <strong>'.$UserProfile->gamesPlayed.'</strong>)</li>';

if ( $User->type['Moderator'])
	print '<li>Integrity: 
		<a onmouseover="showI();"; onmouseout="hideWMTT();" href="#">
		<strong>'.libReliability::integrityRating($UserProfile).'</strong></a> (<strong>'.$UserProfile->CDtakeover.'</strong> CD takeovers)</li>';


print '</ul></li></div>';

print "<h2>".$UserProfile->username;
if ( $User->type['User'] && $UserProfile->type['User'] && ! ( $User->id == $UserProfile->id || $UserProfile->type['Moderator'] || $UserProfile->type['Guest'] || $UserProfile->type['Admin'] ) )
{
	$userMuted = $User->isUserMuted($UserProfile->id);

	print '<a name="mute"></a>';
	if( isset($_REQUEST['toggleMute'])) {
		$User->toggleUserMute($UserProfile->id);
		$userMuted = !$userMuted;
	}
	$muteURL = 'profile.php?userID='.$UserProfile->id.'&toggleMute=on&rand='.rand(0,99999).'#mute';
	print ' '.($userMuted ? libHTML::muted($muteURL) : libHTML::unmuted($muteURL));
}
	
// Start BlockUser-feature (Same as mute, but he can't join your games. (Works on admins too)
if ( $User->type['User'] && $UserProfile->type['User'] && ! ( $User->id == $UserProfile->id || $UserProfile->type['Guest'] || $UserProfile->type['Admin'] ) )
{
	$userBlocked = $User->isUserBlocked($UserProfile->id);

	print '<a name="block"></a>';
	if( isset($_REQUEST['toggleBlock'])) {
		$User->toggleUserBlock($UserProfile->id);
		$userBlocked = !$userBlocked;
	}
	$blockURL = 'profile.php?userID='.$UserProfile->id.'&toggleBlock=on&rand='.rand(0,99999).'#block';
	print ' '.($userBlocked ? libHTML::blocked($blockURL) : libHTML::unblocked($blockURL));
// End BlockUserFeature	
}
print '</h2>';

// Regular user info starts here:
print '<div class="leftHalf" style="width:50%">';




if( $UserProfile->type['Banned'] )
	print '<p><strong>'.l_t('Banned').'</strong></p>';

if ( $UserProfile->comment )
	print '<p class="profileComment">"'.$UserProfile->comment.'"</p>';

print '<p><ul class="formlist">';

if ( $UserProfile->type['ForumModerator'] || $UserProfile->type['Admin'] )
{
	print '<li><strong>'.l_t('Mod/Admin team').'</strong></li>';
	print '<li>&nbsp;</li>';
}

if ( $UserProfile->online )
	print '<li><strong>'.l_t('Currently logged in.').'</strong> ('.libHTML::loggedOn($UserProfile->id).')</li>';
else
	print '<li><strong>'.l_t('Last visited:').'</strong> '.libTime::text($UserProfile->timeLastSessionEnded).'</li>';

list($posts) = $DB->sql_row(
	"SELECT (
		SELECT COUNT(fromUserID) FROM `wD_ForumMessages` WHERE type='ThreadStart' AND fromUserID = ".$UserProfile->id."
		) + (
		SELECT COUNT(fromUserID) FROM `wD_ForumMessages` WHERE type='ThreadReply' AND fromUserID = ".$UserProfile->id."
		)"); // Doing the query this way makes MySQL use the type, fromUserID index
if( is_null($posts) ) $posts=0;
list($likes) = $DB->sql_row("SELECT COUNT(*) FROM wD_LikePost WHERE userID=".$UserProfile->id);
list($liked) = $DB->sql_row("SELECT COUNT(*) FROM wD_ForumMessages fm 
	INNER JOIN wD_LikePost lp ON lp.likeMessageID = fm.id 
	WHERE fm.fromUserID=".$UserProfile->id);
$likes = ($likes ? '<strong>'.l_t('Likes:').'</strong> '.$likes : '');
$liked = ($liked ? '<strong>'.l_t('Liked:').'</strong> '.$liked : '');

print '<li><strong>'.l_t('Forum posts:').'</strong> '.$posts.'<br />
	<strong>'.l_t('View:').'</strong> <a class="light" href="profile.php?detail=threads&userID='.$UserProfile->id.'">'.l_t('Threads').'</a>,
		<a class="light" href="profile.php?detail=replies&userID='.$UserProfile->id.'">'.l_t('replies').'</a>';

print '<br/>'.implode(' / ',array($likes,$liked)).'
	</li>';
unset($likes,$liked);

print '<li>&nbsp;</li>';
print '<li><strong>'.l_t('Joined:').'</strong> '.$UserProfile->timeJoinedtxt().'</li>';
print '<li><strong>'.l_t('User ID#:').'</strong> '.$UserProfile->id.'</li>';
if( $User->type['Moderator'] )
{
	print '<li><strong>'.l_t('E-mail:').'</strong>
			'.$UserProfile->email.($UserProfile->hideEmail == 'No' ? '' : ' <em>'.l_t('(hidden for non-mods)').'</em>').'
		</li>';
}
elseif ( $UserProfile->hideEmail == 'No' )
{
	$emailCacheFilename = libCache::dirID('users',$UserProfile->id).'/email.png';
	if( !file_exists($emailCacheFilename) )
	{
		$image = imagecreate( strlen($UserProfile->email) *8, 15);
		$white = imagecolorallocate( $image, 255, 255, 255);
		$black = imagecolorallocate( $image, 0, 0, 0 );

		imagestring( $image, 2, 10, 1, $UserProfile->email, $black );

		imagepng($image, $emailCacheFilename);
	}

	print '<li><strong>'.l_t('E-mail:').'</strong>
			<img src="'.STATICSRV.$emailCacheFilename.'" alt="'.l_t('[E-mail address image]').'" title="'.l_t('To protect e-mails from spambots they are embedded in an image').'" >
		</li>';
}

if ( $UserProfile->hideEmail != 'No' )
{
	$emailCacheFilename = libCache::dirID('users',$UserProfile->id).'/email.png';

	if( file_exists($emailCacheFilename) )
		unlink($emailCacheFilename);
}

if ( $UserProfile->homepage )
{
	print '<li><strong>'.l_t('Home page:').'</strong> '.$UserProfile->homepage.'</li>';
}

print '<li>&nbsp;</li>';

//print '<li><a href="profile.php?detail=reports&userID='.$UserProfile->id.'" class="light">View/post a moderator report</a></li>';

//print '<li>&nbsp;</li>';

if ( $User->type['Moderator'])
{
	if ($UserProfile->rlGroup != 0)
	{
		print '<li><a href="profile.php?detail=relations&userID='.$UserProfile->id.'" class="light">'.$UserProfile->username.' is currently in a RL-Group.</a>
			(<img src="images/icons/'.($UserProfile->rlGroup < 0 ? 'bad':'').'friends.png">)</li>';

		list($rlNotes)=$DB->sql_row("SELECT note FROM wD_ModeratorNotes WHERE linkIDType='rlGroup' AND linkID=".abs($UserProfile->rlGroup));
		if ($rlNotes!= '')
		print '
		<TABLE>
			<TD style="border: 1px solid #666">
				<span id="EditNoteText">'.$rlNotes.'</span>
			</TD>
		</TABLE>';
	}
	else
	{
		print '<li><a href="profile.php?detail=relations&userID='.$UserProfile->id.'" class="light">'.$UserProfile->username.' is currently in no RL-Group.</a></li>';
	}
}
print '</li></ul></p></div><div style="clear:both"></div></div>';


// Start interactive area:

if ( $User->type['Moderator'] && $User->id != $UserProfile->id )
{
	$modActions=array();

	if ( $User->type['Admin'] )
		$modActions[] = '<a href="index.php?auid='.$UserProfile->id.'">'.l_t('Enter this user\'s account').'</a>';

	$modActions[] = libHTML::admincpType('User',$UserProfile->id);

	if( !$UserProfile->type['Admin'] && ( $User->type['Admin'] || !$UserProfile->type['Moderator'] ) )
		$modActions[] = libHTML::admincp('banUser',array('userID'=>$UserProfile->id), l_t('Ban user'));
	
	if( !$UserProfile->type['Donator'])
		$modActions[] = libHTML::admincp('makeDonator',array('userID'=>$UserProfile->id), l_t('Give donator benefits'));

	if( $User->type['Admin'] && !$UserProfile->type['Moderator'] )
		$modActions[] = libHTML::admincp('giveModerator',array('userID'=>$UserProfile->id), l_t('Make moderator'),true);

	if( $User->type['Admin'] && ($UserProfile->type['Moderator'] && !$UserProfile->type['Admin']) )
		$modActions[] = libHTML::admincp('takeModerator',array('userID'=>$UserProfile->id), l_t('Remove moderator'),true);
	
	if( $User->type['Admin'] && !$UserProfile->type['ForumModerator'] )
		$modActions[] = libHTML::admincp('giveForumModerator',array('userID'=>$UserProfile->id), l_t('Make forum moderator'),true);
	
	if( $User->type['Admin'] && ($UserProfile->type['ForumModerator'] && !$UserProfile->type['Admin']) )
		$modActions[] = libHTML::admincp('takeForumModerator',array('userID'=>$UserProfile->id), l_t('Remove forum moderator'),true);
	
	$modActions[] = libHTML::admincp('reportMuteToggle',array('userID'=>$UserProfile->id), l_t(($UserProfile->muteReports=='No'?'Mute':'Unmute').' mod reports'),true);

	$modActions[] = '<a href="admincp.php?tab=Multi-accounts&aUserID='.$UserProfile->id.'" class="light">'.
		l_t('Enter multi-account finder').'</a>';

	if($modActions)
	{
		print '<div class="hr"></div>';
		print '<p class="notice">';
		print implode(' - ', $modActions);
		print '</p>';
	}
	
	
	if( !$UserProfile->type['Admin'] 
		&& ( $User->type['Admin'] || $User->type['ForumModerator'] ) )
	{
		$silences = $UserProfile->getSilences();
		
		print '<p><ul class="formlist"><li><strong>'.l_t('Silences:').'</strong></li><li>';
		
		if( count($silences) == 0 )
			print l_t('No silences against this user.').'</p>';
		else
		{
			print '<ul class="formlist">';
			foreach($silences as $silence) {
				// There should only be one active silence displayed; other active silences could be misleading
				if( !$silence->isEnabled() || $silence->id == $UserProfile->silenceID )
					print '<li>'.$silence->toString().'</li>';
			}
			print '</ul>';
		}
		
		print '</li><li>';
		print libHTML::admincp('createUserSilence',array('userID'=>$UserProfile->id,'reason'=>''),l_t('Silence user'));
		print '</li></ul></p>';
	}
}

if ( $User->type['User'] && $User->id != $UserProfile->id && !$User->notifications->ForceModMessage)
{
	print '<div class="hr"></div>';

	print '<a name="messagebox"></a>';

	if ( isset($_REQUEST['message']) && $_REQUEST['message'] )
	{
		if ( ! libHTML::checkTicket() )
		{
			print '<p class="notice">'.l_t('You seem to be sending the same message again, this may happen if you refresh '.
				'the page after sending a message.').'</p>';
		}
		else
		{
			if ( $UserProfile->sendPM($User, $_REQUEST['message']) )
            {
                print '<p class="notice">'.l_t('Private message sent successfully.').'</p>';
            }
			else 
            {
                print '<p class="notice">'.l_t('Private message could not be sent. You may be silenced or muted.').'</p>';
            }

		}
	}

	print '<div style="margin-left:20px"><ul class="formlist">';
	print '<li class="formlisttitle">'.l_t('Send private-message:').'</li>
		<li class="formlistdesc">'.l_t('Send a message to this user.').'</li>';

	print '<form action="profile.php?userID='.$UserProfile->id.'#messagebox" method="post">
		<input type="hidden" name="formTicket" value="'.libHTML::formTicket().'" />
		<textarea name="message" style="width:80%" rows="4"></textarea></li>
		<li class="formlistfield"><input type="submit" class="form-submit" value="'.l_t('Send').'" /></li>
		</form>
		</ul>
		</div>';
}

libHTML::pagebreak();

$search = new search('Profile');

$profilePager = new PagerGames('profile.php',$total);
$profilePager->addArgs('userID='.$UserProfile->id);

if ( isset($_REQUEST['advanced']) && $User->type['User'] )
{
	print '<a name="search"></a>';
	print '<h3>'.l_t('Search %s\'s games:',$UserProfile->username).' (<a href="profile.php?page=1&amp;userID='.$UserProfile->id.'#top" class="light">'.l_t('Close').'</a>)</h3>';

	$profilePager->addArgs('advanced=on');

	$searched=false;
	if ( isset($_REQUEST['search']) )
	{
		libAuth::resourceLimiter('profile game search',5);

		$searched=true;
		$_SESSION['search-profile.php'] = $_REQUEST['search'];

		$search->filterInput($_SESSION['search-profile.php']);
	}
	elseif( isset($_REQUEST['page']) && isset($_SESSION['search-profile.php']) )
	{
		$searched=true;
		$search->filterInput($_SESSION['search-profile.php']);
	}

	print '<div style="margin:30px">';
	print '<form action="profile.php?userID='.$UserProfile->id.'&advanced=on#top" method="post">';
	print '<input type="hidden" name="page" value="1" />';
	$search->formHTML();
	print '</form>';
	print '<p><a href="profile.php?page=1&amp;userID='.$UserProfile->id.'#top" class="light">'.l_t('Close search').'</a></p>';
	print '</div>';

	if( $searched )
	{
		print '<div class="hr"></div>';
		print $profilePager->pagerBar('top','<h3>'.l_t('Results:').'</h3>');

		$gameCount = $search->printGamesList($profilePager);

		if( $gameCount == 0 )
		{
			print '<p class="notice">';

			if( $profilePager->currentPage > 1 )
				print l_t('No more games found for the given search parameters.');
			else
				print l_t('No games found for the given search parameters, try broadening your search.');

			print '</p>';
			print '<div class="hr"></div>';
		}
	}
}
else
{
	$searched = true;

	if(isset($_SESSION['search-profile.php']))
		unset($_SESSION['search-profile.php']);

	$leftSide = '<h3>'.l_t('%s\'s games',$UserProfile->username).' '.
			( $User->type['User'] ? '(<a href="profile.php?userID='.$UserProfile->id.'&advanced=on#search">'.l_t('Search').'</a>)' : '' ).
			'</h3>';
	print $profilePager->pagerBar('top', $leftSide);

	$gameCount = $search->printGamesList($profilePager);

	if ( $gameCount == 0 )
	{
		print '<p class="notice">';
		if( $profilePager->currentPage > 1 )
			print l_t('No more games found for this profile.');
		else
			print l_t('No games found for this user.');
		print '</p>';

		print '<div class="hr"></div>';
	}
}

if ( $searched && $gameCount > 1 )
	print $profilePager->pagerBar('bottom','<a href="#top">'.l_t('Back to top').'</a>');
else
	print '<a name="bottom"></a>';

print '</div>';
libHTML::footer();

?>
