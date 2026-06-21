<?php
/*
AdventureClub.info
st
*/
// display list of events
// user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
	// Register a message to show the user
	$message = "Error: you are not logged in! (elist)";
	SessionRegister("message", $message);

	// Register where they came from
	$referer = __FILE__;
	session_register("referer", $referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}



$FileName = __FILE__;
$WebPageTitle = 'Fitness & Exercise - ' . $ClubCompanyName;
//$DisableShoutbox = true;
require('top.php');

?>

<table width="100%" border="0" cellspacing="0" cellpadding="">
	<tr align="center">
		<td width="20%" align="center">
		</td>
		<td width="60%" align="center">
			<h3>Fitness and Exercise... Come join in!</h3>

			<p>This page is dedicated to shout-outs which
				share a training technique, offer encouragement
				to friends working hard at their fitness programs,
				and/or suggest get-togethers for the sake of
				collegial training sessions. This page doesn't
				preclude training postings on our website.
				Rather, it is here to encourage participation
				and diligence relative to posted events as well
				as encouraging all of our members to improve their health.</p>

			<? include dirname(__FILE__) . '/' . $RelOffset . '/shoutboxfe/shoutbox.inc.php'; ?>
		</td>
		<td width="20%">
		</td>
	</tr>
</table>


<?php
require('footer.php');
?>