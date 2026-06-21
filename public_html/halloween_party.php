<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Halloween Party - Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "halloween";
?>

<div id="centercontent">


	<table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
		<tr>
			<td width="100%">
				</embed>
				<h3>
					Halloween Party</h3>
				<div align="right">
					<table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
						<tr>
							<td>
								<img border="0" src="images/halloween1.jpg" alt="Ghosts in the Graveyard at the Halloween Party" width="350" height="262">
								<p>
									<font color="#008000">Ghosts in the Graveyard at the Halloween Party</font>
							</td>
						</tr>
						<tr>
							<td>
								<font color="#008000">&nbsp;</font>
							</td>
						</tr>
						<tr>
							<td>
								<img border="0" src="images/halloween2.jpg" alt="Winners of the Halloween Costume Contest" width="350" height="262">
								<p>
									<font color="#008000">Winners of the Halloween Costume Contest</font>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;</td>
						</tr>
						<tr>
							<td>
								<p align="center">
									<img border="0" src="images/halloween3.jpg" alt="Got Wings?  Adventure Club Halloween Party " width="350" height="262">
									<p>
										<font color="#008000">Got Wings? Adventure Club Halloween Party </font>
							</td>
						</tr>
					</table>
				</div>
				<p>
					One of the premier parties of the year!&nbsp; Members go all out to host
					this masquerade event with decorations, graveyard set-up, fright tours,
					costume contest and more.<p>
						This is a party where EVERYONE dons their most creative, cute, funny,
						original, scary or sexy costume in hopes of winning the contest for the best
						dressed.<p>
							Often, this is a half-catered, half-potluck affair, where you'll find a
							variety of eats and treats.&nbsp; Of course, a wicked punch accompanies the
							fest.<p>
								Past parties have included themes such as pirates and the Wizard of Oz.&nbsp;
								We play games, have graveyard tours, hunt for zombies, get psychic readings
								and circle around the bonfire.&nbsp; If you can dream it, we've probably
								done it.<p>
									The party house is always packed, and everyone has a great time.&nbsp; Join
									this year's ghosts and goblins for an All Hallow's Eve to remember!<b> <br>
										<br>
										See our Events Listing to sign up for the Halloween Party:<br>
									</b><a href="/elist-pub.php">
										/elist-pub.php</a>
									<p><br>
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