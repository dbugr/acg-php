<?php
// Daily runs

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

if (isset($_GET['debug'])) {
	$debug = true;
}

$ClubCode = GetParameter('ClubCode');

// connect to the database
mysqlconnect($connection);

?>
<html>

<head>
	<title>Daily Update</title>
</head>

<body>
	<p>Daily update script</p>
	<pre>

<?php
///////////////  functions

// Get list of expiration dates
function GetMemberExpirationDates()
{
	global $connection;
	//global $ClubCode;
	$ClubCode = GetParameter('ClubCode');

	// Execute the query
	$query = "SELECT cust_id, u_date_expiration FROM members WHERE (m_club='" . $ClubCode . "')";
	if (!($result = @mysqli_query($query, $connection))) {
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		return (0);
	}

	$MembershipExpirationDates = array();
	while ($u_row = @mysqli_fetch_array($result)) {
		$u_cust_id = $u_row['cust_id'];
		$MembershipExpirationDates[$u_cust_id] = $u_row['u_date_expiration'];
	}
	return ($MembershipExpirationDates);
}


// determine if membership will expire
function membership_will_expire($date_expiration, $days)
{
	if (!empty($date_expiration))
		$expiration_stamp = strtotime($date_expiration);
	else
		$expiration_stamp = strtotime('1970-01-01');
	$time_stamp = time();
	if (((float) ($time_stamp) < (float) $expiration_stamp) and
		((float) $expiration_stamp < (float) ($time_stamp + ($days * 3600 * 24)))
	)
		$will_expire = 1;
	else
		$will_expire = 0;
	return ($will_expire);
}

// email admin when memberships are about to expire
function email_expirations()
{
	global $connection;
	global $debug;
	//global $ShortClubName;
	//global $emailNoticesTo;
	//global $email_from;
	//global $public_url_prefix;
	//global $ClubCode;
	//global $public_domain_name;

	$ShortClubName	 	= GetParameter('ShortClubName');
	$EmailNoticesTo 	= GetParameter('EmailNoticesTo');
	$EmailNoticesFrom 	= GetParameter('EmailNoticesFrom');
	$ClubCode 			= GetParameter('ClubCode');
	$public_url_prefix 	= PublicDomainName();


	$days = 21;

	// send member names to a list of email addresses
	$email_subject = $ShortClubName . " Expiring Memberships List";
	$email_body = $ShortClubName . " Expiring Memberships List\n\n" .
		"These memberships will expire in " . $days . " days\n\n\n";

	// obtain list of members
	$query = "SELECT * FROM members WHERE (m_club='" . $ClubCode . "') ORDER BY m_firstname, m_lastname";
	if (!($result = @mysqli_query($query, $connection)))
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	$MemberExpirationDates = GetMemberExpirationDates();
	$todays_date = date('m-d-Y', time());
	$days = 21;
	$day = date('D', time());
	if ($day != "Thu") return;
	$sendmail = false;
	while ($m_row = mysqli_fetch_array($result)) {
		$cust_id = $m_row["cust_id"];
		if (membership_will_expire($MemberExpirationDates[$cust_id], $days)) {
			$email_body .= $m_row["m_firstname"] . " " . $m_row["m_lastname"] . "\n " .
				$m_row["m_phonehome"] . " " .
				$m_row["m_email"] . "\n " .
				"Expires: " . $MemberExpirationDates[$cust_id] . "\n " .
				'/members/medit.php?cust_id=' . $cust_id  . "\n" .
				"Last Login = " . $m_row["u_date_last_login"] . "\n" .
				"Comments = " . $m_row["m_comments"] . "\n\n";
			$sendmail = true;
		}
	}
	if ($sendmail) {
		if (!$debug) {
			MailWrapper($EmailNoticesTo, $email_subject, $email_body, $EmailNoticesFrom);
		} else {
			echo $EmailNoticesTo . "\n";
			echo $EmailNoticesFrom . "\n";
			echo $email_subject . "\n\n";
			echo $email_body;
			echo "\n\n";
		}
	}
}

// send admin a list of members needing refunds for event(s)
function email_refunds()
{
	global $connection;
	global $debug;
	global $ShortClubName;
	global $emailNoticesTo;
	global $email_from;
	global $public_url_prefix;
	//global $ClubCode;
	$ClubCode = GetParameter('ClubCode');


	// send member names needing refunds for events
	$email_subject = $ShortClubName . " Event Refunds needed";
	$email_body = $ShortClubName . " Event Refunds needed";

	// obtain list of members
	$query = "SELECT * FROM reserve WHERE ((cust_id LIKE '" . $ClubCode . "%') AND (r_attending != 'Yes') AND (r_amount_paid > 0))";
	if (!($result = @mysqli_query($query, $connection)))
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	$sendEmails = false;
	while ($row = mysqli_fetch_array($result)) {
		$sendEmails = true;
		$cust_id = $row["cust_id"];
		$event_id = $row["event_id"];
		$amount = $row["r_amount_paid"];
		$email_body .= "\n\nMember:" .  $public_url_prefix . '/members/medit.php?cust_id=' . $cust_id  . "\n" .
			"Event: " . $public_url_prefix . '/members/eview.php?event_id=' . $event_id . "\n" .
			"Amount: " . $amount . "\n" .
			"Paid on: " . $row['r_date_paid'];

		$querydelete = "delete from reserve where ((cust_id = '$cust_id') AND (event_id='" . $event_id . "'))";
		if (!$debug) {
			$querydelete = "delete from reserve where ((cust_id = '$cust_id') AND (event_id='" . $event_id . "'))";
			if (!($result = @mysqli_query($querydelete, $connection)))
				trigger_error("MySQL error: " 
					. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		} else
			echo "query=" . $querydelete . "\n";
	}
	if ($sendEmails) {
		if (!$debug) {
			MailWrapper($emailNoticesTo, $email_subject, $email_body, $email_from);
		} else {
			echo $emailNoticesTo . "\n";
			echo $email_from . "\n";
			echo $email_subject . "\n\n";
			echo $email_body;
			echo "\n\n";
		}
	}
}


// is this member expired
function expired_membership($date_expiration)
{
	if (empty($date_expiration))
		return false;
	$expiration_stamp = strtotime($date_expiration);
	$time_stamp = time();
	return ((float) $time_stamp > (float) $expiration_stamp);
}

// create an array of strings, each of which
// contains an event name and link, ready for
// use in an html table.
function GenEventsList()
{
	global $connection;
	$today = date('Y-m-d', time() - 3600 * 24);
	$query = "SELECT event_id,leader_id,e_date_added,e_name,e_begindate,e_date_added,e_status " .
		"FROM events " .
		"WHERE e_date_changed >= '" . $today . "' AND e_date_added <= '" . $today . "' AND e_status != 'Hide' " .
		"ORDER BY e_begindate";
	if (!($result = @mysqli_query($query, $connection))) {
		return array();
	}
	$strings = array();
	while ($row = mysqli_fetch_array($result)) {
		$event_id = $row['event_id'];
		$leader_name = GetMemberName($row['leader_id']);
		$str = formatDate2($row["e_begindate"]) . "  " . $row["e_name"] . " - " . $leader_name . "\n" .
			"http://" . $_SERVER['HTTP_HOST'] . "/members/eview.php?event_id=" . $row["event_id"];
		if ($row['e_status'] != "Approved") {
			$status = $row['e_status'];
			if ($status == 'Canceled') $status = 'CANCELLED';
			$str .= "&nbsp;&nbsp;&nbsp;" . $status;
		}
		$str .= "\n\n";
		$strings[$event_id] = $str;
	}
	return ($strings);
}

// create an array of strings, each of which
// contains a new members name
// new member is someone who joined
// within the last 7 days.
function GenMembersList()
{
	global $connection;
	//global $ClubCode;
	$ClubCode = GetParameter('ClubCode');

	$MemberExpirationDates = array();
	$MemberExpirationDates = GetMemberExpirationDates($connection);

	$query = "SELECT * FROM members WHERE (m_club='" . $ClubCode . "')";
	if (!($result = @mysqli_query($query, $connection)))
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	$strings = array();
	$rows = array();
	while ($row = mysqli_fetch_array($result)) {
		$cust_id = $row['cust_id'];
		$today = date('m/d/Y', time());
		if (!expired_membership($MemberExpirationDates[$cust_id])) {
			$str = $row["m_firstname"] . " " . $row["m_lastname"] . "\n";
			$strings[$cust_id] = $str;
			$rows[$cust_id] = $row;
		}
	}
	return ($rows);
}


// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
	global $connection;

	$name = "Unknown";
	$query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";
	if (!($result1 = @mysqli_query($query1, $connection)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	if (($memberrow = mysqli_fetch_array($result1)))
		$name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
	return ($name);
}

// Given a cust_id, obtain and return
// the customer email address as a string
function GetMemberEmail($cust_id)
{
	global $connection;

	$email = "Unknown";
	$query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";
	if (!($result1 = @mysqli_query($query1, $connection)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	if (!($memberrow = mysqli_fetch_array($result1)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	$email = $memberrow["m_email"];
	return ($email);
}

// create an array of events for this user
function GenReminderEventsList($cust_id, $days)
{
	global $connection;
	$target_date_stamp = time() + ($days * (3600 * 24));
	$target_date = date('Y-m-d', $target_date_stamp);

	$query = "SELECT reserve.cust_id,events.leader_id,events.event_id,events.e_name,
					events.e_begindate,events.e_begintime,reserve.r_attending
          FROM reserve INNER JOIN events
          ON reserve.event_id = events.event_id
          WHERE reserve.cust_id = '" . $cust_id . "'
          AND events.e_begindate = '" . $target_date . "'
          AND reserve.r_attending != 'Comments'
          ORDER BY e_begindate";
	if (!($result = @mysqli_query($query, $connection))) {
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	}

	$strings = array();
	while ($row = mysqli_fetch_array($result)) {
		$event_id = $row['event_id'];
		$leader_name = GetMemberName($row['leader_id']);

		$str = date('D M j', strtotime($row["e_begindate"])) . "  " .
			date('g:ia', strtotime($row["e_begintime"])) . "  " .
			$row["e_name"] . "\n" .
			"Leader: " . $leader_name . "  " . GetMemberEmail($row["leader_id"]) . "\n" .
			"http://" . $_SERVER['HTTP_HOST'] . "/members/eview.php?event_id="  . $row["event_id"] . "\n\n";
		$strings[$event_id] = $str;
	}
	return ($strings);
}


// create an array of events for this user
function GenReminderDepositList($cust_id, $days)
{
	global $connection;
	$target_date_stamp = time() + ($days * (3600 * 24));
	$target_date = date('Y-m-d', $target_date_stamp);
	$query = "SELECT reserve.cust_id,events.leader_id,events.event_id,events.e_name,
					events.e_begindate,events.e_begintime,events.e_deposit_date,events.e_deposit,
					reserve.r_attending,reserve.r_num_guests,reserve.r_amount_paid
          FROM reserve INNER JOIN events
          ON reserve.event_id = events.event_id
          WHERE reserve.cust_id = '" . $cust_id . "'
          AND events.e_deposit_date = '" . $target_date . "'
          AND reserve.r_attending != 'Comments'
          ORDER BY e_begindate";
	if (!($result = @mysqli_query($query, $connection))) {
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	}
	$strings = array();
	while ($row = mysqli_fetch_array($result)) {
		$event_id = $row['event_id'];
		$leader_name = GetMemberName($row['leader_id']);

		$guests = isset($row['r_num_guests']) ? $row['r_num_guests'] : 0;
		$paid = isset($row['r_amount_paid']) ? $row['r_amount_paid'] : 0;
		$deposit = isset($row['e_deposit']) ? $row['e_deposit'] : 0;

		$needed = ($deposit * $guests) - $paid;
		if ($needed > 0) {
			$str = "You are signed up for this event which needs a deposit paid soon.\n";
			if ($guests > 0)
				$str = "You and " . $guests . " guests are signed up for this event which needs a deposit paid soon. \n";
			$str .= "A total of $" . $needed . " is needed by " . date('D M j', strtotime($row["e_deposit_date"])) . ". \n";
			$str .= "The " . $row["e_name"] . " event starts on " . date('D M j', strtotime($row["e_begindate"])) . "  " .
				date('g:ia', strtotime($row["e_begintime"])) . ". \n" .
				"Leader: " . $leader_name . "  " . GetMemberEmail($row["leader_id"]) . "\n" .
				"http://" . $_SERVER['HTTP_HOST'] . "/members/eview.php?event_id="  . $row["event_id"] . "\n\n";
			$strings[$event_id] = $str;
		}
	}
	return ($strings);
}

// create an array of events for this user
function GenReminderPaymentList($cust_id, $days)
{
	global $connection;
	$target_date_stamp = time() + ($days * (3600 * 24));
	$target_date = date('Y-m-d', $target_date_stamp);

	$query = "SELECT reserve.cust_id,events.leader_id,events.event_id,events.e_name,
					events.e_begindate,events.e_begintime,events.e_fullprice_date,events.e_fullprice,
					reserve.r_attending,reserve.r_num_guests,reserve.r_amount_paid
          FROM reserve INNER JOIN events
          ON reserve.event_id = events.event_id
          WHERE reserve.cust_id = '" . $cust_id . "'
          AND events.e_fullprice_date = '" . $target_date . "'
          AND reserve.r_attending != 'Comments'
          ORDER BY e_begindate";
	if (!($result = @mysqli_query($query, $connection))) {
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	}

	$strings = array();
	while ($row = mysqli_fetch_array($result)) {
		$event_id = $row['event_id'];
		$leader_name = GetMemberName($row['leader_id']);

		$guests = isset($row['r_num_guests']) ? $row['r_num_guests'] : 0;
		$paid = isset($row['r_amount_paid']) ? $row['r_amount_paid'] : 0;
		$deposit = isset($row['e_fullprice']) ? $row['e_fullprice'] : 0;

		$needed = ($deposit * $guests) - $paid;
		if ($needed > 0) {
			$str = "You are signed up for this event which needs to be paid soon.\n";
			if ($guests > 0)
				$str = "You and " . $guests . " guests are signed up for this event which needs to be paid soon.\n";
			$date = "";
			if (isset($row["e_deposit_date"]))
				$date = " is needed by " . date('D M j', strtotime($row["e_deposit_date"]));
			$str .= "A total of $" . $needed . $date . "\n";
			$str .= date('D M j', strtotime($row["e_begindate"])) . "  " .
				date('g:ia', strtotime($row["e_begintime"])) . "  " .
				$row["e_name"] . "\n" .
				"Leader: " . $leader_name . "  " . GetMemberEmail($row["leader_id"]) . "\n" .
				"http://" . $_SERVER['HTTP_HOST'] . "/members/eview.php?event_id="  . $row["event_id"] . "\n\n";
			$strings[$event_id] = $str;
		}
	}
	return ($strings);
}

// ============== code starts here
// STEP 1 - run admin functions
// email future member expirations to admin
$email_from =
	"From: " . $emailNoticesFrom . "\n" .
	"Reply-To: " . $emailNoticesFrom . "\n" .
	"Return-Path: " . $emailNoticesFrom . "\n" .
	"X-Mailer: PHP/" . phpversion();

email_expirations();
//email_refunds();

// STEP 2 - run member notifications
// build up a list of changed events
$echanges = GenEventsList();

$members = GenMembersList($connection);


// loop thru members
foreach ($members as $member) {
	$cust_id = $member['cust_id'];
	$emailNoticesTo = $member["m_firstname"] . " " . $member["m_lastname"] .
		" <" . $member["m_email"] . ">";
	$email_body = $member["m_firstname"] . "-\n";
	$email_subject = $ShortClubName . " event updates";
	$send = false;

	// event reminders
	if (isset($member['m_email_reminder'])) {
		$days = trim($member['m_email_reminder']);
	} else {
		$days = NULL;
	}
	//if (!is_numeric($days)) 
	//{
	//  $days = 1;
	//}

	// deposits due
	if ($days != NULL) {
		$ereminders = GenReminderDepositList($cust_id, $days);
		if (count($ereminders) != 0) {
			$send = true;
			foreach ($ereminders as $str) {
				$email_body .= $str;
			}
		}
	}

	// payments due
	if ($days != NULL) {
		$ereminders = GenReminderPaymentList($cust_id, $days);
		if (count($ereminders) != 0) {
			$send = true;
			foreach ($ereminders as $str) {
				$email_body .= $str;
			}
		}
	}

	if ($days != NULL) {
		$ereminders = GenReminderEventsList($cust_id, $days);
		if (count($ereminders) != 0) {
			$send = true;
			$email_body .= "Count Down!  Look what's coming up on your activity calendar - \n\n";
			foreach ($ereminders as $str) {
				$email_body .= $str;
			}
			$email_body .= "To change your notifications, login to the website \n" .
				"click 'Edit My Profile', adjust the EMail Reminder settiing.\n\n";
		}
	}

	if ($days == NULL) {
		$ereminders = array();
	}

	// any changed events
	if ((count($echanges) != 0) &&
		($member['m_email_on_event_change'] == 1)
	) {
		$send = true;
		$email_body .= "\n\nThe following " . $ClubCompanyName . " events were recently changed \n\n";
		foreach ($echanges as $str) {
			$email_body .= $str;
		}
		$email_body .= "To turn this notification feature off, login to the website \n" .
			"click 'Edit My Profile', then deselect (uncheck) \n" .
			"the 'Email Event Changes' check box.\n\n";
	}

	// add trailer to email
	$email_body .= "Recess for Adults!\n" . $ClubCompanyName . "\n\n";

	// anything need sending?
	if ($send) {
		if (!$debug) {
			MailWrapper($emailNoticesTo, $email_subject, $email_body, $email_from);
		} else {
			echo "To:" . $emailNoticesTo . "\n";
			echo $email_from . "\n";
			echo "Subject: " . $email_subject . "\n";
			echo "Body:\n" . $email_body;
			echo "\n\n";
		}
	}
}

?>  

</pre>
</body>

</html>