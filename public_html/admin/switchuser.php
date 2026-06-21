<?php
/* AdventureClub.info
  
   while logged in with admin privilages,
   given a cust_id vie http_vars
   switch to that user_id, 
   i.e. set loginUsername to the user
   the corresponds to the given user_id   
  */


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


// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" .
      $errors[$fieldName] .
      "</font><br>";
}

if (empty($errors)) {
  mysqlconnect($connection);

  // obtain member information from session variable
  //$cust_id = $customer_id;

  // obtain member information from members table
  //$cust_id = getCustomerID($loginUsername);

  //if ($cust_id == NULL) {
  //   $message = "Error: Invalid Customer ID!\n";
  //}
  //else {
  $cid = clean($_GET['cust_id']);
  if (!isset($cid)) {
    trigger_error(
      "Invalid member_id (" . $cid . ")\n",
      E_USER_ERROR
    );
  }
  $cid = quotesqldata($cid);
  // obtain this members data
  $query = "SELECT cust_id,user_name FROM members WHERE (cust_id='" . $cid . "');";
  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // obtain member data
  $u_row = mysqli_fetch_array($result);

  $_SESSION['loginUsername'] = $u_row['user_name'];
  $customer_id = $u_row['cust_id'];

  //}
}

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Members List ' . $ClubCompanyName;
//$admin = true;
require('top.php');

?>

<div id="centercontent">
  <hr>
  <?php
  // Display any messages to the user
  showMessage();
  ?>


  <h3>You are now logged in as loginUsername: &nbsp;
    <?php echo $_SESSION['loginUsername']; ?>
  </h3>

  <p>To regain your admin privileges, you must log out and then
    log back in again!</p>
</div>

<?php
require('footer.php');
?>