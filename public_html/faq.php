<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

//top($$_SERVER['PHP_SELF'],'FAQ ' . $ClubCompanyName);
$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
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
				<b>Membership in the Adventure Club of Gainesville:</b><br><br>

				A membership in the Adventure Club of Gainesville is defined as: An individual, or group of individuals, who have(s) paid an annual <b>Technology-Usage-Contribution</b> to the Adventure Club of Gainesville, and has been granted a <b>&quot;user-name&quot;</b> and a <b>&quot;password&quot;</b>, <b>by the Adventure Club of Gainesville</b>, allowing that individual or group of individuals to access the Club&quot;s Members&quot; Events List as posted on the Adventure Club of Gainesville&quot;s website:
				<?php
				$s = '<a href="/">' . PublicDomainName() . '</a>.';
				print $s;
				?>


				<br><br>
				Once a <b>Technology-Usage-Contribution</b> is collected and logged in, club members may attend said events provided all other fees are paid as cited and connected to said event are paid for as stated on the event&quot;s individual event page. (e.g. actual cost of hang-gliding event, etc.)
				<br><br>
				The <b>Technology-Usage-Contribution</b> does not include any form of ownership of the Adventure Club of Gainesville.
				<br><br>
				The Adventure Club of Gainesville is a group of individuals, whom by signing up for an event on the club members&quot; events page, have collectively decided to participate in an event and adhere to all liability waivers as cited by the club.
				<br><br>
				An event&quot;s listed &quot;leader&quot; is an event organizer and is not connected physically or financially to the ownership or liabilities connected to any event.
				<br><br>
				The <b>Technology-Usage-Contribution</b>, once collected, is used to pay for website construction, rental, and website-server fees.
				<br><br>
				The <b>Technology-Usage-Contribution is not in any form considered / collected as &quot;Adventure Club of Gainesville&quot;s dues&quot;</b>.
				<br><br>
				Using the &quot;user-name&quot; and &quot;password&quot; to sign up for an event <b>does</b> automatically commit the user thereof to adhere to all waivers of liability as stated within the Adventure Club of Gainesville&quot;s Website. Any guests invited to Adventure Club events become the liability of the club member whom invited the guest.
				<br><br>
				The <b>Technology-Usage-Contribution</b> expires automatically every calendar year and must be renewed by the current holder thereof.
				<br><br>
				The renewal <b>Technology-Usage-Contribution</b> fee is currently $3.00 per month, and is collected yearly.

			</p>
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
				</br>
				The cost is $3 per month which comes out to $36 per year, paid
				annually.
			</p>
			<hr align="left" color="#000000" SIZE="1" noshade>
		</td>
	</tr>

	<tr>
		<td>
			<p align="left">
				<b>How much do events cost?</b><br>
				Some events are free, like hiking or bike rides. If the event is not free, each member is responsible for the charge set by vendors.

				The price of each event is listed on the event's detail page.
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
		<td><a name="ReservationEtiquette"></a>
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
				There are no continuing financial or time-obligation contracts.
				However, when you sign up for an event on the ACG website you
				are restating your understanding, and wilfull participation and
				agreement, relative to the Adventure Club of Gainesville's
				"Policies" and "Liabilities Release Form".
				( Note: these can be viewed by clicking on their
				links at the top of this page.)</p>
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
				<b>What do my yearly dues pay for?</b><br>
				<?php echo GetParameter('ShortClubName'); ?> Membership fees cover <a href="/expenses.php">administrative costs</a> related to maintaining the club such as website
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

	<!--
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
-->
	<tr>
		<td>
			<p align="left">
				<b>How do I cancel my membership?</b><br>

				To cancel your membership, use our
				<a href="/contact.php">contact form</a>
				to send a cancellation request message to the managers.
			</p>
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