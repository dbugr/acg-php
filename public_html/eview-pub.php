<?php
// given an event_id, allow user to see event
// details and who is signed up.
// permit them to sign up for the event.

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

function getRealIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

//// code starts here ////

// get and validate the event number
if (isset($_GET['event_id'])) {
	$event_id = trim(clean($_GET['event_id'],12));
} else {
	$event_id = "";
}

// redirect to the elist-pub.php page with bad event_id
if (empty($event_id)) {
	$loc = "Location: /elist-pub.php";
	header($loc);
	exit;
}

// valicate event_id. Correct form: clubcode_eventnumber
if (!preg_match("/^gnv_+[0-9]+$/",$event_id)) {
	LogMsg("Error malformed event_id: ".$event_id);
	$event_id = "";
	$loc = "Location: /elist-pub.php";
	header($loc);
	exit;
}

// Reset $formVars
$formVars = array();

// Reset the errors
$errors = array();

// connect to the database
mysqlconnect($connection);

// get event
$event_id = quotesqldata($event_id);
$query = "SELECT * FROM events WHERE (event_id = '" . $event_id . "');";
if (!($result = @mysqli_query($connection, $query)))
trigger_error("MySQL error: " 
	. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR
);

$NumRowsReturned = mysqli_num_rows($result);

if ($NumRowsReturned == 0) {
	$RemoteIPNumber = getRealIpAddr();
	LogMsg('Warning bad event_id : '.$event_id
		.'   $NumRowsReturned: '.$NumRowsReturned
		.'   $RemoteIPNumber: '.$RemoteIPNumber
	);
	$loc = "Location: /elist-pub.php";
	header($loc);
	exit;
	}


$row = mysqli_fetch_array($result);

// Load all the form variables with customer data
$formVars["event_id"] = $row["event_id"];
$formVars["leader_id"] = $row["leader_id"];
$formVars["name"] = $row["e_name"];
$formVars["begindate"] = $row["e_begindate"];
$formVars["details"] = $row["e_details"];
$formVars["status"] = $row["e_status"];


if (isset($formVars["status"])) {
	if ($formVars["status"] == "Approved")
		$statusMsg = "";
	else if ($formVars["status"] == "SelfGuided")
		$statusMsg = "SELF GUIDED EVENT!";
	else if ($formVars["status"] == "Proposed")
		$statusMsg = "Proposed Event, NOT firm.";
	else if ($formVars["status"] == "Postponed")
		$statusMsg = "POSTPONED Event POSTPONED!";
	else if ($formVars["status"] == "Canceled")
		$statusMsg = "CANCELLED Event CANCELLED!";
	else
		$statusMsg = $formVars["status"];
}

$begintime = '';
$location_name = 'MEMBERS ONLY';
$contact_phone = 'MEMBERS ONLY';
if (isset($row['e_display']) and ($row['e_display'] == 'Public')) {
	$begintime = $row["e_begintime"];
	$begintime = date('g:ia', strtotime($begintime));
	$location_name = $row["e_location_name"];
	$contact_phone = GetParameter('ContactPhoneNumber1');
	$formVars["fullprice"] = $row["e_fullprice"];
	$formVars["fullprice_date"] = $row["e_fullprice_date"];
	$formVars["min_attendees"] = $row["e_min_attendees"];
	$formVars["max_attendees"] = $row["e_max_attendees"];
	$formVars["details_private"] = $row["e_details_private"];
	$formVars['url1'] = $row['e_url1'];
	$formVars['url2'] = $row['e_url2'];
	$formVars['url3'] = $row['e_url3'];
	$formVars['url4'] = $row['e_url4'];
	$formVars['location_meet_at'] = $row['e_location_meet_at'];
	$formVars['driving_directions'] = $row['e_driving_directions'];
	$formVars['contingency_plan'] = $row['e_contingency_plan'];
	$formVars['bring'] = $row['e_bring'];
	$formVars['includes'] = $row['e_includes'];
}

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Event details ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">
	<?php
	echo "<h3>";
	echo $formVars["name"];
	if (isset($statusMsg))
		if ($statusMsg)
			echo "<br>" . $statusMsg;
	echo "</h3>";


	?>

	<table width="100%" border="1" bordercolor="#111111" cellpadding="2" cellspacing="0" style="border-collapse: collapse">
		<tr>
			<td bgcolor="#eeeeee" class="right"><b>Where:</b></td>
			<td><?php echo stripslashes($location_name); ?>&nbsp;</td>
		</tr>

		<tr>
			<td bgcolor="#eeeeee" class="right"><b>When:</b></td>
			<td>
				<?php
				if (isset($row["e_begindate"])) {
					$strBeginTime = strtotime($row["e_begindate"]);
				} else {
					$strBeginTime = '0000000001';
				}
				/* LogMsg('$event_id: '.$event_id
		.'   $row[e_begindate]: '.$row['e_begindate']
		.'   $strBeginTime: '.$strBeginTime
		.'   $begintime: '.$begintime
		.'   $NumRowsReturned: '.$NumRowsReturned
		);
	*/
				echo date('D M j', $strBeginTime) . "&nbsp;&nbsp;"; // . $begintime;
				?>
				&nbsp;</td>
		</tr>

		<tr>
			<td bgcolor="#eeeeee" class="right"><b>Contact:</b></td>
			<td>
				<?php
				echo '<a href="/contact.php">Contact Form</a>';
				?>
				&nbsp;</td>
		</tr>

		<tr>
			<td colspan=2><b>Public details:</b> <?php echo stripslashes($formVars["details"]); ?></td>
		</tr>

		<?php
		if (isset($row['e_display']) and ($row['e_display'] == 'Public')) {
			if (!empty($formVars["details_private"]))
				echo "<tr><td colspan=2><b>Member details:</b> " . stripslashes($formVars["details_private"]) . "</td></tr>";

			if (isset($row['e_fullprice'])) {
				if ($row['e_fullprice'] > 0) {
					$cost = sprintf("$ %.02f", $row['e_fullprice']);
					$duedate = "";
					if (isset($row['e_fullprice_date']))
						$duedate = " due by " . date('D, M j', strtotime($row["e_fullprice_date"]));
					echo '<tr><td  class="right"  bgcolor="#eeeeee">Total Cost:</td><td>' . $cost . $duedate . '</td></tr>';
				}
			}

			if (isset($row['e_deposit'])) {
				if ($row['e_deposit'] > 0) {
					$cost = sprintf("$ %.02f", $row['e_deposit']);
					$duedate = "";
					if (isset($row['e_deposit_date']))
						$duedate = " due by " . date('D, M j', strtotime($row["e_deposit_date"]));
					echo '<tr><td  class="right"  bgcolor="#eeeeee">Deposit:</td><td>' . $cost . $duedate . '</td></tr>';
				}
			}

			if (!empty($formVars["url1"])) {
				$aURL = parse_url($formVars["url1"]);
				$host = (isset($aURL['host'])) ? $aURL['host'] : 'Unknown';
				echo '<tr><td  class="right"  bgcolor="#eeeeee">URL1:</td>';
				echo '<td><a href="' . $formVars['url1'] . '" target="url1">'
					. $host . '</a></td></tr>';
			}
			if (!empty($formVars["url2"])) {
				$aURL = parse_url($formVars["url2"]);
				$host = (isset($aURL['host'])) ? $aURL['host'] : 'Unknown';
				echo '<tr><td  class="right"  bgcolor="#eeeeee">URL2:</td>';
				echo '<td><a href="' . $formVars['url2'] . '" target="url2">'
					. $host . '</a></td></tr>';
			}
			if (!empty($formVars["url3"])) {
				$aURL = parse_url($formVars["url3"]);
				$host = (isset($aURL['host'])) ? $aURL['host'] : 'Unknown';
				echo '<tr><td  class="right"  bgcolor="#eeeeee">URL3:</td>';
				echo '<td><a href="' . $formVars['url3'] . '" target="url3">'
					. $host . '</a></td></tr>';
			}
			if (!empty($formVars["url4"])) {
				$aURL = parse_url($formVars["url4"]);
				$host = (isset($aURL['host'])) ? $aURL['host'] : 'Unknown';
				echo '<tr><td  class="right"  bgcolor="#eeeeee">URL4:</td>';
				echo '<td><a href="' . $formVars['url4'] . '" target="url4">'
					. $host . '</a></td></tr>';
			}
			if (!empty($formVars["location_meet_at"])) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Meet At:</td>';
				echo '<td>' . stripslashes($formVars["location_meet_at"]) . '</td></tr>';
			}
			if (!empty($formVars["driving_directions"])) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Directions:</td>';
				echo "<td>" . stripslashes($formVars["driving_directions"]) . '</td></tr>';
			}
			if (!empty($formVars["contingency_plan"])) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Contingency Plan:</td>';
				echo "<td>" . stripslashes($formVars["contingency_plan"]) . '</td></tr>';
			}

			if (!empty($formVars["bring"])) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Bring With You:</td>';
				echo "<td>" . stripslashes($formVars["bring"]) . '</td></tr>';
			}

			if (!empty($formVars["includes"])) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Includes:</td>';
				echo "<td>" . stripslashes($formVars["includes"]) . '</td></tr>';
			}

			if ((!empty($formVars["canceldate"])) and ($formVars["canceldate"] != '00/00/0000')) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Deadline Date:</td>';
				echo "<td>" . $formVars["canceldate"] . '</td></tr>';
			}

			if ((!empty($formVars["rescheduledate"]))) {
				echo '<tr><td  class="right"  bgcolor="#eeeeee">Reschedule Notes:</td>';
				echo "<td>" . $formVars["rescheduledate"] . '</td></tr>';
			}
		}

		?>
	</table>
</div>

<?php

require('footer.php');
?>