<?php

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

// Is the user logged in as admin?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
if (!SessionIsRegistered("loginUsername") or !$AdminLevel) {
   // Register a message to show the user
   $message = "Error: you do not have sufficient privileges to "
      . "view this information.";
   SessionRegister("message", $message);
   $loc = "Location: /index.php";
   header($loc);
   exit;
}

$FileName = __FILE__;
$WebPageTitle = 'Admin Function List ' . GetParameter('ClubCompanyName');
$admin = true;
require('top.php');
?>

<div id="centercontent">
   <hr>
   <table width="80%">
      <tr>
         <td><b>Command</b></td>
         <td><b>Description</b></td>
      </tr>
      <tr>
         <td>ADMIN HOME</td>
         <td>This page!</td>
      </tr>
      <tr>
         <td>JoinLog</td>
         <td>Displays the Join script log file</td>
      </tr>
      <tr>
         <td>Webalizer</td>
         <td>Webalizer Log File Analysis</td>
      </tr>
      <tr>
         <td>eNewsletter</td>
         <td>eNewsletter Generator</td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td>MembersCURRENT</td>
         <td>CURRENT Members List</td>
      </tr>
      <tr>
         <td>MembersALL</td>
         <td>ALL Members List</td>
      </tr>
      <tr>
         <td>MembersPDACurrent</td>
         <td>CURRENT Members List formated for PDA</td>
      </tr>
      <tr>
         <td>MembersPDAAll</td>
         <td>ALL Members List (formated for PDA</td>
      </tr>
      <tr>
         <td>Members Gained/Lost</td>
         <td>Members Gained/Lost Report</td>
      </tr>
      <tr>
         <td>Member Referral</td>
         <td>Referral Statistics Report</td>
      </tr>
      <tr>
         <td>Member Occupations</td>
         <td>Member Occupations Report</td>
      </tr>
      <tr>
         <td>AtRisk</td>
         <td>Members with ZERO OR ONE event Registrations</td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td>Events Delete</td>
         <td>Delete an event</td>
      </tr>
      <tr>
         <td>Reservations</td>
         <td>Number of YES Registrations made by each member</td>
      </tr>
      <tr>
         <td>Res/Details</td>
         <td>Number of YES Registrations with details</td>
      </tr>
      <tr>
         <td>Res Counts</td>
         <td>Count of members registered for each event</td>
      </tr>
      <tr>
         <td>Expired Members</td>
         <td>List/Delete Reservations made by Expired Members</td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
   </table>
   <p>&nbsp;</p>
   <p>CAUTION! EVERY time you click the following links, MULTIPLE emails are sent!</p>
   <table width="80%">
      <tr>
         <td>EMail All</td>
         <td>Send email to all club members</td>
      </tr>
   </table>

</div>

<?php
require('footer.php');
?>