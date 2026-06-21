<?php
// validate event record data

// If validation succeeds, it INSERTs or UPDATEs
// a customer and redirect to a receipt page; if it
// fails, it creates error messages and these are later
// displayed by eedit-err.php.

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

LogMsg('EEDIT-POST beginning execution');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername"))
{
	// Register a message to show the user
	$message = "Error: you are not logged in! (elist)";
	SessionRegister("message",$message);

	// Register where they came from
	$referer = $_SERVER['PHP_SELF'];
	SessionRegister("referer",$referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}

//==================================================
// begin function declarations

//==================================================
// if global debug variable is true, write parameters to log file
function DebugLogMsg($description,$var) {
	global $debug;

	if (isset($debug) && $debug) {
		LogMsg($description." ".print_r($var,true));
	}
}



//==================================================
function RetrieveSessionFormVars() {

	$formVars = array();
	if (isset($_SESSION['formVars'])) {
		foreach($_SESSION['formVars'] as $varname => $value) {
			$formVars[$varname] = trim($value);
		}
	}
	return($formVars);
}


//==================================================
function RetrieveSessionErrors() {

	$aErrors = array();
	if (isset($_SESSION['aErrors'])) {
		foreach($_SESSION['aErrors'] as $varname => $value) {
			$aErrors[$varname] = trim($value);
		}
	}
	return($aErrors);
}


//==================================================
function RetrievePostVars() {

	$aPost = array();
	if (isset($_POST)) {
		foreach($_POST as $varname => $value) {
			$aPost[$varname] = trim($value);
		}
	}
	return($aPost);
}


//==================================================
function RetrieveGetVars() {

	$aGet = array();
	if (isset($_GET)) {
		foreach($_GET as $varname => $value) {
			$aGet[$varname] = trim($value);
		}
	}
	return($aGet);
}



// get the next event id
function GetNextEventIdFromDatabase (&$connection)	{
 	$ClubCode			= GetParameter('ClubCode');

	$ClubCodeLength = strlen( $ClubCode);
	$query = "SELECT MAX(substring(event_id, "
						.$ClubCodeLength
			." + 2 "
			.", "
			."14 "
			.") + 0 ) "
						."AS event_id "
			."FROM events "
			."WHERE (event_id LIKE '" . $ClubCode . "%');"
			;
	if (!($result = @ mysqli_query ($connection, $query)))
	{
			trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
		exit;
	}
	$row = mysqli_fetch_array($result);
	$event_id = $row['event_id'];
	$event_id += 1;
	$event_id = $ClubCode . "_" . $event_id;
	return $event_id;
}

// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)  {
  	global $connection;

    $name = "Unknown";
  	$query1 = "SELECT * FROM members where cust_id='" . $cust_id ."'";

      if (!($result1 = @ mysqli_query($connection, $query1)))
      	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);

      if (($memberrow = mysqli_fetch_array($result1)))
        $name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
    return($name);
}


// Given a cust_id, obtain and return
// the customer email address as a string
function GetMemberEmail($cust_id)  {
  	global $connection;

    $email = "Unknown";
  	$query1 = "SELECT * FROM members where cust_id='" . $cust_id . "'";

    if (!($result1 = @ mysqli_query($connection, $query1)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    if (($memberrow = mysqli_fetch_array($result1)))
  	  $email = $memberrow["m_email"];
    return($email);
}


// Given a leader_id, obtain the largest numbered (most recently inserted) event_id
function GetNewEvent_id($leader_id)  {
  	global $connection;

    $query1 = "SELECT event_id FROM events WHERE (leader_id='" . $leader_id . "');";

    if (!($result1 = @ mysqli_query($connection, $query1)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);

    $largest_event_id = 0;
    while ($e_row = mysqli_fetch_array($result1))
  	{
    	if ($e_row['event_id'] > $largest_event_id)
  		{
           $largest_event_id = $e_row['event_id'];
      }
    }
    return($largest_event_id);
}


/*
// Get list of expiration dates
function GetMemberExpirationDates()	{
    global $connection;

	  // Execute the query
		$query = "SELECT cust_id, u_date_expiration FROM members";
  	if (!($result = @ mysqli_query($connection, $query)))
		{
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    	return(0);
 	}

  	$MembershipExpirationDates = array();
  	while ($u_row = @ mysqli_fetch_array($result))
		{
    	$u_cust_id = $u_row['cust_id'];
    	$MembershipExpirationDates[$u_cust_id] = $u_row['u_date_expiration'];
  	}
  	return($MembershipExpirationDates);
}
*/


/*
// is this member expired
function expired_membership ($date_expiration)	{
    if (empty($date_expiration))
    	return false;
    $expiration_stamp = strtotime($date_expiration);
    $time_stamp = time();
    return ((float)$time_stamp > (float)$expiration_stamp);
}
*/

// club that is sponsoring the event
function EventParentClubCode ($event_id) {

	$EventParentClubCode = substr($event_id,0,strpos($event_id,'_'));

	return $EventParentClubCode;
}


// send emails to members who want new event notification
if (isset($debug) && $debug) {
	$DebugEnable = $debug;
} else {
	$DebugEnable = false;
}
function EmailNewEventNotice($event_id,$mode,$DebugEnable)	{
   global $connection;
   global $formVars;				// contains current event
   //global $ClubCompanyName;
   //global $ShortClubName;
   //global $emailNoticesFrom;
   //global $SendNewEvents;
   //global $public_domain_name;
   global $leader_id;

   $ClubCompanyName		= GetParameter('ClubCompanyName');
   $ShortClubName 		= GetParameter('ShortClubName');
   $SendNewEvents 		= GetParameter('SendNewEvents');

   $EmailNoticesTo 		= GetParameter('EmailNoticesTo');
   $EmailNoticesFrom 	= GetParameter('EmailNoticesFrom');
 
   $loginUsername = LoginUsername();
   if (!$SendNewEvents) return;

    $MemberExpirationDates = array();
    $MemberExpirationDates = GetMemberExpirationDates($connection);

    $query = "SELECT cust_id,m_firstname,m_lastname,m_email,
				m_club,
				m_email_on_new_event,
				m_email_on_event_change
              FROM members
			  WHERE (m_email_on_new_event > 0 or
			  m_email_on_event_change > 0) ";

  	if (!($result = @ mysqli_query($connection, $query)))
     	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);

		//$leader_id = $formVars['leader_id'];
    $leader_name = GetMemberName($leader_id);
    $leader_email = GetMemberEmail($leader_id);

		$email_headers = 	GetParameter('EmailHeaders');
		//$email_from= "From: " . $EmailNoticesFrom . "\n" .
    //          	 "Reply-To: " . $EmailNoticesFrom . "\n" .
    //          	 "Return-Path: " . $EmailNoticesFrom . "\n" .
    //          	 "X-Mailer: PHP/" . phpversion();

								 if ($mode == 'new') {
  		$EventModeStr = "NEW";
  	} else if ($mode == 'copy') {
  		$EventModeStr = "NEW";
  	} else if ($mode == 'edit') {
  		$EventModeStr = "CHANGED";
  	} else {
  		$EventModeStr = "NEW";
  	}

		if ($DebugEnable) {
			echo "<p> -------------------- </p>";
			echo "<pre>mode: " . $mode . "\n" . "</pre>";
		}

		$email_subject = $ShortClubName . " " . $EventModeStr . " EVENT: " .
								date('M j Y', strtotime($formVars["begindate"])) .
					" - " . stripslashes($formVars['name']);

		$eventdetails =
			$EventModeStr . " Event alert!\n\n" .
			"Name:     " . stripslashes($formVars["name"]) . "\n" .
			"Location: " . stripslashes($formVars["location_name"]) . "\n" .
			"Date:     " . date('D, M j Y', strtotime($formVars["begindate"])) . "\n" .
			"Time:     " . date('g:ia', strtotime($formVars["begintime"])) . "\n" .
			stripslashes(strip_tags($formVars["details"])) . "\n\n" .
			"Club: " . stripslashes($ShortClubName) . "\n" .
			"Leader: " . stripslashes($leader_name) . "\n" .
			"Leader Email: " . stripslashes($leader_email) . "\n" .
			"For more information on this event, go to the " . $ShortClubName . " website - \n" .
			"MEMBERS EVENT DETAILS\n" .
			"https://" . PublicDomainName() . "/members/eview.php?event_id=" . $event_id . "\n" .
			"PUBLIC EVENT DETAILS (you can email this link to a friend!)\n" .
			"http://".$_SERVER['HTTP_HOST'] . "/eview-pub.php?event_id=" . $event_id . "\n";

		$EventParentClubCode = EventParentClubCode($event_id);

	while ($row = mysqli_fetch_array($result)) {
		$cust_id = $row['cust_id'];
		$today = date('m/d/Y',time());
		$MembersParentClubCode = $row['m_club'];
		$member_name = GetMemberName($cust_id);
		$member_email = GetMemberEmail($cust_id);
		if (!expired_membership($MemberExpirationDates[$cust_id]))
		{
			// do NOT email MembersOnly notices to members of sister clubs!
			// do NOT email members of sister clubs!
			if ( $MembersParentClubCode == $EventParentClubCode) {
				$email_body = "Dear " . $row["m_firstname"] . " " .
				$row["m_lastname"] .",\n\n";
				$email_body .= $eventdetails;
				$email_body .= "\nTo turn this notification feature off, login to the website \n"
					."click 'My Profile', then uncheck the 'Email on New Event' check box.\n\n"
					."Recess for Adults!\n" . $ClubCompanyName
					. "\n\n"
					. "Member Id: " . stripslashes($cust_id) . "\n"
					. "Member Email: " . stripslashes($row['m_email']) . "\n\n";
					//. "Editors loginUsername: " . $loginUsername . "\n\n";
				$EmailNoticesTo = $row["m_email"];
				$DebugString =
				"<p> -------------------- </p><pre>"
				."event_id: " . $event_id . "\n"
				."To:" . $EmailNoticesTo . "\n"
				."Subject: " . $email_subject . "\n"
				//."\n" . $email_body
				."\n\n"
				."</pre>";
				//echo "Sending gazillion emails...";
				$SendEmail = false;
				if (($mode == 'new') and $row['m_email_on_new_event'])
					$SendEmail = true;
				else if	(($mode == 'copy') and $row['m_email_on_new_event'])
					$SendEmail = true;
				else if (($mode == 'edit') and $row['m_email_on_event_change'])
					//$SendEmail = true;
					$SendEmail = false;

				if ($SendEmail) {
					MailWrapper($EmailNoticesTo,$email_subject,$email_body,$email_headers);
				}
			}
		}
	}
} // end function


function CreateInsertQuery($formVars,$event_id,$leader_id,$coleader_id,$begindate,$begintime,$dateadded,$datechanged) {

	if (!is_numeric($formVars["min_attendees"])) {
		$formVars["min_attendees"] = 0;
	} else {
		$formVars["min_attendees"] = (int) $formVars["min_attendees"];
	}

	if (!is_numeric($formVars["max_attendees"])) {
		$formVars["max_attendees"] = 0;
	} else {
		$formVars["max_attendees"] = (int) $formVars["max_attendees"];
	}

	if (!is_numeric($formVars["e_days_res_chgs"])) {
		$formVars["e_days_res_chgs"] = 0;
	} else {
		$formVars["e_days_res_chgs"] = (int) $formVars["e_days_res_chgs"];
	}

  	$query = "INSERT INTO events ("
	  . " event_id, "
      . " leader_id, "
      . " co_leader_id, "
      . " e_name, "
      . " e_begindate,  "
      . " e_begintime, "
      . " e_details, "
      . " e_details_private, "
      //. " e_deposit, e_deposit_date, e_fullprice, e_fullprice_date, "
      . " e_location_name, "
      . " e_location_meet_at, "
      . " e_driving_directions, "
      . " e_min_attendees, "
      . " e_max_attendees, "
      . " e_contingency_plan, "

      . " e_pmt_descr, "

      . " e_pay4event, "
      . " e_url1, "
      . " e_url2, "
      . " e_url3, "
      . " e_url4, "
      . " e_category, "
      . " e_status, "
      . " e_display, "
      . " e_date_added, "
      . " e_date_changed, "
      . " e_bring, "
      . " e_includes, "
      . " e_leader_notes, "
      . " e_days_res_chgs"

	  . ") values ( "
	  . "'" . $event_id                        . "', "
      . "'" . $leader_id                       . "', "
      . "'" . $coleader_id                       . "', "
      . "'" . $formVars["name"]                . "', "
      . "'" . $begindate                       . "', "
      . "'" . $begintime                       . "', "
      . "'" . $formVars["details"]             . "', "
      . "'" . $formVars["details_private"]     . "', "
      //. "'" . $formVars["e_deposit"]           . "', "
      //. "'" . MySqlDate($formVars["e_deposit_date"])      . "', "
      //. "'" . $formVars["e_fullprice"]         . "', "
      //. "'" . MySqlDate($formVars["e_fullprice_date"])    . "', "
      . "'" . $formVars["location_name"]       . "', "
      . "'" . $formVars["location_meet_at"]    . "', "
      . "'" . $formVars["driving_directions"]  . "', "
      . "'" . $formVars["min_attendees"]       . "', "
  	  . "'" . $formVars["max_attendees"]       . "', "
      . "'" . $formVars["contingency_plan"]    . "', "

      . "'" . $formVars["pmt_descr"]           . "', "

      . "'" . 'Disable'           			   . "', "	//$formVars["pay4event"]
      . "'" . $formVars["url1"]                . "', "
      . "'" . $formVars["url2"]                . "', "
      . "'" . $formVars["url3"]                . "', "
      . "'" . $formVars["url4"]                . "', "
      . "'" . $formVars["category"]            . "', "
      . "'" . $formVars["status"]              . "', "
      . "'" . $formVars["display"]             . "', "
      . "'" . $dateadded                       . "', "
      . "'" . $datechanged                     . "' ,"
      . "'" . $formVars["bring"]               . "', "
      . "'" . $formVars["includes"]            . "', "
      . "'" . $formVars["leader_notes"]        . "', "
      . "'" . $formVars["e_days_res_chgs"]     . "' "
      . " ) ";

	return($query);
}  // end insert statement


function CreateUpdateQuery($formVars,$event_id,$leader_id,$coleader_id,$begindate,$begintime,$dateadded,$datechanged) {

	if (!is_numeric($formVars["min_attendees"])) {
		$formVars["min_attendees"] = 0;
	} else {
		$formVars["min_attendees"] = (int) $formVars["min_attendees"];
	}

	if (!is_numeric($formVars["max_attendees"])) {
		$formVars["max_attendees"] = 0;
	} else {
		$formVars["max_attendees"] = (int) $formVars["max_attendees"];
	}

	if (!is_numeric($formVars["e_days_res_chgs"])) {
		$formVars["e_days_res_chgs"] = 0;
	} else {
		$formVars["e_days_res_chgs"] = (int) $formVars["e_days_res_chgs"];
	}

    $query = "UPDATE events SET " .
      "leader_id='" . $leader_id . "', " .
      "co_leader_id='" . $coleader_id . "', " .
      "e_name='" . $formVars["name"] . "', " .
      "e_begindate='" . $begindate . "', " .
  		"e_begintime='" . $begintime . "', " .
      "e_details='" . quotesqldata($formVars["details"]) . "', " .
      "e_details_private='" . quotesqldata($formVars["details_private"]) . "', " .
      "e_fullprice='" . $formVars["e_fullprice"] . "', " .
      "e_fullprice_date='" . MySqlDate($formVars["e_fullprice_date"]) . "', " .
      "e_deposit='" . $formVars["e_deposit"] . "', " .
      "e_deposit_date='" . MySqlDate($formVars["e_deposit_date"]) . "', " .
      "e_location_name='" . $formVars["location_name"] . "', " .
      "e_location_meet_at='" . $formVars["location_meet_at"] . "', " .
      "e_driving_directions='" . $formVars["driving_directions"] . "', " .
      "e_min_attendees='" . $formVars["min_attendees"] . "', " .
      "e_max_attendees='" . $formVars["max_attendees"] . "', " .
      "e_contingency_plan='" . $formVars["contingency_plan"] . "', " .
      "e_url1='" . $formVars["url1"] . "', " .
      "e_url2='" . $formVars["url2"] . "', " .
      "e_url3='" . $formVars["url3"] . "', " .
      "e_url4='" . $formVars["url4"] . "', " .
      "e_category='" . $formVars["category"] . "', " .
      "e_status='" . $formVars["status"] . "', " .
      "e_display='" . $formVars["display"] . "', " .

      "e_pmt_descr='" . $formVars["pmt_descr"] . "', " .

      "e_pay4event='" . 'Disable' . "', " .	// $formVars["pay4event"]
      "e_date_changed='" . $datechanged . "' ," .
      "e_bring='" . $formVars["bring"] . "', " .
      "e_includes='" . $formVars["includes"] . "', " .
      "e_leader_notes='" . $formVars["leader_notes"] . "', " .
      "e_days_res_chgs='". $formVars["e_days_res_chgs"] . "' " .
      "WHERE (event_id='" . $event_id . "');";
	 // end update statement
	 
	 return($query);
}

function WriteEventDataToDatabase($query) {
	global $connection;

	// Run the query on the event table
    //if (!($row = @ mysqli_query ($connection, $query)))    {
    if (!($row = mysqli_query ($connection, $query)))    {
		trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    	exit;
  	}
}
  

function ComputeValueDifferences($formVars,$query,$leader_id,$coleader_id,$begindate,$begintime) {
	global $connection;

	if (!($result = @ mysqli_query($connection, $query)))
	  trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
	$oldrow = mysqli_fetch_array($result);
	$diff = "";
	$oldvalue = $oldrow["leader_id"];
	if ($oldvalue != $leader_id)
		$diff .= "leader_id was " . $oldvalue . " changed to " . $leader_id . "\n";

		$oldvalue = $oldrow["co_leader_id"];
	if ($oldvalue != $coleader_id)
		$diff .= "coleader_id was " . $oldvalue . " changed to " . $coleader_id . "\n";

		$oldvalue = $oldrow["e_name"];
	if ($oldvalue != $formVars["name"])
		$diff .= "Event name was " . $oldvalue . " changed to " . $formVars["name"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_begindate"]);
	if ($oldvalue != $begindate)
		$diff .= "Begin date was " . $oldvalue . " changed to " . $begindate . "\n";
		$oldvalue = quotesqldata($oldrow["e_begintime"]);
	if ($oldvalue != $begintime)
		$diff .= "Begin time was " . $oldvalue . " changed to " . $begintime . "\n";
		$oldvalue = quotesqldata($oldrow["e_details"]);
	if ($oldvalue != $formVars["details"])
		$diff .= "Details was " . $oldvalue . "\n changed to \n" . $formVars["details"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_details_private"]);
	if ($oldvalue != $formVars["details_private"])
		$diff .= "Private Details was " . $oldvalue . "\n changed to \n" . $formVars["details_private"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_fullprice"]);
	if ($oldvalue != $formVars["e_fullprice"])
		$diff .= "Cost was " . $oldvalue . " changed to " . $formVars["e_fullprice"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_fullprice_date"]);
	if ($oldvalue != $formVars["e_fullprice_date"])
		$diff .= "Cost date was " . $oldvalue . " changed to " . $formVars["e_fullprice_date"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_deposit"]);
	if ($oldvalue != $formVars["e_deposit"])
		$diff .= "Deposit was " . $oldvalue . " changed to " . $formVars["e_deposit"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_deposit_date"]);
	if ($oldvalue != $formVars["e_deposit_date"])
		$diff .= "Deposit date was " . $oldvalue . " changed to " . $formVars["e_deposit_date"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_location_name"]);
	if ($oldvalue != $formVars["location_name"])
		$diff .= "Location was " . $oldvalue . " changed to " . $formVars["location_name"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_location_meet_at"]);
	if ($oldvalue != $formVars["location_meet_at"])
		$diff .= "Meet at Location was " . $oldvalue . " changed to " . $formVars["location_meet_at"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_driving_directions"]);
	if ($oldvalue != $formVars["driving_directions"])
		$diff .= "Meet at Location was " . $oldvalue . " changed to " . $formVars["driving_directions"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_min_attendees"]);
	if ($oldvalue != $formVars["min_attendees"])
		$diff .= "Min attendees was " . $oldvalue . " changed to " . $formVars["min_attendees"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_max_attendees"]);
	if ($oldvalue != $formVars["max_attendees"])
		$diff .= "Max attendees was " . $oldvalue . " changed to " . $formVars["max_attendees"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_contingency_plan"]);
	if ($oldvalue != $formVars["contingency_plan"])
		$diff .= "Contingency plan was " . $oldvalue . "\n changed to \n" . $formVars["contingency_plan"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_url1"]);
	if ($oldvalue != $formVars["url1"])
		$diff .= "url1 was " . $oldvalue . " changed to " . $formVars["url1"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_url2"]);
	if ($oldvalue != $formVars["url2"])
		$diff .= "url2 was " . $oldvalue . " changed to " . $formVars["url2"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_url3"]);
	if ($oldvalue != $formVars["url3"])
		$diff .= "url3 was " . $oldvalue . " changed to " . $formVars["url3"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_url4"]);
	if ($oldvalue != $formVars["url4"])
		$diff .= "url4 was " . $oldvalue . " changed to " . $formVars["url4"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_category"]);
	if ($oldvalue != $formVars["category"])
		$diff .= "category was " . $oldvalue . " changed to " . $formVars["category"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_status"]);
	if ($oldvalue != $formVars["status"])
		$diff .= "status was " . $oldvalue . " changed to " . $formVars["status"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_display"]);
	if ($oldvalue != $formVars["display"])
		$diff .= "display was " . $oldvalue . " changed to " . $formVars["display"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_pay4event"]);
	if ($oldvalue != $formVars["pay4event"])
		$diff .= "pay4event was " . $oldvalue . " changed to " . $formVars["pay4event"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_bring"]);
	if ($oldvalue != $formVars["bring"])
		$diff .= "bring was " . $oldvalue . " changed to " . $formVars["bring"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_includes"]);
	if ($oldvalue != $formVars["includes"])
		$diff .= "includes was " . $oldvalue . " changed to " . $formVars["includes"] . "\n";
		$oldvalue = quotesqldata($oldrow["e_leader_notes"]);
	if ($oldvalue != $formVars["leader_notes"])
		$diff .= "leader_notes was " . $oldvalue . "\n changed to \n" . $formVars["leader_notes"] . "\n";

	return($diff);
}
		

//=============================================
// validate form variables
function ValidateFormVariables($formVars) {

	$errors = array();

	// Validate the event Name
	if (empty($formVars["name"]))
		$errors["name"] = "The event name field cannot be blank.";

	// Validate the event location name
	if (empty($formVars["location_name"]))
		$errors["location_name"] = "The event location name field cannot be blank.";

	// Validate begin date
	if (empty($formVars["begindate"]))
		$errors["begindate"] = "You must supply an event begin date.";
	else if (!preg_match("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#", $formVars["begindate"], $parts))
		// Check the format
		$errors["begindate"] = "The begin date is not a valid date in the format MM/DD/YYYY";
	else if (!checkdate($parts[1],$parts[2],$parts[3]))
		  $errors["begindate"] = "Invalid begin date. Month must be " .
			 "between 1 and 12, day must be valid for that month.";
	else {
		// assemble the date into database format
	   $begindate = " \"$parts[3]-$parts[2]-$parts[1]\"";
	}

	// Validate begin time
	if (empty($formVars["timehours"]) || empty($formVars["timeminutes"]))
		$errors["begintime"] = "You must supply an event begin time.";
	else{
		$hrs = $formVars["timehours"];
	if ($formVars["ampm"] == "AM"){
		if ($hrs == 12)
			$hrs = 0;
	} else {
		if ($hrs != 12)
			$hrs += 12;
	}
	$formVars["begintime"] = $hrs . ":" . $formVars["timeminutes"];
	if (!preg_match("#([0-9]{1,2}):([0-9]{1,2})#", $formVars["begintime"], $parts))
		// Check the format
		$errors["begintime"] = "The begin time is not a valid time";
	}

	// Validate the details
	if (empty($formVars["details"]))
		$errors["details"] = "The details field cannot be blank.";

	// Validate the details
	if (empty($formVars["details_private"]))
		$errors["details_private"] = "The private details field cannot be blank.";

	// Validate min attendees
	if (!empty($formVars["min_attendees"]) && !preg_match("#([0-9]{1,3})#", $formVars["min_attendees"], $parts))
		$errors["min_attendees"] = "The Min Attendees value is not a number, 0 - 999.";

	// Validate max attendees
	if (!isset($formVars["max_attendees"]))
		$errors["max_attendees"] =
		"You must specify the Maximum number of Attendees.";
	else if(  !preg_match("#([0-9]{1,3})#", $formVars["max_attendees"], $parts))
		// Check the format
		$errors["max_attendees"] = "The Max Attendees value is not a number, 0 - 999.";

	// Validate the details
	if (strlen($formVars["pmt_descr"]) > 60)
		$errors["pmt_descr"] = "The Cost Comments field must be less than 60 characters.";

	if (strlen($formVars["bring"]) > 254)
	$errors["pmt_descr"] = "The Bring with you field must be less than 254 characters.";

	if (strlen($formVars["includes"]) > 254)
	$errors["pmt_descr"] = "The Events includes field must be less than 254 characters.";

	if (strlen($formVars["contingency_plan"]) > 254)
	$errors["pmt_descr"] = "The Contingency plan field must be less than 60 characters.";

	if (strlen($formVars["leader_notes"]) > 254)
	$errors["pmt_descr"] = "The Leader Notes field must be less than 254 characters.";


	return($errors);
}


//===========================================================================
//// code starts here ////

// obtain member information from members table
$loginUsername = LoginUsername();
LogMsg('EEDIT-POST got loginUsername: '.$loginUsername);

$cust_id = getCustomerID($loginUsername);
LogMsg('EEDIT-POST got cust_id: '.$cust_id);

$formVars = RetrievePostVars();
LogMsg('EEDIT-POST got formVars');

//$formVars = RetrieveSessionFormVars();
//$errors = RetrieveSessionErrors();
//$aGet = RetrieveGetVars();

$event_id = $formVars["event_id"];
$mode = $formVars["mode"];
//DebugLogMsg('$event_id',$event_id);
//DebugLogMsg('$mode',$mode);
//DebugLogMsg('$formVars',$formVars);

LogMsg('incoming $EVENT_ID: '.$event_id.'   $MODE: '.$mode
	.'   $LOGINUSERNAME: '.$loginUsername);
//LogMsg('FORMVARS: '.print_r($formVars,true));

// check the mode (new, edit, copy)
switch ($mode)	{
    case 'new':
      break;
    case 'edit':
      if (empty($event_id))
    	{
          $errors["event_id"] = "Event Id must exist";
          trigger_error($errors["event_id"], E_USER_ERROR);
      }
      break;
    case 'copy':
      break;
    default:
      trigger_error('Error: unknown mode!', E_USER_ERROR);
}

$errors = ValidateFormVariables($formVars);
//LogMsg('$errors AFTER VALIDATE: ',$errors);

// check if any form errors were detected by the validation code
if (count($errors) > 0)  {
	// There are errors.  strip slashes from fields
	foreach ($formVars as $varname => $value)	{
		$formVars[$varname] = stripslashes($value);
	}

	$_SESSION['errors'] = $errors;
	$_SESSION['formVars'] = $formVars;

	// Relocate back to the client form
	// indicate invalid form data in get variable
	$loc = "Location: "
		."/members/eedit.php"
		."?event_id=" . $event_id
		.'&invalid=' . 'true'
		;
	LogMsg('count($errors) > zero for $event_id: '.$event_id
		.'    $loginUsername: '.$loginUsername.'  $errors: '.print_r($errors,true));
	LogMsg('REDIRECTING to header($loc): '.$loc);
	header($loc);
	exit;
}

// Validate the cost
$e_deposit_date = "";
$e_deposit = 0;
$e_fullprice_date = "";
$pay4event = "Disable";
$e_fullpice = 0;
$amount = "";

// If we made it here, then the data is valid
if (!isset($connection))  {
	mysqlconnect($connection);
}

// prep all form data for use in query
$begindate      = MySqlDate($formVars["begindate"]);

$hrs = $formVars["timehours"];
if ($formVars["ampm"] == "AM"){
	if ($hrs == 12)
		$hrs = 0;
} else {
	if ($hrs != 12)
		$hrs += 12;
}
$formVars["begintime"] = $hrs . ":" . $formVars["timeminutes"];
$begintime      = $formVars["begintime"];

$enddate        = "";
$canceldate     = "";
$rescheduledate = "";
$datechanged    = date('Y-m-d',time());
$dateadded      = date('Y-m-d',time());
$loginUsername = LoginUsername();
$cust_id = getCustomerID($loginUsername);

//if ($mode == 'copy') {
//	$leader_id = $cust_id;
//	$coleader_id = $cust_id;
//} else {
	$leader_id = isset($_POST["leader_id"]) ?
		quotesqldata($_POST["leader_id"]) : $cust_id;
	$coleader_id = isset($_POST["coleader_id"]) ?
		quotesqldata($_POST["coleader_id"]) : $cust_id;
//}
$formVars["name"]          		= quotesqldata($formVars["name"]);
$begindate                 		= quotesqldata($begindate);
$begintime                 		= quotesqldata($begintime);
$enddate                   		= quotesqldata($enddate);
$canceldate                		= quotesqldata($canceldate);
$rescheduledate            		= quotesqldata($rescheduledate);
$formVars["details"]       		= quotesqldata($formVars["details"]);
$formVars["details_private"]    = quotesqldata($formVars["details_private"]);
$formVars["location_name"] 		= quotesqldata($formVars["location_name"]);
$formVars["location_meet_at"]  	= quotesqldata($formVars["location_meet_at"]);
$formVars["driving_directions"]  = quotesqldata($formVars["driving_directions"]);
$formVars["min_attendees"] 		= quotesqldata($formVars["min_attendees"]);
$formVars["max_attendees"] 		= quotesqldata($formVars["max_attendees"]);
$formVars["contingency_plan"]  	= quotesqldata($formVars["contingency_plan"]);
$formVars["url1"]          		= quotesqldata($formVars["url1"]);
$formVars["url2"]          		= quotesqldata($formVars["url2"]);
$formVars["url3"]          		= quotesqldata($formVars["url3"]);
$formVars["url4"]          		= quotesqldata($formVars["url4"]);
$formVars["category"]      		= quotesqldata($formVars["category"]);
if ($formVars["status"] == "Cancelled")
	$formVars["status"] = "Canceled";
$formVars["status"]        		= quotesqldata($formVars["status"]);
$formVars["display"]       		= quotesqldata($formVars["display"]);

$formVars["pay4event"]     		= "Disable";
$formVars["e_fullprice"]   		= "0.00";
$formVars["e_fullprice_date"]	= "1000-01-01 00:00:00";
$formVars["e_deposit"]     		= "0.00";
$formVars["e_deposit_date"] 	= "1000-01-01 00:00:00";

$datechanged               = quotesqldata($datechanged);
$formVars["bring"]         = quotesqldata($formVars["bring"]);
$formVars["includes"]      = quotesqldata($formVars["includes"]);
$formVars["leader_notes"]  = quotesqldata($formVars["leader_notes"]);


if (($mode == "new") or ($mode == 'copy'))	{
	// no existing record, so insert a new one!
	// get next event_id for this club
	//echo "Executing GetNextEventIDFromDatabase<br>";
	$event_id = GetNextEventIdFromDatabase($connection);
	$query = CreateInsertQuery($formVars,$event_id,$leader_id,$coleader_id,$begindate,$begintime,$dateadded,$datechanged);
} else if ($mode == 'edit')	{
	//$query = "SELECT * FROM events WHERE (event_id='" . $event_id . "');";
	//$diff = ComputeValueDifferences($formVars,$query,$leader_id,$coleader_id,$begindate,$begintime);
	$query = CreateUpdateQuery($formVars,$event_id,$leader_id,$coleader_id,$begindate,$begintime,$dateadded,$datechanged);
}
LogMsg("SQL query: ".$query);	
LogMsg("Writing event to database");	
WriteEventDataToDatabase($query);

// if bogus event_id, display the event list, else view the event
if (($event_id < 0)) {
	$loc = "Location: /members/elist.php";
	EmailDeveloper("ERROR EEDIT-POST.PHP invalid event_id: ".$event_id.'EVENT NAME: '.$formVars['name'],
		'EVENT NAME: '.$formVars['name']
		."\n"
		.'  $leader_id  '.$loginUsername);
	trigger_error("eedit-post event_id less than zero!: ". print_r($event_id,true), E_NOTICE);
} else {
	if (isset($debug) && $debug) {
		$DebugEnable = $debug;
	} else {
		$DebugEnable = false;
	}
	if (($loginUsername == 'chucktest')) {
		LogMsg("Sending notification email to developer");	
		// email new / changed event notice to club members
		EmailDeveloper('ACG testing, emailing developer', 'Current loginUsername: '.$loginUsername);
	} else {
		LogMsg("Sending notification email to club members");	
		// email new / changed event notice to club members
		EmailNewEventNotice($event_id,$mode,$DebugEnable);
	}
}
$loc = "Location: /members/eview.php?event_id=" . $event_id;

// Clear the formVars so a future <form> is blank
SessionUnregister("formVars");
SessionUnregister("errors");

//LogMsg('WROTE data to database for $event_id: '.$event_id.'    $loginUsername: '.$loginUsername.'  $QUERY: '.$query);
//LogMsg('REDIRECTING to header($loc): '.$loc);
// redirect to appropriate url
header($loc);
exit;
