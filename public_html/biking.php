<?php

require('always.include.php');
//$debug = true;
//$debug = false;
//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Biking Cycling Bike Riding in the Gainesville Florida Area FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "biking";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        <h3>
          Biking, Bike Rides and Cycling Around Florida</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="/images/biking1.jpg" alt="Biking to Brunch on the Hawthorne Trail" width="350" height="287">
                <p>
                  <font color="#008000">Biking to Brunch on the Hawthorne Trail</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="/images/biking2.jpg" alt="Bike Florida" width="350" height="262">
                <p>
                  <font color="#008000">Bike Florida</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="/images/biking3.jpg" alt="Mountain Biking San Felasco" width="350" height="262">
                  <p>
                    <font color="#008000">Mountain Biking San Felasco</font>
              </td>
            </tr>
          </table>
        </div>
        <p>
          Nice weather makes cycling a year-round event here in Gainesville and
          all over Florida.&nbsp; Rides can be a short 1 or 2-hour trip or last all
          day.&nbsp; It's up to you!&nbsp; And you don't need to be an experienced
          rider.&nbsp; We accommodate riders of all levels and ride at a moderate
          speed.&nbsp; No one is left behind.<p>
            We schedule biking trips nearly every month, including many
            organized rides sponsored by bike clubs and charities.&nbsp; Here are just
            some of the bike rides we participate in:<p>
              <b>Gainesville Cycling Festival</b><br>
              Usually spanning a weekend in October. The Santa Fe Century and Horse Farm
              Hundred routes comprise the 2-day, back-to-back century event. On Saturday
              is the &quot;Millhopper Ramble,&quot; an 18 or 27 mile family ride. <p>
                <b>A Ride to Remember</b><br>
                A benefit for ElderCare of Alachua County, supporting Al'z Place Alzheimer's
                Day Care<p>
                  <b>MS150 Ride - Central Florida Chapter</b><br>
                  Road Ride to raise money for Multiple Sclerosis<br>
                  Busch Gardens to Sea World and back<p>
                    <b>MS150 Ride - North-Florida Chapter</b><br>
                    Road Ride to raise money for Multiple Sclerosis<br>
                    St. Augustine to Daytona Beach<p>
                      <b>Bike Florida</b><br>
                      Every year, Bike Florida hosts a weeklong bicycle camping adventure tour.
                      The route varies, linking small towns with natural and historic landmarks
                      along scenic country roads. The event is fully supported with assistance
                      from local law enforcement agencies and safety signage, alerting motorists
                      to &quot;SHARE THE ROAD&quot; with up to 1000 cyclists on this weeklong adventure.<p>
                        <b>Ozello Adventure Race</b><br>
                        An individual or team competition with running, bicycling and kayaking in a
                        relay type race held once a year.<p>
                          <b>Nature Coast State Trail</b><br>
                          Nature Coast State Trail is officially designated as part of Florida�s
                          Statewide System of Greenways and Trails. Traversing Florida's beautiful
                          Nature Coast region, this trail provides an excellent opportunity to
                          experience the Sunshine State &quot;off the beaten path.&quot; The trail consists of
                          two primary alignments built along abandoned rail lines that intersect at
                          Wilcox Junction, connecting the communities of Cross City, Trenton, Fanning
                          Springs and Chiefland. Among the trail's highlights is a historic train
                          trestle that allows trail goers to cross over the Suwannee River near Old
                          Town. The trail is also close to nearby Fanning Springs State Park and
                          Andrews Wildlife Management Area. Don't miss this trail, a jewel of the
                          Nature Coast region.<p>
                            <b>Gainesville-Hawthorne Trail</b><br>
                            Come bike to brunch on the Hawthorne Trail . We ride from the Boulware
                            Trailhead, or you can meet us on just the flat part of the trail in Rochelle
                            at the 6-mile marker. Bike into Hawthorne and take a lunch break at PJ's
                            Cafe. Yummy food, friendly service, excellent prices. It is a 15-mile ride
                            to Hawthorne from Boulware, and approximately 10 miles from Rochelle. The
                            most distance on this one is 30 miles round trip.<p>
                              <b>See our Events Listing for the next
                                Biking Trip:<br>
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