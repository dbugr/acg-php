<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Kayaking Trips in the Gainesville Florida Area FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "kayaking";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Fun and Easy Kayaking Trips</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="images/kayaking1.jpg" alt="Manatee Kayaking in Crystal River" width="350" height="232">
                <p>
                  <font color="#008000">Manatee Kayaking in Crystal River</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="images/kayaking2.jpg" alt="Sunset Kayaking in Cedar Key" width="350" height="262">
                <p>
                  <font color="#008000">Sunset Kayaking in Cedar Key</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="images/kayaking3.jpg" alt="Kayaking the Santa Fe River" width="350" height="262">
                  <p>
                    <font color="#008000">Kayaking the Santa Fe River</font>
              </td>
            </tr>
          </table>
        </div>
        <p>Want to get outdoors and try something new, but not sure you're
          adventurous enough? Kayaking is a great place to start. Kayaking is easy and
          fun, and almost anyone can get the hang of it right away. You don't have to
          be really fit, just able to do some moderate activity.<br>
          <br>
          You don't even need your own kayak. You can rent a kayak on location for
          most of our events. Trips usually range in price from $25 to $40 for the
          day, and a paddle down a river typically takes a few hours to half a day.<p>
            Some of our members also rent kayaks at reasonable rates.&nbsp; You need to
            pick the kayak up and transport it yourself in most cases, but sometimes
            members can even help with that, too.<br>
            <br>
            Kayaks are a little like canoes, except kayaks are generally much lighter
            and easier to steer. Kayaks are usually built for one person. You use one
            long double-ended paddle to propel yourself through the water. In my
            opinion, the easiest kayaks to use are the &quot;sit-on-top&quot; kayaks, where you
            are above the water line. This gives you more leverage when dipping your
            paddle into the water.<br>
            <br>
            <b>Where do we go on kayak trips?</b> We go all over the Central Florida
            area. Usually, we stay close by, within a 1 or 2-hour drive of the kayaking
            location. We've been to Cedar Key, the Santa Fe River, the Waccassassa
            River, Rainbow Springs, Crystal River, the Silver River, the Suwannee, and
            Juniper Springs, just to name a selection of places we frequent.<p><b>See our Events Listing for the next
                Kayaking Trip:<br>
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