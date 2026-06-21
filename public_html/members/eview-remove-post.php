<?php
// remove users reservation

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
}

// Given an event_id, search for
// registration (sign-up) record in
// reserve table
// if found, return it, otherwise return Null
function GetEventRecord($event_id)
{
  global $connection;

  $query1 = "SELECT * FROM events
               WHERE (event_id='" . quotesqldata($event_id) . "');";
  if (!($result1 = @mysqli_query($connection, $query1)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
  $event_row = mysqli_fetch_array($result1);
  return ($event_row);
}

// Given a cust_id, obtain and return
// the customer first and last name as a string
function GetMemberName($cust_id)
{
  global $connection;

  $name = "Unknown";
  $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";

  if (!($result1 = @mysqli_query($connection, $query1)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
  if (($memberrow = mysqli_fetch_array($result1)))
    $name = $memberrow["m_firstname"] . " " . $memberrow["m_lastname"];
  return ($name);
}

// Given a cust_id, obtain and return
// the customer email address as a string
function GetMemberEmail($cust_id)
{
  global $connection;

  $email = "Unknown";
  $query1 = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "');";
  if (!($result1 = @mysqli_query($connection, $query1)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
  if (($memberrow = mysqli_fetch_array($result1)))
    $email = $memberrow["m_email"];
  return ($email);
}

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in!";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: http://"
    . $_SERVER['SERVER_NAME']
    . "/login.php";
  header($loc);
  exit;
}

// connect to the database
mysqlconnect($connection);


if (!SessionIsRegistered("formVars"))
  SessionRegister("formVars", array());

foreach ($_POST as $varname => $value)
  $formVars[$varname] = trim($value);

// obtain member cust_id from members table
$cust_id = getCustomerID($loginUsername, $connection);

if (!isset($event_id)) {
  trigger_error("Invalid event_id (" . $event_id . ")\n", E_USER_ERROR);
  exit;
}
$event_id = $formVars['event_id'];

$cust_id                  = quotesqldata($cust_id);
$formVars["event_id"]     = quotesqldata($formVars["event_id"]);

// update current record
$query = "DELETE FROM reserve WHERE ((cust_id='" . $cust_id . "') AND (event_id='" . $event_id . "'));";
if (!($result = @mysqli_query($connection, $query))) {
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
}

$EmailNoticesTo   = GetParameter('EmailNoticesTo');
$EmailNoticesFrom   = GetParameter('EmailNoticesFrom');

// notify event leaders of reservation changes
$event_row = GetEventRecord($event_id);
$leader_id = getLeaderID($event_id, false);
$leader_email = GetMemberEmail($leader_id);
$email_from =
  "From: " . $emailNoticesFrom . "\n" .
  "Reply-To: " . $emailNoticesFrom . "\n" .
  "Return-Path: " . $emailNoticesFrom . "\n" .
  "Bcc: " . $EmailNoticesTo . "\n" .
  "X-Mailer: PHP/" . phpversion();

if (isset($event_row['e_days_res_chgs'])) {
  // are we within N days of event
  $eventtime = strtotime($event_row['e_begindate']);
  $alerttime = time() + $event_row['e_days_res_chgs'] * 3600 * 24;

  if ((float) $eventtime < (float) $alerttime) {
    $email_subject = $ClubCompanyName . " Event reservation change";
    $email_body =
      $email_subject . "\n\n" .
      "Member:       " . GetMemberName($cust_id) . "\n" .
      "Reservation:  " . $formVars["r_attending"] . "\n" .
      "Member email: " . GetMemberEmail($cust_id) . "\n" .
      "Event Name:   " . $event_row['e_name'] . "\n" .
      "Begin Date:   " . $event_row['e_begindate'] . "\n" .
      "Begin Time:   " . $event_row['e_begintime'] . "\n" .
      "\n\n" .
      "Public event view\n" .
      "http://" . $_SERVER['HTTP_HOST'] . "/eview-pub.php?event_id=" . $event_id . "\n" .
      "http://" . $_SERVER['HTTP_HOST'] . "/elist-pub.php" . "\n\n" .
      "Members only event view\n" .
      "http://" . $_SERVER['HTTP_HOST'] . "/members/eview.php?event_id=" . $event_id . "\n" .
      "http://" . $_SERVER['HTTP_HOST'] . "/members/elist.php" . "\n\n";
    MailWrapper($leader_email, $email_subject, $email_body, $email_from);
  }
}

$loc = "Location: /members/eview.php?event_id=" . $event_id;
header($loc);
?>

<?php
require('footer.php');
?>

