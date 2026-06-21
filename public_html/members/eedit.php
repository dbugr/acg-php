<?php
// Event edit
// auth_level must be leader or admin
//

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


//==================================================
// if global debug variable is true, write parameters to log file
function DebugLogMsg($description, $var)
{
	global $debug;

	if (isset($debug) && $debug) {
		LogMsg($description . " " . print_r($var, true));
	}
}


//=============================================================
// functions start here


//==================================================
// Show an error in a red font
function fieldError($fieldName, $errors)
{
	if (isset($errors[$fieldName]))
		echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
}

//==================================================
function RetrieveSessionFormVars()
{

	$formVars = array();
	if (isset($_SESSION['formVars'])) {
		foreach ($_SESSION['formVars'] as $varname => $value) {
			$formVars[$varname] = trim($value);
		}
	}
	return ($formVars);
}


//==================================================
function RetrieveSessionErrors()
{

	$aErrors = array();
	if (isset($_SESSION['errors'])) {
		foreach ($_SESSION['errors'] as $varname => $value) {
			$aErrors[$varname] = trim($value);
		}
	}
	return ($aErrors);
}


//==================================================
function RetrievePostVars()
{

	$aPost = array();
	if (isset($_POST)) {
		foreach ($_POST as $varname => $value) {
			$aPost[$varname] = trim($value);
			//$aPost[$varname] = $value;
		}
	}
	return ($aPost);
}


//==================================================
function RetrieveGetVars()
{

	$aGet = array();
	if (isset($_GET)) {
		foreach ($_GET as $varname => $value) {
			$aGet[$varname] = trim($value);
		}
	}
	return ($aGet);
}


//==================================================
function GetWebPageTitleText($mode)
{

	switch ($mode) {
		case 'copy':
			$TitleText = 'Event Copy';
			break;
		case 'edit':
			$TitleText = 'Event Edit';
			break;
		case 'new':
			$TitleText = 'New Event';
			break;
	}

	return ($TitleText);
}


//==================================================
function UserHasEditPermission($leader_id, $coleader_id, $mode, $AdminLevel, $LeaderLevel, $cust_id)
{

	// does the current user own the event
	$user_owns_event = ($leader_id == $cust_id) ||
		($coleader_id == $cust_id);
	//DebugLogMsg('$user_owns_event: ',$user_owns_event);
	//DebugLogMsg('$AdminLevel: ',$AdminLevel);
	//DebugLogMsg('$LeaderLevel: ',$LeaderLevel);

	// check user permission levels
	$redirect = true;
	if ($AdminLevel) {
		$redirect = false;
	} else if ($user_owns_event) {
		$redirect = false;
	} else if (($mode == 'copy') && ($LeaderLevel)) {
		$redirect = false;
	}
	//DebugLogMsg('$redirect1: ',$redirect);
	//DebugLogMsg('$mode1: ',$mode);

	// if everything else is ok, but the mode is wrong, redirect
	if ((((!$redirect) && ($mode != 'copy'))
		&& ($mode != 'edit')) && ($mode != 'new')) {
		$redirect = true;
	}
	//DebugLogMsg('$redirect2: ',$redirect);

	return $AuthToEdit = !$redirect;
}


//==================================================
function GetFormDataFromDatabase($event_id, $leader_id, $coleader_id)
{
	global $connection;

	$formVars['leader_id'] = $leader_id;
	$formVars['coleader_id'] = $coleader_id;
	// existing event, load from table and edit
	$query = "SELECT * FROM events
      				WHERE (event_id='" . $event_id . "');";
	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	// redirect to the events list page
	if ($numrows = (@mysqli_num_rows($result) <= 0)) {
		$loc = "Location: /members/elist.php";
		header($loc);
		exit;
	}
	$row = mysqli_fetch_array($result);

	// Load all the form variables with customer data
	$formVars["event_id"] = $row["event_id"];
	$formVars["name"] = $row["e_name"];
	$formVars["begindate"] = HumanDates($row["e_begindate"]);
	$formVars["begintime"] = $row["e_begintime"];
	$formVars["details"] = $row["e_details"];
	$formVars["details_private"] = $row["e_details_private"];
	$formVars["location_name"] = $row["e_location_name"];
	$formVars["location_meet_at"] = $row["e_location_meet_at"];
	$formVars["driving_directions"] = $row["e_driving_directions"];
	$formVars["min_attendees"] = $row["e_min_attendees"];
	$formVars["max_attendees"] = $row["e_max_attendees"];
	$formVars["contingency_plan"] = $row["e_contingency_plan"];
	$formVars["url1"] = $row["e_url1"];
	$formVars["url2"] = $row["e_url2"];
	$formVars["url3"] = $row["e_url3"];
	$formVars["url4"] = $row["e_url4"];
	$formVars["status"] = $row["e_status"];
	$formVars["display"] = $row["e_display"];
	$formVars["pmt_descr"] = $row["e_pmt_descr"];
	$formVars["pay4event"] = $row["e_pay4event"];
	$formVars["date_added"] = HumanDates($row["e_date_added"]);
	$formVars["date_changed"] = HumanDates($row["e_date_changed"]);
	$formVars["bring"] = $row["e_bring"];
	$formVars["includes"] = $row["e_includes"];
	$formVars['leader_notes'] = $row['e_leader_notes'];
	$formVars["category"] = $row["e_category"];
	$formVars['e_fullprice'] = $row['e_fullprice'];
	$formVars['e_fullprice_date'] = $row['e_fullprice_date'];
	$formVars['e_deposit'] = $row['e_deposit'];
	$formVars['e_deposit_date'] = $row['e_deposit_date'];
	$formVars['e_days_res_chgs'] = $row['e_days_res_chgs'];

	if (!isset($formVars['display']) || ($formVars['display'] == ''))
		$formVars['display'] = "AllClubs";

	return $formVars;
}


//==================================================
//obtain this club's cust_id's for use in coleader pull down
function GetCustIds()
{
	$ClubCode = GetParameter('ClubCode');
	mysqlconnect($connection);
	$query = "SELECT cust_id,m_firstname,m_lastname FROM members
					WHERE (cust_id like '" . $ClubCode . "%')
					  and u_date_expiration > now() order by m_firstname,m_lastname;";

	if (!($result = @mysqli_query($connection, $query)))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	$aCustIdStrings = array();
	$aCustIdStrings[] = "";
	while ($row = mysqli_fetch_array($result)) {
		$mCustId = $row['cust_id'];
		$aCustIdStrings[$mCustId] =
			$row['m_firstname']
			. ' '
			. $row['m_lastname'];
	}
	return ($aCustIdStrings);
}


//=============================================================
// code starts here

// Is the user logged in? if not, redirect to login page
$LoggedIn = SessionIsRegistered("loginUsername");
if (!$LoggedIn) {
	// Register a message to show the user
	$message = "Error: you are not logged in!";
	SessionRegister("message", $message);

	// Register where they came from
	$referer = $_SERVER['PHP_SELF'];
	SessionRegister("referer", $referer);

	// redirect to the login page
	$loc = "Location: /login.php";
	header($loc);
	exit;
}

// obtain member information from members table
$loginUsername = LoginUsername();
$cust_id = getCustomerID($loginUsername);

// get user permission level
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
//$AuthLevel = AuthLevel($loginUsername);

//DebugLogMsg('$AdminLevel:',$AdminLevel);
//DebugLogMsg('$LeaderLevel:',$LeaderLevel);

$formVars = RetrieveSessionFormVars();
$errors = RetrieveSessionErrors();
$aPost = RetrievePostVars();
$aGet = RetrieveGetVars();

//DebugLogMsg('$formVars:',$formVars);
//DebugLogMsg('$errors:',$errors);
//DebugLogMsg('$aPost:',$aPost);
//DebugLogMsg('$aGet:',$aGet);

// get the event_id & mode
//$mode = "new";
if (isset($aGet['invalid']) && $aGet['invalid']) {
	// eedit-post detected invalid form data and redirected back to eedit.php
	// obtain data from session variables
	if (isset($formVars['event_id'])) {
		$event_id = $formVars['event_id'];
	} else {
		EmailDeveloper(
			"ERROR EEDIT.PHP event_id missing from $formVars: ",
			'$formVars[name]: ' . $formVars['name'] . "\n"
				. '$leader_id  ' . $loginUsername . "\n"
				. '$aGet[invalid]: ' . $aGet['invalid']
		);
		trigger_error("event_id missing from $formVars!: " . print_r($aPost, true), E_NOTICE);
	}
	if (isset($formVars['mode'])) {
		$mode = trim($formVars['mode']);
	} else {
		EmailDeveloper(
			'ERROR EEDIT.PHP mode missing from $formVars: ',
			'EVENT NAME: ' . $formVars['name'] . "\n"
				. '$formVars[event_id]  ' . $formVars['event_id'] . "\n"
				. '$loginUsername  ' . $loginUsername . "\n"
				. '$aGet[invalid]: ' . $aGet['invalid']
		);
		trigger_error("mode missing from $formVars!: " . print_r($aPost, true), E_NOTICE);
	}
	$RedisplayWebPage = true;
	//DebugLogMsg('$RedisplayWebPage:',$RedisplayWebPage);
} else {
	// obtain data from $_POST and database;
	// clear formvars and errors session variables
	$RedisplayWebPage = false;
	if (isset($aPost['event_id'])) {
		$event_id = $aPost['event_id'];
	} else {
		EmailDeveloper(
			'ERROR EEDIT.PHP event_id missing from $aPost!: ',
			'$aPost[name]: ' . $aPost['name'] . "\n"
				. '$loginUsername: ' . $loginUsername
		);
		trigger_error('event_id missing from $aPost!: ' . print_r($aPost, true), E_NOTICE);
	}
	if (isset($aPost['mode'])) {
		$mode = trim($aPost['mode']);
	} else {
		EmailDeveloper(
			"ERROR EEDIT.PHP mode missing from $aPost!: ",
			'$aPost[name]: ' . $aPost['name'] . "\n"
				. '$aPost[event_id]: ' . $aPost['event_id'] . "\n"
				. '$loginUsername: ' . $loginUsername
		);
		trigger_error("mode missing from $aPost!: " . print_r($aPost, true), E_NOTICE);
	}
	SessionUnRegister('formVars');
	$formVars = array();
	SessionUnRegister('errors');
	$errors = array();
}

/*
EmailDeveloper("EEDIT.PHP editing $event_id: ".$event_id,
	'$event_id: '.$event_id."\n"
	.'$mode: '.$mode."\n"
	.'$loginUsername: '.$loginUsername
	);
$sInvalid = (array_key_exists('invalid',$aGet)) ? $aGet['invalid'] : '';
LogMsg('begin event edit form: '
	.'$event_id: '.print_r($event_id,true)."\n"
	.'$mode: '.print_r($mode,true)."\n"
	.'$RedisplayWebPage: '.print_r($RedisplayWebPage,true)."\n"
	.'$aPost: '.print_r($aPost,true)."\n"
	.'$loginUsername: '.print_r($loginUsername,true)."\n"
	.'$aGet[invalid]: '.$sInvalid
	);
*/
//DebugLogMsg('$event_id:',$event_id);
//DebugLogMsg('$mode:',$mode);

// connect to the database
mysqlconnect($connection);

// get / set variables
if (($mode == "new") && (!$RedisplayWebPage)) {
	// new event
	$leader_id = $cust_id;
	$coleader_id = $cust_id;
	$event_id = 0;
	$formVars["event_id"] = $event_id;
	$formVars['leader_id'] = $cust_id;
	$formVars["coleader_id"] = $cust_id;
	$formVars["display"] = 'AllClubs';
	$formVars["type"] = 'Minor';
	$formVars["newsletter_publish"] = 'True';
} else if (($mode == "new") && ($RedisplayWebPage)) {
	// new event, redisplay due to errors
	// $formVars already loaded with $_SESSION data, nothing to do here!
	$leader_id = $cust_id;
	$coleader_id = $cust_id;
} else if ((($mode == "copy") || ($mode == "edit")) && (!$RedisplayWebPage)) {
	// edit or copy event
	// get form data from database
	if ($mode == "copy") {
		$leader_id = $cust_id;
		$coleader_id = $cust_id;
	} else {
		$leader_id = getLeaderID($event_id, $coleader = false);
		$coleader_id = getLeaderID($event_id, $coleader = true);
	}
	//LogMsg('$leader_id: '.$leader_id);
	//LogMsg('$coleader_id: '.$coleader_id);
	$formVars = GetFormDataFromDatabase($event_id, $leader_id, $coleader_id);
} else if ((($mode == "copy") || ($mode == "edit")) && ($RedisplayWebPage)) {
	// edit or copy event, redisplay due to errors
	// get form data from session variables
	// $formVars already loaded with $_SESSION data, nothing to do here!
	if ($mode == 'copy') {
		$leader_id = $cust_id;
		$coleader_id = $cust_id;
	} else {
		$leader_id = $formVars['leader_id'];
		if (array_key_exists('coleader_id', $formVars)) {
			$coleader_id = $formVars['coleader_id'];
		} else {
			$coleader_id = $cust_id;
			$formVars['coleader_id'] = $cust_id;
		}
	}
} else {
	// default: 
	// redirect to the events list page
	$loc = "Location: /members/elist.php";
	LogMsg(
		"ERROR unknown edit mode: "
			. $mode
			. '   REDIRECTING to header($loc): '
			. $loc
	);
	header($loc);
	exit;
}

$AuthToEdit = UserHasEditPermission($leader_id, $coleader_id, $mode, $AdminLevel, $LeaderLevel, $cust_id);
if (!$AuthToEdit) {
	// redirect to the events list page
	$loc = "Location: /members/elist.php";
	LogMsg('ERROR user not authorized to edit. REDIRECTING to header($loc): '
		. $loc);
	header($loc);
	exit;
}

/*
LogMsg('displaying event edit form: '
	.'   $event_id: '.print_r($event_id,true)
	.'   $mode: '.print_r($mode,true)
	.'   $leader_id: '.print_r($leader_id,true)
	.'   $coleader_id: '.print_r($coleader_id,true)
	);
*/

switch ($mode) {
	case 'new':
		$sMode = "Edit New Event Details";
		break;
	case 'edit':
		$sMode = "Edit Existing Event Details";
		break;
	case 'copy':
		$sMode = "Edit Copy of Existing Event Details";
		break;
	default:
		$sMode = "Error: Unknown Mode: " . $mode;
		break;
}

$TitleText = GetWebPageTitleText($mode);

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	  = GetParameter('ClubCompanyName');
$WebPageTitle = $TitleText . ' - ' . $ClubCompanyName;
require('top.php');


?>
<div id="centercontent">
	<form method="POST" action="/members/eedit-post.php">
		<input type=hidden name=event_id value="<?php echo $event_id; ?>">
		<input type=hidden name=mode value="<?php echo $mode; ?>">
		<input type=hidden name=leader_id value="<?php echo $leader_id; ?>">
		<input type=hidden name=coleader_id value="<?php echo $coleader_id; ?>">

		<?php
		if (count($errors) > 0) {
			echo "<h2><font color=\"red\">Errors Occurred. Please scroll down.</font></h2>";
			//print_r($errors);
			//exit;
		}
		?>


		<table border="0">

			<tr>
				<td colspan="2">
					<h3><?php echo $sMode; ?></h3>
				</td>
			</tr>

			<tr>
				<td colspan="2">(<i>
						<font color="red">Red</font> headings are required.)
					</i></td>
			</tr>
			<tr>
				<td valign="top">
					<font color="red">Event Name:</font>
				</td>
				<td><? echo fieldError("name", $errors); ?>
					<input type="text" name="name" value="<?php echo isset($formVars['name']) ? stripslashes($formVars['name']) : NULL; ?>" size=70 maxlength=100><br>
					<i>
						<font size="-1">(Limit to 5 or 6 words)</font>
					</i>
				</td>
			</tr>

			<tr>
				<td>
					<font color="red">Event Category:</font>
				</td>
				<td><? echo fieldError("category", $errors); ?>
					<select name="category">
						<?php
						$category = "";
						if (isset($formVars['category']))
							$category = $formVars['category'];
						?>

						<option <? if ($category == "Bicycle") echo "selected"; ?>>Bicycle</option>
						<option <? if ($category == "Bowling") echo "selected"; ?>>Bowling</option>
						<option <? if ($category == "Camping") echo "selected"; ?>>Camping</option>
						<option <? if ($category == "Classes") echo "selected"; ?>>Classes</option>
						<option <? if ($category == "Clubbing") echo "selected"; ?>>Clubbing</option>
						<option <? if ($category == "Cruise") echo "selected"; ?>>Cruise</option>
						<option <? if ($category == "Dancing") echo "selected"; ?>>Dancing</option>
						<option <? if ($category == "Dining") echo "selected"; ?>>Dining</option>
						<option <? if ($category == "Events") echo "selected"; ?>>Events</option>
						<option <? if ($category == "Flying") echo "selected"; ?>>Flying</option>
						<option <? if ($category == "Geocaching") echo "selected"; ?>>Geocaching</option>
						<option <? if ($category == "Hiking") echo "selected"; ?>>Hiking</option>
						<option <? if ($category == "Meet-n-Greet") echo "selected"; ?>>Meet-n-Greet</option>
						<option <? if ($category == "Movie") echo "selected"; ?>>Movie</option>
						<option <? if ($category == "Outdoors") echo "selected"; ?>>Outdoors</option>
						<option <? if ($category == "Party") echo "selected"; ?>>Party</option>
						<option <? if ($category == "Socials") echo "selected"; ?>>Socials</option>
						<option <? if ($category == "Sports") echo "selected"; ?>>Sports</option>
						<option <? if ($category == "Theater") echo "selected"; ?>>Theater</option>
						<option <? if ($category == "Tour") echo "selected"; ?>>Tour</option>
						<option <? if ($category == "Water") echo "selected"; ?>>Water</option>
						<option <? if ($category == "Weekends") echo "selected"; ?>>Weekends</option>
						<option <? if ($category == "Other") echo "selected"; ?>>Other</option>
					</select></td>
			</tr>


			<tr>
				<td valign="top">
					<font color="red">Begin Date:</font>
				</td>
				<td><? echo fieldError("begindate", $errors); ?>
					<input type="text" name="begindate" value="<?php echo isset($formVars['begindate']) ? $formVars['begindate'] : NULL; ?>" size="10" maxlength="10" onfocus="showCalendar('',this,this,'','holder1',0,30,1)">&nbsp;
					<br><i>
						<font size="-1">(Enter the date as mm/dd/yyyy, or left mouse click on calendar to select date)</font>
					</i>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<font color="red">Begin Time:</font>
				</td>
				<td><? echo fieldError("begintime", $errors);
						$am = true;
						$hours = 10;
						$min = '00';
						if (isset($formVars['begintime'])) {
							$time = strtotime($formVars['begintime']);
							$hours = date('h', $time);
							$min = date('i', $time);
							$am = (date('A', $time) == 'AM');
						}
						echo '<input type="text" name="timehours" value="' . $hours . '" size="2" maxlength="2">&nbsp;:&nbsp';
						echo '<input type="text" name="timeminutes" value="' . $min . '" size="2" maxlength="2">&nbsp;';
						?>
					<select name="ampm">
						<option <?php if ($am) echo 'selected'; ?>>AM</option>
						<option <?php if (!$am) echo 'selected'; ?>>PM</option>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<font color="red">Event Status:</font>
				</td>
				<td><?php echo fieldError("status", $errors); ?>
					<select name="status">
						<?php
						$status = "";
						if (isset($formVars['status']))
							$status = $formVars['status'];

						echo "<option" . (($status == "Approved") ? " selected" : "") . ">Approved</option>";
						//echo "<option" . (($status=="SelfGuided") ? " selected" : "") . ">SelfGuided</option>";
						echo "<option" . (($status == "Proposed") ? " selected" : "") . ">Proposed</option>";
						echo "<option" . (($status == "Hide") ? " selected" : "") . ">Hide</option>";
						echo "<option" . ((($status == "Cancelled") || ($status == "Canceled")) ? " selected" : "") . ">Cancelled</option>";
						?>
					</select>
					<ul>
						<li>Approved = Normal event view</li>
						<!-- <li>SelfGuided = No Event Leader, self guided event</li> -->
						<li>Proposed = <i>Event details are not firm</i></li>
						<li>Hide = <i><strike>only event leaders can view</strike></i></li>
						<li>Cancelled = <strike>display in Strike-Through</strike></li>
					</ul>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<font color="red">Location Name:</font>
				</td>
				<td><? echo fieldError("location_name", $errors); ?>
					<input type="text" name="location_name" value="<?php echo isset($formVars['location_name']) ? stripslashes($formVars['location_name']) : NULL; ?>" size=70 maxlength="80"></td>
			</tr>

			<tr>
				<td valign="top">Location Link:</td>
				<td><? echo fieldError("url4", $errors); ?>
					<input type="text" name="url4" value="<?php echo isset($formVars['url4']) ? $formVars['url4'] : NULL; ?>" size=70 maxlength="254">
					<br><i>
						<font size="-1">(Place optional web link here, where members can get more information. Visible to members only.)
						</font>
					</i></td>
			</tr>

			<tr>
				<td colspan=2><br><i>
						<font size="-1">(Write up your event description letting your enthusiasm show!
							To format, use &lt;P&gt; for new paragraphs and &lt;BR&gt; for line breaks.
							Keep the public description brief, avoiding specific location details.)</font>
					</i></td>
			</tr>
			<tr>
				<td valign="top">
					<font color="red">Brief event description for public viewing:</font>
				</td>
				<td><? echo fieldError("details", $errors); ?>
					<textarea name=details rows=20 cols="44" maxlength="2048"><?php echo isset($formVars['details']) ? stripslashes($formVars['details']) : NULL; ?></textarea></td>
			</tr>

			<tr>
				<td colspan=2><br><i>
						<font size="-1">(If
							you have years of experience or training, or if you are a novice, let us know here.
							Mention any special notes - pets and/or children allowed or not? If attendees must show
							up on time, emphasize it. Please indicate 'dress' - for example, semi-formal, casual, etc.
							Anything typed here is for members only.)</font>
					</i></td>
			</tr>
			<tr>
				<td valign="top">
					<font color="red">Enter additonal details for members only:</font>
				</td>
				<td><? echo fieldError("details_private", $errors); ?>
					<textarea name=details_private rows=20 cols="44" maxlength="8000"><?php echo isset($formVars['details_private']) ? stripslashes($formVars['details_private']) : NULL; ?></textarea>

				</td>
			</tr>


			<tr>
				<td valign="top">
					<font color="red">Attendees:</font>
				</td>
				<td><? echo fieldError("min_attendees", $errors); ?>
					<? echo fieldError("max_attendees", $errors); ?>
					Min: <input type="text" name="min_attendees" value="<?php echo isset($formVars['min_attendees']) ? $formVars['min_attendees'] : NULL; ?>" size=3 maxlength=3> &nbsp;

					Max: <input type="text" name="max_attendees" value="<?php echo isset($formVars['max_attendees']) ? $formVars['max_attendees'] : NULL; ?>" size=3 maxlength=3>
					<br><i>
						<font size="-1">(Minimum and maximum number of reservations)</font>
					</i></td>
			</tr>


			<tr>
				<td colspan=2><i>
						<font size="-1">(Any other links of interest regarding this event?)</font>
					</i></td>
			</tr>
			<tr>
				<td>Event Link1: </td>
				<td><? echo fieldError("url1", $errors); ?>
					<input type="text" name="url1" value="<?php echo isset($formVars['url1']) ? $formVars['url1'] : NULL; ?>" size=70 maxlength="254">
				</td>
			</tr>

			<tr>
				<td>Event Link2:</td>
				<td><? echo fieldError("url2", $errors); ?>
					<input type="text" name="url2" value="<?php echo isset($formVars['url2']) ? $formVars['url2'] : NULL; ?>" size=70 maxlength="254"></td>
			</tr>



			<tr>
				<td colspan=2>
					<hr align="left" color="#000000" SIZE="1" noshade>
				</td>
			</tr>


			<tr>
				<td valign="top">Meet At: </td>
				<td><? echo fieldError("location_meet_at", $errors); ?>
					<input type="text" name="location_meet_at" value="<?php echo isset($formVars['location_meet_at']) ? stripslashes($formVars['location_meet_at']) : NULL; ?>" size=70 maxlength="120">
					<br><i>
						<font size="-1">(Optional field indicating where to meet, for example - main gate,
							at the front door, across the street, etc.)</font>
					</i>
				</td>
			</tr>

			<tr>
				<td valign="top">Driving Directions Link:</td>
				<td><? echo fieldError("url3", $errors); ?>
					<input type="text" name="url3" value="<?php echo isset($formVars['url3']) ? $formVars['url3'] : NULL; ?>" size=70 maxlength="254">
					<br><i>
						<font size="-1">(Use mapquest.com or other site, and place web link here. Visible to members only.)
						</font>
					</i></td>
			</tr>

			<tr>
				<td valign="top">Driving Directions or special notes:</td>
				<td><? echo fieldError("driving_directions", $errors); ?>
					<textarea name=driving_directions rows=10 cols=44 maxlength="254"><?php echo isset($formVars['driving_directions']) ? stripslashes($formVars['driving_directions']) : NULL; ?></textarea>
				</td>
			</tr>

			<tr>
				<td colspan=2>
					<hr align="left" color="#000000" SIZE="1" noshade>
				</td>
			</tr>

			<tr>
				<td colspan=2><i>
						<font size="-1">('COST COMMENTS:
							Example: "Group rate $25; Individuals $30".)
						</font>
					</i></td>
			</tr>

			<tr>
				<td colspan=2></td>
			</tr>
			<tr>
				<td>Cost Comments: </td>
				<td><? echo fieldError("pmt_descr", $errors); ?>
					<input type="text" name="pmt_descr" value="<?php echo isset($formVars['pmt_descr']) ? stripslashes($formVars['pmt_descr']) : NULL; ?>" size=70 maxlength="60"></td>
			</tr>



			<?php
			// cost, deposit and pay4event
			// echo '<tr><td valign=top>Cost:</td><td>' . fieldError("e_fullprice", $errors) .
			//  		 fieldError("e_fullprice_date", $errors) .
			//		 fieldError("e_deposit", $errors) .
			//		 fieldError("e_deposit_date", $errors) .
			//		 fieldError("pay4event", $errors) .
			//		 '<table bgcolor="#eeeeee">';
			$e_deposit_date = "";
			$e_deposit = 0;
			$e_fullprice_date = "";
			$pay4event = "";
			$e_fullpice = 0;
			$amount = "";
			if (isset($formVars['e_fullprice']))
				$amount = sprintf("%.02f", $formVars['e_fullprice']);
			$date = "";
			if (
				isset($formVars['e_fullprice_date']) and
				!empty($formVars['e_fullprice_date']) and
				($formVars['e_fullprice_date'] != 0)
			)
				$date = HumanDates($formVars['e_fullprice_date']);
			// echo '<tr><td>Full Price</td><td>' .
			//		 '$ <input type="text" name="e_fullprice" value="' . $amount  . '" size=7 maxlength=7>'.
			//		 ' due on ' .
			//		 '<input type="text" name="e_fullprice_date" value="' . $date . '" size=10 maxlength=10>' .
			//		 '&nbsp;<a href="javascript:showCal(\'eFullpriceDate\', 1800, 600)">' .
			//  '<img src="/images/date.gif" width="19" height="17" border="0" alt="select date"></a>' .
			//  '(mm/dd/yyyy)</td></tr>';
			// echo '<tr><td></td><td><font size="-2">including deposit</font></td></tr>';
			// $amount = "";
			// if (isset($formVars['e_deposit']))
			//	$amount = sprintf( "%.02f", $formVars['e_deposit']);
			// $date = "";
			// if (isset($formVars['e_deposit_date']) AND
			//   !empty($formVars['e_deposit_date']) AND
			//		($formVars['e_deposit_date'] != 0 ))
			//	 $date = HumanDates($formVars['e_deposit_date']);
			// echo '<tr><td>Deposit</td><td>' .
			//		 '$ <input type="text" name="e_deposit" value="' . $amount  . '" size=7 maxlength=7>'.
			//		 ' due on ' .
			//		 '<input type="text" name="e_deposit_date" value="' . $date . '" size=10 maxlength=10>' .
			//		 '&nbsp;<a href="javascript:showCal(\'eDepositDate\', 1900, 600)">' .
			//  '<img src="/images/date.gif" width="19" height="17" border="0" alt="select date"></a>' .
			//   '(mm/dd/yyyy)</td></tr>';
			// echo '</table></td></tr>';
			?>

			<!-- <tr><td colspan=2><i><font size="-1">('Pay4Event option:' enable if
	   you need to accept credit card payments for the event or
	   event deposit through the site.)
	  	</font></i></td></tr>

     <tr><td>Pay4Event option:</td>
    <td><? // echo fieldError("pay4event", $errors); 
				?>
      <select name="pay4event">
      <option <? if (isset($formVars['pay4event']) && ($formVars["pay4event"] == "Disable")) echo "selected"; ?>>
         Disable
      <option <? if (isset($formVars['pay4event']) && ($formVars["pay4event"] == "Enable")) echo "selected"; ?>>
         Enable
      </select></td>
   </tr>

   <tr><td colspan=2><hr align="left" color="#000000" SIZE="1" noshade>
</td></tr> -->
			<tr>
				<td>Bring with you: </td>
				<td><? echo fieldError("bring", $errors); ?>
					<input type="text" name="bring" value="<?php echo isset($formVars['bring']) ? stripslashes($formVars['bring']) : NULL; ?>" size=70 maxlength="254"></td>
			</tr>

			<tr>
				<td valign="top">Event Includes: </td>
				<td><? echo fieldError("includes", $errors); ?>
					<input type="text" name="includes" value="<?php echo isset($formVars['includes']) ? stripslashes($formVars['includes']) : NULL; ?>" size=70 maxlength="254">
					<br>
					<i>
						<font size="-1">(The club provides cups, plates, ice, and condiments for picnics. Those types of things are listed here.)
						</font>
					</i></td>
			</tr>


			<tr>
				<td valign="top">Contingency Plan:</td>
				<td><? echo fieldError("contingency_plan", $errors); ?>
					<input type="text" name="contingency_plan" value="<?php echo isset($formVars['contingency_plan']) ? stripslashes($formVars['contingency_plan']) : NULL; ?>" size=70 maxlength="254">
					<br>
					<i>
						<font size="-1">(For example, 'Plan B' for outside events if it rains.)</font>
					</i></td>
			</tr>

			<?php
			$s1 = "";				// public
			$s2 = "";				// all clubs
			$s3 = "";				// members only
			if (isset($formVars["display"])) {
				$display = $formVars["display"];
				if ($display == "Public")
					$s1 = "selected";
				else if ($display == "MembersOnly")
					$s3 = "selected";
				else
					$s3 = "selected";
			} else
				$s3 = "selected";

			echo "<tr><td>Details Display: </td><td>" . fieldError("display", $errors);
			echo "<select name='display'>";
			if (($AdminLevel) || isset($AllowLeadersToSetEventsPublic) || ($s1 == "selected"))
				echo "<option " . $s1 . ">Public</option>";
			//echo "<option " . $s2 . ">AllClubs</option>";
			echo "<option " . $s3 . ">MembersOnly</option>";
			echo "</select>";
			if (($AdminLevel) || isset($AllowLeadersToSetEventsPublic) || ($s1 == "selected"))
				echo "Display event details to Public or Members Only?";
			else
				echo "Are event details Members Only or Public?";
			echo "</td></tr>";
			?>




			<tr>
				<td valign="top">Leader Notes:</td>
				<td><? echo fieldError("leader_notes", $errors); ?>
					<textarea name=leader_notes rows=4 cols="44" maxlength="254"><?php echo isset($formVars['leader_notes']) ? stripslashes($formVars['leader_notes']) : NULL; ?></textarea>
					<br>
					<i>
						<font size="-1">(Notes visible only to leaders. You may want to put contact phone numbers here, or
							pricing information. Enter anything that may help another leader running this event in the future.)</font>
					</i>
				</td>
			</tr>

			<tr>
				<td valign="top">Notification:</td>
				<td><? echo fieldError("e_days_res_chgs", $errors); ?>
					<input type="text" name="e_days_res_chgs" size=4 value="<?php echo isset($formVars['e_days_res_chgs']) ? $formVars['e_days_res_chgs'] : NULL; ?>">
					<br>
					<i>
						<font size="-1">(Number of days before event to begin notification when members
							change reservations. Leave blank if you don't want any notifications.)</font>
					</i>
				</td>
			</tr>


			<tr>
				<td>CoLeader: </td>
				<td>
					<?php
					//$coleader_id = "";
					//if (isset($formVars['coleader_id']))
					//			$coleader_id = $formVars['coleader_id'];
					/*        echo '<select name="coleader_id">';
        foreach($aCustIdStrings as $varname => $value) {
           echo '<option ';
           if ($coleader_id == $varname) {
		     echo "selected ";
		   }
		   echo 'value="';
		   echo $varname;
		   echo '">';
		   echo $value;
		   echo '</option>';
		}
        echo '</select>';
        echo '<br>';
  	    echo '<i><font size="-1">(2nd member who can edit this event)</font></i>';
        echo '</td>';
*/
					?>

			</tr>
			<tr>
				<td>Date Added: </td>
				<td><? echo fieldError("date_added", $errors); ?>
					<? echo isset($formVars['date_added']) ? $formVars['date_added'] : NULL; ?>
				</td>
			</tr>

			<tr>
				<td>Date Changed: </td>
				<td><? echo fieldError("date_changed", $errors); ?>
					<? echo isset($formVars['date_changed']) ? $formVars['date_changed'] : NULL; ?>
				</td>
			</tr>

			<tr>
				<td colspan=2>
					<hr align="left" color="#000000" SIZE="1" noshade>
				</td>
			</tr>


			<tr>
				<td><input type="submit" value="Submit"></td>
				<td></td>
			</tr>


			<?php
			if (($LeaderLevel) or ($AdminLevel)) {
				$leader_id = 0;
				if (isset($formVars['leader_id']))
					$leader_id = $formVars['leader_id'];
				$event_id = 0;
				if (isset($formVars['event_id']))
					$event_id = $formVars['event_id'];

				echo '<tr><td></td><td>';
				echo 'event_id: ' . $event_id . '<br>';
				echo 'cust_id: ' . $cust_id . '<br>';
				echo 'leader_id: ' . $leader_id . '<br>';
				echo 'coleader_id: ' . $coleader_id . '<br>';
				echo 'mode: ' . $mode . '<br>';
				echo '</td></tr>';
			}
			if ($AdminLevel) {
				echo '<tr><td>leader_id:';
				echo '<input type="text" name="leader_id" value="' . $leader_id . '" size=14>';
				echo fieldError("leader_id", $errors) . '</td><td></td></tr>';
			} else {
				echo '<input type="hidden" name="leader_id" value="' . $leader_id . '" size=14>';
			}
			?>

		</table>
	</form>
</div>

<?php
require('footer.php'); ?>