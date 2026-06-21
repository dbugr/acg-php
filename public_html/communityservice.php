<?php
/*
AdventureClub.info
st
*/
// display list of events
// user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Community Service - ' . $ClubCompanyName;
require('top.php');

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<div align="right">
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber1" align="right">
					<tr>
						<td>
							<img border="0" src="images/cs_march.jpg" alt="March for Babies" width="245" height="190"></td>
					</tr>
					<tr>
						<td>
							<img border="0" src="images/cs_rmh.jpg" alt="Ronald McDonald House" width="245" height="190"></td>
					</tr>
					<tr>
						<td>
							<img border="0" src="images/cs_bears.jpg" alt="Teddy Bear Donations" width="245" height="190"></td>
					</tr>
					<tr>
						<td>
							<img border="0" src="images/cs_bike.jpg" alt="Charity Bike Rides" width="245" height="190"></td>
					</tr>
				</table>
			</div>
			<p>
				<h3>Adventure Club Community Service</h3>
			</p>

			<p>Along with having fun, we in ACG like to host events that give back
				to a community which has treated us so well.&nbsp; Here's some of the
				community service activities we sponsor throughout the year:</p>

			<p><b>March for Babies</b><br>
				Our team walks one of the longest routes in the nation (8.6 miles) to
				raise money each year to help the March of Dimes fund research for
				premature babies.&nbsp; We've raised over $1000 for this event in the
				past and hope to exceed that amount in coming years.</p>

			<p><b>Ronald McDonald House</b><br>
				Several times a year, we cook dinner for families at the Ronald McDonald
				House (RMH).&nbsp; RMH is a temporary home to families of children who
				are confined in the hospital for extended durations. We also donate cash
				and personal care items to the House.</p>

			<p><b>St. Francis House</b><br>
				We also cook and server dinner for the homeless at St. Francis House.&nbsp;
				This event is posted every two to three months, alternating with our RMH
				dinners.</p>

			<p><b>Fundraisers</b><br>
				Once or twice a year we have a party or picnic designated as
				a fundraiser for various charities. Monies from these events have
				provided health
				care and cleaning supplies for the Ronald McDonald House, care packages
				for our troops serving overseas, and support for other projects.</p>

			<p><b>Food Drives</b><br>
				Over the holidays, members donate over 200 pounds of food each year to
				our local food bank to distribute Thanksgiving and Christmas baskets to
				those in need.&nbsp; </p>

			<p><b>Teddy Bear Donations</b><br>
				Every year at Christmas, ACG hosts a teddy bear holiday party.
				Members have donated over 100 brand new bears each year.&nbsp; The bears
				are distributed to young children in times of need by the
				Gainesville Fire Department, the Gainesville Police Department,

				the Ronald McDonald House and Shands Hospital.</p>

			<p><b>Charity Bike Rides</b><br>
				Club members ride for many causes throughout the year, such as Multiple
				Sclerosis and Alzheimer's, among others.&nbsp; Members donated
				over $600 to the Multiple Sclerosis Foundation by sponsoring
				riders in the MS 150 cycling event.
			</p>

			<p>We are proud and happy to be an integral part of
				Gainesville, and thankful we can participate in so many
				of its unique and exciting activities.</p>

			<p><?php include 'more_events.php'; ?></p>

		</td>
	</tr>
</table>

<?php
require('footer.php');
?>