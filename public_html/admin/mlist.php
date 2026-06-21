<?php
/*
AdventureClub.info

*/
// display list of events
// user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

require('mstats.php');

$ClubCode           = GetParameter('ClubCode');
$ClubCompanyName    = GetParameter('ClubCompanyName');

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

// get and validate the show all/active members display switch
$ShowFilter = isset($_GET['show']) ? clean($_GET['show']) : "";
$ShowFilter = substr(trim($ShowFilter), 0, 10);
$ShowFilter = quotesqldata($ShowFilter);

$ShowFilterSql = "";
if (strtolower($ShowFilter) != 'all')
  $ShowFilterSql = " members.u_date_expiration >= now() ";

if (empty($errors)) {
  mysqlconnect($connection);

  // obtain member information from members table
  $cust_id = getCustomerID($loginUsername);

  if ($cust_id == NULL) {
    $message = "Error: Invalid Customer ID!\n";
    trigger_error("Error cust_id is NULL", E_USER_ERROR);
    exit;
  } else {
    // obtain list of members

    $sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;
    switch ($sort) {
      case 'last':
        $order_by = "ORDER BY members.m_lastname, members.m_firstname";
        break;
      case 'id':
        $order_by = "ORDER BY members.cust_id";
        break;
      case 'user':
        $order_by = "ORDER BY members.user_name";
        break;
      case 'status':
        $order_by = "ORDER BY m_memberstatus, members.m_firstname, members.m_lastname ASC";
        break;
      case 'auth':
        $order_by = "ORDER BY members.u_auth_level, members.m_firstname, members.m_lastname ASC";
        break;
      case 'exp':
        $order_by = "ORDER BY members.u_date_expiration, members.m_firstname, members.m_lastname";
        break;
      case 'login':
        $order_by = "ORDER BY members.u_date_last_login, members.m_firstname, members.m_lastname";
        break;
      case 'joined':
        $order_by = "ORDER BY m_date_joined, members.m_firstname, members.m_lastname";
        break;
      case 'payment':
        $order_by = "ORDER BY m_pay_method, members.m_firstname, members.m_lastname";
        break;
      default:
        $order_by = "ORDER BY members.m_firstname, members.m_lastname";
        break;
    }

    $query = "SELECT members.cust_id, members.m_firstname, "
      . " members.m_lastname, "
      . " members.m_memberstatus, "
      . " members.m_email, "
      . " members.m_phonemobile, "
      . " members.m_date_joined, "
      . " members.user_name, "
      . " members.u_date_expiration, "
      . " members.m_pay_method, "
      . " members.u_auth_level, "
      . " members.u_date_last_login "
      . " FROM members "
      . " WHERE (m_club='" . $ClubCode . "')"
      . (empty($ShowFilterSql) ? "" : " and " . $ShowFilterSql)
      . " " . $order_by;
    if ($debug) {
      LogMsg("admin/mlist.php query: " . $query);
    }

    if (!($result = @mysqli_query($connection, $query)))
      trigger_error(
        "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
        E_USER_ERROR
      );

    // suck in member data
    $header_str = "<tr>"
      . "<td nowrap>"
      . "<a href=/admin/mlist.php?show=" . $ShowFilter . ">"
      . "First Name"
      . "</a>"
      . "</td>"

      . "<td nowrap>"
      . "<a href=/admin/mlist.php?sort=last&show=" . $ShowFilter . ">"
      . "Last Name"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=exp&show=" . $ShowFilter . ">"
      . "Expiration"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=status&show=" . $ShowFilter . ">"
      . "Status"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=payment&show=" . $ShowFilter . ">"
      . "Payment"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=id&show=" . $ShowFilter . ">"
      . "MembID"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=user&show=" . $ShowFilter . ">"
      . "Username"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=email&show=" . $ShowFilter . ">"
      . "EMail"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=auth&show=" . $ShowFilter . ">"
      . "AuthLvl"
      . "</a>"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=login&show=" . $ShowFilter . ">"
      . "LastLogin"
      . "</a>"
      . "</td>"

      . "<td>"
      . "MobilePhone"
      . "</td>"

      . "<td>"
      . "<a href=/admin/mlist.php?sort=joined&show=" . $ShowFilter . ">"
      . "Joined"
      . "</a>"
      . "</td>"

      . "</tr>";

    $header_str2 = "<tr>"
      . "<td nowrap>"
      . "View Info"
      . "</td>"

      . "<td nowrap>"
      . "Edit Info"
      . "</td>"

      . "<td>"
      . "Date"
      . "</td>"

      . "<td>"
      . ""
      . "</td>"

      . "<td>"
      . ""
      . "</td>"

      . "<td>"
      . "ChngPswd"
      . "</td>"

      . "<td>"
      . "SwitchUser"
      . "</td>"

      . "<td>"
      . "EmailAddress"
      . "</td>"

      . "<td>"
      . ""
      . "</td>"

      . "<td>"
      . ""
      . "</td>"

      . "<td>"
      . ""
      . "</td>"

      . "<td>"
      . "Date"
      . "</td>"

      . "</tr>";


    $count_logins = 0;
    $count_valid_logins = 0;
    $count_active_logins = 0;
    $count_leader_logins = 0;
    $count_free_logins = 0;
    $count_paid_logins = 0;
    while ($m_row = mysqli_fetch_array($result)) {

      $cust_id = $m_row["cust_id"];
      $str = // first name / view member info
        "<tr>"
        . "<td nowrap>"
        . "<a href=/members/mview.php?cust_id="
        . $m_row["cust_id"]
        . ">"
        .   $m_row["m_firstname"]
        . "</a>"
        . "</td>"

        // last name / edit member info
        . "<td nowrap>"
        . "<a href=/members/medit.php?cust_id="
        . $m_row["cust_id"]
        . "#AdminStart>"
        .   $m_row["m_lastname"]
        . "</a>"
        . "</td>"

        // account expiration date
        . "<td nowrap>"
        .   substr($m_row["u_date_expiration"], 0, 10)
        . "</td>"

        // memberstatus / email mailto link
        . "<td nowrap>"
        .   $m_row["m_memberstatus"]
        . "</td>"

        // payment method
        . "<td nowrap>"
        .   $m_row["m_pay_method"]
        . "</td>"

        // customer id / change users password
        . "<td nowrap>"
        . "<a href=/members/passwdchng.php?cust_id="
        .   $m_row["cust_id"]
        //.' target="chngpwd" '
        . ">"
        . $m_row["cust_id"]
        . "</a>"
        . "</td>"

        // username / switch to (login as) user
        . "<td nowrap>"
        . "<a href=/admin/switchuser.php?cust_id="
        . $m_row["cust_id"]
        . ">"
        .   $m_row["user_name"]
        . "</a>"
        . "</td>"

        // email address / email mailto link
        . "<td nowrap>"
        . '<a href="mailto:' . $m_row['m_email'] . '"'
        . ">"
        .   $m_row["m_email"]
        . "</a>"
        . "</td>"

        // account authorization level
        . "<td nowrap>"
        .   $m_row["u_auth_level"]
        . "</td>"

        // last login date
        . "<td nowrap>"
        .   $m_row["u_date_last_login"]
        . "</td>"

        // mobile phone number
        . "<td nowrap>"
        .   $m_row["m_phonemobile"]
        . "</td>"

        // date member joined the club
        . "<td nowrap>"
        .   substr($m_row["m_date_joined"], 0, 10)
        . "</td>"

        . "<td><a href=\"deletemembers.php?cust_id=" . $cust_id . "\">Delete</a></td>"
        . "</tr>";

      //echo $str . "<br>\n";
      $strings[$cust_id] = $str;
      //break;
    }
  }
}

$mStats = MemberStatistics();

$FileName = __FILE__;
$WebPageTitle = 'Admin Members List ' . $ClubCompanyName;
$admin = true;
require('top.php');

?>

<hr>
<div id="centercontent">
  <table border=1>
    <col span="1" align="right">

    <?php

    echo $header_str . "\n";
    echo $header_str2 . "\n";

    foreach ($strings as $str) {
      echo $str . "\n";
    }

    ?>

  </table>

  <ul>
    <li>Total Logins: <? echo $mStats['count_logins']; ?></li>
    <li>Unexpired Logins: <? echo $mStats['count_unexpired_logins']; ?></li>
    <li>Unexpired Active Logins: <? echo $mStats['count_unexpired_active_logins']; ?></li>
    <li>Unexpired Leader Logins: <? echo $mStats['count_unexpired_leader_logins']; ?></li>
    <li>Unexpired Free Logins: <? echo $mStats['count_unexpired_free_logins']; ?></li>
    <li>Unexpired Paid Logins: <? echo $mStats['count_unexpired_paid_logins']; ?></li>
    <li>Unexpired Male Logins: <? echo $mStats['count_unexpired_male_logins']; ?></li>
    <li>Unexpired Female Logins: <? echo $mStats['count_unexpired_female_logins']; ?></li>
    <li>Percent unexpired Male Logins: <? echo $mStats['percent_unexpired_male_logins']; ?>%</li>
    <li>Percent unexpired Female Logins: <? echo $mStats['percent_unexpired_female_logins']; ?>%</li>
    <li>Average Unexpired Age: <? echo $mStats['average_unexpired_age']; ?> years</li>
    <li>Age Std Dev: <? echo $mStats['age_std_dev']; ?> years</li>
    <li>66% of unexpired Members are between&nbsp;
      <?
      echo ($mStats['average_unexpired_age'] - $mStats['age_std_dev']);
      echo '&nbsp;and &nbsp;';
      echo ($mStats['average_unexpired_age'] + $mStats['age_std_dev']);
      ?>
      years of age.
    </li>
  </ul>

  <ul>
    <li>Click column title to sort by that column</li>
    <li>Select FIRST NAME to VIEW member info</li>
    <li>Select LAST NAME to EDIT member info</li>
    <li>Select Id to change members password</li>
    <li>Select USERNAME to switch (login) as that user</li>
    <li>Status link is email link</li>
  </ul>
</div>

<?php

require('footer.php');

?>