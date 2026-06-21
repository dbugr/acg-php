<?
// delete an expired members reservation record

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');
//require('expired_membership.php');

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
//LogMsg('deletereserve.php:$loginUsername: '.$loginUsername);
//LogMsg('deletereserve.php:$AdminLevel: '.$AdminLevel);
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  //LogMsg('redirecting to: '.$loc);
  header($loc);
  exit;
}

mysqlconnect($connection);

$FileName = __FILE__;
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Delete Expired Reservations ' . $ClubCompanyName;
$admin = true;
require('top.php');
?>

<?

$reserve_id = isset($_POST['reserve_id']) ? clean($_POST['reserve_id']) : "";
$reserve_id = trim($reserve_id);
//LogMsg('$reserve_id: '.$reserve_id);

// redirect to the elist.php page with bad reserve_id
if (empty($reserve_id)) {
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}

$querydelete = "delete from reserve where reserve_id = " . $reserve_id;
if (!$resultdelete = mysqli_query($connection, $querydelete)) {
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );
}
?>

<h3>Record with ID <?php echo $reserve_id; ?> has been Deleted </h3>

Click <a href="index.php">here</a> to go back to Main Menu

<?
require('footer.php');
?>