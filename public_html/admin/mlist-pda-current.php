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

$ClubCode      = GetParameter('ClubCode');


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
  $loc = "Location: /login.php";
  header($loc);
  exit;
}


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
    /*        $query = "SELECT members.*, "
                 ." users.user_name " 
                 ." FROM members, users "
                 ." where members.cust_id = users.cust_id "
                 ." order by m_firstname";
       */

    $sort = isset($_GET['sort']) ? clean($_GET['sort']) : NULL;
    switch ($sort) {
      case 'last':
        $order_by = "ORDER BY members.m_lastname, members.m_firstname";
        break;
      case 'id':
        $order_by = "ORDER BY members.cust_id";
        break;
      case 'user':
        $order_by = "ORDER BY users.user_name";
        break;
      case 'status':
        $order_by = "ORDER BY m_memberstatus, members.m_firstname, members.m_lastname ASC";
        break;
      case 'auth':
        $order_by = "ORDER BY users.u_auth_level, members.m_firstname, members.m_lastname ASC";
        break;
      case 'exp':
        $order_by = "ORDER BY users.u_date_expiration, members.m_firstname, members.m_lastname";
        break;
      case 'login':
        $order_by = "ORDER BY users.u_date_last_login, members.m_firstname, members.m_lastname";
        break;
      case 'joined':
        $order_by = "ORDER BY m_date_joined, members.m_firstname, members.m_lastname";
        break;
      default:
        $order_by = "ORDER BY members.m_firstname, members.m_lastname";
        break;
    }

    $query = "SELECT members.cust_id, members.m_firstname, "
      . " members.m_lastname, members.m_memberstatus, "
      . " members.m_email, members.m_phonehome, "
      . " members.m_phonemobile, "
      . " members.m_date_joined, "
      . " members.user_name, "
      . " members.u_date_expiration, members.u_auth_level, "
      . " members.u_date_last_login "
      . " FROM members "
      . " WHERE (m_club='" . $ClubCode . "')"
      . " AND members.u_date_expiration >= now() "
      . " " . $order_by;

    //   ." ORDER BY m_firstname, m_lastname";


    if (!($result = @mysqli_query($connection, $query)))
      trigger_error(
        "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
        E_USER_ERROR
      );

    // suck in member data
    $header_str = "<tr>"
      . "<td nowrap>"
      . "<a href=/admin/mlist.php>"
      . "First Name"
      . "</a>"
      . "</td>"

      . "<td nowrap>"
      . "<a href=/admin/mlist.php?sort=last>"
      . "Last Name"
      . "</a>"
      . "</td>"
      . "<td>"
      . "HomePhone"
      . "</td>"
      . "<td>MobilePhone</td>"
      //."<td>eMail</td>"

      . "</tr>";

    $header_str2 = "";

    $count_logins = 0;
    $count_valid_logins = 0;
    $count_active_logins = 0;
    $count_leader_logins = 0;
    $count_free_logins = 0;
    $count_paid_logins = 0;
    while ($m_row = mysqli_fetch_array($result)) {

      $cust_id = $m_row["cust_id"];
      $str = // view member info
        "<tr>"
        . "<td nowrap>"
        . "<a href=/members/mview.php?cust_id="
        . $m_row["cust_id"]
        . ">"
        .   $m_row["m_firstname"]
        . "</a>"
        . "</td>"

        // edit member info
        . "<td nowrap>"
        . "<a href=/members/medit.php?cust_id="
        . $m_row["cust_id"]
        . ">"
        .   $m_row["m_lastname"]
        . "</a>"
        . "</td>"

        . "<td nowrap>"
        .   $m_row["m_phonehome"]
        . "</td>"

        . "<td nowrap>"
        .   $m_row["m_phonemobile"]
        . "</td>"

        //."<td>" . $m_row["m_email"] . "</td>"
        . "</tr>";

      //echo $str . "<br>\n";
      $strings[$cust_id] = $str;
      //break;
    }
  }
}

$mStats = MemberStatistics();
$ClubCompanyName	= GetParameter('ClubCompanyName');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
  <title>Members List</title>
</head>

<body>

  <h1><a href="/">
      <?php echo $ClubCompanyName; ?> home</a></h1>

  <h2><a href="/admin/">
      <?php echo $ClubCompanyName; ?> Admin</a></h2>

  <h2>Members List - PDA</h2>

  <div id="centercontent">
    <table border=1 width="80%">
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

</body>

</html>