<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Scalloping Trips - Steinhatchee Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "scalloping";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Scalloping Trips</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="/images/scalloping1.jpg" alt="Scalloping at Steinhatchee" width="350" height="231">
                <p>
                  <font color="#008000">Scalloping at Steinhatchee, Florida</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="/images/scalloping2.jpg" alt="Scalloping at Steinhatchee, Florida" width="350" height="231">
                <p>
                  <font color="#008000">Scalloping at Steinhatchee, Florida</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="/images/scalloping3.jpg" alt="Kayak Scalloping from Hagan's Cove, Steinhatchee, Florida" width="350" height="262">
                  <p>
                    <font color="#008000">Kayak Scalloping from Hagan's Cove, Steinhatchee,
                      Florida</font>
              </td>
            </tr>
          </table>
        </div>
        <p>
          The Steinhatchee River is a fisherman's mecca and a kayaker's paradise. The
          Adventure Club travels there at least once a summer to catch succulent bay
          scallops.&nbsp; Afterward, we come back to Gainesville to cook up our catch
          and have a potluck feast.<p>
            Scallops appear in the grassy, shallow, warm waters in the summer months.&nbsp;
            Their purple eyes lining their clam-like shells are simple to spot in the
            3-to-4-foot depths amid the sea grass, and it's easy pickings to grab 'em
            and bag 'em as you snorkel.<p>
              Scallopers need saltwater fishing licenses. An annual license for Floridians
              is about $14.&nbsp; You can pick one up at marinas, bait-and-tackle shops
              and sporting goods stores, or buy one online and print out a temporary copy.<p>
                Often, we rent boats from Sea Hag Marina or River Haven Marina and make our
                own flotilla as we motor out to the sandbars.&nbsp; But you don't need a
                boat for great scalloping. At Hagan's Cove you can kayak or wade right into
                the marsh. Hagan's Cove is 14 miles north of Steinhatchee.<p>
                  We also charter a single boat for a small group, so we can schedule more
                  trips during the height of the season.<p>
                    Once we've caught our 5-gallon limit per boat, we head back to the tavern
                    while the &quot;dock ladies&quot; shuck the scallops for us.&nbsp; At a few bucks per
                    gallon or so, it's worth the money!<br>
                    <br>
                    Steinhatchee is considered one of the richest and most popular scalloping
                    spots.&nbsp; Scallops are largest in August, when we schedule most of our
                    trips.<b><br>
                      <br>
                      See our Events Listing for the next
                      Scalloping Trip:<br>
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