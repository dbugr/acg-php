<?php
/*
AdventureClub.info

*/
// listing of member referral categories 
// with count of members per category

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
  // query for past events posted by each member
  $query = "
          select members.cust_id, members.m_firstname, 
          members.m_lastname, reserve.r_attending, 
          members.m_club,
          events.e_name 
          from members, reserve, events 
          where members.cust_id = reserve.cust_id 
          and reserve.event_id = events.event_id  
          and reserve.r_attending = 'Yes' 
          order by members.m_firstname, members.m_lastname";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "First Name"
    . "</td>"
    . "<td nowrap>"
    . "Last Name"
    . "</td>"
    . "<td>"
    . "Event Description"
    . "</td>"
    . "</tr>";


  $MemberExpirationDates = GetMemberExpirationDates();

  $strings = array();
  while ($m_row = mysqli_fetch_array($result)) {
    if ($m_row['m_club'] != $ClubCode) continue;
    $cust_id = $m_row['cust_id'];
    if (!expired_membership($MemberExpirationDates[$cust_id])) {
      $str = "<tr>"

        . "<td nowrap>"
        .   $m_row["m_firstname"]
        . "</td>"

        . "<td nowrap>"
        .   $m_row['m_lastname']
        . "</td>"

        . "<td nowrap>"
        .   $m_row['e_name']
        . "</td>"
        . "</tr>";

      //echo $str . "<br>\n";
      $strings[] = $str;
      //break;
    }
  } //end while

} // if


$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Member Event Registration List ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent">
  <hr>
  <table width="100">
    <tr>
      <td nowrap>
        <h2><i>AC Current Member YES Reservations Report</i>
        </h2>
      </td>
    </tr>
  </table>

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