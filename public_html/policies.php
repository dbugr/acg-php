<?php

require('always.include.php');
$debug = true;
$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
//$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$ShortClubName = GetParameter('ShortClubName');
$WebPageTitle = 'Policies - ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">

  <table>
    <tr>
      <td>
        <h3>Policies</h3>

        <p>As a member of the <?php echo $ClubCompanyName; ?> (<?php echo $ShortClubName; ?>),
          you agree to the following:</p>
        <p></p>
        <ul>
          <li>To release <?php echo $ClubCompanyName; ?> (<?php echo $ShortClubName; ?>)
            from any
            liability resulting
            from participation in an <?php echo $ShortClubName; ?> event.</li>
          <li>To release <?php echo $ShortClubName; ?> Event Leaders from
            any liability resulting
            from participation in an <?php echo $ShortClubName; ?> event.</li>

          <li>
            To inform and advise your (the member's) Adventure Club of Gaineville
            (ACG) guest(s) of the Adventure Club of Gainesville's (ACG) POLICIES,
            and to accept the responsibility as your
            (the member's) guest's (s') Sponsor and Event Leader
            while at an Adventure Club of Gainesville (ACG) event.
          </li>

          <li>To release Home Owners or Leasees (including Landlord) who are hosting an
            <?php echo $ShortClubName; ?> event from
            any liability resulting
            from <?php echo $ShortClubName; ?> members participating in an <?php echo $ShortClubName; ?> event.</li>
          <li>To make your medical issues known to
            <?php echo $ShortClubName; ?> Event Leaders
            at the time you sign up for an event,
            and to provide your own medications.</li>
          <li><?php echo $ShortClubName; ?> Event Leaders have full authority to
            handle an event,
            including cancellation of the event and
            dismissal of an
            event attendee.</li>
          <li>Members are responsible for providing their
            own medical insurance.</li>
          <li>Membership fees are not refundable.
            See <a href="/faq.php">
              Frequently Asked Questions</a> page
            for more details.
          </li>
          <li><?php echo $ShortClubName; ?> reserves the right to cancel your membership
            at any time.</li>
          <li><?php echo $ShortClubName; ?> reserves the right to use any event
            in the <?php echo $ShortClubName; ?> website database
            for marketing purposes.</li>
          <li><?php echo $ShortClubName; ?> reserves the right to use photos in the
            <?php echo $ShortClubName; ?> website Photo Gallery or photos contributed
            by members or non-members
            for marketing purposes.</li>
          <li><?php echo $ShortClubName; ?> reserves the right to modify this document without prior
            notice to members.</li>
          <li>In addition, I agree to contents of the
            <a href="/liabilityrelease.pdf"><?php echo $ShortClubName; ?> Liability Release</a>.</li>
        </ul>
        <p></p>
        <p><?php echo $ShortClubName; ?> Management</p>
      </td>
    </tr>
  </table>
</div>

<?php

require('footer.php');
?>