<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Manatee Kayaking Trips in the Gainesville Crystal River Florida Area FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "manatee";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Manatee Kayaking Trips</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="images/manateekayaking1.jpg" alt="Manatee Kayaking in Crystal River" width="350" height="232">
                <p>
                  <font color="#008000">Close Encounter With a Manatee</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="images/manateekayaking2.jpg" alt="Manatee Kayak Crystal River" width="350" height="232">
                <p>
                  <font color="#008000">Manatees Sanctuary at Three Sisters, Crystal River</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="images/manateekayaking3.jpg" alt="Manatee Kayaking in Crystal River, Florida FL" width="350" height="232">
                  <p>
                    <font color="#008000">Manatee Kayaking in Crystal River</font>
              </td>
            </tr>
          </table>
        </div>
        <p>Want to see a real &quot;mermaid?&quot;&nbsp; Join us for our annual Manatee Kayaking
          Trip
          posted in February each year when we travel to Crystal River, Florida, to
          seek out these gentle, giant river dwellers and get a close-up look at them
          while they winter here.&nbsp;
          <p><b>About Manatees</b>
            <p>There are six manatee sanctuaries in the Crystal
              River's headwaters at Kings Bay that protect approximately 39 acres of
              essential manatee habitat. The sanctuaries were created to provide manatees
              areas where they could retreat from people during their winter-long stay in
              the area. Kings Bay is considered the most important winter refuge for
              manatees on Florida's west coast. More than 250 manatees are known to winter
              here.<p>Manatees have a large, seal-like body that tapers to a powerful flat
                tail and two agile forelimbs with<br>
                three to four toenails on each, which act like arms to help the manatee
                maneuver in shallow water, grasp and move food toward their mouths, and act
                like flippers during swimming. They average 9 to 10 feet long, weighing
                around 1,000 lbs and can grow as large as 13 feet and weigh more than 3,000
                lbs.
                <p>Gentle and slow-moving, manatees spend most of the time eating vegetation
                  (100-150 lbs. per day), resting, and traveling. On average manatees can
                  travel about 40 to 50 miles a day, sometimes farther. Chessie, the famed
                  manatee rescued from the cold waters of the Chesapeake Bay and returned to
                  Florida, was tagged with a locating device which showed he traveled as far
                  as Rhode Island during hot summer months.<p>Manatees are found in costal
                    waterways, estuaries, salt-water bays, rivers and canals, particularly where
                    seagrass beds are located. Manatees are completely herbivorous and can eat
                    10-15% of their body weight daily.
                    <p><b>About Kayaking</b>
                      <p>Kayaking is an easy and fun way to get close to
                        manatees without disturbing them.&nbsp; Almost anyone can get the hang of
                        kayaking right away, and you don't have to
                        be really fit, just able to do some moderate activity.<br>
                        <br>
                        You don't even need your own kayak. You can rent a kayak on location at
                        Bird's Underwater or another nearby shop. This trip usually ranges in price from $25 to $40 for the
                        day, and the paddle typically takes only a few hours.<p>
                          Some of our members also rent out their personal kayaks at reasonable rates.&nbsp; You need to
                          pick the kayak up and transport it yourself in most cases, but sometimes
                          members can even help with that, too.<br>
                          <br>
                          Kayaks are a little like canoes, except kayaks are generally much lighter
                          and easier to steer. Kayaks are usually built for one person. You use one
                          long double-ended paddle to propel yourself through the water. In my
                          opinion, the easiest kayaks to use are the &quot;sit-on-top&quot; kayaks, where you
                          are above the water line. This gives you more leverage when dipping your
                          paddle into the water.<p>
                            <b>See our Events Listing for the next
                              Manatee Kayaking Trip:<br>
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