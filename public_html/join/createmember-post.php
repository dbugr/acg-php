<?php
// This script validates customer data entered into
// join.php
// If validation succeeds, it INSERTs or UPDATEs
// a customer and redirect to the credit card form page; if it
// fails, it creates error messages and redirects 
// back to join.php

//require ('site.php');	
//require ( $path . 'ErrorHandler.inc');
//$error =& new ErrorHandler();
//$error->set_context('strict',TRUE);

// Connect to a session
require('always.include.php');
//session_start();
//require('include.php');
//require('cc.php');

////// functions
// Get Next member id from database 

function GetNextMemberIdFromDatabase(&$connection)
{
  global $ClubCode;

  $ClubCodeLength = strlen($ClubCode);
  $query = "SELECT MAX(substring(cust_id, " . $ClubCodeLength
    . " + 2 "
    . ", "
    . "14 "
    . ") + 0 ) "
    . "AS cust_id "
    . "FROM members "
    . "WHERE (cust_id LIKE '" . $ClubCode . "%');";
  if (!($result = @mysqli_query($connection, $query))) {
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
    exit;
  }
  $row = mysqli_fetch_array($result);
  $cust_id = $row['cust_id'];
  $cust_id += 1;
  $cust_id = $ClubCode . "_" . $cust_id;
  return $cust_id;
}

// Clear any errors that might have been 
// found previously
$errors = array();

// Set up a $formVars array with the POST variables
// and register with the session.
foreach ($_POST as $varname => $value)
  $formVars[$varname] = clean($value);

// go ahead and connect to the database
mysqlconnect($connection);

// Validate the firstName
if (empty($formVars["firstName"]))
  $errors["firstName"] = "The first name field cannot be blank.";
else if (strlen($formVars["firstName"]) > 50)
  $errors["firstName"] = "The first name must be less than 50 characters";

// Validate the Surname
if (empty($formVars["surname"]))
  $errors["surname"] = "The Last name cannot be blank.";
else if (strlen($formVars["surname"]) > 50)
  $errors["surname"] = "The Last name must be less than 50 characters";

// Validate the Address
if (
  empty($formVars["address1"]) &&
  empty($formVars["address2"]) &&
  empty($formVars["address3"])
) {
  // all the fields of the address cannot be null
  $errors["address"] = "You must supply at least one address line.";
} else {
  if (strlen($formVars["address1"]) > 50)
    $errors["address1"] = "The address line 1 can be no longer than 50 characters";
  if (strlen($formVars["address2"]) > 50)
    $errors["address2"] = "The address line 2 can be no longer than 50 characters";
  if (strlen($formVars["address3"]) > 50)
    $errors["address3"] = "The address line 3 can be no longer than 50 characters";
}

// Validate the user's Initial
// If there is a middle initial, it must be one character in length
if (!empty($formVars["initial"]) && (!strlen($formVars["initial"] == 1)))
  $errors["initial"] = "The initial field must be empty or one character in length.";

// Validate the City
if (empty($formVars["city"]))
  $errors["city"] = "You must supply a city.";
else if (strlen($formVars["city"]) > 20)
  $errors["city"] = "The city must be less than or equal to 20 characters";

// Validate State - any string less than 21 characters
if (empty($formVars["state"]))
  $errors["state"] = "You must supply a state.";
else if (strlen($formVars["state"]) > 20)
  $errors["state"] = "The state can be no longer than 20 characters";

// Validate Zipcode
if (empty($formVars["zipcode"]))
  $errors["zipcode"] = "You must supply a zip code.";
else if (!preg_match("^([0-9]{4,5}[- ]?[0-9]*)$", $formVars["zipcode"]))
  $errors["zipcode"] = "The zipcode must be 4 or 5 digits in length. " .
    "zip+4 is optional";

// Validate Country
if (strlen($formVars["country"]) > 20)
  $errors["country"] = "The country must be less than 21 characters";

// Validate Phone 
if (empty($formVars["phone"]))
  $errors["phone"] = "You must enter a contact phone number";

// Validate sex field
if (empty($formVars["sex"]))
  $errors["sex"] = "You must indicate your sex";

// validate email
$validEmailExpr =
  "^[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*" .
  "@[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*$";

// the user's email cannot be a null string
if (empty($formVars["email"]))
  $errors["email"] = "You must supply an email address.";
else if (strlen($formVars["email"]) > 50)
  $errors["email"] = "The email address can be no longer than 50 characters.";

// validate referral field
if (empty($formVars["referral"]))
  $errors["referral"] = "You must supply a referral.";

// Validate username - must be non-empty
if (empty($formVars["username"]))
  $errors["username"] = "You must supply a username";
else {
  // Check if the username is already in use

  $query = "SELECT * FROM members
              WHERE user_name = '" . $formVars["username"] . "'";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  // Is it taken?
  if (mysqli_num_rows($result) == 1)
    $errors["username"] = "A customer already exists with this login name.";
}

// Validate password - between 6 and 16 characters
if ((strlen($formVars["password"]) < 6) || (strlen($formVars["password"]) > 20))
  $errors["password"] = "The password must be between 4 and 20 characters in length";

// Validate discount code field
if (!empty($formVars["discountcode"])) {
  $query = "SELECT discount_code FROM ac_discountcode
              WHERE discount_code = '" .
    $formVars["discountcode"] . "'";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

  // Is it valid?
  if (mysqli_num_rows($result) != 1)
    $errors["discountcode"] = "You supplied an invalid discount code!";
}

// Validate agreement field
if (empty($formVars["agreement"]) || ($formVars["agreement"] != "agree"))
  $errors["agreement"] = "To become a member, you must agree to the policies and terms";

$formVars['cost'] = "12.00";

// Now the script has finished the validation,
// check if there were any errors
if (count($errors) > 0) {
  // There are errors.
  $_SESSION['errors'] = $errors;
  $_SESSION['formVars'] = $formVars;

  //Relocate back to the client form
  header("Location: " . GetParameter('vd') . "createmember.php");
  exit;
}

// set initial date of birth to thirty years old
// need this for member age statistics!
$date_of_birth = (date('Y', time()) - 30) . '-00-00';

// set member expiration date for one year in the future
$days_in_future = 364;
$u_date_expiration = time() + ($days_in_future * 24 * 3600);
$u_date_expiration = date('Y-m-d', $u_date_expiration);

// Use the first two characters of the
// USERNAME as a salt for the password
$formVars["username"] = strtolower($formVars["username"]);
$salt = substr($formVars["username"], 0, 2);

// Create the encrypted password
$stored_password = crypt($formVars["password"], $salt);

// get next customer id
$cust_id = GetNextMemberIdFromDatabase($connection);
$query = "INSERT INTO members (" .
  "cust_id, " .
  "m_lastname, " .
  "m_firstname, " .
  "m_address1, " .
  "m_address2, " .
  "m_address3, " .
  "m_city, " .
  "m_state, " .
  "m_zipcode, " .
  "m_country, " .
  "m_phonehome, " .
  "m_sex, " .
  "m_email, " .
  "m_referral, " .
  "m_referral_detail, " .
  "m_discount_code, " .
  "m_date_birth, " .
  "m_date_joined, " .
  "m_memberstatus, " .
  "password,user_name,u_auth_level,u_date_expiration," .
  "m_club" .
  ") " .
  " VALUES (" .
  "'" . $cust_id . "', " .
  "\"" . $formVars["surname"] . "\", " .
  "\"" . $formVars["firstName"] . "\", " .
  "\"" . $formVars["address1"] . "\", " .
  "\"" . $formVars["address2"] . "\", " .
  "\"" . $formVars["address3"] . "\", " .
  "\"" . $formVars["city"] . "\", " .
  "\"" . $formVars["state"] . "\", " .
  "\"" . $formVars["zipcode"] . "\", " .
  "\"" . $formVars["country"] . "\", " .
  "\"" . $formVars["phone"] . "\", " .
  "\"" . $formVars["sex"] . "\", " .
  "\"" . $formVars["email"] . "\", " .
  "\"" . $formVars["referral"] . "\", " .
  "\"" . $formVars["referral_detail"] . "\", " .
  "\" \", " .
  "\"" . $date_of_birth . "\", " .
  "'" . date('Y-m-d', time()) . "', " .
  "\"Paid \", " .
  "'" . $stored_password . "', " .
  "'" . $formVars["username"] . "', " .
  "'Member'," .
  "'" . $u_date_expiration . "'," .
  "'" . $ClubCode . "'" .
  ")";

// Run the query on the customer table
if (!(@mysqli_query($connection, $query))) {
  if ($laptop) echo $query . "<br>";
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
}

// notify admin staff that someone tried to join the club!
$formVars['cust_id'] = $cust_id;

SessionUnregister('formVars');
SessionUnregister('errors');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
  <title>Member Created</title>
  <link rel=STYLESHEET href=" <?php echo GetParameter('vd'); ?>club.css" Type="text/css">
</head>

<body>
  <div id="centercontent">
    <hr>
    <p>Member created</p>
    <p>&nbsp;</p>
    Press <a href="<?php echo $home; ?>">home</a> to return to the club site.<br>
    Or, <a href="<?php echo GetParameter('vd') . "createmember.php" ?>">CreateMember</a> to create another member.
    <p>&nbsp;</p>
  </div>
  <?php
  echo SSLBannerOnly("Receipt", "Join", 3);

  // reset data
  $formVars = array();
  $errors = array();
  ?>
</body>

</html>