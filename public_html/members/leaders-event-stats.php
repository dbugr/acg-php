<?php

// count number of events posted by
// each event leader during the PAST N days.
// and during the FUTURE N days

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (leaders-event-stats) or "
    . "do not have sufficient privilages to "
    . "view this information.";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect back where they came from
  // Do we need to redirect to a calling page?
  //if (SessionIsRegistered("referer"))
  //   {
  //       // Delete the referer session variable
  //    SessionUnregister("referer");

  //       // Then, use it to redirect
  //       header("Location: $referer");
  //       exit;
  //   }

  $loc = "Location: http://"
    . $_SERVER['HTTP_HOST']
    . "/login.php";
  header($loc);
  exit;
}
mysqlconnect($connection);


// obtain member information from members table
$cust_id = getCustomerID($loginUsername);

if ($cust_id == NULL) {
  $message = "Error: Invalid Customer ID!\n";
} else {
  $date_today = date('Y-m-d', time());
  // query for event leader names
  $query = "SELECT
                  cust_id,
                  m_firstname,
                  m_lastname,
                  m_memberstatus,
                  m_email,
                  u_date_expiration,
                  u_auth_level as AuthLevel
                  FROM members
                  WHERE
                  ((u_auth_level='Leader') OR (u_auth_level='Admin')) AND
                  (m_club='" . $ClubCode . "') AND u_date_expiration >= '"
    . $date_today
    . "'
				   ORDER BY m_memberstatus,
                    m_firstname, m_lastname";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) leader data
  $leader_header_str = "<tr>"
    . "<td nowrap>"
    . "Leader Name"
    . "</td>";
  //if ($AdminLevel) {
  $leader_header_str .=
    "<td nowrap>"
    .   'AuthLevel'
    . "</td>";
  //}
  $leader_header_str .= "</tr>";

  $leader_strings = array();
  $rows = array();
  while ($m_row = mysqli_fetch_array($result)) {

    $cust_id = $m_row["cust_id"];
    $rows[$cust_id] = $m_row;

    $str = "<tr>"

      . "<td nowrap>"
      . '<a href="/members/mview.php?cust_id='
      . $cust_id
      . '" '
      . 'target="'
      . 'profiles'
      . '"'
      . '>'
      . $m_row["m_firstname"]
      . " "
      . $m_row["m_lastname"]
      . "</a>"
      . "</td>";

    //if ($AdminLevel) {
    $str .=
      "<td nowrap>"
      .   $m_row['AuthLevel']
      . "</td>";
    //}

    $str .= "</tr>";

    //echo $str . "<br>\n";
    $leader_strings[$cust_id] = $str;
    //break;
  } //end while
  //$error->debug($rows, 'rows', __FILE__, __LINE__);


} //end if



$days_past_default = 30; // default number of days past
$days_future_default = 30; // default number of days future
$date_today = date('Y-m-d', time());

// get past days, reject incorrect dates
if (!isset($_POST['days_past'])) {
  $days_past = $days_past_default;
  $seconds_past = $days_past * 3600 * 24;
  $date_past = date('Y-m-d', time() - $seconds_past);
} else {
  $days_past = $_POST['days_past'];
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


// get past days, reject incorrect dates
if (!isset($_POST['days_future'])) {
  $days_future = $days_future_default;
  $seconds_future = $days_future * 3600 * 24;
  $date_future = date('Y-m-d', time() + $seconds_future);
} else {
  $days_future = $_POST['days_future'];
  // Validate days past
  if (is_numeric(trim($days_future))) {
    $integer = "True";
    $seconds_future = $days_future * 3600 * 24;
    $date_future = date('Y-m-d', time() + $seconds_future);
  } else {
    // the begin date cannot be a null string
    $integer = "False";
    $days_future = $days_future_default;
    $seconds_future = $days_future * 3600 * 24;
    $date_future = date('Y-m-d', time() + $seconds_future);
  }
}
$date_future = quotesqldata($date_future);


//$date_past = '2003-05-04' ;
//$date_future = '2003-07-04';

if ($cust_id == NULL) {
  $message = "Error: Invalid Customer ID!\n";
} else {
  // query for past events posted by each member
  $query = "SELECT  events.leader_id,
                  count(*) as EventCount1,
                  events.e_begindate,
                  members.cust_id,
                  members.m_firstname,
                  members.m_lastname,
                  members.m_memberstatus,
                  members.m_email,
                  members.u_auth_level as AuthLevel
                  FROM events, members
                  WHERE events.leader_id = members.cust_id
                  AND (members.m_club='" . $ClubCode . "')
                  AND events.e_begindate >= "
    . "'" . $date_past . "'"  //'2003-05-04'
    . " AND events.e_begindate <= "
    . "'" . $date_today . "'" //'2003-07-04'
    . " GROUP BY events.leader_id
                  ORDER BY EventCount1 DESC";

  if (!($result1 = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // format (stringify) member data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "Name"
    . "</td>"
    . "<td>"
    . "EventCount"
    . "</td>";
  //if ($AdminLevel) {
  $header_str .=
    "<td nowrap>"
    .   'AuthLevel'
    . "</td>";
  //}
  $header_str .= "</tr>";

  $strings = array();
  $rows1 = array();
  while ($m_row1 = mysqli_fetch_array($result1)) {

    $cust_id = $m_row1["cust_id"];
    $rows1[$cust_id] = $m_row1;

    $str = "<tr>"

      . "<td nowrap>"
      .   $m_row1["m_firstname"]
      . " "
      .   $m_row1["m_lastname"]
      . "</td>"

      . "<td nowrap>"
      .   $m_row1['EventCount1']
      . "</td>";

    //if ($AdminLevel) {
    $str .=
      "<td nowrap>"
      .   $m_row1['AuthLevel']
      . "</td>";
    //}

    $str .= "</tr>";

    //echo $str . "<br>\n";
    $strings[$cust_id] = $str;
    //break;
  } //end while

  // query for FUTURE events posted by each member
  $query2 = "SELECT  events.leader_id,
                  count(*) as EventCount2,
                  events.e_begindate,
                  members.cust_id,
                  members.m_firstname,
                  members.m_lastname,
                  members.m_memberstatus,
                  members.m_email,
                  members.u_auth_level as AuthLevel
                  FROM events, members
                  where events.leader_id = members.cust_id
                  AND events.e_begindate >= "
    . "'" . $date_today . "'
                  AND (members.m_club='" . $ClubCode . "')"
    . " AND events.e_begindate <= "
    . "'" . $date_future . "'" //'2003-07-04'
    . " GROUP BY events.leader_id
                  ORDER BY EventCount2 DESC";

  if (!($result2 = @mysqli_query($connection, $query2)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $rows2 = array();
  while ($m_row2 = mysqli_fetch_array($result2)) {

    $cust_id = $m_row2["cust_id"];
    $rows2[$cust_id] = $m_row2;
    $str = "<tr>"
      . "<td nowrap>"
      .   $m_row2["m_firstname"]
      . " "
      .   $m_row2["m_lastname"]
      . "</a>"
      . "</td>"
      . "<td nowrap>"
      .   $m_row2['EventCount2']
      . "</td>";
    $str .=
      "<td nowrap>"
      .   $m_row2['AuthLevel']
      . "</td>";
    $str .= "</tr>";

    $strings_future[$cust_id] = $str;
  } //end while

} //end if

// start new code

// format (stringify) leader data
$leader_header_str3 =
  "<tr>"
  . "<td nowrap>"
  . "Leader Name"
  . "</td>"
  . "<td nowrap>"
  . "AuthLevel"
  . "</td>"
  . "<td nowrap>"
  . "MemberStatus"
  . "</td>"
  . "<td nowrap>"
  . "PastEvents"
  . "</td>"
  . "<td nowrap>"
  . "FutureEvents"
  . "</td>"
  . "</tr>";

//$error->debug($rows1, 'rows1', __FILE__, __LINE__);

// combine date from m_row, m_row1 and m_row2 into
// a single structure, strings3
$leader_strings3 = array();
$leader_email_str = '';
$leader_name_str = '';
foreach ($rows as $key => $value) {
  //$error->debug($value, 'value', __FILE__, __LINE__);
  $cust_id = $key;
  $str =
    "<tr>"
    //."<td>"
    //.$cust_id
    //."</td>"

    . "<td nowrap>"
    . '<a href="/members/mview.php?cust_id='
    . $cust_id
    . '" '
    . 'target="'
    . 'profiles'
    . '"'
    . '>'
    . $value["m_firstname"]
    . " "
    . $value["m_lastname"]
    . "</a>"
    . "</td>"

    . "<td>"
    . $value["AuthLevel"]
    . "</td>"

    . "<td>"
    . $value["m_memberstatus"]
    . "</td>"

    . "<td>";
  if (isset($rows1[$key])) {
    $str .= $rows1[$key]["EventCount1"];
  } else {
    $str .= "&nbsp;";
  }
  $str .=
    "</td>"
    . "<td>";
  if (isset($rows2[$key])) {
    $str .= $rows2[$key]["EventCount2"];
  } else {
    $str .= "&nbsp;";
  }
  $str .=
    "</td>";
  $leader_strings3[$key] = $str;
  $leader_email_str .= "," . $value['m_email'];
  $leader_name_str .= "," . $value['m_firstname'];
}
//strip leading comma (,) from strings
$leader_email_str = substr($leader_email_str, 1, strlen($leader_email_str));
$leader_name_str = substr($leader_name_str, 1, strlen($leader_name_str));


$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Event Leader Stats - ' . $ClubCompanyName;
require('top.php');

?>


<div id="centercontent">

  <!-- begin new code -->

  <form method="POST" action="leaders-event-stats.php">
    <h4>Events for past <input type=text name=days_past value="<?php echo $days_past; ?>" size=5 maxlength=5> days; Start Date: <?php echo $date_past; ?></h4>
    <p>Start Date: <?php echo $date_past; ?></p>


    <h4>Events for next <input type=text name=days_future value="<?php echo $days_future; ?>" size=5 maxlength=5> days; End Date: <?php echo $date_future; ?></h4>


    <p> <input type="submit" value="Submit"></p>
  </form>
  <hr>

  <h4>Complete list of Event Leaders</h4>
  <table border=1>
    <col span="1" align="right">
    <?php
    echo $leader_header_str3 . "\n";
    foreach ($leader_strings3 as $str) {
      echo $str . "\n";
    }
    ?>
  </table>

  <br>

</div>

<?php
if ($AdminLevel) {
  echo "<p>&nbsp;</p><table>";
  echo "<tr><th colspan=2 align=left>Leaders eMail addresses</th></tr>";
  echo "<tr><td>To: </td><td><a href='mailto:" . $leader_email_str . "'>" . $leader_email_str . "</a></td></tr>";
  echo "<tr><td>Names: </td><td>" . $leader_name_str . "</td></tr>";
  echo "</table>";
}
require('footer.php');
?>