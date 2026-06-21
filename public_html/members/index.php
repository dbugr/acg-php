<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName  = GetParameter('ClubCompanyName');
$ShortClubName    = GetParameter('ShortClubName');
$MembershipFee    = GetParameter('MembershipFee');
$WebPageTitle = 'FAQ ' . $ClubCompanyName;
require('top.php');

?>

<table>

  <tr>
    <td>
      <h3 align="left">Frequently Asked Questions (FAQ)</h3>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Policies and Liability Release</b><br>
        The fine print is contained in our <a href="/policies.php">Policies</a>.<br>
        Some of our events require that you sign a
        paper copy of our
        <a href="/liabilityrelease.pdf">Liability Release Form.</a> </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>What type of events are offered?</b><br>
        Both outdoors and indoors activities.
        Activities include: bike rides, happy hours, skydiving, horseback riding,
        canoe trips, camping trips, wine and beer tastings, bowling, drive-in movies,
        sporting events, club social get-togethers and much more.&nbsp;&nbsp;
        Club members suggest events, then our team of Event Leaders
        make the necessary arrangements. </p>

      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Who belongs to the Adventure Club?</b><br>
        Mainly professionals, both couples and singles, young and old. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How much does it cost to become a member?</b><br>
        Membership is $<?php echo $MembershipFee; ?> per month per person, family or couple living under the same roof.
        Free membership is available by sponsoring two events a month. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Is this a singles club?</b><br>
        No.&nbsp; It is an activity-driven organization where people of any age group
        or marital status join together to do fun activities that they would not or could not
        do by themselves. It is possible, though, that you will meet that someone special.
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How do I join?</b><br>
        Click here to <a href="<?php echo JoinURL(); ?>">Join</a>.
        Note: You will be asked to create your <?php echo $ShortClubName; ?> username and password.
        Please also note that our free trial membership period
        is extended as a courtesy to <i><b>new</b></i> members. Returning former members are
        not entitled to any additional free membership period, as they already
        received a free membership period during their initial membership.

        Also, should a new member cancel
        within the free trial period, no charges to the member will be
        incurred, and all member access to the website and events will be
        discontinued immediately upon cancellation. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>


  <tr>
    <td>
      <p align="left">
        <b>How much do events cost?</b><br>
        Some events are free, like hiking or bike rides. If the event is not free, each member is responsible for the charge set by vendors.
        Event fees can be paid on the club's website or at the
        event location. The price of each event is listed on the event's detail page.
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How do I sign up for an event?</b><br>
        After becoming a member, log-in with your username and password to view our Member's Only calendar.
        Clicking on the name of the event advances you to the event sign up page.
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Reservation Etiquette</b><br>
        Adventure Club Event Leaders spend quite a bit of time
        coordinating club events. With this in mind, please
        be courteous and
        considerate to Event Leaders and other members and follow
        the conventions below: </p>
      <ul>
        <li>
          <p align="left">Do not be a no-show. If you must cancel within 24 hours before an event,
            please change your website reservation status
            from Yes to either Waiting-List or No. Follow the change in
            status with an email to the Event Leader.
        </li>
        <li>
          <p align="left">If you have to cancel the day of the event, please
            contact the event leader. If it is 3 or 4 hours before
            the event,
            try the event leader's cell phone. Remember most members and Event
            Leaders check email only once a day.
        </li>

        <li>
          <p align="left">
            If you are uncertain about attending an event, sign up on the Waiting List. Understand that this is not a firm reservation, and you will not be expected at the event. Leaders will not count on you to show, and may not have room for you, and perhaps even start without you. Please move to the Yes list as soon as you can commit.
        </li>

        <li>
          <p align="left">
            Please DO NOT CRASH EVENTS THAT ARE FULL. Leaders have reasons for
            the limits on the number of people that can attend an event. These
            include cost, limited room in a private residence and limits on
            available resources (number of horses, number of windsurfers,
            number of seats at a restaurant, etc).
        </li>

        <li>
          <p align="left">Please refrain from entering racist or other
            inappropriate (sexual innuendo) comments on the website.
        </li>
      </ul>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>





  <tr>
    <td>
      <p align="left">
        <b>Do I have to sign any contracts?</b><br>
        No.&nbsp; </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Can I bring a guest to club events?</b><br>
        We love guests!&nbsp; We ask that by the fourth event, your
        guest should consider joining the club.
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>What do my monthly dues pay for?</b><br>
        <?php echo $ShortClubName; ?> Membership fees cover <a href="/expenses.php" target="_blank">administrative costs</a> related to maintaining the club such as website
        hosting, advertising, maintenance fees, office supplies as well
        as some supplies for activities. In addition, these funds are
        sometimes used to pay in advance for club events.
        <br>
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How do I become an Event Leader?</b><br>
        Click here to find more
        information about <a href="/leader.php">becoming a leader</a>. We'd love to hear from you!
      </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How am I billed?</b><br>
        Your membership fee will be charged to your
        credit card once each month by our
        automated software. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>How do I cancel my membership?</b><br>

        To cancel your membership, use our
        <a href="/contact.php">contact form</a>
        to send a message to managers.
        The cancellation notice must be received 7 days
        prior to the end of your billing cycle to avoid
        being billed for the next month.
        Your membership will terminate at the end of your
        most recent billing cycle. There are no refunds
        for membership dues. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Can I get an event payment refund?</b><br>

        If YOU can find someone to take your place and
        pay for their attendance, then yes, you can get
        a refund. If not, then no, there will not be a refund.
        Why? If we gave you a refund, then the cost for all other
        members who signed up for the event would increase. This
        would not be fair to the other members who signed up. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Are there other clubs like this one?</b><br>
        Not at this time. </p>
      <ul>
        <li>
          <p align="left"><a href="/startclub.php">OWN YOUR OWN CLUB!!!!</a>
        </li>
      </ul>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>



  <tr>
    <td>
      <p align="left">
        <b>Privacy Statement</b><br>
        Personal information that you provide during the "Join"
        process or in your personal profile is considered
        confidential and will not be shared with third parties.
        You can use your the
        "Edit My Profile"
        page to control the personal information that is
        published for other members to see. </p>
      <hr align="left" color="#000000" SIZE="1" noshade>
    </td>
  </tr>

  <tr>
    <td>
      <p align="left">
        <b>Conflict Resolution</b><br>
        The club maintains a casual environment and wholesome atmosphere. It is the club owner's intent to
        ensure appropriate behavior, dress and language at events and text on the website.
        If a member's behavior, dress or language offends, antagonizes or
        embarrasses others, the Event
        Leader and/or Club Owners will first direct the member to this policy, but under extreme circumstances
        the member's membership will be cancelled. <br>
        &nbsp;
    </td>
  </tr>

</table>


<?php

require('footer.php');

?>