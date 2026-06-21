<?php
// event leader page to show contact information for event attendees

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


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

// connect to database
mysqlconnect($connection);

// obtain member information from members table
$loginUsername = LoginUsername();
$cust_id = getCustomerID($loginUsername, $connection);

// event_id may be part of URL
if (isset($_GET['event_id']))
  $event_id = clean($_GET['event_id']);
if (!isset($event_id)) {
  trigger_error("event id not set", E_USER_ERROR);
  exit;
}
$event_id = quotesqldata($event_id);

// go get event
$query = "SELECT * FROM events WHERE (event_id='" . $event_id . "');";
if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
$row = mysqli_fetch_array($result);

$leader_id = $row['leader_id'];
$user_owns_event = ($leader_id == $cust_id);

// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
if ((!($AdminLevel) and !($LeaderLevel)) and !($user_owns_event)) {
  // Register a message to show the user
  $message = "Error: you do not have sufficient privilages to view this information.";
  SessionRegister("message", $message);
  $loc = "Location: http://" . $_SERVER['HTTP_HOST'] . "/members/elist.php";
  header($loc);
  exit;
}

// Reset $formVars, since we're loading from the event/reserve/members tables
$formVars = array();

// Reset the errors
$errors = array();

// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
  global $connection;

  $name = "Unknown";
  $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "')
    ORDER BY members.m_firstname,members.m_lastname;";
  if (!($result1 = @mysqli_query($connection, $query1)))
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
  if (!($result1 = @mysqli_query($connection, $query1)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
  if (($memberrow = mysqli_fetch_array($result1)))
    $email = $memberrow["m_email"];
  return ($email);
}

// get everyone attending this event
$query = "SELECT members.cust_id,
        members.m_firstname, members.m_lastname,
        members.m_email, members.m_email2,
        members.m_phonehome, members.m_phonework,
        members.m_phonemobile, members.m_phonemisc,
        reserve.cust_id, reserve.r_attending
        FROM members
        LEFT JOIN reserve
        ON members.cust_id = reserve.cust_id
        WHERE ((event_id='" . $event_id . "') AND (
        reserve.r_attending = 'Yes' OR reserve.r_attending = 'WaitingList'))
        ORDER BY members.m_firstname,members.m_lastname";

if (!($contactresult = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$contact_header_string =
  "<tr>" .
  "<td nowrap>Name</td>" .
  "<td nowrap>EMail</td>" .
  "<td nowrap>MobilePhone</td>" .
  "<td nowrap>HomePhone</td>" .
  "<td nowrap>WorkPhone</td>" .
  "<td nowrap>MiscPhone</td>" .
  "</tr>\n";

$email_string = "";
$name_string = "";
while ($contact_row = mysqli_fetch_array($contactresult)) {
  $contact_str = "<tr>" .
    "<td nowrap><a href='/members/mview.php?cust_id=" .
    $contact_row['cust_id'] . "'>" .
    $contact_row['m_firstname'] . " " . $contact_row['m_lastname'] . "</a></td>" .
    "<td nowrap>" . $contact_row['m_email'] . "</td>" .
    "<td nowrap>" . $contact_row['m_phonemobile'] . "</td>" .
    "<td nowrap>" . $contact_row['m_phonehome'] . "</td>" .
    "<td nowrap>" . $contact_row['m_phonework'] . "</td>" .
    "<td nowrap>" . $contact_row['m_phonemisc'] . "</td>" .
    "</tr>\n";
  $contact_strings[] = $contact_str;
  $email_string .= !empty($email_string) ? "," : "";
  $email_string .= $contact_row['m_email'];

  $name_string .= !empty($name_string) ? ", " : "";
  $name_string .= $contact_row['m_firstname'];
}

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName    = GetParameter('ClubCompanyName');
$WebPageTitle = 'Event Contact Info ' . $ClubCompanyName;
require('top.php');


?>

<div id="centercontent">
  <hr>

  <!-- begin upper details table -->
  <table width=450 border=1>

    <!-- display event name -->
    <tr>
      <th colspan="2">
            <?php
            echo $row["e_name"];
            ?>
          </b>
      </th>
    </tr>


    <!-- <tr><td><br></td><td></td></tr> -->

    <tr>
      <td align=right>Where:</td>
      <td><?php echo $row["e_location_name"]; ?></td>
    </tr>

    <tr>
      <td align=right>When:</td>
      <td>
        <?php
        echo $row["e_begindate"];
        $begintime = $row['e_begintime'];
        $begintime = date('g:ia', strtotime($begintime));
        echo "&nbsp;" . $begintime;
        ?>
      </td>
    </tr>

    <tr>
      <td align=right>Leader:</td>
      <td>
        <a href="/members/mview.php?cust_id=<?php echo trim($row['leader_id']); ?>" target="leader_profile">
          <?php echo GetMemberName($row["leader_id"]); ?>
        </a>
      </td>
    </tr>

    <tr>
      <td align=right>EMail:</td>
      <td><a href="mailto:<?php echo GetMemberEmail($row["leader_id"]); ?>" target="leader_email">
          <?php echo GetMemberEmail($row["leader_id"]); ?></a>
      </td>
    </tr>

  </table>
  <!-- end upper details table -->


  <?php
  echo "<p>&nbsp;</p><table>";
  echo "<tr><th colspan=2 align=left>eMail attendees</th></tr>";
  echo "<tr><td>To: </td><td><a href='mailto:" . $email_string . "'>" . $email_string . "</a></td></tr>";
  echo "<tr><td>Names: </td><td>" . $name_string . "</td></tr>";
  echo "</table>";

  echo "<h3>Attendee Contact Info:</h3>";
  echo "<table border=1>";
  if (isset($contact_strings)) {
    echo $contact_header_string;
    foreach ($contact_strings as $str)
      echo $str;
  }
  echo "</table>\n";

  if ($AdminLevel) {
    echo "<table border=0>";
    echo "<tr>";
    echo "<td>";

    // Get login cust_id
    $cust_id = getCustomerID($loginUsername, $connection);
    echo "<br>cust_id: " . $cust_id;
    echo "<br>leader_id: " . $row['leader_id'];
    echo "<br>event_id: " . $event_id;

    echo "</td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</table>\n";
  } //if AdminLevel
  ?>
</div>

<?php

require('footer.php');
//tail();

?>