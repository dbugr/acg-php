<?php

// generate eNewsletter text

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');


// Is the user logged in?
//$loginUsername = $HTTP_SESSION_VARS['loginUsername'];
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

//========================================================================
// create an array of strings, each of which
// contains a new members name
// new member is someone who joined
// within the last 7 days.
function GenNewMembersList($connection)
{
  global $ClubCode;

  $days = 7;
  $lastweek = date('Y-m-d', (time() - ($days * 24 * 3600)));

  $query = "SELECT cust_id, m_firstname, m_lastname,
            m_date_joined, m_memberstatus
            FROM members
            WHERE (members.m_club='$ClubCode') AND m_date_joined >= '" . $lastweek . "' "
    //." and m_memberstatus != 'NotPaid' "
    . " ORDER BY m_date_joined";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $strings = array();
  while ($row = mysqli_fetch_array($result)) {
    $cust_id = $row['cust_id'];
    $str = ""
      . ""
      . $row["m_firstname"]
      . " "
      . $row["m_lastname"]
      . ""
      . "<br>";
    //echo $str;
    $strings[$cust_id] = $str;
  }
  //LogMsg("GenNewMembersList");
  //LogMsg(print_r($strings,true));
  return ($strings);
} // end, GenEventsList

//========================================================================
// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
  global $connection;

  $name = "Unknown (ID=" . $cust_id . ")";
  if (substr($cust_id, strpos($cust_id, '_') + 1, 10) > 0) {
    $query1 = "SELECT * FROM members where cust_id ='" . $cust_id . "'";
    if (!($result1 = @mysqli_query($connection, $query1)))
      trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
    if (($memberrow = mysqli_fetch_array($result1)))
      $name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
  }
  return ($name);
}

//========================================================================
// create an array of strings, each of which
// contains an event name and link, ready for
// use in an html table.
function GenEventsList($connection)
{
  //global $ClubCode;
  //global $public_domain_name;
  global $formVars;
  $ClubCode      = GetParameter('ClubCode');


  $days = 14;
  $nextmonth = date('Y-m-d', (time() + ($days * 24 * 3600)));
  $TodaysDate = date('Y-m-d', time());
  $length = strlen($ClubCode);
  $query = "SELECT event_id,
          leader_id,
          e_name,
          e_begindate, e_details, e_location_name, e_display
          FROM events
          WHERE e_begindate >= '" . $TodaysDate . "'
          and e_status != 'Hide'
          and e_status != 'Canceled'";
  $query .= " order by e_begindate";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  $strings = array();
  while ($row = mysqli_fetch_array($result)) {
    $event_id = $row['event_id'];
    // don't include MembersOnly events not in this club
    if (!strstr($event_id, $ClubCode) && ($row['e_display'] == 'MembersOnly'))
      continue;
    $leader_name = GetMemberName($row['leader_id']);
    $str = date('D M j', strtotime($row["e_begindate"])) . " - " . $row["e_name"] . "<br>" .
      //$row["e_location_name"] . "<br>";
      '-- Event Leader: ' . $leader_name . "<br>";
    // event was selected in check list form?
    //if ((isset($formVars[$event_id])) && in_array($event_id,$formVars)) { 
    if ((isset($formVars[$event_id])) && $event_id == $formVars[$event_id]) {
      // event was selected in check list form!
      $str .= $row["e_details"] . "<br>";     // so display the event details
      $str .= '<input type=hidden name="' . $event_id . '" value="' . $event_id . '">';
      $str .= "/eview-pub.php?event_id=" . $row["event_id"] . "<br><br>";
      $strings[$event_id] = $str;
    }
  }
  return ($strings);
} // end, GenEventsList


//========================================================================
// Generate and display a list of events with check boxes on the left
// so admin can select which events to include in the eNewsletter.
// Preselect the first weeks worth of events
function GenEventsCheckList($command)
{

  //global $ClubCode;
  //global $public_domain_name;
  global $connection;
  global $formVars;
  $ClubCode      = GetParameter('ClubCode');

  $days = GetParameter('eNewsletterNumDaysChecked');;
  // pre-select all days from now until this date
  $PreSelectDate = date('Y-m-d', (time() + ($days * 24 * 3600)));
  $TodaysDate = date('Y-m-d', time());
  $length = strlen($ClubCode);
  $query = "SELECT event_id,
          leader_id,
          e_name,
          e_begindate, e_details, e_location_name, e_display
          FROM events
          WHERE e_begindate >= '" . $TodaysDate . "'
          and e_status != 'Hide'
          and e_status != 'Canceled'";
  $query .= " order by e_begindate";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  $strings = array();
  while ($row = mysqli_fetch_array($result)) {
    //echo "Event_Id: " . $event_id . "<br>";
    $event_id = $row['event_id'];
    // don't include MembersOnly events not in this club
    if (!strstr($event_id, $ClubCode) && ($row['e_display'] == 'MembersOnly'))
      continue;

    $leader_name = GetMemberName($row['leader_id']);
    $str = "";
    $str .= "<tr>";
    $str .= "<td>";
    $str .= '<input type="checkbox" ';

    if ($command) {
      if (isset($formVars[$event_id])) {
        $str .= ($formVars[$event_id] == $event_id) ? " checked " : "";
      }
    } else  // first time check list form is generated, use PreSelectDate
      $str .= ($row['e_begindate'] <= $PreSelectDate) ? " checked " : "";

    $str .= ' name="' . $event_id . '" value="' . $event_id . '"> ';
    $str .= "</td>";
    $str .= "<td>";
    $str .= date('D M j', strtotime($row["e_begindate"])) . " - " . $row["e_name"] . "<br>" .
      $row["e_location_name"] . "<br>"
      . $row["e_display"] . "<br>"
      //$row["e_details"] . "<br>" .
      . "/eview-pub.php?event_id=" . $row["event_id"] . "<br><br>";
    $str .= "</td>";
    $str .= "</tr>";

    $strings[$event_id] = $str;
  }
  return ($strings);
}


//========================================================================
// Display a form with the events checklist
// so admin can select which events to include in the eNewsletter.
function DispEventsCheckListForm($EventsCheckListStrings)
{

  //global $ClubCompanyName;
  //global $ShortClubName;
  //global $ContactPhoneNumber1;
  //global $ContactPhoneNumber2;
  //global __FILE__;
  //global $DirOffset;
  //global $public_domain_name;
  //global $RelOffset;
  global $loginUsername;
  global $MemberInfo;
  $ClubCompanyName  = GetParameter('ClubCompanyName');
  $ShortClubName    = GetParameter('ShortClubName');

  $FileName = __FILE__;
  $WebPageTitle = 'Admin eNewsletter Generator '; // . $ClubCompanyName;
  $admin = true;
  require('top.php');

  echo '<div id="centercontent">';
  echo '<hr>';
  echo "<h3>" . $WebPageTitle . "</h3>";
  echo '<form method="POST" action="/admin/enewsletter.php">';
  echo '<input type=hidden name="command" value="enewsletter">';
  echo '<input type=submit value="Generate eNewsletter">';
  echo "<br>";
  echo "<table border=0>";
  foreach ($EventsCheckListStrings as $str) {
    echo $str;
  }
  echo "</table>";
  echo "<br>";
  echo '<input type=submit value="Generate eNewsletter">';
  echo '</form>';
  echo '</div>';

  require('footer.php');
}


//========================================================================
// Display a form with the events checklist
// so admin can select which events to include in the eNewsletter.
function DispeNewsletterForm($EventStrings)
{
  global $AdminAssistant;
  //global $public_domain_name; //, $FullContactName;
  global $connection;
  //global $ClubCompanyName;
  //global $ShortClubName;
  //global $ContactPhoneNumber1;
  //global $ContactPhoneNumber2;
  //global __FILE__;
  //global $DirOffset;
  //global $RelOffset;
  global $loginUsername;
  global $MemberInfo;
  $ClubCompanyName  = GetParameter('ClubCompanyName');
  $ShortClubName    = GetParameter('ShortClubName');

  $NewMemberStrings = GenNewMembersList($connection);

  $FileName = __FILE__;
  $home    = GetParameter('home');
  $WebPageTitle = 'Admin eNewsletter Generator ' . $ClubCompanyName;
  $admin = true;
  require('top.php');


  echo '<div id="centercontent">';
  echo '<hr>';
  echo '<form method="POST" action="/admin/enewsletter.php">';
  echo '<input type=hidden name="command" value="checklist">';
  echo '<input type=submit value="Events Check List Form"><br>';
  echo $ClubCompanyName;
  echo " eNewsletter&nbsp;&nbsp;&nbsp;";
  echo date('m/d/Y', time());
  echo "<br>";
  echo "";
  echo $home;
  echo "<br>";
  echo "======================================================================";
  echo "<br>";
  echo "WELCOME TO THE GROUP!";
  echo "<br>";
  foreach ($NewMemberStrings as $str) {
    echo $str;
  }
  echo "<br>";
  echo "======================================================================";
  echo "<br>";
  foreach ($EventStrings as $str) {
    echo $str;
  }
  echo "<br>";
  echo "======================================================================";
  echo "<br>";
  echo "HOPE YOU HAVE A GREAT WEEK!";
  echo "<br>";
  if (!empty($AdminAssistant))
    echo $AdminAssistant . " " . $ShortClubName . " Manager" . "<br>";
  echo GetParameter('FullContactName') . " " . $ShortClubName . " Owner" . "<br>";
  echo "<br>";
  echo '<input type=submit value="Event Check List Form">';
  echo "<br>";
  echo '</form>';
  echo '</div>';

  require('footer.php');
}

// code execution begins here!!!

$formVars = array();
if (count($_POST) != 0) {
  foreach ($_POST as $varname => $value)
    $formVars[$varname] = clean($value,20);
}
$command = "";
if (isset($formVars['command']))
  $command = $formVars['command'];

//LogMsg("eNewsletter command: " . $command);
//LogMsg("Echo _POST array:");
//LogMsg(print_r($_POST,true));
//LogMsg("Echo forVars array:");
//LogMsg(print_r($formVars,true));

if ($command == 'enewsletter') {
  $EventStrings = GenEventsList($connection);
  DispeNewsletterForm($EventStrings);
} else {
  $EventsCheckListStrings = GenEventsCheckList($command);
  //print_r($EventsCheckListStrings);
  DispEventsCheckListForm($EventsCheckListStrings);
}

exit;
