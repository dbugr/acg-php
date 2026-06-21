<?php
// display list of events
// user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
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
// validation?  

// Is the user logged in?
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
//$AuthLevel = AuthLevel($loginUsername);
//$AdminLevel = $AuthLevel == 'Admin';	
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $PHP_SELF;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}

// set user levels
//$AuthLevel = AuthLevel($loginUsername);
//$AdminLevel = $AuthLevel == 'Admin' ? 1 : 0;
//$LeaderLevel = ($AuthLevel === 'Leader' ? 1 : 0);

// connect to the database
mysqlconnect($connection);

// connect to database
//if (!($connection = @ mysqli_connect($hostName,$username,$password)))
//	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);
//if (!mysqli_select_db($databaseName, $connection))
//	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);

// obtain member information from members table
$loginUsername = LoginUsername();
$cust_id = @getCustomerID($loginUsername);
$ClubCode			= GetParameter('ClubCode');

if (!isset($cust_id)) {
  trigger_error(
    "Invalid Customer ID ("
      . $cust_id . ") for loginUsername: " . $loginUsername .
      " in eList!",
    E_USER_ERROR
  );
  exit;
}

// get start position
if (!isset($_POST['list_start_date'])) {
  $today = date('m/d/Y', time());
  $list_start_date = $today;
} else {
  // Validate begin date
  $list_start_date = clean($_POST['list_start_date']);

  // the begin date cannot be a null string
  if (empty($list_start_date))
    $list_start_date = date('m/d/Y', time());
  else if (!preg_match("{([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})}",  $list_start_date, $parts))
    $list_start_date = date('m/d/Y', time());
  else if (!checkdate($parts[1], $parts[2], $parts[3]))
    $list_start_date = date('m-d-Y', time());
}
$list_start_date = quotesqldata($list_start_date);

$query = "SELECT events.event_id, events.e_name, events.e_begindate, "
  . " events.e_location_name, events.e_status, events.e_display, "
  . " events.e_begintime "
  . " FROM events "
  . " WHERE  "
  . " events.e_begindate >= "
  .  " '" . MySqlDate($list_start_date) . "' "
  . " AND (events.event_id LIKE '" . $ClubCode . "%')"
  . " ORDER BY events.e_begindate, events.e_begintime ";


if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$e_recs = array();

// suck in event data
while ($e_row = mysqli_fetch_array($result)) {
  $e_id = $e_row['event_id'];

  // don't include MembersOnly events not in this club
  if (!strstr($e_id, $ClubCode) && ($e_row['e_display'] == 'MembersOnly'))
    continue;

  $e_row['attendee_sum'] = 0;
  $e_row['attending'] = 0;
  $r_query = "SELECT  "
    . " reserve.reserve_id, reserve.event_id, "
    . " reserve.cust_id, reserve.r_attending, "
    . " reserve.r_num_guests "
    . " FROM reserve "
    . " WHERE reserve.event_id = "
    .  " '" . $e_id . "' ";
  if (!($r_result = @mysqli_query($connection, $r_query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  while ($r_row = mysqli_fetch_array($r_result)) {
    // same event, increment attendee count?
    if ($r_row['r_attending'] == 'Yes') {
      $e_row['attendee_sum'] += 1;
      if (isset($r_row['r_num_guests']) && $r_row['r_num_guests'] > 0)
        $e_row['attendee_sum'] += $r_row['r_num_guests'];
    }
    // is current user attending this event?
    if ($r_row['cust_id'] == $cust_id)
      $e_row['attending'] = $r_row['r_attending'];
  }
  $e_recs[$e_id] = $e_row;
}

// get admin level
$admin_level = 0;
$AuthLevel = AuthLevel($loginUsername);
if ($AuthLevel == "Admin")
  $admin_level = 1;

$week = 0;
foreach ($e_recs as $e_row) {
  $event_id = $e_row["event_id"];
  $weekstring = "";
  $eventweek = date('W', strtotime($e_row["e_begindate"]));
  // start a new week with a horizontal rule
  if ($week != $eventweek) {
    $week = $eventweek;
    $weekstring = "<tr><td colspan=8><hr></td></tr>\n";
  }

  // preset string with event day of week: Mon, Tues, etc
  $day = date('D', strtotime($e_row["e_begindate"]));

  $begintime = $e_row["e_begintime"];
  $begintime = date('h:ia', strtotime($begintime));

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

    $str = $weekstring . "<tr>";

    // event id
    if ($admin_level) {
      $str .= "<td nowrap>" . $e_row["event_id"]
        . "</td><td><a href='deleteevents.php?event_id=" .
        $e_row['event_id'] . "'>Delete</a></td>";
    }

    // Display Details flag
    if ($admin_level || ($AuthLevel == 'Leader')) {
      $str .= "<td nowrap>";
      if ($e_row["e_display"] == 'MembersOnly')
        $str .= 'M';
      else if ($e_row["e_display"] == 'Public')
        $str .= 'P';
      else
        $str .= '?';
      $str .= "</td>";
    }

    // event begin date
    $str .= "<td nowrap>";
    $str .= $col_start_color;
    $str .= formatDate2($e_row["e_begindate"]);
    $str .= $col_end_color;
    $str .= "</td>";

    // event begin day of week (Mon, Tues, Wed, etc)
    $str .= "<td nowrap>";
    $str .= $col_start_color;
    $str .= $day;
    $str .= $col_end_color;
    $str .= "</td>";

    // event begin time
    $str .= "<td nowrap>";
    $str .= $col_start_color;
    $str .= $begintime;
    $str .= $col_end_color;
    $str .= "</td>";

    // event attendee count column
    $str .= "<td nowrap>";
    $str .= $col_start_color;
    if (isset($e_row['attendee_sum'])) {
      $str .= "<center>" . $e_row['attendee_sum'] . "</center>";
    }
    $str .= $col_end_color;
    $str .= "</td>";

    // event name link
    $EventName = $e_row["e_name"];
    $str .= "<td>";
    $str .= $col_start_color;
    $str .= "<a href=\"/members/eview.php?event_id="
      . $e_row["event_id"]
      . "\">"
      //."&mode=edit\">"
      . $EventName
      . "</a>";
    $str .= $col_end_color;
    $str .= "</td>";

    $str .= "<td>";
    $str .= $col_start_color;
    $str .= $e_row["e_location_name"];
    $str .= $col_end_color;
    $str .= "</td>";


    $str .= "</tr>";
    $strings[$event_id] = $str;
  } // end foreach 
} // end while

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Event List ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent">
  <hr>
  <form method="POST" action="/admin/elist.php">
    <table>
      <tr>
        <td nowrap>
          &nbsp;&nbsp;Start Date&nbsp;<input type=text name=list_start_date value="<?php echo $list_start_date ?>" size=10 maxlength=10>
          &nbsp;
          <a href="javascript:showCal('eStartDate', 150, 500)">
            <img src="/images/date.gif" width="19" height="17" border="0" alt="Select date"></a>
        </td>
        <td nowrap>
          &nbsp;<input type="submit" value="Submit">
        </td>
      </tr>
    </table>
  </form>
  <hr>
  <form method="POST" action="/admin/elist.php">
    <table>
      <tr>
        <td>E-Id</td>
        <td>&nbsp;</td>
        <td>Disp</td>
        <td>Date</td>
        <td>Day</td>
        <td>Time</td>
        <td>RSVP</td>
        <td>Event Name/Location</td>
      </tr>
      <?php
      if (!empty($strings))
        foreach ($strings as $str) {
          echo $str . "\n";
        }
      ?>
    </table>
  </form>

  <h3>Key</h3>
  <ul>
    <li><b>
        <font color="green">Green/Bold</font>
      </b>: Yes, you are signed up</li>
    <li>
      <font color="orange">Orange: Waiting List, I want to attend!</font>
    </li>
    <li>
      <font color="red">Red: Comments from the Peanuts Gallery.</font>
    </li>
    <li><i>Italics: Proposed event, subject to change, cancelation.</i></li>
    <li><strike>Strike-Through: event has been CANCELED</strike></li>
    <li><i><strike>Italics/strike through: event is hidden from normal members</strike></i></li>
    <li>Disp: P means public can view event details</li>
    <li>Disp: M means only members can view event details</li>
  </ul>
</div>

<?php
require('footer.php');
?>