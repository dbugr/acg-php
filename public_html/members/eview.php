<?php

// given an event_id, allow user to see event
// details and who is signed up.
// permit them to sign up for the event.

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$border = 0;
$FullTableWidth = "100%";
$HalfTableWidth = "250";


function GoogleCalendarLink($eVars, $formVars)
{

  $BeginDate = str_replace('-', '', $eVars['RawBeginDate']);
  $BeginTime = str_replace(':', '', $eVars['RawBeginTime']);
  $EndDate = $BeginDate;
  $EndTime = str_replace(':', '', $eVars['RawBeginTime']);
  $hour = substr($EndTime, 0, 2);
  $hour = (string) ($hour + 1);
  if (strlen($hour) < 2) {
    $hour = '0' . $hour;
  }
  $EndTime = substr_replace($EndTime, $hour, 0, 2);
  $DateValue =
    $BeginDate
    . 'T'
    . $BeginTime
    . '/'
    . $EndDate
    . 'T'
    . $EndTime;

  $aVars = array(
    'action' => 'TEMPLATE',
    'hl' => 'en',
    'text' => $formVars['name'],
    'dates' => $DateValue,
    'location' => $formVars['location_name'],
    'details' => $formVars['details'],
  );
  $query_string = http_build_query($aVars);
  $url = 'https://www.google.com/calendar/event?' . $query_string;

  return ($url);
}


// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
}

// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberData($cust_id)
{
  global $connection;

  $member_row = array();
  $member_row[] = "Unknown";

  $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";

  if (!($result1 = @mysqli_query($connection, $query1)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  if (!($member_row = mysqli_fetch_array($result1))) {
  }
  return ($member_row);
}

// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
  global $connection;

  $name = "Unknown";
  // discard $ClubCode ('gnv_', 'cfac_', etc)
  $CustIdStartsAt = strpos($cust_id, '_') + 1;
  $NumericCust_Id = substr($cust_id, $CustIdStartsAt, 14);
  if (!empty($NumericCust_Id) and !is_null($NumericCust_Id)) {
    $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";
    if (!($result1 = @mysqli_query($connection, $query1)))
      trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
    if (($memberrow = mysqli_fetch_array($result1)))
      $name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
  }
  return ($name);
}

// Given a cust_id, obtain and return
// the customer email address as a string
function GetMemberEmail($cust_id)
{
  global $connection;

  $email = "Unknown";
  //$email = $cust_id;
  // discard $ClubCode ('gnv_', 'cfac_', etc)
  $CustIdStartsAt = strpos($cust_id, '_') + 1;
  $NumericCust_Id = substr($cust_id, $CustIdStartsAt, 14);
  if (!empty($NumericCust_Id)  and  !is_null($NumericCust_Id)) {
    if ($NumericCust_Id >= 0) {
      $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";

      if (!($result1 = @mysqli_query($connection, $query1)))
        trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
      if (!($memberrow = mysqli_fetch_array($result1)))
        trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
      $email = $memberrow["m_email"];
    }
  }

  return ($email);
}

function formatCurrentDateTime()
{
  $datestr = date('m-d-Y, g:ia', time());
  return ($datestr);
}

//// code starts here ////
// get and validate the event number
$event_id = isset($_GET['event_id']) ? clean($_GET['event_id']) : "";
//$event_id = trim($event_id);

// redirect to the elist.php page with bad event_id
if (empty($event_id)) {
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}

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
  LogMsg('REDIRECTING to $loc: ' . $loc . '   $referer: ' . $referer);
  header($loc);
  exit;
}

// Reset $formVars
$formVars = array();

// Reset the errors
$errors = array();

// connect to the database
mysqlconnect($connection);

// get event
$event_id = quotesqldata($event_id);
$query = "SELECT * FROM events WHERE (event_id ='" . $event_id . "');";
if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

// redirect to the events list page
if ($numrows = (@mysqli_num_rows($result) <= 0)) {
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}
$row = mysqli_fetch_array($result);

// is currently logged in user attending this event?
// obtain member cust_id from members table
$loginUsername = LoginUsername();
$cust_id = getCustomerID($loginUsername, $connection);
$formVars['cust_id'] = $cust_id;

$query = "SELECT * FROM reserve " .
  "WHERE ( (event_id='" . $event_id . "')"
  . " AND (cust_id='" . $cust_id . "'))"
  . " order by r_date_reserved;";

if (!($curresult = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$CurrentUserReserveRow = mysqli_fetch_array($curresult);
if (!is_null($CurrentUserReserveRow)) {
  $formVars["attending"] = $CurrentUserReserveRow["r_attending"];
  $formVars["comments"] = $CurrentUserReserveRow["r_comments"];
  $formVars["guests"] = $CurrentUserReserveRow["r_num_guests"];
  $formVars["r_amount_paid"] = $CurrentUserReserveRow["r_amount_paid"];
} else {
  $formVars["attending"] = "";
  $formVars["comments"] = "";
  $formVars["guests"] = "";
  $formVars["r_amount_paid"] = "";
}

// obtain data for folks who said YES
$query = "SELECT * FROM reserve " .
  "WHERE ((event_id='" . $event_id . "') AND (r_attending = 'Yes'))  order by r_date_reserved;";
if (!($YesHandle = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$Yes = array();
$i = 0;
$YesGuests = 0;
while ($YesRow = mysqli_fetch_array($YesHandle)) {
  $member_data = GetMemberData($YesRow["cust_id"]);
  $Yes[$i] = $YesRow;
  $Yes[$i]['custName'] = $member_data["m_firstname"] . " " . $member_data["m_lastname"];
  $Yes[$i]['custProfile'] = $member_data["m_profile_display"];
  $YesGuests += $YesRow["r_num_guests"];
  $i = $i + 1;
}
$formVars["CurrentYesAttendees"] = count($Yes) + $YesGuests;

// obtain data for folks who said MAYBE
$query = "SELECT * FROM reserve
            WHERE ((event_id='" . $event_id . "') AND (r_attending = 'WaitingList'))" .
  " ORDER BY reserve.r_date_reserved";
if (!($MaybeHandle = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$Maybe = array();
$i = 0;
$MaybeGuests = 0;
while ($MaybeRow = mysqli_fetch_array($MaybeHandle)) {
  $Maybe[$i] = $MaybeRow;
  $member_data = GetMemberData($MaybeRow["cust_id"]);
  $Maybe[$i]['custName'] = $member_data["m_firstname"] . " " . $member_data["m_lastname"];
  $Maybe[$i]['custProfile'] = $member_data["m_profile_display"];
  $MaybeGuests += $MaybeRow["r_num_guests"];
  $i = $i + 1;
}
$formVars["CurrentMaybeAttendees"] = count($Maybe) + $MaybeGuests;

// obtain data for folks who said NO
$query = "SELECT * FROM reserve
            WHERE ((event_id='" . $event_id . "') AND (r_attending = 'Comments'))  order by r_date_reserved;";

if (!($NoHandle = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$No = array();
$i = 0;
$NoGuests = 0;
while ($NoRow = mysqli_fetch_array($NoHandle)) {
  $No[$i] = $NoRow;
  $member_data = GetMemberData($NoRow["cust_id"]);
  $No[$i]['custName'] = $member_data["m_firstname"] . " " . $member_data["m_lastname"];
  $No[$i]['custProfile'] = $member_data["m_profile_display"];
  $NoGuests += $NoRow["r_num_guests"];
  $i = $i + 1;
}
$formVars["CurrentNoAttendees"] = count($No) + $NoGuests;

// Load all the form variables with customer data
$formVars["event_id"] = $row["event_id"];
$formVars["leader_id"] = $row["leader_id"];
$formVars["coleader_id"] = $row["co_leader_id"];
$coleader_id = $formVars["coleader_id"];

$formVars["name"] = $row["e_name"];

$formVars["begindate"] = HumanDates($row["e_begindate"]);
$formVars["begintime"] = $row["e_begintime"];

$formVars["details"] = $row["e_details"];
$formVars["details_private"] = $row["e_details_private"];

$formVars["location_name"] = $row["e_location_name"];

$formVars["location_meet_at"] = $row["e_location_meet_at"];
$formVars["driving_directions"] = $row["e_driving_directions"];
$formVars["min_attendees"] = $row["e_min_attendees"];
$formVars["max_attendees"] = $row["e_max_attendees"];
$formVars["contingency_plan"] = $row["e_contingency_plan"];
$formVars["url1"] = $row["e_url1"];
$formVars["url2"] = $row["e_url2"];
$formVars["url3"] = $row["e_url3"];
$formVars["url4"] = $row["e_url4"];
$formVars["status"] = $row["e_status"];
$formVars["display"] = $row["e_display"];

$formVars["pmt_descr"] = $row["e_pmt_descr"];

$formVars["pay4event"] = $row["e_pay4event"];

$formVars["e_fullprice"] = $row["e_fullprice"];
$formVars["e_fullprice_date"] = $row["e_fullprice_date"];
$formVars["e_deposit"] = $row["e_deposit"];
$formVars["e_deposit_date"] = $row["e_deposit_date"];

$formVars["date_added"] = HumanDates($row["e_date_added"]);
$formVars["date_changed"] = HumanDates($row["e_date_changed"]);

$formVars["bring"] = $row["e_bring"];
$formVars["includes"] = $row["e_includes"];
$formVars["leader_notes"] = $row["e_leader_notes"];

// convert time to 12 hr am/pm format
$begintime = $formVars['begintime'];
$begintime = date('g:ia', strtotime($begintime));

// preset string with event day of week: Mon, Tues, etc
$daystring = date('D', strtotime($row["e_begindate"]));

$eVars = array();
$eVars['RawBeginDate'] = $row['e_begindate'];
$eVars['RawBeginTime'] = $row['e_begintime'];

if ($formVars["status"] == "Approved")
  $statusMsg = "";
else if ($formVars["status"] == "SelfGuided")
  $statusMsg = "SELF GUIDED EVENT!";
else if ($formVars["status"] == "Hide")
  $statusMsg = "HIDDEN! Invisible to Members!";
else if ($formVars["status"] == "Proposed")
  $statusMsg = "PROPOSED Event PROPOSED!";
else if ($formVars["status"] == "Canceled")
  $statusMsg = "CANCELLED Event CANCELLED!";
else
  $statusMsg = $formVars["status"];

if ($formVars["display"] == "Public")
  $publicMsg = "Public Event";
else
  $publicMsg = "";

$GoogleCalendarLink = GoogleCalendarLink($eVars, $formVars);

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$ShortClubName = GetParameter('ShortClubName');
$AllowNoComments = GetParameter('AllowNoComments');
$WebPageTitle = 'Event Details ' . $ClubCompanyName;
require('top.php');

?>
<div id="centercontent">
  <?php
  // if user has event leader or admin privilages,
  // let them edit the event

  $AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
  $LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
  $VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';

  $leader_id = $formVars['leader_id'];
  $user_owns_event = ($leader_id == $cust_id) ||
    ($coleader_id == $cust_id);

  $str = "";
  $str = '<td valign="center">';
  if (($AdminLevel) || ($user_owns_event)) {
    //$str .= '<td valign="center" align="center">'."\n";
    $str .= '<form method="POST" action="/members/eedit.php">' . "\n";
    $str .= '<input type=hidden name="event_id" value="' . $formVars['event_id'] . '">' . "\n";
    $str .= '<input type=hidden name="mode" value="edit">' . "\n";
    $str .= '<input type="submit" value="Edit event">' . "\n";
    $str .= '</form>';
    //$str .= '</td>'."\n";
    /*LogMsg('event view edit button tag $loginUsername: '
		.$loginUsername
		."\n"
		.'$formVars[name]: '.$formVars["name"]
		."\n"
		.'$str: '.print_r($str,true)
		);
		*/
  }

  if (($AdminLevel || $LeaderLevel)) {
    //$str .= '<td valign="center" align="center">'."\n";
    $str .= '<form method="POST" action="/members/eedit.php">' . "\n";
    $str .= '<input type=hidden name="event_id" value="' . $formVars['event_id'] . '">' . "\n";
    $str .= '<input type=hidden name="mode" value="copy">' . "\n";
    $str .= '<input type="submit" value="Copy event">' . "\n";
    $str .= '</form>' . "\n";
    //$str .= '</td>'."\n";
    /*LogMsg('event view copy button tag $loginUsername: '
		.$loginUsername
		."\n"
		.'$formVars[name]: '.$formVars["name"]
		."\n"
		.'$str: '.print_r($str,true)
		);
		*/
  }

  if (($AdminLevel || $user_owns_event)) {
    //$str .= '<td valign="center" align="center">'."\n";
    $str .= '<form method="POST" action="/members/eedit-co-leader.php">' . "\n";
    $str .= '<input type=hidden name="event_id" value="' . $formVars['event_id'] . '">' . "\n";
    $str .= '<input type=hidden name="mode" value="changecoleader">' . "\n";
    $str .= '<input type="submit" value="Change CoLeader">' . "\n";
    $str .= '</form>' . "\n";
    //$str .= '</td>'."\n";
    //$str .= '<td valign="center" align="center">'."\n";
    $str .= '<form method="POST" action="/members/eedit-leader.php">' . "\n";
    $str .= '<input type=hidden name="event_id" value="' . $formVars['event_id'] . '">' . "\n";
    $str .= '<input type=hidden name="mode" value="changeleader">' . "\n";
    $str .= '<input type="submit" value="Change Leader">' . "\n";
    $str .= '</form>' . "\n";
    //$str .= '</td>'."\n";
  }
  $str .= '</td>';
  //if (!empty($str)) {
  //	$str2 = '<table width="100%" border="1" bordercolor="#111111" cellpadding="7" cellspacing="0" style="border-collapse: collapse">'
  //		.'<tr>'
  //		.$str
  //		.'</tr>'
  //		.'</table>';
  //	echo $str2;
  //}


  ?>

  <!-- display event name -->
  <table width="<?php echo $FullTableWidth; ?>" border="<?php echo $border; ?>">
    <tr>
      <td valign="top">
        <b>
          <?php
          echo stripslashes($formVars["name"]);
          //echo "<br>" . $formVars["status"];
          //echo "<br>" . $statusMsg;
          //echo "<br>";
          if ($statusMsg)
            echo "<br>" . $statusMsg;
          if ($publicMsg)
            echo "<br>"
              . $publicMsg;
          ?>
        </b>
      </td>
      <?php
      echo $str;
      ?>
    </tr>
  </table>

  <!-- begin upper details table -->
  <table width="<?php echo $FullTableWidth; ?>" border="<?php echo $border; ?>">
    <tr>
      <td bgcolor="#eeeeee" width="20%" class="right">Where:</td>
      <td width="80%"><?php echo stripslashes($formVars["location_name"]); ?></td>
    </tr>

    <?php
    if (!empty($formVars["url4"])) {
      echo '<tr>';
      echo '<td align=right  bgcolor="#eeeeee" class="right">Event Link:</td>';
      echo '<td>';
      $aURL = parse_url($formVars["url4"]);
      if (isset($aURL['host'])) {
        echo '<a href="' . $formVars['url4'] . '" target="url4">'
          . $aURL['host'] . '</a>';
      } else {
        echo $formVars['url4'];
      }
      echo '</td>';
      echo '</tr>';
    }
    ?>

    <tr>
      <td align=right bgcolor="#eeeeee" class="right">When:</td>
      <td>
        <?php
        echo date('D, M j', strtotime($row["e_begindate"])) . "&nbsp;" . $begintime;
        ?>
      </td>
    </tr>

    <tr>
      <td align=right bgcolor="#eeeeee" class="right">Leader:</td>
      <td>
        <a href="/members/mview.php?cust_id=<?php echo trim($formVars['leader_id']); ?>" target="leader_profile">
          <?php echo GetMemberName($formVars["leader_id"]); ?>
        </a>
      </td>
    </tr>

    <tr>
      <td align=right bgcolor="#eeeeee" class="right">Email:</td>
      <td><a href="mailto:<?php echo GetMemberEmail($formVars["leader_id"]); ?>" target="leader_email"><?php echo GetMemberEmail($formVars["leader_id"]); ?></a>
      </td>
    </tr>

    <?php if (GetMemberName($formVars["coleader_id"]) != "Unknown") { ?>
      <tr>
        <td align=right bgcolor="#eeeeee" class="right">Co-Leader:</td>
        <td>
          <a href="/members/mview.php?cust_id=<?php echo trim($formVars['coleader_id']); ?>" target="leader_profile">
            <?php echo GetMemberName($formVars["coleader_id"]); ?>
          </a>
        </td>
      </tr>

      <tr>
        <td align=right bgcolor="#eeeeee" class="right">Email:</td>
        <td><a href="mailto:<?php echo GetMemberEmail($formVars["coleader_id"]); ?>" target="leader_email"><?php echo GetMemberEmail($formVars["coleader_id"]); ?></a>
        </td>
      </tr>
    <?php } ?>

    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>


    <tr>
      <td></td>
      <td>
        <a href="<?php echo $GoogleCalendarLink; ?>" target="_blank">Add Event to Google Calendar</a>
      </td>
    </tr>

    <tr>
      <td colspan=2>
        <p>&nbsp;<br><b>Public Details:</b> <?php echo stripslashes($formVars["details"]); ?>
      </td>
    </tr>

    <?php
    if (!empty($formVars["details_private"])) {
      echo "<tr><td colspan=2><p>&nbsp;<br><b>Member Details:</b> " . $formVars["details_private"] . "<br>&nbsp;</p></td></tr>";
    }
    ?>

    <?php

    if (isset($row['e_pmt_descr']))
      echo '<tr><td align=right  bgcolor="#eeeeee" class="right">Cost Comments:</td><td>' . $row['e_pmt_descr'] . '</td></tr>';

    if (isset($row['e_fullprice'])) {
      if ($row['e_fullprice'] > 0) {
        $cost = sprintf("$ %.02f", $row['e_fullprice']);
        $duedate = "";
        if (isset($row['e_fullprice_date'])  and !empty($row['e_fullprice_date']) and ($formVars['e_fullprice_date'] != 0))
          $duedate = " due by " . date('D, M j', strtotime($row["e_fullprice_date"]));
        //echo '<tr><td align=right  bgcolor="#eeeeee" class="right">Total Cost:</td><td>' . $cost . $duedate . '</td></tr>';
      }
    }

    if (isset($row['e_deposit'])) {
      if ($row['e_deposit'] > 0) {
        $cost = sprintf("$ %.02f", $row['e_deposit']);
        $duedate = "";
        if (isset($row['e_deposit_date'])  and !empty($row['e_deposit_date']) and ($formVars['e_deposit_date'] != 0))
          $duedate = " due by " . date('D, M j', strtotime($row["e_deposit_date"]));
        echo '<tr><td  bgcolor="#eeeeee" class="right">Deposit:</td><td>' . $cost . $duedate . '</td></tr>';
      }
    }
    ?>

    <tr>
      <td align=right bgcolor="#eeeeee" class="right">Attendees:</td>
      <td>Minimum needed for event - <?php echo $formVars["min_attendees"]; ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Maximum number - <?php echo $formVars["max_attendees"]; ?> </td>
    </tr>

    <?php
    if (!empty($formVars["url1"])) {
      echo '<tr>';
      echo '<td align=right  bgcolor="#eeeeee" class="right">Details Link:</td>';
      echo '<td>';
      $aURL = parse_url($formVars["url1"]);
      if (isset($aURL['host'])) {
        echo '<a href="' . $formVars['url1'] . '" target="url1">'
          .  $aURL['host'] . '</a>';
      } else {
        echo $formVars['url1'];
      }
      echo '</td>';
      echo '</tr>';
    }
    if (!empty($formVars["url2"])) {
      echo '<tr>';
      echo '<td bgcolor="#eeeeee" class="right">Details Link:</td>';
      echo '<td>';
      $aURL = parse_url($formVars["url2"]);
      if (isset($aURL['host'])) {
        echo '<a href="' . $formVars['url2'] . '" target="url2">'
          .  $aURL['host'] . '</a>';
      } else {
        echo $formVars['url2'];
      }
      echo '</td>';
      echo '</tr>';
    }
    if (!empty($formVars["url3"])) {
      echo '<tr>';
      echo '<td bgcolor="#eeeeee" class="right">Directions Link:</td>';
      echo '<td>';
      $aURL = parse_url($formVars["url3"]);
      if (isset($aURL['host'])) {
        echo '<a href="' . $formVars['url3'] . '" target="url3">'
          .  $aURL['host'] . '</a>';
      } else {
        echo $formVars['url3'];
      }
      echo '</td>';
      echo '</tr>';
    }
    if (!empty($formVars["location_meet_at"])) {
      echo '<tr><td bgcolor="#eeeeee" class="right">Meet At:</td>';
      echo '<td>' . stripslashes($formVars["location_meet_at"]) . '</td></tr>';
    }
    if (!empty($formVars["driving_directions"])) {
      echo '<tr><td bgcolor="#eeeeee" class="right">Directions:</td>';
      echo "<td>" . stripslashes($formVars["driving_directions"]) . '</td></tr>';
    }
    if (!empty($formVars["contingency_plan"])) {
      echo '<tr><td bgcolor="#eeeeee" class="right">Contingency Plan:</td>';
      echo "<td>" . stripslashes($formVars["contingency_plan"]) . '</td></tr>';
    }
    if (!empty($formVars["bring"])) {
      echo '<tr><td align=right  bgcolor="#eeeeee" class="right">Bring With You:</td>';
      echo "<td>" . stripslashes($formVars["bring"]) . '</td></tr>';
    }
    if (!empty($formVars["includes"])) {
      echo '<tr><td bgcolor="#eeeeee" class="right">Includes:</td>';
      echo "<td>" . stripslashes($formVars["includes"]) . '</td></tr>';
    }
    if ((!empty($formVars["canceldate"])) and ($formVars["canceldate"] != '00/00/0000')) {
      echo '<tr><td align=right  bgcolor="#eeeeee">Deadline Date:</td>';
      echo "<td>" . $formVars["canceldate"] . '</td></tr>';
    }
    if ((!empty($formVars["rescheduledate"]))) {
      echo '<tr><td align=right  bgcolor="#eeeeee">Reschedule Notes:</td>';
      echo "<td>" . $formVars["rescheduledate"] . '</td></tr>';
    }


    $AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
    $LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
    $VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
    if (
      ($AdminLevel || $LeaderLevel) or
      ($user_owns_event)
    ) {

      if ((!empty($formVars["display"]))) {
        echo '<tr><td align=right  bgcolor="#eeeeee" class="right">Display:</td>';
        echo "<td>" . $formVars["display"] . '</td></tr>';
      }
    }


    ?>
  </table>
  &nbsp;
  <table width="100%" border="0">
    <tr>
      <td valign=top>
        <table cellpadding="4" width="<?php echo $HalfTableWidth; ?>" border="<?php echo $border; ?>">
          <tr>
            <th bgcolor="#3399cc">
              <font color=white>Who is Signed Up?</font>
            </th>
          </tr>
          <tr>
            <td>As of <?php echo formatCurrentDateTime(); ?></td>
          </tr>
          <tr>
            <td><? include 'eview-yes-event.inc'; ?></td>
          </tr>
          <tr>
            <td><? include 'eview-maybe-event.inc'; ?></td>
          </tr>
          <tr>
            <td><? include 'eview-no-event.inc'; ?></td>
          </tr>
        </table>
      </td>

      <td valign=top>
        <? include 'eview-sign-up.inc'; ?>
      </td>
    </tr>
  </table>
  <hr align="left" color="#000000" SIZE="1" noshade>

  <table border="<?php echo $border; ?>">
    <tr>
      <td>Date Event Added: </td>
      <td><? echo fieldError("date_added", $errors); ?>
        <? echo $formVars["date_added"]; ?>
      </td>

      <td>Date Event Changed: </td>
      <td><? echo fieldError("date_changed", $errors); ?>
        <? echo $formVars["date_changed"]; ?>
      </td>
    </tr>
  </table>



  <?php
  $AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
  $LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
  $VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
  if (
    ($AdminLevel || $LeaderLevel) or
    ($user_owns_event)
  ) {

    echo "<br>";
    echo '<b>';
    echo '<a href="/members/contacts.php?event_id=';
    if (isset($formVars["event_id"]))
      echo $formVars["event_id"];
    echo '">Contact Information for Event Attendees&nbsp;</a></b><br>&nbsp;';

    if (!empty($formVars["leader_notes"])) {
      echo '<table width="' . $FullTableWidth . '" border="0">';
      echo "<tr>";
      echo "<td>";
      echo "Leader Notes: ";
      echo $formVars['leader_notes'];
      echo "</td>";
      echo "</tr>";
      echo "</table>";
    }

    echo '<table width="' . $FullTableWidth . '" border="0">';
    echo "<tr>";
    echo "<td>";
    echo "event_id: ";
    echo $event_id;
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "cust_id: ";
    echo $cust_id;
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "leader_id: ";
    echo $formVars['leader_id'];
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "CoLeader_id: ";
    echo $coleader_id;
    echo "</td>";
    echo "</tr>";
    echo "</table>";
  } //if AdminLevel or LeaderLevel
  ?>
</div> <!-- end CenterContent -->

<?php

require('footer.php');

?>