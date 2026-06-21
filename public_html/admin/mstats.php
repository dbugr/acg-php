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

require('mstats.inc');

// Connect to a session
////session_start();

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
if ((!SessionIsRegistered("loginUsername")) or (!$AdminLevel)) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (mList) or "
    . "do not have sufficient privilages to "
    . "view this information.";
  SessionRegister("message", $message);

  trigger_error("Error not logged in or insufficient privilages (mlist)!", E_USER_ERROR);

  // Register where they came from
  SessionRegister("referer", $referer);
  $referer = __FILE__;

  // redirect to the login page
  header("Location: /members/login.php");
  exit;
}

$mStats = MemberStatistics();

?>




<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
  <title>Members Statistics</title>
  <link rel=STYLESHEET href="/include/admin.css" Type="text/css">
</head>

<body>

  <div id="banner">
    <h1>Adventure Club of Gainesville</h1>
  </div>
  <div id="banner2"><b>Administration Functions</b></div>
  <div id="banner3">Members Statistics</div>
  <div id="banner4">&nbsp;</div>

  <hr>

  <div id="leftcontent">
    <p>&nbsp;</p>
    <?php DisplayAdminMenu(); ?>
    <p>&nbsp;</p>
  </div>

  <div id="centercontent">
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

  </div>

  <div id="logo">
    <img src="/images/logo1.gif" width="152" height="143" alt="logo">
  </div>
</body>

</html>