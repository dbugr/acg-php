<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Summer Water Days - Boating Swimming Knee Boarding Water Skiing - Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "waterday";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Summer Water Days</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="images/waterday1.jpg" alt="Jet Skiiing and Boating at Water Day" width="350" height="262">
                <p>
                  <font color="#008000">Jet Skiiing and Boating at Water Day</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="images/waterday2.jpg" alt="Water Day on Lake Santa Fe" width="350" height="262">
                <p>
                  <font color="#008000">Sailing at Water Day on Lake Santa Fe</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="images/waterday3.jpg" alt="Jet Skiing and Kayaking at Water Day" width="350" height="262">
                  <p>
                    <font color="#008000">Jet Skiing and Kayaking at Water Day</font>
              </td>
            </tr>
          </table>
        </div>
        <p>
          Cool off during the dog days of summer on Lake Santa Fe.&nbsp; Water ski and
          tube behind the boats, swim, float, laugh and picnic on shore, and spend the
          day with friends!<p>
            Since a few members of the Adventure Club have powerboats, they generously
            share them during the summer, posting a Water Day for members only, held
            about once a month.<p>
              Past events have included jet skiing, boating, water skiing, knee boarding
              and tubing, as well as sailing, kayaking and swimming.&nbsp; Members bring
              picnic baskets of food to share for a cookout, and of course, plenty of
              beverages.<p>
                A favorite Water Day is held on the 4th of July, when we gather on the dock
                and watch the Melrose and Keystone Heights fireworks displays, as well as
                other locals who just can't resist lighting up the skies.&nbsp; Lucky us!&nbsp;
                It's quite a show and goes on for well over an hour once it gets dark.<p>
                  Members chip in a $5 donation for gas for the boats, and everyone gets many
                  chances to take boat rides and join in the water activities.<p>
                    So, bring your towels, sunscreen, and something sweet to share with
                    friends--and join us for the next Water Day!<b><br>
                      <br>
                      See our Events Listing for the next Scalloping Trip:<br>
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