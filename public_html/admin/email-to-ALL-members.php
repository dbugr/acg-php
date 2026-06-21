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
} else {
   $debug = 0;
}

$EmailNoticesTo    = GetParameter('EmailNoticesTo');
$EmailNoticesFrom    = GetParameter('EmailNoticesFrom');

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

$FileName = __FILE__;
$WebPageTitle = 'Admin eMail to ALL Members ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent">
   <hr>
   <h1>CAUTION! This form sends an email to ALL members!!</h1>
   <br>

   <form method="POST" action="/admin/email-to-ALL-members-post.php">
      <table>
         <tr>
            <td>Subject: </td>
            <td>
               <input type="text" name="email_subject" value="" size=60 maxlength=120></td>
         </tr>

         <tr>
            <td>Body:</td>
            <td>
               <textarea name=email_body rows=20 cols="60"></textarea></td>
         </tr>
      </table>
      <input type="submit" value="Submit">
   </form>
   <p>Please be PATIENT. Allow one second for every 10 emails.</p>
</div>

<?php
require('footer.php');
?>