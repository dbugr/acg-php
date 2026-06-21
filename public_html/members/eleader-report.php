<?php
// eleader-report
// show event leader complete status of all upcoming events

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


/////////////// utility functions
// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberData($cust_id)
{
  global $connection;

  $member_row = array();
  $member_row[] = "Unknown";
  if ($cust_id > 0) {
    $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";
    if (!($result1 = @mysqli_query($connection, $query1)))
      trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

    if (!($member_row = mysqli_fetch_array($result1))) {
    }
  }
  return ($member_row);
}
// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
  global $connection;

  $name = "Unknown";
  if ($cust_id > 0) {
    $query1 = "SELECT m_firstname,m_lastname FROM members WHERE (cust_id='" . $cust_id . "');";

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
  if ($cust_id > 0) {
    $query1 = "SELECT m_email FROM members  WHERE (cust_id='" . $cust_id . "');";

    if (!($result1 = @mysqli_query($connection, $query1)))
      trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
    if (($memberrow = mysqli_fetch_array($result1)))
      $email = $memberrow["m_email"];
  }
  return ($email);
}

////////////// start of script

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: http://" . $_SERVER['HTTP_HOST'] . "/login.php";
  header($loc);
  exit;
}

// connection to database
mysqlconnect($connection);

// Is the user an admin or leader?
$loginUsername = LoginUsername();
$cust_id = getCustomerID($loginUsername, $connection);
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
if (!($AdminLevel) and !($LeaderLevel)) {
  // Register a message to show the user
  SessionRegister("message", $message);
  $message = "Error: you "
    . "do not have sufficient privilages to "
    . "view this information.";

  $loc = "Location: http://" . $_SERVER['HTTP_HOST']  . "/members/elist.php";
  header($loc);
  exit;
}

$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Event Leaders Report - ' . $ClubCompanyName;
require('top.php');


?>

<div id="centercontent">

  <h2>Event Leader Report</h2>
  <!-- <p><a href="javascript:window.print()">Send To Printer</a></p> -->
  <!-- <p><a href="/members/elist.php">Events Listing</a></p> -->

  <?php
  // get list of all future events for this leader
  $query = "SELECT e_name,e_begindate,e_begintime,e_location_name,event_id FROM events " .
    "WHERE ((leader_id='" . $cust_id . "') AND (e_begindate>='" . date('Y-m-d', time()) . "')) " .
    "ORDER BY events.e_begindate";
  if (!($resultL = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
  echo "<table border=1>";
  while ($levents = mysqli_fetch_array($resultL)) {
    echo "<tr><td colspan=7>&nbsp;<br><b>"
      . '<a href="/members/eview.php?event_id='
      . $levents['event_id']
      . '" target="eview">'
      . $levents['e_name'] . "</a></b><br>";
    echo $levents['e_location_name'] . " on "
      . date('D M j', strtotime($levents['e_begindate'])) . " at " . $levents['e_begintime'] . "</td></tr>";
    $event_id = $levents['event_id'];


    // obtain member contact information
    $query = "SELECT members.cust_id, 
        members.m_firstname, members.m_lastname,
        members.m_email, members.m_email2,
        members.m_phonehome, members.m_phonework, 
        members.m_phonemobile, members.m_phonemisc,
        reserve.cust_id, reserve.r_attending, 
				reserve.r_amount_paid, reserve.r_date_paid, reserve.r_num_guests
        FROM members 
        LEFT JOIN reserve
        ON members.cust_id = reserve.cust_id
        WHERE ((event_id='" . $event_id . "') AND ( reserve.r_attending = 'Yes'))
		ORDER BY members.m_firstname, members.m_lastname;";
    if (($contactresult = @mysqli_query($connection, $query))) {
      if (mysqli_num_rows($contactresult) > 0) {
        echo "<tr><td align=center colspan=6>Signed up</td></tr>";
        echo "<tr>";
        echo "<td>Name</td>";
        echo "<td>EMail</td>";
        echo "<td>MobilePhone</td>";
        echo "<td>HomePhone</td>";
        echo "<td>WorkPhone</td>";
        echo "<td>Guests</td>";
        echo "</tr>\n";
      }
      while ($contact_row = mysqli_fetch_array($contactresult)) {
        echo "<tr>";
        echo '<td nowrap><a href="/members/mview.php?cust_id='
          . $contact_row['cust_id']
          . '" target="contact">'
          . $contact_row['m_firstname'] . " "
          . $contact_row['m_lastname'] . "</a></td>";
        echo '<td nowrap><a href="mailto:'
          . $contact_row['m_email'] . '">'
          . $contact_row['m_email'] . "</a></td>";
        echo "<td nowrap>" . $contact_row['m_phonemobile'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_phonehome'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_phonework'] . "</td>";
        echo "<td nowrap>" . $contact_row['r_num_guests'] . "</td>";
        echo "</tr>\n";

        $amount = $contact_row['r_amount_paid'];
        if ($amount > 0) {
          echo "<tr><td></td><td colspan=5>Has paid $" . $contact_row['r_amount_paid'] . " through " . date('D M j', strtotime($contact_row['r_date_paid'])) . "</td></tr>";
        }
      }
    }

    // obtain member contact information
    $query = "SELECT members.cust_id, 
        members.m_firstname, members.m_lastname,
        members.m_email, members.m_email2,
        members.m_phonehome, members.m_phonework, 
        members.m_phonemobile, members.m_phonemisc,
        reserve.cust_id, reserve.r_attending, 
				reserve.r_amount_paid, reserve.r_date_paid, reserve.r_num_guests
        FROM members 
        LEFT JOIN reserve
        ON members.cust_id = reserve.cust_id
        WHERE ((event_id='" . $event_id . "') AND ( reserve.r_attending = 'WaitingList'));" .
      " ORDER BY reserve.r_date_reserved";
    if (($contactresult = @mysqli_query($connection, $query))) {
      if (mysqli_num_rows($contactresult) > 0) {
        echo "<tr><td align=center colspan=6><i>Waiting List</i></td></tr>";
        echo "<tr>";
        echo "<td>Name</td>";
        echo "<td>EMail</td>";
        echo "<td>MobilePhone</td>";
        echo "<td>HomePhone</td>";
        echo "<td>WorkPhone</td>";
        echo "<td>Guests</td>";
        echo "</tr>\n";
      }
      while ($contact_row = mysqli_fetch_array($contactresult)) {
        echo "<tr>";
        echo "<td nowrap>" . $contact_row['m_firstname'] . " " . $contact_row['m_lastname'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_email'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_phonemobile'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_phonehome'] . "</td>";
        echo "<td nowrap>" . $contact_row['m_phonework'] . "</td>";
        echo "<td nowrap>" . $contact_row['r_num_guests'] . "</td>";
        echo "</tr>\n";
        $amount = $contact_row['r_amount_paid'];
        if ($amount > 0) {
          echo "<tr><td></td><td colspan=5>Has paid $" . $contact_row['r_amount_paid'] . " through " . date('D M j', strtotime($contact_row['r_date_paid'])) . "</td></tr>";
        }
      }
    }
  }
  echo "</table>";
  ?>

</div>

<?php
require('footer.php');
?>