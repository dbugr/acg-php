<?php

// listing of member referral categories 
// with count of members per category

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

$ClubCode      = GetParameter('ClubCode');


// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
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


if (
  (!($AdminLevel) and
    !($LeaderLevel))
) {

  $loc = "Location: http://" . $_SERVER['HTTP_HOST']
    . "/members/elist.php";
  header($loc);
  exit;
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
  // current members stats
  $today = date('Y-m-d', time());

  // query for past events posted by each member
  $query = "SELECT 
                  m_firstname, 
                  m_lastname,
                  p_occupation
                  FROM members
                  WHERE (members.m_club='$ClubCode') AND p_occupation <> ''
                  order by p_occupation";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "FirstName"
    . "</td>"
    . "<td>"
    . "LastName"
    . "</td>"
    . "<td>"
    . "Occupation"
    . "</td>"
    . "</tr>";

  $strings = array();

  while ($m_row = mysqli_fetch_array($result)) {

    $str = "<tr>"

      . "<td nowrap>"
      .   $m_row["m_firstname"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row["m_lastname"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row['p_occupation']
      . "</td>"

      . "</tr>";

    //echo $str . "<br>\n";
    $strings[] = $str;
    //break;
  } //end while



} // if

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Event Leader - ' . $ClubCompanyName;
$admin = true;
require('top.php');

?>
<div id="centercontent2">
  <hr>
  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";
    foreach ($strings as $str) {
      echo $str . "\n";
    }
    ?>
  </table>
</div>

<?php
require('footer.php');
?>