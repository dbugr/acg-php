<?

require('always.include.php');
//$debug = true;
//$debug = false;

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

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Delete Members ' . $ClubCompanyName;
$admin = true;
require('top.php');

?>

<div id="centercontent">
  <hr>
  <?
  mysqlconnect($connection);

  $cust_id = isset($_GET['cust_id']) ? $_GET['cust_id'] : "";
  $cust_id = trim($cust_id);

  // redirect to the elist.php page with bad event_id
  if (empty($cust_id)) {
    $loc = "Location: /members/elist.php";
    header($loc);
    exit;
  }

  // does customer exist?
  $CustExists = getCustomerId($cust_id);

  // first delete members records from the reserve table
  // no orphans allowed!
  //if ($CustExists) {
  $querydelete = "delete from reserve where cust_id = '$cust_id'";
  if (!($result = @mysqli_query($connection, $querydelete)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $querydelete = "delete from members where cust_id = '$cust_id'";
  if (!($result = @mysqli_query($connection, $querydelete)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );
  //}
  ?>


  <h3>Record with ID <?php echo $cust_id; ?> has been Deleted </h3>

  Click <a href="mlist.php">here</a> to go return to previous screen.
</div>

<div id="logo">
  <img src="/images/logo1.gif" width="152" height="143" alt="logo">
</div>

<?php
require('footer.php');
?>

</body>

</html>