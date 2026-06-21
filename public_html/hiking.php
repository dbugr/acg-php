<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Hikes and Hiking in the Gainesville Florida Area FL - ' . $ClubCompanyName;
require('top.php');

$thisPage = "hiking";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>
        <h3>
          Hiking Around Florida</h3>
        <div align="right">
          <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" align="right">
            <tr>
              <td>
                <img border="0" src="images/hiking1.jpg" alt="Hiking at Torreya State Park, Florida" width="350" height="265">
                <p>
                  <font color="#008000">Hiking at Torreya State Park, Florida Panhandle</font>
              </td>
            </tr>
            <tr>
              <td>
                <font color="#008000">&nbsp;</font>
              </td>
            </tr>
            <tr>
              <td>
                <img border="0" src="images/hiking2.jpg" alt="Hiking Paynes Prairie" width="350" height="234">
                <p>
                  <font color="#008000">Hiking Payne's Prairie</font>
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;</td>
            </tr>
            <tr>
              <td>
                <p align="center">
                  <img border="0" src="images/hiking1.jpg" alt="Hiking Oleno State Park, Florida FL" width="350" height="233">
                  <p>
                    <font color="#008000">Hiking O'Leno State Park, Florida FL</font>
              </td>
            </tr>
          </table>
        </div>
        <p>
          Whether you want to spend a few hours or camp overnight, we offer a variety
          of hiking trips to suit your passion.&nbsp;
          <p>
            Most trips are scheduled in moderate weather in the Central Florida area.&nbsp;
            Locations we frequent include: Payne's Prairie, Newnan's Lake and O'Leno
            State Park.&nbsp; We've also hiked in Torreya State Park in Florida's
            Panhandle and incorporated short hikes into our whitewater paddles,
            overnight kayaking trips, and camping events.<p>
              Hikes around Gainesville and other areas are coordinated by our event leaders, who map out the routes and plan
              all details of your trip.&nbsp; Some hikes are simply short jaunts of two to
              three miles around a local park, such as Morningside Nature
              Park that has several trails through its wooded grounds.&nbsp; Other hikes
              involve several miles of hiking, usually in the state parks around the
              Central Florida area.<p>
                Adventure Club members can login to our website and get all the details for
                hiking events and sign up online.&nbsp; Here are some of the places we go
                hiking here in the Gainesville, Florida vicinity:<p>
                  <b>Payne's Prairie Preserve State Park</b><br>
                  Paynes Prairie Preserve State Park is located in north central Florida in
                  Alachua County. It is known for its outstanding wildlife viewing
                  opportunities including bison, cracker horses and cattle, alligators,
                  sandhill cranes, and over 270 bird species. The principal physical feature
                  of the preserve is a 16,000 acre freshwater marsh/wet prairie known as
                  Paynes Prairie. The preserve includes the Prairie and some of the
                  surrounding uplands for a total of nearly 22,000 acres. Paynes Prairie
                  Preserve State Park is recognized as one of the most significant natural and
                  cultural areas in Florida. It is the first preserve in the Florida Park
                  System and was nominated to the National Register of Natural Landmarks in
                  1974.<p>
                    <b>Newnan's Lake</b><br>
                    This large, 6,000-acre lake on eastern border of Gainesville is a great lake
                    to canoe or kayak or bike alongside. Lots of gators, wading birds, osprey,
                    eagles.<p>
                      <b>O'Leno State Park</b><br>
                      Located along the banks of the scenic Santa Fe River, a tributary of the
                      Suwannee River, the park features sinkholes, hardwood hammocks, river
                      swamps, and sandhills. As the river courses through the park, it disappears
                      underground and reemerges over three miles away in the River Rise State
                      Preserve. One of Florida's first state parks, O'Leno was first developed by
                      the Civilian Conservation Corps (CCC) in the 1930s. The suspension bridge
                      built by the CCC still spans the river. Visitors can picnic at one of the
                      pavilions or fish in the river for their dinner. Canoes and bicycles are
                      available for rent. While hiking the nature trails, visitors can look for
                      wildlife and enjoy the beauty of native plants. The shady, full-facility
                      campground is the perfect place for a relaxing overnight stay. Located on
                      U.S. 441, six miles north of High Springs.<p>
                        <b>See our Events Listing for the next
                          Hiking Trip:<br>
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