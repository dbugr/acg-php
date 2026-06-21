<?php
/*
AdventureClub.info

At-Risk members report:
Show members who:
	have zero or one event reservations
	todays_date - date_joined < 60 days

*/

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');
//require('expired_membership.php');

$ClubCode      = GetParameter('ClubCode');


// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$MemberRetentionCoordinator = false;
if ($loginUsername == MemberRetentionLoginUsername()) {
  $AdminLevel = true;
  $MemberRetentionCoordinator = true;
}
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}


function CountReservations($cust_id)
{
  global $connection;

  $query =
    "SELECT count(*) as ReserveCount
FROM reserve
WHERE cust_id = '"
    . $cust_id
    . "'";

  //echo "Query: " . $query . "<br>";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );
  $m_row = mysqli_fetch_array($result);

  return ($m_row['ReserveCount']);
}


function DaysSinceJoined($date_joined)
{
  global $debug;


  $ts_today = time();
  if ($debug)
    echo 'ts_today: ' . $ts_today . '<br>';

  if ($debug)
    echo 'date_joined: ' . $date_joined . '<br>';

  $ts_date_joined = strtotime($date_joined);
  if ($debug)
    echo 'ts_date_joined: ' . $ts_date_joined . '<br>';

  $ts_diff = $ts_today - $ts_date_joined;
  if ($debug)
    echo 'ts_diff: ' . $ts_diff . '<br>';

  $diff = $ts_diff / (60 * 60 * 24);
  if ($debug)
    echo 'diff: ' . $diff . '<br>';
  return ($diff);
}


mysqlconnect($connection);

$days_past_default = 30; // default number of days past
$date_today = date('Y-m-d', time());

// get past days, reject incorrect dates
if (!isset($_POST['days_past'])) {
  $days_past = $days_past_default;
  $seconds_past = $days_past * 3600 * 24;
  $date_past = date('Y-m-d', time() - $seconds_past);
} else {
  $days_past = clean($_POST['days_past']);
  // Validate days past
  if (is_numeric($days_past)) {
    $seconds_past = $days_past * 3600 * 24;
    $date_past = date('Y-m-d', time() - $seconds_past);
  } else {
    // the begin date cannot be a null string
    $days_past = $days_past_default;
    $seconds_past = $days_past * 3600 * 24;
    $date_past = date('Y-m-d', time() - $seconds_past);
  }
}
$date_past = quotesqldata($date_past);


//$date_past = '2003-05-04' ;
//$date_future = '2003-07-04'; 

// obtain member information from members table
$cust_id = getCustomerID($loginUsername);

if ($cust_id == NULL) {
  $message = "Error: Invalid Customer ID!\n";
} else {

  // format (stringify) member data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "First Name"
    . "</td>"
    . "<td nowrap>"
    . "Last Name"
    . "</td>"
    . "<td nowrap>"
    . "Reserve"
    . "</td>"

    . "<td>"
    . "MemberStatus"
    . "</td>"
    . "<td>"
    . "DateLastLogin"
    . "</td>"
    . "<td>"
    . "DateJoined"
    . "</td>"
    . "<td>"
    . "DateExpiration"
    . "</td>"

    . "<td>"
    . "HomePhone"
    . "</td>"
    . "<td>"
    . "MobilePhone"
    . "</td>"
    . "<td>"
    . "Email"
    . "</td>"

    . "</tr>";

  $query =
    "SELECT members.cust_id, members.m_firstname, 
members.m_lastname, members.m_club,
members.m_email, members.u_date_expiration,
members.m_memberstatus,
members.m_phonehome,
members.m_phonemobile,
members.u_date_last_login, members.m_date_joined
FROM members
where members.m_club = '" . $ClubCode . "'
AND u_date_expiration >= " . $date_today . "
AND members.m_memberstatus <> 'Free'
AND members.m_memberstatus <> 'Canceled'
AND members.m_memberstatus <> 'Expired'
ORDER BY members.m_date_joined";
  //echo "Query: " . $query . "<br>";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );


  //$MemberExpirationDates = GetMemberExpirationDates();

  $strings = array();
  $email_string = "";
  $email_string2 = "";
  $name_string = "";
  while ($m_row = mysqli_fetch_array($result)) {
    if ($m_row['m_club'] != $ClubCode) continue;
    $cust_id = $m_row['cust_id'];
    $ReserveCount = CountReservations($cust_id);
    if ((($ReserveCount == 0) or
        ($ReserveCount == 1)) and
      (DaysSinceJoined($m_row['m_date_joined'])
        <= 90)
    )
    /*			if ( 	($ReserveCount == 0) OR
					(($ReserveCount == 1) AND
					((now() - date_joined >= 60 )) )
				) 
*/ {
      $email_string .= !empty($email_string) ? "," : "";
      $email_string .= $m_row['m_email'];
      $email_string2 .= !empty($email_string2) ? "<br>\n" : "";
      $email_string2 .= $m_row['m_email'];
      $name_string .= !empty($name_string) ? ", " : "";
      $name_string .= $m_row['m_firstname'];
      $str = "<tr>"

        . "<td nowrap>"
        .   $m_row["m_firstname"]
        . "</td>"

        // edit member info
        . "<td nowrap>";
      if ($MemberRetentionCoordinator) {
        $str .=
          $m_row["m_lastname"];
      } else {
        $str .= "<a href=\"/members/medit.php?cust_id="
          . $m_row["cust_id"]
          . "\" target=\"reservations\">"
          . $m_row["m_lastname"]
          . "</a>";
      }
      $str .=
        "</td>"

        . "<td nowrap>"
        .   $ReserveCount
        . "</td>"

        . "<td nowrap>"
        .   $m_row['m_memberstatus']
        . "</td>"

        . "<td nowrap>"
        .   substr(
          $m_row['u_date_last_login'],
          0,
          strpos($m_row['u_date_last_login'], ' ')
        )
        . "</td>"

        . "<td nowrap>"
        .   substr(
          $m_row['m_date_joined'],
          0,
          strpos($m_row['m_date_joined'], ' ')
        )
        . "</td>"

        . "<td nowrap>"
        .   substr(
          $m_row['u_date_expiration'],
          0,
          strpos($m_row['u_date_expiration'], ' ')
        )
        . "</td>"

        . "<td nowrap>"
        .   $m_row['m_phonehome']
        . "</td>"

        . "<td nowrap>"
        .   $m_row['m_phonemobile']
        . "</td>"

        . "<td nowrap>"
        .   '<a href="mailto:'
        .   $m_row['m_email']
        .   '">'
        .   $m_row['m_email']
        .   '</a>'
        . "</td>"

        . "</tr>";

      //echo $str . "<br>\n";
      $strings[] = $str;
      //break;
    } // end if
  } //end while
} // if





$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin At-Risk Members Report ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent">
  <hr>
  <h3>At-Risk members who need some encouragement:</h3>
  <p>Shows members who:</p>
  <ul>
    <li>Have zero or one event reservations</li>
    <li>Less than 90 days since joined club</li>
  </ul>

  <?php
  echo "<p>&nbsp;</p><table>";
  echo "<tr><th colspan=2 align=left>eMail Addresses</th></tr>";
  echo "<tr><td>To: </td><td><a href='mailto:" . $email_string . "'>" . $email_string . "</a></td></tr>";
  echo "<tr><td>Names: </td><td>" . $name_string . "</td></tr>";
  echo "</table>";
  ?>
  <br>

  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";
    foreach ($strings as $str) {
      echo $str . "\n";
    }
    ?>
  </table>

  <?php
  echo "<br>";
  echo "<p>eMail Addresses</p>";
  echo "<p>";
  echo $name_string;
  echo "</p>";
  echo "<p>";
  echo $email_string2;
  echo "</p>";
  ?>
  <br>


</div>

<?php
require('footer.php');
?>