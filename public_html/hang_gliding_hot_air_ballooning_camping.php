<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Hang Gliding, Hot Air Ballooning, Camping Weekend - Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "wallaby";
?>

<div id="centercontent">


	<table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
		<tr>
			<td width="100%">
				</embed>
				<h3>
					Hang Gliding, Hot Air Ballooning and Camping
					Weekend</h3>
				<div align="right">
					<table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
						<tr>
							<td>
								<img border="0" src="images/hanggliding1.jpg" alt="Hang Gliding at Wallaby Ranch" width="350" height="466">
								<p>
									<font color="#008000">Hang Gliding at Wallaby Ranch</font>
							</td>
						</tr>
						<tr>
							<td>
								<font color="#008000">&nbsp;</font>
							</td>
						</tr>
						<tr>
							<td>
								<img border="0" src="images/hanggliding2.jpg" alt="Hot Air Ballooning at Wallaby Ranch" width="350" height="262">
								<p>
									<font color="#008000">Hot Air Ballooning at Wallaby Ranch</font>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;</td>
						</tr>
						<tr>
							<td>
								<p align="center">
									<img border="0" src="images/hanggliding3.jpg" alt="Camping at Wallaby Ranch" width="350" height="233">
									<p>
										<font color="#008000">Camping at Wallaby Ranch</font>
							</td>
						</tr>
					</table>
				</div>
				<p>Once a year in the Fall, we journey to Wallaby Ranch, a serene and
					exciting getaway in the Orlando-Kissimmee area for hang gliding, hot air
					ballooning and camping.</p>
				<p>Come just to camp, or soar high above the trees in a hot air balloon or
					hang glider...or all of the above!</p>
				<p>This event is scheduled for the second weekend in November each year.</p>
				<p>Wallaby Ranch has a group of seasoned hang gliding instructors who take
					you up on tandem flights.&nbsp; A small ultra light airplane tows the glider
					up to altitude and releases you for a ride on the thermals.&nbsp; Your pilot
					does all the work, and you just get to enjoy the scenery--or you can steer a
					little and experience the thrill of dives and spins.&nbsp; Take your camera
					up to the heights for some great photos.&nbsp; Flights are available
					according to the wind conditions, either early in the morning before
					breakfast or in the late afternoon at sunset.&nbsp; The experience lasts for
					about 20 minutes, and the Adventure Club gets group rates.</p>
				<p>Blue Water Balloons also offers group rates for hot air ballooning.&nbsp;
					The balloons often either take off or land at Wallaby Ranch or in the nearby
					countryside depending on wind conditions.&nbsp; You get to watch and assist
					as the balloons are inflated, and you lift off at dawn for a spectacular
					sunrise viewing.&nbsp; A ride typically lasts for about 45 minutes to an
					hour.&nbsp; Upon landing, you get a commemorative flight certificate and
					champagne brunch.</p>
				<p>Wallaby Weekend is one of the most attended events of the year, so don't
					miss it.&nbsp; You will get to meet a lot of our members and make new
					friends.&nbsp;
					<p>Most folks bring tents to set up right in the field near the grass
						runway.&nbsp; In the morning, you may wake to the sight of gliders taking
						off or balloons floating across the sky.<p>The Wallaby dining hall serves
							two excellent meals per day: brunch after the morning flight and a late
							lunch around 3:00 pm.&nbsp; They also have grills, drinks, pool, hot tub,
							trampoline, playground, hiking trails, hot showers and clean restrooms.<p>At
								night, we have a bonfire where we cook all sorts of camping fare like baked
								potatoes, hot dogs, chili, s'mores, marshmallows, and even fruit pies.&nbsp;
								Just bring whatever you can dream up to share in the feast.<p><b>See our Events Listing
										to sign up for this event:<br>
									</b><a href="/elist-pub.php">
										/elist-pub.php</a>
									<p>
										<b>
											<font size="4">Events are open to all members!</font>
										</b>&nbsp; <br>
										Not a member yet?&nbsp; <b>Join us: </b>
										<a href="<?php echo GetParameter('vd') . '/join.php'; ?>">
											<?php echo GetParameter('vd') . '/join.php'; ?></a>
										<p><?php include 'more_events.php'; ?>
			</td>
		</tr>
	</table>
</div>

<?php

require('footer.php');
?>