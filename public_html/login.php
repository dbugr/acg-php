<?php
/*
get username and password
if acceptable
   set session variable
   refer user to members page
else
   make em try again!

*/

// This script manages the login process.
// It should only be called when the user is not logged in.
// If the user is logged in, it will redirect back to the calling page.
// If the user is not logged in, it will show a login <form>

require('always.include.php');
//$debug = true;
$debug = false;

//session_start();
//require('include.php');

// Open a connection to the DBMS
mysqlconnect($connection);

//==================================================
// if global debug variable is true, write parameters to log file
function DebugLogMsg($description, $var)
{
  global $debug;

  if (isset($debug) && $debug) {
    LogMsg($description . " " . print_r($var, true));
  }
}


// update users.date_last_login field with current date/time
function update_last_login_date($customerid)
{
  global $connection;

  $date_time = date('Y-m-d H:i:s', time());

  $query = "UPDATE members SET u_date_last_login = '$date_time'
            WHERE cust_id = '$customerid'";

  // Execute the query
  if (!($result = @mysqli_query($connection, $query))) {
    trigger_error("MySQL error: " 
    . mysqli_errno($connection) . " : " 
    . mysqli_error($connection), E_USER_ERROR
  );
  }
  return ($result);
}


// check if the user's account has expired
function GetUserRecord($loginUsername)
{
  global $connection;
  //global $ClubCode;
  $ClubCode = GetParameter('ClubCode');

  $query = "SELECT user_name, password, m_club,
				   u_date_expiration FROM members
               WHERE ( (user_name = '$loginUsername')
			   		and (m_club = '$ClubCode') );";

  // Execute the query
  if (!($result = @mysqli_query($connection, $query))) {
    trigger_error("MySQL error: " 
    . mysqli_errno($connection) . " : " 
    . mysqli_error($connection), E_USER_ERROR
  );
    return (0);
  }

  $FoundMatchingUsername = mysqli_num_rows($result);
  $row = mysqli_fetch_array($result);
  return ($row);
}


// check if the user's account has expired
function ActiveMembership($UserRecord)
{
  global $debug;
  //$debug = true;

  $active = false;
  if (!empty($UserRecord)) {
    // if time() < u_date_expired return true valid membership
    $u_date_expiration = $UserRecord["u_date_expiration"];
    if (!empty($u_date_expiration)) {
      $expiration_time_stamp = strtotime($u_date_expiration);
    } else {
      $expiration_time_stamp = strtotime('1970-01-01');
    }
    $time_stamp_today = (float) time();
    if ((float) $time_stamp_today < (float) $expiration_time_stamp) {
      $active = true;
    }
    if ($debug) {
      //if (1) {
      LogMsg('UserRecord:u_date_expiration: ' . $UserRecord["u_date_expiration"]);
      LogMsg('u_date_expiration: ' . $u_date_expiration);
      LogMsg('date(expiration_time_stamp): ' . date("Y-m-d H:i:s", $expiration_time_stamp));
      LogMsg('expiration_time_stamp: ' . $expiration_time_stamp);
      LogMsg('time_stamp_today: ' . $time_stamp_today);
      LogMsg('expiration_time_stamp: ' . $expiration_time_stamp);
      LogMsg('active: ' . $active);
    }
  }
  return ($active);
}


function PasswordCompare($loginUsername, $loginPassword, $UserRecord)
{
  global $error;
  // return true if passwords match, otherwise return false

  $PasswordsMatch = false;
  // Get the two character salt from the user-name collected from the challenge
  $salt = substr($loginUsername, 0, 2);
  // Encrypt the loginPassword collected from the challenge
  $crypted_password = crypt($loginPassword, $salt);
  $PasswordFromDatabase = $UserRecord['password'];
  //$error->debug($loginUsername, 'loginUsername', __FILE__, __LINE__);			
  //$error->debug($loginPassword, 'loginPassword', __FILE__, __LINE__);			
  //$error->debug($crypted_password, 'crypted_password', __FILE__, __LINE__);			
  //$error->debug($PasswordFromDatabase, 'PasswordFromDatabase', __FILE__, __LINE__);			
  //LogMsg('crypted_password: ' . $crypted_password);			
  //LogMsg('PasswordFromDatabase' . $PasswordFromDatabase);
  if ($crypted_password == $PasswordFromDatabase) {
    $PasswordsMatch = true;
  } else { // try using lower case loginUsername...
    // Get the two character salt from the user-name collected from the challenge
    $salt = substr(strtolower($loginUsername), 0, 2);
    // Encrypt the loginPassword collected from the challenge
    $crypted_password = crypt($loginPassword, $salt);
    $PasswordFromDatabase = $UserRecord['password'];
    if ($crypted_password == $PasswordFromDatabase) {
      $PasswordsMatch = true;
    }
  }
  return ($PasswordsMatch);
}



// check if the suppied user/pswd are valid
function check_login($loginUsername, $loginPassword)
{
  global $connection;
  global $referer;
  global $message;
  global $error;
  global $debug;

  $ClubCode = GetParameter('ClubCode');

  //LogMsg('=====================================');
  //LogMsg('AUTHENTICATING LOGINUSERNAME: '.$loginUsername);
  $Authenticated = false;
  $UserRecord = GetUserRecord($loginUsername);
  $ActiveMembership = false;

  $PasswordsMatch = false;
  $ActiveMembership = false;
  if (!empty($UserRecord)) {
    // record exists, is user an active member or expired?
    $ActiveMembership = ActiveMembership($UserRecord);
    $PasswordsMatch =
      PasswordCompare(
        $loginUsername,
        $loginPassword,
        $UserRecord
      );
    if ($ActiveMembership && $PasswordsMatch) {
      $Authenticated = true;
    }
    //if ($debug) {
    //  LogMsg("ActiveMembership: " . $ActiveMembership);
    //  LogMsg("PasswordsMatch: " . $PasswordsMatch);
    //  LogMsg("Authenticated: " . $Authenticated);
    //}
  }

  // record remote IP number and x_forwarded_for values
  LogMsg('===========================================');
  LogMsg("loginUsername: ".$loginUsername);
  LogMsg("ActiveMembership: " . $ActiveMembership);
  LogMsg('REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR']);
  if (array_key_exists('HTTP_X_FORWARDED_FOR',$_SERVER)) {
    LogMsg('HTTP_X_FORWARDED_FOR: '.$_SERVER['HTTP_X_FORWARDED_FOR']);
  }

  if (isset($debug) && $debug) {
    LogMsg("EmptyUserRecord: ".empty($UserRecord) ? 'empty' : 'not empty');
    LogMsg("loginUsername: ".$loginUsername);
    //LogMsg("loginPassword: ".$loginPassword);
    LogMsg("PasswordsMatch: " . $PasswordsMatch);
    LogMsg("Authenticated: " . $Authenticated);
  }

  // username exists, is active and password matches
  if (!empty($UserRecord) && ($ActiveMembership &&  $PasswordsMatch)) {
    // Register the loginUsername to show the user is logged in
    SessionRegister("loginUsername", $loginUsername);
    LogMsg('SessionRegister($loginUsername): ' . $loginUsername);
    // obtain members customer id from users table
    $customerid = getCustomerID($loginUsername);
    DebugLogMsg('CUSTOMERID: ', $customerid);
    SessionRegister("customerid", $customerid);
    // Clear any other session variables
    if (SessionIsRegistered("errors"))
      // Delete the form errors session variable
      SessionUnregister("errors");
    if (SessionIsRegistered("formVars"))
      // Delete the formVars session variable
      SessionUnregister("formVars");
    // update users date_last_login field with current date/time
    update_last_login_date($customerid);

    // Do we need to redirect to a calling page?
    if (SessionIsRegistered("referer")) {
      // get the referer session variable
      $referer = $_SESSION["referer"];
      // delete the referer session variable value
      SessionUnregister("referer");
      // use $referer to redirect
      $loc = "Location: " . $referer;
      LogMsg('SessionIsRegistered(referer): redirecting to $loc: ' . $loc);
      header($loc);
      exit;
    } else {
      // login approved, redirect to event list script
      $loc = "Location: /members/elist.php";
      LogMsg('login approved redirecting to $loc: ' . $loc);
      header($loc);
      exit;
    }
  }

  // login failed, grudgingly give the poor wretched user some feedback
  // unregister the users session variable so the user is not logged in
  if (SessionIsRegistered("loginUsername"))
    SessionUnregister("loginUsername");
  // Register an error message
  //SessionIsRegistered("message");
  // username matches, expired membership
  $message = "Unknown error";
  if (empty($UserRecord))
    $message = "Username or password incorrect, login failed.";
  else if (!empty($UserRecord) && (!$ActiveMembership)) {
    $message = "Membership expired, login failed";
    if ($debug) {
      LogMsg("message@login.php: " . $message);
      LogMsg("ActiveMembership@login.php: " . $ActiveMembership);
    }
  } else if ((!empty($UserRecord) && ($ActiveMembership))
    && (!$PasswordsMatch)
  )
    $message = "Username or Password incorrect, login failed.";
  // Show the login page so the user can have another go!
  LogMsg('login failed $loginUsername: ' . $loginUsername . '   $message: ' . $message);
  login_page($message);
  exit;
} // end function check_login



// Function that shows the HTML <form> that is 
// used to collect the user-name and password
function login_page($message)
{

  $FileName = __FILE__;
  $ClubCompanyName = GetParameter("ClubCompanyName");
  $WebPageTitle = 'Login - ' . $ClubCompanyName;
  require('top.php');

?>

  <div id="centercontent">
    <p>Please log in to the <?php echo $ClubCompanyName; ?> to make memories and new friends.</p>
    <form method="POST" action="login.php" id="login_form" name="login_form">

      <h3>
        <font color="red"><?php echo $message; ?></font>
      </h3>

      <table>
        <tr>
          <td>Enter your username:</td>
          <td><input type="text" size=20 maxlength=30 name="loginUsername"></td>
        </tr>
        <tr>
          <td>Enter your password:</td>
          <td><input type="password" size=20 maxlength=20 name="loginPassword"></td>
        </tr>
        <tr>
          <td><input type="submit" value="Log in"></td>
        </tr>
      </table>
    </form>
  </div>

  <!-- add/chng chuck 8/26/2005   -->
  <script language='javascript'>
    document.login_form.loginUsername.focus();
  </script>

<?php
  require('footer.php');
} // end function login_page()

// ============================================================
// code execution begins here

$s = "";
if (isset($_POST["loginUsername"])) {
  $loginUsername = clean($_POST["loginUsername"], 30);
  $s = $loginUsername;
}

//LogMsg('dumping $loginUsername: ' . $s);
//LogMsg('dumping $_SESSION: ' . print_r($_SESSION, true));

if (isset($_POST["loginPassword"]))
  $loginPassword = clean($_POST["loginPassword"], 20);

// Check if the user is already logged in
if (SessionIsRegistered("loginUsername")) {
  $loc = "Location: /index.php";
  LogMsg('already logged in redirecting to $loc: '.$loc);
  header($loc);
  exit;
}


$message = "";
// Have they provided both a username and password?
if (
  empty($_POST["loginUsername"]) &&
  empty($_POST["loginPassword"])
) {
  $message = "Both a username and password must be supplied.";
}

if (
  empty($_POST["loginUsername"]) &&
  !empty($_POST["loginPassword"])
) {
  $message = "You must supply a username!";
}

if (
  !empty($_POST["loginUsername"]) &&
  empty($_POST["loginPassword"])
) {
  $message = "You must supply a password!";
}

// did user provide a username/password, or was there an error?
if (!empty($message)) {
  login_page($message);
} else {
  // They have provided a login. Is it valid?
  check_login($loginUsername, $loginPassword);
}
?>