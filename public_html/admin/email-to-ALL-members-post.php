<?php
/*
AdventureClub.info

  // list all active members,
  // send to selected vendor email addresses

*/

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

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


if (!isset($debug) && isset($_GET['debug'])) {
  $debug = 1;
}

$EmailNoticesTo   = GetParameter('EmailNoticesTo');
$EmailNoticesFrom   = GetParameter('EmailNoticesFrom');

$email_from =
  "From: "
  . $EmailNoticesFrom
  . "\n"
  . "Reply-To: "
  . $EmailNoticesFrom
  . "\n"
  . "Return-Path: "
  . $EmailNoticesFrom
  . "\n"
  . "X-Mailer: PHP/" . phpversion();



$errors = array();

if (empty($errors)) {
  mysqlconnect($connection);


  // obtain list of members
  $ClubCode = GetParameter('ClubCode');
  $query = "SELECT * FROM members WHERE m_club='$ClubCode'"
    . " ORDER BY m_firstname, m_lastname";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $MemberExpirationDates = GetMemberExpirationDates();

  $todays_date = date('m-d-Y', time());

  while ($m_row = mysqli_fetch_array($result)) {
    $cust_id = $m_row["cust_id"];
    if (!expired_membership($MemberExpirationDates[$cust_id])) {
      $str = ""
        . $m_row["m_firstname"]
        . " "
        . $m_row["m_lastname"]
        . ' <'
        . $m_row["m_email"]
        . '> ';
      $strings[$cust_id] = $str;
      //break;
    } // if (!expired)
  } // while (m_row)
} // empty(errors)

// get the subject, body and debug info
$email_subject = clean($_POST['email_subject']);
$email_body = clean($_POST['email_body']);

$FileName = __FILE__;
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'EMail to All Members Post - ' . $ClubCompanyName;
require('top.php');

?>

<hr>

<div id="centercontent2">
  <pre>
<?php
$test_mode = !empty($_POST['test_mode']);
if ($test_mode) {
  echo 'TEST MODE: sending only to ' . $EmailNoticesTo . ' instead of all members' . "\n\n";
}

echo 'Number of EMails to be sent: ' . ($test_mode ? 1 : count($strings)) . '<br><br>';

echo 'FROM: ' . $email_from . "<br><br>";

echo 'SUBJECT: <br>' . $email_subject . "<br><br>";
echo 'BODY:    <br>' . $email_body . "<br><br>";

// send copy to admin staff
echo 'Sending admin copy to: ' . $EmailNoticesTo . "<br>";
try {
  MailWrapper($EmailNoticesTo, $email_subject, $email_body, $email_from);
} catch (\Throwable $ex) {
  echo 'ERROR sending admin copy: ' . $ex->getMessage() . "<br>";
}

if (!$test_mode) {
  foreach ($strings as $email_to) {
    echo 'Sending email to: ' . $email_to . "<br>";
    try {
      MailWrapper($email_to, $email_subject, $email_body, $email_from);
    } catch (\Throwable $ex) {
      echo 'ERROR sending to ' . $email_to . ': ' . $ex->getMessage() . "<br>";
    }
    for ($i = 0; $i < 20000; $i++)
      $e = 10 / 5 * 10 / 5; // waste some time to reduce server load
  }
}
?>
</pre>
</div>

<?php
require('footer.php');
?>