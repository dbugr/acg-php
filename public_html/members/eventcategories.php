<?php
// display event categories by month

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
	// Register a message to show the user
	$message = "Error: you are not logged in!";
	SessionRegister("message", $message);

	// Register where they came from
	$referer = $_SERVER['PHP_SELF'];
	SessionRegister("referer", $referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}

// get levels  
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';

if (!$AdminLevel && !$LeaderLevel) {
	// redirect to the home page
	$loc = "Location: /index.php";
	header($loc);
	exit;
}

// connect to database
mysqlconnect($connection);

$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Event Categories - ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">
	<hr>
	<p>Events by category and month</p>


	<?php
	// get event categories
	$categories = array();
	$query = "SELECT DISTINCT e_category FROM events";
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	echo "<table border=1>";
	echo "<tr>";
	echo "	<th>Category</th>";
	echo "  <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>May</th><th>Jun</th>";
	echo "  <th>Jul</th><th>Aug</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th>";
	echo "  <th>Total</th>";
	echo "</tr>";

	$totals = array();
	for ($m = 1; $m <= 12; $m++)
		$totals[$m] = 0;
	while ($e_row = mysqli_fetch_array($result)) {
		$category = $e_row['e_category'];

		// events by month
		$months = array();
		for ($m = 1; $m <= 12; $m++)
			$months[$m] = 0;

		$query = "SELECT e_begindate FROM events WHERE e_category='" . $category . "'";
		if (!($result2 = @mysqli_query($connection, $query)))
			trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		while ($e_row = mysqli_fetch_array($result2)) {
			if (!isset($e_row['e_begindate'])) continue;
			$e_begindate = $e_row['e_begindate'];
			if (substr($e_begindate, 0, 1) != "2") continue;
			$month = date('n', strtotime($e_begindate));
			$months[$month] += 1;
			$totals[$month] += 1;
		}

		$total = 0;
		echo "<tr><td><a href='/members/eventnamesbycategory.php?category=" . $category . "'>" . $category . "</a></td>";
		for ($m = 1; $m <= 12; $m++) {
			$total += $months[$m];
			echo "<td>" . $months[$m] . "</td>";
		}
		echo "<td>" . $total . "</td>";
		echo "</tr>";
	}
	$total = 0;
	echo "<tr><td>Totals</td>";
	for ($m = 1; $m <= 12; $m++) {
		$total += $totals[$m];
		echo "<td>" . $totals[$m] . "</td>";
	}
	echo "<td>" . $total . "</td>";
	echo "</tr>";
	echo "</table>"
	?>

	<hr>
	<p>Reservations by category and month</p>

	<?php
	$reservations = array();

	$query = "SELECT DISTINCT e_category FROM events";
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	echo "<table border=1>";
	echo "<tr>";
	echo "	<th>Category</th>";
	echo "  <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>May</th><th>Jun</th>";
	echo "  <th>Jul</th><th>Aug</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th>";
	echo "  <th>Total</th>";
	echo "</tr>";

	$totals = array();
	for ($m = 1; $m <= 12; $m++)
		$totals[$m] = 0;
	while ($e_row = mysqli_fetch_array($result)) {
		$category = $e_row['e_category'];

		// reservations by month
		$months = array();
		for ($m = 1; $m <= 12; $m++)
			$months[$m] = 0;

		$query = "SELECT event_id,e_begindate FROM events WHERE e_category='" . $category . "'";
		if (!($result2 = @mysqli_query($connection, $query)))
			trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		while ($e_row = mysqli_fetch_array($result2)) {
			if (!isset($e_row['e_begindate'])) continue;
			$e_begindate = $e_row['e_begindate'];
			if (substr($e_begindate, 0, 1) != "2") continue;
			$month = date('n', strtotime($e_row['e_begindate']));
			$eventid = $e_row['event_id'];
			$query = "SELECT cust_id FROM reserve WHERE ((r_attending='Yes') AND (event_id='" . $eventid . "'));";
			if (!($resultR = @mysqli_query($connection, $query)))
				trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
			$months[$month] += mysqli_num_rows($resultR);
			$totals[$month] += mysqli_num_rows($resultR);
		}

		$total = 0;
		echo "<tr><td>" . $category . "</td>";
		for ($m = 1; $m <= 12; $m++) {
			$total += $months[$m];
			echo "<td>" . $months[$m] . "</td>";
		}
		echo "<td>" . $total . "</td>";
		echo "</tr>";
	}
	$total = 0;
	echo "<tr><td>Totals</td>";
	for ($m = 1; $m <= 12; $m++) {
		$total += $totals[$m];
		echo "<td>" . $totals[$m] . "</td>";
	}
	echo "<td>" . $total . "</td>";
	echo "</tr>";
	echo "</table>";
	?>

</div>

<?php
require('footer.php');
?>