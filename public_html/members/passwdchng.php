<?php
/* member password change */

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$MinPasswordLength = 4;

function change_password($cust_id, $newUsername, $newPassword)
{
  global $referer;
  //global $username;
  //global $password;
  //global $hostName;
  //global $databaseName;
  global $message;
  //global $public_domain_name;
  global $MemberInfo;

  $loginUsername = LoginUsername();
  // Formulate the SQL find the user
  $query = "SELECT user_name FROM members
               WHERE (cust_id='" . $cust_id . "');";

  // Open a connection to the DBMS
  mysqlconnect($connection);

  // Execute the query
  $phpself = "FILE : " . $_SERVER['PHP_SELF'] . "\n";
  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . $phpself . ": " .
        mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  // exactly one row? then we have found the user
  if ((mysqli_num_rows($result) == 1)) {
    // Clear any other session variables
    if (SessionIsRegistered("errors"))
      // Delete the form errors session variable
      SessionUnregister("errors");

    if (SessionIsRegistered("formVars"))
      // Delete the formVars session variable
      SessionUnregister("formVars");

    $newUsername = strtolower($newUsername);

    // encrypt the supplied password and update the users record!
    // Get the two character salt from the
    // user-name collected from the challenge
    $salt = substr($newUsername, 0, 2);

    // Encrypt the loginPassword collected from
    // the challenge
    $crypted_password = crypt($newPassword, $salt);

    // Formulate the SQL to update the username & password
    $query = "UPDATE members SET "
      . "password='" . $crypted_password . "', "
      . "user_name='" . $newUsername . "' "
      . "WHERE (cust_id='" . $cust_id . "');";

    // Execute the query
    $phpself = "FILE : " . $_SERVER['PHP_SELF'] . "\n";
    if (!($result = @mysqli_query($connection, $query)))
      trigger_error(
        "MySQL error: " . $phpself . ": " .
          mysqli_errno($connection) . " : " . mysqli_error($connection),
        E_USER_ERROR
      );

    // update the session variable so the
    // user will continue to be logged in!
    $AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
    $LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
    $VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
    if ($AdminLevel) {
      $loc = "Location: /admin/index.php";
    } else {
      //if (strcmp($newUsername,$loginUsername)) {
      //  SessionUnregister("loginUsername");
      //  SessionRegister("loginUsername");
      //  $loginUsername = $newUsername;
      //}
      $loc = "Location: /members/medit.php?cust_id=" . $cust_id;
    }
    header($loc);
    exit;
  } else {
    // Register an error message
    $message = "Customer ID not found! Please try again.";
    SessionRegister("message", $message);

    $loc = "Location: /members/passwd_change.php?cust_id=" . $cust_id;
    header($loc);
    exit;
  }
}

// Function that shows the HTML <form> that is
// used to collect the user-name and password
function passwd_change_form($cust_id, $newUsernameClean, $loginUsername)
{
  global $message;
  //global $doc_root;
  //global $ContactPhoneNumber;
  //global $secure_url_prefix;
  //global $ClubCompanyName;
  //global $_SERVER['PHP_SELF'];
  //global $ShortClubName;
  //global $DirOffset;
  //global $RelOffset;
  //global $public_domain_name;
  global $MemberInfo;

  $ClubCompanyName = GetParameter('ClubCompanyName');
  $ShortClubName = GetParameter('ShortClubName');
  $CantactPhoneNumber = GetParameter('CantactPhoneNumber1');


  $FileName = $_SERVER['PHP_SELF'];
  $WebPageTitle = 'Event Leader - ' . $ClubCompanyName;
  require('top.php');

?>

  <div id="centercontent">
    <hr>
    <form method="POST" action="/members/passwdchng.php?cust_id=<?php echo $cust_id; ?>">
      <?php
      // Show messages
      showMessage();

      // Generate the password change <form> layout
      ?>
      <table>
        <tr>
          <td>Username:</td>
          <td><?php echo isset($newUsernameClean) ? $newUsernameClean : ''; ?></td>
        </tr>

        <tr>
          <td>Enter new password:</td>
          <td><input type="password" size=20 maxlength=20 name="newPassword"></td>
        </tr>

        <tr>
          <td>Re-enter new password:</td>
          <td><input type="password" size=20 maxlength=20 name="newPassword_verify"></td>
        </tr>

        <tr>
          <td><input type="submit" value="Change Member Password"></td>
        </tr>
      </table>
    </form>
  </div>

  <?php
  require('footer.php');
  ?>

  </body>

  </html>

<?php
}

// ------------------

// Register an error message
$message = "";
SessionRegister("message", $message);
// Check if the user is already logged in
$loginUsername = LoginUsername();
$AdminLevel = AuthLevel(LoginUsername()) == "Admin";

if ((!SessionIsRegistered("loginUsername"))) {
  // If they are not logged in then just bounce them back where
  // they came from
  if (SessionIsRegistered("referer")) {
    $referer = $_SESSION["referer"];
    SessionUnregister("referer");
    header("Location: $referer");
    exit;
  }
}


// Have they provided both a username and password?
if (isset($_GET["cust_id"]))
  $cust_id = clean($_GET["cust_id"], 20);
$UsernameFromTable = getLoginUsername($cust_id);
if (isset($_POST["newUsername"]))
  $newUsernameClean = clean($_POST["newUsername"], 20);
else
  $newUsernameClean = $UsernameFromTable;

if (isset($_POST["newPassword"]))
  $newPasswordClean = clean($_POST["newPassword"], 20);
if (isset($_POST["newPassword_verify"]))
  $newPassword_verifyClean = clean($_POST["newPassword_verify"], 20);


if ((!$AdminLevel) and (strcmp(strtolower($loginUsername), strtolower($UsernameFromTable)))) {
  $message = "You are not authorized to make this change.";
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}

if (
  (empty($newUsernameClean) or
    empty($newPasswordClean)) or
  (empty($newPassword_verifyClean))
) {
  $message = "Please supply a password.";
} elseif (strcmp($newPasswordClean, $newPassword_verifyClean)) {
  $message = 'Passwords are different, please try again.';
} elseif (strlen($newPasswordClean) < $MinPasswordLength) {
  $message = 'Password must have at least '
    . $MinPasswordLength
    . ' characters.';
}

// Have they not provided a username/password, or was there an error?
if (!empty($message))
  passwd_change_form($cust_id, $newUsernameClean, $loginUsername);
else
  // They have provided a login. Is it valid?
  change_password($cust_id, $newUsernameClean, $newPasswordClean);


?>