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
	session_register("message", $message);

	// Register where they came from
	$referer = __FILE__;
	session_register("referer", $referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}



$FileName = __FILE__;
$WebPageTitle = 'Services / Trade or Barter - ' . $ClubCompanyName;
//$DisableShoutbox = true;
require('top.php');

?>

<table width="100%" border="0" cellspacing="0" cellpadding="">
	<tr align="center">
		<td width="20%" align="center">
		</td>
		<td width="60%" align="center">
			<h3>Services / Trade or Barter</h3>
			<? include dirname(__FILE__) . '/' . $RelOffset . '/shoutboxmc/shoutbox.inc.php'; ?>
		</td>
		<td width="20%">
		</td>
	</tr>
</table>


<?php
require('footer.php');
?>