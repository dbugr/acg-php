<?php

// event leader FAQ

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Event Leader FAQ - ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">
	<h3>Leader Frequently Asked Questions (FAQ)</h3>
	<p>
		The purpose of this booklet is to assist Event Leaders with listing events on the club's website, introduce advanced website Event Leader features and helpful tips.
	</p>
	<h3>Getting Started</h3>
	<p>
		We welcome you as a new Event Leader and look forward to your events!
		Thank you for allowing club members to benefit from your experience.
		If not your experience, then your enthusiasm!
		Perhaps you will host activities with which you have lots of
		experience, great! Or ones with which you have little, or even no,
		experience. Not a problem. Experience is not required. Just a
		playful attitude! We encourage you to stretch a little and to
		think outside the box. Whatever you are willing to do, we guarantee
		others would love to do, too. Berry picking to dancing, movies to
		horseback riding; new or unusual things like geocaching or
		electronic trivia; or daring things like sky diving, rock
		climbing or white water rafting, anything you are willing to host,
		someone is waiting to do!
	</p>

	<h3>How to Post an Event Using an Archived Event</h3>
	<p>
		Log onto the club website. Click on Event Link, then click on
		Create New Event. Create New Event is a feature available to
		Event Leaders and site administrators only.
		The club site stores events and event descriptions in an archive.
		Unless you are certain your event is unique (a theme house party,
		for example) research archived events for an identical event.
		If one exists, here is how to save a lot of time and effort by
		simply copying and updating the event:
	</p>
	<ol>
		<li>Click on the little calendar by Start Date at the top of the Event page. </li>
		<li>Use the ? (arrow) on the calendar and choose, by left mouse click, a list of a couple months of previous events. </li>
		<li>Click on Submit. The Event page now lists all the club events from the date selected. </li>
		<li>Next, left mouse click on the Internet Explorer Edit tab at the very top of the screen. (The Edit tab is on the static bar that stays at the top of the screen when you are on the Internet.) </li>
		<li>Highlight and left mouse click on Find, then type in a keyword for your event. </li>
		<li>If you got lucky and found an identical event, left mouse click on Copy Event. </li>
		<li>Read through all event text, sometimes minor details date the event. </li>
		<li>When through, click Submit. </li>
	</ol>

	<h3>How to Write an Event from scratch</h3>
	<p>Tip: Begin with the basics:<br>
		What - one to five word event title; <br>
		When - date and time; and <br>
		Where - location and starting time.</p>
	<p>Heading names in red must be filled in. Heading names in black are optional.</p>
	<p>Begin Date: Left click on little calendar, left click on selected date to fill in date on screen. To change calendar month, click on the ? (arrow) on top of calendar.</p>
	<p>Event Category: Select one most closely related to your event. If nothing on list matches, choose Other.</p>
	<p>Event Status: Leave as is. If event gets cancelled, this is where to change status to Event Cancelled.</p>
	<p>Location Link: Open an Internet Explorer session. Go to the website you want members to know about, put your mouse on address line and right click to Copy web address. Go back to Club site, click on Location Link box and right click to Paste.</p>
	<p>Descriptions: Write a brief description for public viewing think travel brochure make it appealing and fun. In Enter Additional Details for Members Only type specifics about event.
		In both description boxes, type &lt;p&gt; to create new paragraph.
		If event is free, give that information in description boxes. Pay4Event (see below) doesn not accept text, only numbers. Typing Free in the description is the only method to get that information to members.</p>
	<p>Attendees: How to pick a Minimum number? Think of the lowest number of attendees acceptable for you to hold the event. The maximum number is determined by many factors such as number of items that must rented, parking lot sizes, etc.</p>
	<p>Event Link 1 and 2: Optional for any additional internet links related to event - a sites waiver or river levels, for example.</p>
	<p>Meet At: Describe specific spot to meet. For ex. Just inside pay booth or north side of mall parking lot outside Penneys.</p>
	<p>Driving Directions: (See Location Link for copy/paste instructions) Mapquest.com is an internet site for driving directions; use the Driving Direction option. If you type in the address of the event and use that as the destination direction link, that address will remain when users use that link. All they will have to do is type in their home address for home-to-site personalized driving directions. Pretty cool, huh?</p>
	<p>Pay4Event: Total = amount of event including deposit. Date chosen usually equals date of event unless event must be paid in full beforehand. Deposit = some events require deposit. Leave blank if event requires no deposit. If event cost is greater than $30, we suggest asking for half of the full amount as a deposit to cut down number of cancellations.
		Club owners send check to vendors if deposit or prepayment is required. You will no be asked to spend any out-of-pocket cash.
		If check has to be mailed in advance, adjust members Payment Due By date to allow for mailing time.
		Notify club owners at the time check must be sent with total amount to send and name and address of vendor. Please do not notify us until the day or day before the check must be mailed.</p>
	<p>Leader Notes: Optional. Fill in before or after event. Is another leader needed to assist with event or another leader backing you up in case you can not attend the event? This is the place to make notes viewable to Event Leaders and System Administrators only. If after the event youd like to make any special comment like, This vendor lets event leader in for free if asked or What they advertised is NOT what we got, this is your place to tell others any pros or cons of the event for future use.</p>


	<h3>Event Tracking Tool - Show My Events</h3>
	<p>This feature helps you keep track of all your posted events. After logging on, go to the Events link. There you will see a button, Show My Events. This feature is another Event Leader/Admin only feature.
		When you click on Show My Events, you will see:<br>
		All your events posted on the Event Calendar<br>
		Who signed up for the event (including Waiting List)<br>
		Contact information of attendees<br>
		Number of guests signed up<br>
		If Pay4Event is active, how much a member paid and date of last payment. <br>
		A Print feature allows for easy printing of your events to keep on hand.<br>
		<p>

			<h3>Other Event Leader features</h3>
			<p>Event cost or space limitations critical for an event? Here is a feature to help you manage that information.
				Find the Notification box near the bottom of the Edit Event page. By typing a number in the number of days before event option, the following occurs:
				<ol>
					<li>As members sign up with a -Yes- or choose -Waiting List,- an email is automatically sent to the Event Leader;</li>
					<li>If a member changes status from Yes/Waiting List to -No- (cancelling their reservation), this also auto sends an email to the Event Leader;</li>
					<li>Both these events begin to occur at the number of days filled in at the option. </li>
					<li>Leave number of days blank to receive no notifications.</li>
				</ol>
				Other things we thought you would like<br>
				These events trigger an email and are optional choices for all members found on the Edit My Profile page:
				Event changed, edited, updated<br>
				Advance event reminders to attendees with a further optional choice of -how many- days in advance (similar to Event Leader advance notification)<br>
				Payment reminders, deposit and full price of event. Again, advance notice is determined by each member choice of how many days in advance. (Event Leaders select Payment Due Date.)


				<h3>MISCELLANEOUS TIDBITS</h3>
				<p>It is OK for different Event Leaders to post more than one event on the same day. </p>
				<p>Bring a mini-supply kit (ex. bug spray, sunblock) and a few first aid supplies such as bandages and Benedryl.
					We ask that members disclose special needs or medical problems to Event Leaders. We trust the information will be kept private or given to others on an -As needed- only basis.
				</p>
				<p>If you do not own a cell phone, ask if any attending members have one. We hope you will not need it, but if an emergency arises you will know whose phone to borrow. </p>
				<p>If the event involves a vendor, introduce yourself as an Event Leader of the <?php echo $ClubCompanyName; ?>. Ask for a discount or group rate and ask for a free admission/rental for yourself!
					If the vendor expresses interest in participating as a club vendor, please ask them to call <?php echo GetParameter('ContactPhoneNumber1'); ?> or <a href="/contact.php">contact us</a>.</p>
				<p>With some events, attendees must arrive on time. Stress that in your event description. You might use a favorite mom trick - put the arrival time a half hour
					before attendees must arrive. It will give members a few minutes to socialize and the chronically late to be on time. </p>
				<h3>Most important of all - have fun!</h3>
				<p>
</div>

<?php

require('footer.php');

?>