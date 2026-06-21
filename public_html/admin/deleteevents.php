<?
//$debug = true;
$debug = false;

require('always.include.php');

////session_start();
//require('include.php');

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}


//require("head.php");
//require("datacon.php"); 
?>
<?

$event_id = isset($_GET['event_id']) ? clean($_GET['event_id']) : "";
$event_id = trim($event_id);

// redirect to the elist.php page with bad event_id
if (empty($event_id)) {
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}

mysqlconnect($connection);

// first delete members records from the reserve table
// no orphans allowed!
$query = "delete from reserve where event_id = '$event_id'";
if (!($resultdelete = mysqli_query($connection, $query))) {
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );
}

$query = "delete from events where event_id = '$event_id'";
if (!($resultdelete = mysqli_query($connection, $query))) {
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );
}

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Event Delete ' . $ClubCompanyName;
$admin = true;
require('top.php');

?>

<h3>Record with ID <?php echo $event_id; ?> has been Deleted </h3>

<p>Click <a href="elist.php">here</a> to go return to previous screen.</p>

<p>Click <a href="index.php">here</a> to go back to Main Menu</p>



<?
require('footer.php');

?>