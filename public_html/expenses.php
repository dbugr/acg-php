<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
//$FileName = __FILE__;
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Expenses ' . $ClubCompanyName;
require('top.php');

?>

<table>

	<tr>
		<td>
			<h3 align="left">Expenses: what your membership dues pay for</h3>
			<hr align="left" color="#000000" SIZE="1" noshade>
		</td>
	</tr>


	<tr>
		<td>
			<ul>
				<li>Accounting - IRS annual corporate 1120S form</li>
				<li>Advertising - radio and print</li>
				<li>Business supplies</li>
				<li>Cell phone</li>
				<li>T-shirts - last year we sold t-shirts below cost -</li>
				<li>Marketing expense: pay Marketing Manager for time and expenses - window clings,
					flyers, managing the Meet n Greets, t-shirts for superactive event leaders, etc.</li>
				<li>Internet access</li>
				<li>Domain name registration (www.adventureclub.info, www.actfl.com)</li>
				<li>Event leader perks - for complex events, event leaders spend
					many hours... we respond with free movie coupons, etc.</li>
				<li>Fl State Annual Incorporation fees</li>
				<li>Management fees: pay our Administrative Assistant for press releases, eNewsletter,
					handling incoming information requests, membership cancelations.</li>
				<li>Marketing materials - flyers, handouts</li>
				<li>Internet credit card processing fees</li>
				<li>Website hosting - www.adventureclub.info</li>
				<li>Website design - look and feel - www.adventureclub.info</li>
				<li>Website software development - www.adventureclub.info</li>
			</ul>
		</td>
	</tr>

</table>


<?php

require('footer.php');
?>