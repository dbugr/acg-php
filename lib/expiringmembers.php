<?php
// Run daily by cron
// expiring members report and emails to club administrators

//print "expiring members.php: starting execution...\n";

require('always.include.php');

LogMsg("expiring members.php: starting execution...");

//require('/shared/httpd/acg/lib/always.include.php');
$debug = true;
$debug = false;

//session_start();
//require('include.php');

$NumberOfDaysInReport = 21;

// set debug flag from get parameters
//$debug = true;
//$debug = false;
if (isset($_GET['debug'])) {
	$debug = true;
}

if (isset($_GET['days'])) {
	$NumberOfDaysInReport = $_GET['days'];
	$NumberOfDaysInReport = clean($_GET['days'], 3);
}


// connect to the database
//print 'connecting to mysql...\n';
mysqlconnect($connection);

///////////////  functions

/*
// Get list of expiration dates
function GetMemberExpirationDates()
{
	global $connection;
	//global $ClubCode;
	$ClubCode			= GetParameter('ClubCode');


	// Execute the query
	$query = "SELECT cust_id, u_date_expiration FROM members WHERE (m_club='" . $ClubCode . "')";
	if (!($result = @mysqli_query($connection, $query))) {
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
		return (0);
	}

	$MembershipExpirationDates = array();
	while ($u_row = @mysqli_fetch_array($result)) {
		$u_cust_id = $u_row['cust_id'];
		$MembershipExpirationDates[$u_cust_id] = $u_row['u_date_expiration'];
	}
	return ($MembershipExpirationDates);
}
*/


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
function email_expirations($days)
{
	global $connection;
	global $debug;
	$ClubCompanyName	= GetParameter('ClubCompanyName');
	$ShortClubName		= GetParameter('ShortClubName');
	$EmailNoticesTo		= GetParameter('EmailNoticesTo');
	$EmailNoticesFrom	= GetParameter('EmailNoticesFrom');
	$ClubCode					= GetParameter('ClubCode');
	$home							= GetParameter('home');
	$DeveloperEmailAddr	= GetParameter('DeveloperEmailAddr');

	//print "email_expirations() days: ".$days."\n";

	$email_from = $EmailNoticesFrom;

	$email_body = "";
	// send member names to a list of email addresses
	$email_subject = $ShortClubName . " Expiring Memberships List";
	$email_body .= "" . $ShortClubName . " Expiring Memberships List\n\n" .
		"The following memberships will expire in " . $days . " days\n\n";

	// obtain list of members
	$query = "SELECT * FROM members WHERE (m_club='" . $ClubCode . "') ORDER BY m_firstname, m_lastname";
	//print 'query: '.$query;
	//LogMsg('query: '.$query);
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	$MemberExpirationDates = GetMemberExpirationDates();
	$todays_date = date('m-d-Y', time());
	//$day = date('D', time());
	//if ($day != "Thu") return;
	$FoundExpiringMember = false;
	while ($m_row = mysqli_fetch_array($result)) {
		$cust_id = $m_row["cust_id"];
		//print 'Checking expiration date for cust_id: '.$cust_id."\n";
		//LogMsg('Checking expiration date for cust_id: '.$cust_id);
		if (membership_will_expire($MemberExpirationDates[$cust_id], $days)) {
			$email_body .= "";
			$email_body .= "" . $m_row["m_firstname"] . " " . $m_row["m_lastname"] . "\n";
			$email_body .= ""
				. "" . "Expires: " . $MemberExpirationDates[$cust_id] . "\n"
				. "" . "Last Login = " . $m_row["u_date_last_login"] . "\n"
				. "" . '<a href="https://' . $home . '/members/medit.php?cust_id=' . $cust_id  . '#AdminStart">'
				. 'Edit membership data</a>' . "\n"
				. "" . $m_row["m_phonehome"] . " "
				. "" . $m_row["m_email"] . "\n"
				. "" . "Comments = " . $m_row["m_comments"] . "\n"
				. "" . "\n";
			$FoundExpiringMember = true;
			//print 'Membership will expire for cust_id: '.$cust_id."\n";
			LogMsg('Membership will expire for cust_id: '.$cust_id);
		}
	}
	if (!$FoundExpiringMember) {
		$email_body .= "No expiring memberships found for time period " . $days . " days" . "\n";
	}
	$email_body .= "" . "\n";

	// send email
	$MailTo = $EmailNoticesTo;  // for production use !!
	//$MailTo = 'sail.fl@gmail.com';  // for testing

	$email_headers = 	GetParameter('EmailHeaders');

	//$headers = "";
	//$headers .= 'From: ' . $EmailNoticesFrom . "\n";
	//$headers .= 'Cc: ' . $DeveloperEmailAddr . "\n";
	//$headers .= 'Reply-To: ' . $EmailNoticesFrom . "\n";
	//$headers .= "X-Mailer: PHP/" . phpversion() . "\n";
	//LogMsg('Attempting to send email '.$email_subject );
	print 'Memberships that will expire in ' . $days . " days:\n\n";
	print "To: " . $MailTo . "\n";
	print $email_headers;
	print 'Subject: ' . $email_subject . "\n";
	print $email_body;
	//print "<br>";
	$message_sent = MailWrapper($MailTo, $email_subject, $email_body, $email_headers);
}


// ============== code starts here
// STEP 1 - run admin functions
// email future member expirations to admin
//print 'starting email_expirations()...'."\n";
email_expirations($NumberOfDaysInReport);


?>  
