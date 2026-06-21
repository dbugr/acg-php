<?php

// members preferences survey
// TODO- 
// 1.  add m_preferences to members table
// 2.  serialize/deserialize array
// 3.  write report module

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
	// Register a message to show the user
	$message = "Error: you are not logged in!";
	SessionRegister("message", $message);

	// Register where they came from
	if (isset($_GET['event_id']))
		$referer = $_SERVER['PHP_SELF'] . "?event_id=" . clean($_GET['event_id']);
	else
		$referer = $_SERVER['PHP_SELF'];
	SessionRegister("referer", $referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}

// Reset $formVars
$formVars = array();

// Reset the errors
$errors = array();

// connect to the database
mysqlconnect($connection);

// obtain member cust_id from members table
$loginUsername = LoginUsername();
$cust_id = getCustomerID(LoginUsername(), $connection);

$query = "SELECT m_preferences FROM members WHERE (cust_id='" . $cust_id . "');";
$row = array();
if (($result = @mysqli_query($connection, $query))) {
	if ((@mysqli_num_rows($result)) > 0)
		$row1 = mysqli_fetch_array($result);
	if (isset($row1['m_preferences'])) {
		$p = $row1['m_preferences'];
		$s = explode("&", $p);
		foreach ($s as $Token => $Value) {
			$pieces = explode("=", $Value);
			if (isset($pieces[1]))
				$row[$pieces[0]] = $pieces[1];
			else
				$row[$pieces[0]] = "";
		}
	}
}

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName		= GetParameter('ClubCompanyName');
$WebPageTitle = 'Preferences Survey ' . $ClubCompanyName;
require('top.php');
?>

<div id="centercontent2">
	<hr>

	<form method="POST" action="/members/survey-post.php">
		<table>
			<tr>
				<th align=left width="50%">Indoor</th>
				<th align=left width="50%">Outdoor</th>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<input type="checkbox" name="Bingo" value="1" <?php if (isset($row['Bingo'])) echo "checked"; ?>>Bingo<br>
					<input type="checkbox" name="Bowling" value="1" <?php if (isset($row['Bowling'])) echo "checked"; ?>>Bowling<br>
					<input type="checkbox" name="Concerts" value="1" <?php if (isset($row['Concerts'])) echo "checked"; ?>>Concerts<br>
					<input type="checkbox" name="Dancing" value="1" <?php if (isset($row['Dancing'])) echo "checked"; ?>>Dancing<br>
					<input type="checkbox" name="DinnerTheater" value="1" <?php if (isset($row['DinnerTheater'])) echo "checked"; ?>>Dinner Theater<br>
					<input type="checkbox" name="ElectronicTrivia" value="1" <?php if (isset($row['ElectronicTrivia'])) echo "checked"; ?>>Electronic Trivia<br>
					<input type="checkbox" name="HappyHour" value="1" <?php if (isset($row['HappyHour'])) echo "checked"; ?>>Happy Hour<br>
					<input type="checkbox" name="HouseParties" value="1" <?php if (isset($row['HouseParties'])) echo "checked"; ?>>House Parties<br>
					<input type="checkbox" name="LivePlays" value="1" <?php if (isset($row['LivePlays'])) echo "checked"; ?>>Live Plays<br>
					<input type="checkbox" name="Movies" value="1" <?php if (isset($row['Movies'])) echo "checked"; ?>>Movies<br>
					<input type="checkbox" name="Restaurants" value="1" <?php if (isset($row['Restaurants'])) echo "checked"; ?>>Restaurants<br>
					<input type="checkbox" name="Skating" value="1" <?php if (isset($row['Skating'])) echo "checked"; ?>>Skating<br> &nbsp;&nbsp;&nbsp;&nbsp; Type:&nbsp;<input type="text" name="SkatingType" <?php if (isset($row['SkatingType'])) echo 'value="' . $row['SkatingType'] . '"'; ?>><br>
					<input type="checkbox" name="SportObservation" value="1" <?php if (isset($row['SportObservation'])) echo "checked"; ?>>Sport Observation<br> &nbsp;&nbsp;&nbsp;&nbsp; Type:&nbsp;<input type="text" name="SportObservationType" <?php if (isset($row['SportObservationType'])) echo 'value="' . $row['SportObservationType'] . '"'; ?>><br>
					<input type="checkbox" name="SportParticipation" value="1" <?php if (isset($row['SportParticipation'])) echo "checked"; ?>>Sport Participation<br> &nbsp;&nbsp;&nbsp;&nbsp; Type:&nbsp;<input type="text" name="SportParticipationType" <?php if (isset($row['SportObservationType'])) echo 'value="' . $row['SportObservationType'] . '"'; ?>><br>
					<input type="checkbox" name="Swimming" value="1" <?php if (isset($row['Swimming'])) echo "checked"; ?>>Swimming<br>
					<input type="checkbox" name="WineTasting" value="1" <?php if (isset($row['WineTasting'])) echo "checked"; ?>>Wine Tasting<br>
					<input type="checkbox" name="Yoga" value="1" <?php if (isset($row['Yoga'])) echo "checked"; ?>>Yoga<br>
				</td>
				<td valign="top" width="50%">
					<input type="checkbox" name="Bicycling" value="1" <?php if (isset($row['Bicycling'])) echo "checked"; ?>>Bicycling<br>
					<input type="checkbox" name="Canoeing" value="1" <?php if (isset($row['Canoeing'])) echo "checked"; ?>>Canoeing/Kayaking<br>
					<input type="checkbox" name="DayAtTheBeach" value="1" <?php if (isset($row['DayAtTheBeach'])) echo "checked"; ?>>Day At The Beach<br>
					<input type="checkbox" name="DeepSeaFishing" value="1" <?php if (isset($row['DeepSeaFishing'])) echo "checked"; ?>>Deep Sea Fishing<br>
					<input type="checkbox" name="DiscGolf" value="1" <?php if (isset($row['DiscGolf'])) echo "checked"; ?>>Disc Golf<br>
					<input type="checkbox" name="Golf" value="1" <?php if (isset($row['Golf'])) echo "checked"; ?>>Golf<br>
					<input type="checkbox" name="HangGliding" value="1" <?php if (isset($row['HangGliding'])) echo "checked"; ?>>Hang Gliding<br>
					<input type="checkbox" name="Hiking" value="1" <?php if (isset($row['Hiking'])) echo "checked"; ?>>Hiking<br>
					<input type="checkbox" name="HorsebackRiding" value="1" <?php if (isset($row['HorsebackRiding'])) echo "checked"; ?>>Horseback Riding<br>
					<input type="checkbox" name="RockClimbing" value="1" <?php if (isset($row['RockClimbing'])) echo "checked"; ?>>Rock Climbing<br>
					<input type="checkbox" name="SkyDiving" value="1" <?php if (isset($row['SkyDiving'])) echo "checked"; ?>>Sky Diving<br>
					<input type="checkbox" name="OutsideSportObservation" value="1" <?php if (isset($row['OutsideSportObservation'])) echo "checked"; ?>>Sport Observation<br> &nbsp;&nbsp;&nbsp;&nbsp; Type:&nbsp;<input type="text" name="OutsideSportObservationType" <?php if (isset($row['OutsideSportObservationType'])) echo 'value="' . $row['OutsideSportObservationType'] . '"'; ?>><br>
					<input type="checkbox" name="OutsideSportParticipation" value="1" <?php if (isset($row['OutsideSportParticipation'])) echo "checked"; ?>>Sport Participation<br> &nbsp;&nbsp;&nbsp;&nbsp; Type:&nbsp;<input type="text" name="OutsideSportParticipationType" <?php if (isset($row['OutsideSportParticipationType'])) echo 'value="' . $row['OutsideSportParticipationType'] . '"'; ?>><br>
					<input type="checkbox" name="Windsurfing" value="1" <?php if (isset($row['Windsurfing'])) echo "checked"; ?>>Windsurfing<br>
				</td>
			</tr>
		</table>
		<p>&nbsp;</p>
		<table>
			<tr>
				<th align=left width="50%">Out of Town</th>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<input type="checkbox" name="WeekendsInFlorida" value="1" <?php if (isset($row['WeekendsInFlorida'])) echo "checked"; ?>>Weekend Getaways In Florida<br>
					<input type="checkbox" name="WeekendsOutOfFlorida" value="1" <?php if (isset($row['WeekendsOutOfFlorida'])) echo "checked"; ?>>Weekend Getaways Out Of Florida<br>
					<input type="checkbox" name="ExtendedWeekendsInFlorida" value="1" <?php if (isset($row['ExtendedWeekendsInFlorida'])) echo "checked"; ?>>Extended Weekend Getaways In Florida<br>
					<input type="checkbox" name="ExtendedWeekendsOutOfFlorida" value="1" <?php if (isset($row['ExtendedWeekendsOutOfFlorida'])) echo "checked"; ?>>Extended Weekend Getaways Out Of Florida<br>
					<input type="checkbox" name="Cruises" value="1" <?php if (isset($row['Cruises'])) echo "checked"; ?>>Cruises<br>
					<input type="checkbox" name="WeekLongOutOfFlorida" value="1" <?php if (isset($row['WeekLongOutOfFlorida'])) echo "checked"; ?>>Week Long Getaways Out Of Florida<br>
					<input type="checkbox" name="ThemeVacation" value="1" <?php if (isset($row['ThemeVacation'])) echo "checked"; ?>>Theme Vacation<br>
				</td>
				<td width="50%">
					<P>Classes I'd like to attend:
						<input type="text" name="classes" size=40 <?php if (isset($row['classes'])) echo 'value="' . $row['classes'] . '"'; ?>>
						&nbsp;&nbsp;&nbsp;&nbsp; (painting, cooking, dancing, yoga, etc.)</P>

					<P>Something I've always wanted to do:
						<input type="text" name="always" size=40 <?php if (isset($row['always'])) echo 'value="' . $row['always'] . '"'; ?>></P>

					<P>I'd like to see
						<input type="text" name="activity" <?php if (isset($row['activity'])) echo 'value="' . $row['activity'] . '"'; ?>>
						listed as a club event</P>

					<P>Do you prefer <input type="checkbox" name="Spontaneous" value="1" <?php if (isset($row['Spontaneous'])) echo "checked"; ?>>Spontaneous or<br>
						<input type="checkbox" name="LongRange" value="1" <?php if (isset($row['LongRange'])) echo "checked"; ?>>Long Range plans.</P>
			</tr>
		</table>
		<input type="submit" name="Submit" value="Submit">
	</form>
</div>

<?php

require('footer.php');

?>