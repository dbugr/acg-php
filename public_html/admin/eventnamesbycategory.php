<?php
// display event categories by month


require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

require('admin.php');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in!";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /login.php";
  header($loc);
  exit;
}

// get levels  
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';

if (!$AdminLevel) {
  // redirect to the home page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}

foreach ($_POST as $varname => $value)
  $formVars[$varname] = clean($value);
foreach ($_GET as $varname => $value)
  $formVars[$varname] = clean($value);

// connect to database
mysqlconnect($connection);

// get event names by month
$months = array();
for ($m = 1; $m <= 12; $m++)
  $months[$m] = "";
$query = "SELECT e_name,event_id,e_begindate FROM events WHERE e_category='" . $formVars['category'] . "'";
if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
while ($e_row = mysqli_fetch_array($result)) {
  $m = date('n', $e_row['e_begindate']);
  $months[$m] .= "<a href='/members/eview.php?event_id=" . $e_row['event_id'] . "'>" . $e_row['e_name'] . "</a><br>";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
  <title>Event names by category and month</title>
  <link rel=STYLESHEET href="/inc/admin.css" Type="text/css">
</head>

<body>
  <div id="centercontent">
    <hr>
    <p>Event names for <?php echo $formVars['category']; ?> by month</p>
    <table border=1>
      <tr>
        <th>Month</th>
        <th>Events</th>
      </tr>
      <?php

      for ($k = 1; $k <= 12; $k++) {
        echo "<tr><td>" . $k . "</td><td>" . $months[$m] . "</td></tr>";
      }
      ?>
    </table>
  </div>

  <?php TopBanner("Event names for " . $formVars['category'] . " by month"); ?>
</body>

</html>