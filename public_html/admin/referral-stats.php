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
  $today = date('Y-m-d h:i:s', time());
  $query = "SELECT
                  count(m_referral) as TotalReferrals
                  FROM members
                  WHERE (members.m_club='$ClubCode')  AND (m_memberstatus='Paid') AND " .
    "u_date_expiration >= '"
    . $today
    . "'";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );
  $mrow = mysqli_fetch_array($result);
  $total_referrals = $mrow['TotalReferrals'];
  //$total_referrals = 10;

  // query for referrals for each member when they joined
  $query = "SELECT "
    . "m_referral,"
    . "count(m_referral) / "
    . $total_referrals
    . " as ReferralPercent "
    //."u_date_expiration "
    . "FROM members "
    . "WHERE (members.m_club='$ClubCode')  AND (m_memberstatus='Paid') AND u_date_expiration >= '"
    . $today
    . "'"
    . " GROUP BY m_referral "
    . "ORDER BY ReferralPercent DESC";

  //SELECT m_referral,
  //count(m_referral) as ReferralCount 
  //FROM members
  //WHERE (members.m_club='gnv')  AND (m_memberstatus='Paid') AND u_date_expiration >= '2020-03-07 01:44:26'
  //GROUP BY m_referral
  //ORDER BY ReferralCount DESC
  //LogMsg($query);     
  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "Category"
    . "</td>"
    . "<td>"
    . "Referral Percentages"
    . "</td>"
    . "</tr>";

  $strings = array();

  while ($m_row = mysqli_fetch_array($result)) {

    $str = "<tr>"

      . "<td nowrap>"
      .   $m_row["m_referral"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row['ReferralPercent']
      . "</td>"

      . "</tr>";

    //echo $str . "<br>\n";
    $strings[] = $str;
    //break;
  } //end while

  // current AND former members
  $query = "SELECT
                  count(m_referral) as TotalReferrals
                  FROM members WHERE (members.m_club='$ClubCode')  AND (m_memberstatus='Paid') ";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );
  $mrow = mysqli_fetch_array($result);
  $total_referrals2 = $mrow['TotalReferrals'];
  //$total_referrals = 10;

  // query for past events posted by each member
  $query = "SELECT "
    . "m_referral,"
    . "count(m_referral) / "
    . $total_referrals2
    . " as ReferralPercent "
    . "FROM members WHERE (members.m_club='$ClubCode')  AND (m_memberstatus='Paid') "
    . " GROUP BY m_referral "
    . "ORDER BY ReferralPercent DESC";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  //$header_str = "<tr>"
  //       ."<td nowrap>"
  //       ."Category"
  //       ."</td>"
  //       ."<td>"
  //       ."Referral Percentages"
  //       ."</td>"
  //       ."</tr>";

  $strings2 = array();

  while ($m_row = mysqli_fetch_array($result)) {

    $str = "<tr>"

      . "<td nowrap>"
      .   $m_row["m_referral"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row['ReferralPercent']
      . "</td>"

      . "</tr>";

    //echo $str . "<br>\n";
    $strings2[] = $str;
    //break;
  } //end while


  // query for ALL referral details
  $query = "SELECT "
    . "m_firstname,"
    . "m_lastname,"
    . "m_date_joined,"
    . "m_referral,"
    . "m_phonehome,"
    . "m_referral_detail, "
    . "m_discount_code "
    . "FROM members WHERE (members.m_club='$ClubCode') AND (m_memberstatus='Paid') "
    . "order by m_date_joined desc";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  $header_str3 = "<tr>"
    . "<td nowrap>"
    . "First"
    . "</td>"
    . "<td>"
    . "Last"
    . "</td>"
    . "<td>"
    . "Joined"
    . "</td>"
    . "<td>"
    . "HomePhone"
    . "</td>"
    . "<td>"
    . "Discount Code"
    . "</td>"
    . "<td nowrap>"
    . "Referral"
    . "</td>"
    . "<td>"
    . "Referral Details"
    . "</td>"
    . "</tr>";

  $strings3 = array();

  while ($m_row = mysqli_fetch_array($result)) {

    $str = "<tr>"

      . "<td nowrap>"
      .   $m_row["m_firstname"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row["m_lastname"]
      . "</td>"

      . "<td nowrap>"
      .   substr($m_row["m_date_joined"], 0, 10)
      . "</td>"

      . "<td nowrap>"
      .   $m_row["m_phonehome"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row['m_discount_code']
      . "</td>"

      . "<td nowrap>"
      .   $m_row["m_referral"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row['m_referral_detail']
      . "</td>"

      . "</tr>";

    //echo $str . "<br>\n";
    $strings3[] = $str;
    //break;
  } //end while



} // if

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Members Referral Statistics ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent2">
  <hr>
  <h3><?php echo $WebPageTitle; ?></h3>
  <p>Current Members...</p>

  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";
    foreach ($strings as $str) {
      echo $str . "\n";
    }
    ?>
  </table>

  <br>

  <?php
  echo "Total Referrals: " .  $total_referrals . "<br><br>";
  ?>

  <br>

  <p>Current and Former Members...</p>

  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";
    foreach ($strings2 as $str) {
      echo $str . "\n";
    }
    ?>
  </table>

  <br>

  <?php
  echo "Total Referrals: " .  $total_referrals2 . "<br><br>";
  ?>

  <br>

  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str3 . "\n";
    foreach ($strings3 as $str) {
      echo $str . "\n";
    }
    ?>
  </table>
</div>

<?php
require('footer.php');
?>