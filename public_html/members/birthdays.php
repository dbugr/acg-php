<?php

// event leaders home page


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


$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';

mysqlconnect($connection);

// obtain member information from members table
$cust_id = @getCustomerID($loginUsername);

if (!isset($cust_id)) {
	trigger_error(
		"Invalid Customer ID ("
			. $cust_id . ") for loginUsername: " . $loginUsername .
			" in eList!",
		E_USER_ERROR
	);
	exit;
}

$query = "SELECT cust_id, m_firstname,"
	. "m_lastname, m_date_birth, "
	. "m_club, u_date_expiration, "
	. "m_disp_date_birth "
	. " FROM members "
	. " WHERE  "
	. " m_club = "
	. "'" . $ClubCode . "'"
	. " AND "
	. " now() <= u_date_expiration"
	. " AND m_disp_date_birth = 1"
	. " ORDER BY substring(m_date_birth,5,5) ";
//." ORDER BY m_date_birth ";

if (!($result = @mysqli_query($connection, $query)))
	trigger_error(
		"MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
		E_USER_ERROR
	);

$m_recs = array();
while ($m_row = mysqli_fetch_array($result)) {
	$db = substr($m_row['m_date_birth'], 5, 5);
	$m_recs[$db] =
		"<tr>"
		. "<td>"
		. $m_row['m_firstname']
		. '&nbsp;'
		. $m_row['m_lastname']
		. "</td>"
		. "<td>"
		. substr($m_row['m_date_birth'], 5, 5)
		. "</td>"
		//."<td>"
		//.$m_row['m_disp_date_birth']
		//."</td>"
		//."<td>"
		//.$m_row['m_club']
		//."</td>"
		//."<td>"
		//.$m_row['u_date_expiration']
		//."</td>"
		. "</tr>";
}
//ksort($m_recs);

$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Member Birthday list ' . $ClubCompanyName;
require('top.php');
?>

<h1>Member Birthday List</h1>
<table>
	<?php
	foreach ($m_recs as $key => $value) {
		echo $value;
	}
	?>
</table>

<?php
require('footer.php');
?>