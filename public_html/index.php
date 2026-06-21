<?php $thisPage = "home"; ?>
<?php
/* index.php */

// Connect to a session
require("always.include.php");
$debug = true;
$debug = false;
//session_start();
//require('include.php');

if (isset($debug) and $debug) {
  //print "debug: $debug<br>";
  //print "path: $path";
  LogMsg('dirname(FILE): ' . DIRNAME(__FILE__));
  LogMsg('$_SERVER[SERVER_NAME]: ' . $_SERVER['SERVER_NAME']);
  LogMsg('$_SERVER[HTTP_HOST]: ' . $_SERVER['HTTP_HOST']);
  LogMsg('gethostname: ' . gethostname());
}

//echo 'redirecting...<br>';
//$firephp->log($connection,"before Redirect...");
// redirect actfl.com to adventureclub.info
//RedirectToPrimaryDomainName(__FILE__);


function FindNextPublicEvent($connection)
{
  global $ClubCode;

  $today = date('m/d/Y', time());
  $list_start_date =  quotesqldata($today);



  // obtain event data
  //					 				"(events.e_display = 'Public') AND (e_category = 'Meet-n-Greet') " .
  $query = "SELECT * FROM events " .
    "WHERE (events.e_begindate >=  '" . MySqlDate($list_start_date) . "') AND " .
    "(e_category = 'Meet-n-Greet') " .
    " AND (events.leader_id LIKE '" . $ClubCode . "%') " .
    "ORDER BY events.e_begindate";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " 
    . mysqli_errno($connection) . " : " 
    . mysqli_error($connection), E_USER_ERROR);

  // put event data into an array
  $e_recs = array();
  $e_row = mysqli_fetch_array($result);
  $event_id = 0;
  if (isset($e_row['event_id']))
    $event_id = $e_row['event_id'];
  return $event_id;
} // FindNextPublicEvent


// obtain next social meet n' greet event data

// Set table border value
$border = " border=0";

// Reset $formVars, since we're loading from
// the customer table
$formVars = array();

// Reset the errors
$errors = array();

//$firephp->log($connection,"before mysqlconnect...");

// connect to the database
mysqlconnect($connection);

//exit;



//$firephp->log($connection,"before FindNextPublicEvent...");
// specify which meet n' greet event to
// display on the home page
$event_id = FindNextPublicEvent($connection);

$event_id = quotesqldata($event_id);

$query = "SELECT * FROM events
        WHERE (event_id = '" . $event_id . "');";

if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " 
    . mysqli_errno($connection) . " : " 
    . mysqli_error($connection), E_USER_ERROR
  );

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Load all the form variables with customer data
$formVars["event_id"] = $row["event_id"];
$formVars["leader_id"] = $row["leader_id"];
$formVars["name"] = $row["e_name"];

$BeginDate = (isset($row["e_begindate"])) ? $row["e_begindate"] : "01-01-2001";
$formVars["begindate"] =
  date('D, M j', strtotime($BeginDate));

$BeginTime = (isset($row["e_begintime"])) ? $row["e_begintime"] : "00-00-00";
$formVars["begintime"] = $BeginTime;

$formVars["location_name"] = $row["e_location_name"];
$formVars["driving_directions"] = $row["e_driving_directions"];
$formVars["details"] = $row["e_details"];

// convert time to 12 hr am/pm format
$begintime = $formVars['begintime'];
$begintime = date('g:ia', strtotime($begintime));


if (isset($formVars["status"])) {
  if ($formVars["status"] == "Approved")
    $statusMsg = "";
  else {
    if ($formVars["status"] == "Planning")
      $statusMsg = "PLANNING Stage!";
    elseif ($formVars["status"] == "Postponed")
      $statusMsg = "POSTPONED Event POSTPONED!";
    elseif ($formVars["status"] == "Canceled")
      $statusMsg = "CANCELED Event CANCELED!";
    else
      $statusMsg = $formVars["status"];
  }
}


$FileName = __FILE__;
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'The ' . $ClubCompanyName . ', Florida - social events, activities, things to do';
$IndexPage = true;
require('top.php');

?>
&nbsp;
<?php
require('features.php');
?>
<!-- gainesville florida social activities events things to do places to go -->
<td width="75%" valign="top">


  <table border="0" style="border-collapse: collapse" bordercolor="#111111" cellpadding="8" cellspacing="0">
    <tr>
      <td colspan="3">
        <h2>
          <font color="#003366">Attention Members:</font>
          <font color="#009933">Is Your Website Access Expired?</font> <a href="renew.php">Renew Now!</a>
        </h2>
        <p align="center">
  </table>

  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" width="316">
    <tr>
      <td width="316">
        </embed>
        <img border="0" src="images/youre_invited.jpg" alt="You're Invited to Join Us" width="316" height="45"></td>
      <td>
        &nbsp;&nbsp; </td>
      <td width="45%" rowspan="2">
        <h2>Follow Your Dreams to Great Adventures</h2>
        <img border="0" height="280" width="420" src="/images/IMG_4426.jpg" alt="White Water Rafting Bull Sluice Chattooga River">
      </td>
    </tr>
    <tr>
      <td width="316" bgcolor="#003366">
        <table border="0" cellpadding="8" cellspacing="8" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber3">
          <tr>
            <td width="100%">
              <p align="center">
                <span style="line-height:18px; margin-bottom:0px;">
                  <font face="Verdana" size="2" color="#FFFFFF">
                    Please come be our guest at our next<br></font>
                  <b>
                    <font color="#99FF99" face="Arial" size="3">MEET n' GREET</font>
                    <font color="#FFFFFF" face="Arial">: </font>
                    <font color="#66FFFF" face="Arial" size="3"><?php echo $begintime . "  " . $formVars["begindate"]; ?></font>
                  </b>
              </p>
              <p align="center">
                <font face="Verdana" size="2" color="#FFFFFF">Free
                  admission. Open to the public. Held at:<br></font>
                <font color="#66FFFF" size="2" face="Verdana"><?php echo $formVars["location_name"]; ?></font>
                <p align="center">
                  <font face="Verdana" size="2" color="#FFFFFF">Directions:</font>
                  <br>
                  <font face="Verdana" size="2" color="#66ffff"><?php echo $formVars["driving_directions"]; ?></font>
                  <p align="center">
                    </span>
                    <font face="Verdana" size="2" color="#FFFFFF">Get
                      full access to all of our events for only $3 per month,
                      paid annually.<br>
                    </font><b><a href="<?php echo JoinURL(); ?>">
                        <font face="Arial" color="#99FF99" size="4">Join
                          Today!</font>
                      </a></b>
            </td>
          </tr>
        </table>
      </td>
      <td valign="top">
        &nbsp; </td>
    </tr>
  </table>

  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber1" width="100%">
    <tr>
      <td valign="top"><a href="sample_event.php">
          <img border="0" src="images/sneak-peek.jpg" vspace="5" width="743" height="116"></a></td>
    </tr>
  </table>
  <h2>Like What You See?&nbsp;
    <a class="home" href="<?php echo JoinURL(); ?>">Join
      Now</a> for Access to Events</h2>
  <font color="#009933">
    <b>Who We Are</b><br>
  </font>ACG is a community of professionals in Gainesville, Florida,
  who are constantly looking for new and exciting social activities and things to
  do. Our events are coordinated by an experienced team of event leaders, and are
  designed to accommodate a wide range of interests.&nbsp; <p>
    <font color="#009933">
      <b>There's Always Something Fun Going On</b><br>
    </font>We post an average of 10 to 20 events every month &#8212; enough to keep
    anyone's calendar full. Whether you're looking to plan a
    weekend getaway months in advance, or just feel like doing
    something fun with friends tomorrow night, we've got you
    covered.&nbsp; If you live in or around Gainesville, Florida,
    we have dozens of social activities, group events, and things to do!&nbsp;
  </p>
  <p>
    <font color="#009933"><b>It's easy to join in.</b><br>
    </font>Our website was designed by fellow club members: veteran leaders with
    years of experience coordinating events. With a few mouse clicks, members can
    learn about upcoming adventures, find members with similar interests, and sign
    up for events. We put all the information you need in one place: when to come,
    how to get there, and what to bring. It's really that simple! <br>
    <br>
    <font color="#009933"><b>So, come out and play!</b><br>
    </font>With close to 100 current members, the ACG is a diverse group. No matter what
    your age or physical condition, we have something for you to enjoy. <br>
    <br>
    <font color="#009933"><b>Traveling on a Florida Holiday?&nbsp; Join Us!<br>
      </b>
    </font>People traveling on their
    Florida
    holidays</a> can take part in our organized events and
    activities here in Gainesville, Florida. The <b>Adventure Club of Gainesville</b>
    offers more than just adventure<font face="Verdana">: </font>it
    is a chance to connect with people like you, and have an
    amazing holiday in Florida.&nbsp; <a href="contact.php">
      Contact us</a> to find out more.
  </p>
  <p><a class="home" href="<?php echo JoinURL(); ?>"><img border="0" src="images/join-now.png" width="220" height="42"></a></p>


  <?php include 'more_events.php'; ?>

  <?php
  $IndexFile = true;
  require('footer.php');
  ?>