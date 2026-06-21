<?php
// display event categories by month

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');
require('admin.php');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
	// Register a message to show the user
	$message = "Error: you are not logged in!";
	SessionRegister("message", $message);

	// Register where they came from
	$referer = __FILE__;
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

if (!$AdminLevel) {
	// redirect to the home page
	$loc = "Location: /index.php";
	header($loc);
	exit;
}

// connect to database
mysqlconnect($connection);

// get event categories
$categories = array();
$reservations = array();
$query = "SELECT DISTINCT e_category FROM events";
if (!($result = @mysqli_query($connection, $query)))
	trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
while ($e_row = mysqli_fetch_array($result)) {
	$category = $e_row['e_category'];

	// events by month
	$months = array();
	for ($m = 1; $m <= 12; $m++)
		$months[$m] = 0;
	$categories[$category] = $months;

	// yes by month
	$rmonths = array();
	for ($m = 1; $m <= 12; $m++)
		$rmonths[$m] = 0;
	$reservations[$category] = $rmonths;
}

// find out how many events in each month
foreach ($categories as $category => $months) {
	$query = "SELECT e_begindate FROM events WHERE e_category='" . $category . "'";
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	while ($e_row = mysqli_fetch_array($result)) {
		$month = date('n', $e_row['e_begindate']);
		$months[$month] += 1;
	}
}

// find out how many people in each month
foreach ($reservations as $category => $months) {
	$query = "SELECT event_id,e_begindate FROM events WHERE e_category='" . $category . "'";
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	while ($e_row = mysqli_fetch_array($result)) {
		$month = date('n', $e_row['e_begindate']);
		$eventid = $e_row['event_id'];
		$query = "SELECT cust_id FROM reserve WHERE ((r_attending=='Yes') AND (event_id=" . $eventid . "))";
		if (!($resultR = @mysqli_query($connection, $query)))
			trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		$months[$month] += mysqli_num_rows($resultR);
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
	<title>Event categories by month</title>
	<link rel=STYLESHEET href="/inc/admin.css" Type="text/css">
</head>

<body>
	<div id="centercontent">
		<hr>
		<p>Events by category and month</p>
		<table border=1>
			<tr>
				<th>Category</th>
				<th>Jan</th>
				<th>Feb</th>
				<th>Mar</th>
				<th>Apr</th>
				<th>May</th>
				<th>Jun</th>
				<th>Jul</th>
				<th>Aug</th>
				<th>Sep</th>
				<th>Oct</th>
				<th>Nov</th>
				<th>Dec</th>
				<th>Total</th>
			</tr>
			<?php

			// initialize monthly totals
			$mTotals = array();
			for ($k = 1; $k <= 12; $k++) {
				$mTotals[$k] = 0;
			}

			// display events by category and month	
			foreach ($categories as $category => $months) {
				$total = 0;
				echo "<tr><td><a href='/admin/eventnamesbycategory.php?category=" . $category . "'>" . $category . "</a><td>";
				for ($k = 1; $k <= 12; $k++) {
					echo "<td>" . $months[$k] . "</td>";
					$mTotals[$k] += $months[$k];
					$total += $months[$k];
				}
				echo "<td>" . $total . "</td></tr>";
			}

			// display monthly totals
			echo "<tr><td>Totals</td>";
			$total = 0;
			for ($k = 1; $k <= 12; $k++) {
				echo "<td>" . $mTotals[$k] . "</td>";
				$total += $mTotals[$k];
			}
			echo "<td>" . $total . "</td></tr>";

			?>
		</table>

		<hr>
		<p>Reservations by category and month</p>
		<table border=1>
			<tr>
				<th>Category</th>
				<th>Jan</th>
				<th>Feb</th>
				<th>Mar</th>
				<th>Apr</th>
				<th>May</th>
				<th>Jun</th>
				<th>Jul</th>
				<th>Aug</th>
				<th>Sep</th>
				<th>Oct</th>
				<th>Nov</th>
				<th>Dec</th>
				<th>Total</th>
			</tr>
			<?php
			// initialize monthly totals
			$mTotals = array();
			for ($k = 1; $k <= 12; $k++) {
				$mTotals[$k] = 0;
			}

			// display reservations by category and month	
			foreach ($reservations as $category => $months) {
				$total = 0;
				echo "<tr><td>" . $category . "<td>";
				for ($k = 1; $k <= 12; $k++) {
					echo "<td>" . $months[$k] . "</td>";
					$mTotals[$k] += $months[$k];
					$total += $months[$k];
				}
				echo "<td>" . $total . "</td></tr>";
			}

			// display monthly totals
			echo "<tr><td>Totals</td>";
			$total = 0;
			for ($k = 1; $k <= 12; $k++) {
				echo "<td>" . $mTotals[$k] . "</td>";
				$total += $mTotals[$k];
			}
			echo "<td>" . $total . "</td></tr>";
			?>
		</table>
	</div>

	<?php TopBanner("Event categories by month"); ?>
</body>

</html>