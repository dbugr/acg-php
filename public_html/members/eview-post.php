<?php
/*

AdventureClub.Info, Inc.

Take event data from eview.php and write to database.

*/

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


  // Show an error in a red font
  function fieldError($fieldName, $errors)
  {
    if (isset($errors[$fieldName]))
       echo "<font color=\"red\">" .
           $errors[$fieldName] .
           "</font><br>";
  }


  // Is the user logged in and were there no errors from a previous
  // validation?  If so, look up the customer for editing
  // Is the user logged in?
  if (!SessionIsRegistered("loginUsername"))
  {
     // Register a message to show the user
     $message = "Error: you are not logged in!";
     SessionRegister("message",$message);

     // Register where they came from
     $referer = $_SERVER['PHP_SELF'];
     SessionRegister("referer",$message);

     // redirect to the login page
     $loc = "Location: /login.php";
     header($loc);
     exit;
  }

  // setup database connections
	mysqlconnect($connection);
  $ClubCompanyName	  = GetParameter('ClubCompanyName');
  $ShortClubName 		  = GetParameter('ShortClubName');
  $EmailNoticesTo 		= GetParameter('EmailNoticesTo');
  $EmailNoticesFrom 	= GetParameter('EmailNoticesFrom');
  $AllowNoComments 	  = GetParameter('AllowNoComments');


  // Given a cust_id and event_id, search for
  // registration (sign-up) record in
  // reserve table
  // if found, return it, otherwise return Null
	function GetReserveRecord($cust_id, $event_id)
	{
  	global $connection;

  	$query1 = "SELECT * FROM reserve
          where ((cust_id='" . quotesqldata($cust_id). "') AND (event_id='" . quotesqldata($event_id) . "'));";
    if (!($result1 = @ mysqli_query($connection, $query1)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    $reserverow = mysqli_fetch_array($result1);
    return($reserverow);
	}

  // count number of people who who are signed up
  // for a particular event, including guests,
  // excluding the current member and his/her guests.
	function yes_count_exclude_current_member($event_id, $cust_id)
	{
  	global $connection;

    $query1 = "SELECT event_id, cust_id, r_attending, r_num_guests FROM reserve
            WHERE (event_id='" . quotesqldata($event_id) . "');";
    if (!($result1 = @ mysqli_query($connection, $query1)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);

    $yes_count = 0;
    while ($reservation_row = mysqli_fetch_array($result1) )
		{
    	if (($reservation_row['r_attending'] == 'Yes') and
			    ($cust_id != $reservation_row['cust_id']))
			{
        $yes_count += 1;
        $yes_count += (int) $reservation_row['r_num_guests'];
      }
    }
    return($yes_count);
	}

  // count number of people who who are signed up
  // for a particular event, including guests,
  // excluding the current member and his/her guests.
	function yes_count($event_id)
	{
  	global $connection;

    $query1 = "SELECT event_id, cust_id, r_attending, r_num_guests FROM reserve
               WHERE (event_id='" . quotesqldata($event_id) . "');";
    if (!($result1 = @ mysqli_query($connection, $query1)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);

    $yes_count = 0;
    while ($reservation_row = mysqli_fetch_array($result1) )
		{
      if (($reservation_row['r_attending'] == 'Yes'))
  		{
        $yes_count += 1;
        $yes_count += (int) $reservation_row['r_num_guests'];
      }
    }
    return($yes_count);
	}

  // Given a cust_id, obtain and return
  // the customer first and last name as a string
	function GetMemberName($cust_id)
	{
		global $connection;

    $name = "Unknown";
   	$query1 = "SELECT * FROM members WHERE cust_id ='" . $cust_id . "'";
   	if (!($result1 = @ mysqli_query($connection, $query1)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
   	if (($memberrow = mysqli_fetch_array($result1)))
      $name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
    return($name);
	}

  // Given a cust_id, obtain and return
  // the customer email address as a string
  function GetMemberEmail($cust_id)
  {
  	global $connection;

    $email = "Unknown";
   	$query1 = "SELECT * FROM members WHERE cust_id ='" . $cust_id . "'";
    if (!($result1 = @ mysqli_query($connection, $query1)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);
    if (($memberrow = mysqli_fetch_array($result1)))
     $email = $memberrow["m_email"];
  	return($email);
	}

  // Given an event_id, search for
  // registration (sign-up) record in
  // reserve table
  // if found, return it, otherwise return Null
	function GetEventRecord($event_id)
	{
  	global $connection;

    $query1 = "SELECT * FROM events
               WHERE (event_id='" . quotesqldata($event_id) . "');";
    if (!($result1 = @ mysqli_query($connection, $query1)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    $event_row = mysqli_fetch_array($result1);
    return($event_row);
	}

	if (!SessionIsRegistered("formVars"))
   SessionRegister("formVars",array());

	foreach($_POST as $varname => $value)
   	$formVars[$varname] = clean($value);
  if( isset( $_GET))
  {
  	foreach($_GET as $varname => $value)
     	$formVars[$varname] = clean($value);
  }

  $errors = array();


	// fix up
	if (!isset($formVars['attending']))
		$formVars['attending'] = "";
	if ($formVars['attending'] == 'No')
	{
		$formVars['attending'] = 'Comments';
		$formVars["guests"]	= 0;
		if (!$AllowNoComments)
		{
			$formVars["comments"] = "";
		}
	}

  // obtain member cust_id from members table
  $loginUsername = LoginUsername();
  $cust_id = getCustomerID($loginUsername, $connection);
  if (!isset($formVars['event_id']))
	{
  	trigger_error("Invalid event_id, User: " . $loginUsername . "\n", E_USER_ERROR);
    exit;
  }

  $event_id = $formVars['event_id'];

  $reserverow= GetReserveRecord($cust_id, $event_id);

  // replace single quotes with double quotes to thwart user attacks
  $cust_id                  = quotesqldata($cust_id);
  $formVars["event_id"]     = quotesqldata($formVars["event_id"]);
  $formVars["attending"]    = quotesqldata($formVars["attending"]);
  $formVars["comments"]     = quotesqldata($formVars["comments"]);

	$guests = 0;
  if (!isset( $formVars["guests"])) {
	  $formVars["guests"] = 0;
		$guests = 0;
  } else if (!is_numeric($formVars["guests"])) {
	  $formVars["guests"] = 0;
		$guests = 0;
	} else {
    $formVars["guests"]       = quotesqldata($formVars["guests"]);
    $validGuestsExpr = preg_match("#[0-9 ]{0,2}#",$formVars["guests"]);
    $formVars["guests"] = $validGuestsExpr ? $formVars["guests"] : 0;
		$formVars["guests"] = (int) $formVars["guests"];
		$guests = (int) $formVars["guests"];
  }
  $event_row= GetEventRecord($event_id);
  $max_attendees = (int) $event_row['e_max_attendees'];

  $yes_count = yes_count_exclude_current_member($event_id,$cust_id);

  if (!SessionIsRegistered("errors"))
	  SessionRegister("errors",array());
  $errors = array();

  if (!isset($formVars['liabilitywaiver']))
  	$errors['deny_signups'] =
            'To sign up for an event you MUST accept<br>'
           .'the Policies and Liability Waiver.<br>';

  $r_attending = isset($reserverow['r_attending']) ? $reserverow['r_attending'] : "";
  $attending = isset($formVars['attending']) ? $formVars['attending'] : "";

  if (((( $r_attending != $attending) and
       (($r_attending != 'Yes') and
			 ($attending == 'Yes'))) or
      (( $r_attending == $attending) and
       (($r_attending == 'Yes')))) and
     (((int)$yes_count + ($formVars['guests'] + 1) ) > $max_attendees))
	{
    $errors['deny_signups'] =
        'Your registration request exceeds the maximum permitted '
        .'attendees for this event. '
        .'Sorry! &nbsp;'
        .'To sign-up on the waiting list, click <a href="/members/eview.php?event_id=' . $event_id .'">here<a>.<br>';
  }

  if (!empty($errors))
	{
	$_SESSION["errors"] = $errors;
	$_SESSION["formVars"] = $formVars;
  	$loc = "Location: /members/errormsg.php?event_id=" . $event_id;
    header($loc);
    exit;
	}

	// if already paid, previous people greater than current people
	// and past date(s) give warning
	$droppingpeople = false;
	if (!isset( $reserverow['r_num_guests'])) {
		$r_num_guests = 0;
	} else {
		$r_num_guests = $reserverow['r_num_guests'];
		$previouspeople = (int) $reserverow['r_num_guests'] + 1;
		$droppingpeople = ($previouspeople > ($guests+1));
	}
	if ($formVars['attending'] != 'Yes')
		$droppingpeople = true;

	if (isset( $reserverow['r_amount_paid']) and !isset($formVars['override']))
	{
		if (($reserverow['r_amount_paid'] > 0) and ($droppingpeople))
		{
    	// Warning if past deposit date
    	if (isset($event_row['e_deposit_date']))
    	{
    		$d1 = date('y-m-d',strtotime($event_row['e_deposit_date']));
    		$d2 = date('y-m-d',time());
        $errors['deny_signups'] = 'You are lowering the number of people signed up for this event.<br>';

    		if (strcmp($d1, $d2) < 0)
    		{
        	$errors['deny_signups'] .=  'This is occurring after payments have been paid to the vendor(s).<br>' .
              'Previous payments can not be refunded.  Sorry! <br>';
        }
        else
        {
        	$errors['deny_signups'] .=  'Previous payments may be refunded.  <br>';
        }
				$errors['deny_signups'] .= 'To cancel this request, click <a href="/members/eview.php?event_id=' . $event_id .'">here<a>.<br>' .
							'To continue processing, click <a href="/members/eview-post.php?override=yes&liabilitywaiver=yes">here<a>.<br>';
      	$loc = "Location: /members/errormsg.php?event_id=" . $event_id;
        header($loc);
        exit;
    	}

    	// Warning if past fullprice date
    	if (isset($event_row['e_fullprice_date']))
    	{
    		$d1 = date('y-m-d',strtotime($event_row['e_fullprice_date']));
        $errors['deny_signups'] = 'You are lowering the number of people signed up for this event.<br>';

    		if (strcmp($d1, $d2) < 0)
    		{
        	$errors['deny_signups'] .=  'This is occurring after payments have been paid to the vendor(s).<br>' .
              'Previous payments can not be refunded.  Sorry! <br>';
        }
        else
        {
        	$errors['deny_signups'] .=  'Previous payments may be refunded.  <br>';
        }
				$errors['deny_signups'] .= 'To cancel this request, click <a href="/members/eview.php?event_id=' . $event_id .'">here<a>.<br>' .
							'To continue processing, click <a href="/members/eview-post.php?override=yes&liabilitywaiver=yes">here<a>.<br>';
      	$loc = "Location: /members/errormsg.php?event_id=" . $event_id;
        header($loc);
        exit;
    	}
		}
	}
	//
  // today's date
  $date_time = date('Y-m-d H:i:s', time());
  $date_reserved = "";
  // if signup was changed ("yes" to "maybe", etc), change the reservation date
  $fv_attending = isset($formVars["attending"]) ? rtrim($formVars["attending"]) : "";
  $r_attending = isset($reserverow["r_attending"]) ? rtrim($reserverow["r_attending"]) : "";
  if ( $r_attending != $fv_attending ) {
  	$date_reserved = date('Y-m-d H:i:s', time());
  }
  if ($reserverow)
	{
    // update current record
    $query = "UPDATE reserve SET r_attending = '" . $fv_attending ."'";
    if (isset($formVars["comments"]))
    	$query .= ", r_comments='" . $formVars["comments"]. "'";
    if (isset($formVars["guests"]))
    	$query .= ", r_num_guests='"  .$guests. "'";
    if (!empty($date_reserved))
    	$query .= ", r_date_reserved='"  .$date_reserved. "'";
    $query .= " WHERE ((cust_id='" . $cust_id . "') AND (event_id='" . $event_id . "'));";
  }
  else
  {
    // insert a new record
    $query = "INSERT INTO reserve (cust_id,event_id,r_attending";
    if ($formVars["comments"])
	    $query .= ",r_comments";
    if ($formVars["guests"])
      $query .=  ", r_num_guests";
    $query .= ",r_date_reserved";
    $query .= ") VALUES ('" .$cust_id . "', '" .$event_id . "', '" .$fv_attending . "'";
    if ($formVars["comments"])
    	$query .=  ", '" . $formVars["comments"] ."' ";
    if ($formVars["guests"])
      $query .=  ", '"  .$guests. "'";
    $query .= ", '"  .$date_time. "')";
	}
  if (!($result = @ mysqli_query($connection, $query)))
  {
  	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);
	}
  $email_from =
	  "From: " . $EmailNoticesFrom ."\n" .
   	"Reply-To: " . $EmailNoticesFrom ."\n" .
    "Return-Path: " . $EmailNoticesFrom ."\n" .
    "X-Mailer: PHP/" . phpversion();

  $leader_id = getLeaderID($event_id,false);
  $leader_email = GetMemberEmail($leader_id);

  $email_subject = $ShortClubName . " Event FULL event_id=".$event_id;
  $email_body =
    $email_subject."\n"
    ."Event Name:   ".$event_row['e_name']."\n"
    ."\n"
    ."Begin Date:   ".$event_row['e_begindate']."\n"
    ."Begin Time:   ".$event_row['e_begintime']."\n"
    ."Leader Name:  ".GetMemberName($leader_id)."\n"
    ."Leader email: ".$leader_email."\n"
    ."\n"
    ."Public event view\n"
	
    ."http://".$_SERVER['HTTP_HOST']."/eview-pub.php?event_id="
    . $event_id."\n"
    ."http://".$_SERVER['HTTP_HOST']."/elist-pub.php"."\n"
    ."\n"
    ."Members only event view\n"
    ."http://".$_SERVER['HTTP_HOST']."/members/eview.php?event_id="
    . $event_id."\n"
    ."http://".$_SERVER['HTTP_HOST']."/members/elist.php"."\n"
    ."\n"
    ;

  $yes_count = yes_count($event_id);
  if ($yes_count == $max_attendees)
    MailWrapper($leader_email,$email_subject,$email_body,$email_from);

	// notify event leaders of reservation changes
  else if ( isset($event_row['e_days_res_chgs'])
  	   and ($event_row['e_days_res_chgs'] > 0)
  )
  {
  	// are we within N days of event
  	$eventtime = strtotime( $event_row['e_begindate']);
  	$alerttime = time() + $event_row['e_days_res_chgs'] * 3600 * 24;
  	$attending = $formVars["attending"];
  	if ($attending == "Comments")
  		$attending = "No";
		if (isset( $notified))
		{

			// already notified?
			if ($notified == $attending)
			{
			    $eventtime = $alerttime+1;
			}
		}
  	if ((float)$eventtime < (float)$alerttime)
  	{
      $email_subject = $ShortClubName . " Event reservation change";
      $email_body =
        $email_subject . "\n\n" .
        "Member:       " . GetMemberName($cust_id) . "\n" .
        "Attending:    " . $attending . "\n" .
        "Member email: " . GetMemberEmail($cust_id) . "\n" .
        "Event Name:   " . $event_row['e_name'] . "\n" .
        "Begin Date:   " . $event_row['e_begindate'] . "\n" .
        "Begin Time:   " . $event_row['e_begintime'] . "\n" .
        "\n\n" .
        "Public event view\n" .
        "http://".$_SERVER['HTTP_HOST']."/eview-pub.php?event_id=" . $event_id . "\n" .
        "http://".$_SERVER['HTTP_HOST']."/elist-pub.php" . "\n\n" .
        "Members only event view\n" .
        "http://".$_SERVER['HTTP_HOST']."/members/eview.php?event_id=" . $event_id . "\n" .
        "http://".$_SERVER['HTTP_HOST']."/members/elist.php" . "\n\n";
      MailWrapper($leader_email,$email_subject,$email_body,$email_from);
			$notified = $attending;
			SessionRegister("notified",$notified);
    }
  }

  $loc = "Location: /members/eview.php?event_id=" . $event_id;
  $loc .= "&yes_count=" . $yes_count;
  header($loc);
