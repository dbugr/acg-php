<?php


require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

//top($PHP_SELF,'Start Club ' . $ClubCompanyName);
$FileName = __FILE__;
$ShortClubName = GetParameter('ShortClubName');
$WebPageTitle = 'Start Club ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">
  <h3>Club Ownership!</h3>

  <p><b>Want to own an Adventure Club?</b></p>
  <p>
    The AdventureClub.Info, Inc. software enables
    you to manage and automate most aspects of
    club event management. This includes:</p>

  <ul>
    <li>Event leaders can edit and publish events</li>
    <li>Members can view and signup for events</li>
    <li>New members can use a credit card to join your club</li>
    <li>Credit cards are automatically processed each month</li>
    <li>Full administrative management interface and reports</li>
    <li>Photo gallery</li>
    <li>Member profiles</li>
    <li>Virtual Hosting means you have a unique home page</li>
    <li>Payments go directly to your merchant account</li>
    <li>Event Leaders can choose to share events between clubs!</li>
    <li>Members receive event reminder emails</li>
    <li>Members can pay for events online</li>
  </ul>
  <p>
    <b>Cost:</b></p>
  <ul>
    <li>So sorry, we are no longer offering this service.</li>
  </ul>

</div>

<?php
require('footer.php');
?>