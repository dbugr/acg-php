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

//top($PHP_SELF,'Leader Info ' . $ClubCompanyName);
$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'High Fives - Testimonials - ' . $ClubCompanyName;
require('top.php');

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<h3>High Fives - What Folks Say About the Club</h3>

			<p> "The <b>Adventure Club of Gainesville</b> has been a great
				way to learn about exciting places, new activities,
				and, of course, new people. The event posting features
				really helps coordinate all of these activities. The
				image gallery has also been a great way for us share
				our photos, and to chat about them. The club's features
				are great, but the best part has been trying new things
				with fun and adventuresome people! -- Jeff"</p>

			<p> "The bar scene bores me, but I love to socialize and
				get into the outdoors. Being in the <b>Adventure Club</b>
				gives me fun things to do with a great group of people.
				I love looking at the listing of events and knowing that
				no matter what day of the week it is, I have my choice
				of interesting activities to enjoy. And Chuck does a
				great job of soliciting ideas from members and "making
				it happen!" -- Lisa"</p>

			<p> "Before the <b>Adventure Club</b> I had very few
				local friends and my adventures
				were few and far between. Those days are over!
				Now there are always new
				adventures to participate in every month, and
				lots of friends to share them
				with. I've moved out of my comfort zone, faced
				my fears, and done lots of
				exciting things I only dreamed about before.
				Thanks, Adventure Club! -- Joe"</p>

			<p>"Life has taken me away from Gainesville for now. Maybe forever. Who knows?
				It was great fun while it lasted! I still smile when I think of all of you fine people.
				I had a wonderful time. --Cliff"</p>

			<p>"Thank you so much... This group is one of the best things that I ever did. I am glad to have such great friends :o) --Jean"</p>

		</td>
	</tr>
</table>


<?php
require('footer.php');
?>