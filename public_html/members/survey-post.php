<?php
// survey-post: put survey results into members record

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$ClubCompanyName   = GetParameter('ClubCompanyName');


// Is the user logged in?
if ((!SessionIsRegistered("loginUsername"))) {
   // Register a message to show the user
   $message = "Error: you are not logged in!";
   SessionRegister("message", $message);

   // Register where they came from
   $referer = $_SERVER['PHP_SELF'];
   SessionRegister("referer", $referer);

   // redirect to the login page
   header("Location: /login.php");
   exit;
}

$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';


// Set up a $formVars array with the POST variables
// and register with the session.
if (!SessionIsRegistered("formVars"))
   SessionRegister("formVars", array());

foreach ($_POST as $varname => $value)
   $formVars[$varname] = trim($value);

$cust_id = isset($formVars["cust_id"]) ? $formVars["cust_id"] : getCustomerID(LoginUsername());

// package data into a string
$preferences = "";
foreach ($formVars as $varname => $value)
   $preferences .= $varname . "=" . $value . "&";
$preferences .= "\r\n";

// connect to the database
mysqlconnect($connection);

// TODO: update members row
$query = "UPDATE members SET m_preferences = '" . quotesqldata($preferences) . "' " .
   "WHERE cust_id='" . $cust_id  . "'";

// Run the query on the members table
if (!(@mysqli_query($connection, $query))) {
   trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
}

// Clear the formVars so a future <form> is blank
$formVars = array();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
   <title>Thanks for taking our survey</title>
   <meta http-equiv="refresh" content="5;URL=/">
   <meta name="description" content="Oops! Try it once more to come to where the action is.">
   <meta name="keywords" content="Gainesville, Florida, activities, fun, kayaking">
   <meta name="Robots" content="index,follow">
   <meta name="Author" content="<?php echo $ClubCompanyName; ?>">
</head>

<body>
   <div align="center">

      <h3><b> Thanks for taking our survey!
            <?php
            //foreach($formVars as $varname => $value)
            //	echo $varname . "=" . $value . "<br>";
            ?>
            <br>
            Your browser should automatically take you back to the site in 5 seconds. <br><br>

            If it doesn't, please go to <a href="/"><?php echo $ClubCompanyName ?></a>
         </b></h3>
   </div>
</body>

</html>