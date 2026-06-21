<?php

require('always.include.php');
//session_start();
//require('include.php');


function FindNextPublicEvent()
{
	global $hostName;
	global $username;
	global $password;
	global $databaseName;
	
	$today = date('m/d/Y',time());
  $list_start_date =  quotesqldata($today);

  mysqlconnect($connection);

  // obtain event data
  $query = "SELECT * FROM events ".
					 "WHERE (events.e_begindate >=  '" . MySqlDate($list_start_date) . "') AND ".
					 				"(events.e_display = 'Public') AND (e_category = 'Meet-n-Greet') ORDER BY events.e_begindate";

	if (!($result = @ mysqli_query($connection,$query)))
   	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);

  // put event data into an array
  $e_recs = array();
  $e_row = mysqli_fetch_array($result);
  $event_id = 0;
	if (isset( $e_row['event_id']))
		$event_id = $e_row['event_id'];
	return $event_id;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $ClubCompanyName; ?> home page</title>
<meta name="keywords" content="recess, adults, activities, indoor, outdoor, single, divorced, central, florida" >
<meta name="description" content="Central Florida Activity Club - - Recess for Adults! Home of indoor and outdoor fun for 
single, married, widowed and divorced people of all ages">
<meta name="Robots" content="index,follow">
<meta name="Author" content="<?php echo $ClubCompanyName; ?>" >
<link rel=STYLESHEET href="/inc/club.css" Type="text/css">
<?php

// obtain next social meet n' greet event data

// Set table border value
$border = " border=0";

// Reset $formVars, since we're loading from
// the customer table
$formVars = array();

// Reset the errors
$errors = array();

// specify which meet n' greet event to 
// display on the home page
$event_id = FindNextPublicEvent();

  mysqlconnect($connection);

$event_id = quotesqldata($event_id);

$query = "SELECT * FROM events
        where event_id = " . $event_id;

if (!($result = @ mysqli_query($connection,$query)))
        trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);

$row = mysqli_fetch_array($result);

     // Load all the form variables with customer data
     $formVars["event_id"] = $row["event_id"];
     $formVars["location_id"] = $row["location_id"];
     $formVars["leader_id"] = $row["leader_id"];
     $formVars["name"] = $row["e_name"];
		 $formVars["begindate"] = 
     	 date('D, M j',strtotime($row["e_begindate"]));

     $formVars["begintime"] = $row["e_begintime"];
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
?>

</head>

<body>
<div id="centercontent2">
<hr>
<?php echo $ClubCompanyName; ?> targets active professionals of any marital status.
The club brings together two things: people who like to do things, and things to do.
<p>

<p><b>Members take three steps to fabulous fun: 1. Access calendar on-line &nbsp; &nbsp;  2. Register for 
event &nbsp;  3. Go have fun! </b>&nbsp;&nbsp; <a href="/subscribe.php">Click here</a> 
to receive the newsletter.
<hr>
  <table>
  <tr>
  <td>
    	Access the on-line event calendar at work or at home.  Memberships are on 
			a month-to-month basis, no one-time fee, no annual agreements to sign.  
		Check out the Club's activity calendar:
		<center>
			
 			<table cellspacing=5>
 				<tr>
 					<td>
 					<ul>
							 <li><b>hiking</b></li>
  						 <li><b>deep sea fishing</b></li>
  						 <li><b>bicycle</b></li>
  						 <li><b>kayak</b></li>
					</ul>
				</td>
				<td>
 					<ul>
							 <li><b>happy hours</b></li>
  						 <li><b>theme parties</b></li>
  						 <li><b>bowling</b></li>
  						 <li><b>weekend getaways</b></li>
					</ul>
				</td>
			</tr>
		</table>
		
		</center>
		Join the club and discover how easy it is to say <i>yes</i> to fun! 
		<p><p>&nbsp;
	  <font color=red> Be the first to find the Star Trek reference on the Club's website
		 and get one month of membership FREE!</font>
		</td>
		<td valign="top">
  				<table border=1 cellspacing=5>
  					<tr>
  						<td bgcolor="#E1BC88" align=center>
								<b><?php echo $formVars["name"]; ?></b>
							</td>
						</tr>
						<tr>
							<td align=center>
								<?php
									$msg = $formVars["details"];
									$k = strpos ( $msg, "<br>");
									echo substr( $msg, 0, $k);
								?>
							</td>
						</tr>
						<tr>
							<td bgcolor="#E1BC88" align=center title="Where">      					
								<?php echo $formVars["location_name"]; ?>
      				</td>
						</tr>
						<tr>
							<td bgcolor="#E1BC88"  align=center title="When">      					
      			    <?php echo $begintime . "  " . $formVars["begindate"]; ?>
      				</td>
      			</tr>
      			<tr>
							<td bgcolor="#E1BC88">
      					<?php echo $formVars["driving_directions"]; ?>
     					</td>
  					</tr>
  				</table>
		</td>
		</tr>
	</table>
</div>

<?php TopBanner("Welcome to Recess for Adults!", "Home", 1); ?>
</body>
</html>