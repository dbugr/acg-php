<?php

// listing of member preferences

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
	// Register a message to show the user
	$message = "Error: you are not logged in! (elist)";
	SessionRegister("message", $message);

	// Register where they came from
	$referer = $_SERVER['PHP_SELF'];
	SessionRegister("referer", $referer);

	// redirect to the login page
	$loc = "Location: /index.php";
	header($loc);
	exit;
}


if (!$AdminLevel) {
	$loc = "Location: http://" . $_SERVER['HTTP_HOST'] . "/members/elist.php";
	header($loc);
	exit;
}


// connect to the database
mysqlconnect($connection);

$query = "SELECT m_preferences FROM members WHERE (members.m_club='$ClubCode') ";
$totals = array();
$members = 0;
$surveys = 0;
$SkatingType = "";
$SportObservationType = "";
$SportParticipationType = "";
$OutsideSportObservationType = "";
$OutsideSportParticipationType = "";
$classes = "";
$always = "";
$activity = "";
if (($result = @mysqli_query($connection, $query))) {
	$members = @mysqli_num_rows($result);
	while ($row1 = mysqli_fetch_array($result)) {
		if (isset($row1['m_preferences'])) {
			$surveys++;
			$p = $row1['m_preferences'];
			$s = explode("&", $p);
			foreach ($s as $Token => $Value) {
				$pieces = explode("=", $Value);
				if ($pieces[0] == "SkatingType") {
					if (!empty($pieces[1]))
						$SkatingType .= $pieces[1] . ";";
				} else if ($pieces[0] == "SportObservationType") {
					if (!empty($pieces[1]))
						$SportObservationType .= $pieces[1] . ";";
				} else if ($pieces[0] == "SportParticipationType") {
					if (!empty($pieces[1]))
						$SportParticipationType .= $pieces[1] . ";";
				} else if ($pieces[0] == "OutsideSportObservationType") {
					if (!empty($pieces[1]))
						$OutsideSportObservationType .= $pieces[1] . ";";
				} else if ($pieces[0] == "OutsideSportParticipationType") {
					if (!empty($pieces[1]))
						$OutsideSportParticipationType .= $pieces[1] . ";";
				} else if ($pieces[0] == "classes") {
					if (!empty($pieces[1]))
						$classes .= $pieces[1] . ";";
				} else if ($pieces[0] == "always") {
					if (!empty($pieces[1]))
						$always .= $pieces[1] . ";";
				} else if ($pieces[0] == "activity") {
					if (!empty($pieces[1]))
						$activity .= $pieces[1] . ";";
				} else if (isset($totals[$pieces[0]])) {
					$totals[$pieces[0]]++;
				} else {
					$totals[$pieces[0]] = 1;
				}
			}
		}
	}
}

// Get list of expiration dates
function DisplayResult($name)
{
	global $totals;
	if (isset($totals[$name]))
		echo $totals[$name];
	else
		echo "0";
	echo " - " . $name . "<br>";
}

$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Admin Member Preferences Report ' . $ClubCompanyName;
$admin = true;
require('include.php');


?>

<div id="centercontent2">
	<hr>
	<table>
		<tr>
			<th align=left width="50%">Member Event Preferences Report</th>
		</tr>
		<tr>
			<td valign="top" width="50%">

				<?php
				arsort($totals);
				foreach ($totals as $activity => $frequency) {
					DisplayResult($activity);
					switch ($activity) {
						case 'Skating':
							echo $SkatingType . "<br>";
							break;
						case 'SportObservation':
							echo $SportObservationType . "<br>";
							break;
						case 'SportParticipation':
							echo $SportParticipationType . "<br>";
							break;
						case 'OutsideSportObservation':
							echo $OutsideSportObservationType . "<br>";
							break;
						case 'OutsideSportParticipation':
							echo $OutsideSportParticipationType . "<br>";
							break;
					}
				}

				?>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td width="50%">
				<P>Classes I'd like to attend: <?php echo $classes; ?></P>
				<P>Something I've always wanted to do: <?php echo $always; ?></P>
				<P>I'd like to see <?php echo $activity; ?></P>

				<P><?php
						DisplayResult('Spontaneous');
						DisplayResult('LongRange');
						?>
				</P>
		</tr>
	</table>
</div>

<?php
require('footer.php');
?>