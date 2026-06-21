<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Teddy Bear Holiday Christmas Party - Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "holiday";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Teddy Bear Holiday Party</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="images/teddybear1.jpg" alt="Santa Bear and the Teddy Bears at the Holiday Party" width="350" height="262">
                <p>
                  <font color="#008000">Santa Bear and the Teddy Bears at the Holiday Party</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="images/teddybear2.jpg" alt="Elves at the Teddy Bear Holiday Party" width="350" height="262">
                  <p>
                    <font color="#008000">Elves at the Teddy Bear Holiday Party</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <img border="0" src="images/teddybear3.jpg" alt="Teddy Bear Holiday Party" width="350" height="262">
                <p>
                  <font color="#008000">Teddy Bear Holiday Party</font>
              </td>
            </tr>
          </table>
        </div>
        <p>
          This is the biggest event of the year!&nbsp; Hosted by our very own &quot;Papa
          Bear,&quot; the Teddy Bear Holiday Party is not only a celebration of the season,
          but also a community service event where members each donate a brand new
          teddy bear for a good cause.&nbsp;
          <p>
            The bears are donated to the Gainesville Fire Department, the Gainesville
            Police Department, Shands Hospital, and Ronald McDonald House to be
            distributed to a child in need during a crisis or during the holidays.<p>
              The party is an elegant affair with formal attire and held in the
              beautifully decorated home of our club's director, Jere Steele.&nbsp; Often,
              &quot;elves&quot; are on hand to spirit your coats away and help you with your food
              and drinks.<p>
                Members bring a savory appetizer to share, with fare including seafood,
                tapas, fondue, dips and homemade desserts.&nbsp; The customary party
                cocktail is Black Russians along with a selection of red and white wines.&nbsp;
                Guests are invited to bring their own beverages, too.<p>
                  Food and drinks are spread throughout the home to entice guests to mingle
                  and meet new friends.&nbsp; The &quot;tavern&quot; room makes a great dance floor
                  where members dance well into the night.<p>
                    The holiday party is an exclusive affair reserved for members and their
                    significant others only.&nbsp; It's the perfect opportunity to celebrate the
                    season with friends...and make new ones.&nbsp; Join us for this year's Teddy
                    Bear Holiday Party.&nbsp; You won't want to miss it!<b><br>
                      <br>
                      See our Events Listing to sign up for the Teddy Bear Holiday Party:<br>
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