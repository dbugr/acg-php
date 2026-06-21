<?php
// public event listing

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$ClubCode       = GetParameter('ClubCode');

$AdminLevel = 0;

// connect to the database
mysqlconnect($connection);

// get start date from user, reject incorrect dates
//if (!isset($_POST['list_start_date'])) {
	$today = date('m/d/Y', time());
	$list_start_date = $today;
//} else {
	// Validate begin date
//	$list_start_date = htmlentities($_POST['list_start_date'], ENT_QUOTES, 'UTF-8');
	// the begin date cannot be a null string
//	if (empty($list_start_date))
//		$list_start_date = date('m/d/Y', time());
//	else if (!preg_match("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#", $list_start_date, $parts))
//		$list_start_date = date('m/d/Y', time());
//	else if (!checkdate($parts[1], $parts[2], $parts[3]))
//		$list_start_date = date('m/d/Y', time());
//}
$list_start_date = quotesqldata($list_start_date);

// obtain event data
$query = "SELECT events.event_id, events.e_name, events.e_begindate, "
	. " events.e_location_name, events.e_status, events.e_display, "
	. " events.e_begintime "
	. " FROM events "
	. " WHERE  "
	. " events.e_begindate >= "
	.  " '" . MySqlDate($list_start_date) . "' "
	. " ORDER BY events.e_begindate, events.e_begintime ";

if (!($result = @mysqli_query($connection, $query)))
	trigger_error("MySQL error: " 
		. mysqli_errno($connection) . " : " 
		. mysqli_error($connection), E_USER_ERROR);

// put event data into an array
$e_recs = array();
while ($e_row = mysqli_fetch_array($result)) {
	$e_id = $e_row['event_id'];
	// don't include MembersOnly events not in this club
	if (!strstr($e_id, $ClubCode) && ($e_row['e_display'] == 'MembersOnly'))
		continue;
	$e_id = $e_row['event_id'];
	$e_recs[$e_id] = $e_row;
}
$admin_level = 0;

// create one string for each event
// include html anchors
// strings will be output when page is displayed
$week = 0;
foreach ($e_recs as $e_row) {
	$event_id = $e_row["event_id"];
	$weekstring = "";
	$eventweek = date('W', strtotime($e_row["e_begindate"]));
	if ($week != $eventweek) {
		// start a new week with a horizontal rule
		$week = $eventweek;
		$weekstring = "<tr><td colspan=8><hr align='left' color='#000000' SIZE='1' noshade></td></tr>\n";
		//$weekstring = "<tr><td colspan=8><hr align='left' style='border-style: dashed; border-width: 1' noshade color='#000000' size='1'></td></tr>\n";

	}

	// preset string with event day of week: Mon, Tues, etc
	$day = date('l', strtotime($e_row["e_begindate"]));

	// do not display events with status == Hide
	if (($e_row['e_status'] != 'Hide')) {
		$col_start_color = "";
		$col_end_color = "";

		// display proposed events in italics
		if ($e_row['e_status'] == 'Proposed') {
			$col_start_color .= "<i>";
			$col_end_color = "</i>" . $col_end_color;
		}

		// display canceled events in Strike-Through
		if ($e_row['e_status'] == 'Canceled') {
			$col_start_color .= "<strike>";
			$col_end_color = "</strike>" . $col_end_color;
		}

		// display PUBLIC events in BOLD/GREEN
		if ($e_row['e_display'] == 'Public') {
			$col_start_color .= "<b><font color='green'>";
			$col_end_color = "</font></b>" . $col_end_color;
		}

		$str = $weekstring . "<tr>";

		// event id
		if ($admin_level) {
			$str .= "<td nowrap>" . $e_row["event_id"] . "</td>";
		}

		// event begin date
		$str .= "<td align=left>";
		$str .= $col_start_color;
		$str .= date('D M j', strtotime($e_row["e_begindate"]));
		$str .= $col_end_color;
		$str .= "&nbsp; </td>";

		// event name link
		$str .= "<td align=left>";
		$str .= $col_start_color;
		$str .= '<a href="eview-pub.php?event_id='
			. $e_row["event_id"]
			. '">'
			. stripslashes($e_row["e_name"])
			. "</a>";
		$str .= $col_end_color;
		$str .= "</td>";

		// club code: gnv, cfac, etc
		//$club_code = $e_row['event_id'];
		//$club_code = substr($club_code,0,strpos($club_code,'_'));
		//$str .= "<td>";
		//$str .= $club_code;
		//$str .= "</td>";

		$str .= "</tr>";

		$strings[$event_id] = $str;
	} // end foreach
} // end while
// start displaying the page!
$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Event Calendar ' . $ClubCompanyName;
require('top.php');

?>

<table width="100%">
	<tr>
		<td align="left">
			<h3>Public Events List</h3>
			<p><b>
					<font color="green">Green</font>
				</b>&nbsp;public events. You and
				your friends are invited to come out and meet club members. <br>
				<hr align='left' color='#000000' SIZE='1' noshade>
				<table>
					<tr>
						<?php if ($AdminLevel) echo "<td>E-Id</td>"; ?>
						<td align="left" width="20%"><b>Date</b></td>
						<td align="left" width="80%"><b>Event Name</b></td>
						<td align="left"></td>
					</tr>

					<?php
					if (!empty($strings))
						foreach ($strings as $str) {
							echo $str . "\n";
						}
					?>

				</table>
				<hr align='left' color='#000000' SIZE='1' noshade>
				<p>
					<h3>Key</h3>
					<ul>
						<li><i>Italics: Proposed event, subject to change, cancellation</i></li>
						<li><strike>Strike-Through: event has been CANCELED</strike></li>
						<li><b>
								<font color="green">Bold: event open to general public, you are invited to attend!</font>
							</b></li>
					</ul>

		</td>
	</tr>
	<tr>
		<td width="100%" nowrap valign="top" colspan="2">&nbsp;</td>
	</tr>
</table>
</center>
</div>

<?php
require('footer.php');
?>