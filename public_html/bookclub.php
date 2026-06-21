<?php

require('always.include.php');
//$debug = true;
//$debug = false;
//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Adventures in Reading Book Club Meetings Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "bookclub";
?>

<div id="centercontent">

	<table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
		<tr>
			<td width="100%">
				</embed>
				<h3>
					Adventures
					in Reading Book Club</h3>
				<div align="right">
					<table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
						<tr>
							<td>
								<img border="0" src="/images/bookclub1.jpg" alt="In New York to See &quot;Wicked&quot; on Broadway" width="350" height="262">
								<p>
									<font color="#008000">In New York to See <b><i>Wicked</i></b> on Broadway</font>
							</td>
						</tr>
						<tr>
							<td>
								<font color="#008000">&nbsp;</font>
							</td>
						</tr>
						<tr>
							<td>
								<img border="0" src="/images/bookclub2.jpg" alt="At Marjorie Kinnan Rawlings' Home in Cross Creek" width="350" height="231">
								<p>
									<font color="#008000">At Marjorie Kinnan Rawlings' Home in Cross Creek</font>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;</td>
						</tr>
						<tr>
							<td>
								<p align="center">
									<img border="0" src="/images/bookclub3.jpg" alt="In Savannah for the Midnight in the Garden of Good and Evil Tour" width="350" height="262">
									<p>
										<font color="#008000">Savannah <i><b>Midnight in the Garden </b></i>
											Tour</font>
							</td>
						</tr>
					</table>
				</div>
				<p>The book club started in March 2006, and we are still going strong!&nbsp;
					Our first book selection was <b><i>The DaVinci Code</i></b> by Dan Brown.&nbsp;
					Since then, we've read a book every month, everything from popular fiction
					to mystery and humor to quantum physics!<p>Every month we meet at a club
						member's home for some wine and cheese and other munchies to discuss that
						month's selection.&nbsp; Discussion is very informal and centered around
						what we like and don't like about the story, as well as some debate about
						the message of the book and what the author was trying to get across.<p>The
							original inspiration for the book club was to read a book, then do a related
							adventure.&nbsp; As a result, we've gone on &quot;novel&quot; trips like these:<ul>
								<li>Savannah, Georgia, for a tour of the sites in <b><i>Midnight in the
											Garden of Good and Evil</i></b><br>
									&nbsp;</li>
								<li>New York, New York, to see the show <b><i>Wicked</i></b> on Broadway<br>
									&nbsp;</li>
								<li>Cross Creek, Florida, to visit Marjorie Kinnan Rawlings' home
									described in her autobiographical book, <i><b>Cross Creek</b></i></li>
							</ul>
							<p>We've also seen the movie versions of many novels, including <i><b>
										Memoirs of a Geisha</b></i>, <b><i>Midnight in the Garden of Good and Evil</i></b>,
								and <b><i>Secret Life of Bees</i></b>, to name a few.<p>One book, <i><b>
											Triggerfish Twist</b></i>, even inspired a party after the main character
									finds some tortuous ways to use a water sprinkler toy.&nbsp; Thus was born
									the Summer Wham-O Party where we played with all the Wham-O toys we could
									find, including a Slip n' Slide and Hula Hoops.<p>So, you never know what
										fun we'll have when we read!<p><b>Here's a just some of the books we've read:</b>
											<p><i>The Kite Runner<br>
													A Thousand Splendid Suns<br>
													Even Cowgirls Get the Blues<br>
													Life of Pi<br>
													Monkeys are Made of Chocolate<br>
													The Good German<br>
													Tallgrass<br>
													A Dirty Job<br>
													Veronika Decides to Die<br>
													A Brief History of Time<br>
													A Walk in the Woods<br>
													Whisper to the Blood<br>
													Triggerfish Twist<br>
													Lonesome Dove<br>
													Pillars of the Earth<br>
													Cider House Rules<br>
													Bluebeard</i>
												<p>All Adventure Club members are welcome to join the Book Club at
													anytime!&nbsp;
													<p><b>See our Events Listing to find the next Book Club Meeting:<br>
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