<?php

$debug = true;
//$debug = false;

require('always.include.php');

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Whitewater Rafting Trips - Gainesville Florida FL - ' . $ClubCompanyName;
require('top.php');

$thisPage="whitewater";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
  <tr>
    <td width="100%">
    </embed>
    <h3>
    Whitewater Rafting</h3>    <div align="right">
      <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
        <tr>
          <td>
    <img border="0" src="/images/whitewater1.jpg" alt="Whitewater Rafting on the Ocoee" width="350" height="238"><p>
    <font color="#008000">Whitewater Rafting on the Ocoee</font></td>
        </tr>
        <tr>
          <td>
    <font color="#008000">&nbsp;</font></td>
        </tr>
        <tr>
          <td>
    <img border="0" src="/images/whitewater2.jpg" alt="Whitewater Rafts and Duckies on the Nantahala" width="350" height="233"><p>
    <font color="#008000">Whitewater Rafts and Duckies on the Nantahala</font></td>
        </tr>
        <tr>
          <td>
    &nbsp;</td>
        </tr>
        <tr>
          <td>
          <p align="center">
    <img border="0" src="/images/whitewater3.jpg" alt="Whitewater Rafting the Chattooga" width="350" height="233"><p>
          <font color="#008000">Whitewater Rafting the Chattooga</font></td>
        </tr>
        </table>
    </div>
    <p>
    Do you want intense action, a wilderness experience, a reinvigorating 
    getaway or a bouncy ride with your family? Our yearly whitewater trip has 
    distinct experiences for everyone.<p>
    Past locations have included the Chattooga, the Nantahala and the Ocoee 
    Rivers.&nbsp; Here's a little about each run:<br>
    <br>
    <b>Nantahala River Whitewater Rafting</b><br>
    The shimmering Nantahala River offers whitewater rafting through mild but 
    exciting rapids. Nantahala rafting features eight miles of practice on easy 
    Class II rapids before splashing through the exciting Class III whitewater 
    of Nantahala Falls. This is one of America�s most popular whitewater runs, 
    so expect to see other paddlers playing in the waves.<br>
    <br>
    The &quot;Nanny&quot; is a relatively gentle river suitable for the whitewater novice. 
    We rent &quot;duckies,&quot; inflatable 1-person or tandem kayaks for a few-hour trip 
    down this run.<br>
    <br>
    <b>Middle Ocoee River</b><br>
    The exciting Middle Ocoee rafting trip begins against the roaring backdrop 
    of whitewater falling down Ocoee Dam #2, with the explosive entrance rapid 
    called &quot;Grumpy's.&quot; The action hardly slows down during this five-mile 
    stretch of whitewater. Highlights include negotiating major Class III and IV 
    rapids with names like Table Saw, Broken Nose, Double Trouble, Double Suck 
    and Powerhouse.<br>
    <br>
    <b>Upper/Middle Ocoee Combo</b><br>
    The Ocoee Combo whitewater trip adds five miles of world-class whitewater to 
    the already-loaded Middle Ocoee trip. Featuring the 1996 Olympic Canoe and 
    Kayak course, the Upper Ocoee doubles the fun with Olympic caliber thrills! 
    They weren't kidding when they named one of this section's rapids 
    &quot;Humongous!&quot;<br>
    <br>
    On the Ocoee, we have professional guides who insure an exciting, SAFE trip 
    down this challenging run. After the Nantahala, beginners can certainly 
    enjoy this trip. Just listen to your guide and hold on!<p>
    <b>Chattooga River</b><br>
    With spectacular falls and an abundance of wildlife, the Chattooga is one of 
    the top destinations for adventurers and thrill seekers. Southeastern 
    Expeditions offers whitewater rafting, canoe and kayaking trips on the 
    Chattooga River in Northeast Georgia and South Carolina. The Chattooga 
    provides miles of Class II to Class V whitewater rafting for both the first 
    time rafters and the &quot;ledge dropping&quot; risk takers. With the Sumter and 
    Chattahoochee National Forest surrounding the river, we guarantee that your 
    Chattooga whitewater rafting experience will be packed with beauty and 
    excitement for everyone! <b><br>
    <br>
    See our Events Listing for the next 
    Whitewater Rafting Adventure:<br>
    </b><a href="/elist-pub.php">
    /elist-pub.php</a> 
    <p><br>
    <b><font size="4">Events are open to all members!</font></b>&nbsp; <br>
    Not a member yet?&nbsp; <b>Join us: </b>
    <a href="<?php echo GetParameter('vd').'/join.php'; ?>">
    <?php echo GetParameter('vd').'/join.php'; ?></a> 
    <p><?php include 'more_events.php'; ?></td>
  </tr>
</table></div>

<?php 

require('footer.php');

?>