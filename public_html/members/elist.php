<?php
// elist.php
// display list of events
// user can click on an event name to view details

//echo "elist: 1<br>";

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();


if (isset($debug) && $debug) {
  ob_start();
  $start = microtime(true);
}
//echo "elist: 2<br>";

$ClubCode = GetParameter('ClubCode');

//print "continuing...<br>";

//echo "elist: 3<br>";

$PrintFlag = false;

$GetVars = array();
if (count($_POST) != 0) {
  foreach ($_POST as $varname => $value) {
    $GetVars[$varname] = substr($value, 0, 30);
    //echo 'GetVars[varname]: '.$GetVars[$varname]."<br>\n";
  }
}

if (!isset($_SESSION['DisplayFilter'])) {
  $_SESSION['DisplayFilter'] = 'AllClubs';
}

if (isset($GetVars['DisplayFilter'])) {
  if ((strtolower($GetVars['DisplayFilter']) == 'allclubs') ||
    (strtolower($GetVars['DisplayFilter']) == $ClubCode)
  ) {
    $_SESSION['DisplayFilter'] = $GetVars['DisplayFilter'];
  }
}


// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
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

// Is the user logged in and were there no errors from a previous
// validation?

//echo "loginUsername: " . $loginUsername . "<br>";
// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
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
$AuthLevel = AuthLevel($loginUsername);
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';

//echo "AuthLevel(): " . AuthLevel() . "<br>";
//echo "AuthLevel: " . $AuthLevel . "<br>";
//echo "elist: 4<br>";

mysqlconnect($connection);

//echo "elist: 6<br>";
//echo "loginUsername: " . $loginUsername . "<br>";
// obtain member information from members table
$cust_id = getCustomerID($loginUsername);
#$cust_id = @ getCustomerID($loginUsername);

//echo "elist: 7<br>";
if (!isset($cust_id)) {
  trigger_error(
    "Invalid Customer ID ("
      . $cust_id . ") for loginUsername: " . $loginUsername .
      " in eList!",
    E_USER_ERROR
  );
  exit;
}
//echo "elist: 8<br>";

if (!isset($_POST['list_start_date'])) {
  $today = date('m/d/Y', time());
  $list_start_date = $today;
} else {
  $list_start_date = $_POST['list_start_date'];
  // Validate begin date
  if (empty($list_start_date))
    // the begin date cannot be a null string
    $list_start_date = date('m/d/Y', time());
  elseif (!preg_match(
    '#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#',
    $list_start_date,
    $parts
  ))
    // Check the format
    //$errors["begindate"] =
    //"The begin date is not a valid date in the " .
    //"format MM/DD/YYYY";
    $list_start_date = date('m/d/Y', time());
  elseif (!checkdate($parts[1], $parts[2], $parts[3]))
    //$errors["begindate"] =
    //"Invalid begin date. Month must be " .
    //"between 1 and 12, day must be valid for that month.";
    $list_start_date = date('m-d-Y', time());
  //else
  // Reassemble the date into database format
}
$list_start_date = quotesqldata($list_start_date);

// echo 'DisplayFilter: '.$GetVars['DisplayFilter']."<br>\n";
// display only 'home' club events or AllClubs events?
//switch ($_SESSION ['DisplayFilter']) {
//switch (strtolower($GetVars['DisplayFilter'])) {
switch (strtolower($_SESSION['DisplayFilter'])) {
  case 'allclubs':
    $filter = '';
    break;
  case $ClubCode:
    $filter = " and events.event_id like '" . $ClubCode . "%' ";
    break;
  default:
    $filter = "";
    break;
}

$query = "SELECT events.event_id, events.e_name, events.e_begindate, "
  . " events.e_location_name, events.e_status, events.e_display, "
  . " events.e_begintime, events.leader_id "
  . " FROM events "
  . " WHERE  "
  . " events.e_begindate >= "
  .  " '" . MySqlDate($list_start_date) . "' "
  . $filter
  . " ORDER BY events.e_begindate, events.e_begintime ";

//$timer->setMarker('Just prior to select events mysqli_query...');

if (!($result = @mysqli_query($connection, $query)))
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );

//$timer->setMarker('Prior to mysqli_fetch_array while loop...');

$e_recs = array();
// suck in event data
while ($e_row = mysqli_fetch_array($result)) {
  $e_id = $e_row['event_id'];

  // don't include MembersOnly events not in this club
  if (!strstr($e_id, $ClubCode) && ($e_row['e_display'] == 'MembersOnly'))
    continue;

  // new event record, grab it
  $e_row['attendee_sum'] = 0;
  $e_row['attendee_gnv'] = 0;
  $e_row['attendee_cfac'] = 0;
  $e_row['attending'] = 0;
  $r_query = "SELECT  "
    . " reserve.reserve_id, reserve.event_id, "
    . " reserve.cust_id, reserve.r_attending, "
    . " reserve.r_num_guests "
    . " FROM reserve "
    . " WHERE reserve.event_id = "
    .  " '" . $e_id . "' ";

  //$timer->setMarker('Prior to select reserve mysqli_query...');

  if (!($r_result = @mysqli_query($connection, $r_query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  //$timer->setMarker('Prior to mysqli_fetch_array(r_result) while loop...');

  while ($r_row = mysqli_fetch_array($r_result)) {
    // same event, increment attendee count?
    if ($r_row['r_attending'] == 'Yes') {
      $e_row['attendee_sum'] += 1;
      if (strstr($r_row['cust_id'], 'gnv'))
        $e_row['attendee_gnv'] += 1;
      if (strstr($r_row['cust_id'], 'cfac'))
        $e_row['attendee_cfac'] += 1;
      if (
        isset($r_row['r_num_guests']) &&
        $r_row['r_num_guests'] > 0
      )
        $e_row['attendee_sum'] += $r_row['r_num_guests'];
    }

    // is current user attending this event?
    if ($r_row['cust_id'] == $cust_id)
      $e_row['attending'] = $r_row['r_attending'];
  }
  $e_recs[$e_id] = $e_row;
}
//DisplayArry($e_recs);


$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';

$week = 0;
foreach ($e_recs as $e_row) {
  $event_id = $e_row["event_id"];

  $leader_id = stripslashes($e_row["leader_id"]);
  $LeaderName = GetMemberName($leader_id);

  $weekstring = "";
  $eventweek = date('W', strtotime($e_row["e_begindate"]));
  if ($week != $eventweek) {
    // start a new week with a horizontal rule
    $week = $eventweek;
    $weekstring = "<tr><td colspan=8><hr align='left' color='#000000' SIZE='1' noshade></td></tr>\n";
    //$weekstring = "<tr><td colspan=8><hr align='left' style='border-style: dashed; border-width: 1' noshade color='#000000' size='1'></td></tr>\n";
  }

  // preset string with event day of week: Mon, Tues, etc
  $day = date('D', strtotime($e_row["e_begindate"]));

  $begintime = $e_row["e_begintime"];
  $begintime = date('g:ia', strtotime($begintime));
  //$begintime = substr($e_row["e_begintime"],0,5);

  // do not display events with status == Hide
  if ((($AuthLevel == 'Admin') ||
      ($AuthLevel == 'Leader')) ||
    ($e_row['e_status'] != 'Hide')
  ) {

    $col_start_color = "";
    $col_end_color = "";
    if (!empty($e_row['attending']))
      switch ($e_row['attending']) {
        case 'Yes':
          $col_start_color = '<font color="green"><b>';
          $col_end_color = '</b></font>';
          break;
        case 'WaitingList':
          $col_start_color = '<font color="orange">';
          $col_end_color = '</font>';
          break;
        case 'Comments':
          $col_start_color = '<font color="red">';
          $col_end_color = '</font>';
          break;
      }

    // display canceled events in Strike-Through
    if ($e_row['e_status'] == 'Canceled') {
      $col_start_color .= "<strike>";
      $col_end_color = "</strike>" . $col_end_color;
    }

    // display proposed events in italics
    if ($e_row['e_status'] == 'Proposed') {
      $col_start_color .= "<i>";
      $col_end_color = "</i>" . $col_end_color;
    }

    // display hidden events in strike through AND italics
    if ($e_row['e_status'] == 'Hide') {
      $col_start_color .= "<strike><i>";
      $col_end_color = "</i></strike>" . $col_end_color;
    }

    if ($PrintFlag) {
      $col_start_color = "";
      $col_end_color = "";
    }
    // build string which represents an event row
    $str = '';
    // if event is sponsored by home club, highlight it
    $club_code = $e_row['event_id'];
    $club_code = substr($club_code, 0, strpos($club_code, '_'));
    if ($club_code == $ClubCode) {
      $str .= $weekstring
        . "<tr style='background: #f5f5f5;'>";
    } else {
      $str .= $weekstring
        . "<tr>";
    }
    //<span style="background: #e9e9e9;">text here</span>
    //$str.='<span style="background: #e9e9e9;">';

    // event begin date
    //$str.='<div id="HomeClubEventListRow">';
    //$str.='<span style="background: #e9e9e9;">';
    $str .= "<td>";
    $str .= $col_start_color;
    $str .= date('D M j Y', strtotime($e_row["e_begindate"]));
    $str .= $col_end_color;
    $str .= "<br>";

    // event begin time
    $str .= $col_start_color;
    $str .= $begintime;
    $str .= $col_end_color;
    $str .= "</td>";

    // event attendee count column
    $str .= "<td nowrap align='center'>";
    $str .= $col_start_color;
    if (isset($e_row['attendee_sum'])) {
      $str .= $e_row['attendee_sum'];
    }
    $str .= $col_end_color;
    $str .= "</td>";

    // event name link
    $EventName = stripslashes($e_row["e_name"]);
    //if ($e_row["e_status"] == 'Canceled') {
    //   $EventName = 'CANCELED'. $EventName .'CANCELED';
    //}
    $str .= "<td>";
    $str .= $col_start_color;
    if (!$PrintFlag) {
      $str .= "<a href=\"eview.php?event_id="
        . $e_row["event_id"] . "\">"
        . $EventName . "</a>";
    } else {
      $str .= $EventName;
    }
    $str .= $col_end_color;
    $str .= "</td>";

    // club code: gnv, cfac, etc
    $club_code = $e_row['event_id'];
    $club_code = substr($club_code, 0, strpos($club_code, '_'));
    //$str .= "<td>";
    //$str .= $club_code;
    //$str .= "</td>";

    // event location link
    $str .= "<td>";
    $str .= $col_start_color;
    $str .= stripslashes($e_row["e_location_name"]);
    $str .= "<br>";
    //$str .= stripslashes($leader_id);
    $str .= stripslashes($LeaderName);
    $str .= $col_end_color;
    $str .= "</td>";

    // e_display: MembersOnly, AllClubs, Public
    //$e_display = $e_row['e_display'];
    //$str .= "<td>";
    //$str .= $e_display;
    //$str .= "</td>";

    //if ($AuthLevel == 'Admin') {

    //    $str .= "<td>";
    //    $str .= $e_row['attendee_gnv'];
    //    $str .= "</td>";

    //    $str .= "<td>";
    //    $str .= $e_row['attendee_cfac'];
    //    $str .= "</td>";

    //}

    //$str .= "</div>";
    //$str .= "</span>";
    $str .= "</tr>";
    $strings[$event_id] = $str;
  } // end foreach
} // end while

//$timer->setMarker('After all strings generated...');


?>
<?php
if ($debug) {
  $end = microtime(true);
  ob_end_clean();
  echo "<p>elist.php took " . ($end - $start) . " seconds to run.<br></p>";
}

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Event List ' . $ClubCompanyName;
$EnableShoutbox = true;
require('top.php');

?>

<div id="centercontent">
  <h3 align="left">Member Events List</h3>
  <table border="1" cellpadding="5" cellspacing="5" style="border-collapse: collapse" bordercolor="#dedede" width="100%" id="AutoNumber16" bgcolor="#F5F5F5">
    <tr>
      <td width="100%"><img border="0" src="../images/email-icon.jpg" align="left" hspace="7" width="42" height="38">Email Notifications:</b> If you are not getting NEW EVENT EMAILS, please check your spam folder.
        Mark it as NOT SPAM or add our email address, donotreply@adventureclub.info, to your contacts.<br>
        <br>
        <br>
        <img border="0" src="../images/info-icon.jpg" hspace="7" align="left" width="42" height="41">As a courtesy to leaders and other members, please observe our <a href="#ReservationEtiquette">Reservation Etiquette</a>.
      </td>
    </tr>
  </table>

  <table border="0">
    <tr>
      <td nowrap valign=top>
        <form method="POST" action="/members/elist.php">
          Start Date: &nbsp;
          <input type=text name=list_start_date value="<?php echo $list_start_date; ?>" size=9 maxlength=10 onfocus="showCalendar('',this,this,'','holder1',0,30,1)">
          &nbsp;<input type="submit" value="Submit">
        </form>
      </td>
      <?php
      if (($AdminLevel || $LeaderLevel)) {
      ?>
        <td valign=top>
          <form method="POST" action="/members/eedit.php">
            <input type=hidden name="event_id" value="0">
            <input type=hidden name="mode" value="new">
            <input type="submit" value="New event">
          </form>
        </td>
      <?php
      }
      ?>
    </tr>
  </table>
  </form>

  <hr align='left' color='#000000' SIZE='1' noshade>

  <table border=0 width=100%>
    <col span="1">
    <tr>
      <td>Date</td>
      <td>RSVP</td>
      <td>Event Name</td>
      <td>Location / Leader</td>

    </tr>

    <?php
    if (!empty($strings)) {
      foreach ($strings as $str) {
        echo $str . "\n";
      }
    }
    ?>

  </table>


  <hr align='left' color='#000000' SIZE='1' noshade>
  <table>
    <tr>
      <td><a name="ReservationEtiquette"></a>
        <p align="left">
          <b>Reservation Etiquette</b><br>
          Adventure Club Event Leaders spend quite a bit of time
          coordinating club events. With this in mind, please
          be courteous and
          considerate to Event Leaders and other members and follow
          the conventions below: </p>
        <ul>
          <li>
            <p align="left">Do not be a no-show. If you must cancel within 24 hours before an event,
              please change your website reservation status
              from Yes to either Waiting-List or No. Follow the change in
              status with an email to the Event Leader.
          </li>
          <li>
            <p align="left">If you have to cancel the day of the event, please
              contact the event leader. If it is 3 or 4 hours before
              the event,
              try the event leader's cell phone. Remember most members and Event
              Leaders check email only once a day.
          </li>

          <li>
            <p align="left">
              If you are uncertain about attending an event, sign up on the Waiting List. Understand that this is not a firm reservation, and you will not be expected at the event. Leaders will not count on you to show, and may not have room for you, and perhaps even start without you. Please move to the Yes list as soon as you can commit.
          </li>

          <li>
            <p align="left">
              Please DO NOT CRASH EVENTS THAT ARE FULL. Leaders have reasons for
              the limits on the number of people that can attend an event. These
              include cost, limited room in a private residence and limits on
              available resources (number of horses, number of windsurfers,
              number of seats at a restaurant, etc).
          </li>

          <li>
            <p align="left">Please refrain from entering racist or other
              inappropriate (sexual innuendo) comments on the website.
          </li>
        </ul>
        <hr align="left" color="#000000" SIZE="1" noshade>
      </td>
    </tr>
    <tr>
      <td>
        <h3>Key</h3>
        <ul>
          <li><b>
              <font color="green">Green/Bold</font>
            </b>: Yes, you are signed up</li>
          <li>
            <font color="orange">Orange: Waiting List, I want to attend!</font>
          </li>
          <li><i>Italics: Proposed event, subject to change, cancellation.</i></li>
          <li><strike>Strike-Through: event has been CANCELLED</strike></li>
          <?php
          if ($AdminLevel or $LeaderLevel) {
            echo "<li><i><strike>Italics/strike through: event is hidden from normal members</strike></i></li>" . "\n";
            echo "<br>";
            //$s = session_id();
            //echo "Session ID: ";
            //var_dump($s);
          }
          ?>
        </ul>
      </td>
    </tr>


    <tr>
      <td width="100%" nowrap valign="top" colspan="2">&nbsp;</td>
    </tr>
  </table>

</div>

<?php
require('footer.php');
?>