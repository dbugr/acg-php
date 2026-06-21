<?php
/*
adventure club

validate and post membership data to database

*/

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in?
if ((!SessionIsRegistered("loginUsername")))
{
    // Register a message to show the user
    $message = "Error: you are not logged in!";
    SessionRegister("message",$message);

    // Register where they came from
    $referer = $_SERVER['PHP_SELF'];
    SessionRegister("referer",$referer);

    // redirect to the login page
    header("Location: /login.php");
    exit;
}

$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';	
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';	
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';	

if ($debug) {
	LogMsg("scriptName: " . basename(__FILE__));
	LogMsg("loginUsername: " . $loginUsername);
}

// obtain member information from session variable
//$cust_id = $customer_id;

// obtain member information from members table
$cid = getCustomerID($loginUsername);

function DisplayArry($arry) {

	echo "<html>";
	echo "<head><title>AC Event View Data</title></head>";
	echo '<body bgcolor="white">';

	echo "<pre>";
	echo "Data Dump for array<br>";

	print_r($arry);

	echo "</pre>";
	echo "</body></html>";
	die("<br>Thats all, folks!");
}

function DisplayVar($var) {

	echo "<html>";
	echo "<head><title>AC Event View Data</title></head>";
	echo '<body bgcolor="white">';

	echo "<pre>";
	echo "Data Dump for array<br>";

	print_r($var);

	echo "</pre>";
	echo "</body></html>";
	die("<br>Thats all, folks!");
}


function DispMemberProfile($formVars,$cust_id) {
	global $doc_root;

	// did the club member select any profile data to be displayed?
	$disp = 0;
	foreach ($formVars as $key => $val) {
	if (substr($key,0,4) == 'disp')
	if ($val)
		$disp = 1;
	}

	if (!empty($formVars["summary"]))
		$disp = 1;
	if (!empty($formVars["occupation"]))
		$disp = 1;
	if (!empty($formVars["details"]))
		$disp = 1;

	// did the member upload a photo?
	if (file_exists($doc_root . "/var/webtmp/$cust_id.jpg")) {
		$disp = 1;
	}

	return $disp;
}





  // Clear any errors that might have been
  // found previously
  $errors = array();

  // Set up a $formVars array with the POST variables
  // and register with the session.
  //if (!SessionIsRegistered("formVars"))
  SessionRegister("formVars",array());
  SessionRegister("errors",array());
  SessionRegister("photoerrors",array());
  

  foreach($_POST as $varname => $value)
      $_SESSION['formVars'][$varname] = trim($value);
      //$formVars[$varname] = trim(clean($value, 50));

	$_SESSION['formVars']['username'] = $loginUsername;
  
     $cust_id = isset($_SESSION['formVars']["cust_id"])
                    ? $_SESSION['formVars']["cust_id"] : -1;

     $_SESSION['formVars']["disp_email"]       = isset($_SESSION['formVars']["disp_email"])
                                         ? $_SESSION['formVars']["disp_email"] : NULL;
     $_SESSION['formVars']["disp_title"]       = isset($_SESSION['formVars']["disp_title"])
                                         ? $_SESSION['formVars']["disp_title"] : NULL;
     $_SESSION['formVars']["disp_lastname"]    = isset($_SESSION['formVars']["disp_lastname"])
                                         ? $_SESSION['formVars']["disp_lastname"] : NULL;
     $_SESSION['formVars']["disp_address"]     = isset($_SESSION['formVars']["disp_address"])
                                         ? $_SESSION['formVars']["disp_address"] : NULL;
     $_SESSION['formVars']["disp_address2"]    = isset($_SESSION['formVars']["disp_address2"])
                                         ? $_SESSION['formVars']["disp_address2"] : NULL;
     $_SESSION['formVars']["disp_address3"]    = isset($_SESSION['formVars']["disp_address3"])
                                         ? $_SESSION['formVars']["disp_address3"] : NULL;
     $_SESSION['formVars']["disp_city"]        = isset($_SESSION['formVars']["disp_city"])
                                         ? $_SESSION['formVars']["disp_city"] : NULL;
     $_SESSION['formVars']["disp_state"]       = isset($_SESSION['formVars']["disp_state"])
                                         ? $_SESSION['formVars']["disp_state"] : NULL;
     $_SESSION['formVars']["disp_zipcode"]     = isset($_SESSION['formVars']["disp_zipcode"])
                                         ? $_SESSION['formVars']["disp_zipcode"] : NULL;
     $_SESSION['formVars']["disp_country"]     = isset($_SESSION['formVars']["disp_country"])
                                         ? $_SESSION['formVars']["disp_country"] : NULL;
     $_SESSION['formVars']["disp_phonehome"]   = isset($_SESSION['formVars']["disp_phonehome"])
                                         ? $_SESSION['formVars']["disp_phonehome"] : NULL;
     $_SESSION['formVars']["disp_phonemobile"] = isset($_SESSION['formVars']["disp_phonemobile"])
                                         ? $_SESSION['formVars']["disp_phonemobile"] : NULL;
     $_SESSION['formVars']["disp_phonework"]   = isset($_SESSION['formVars']["disp_phonework"])
                                         ? $_SESSION['formVars']["disp_phonework"] : NULL;
     $_SESSION['formVars']["disp_phonemisc"]   = isset($_SESSION['formVars']["disp_phonemisc"])
                                         ? $_SESSION['formVars']["disp_phonemisc"] : NULL;
     $_SESSION['formVars']["disp_email2"]      = isset($_SESSION['formVars']["disp_email2"])
                                         ? $_SESSION['formVars']["disp_email2"] : NULL;
     $_SESSION['formVars']["disp_sex"]         = isset($_SESSION['formVars']["disp_sex"])
                                         ? $_SESSION['formVars']["disp_sex"] : NULL;
     $_SESSION['formVars']["disp_emailalias"]  = isset($_SESSION['formVars']["disp_emailalias"])
                                         ? $_SESSION['formVars']["disp_emailalias"] : NULL;
     $_SESSION['formVars']["disp_date_birth"]  = isset($_SESSION['formVars']["disp_date_birth"])
                                         ? $_SESSION['formVars']["disp_date_birth"] : NULL;

     $_SESSION['formVars']["email_on_new_event"]  = isset($_SESSION['formVars']["email_on_new_event"])
                                            ? $_SESSION['formVars']["email_on_new_event"] : 0;
     $_SESSION['formVars']["email_on_event_change"]  = isset($_SESSION['formVars']["email_on_event_change"])
                                               ? $_SESSION['formVars']["email_on_event_change"] : 0;

     $_SESSION['formVars']["ccName"]           = isset($_SESSION['formVars']["ccName"])
                                         ? $_SESSION['formVars']["ccName"] : NULL;
     $_SESSION['formVars']["ccType"]           = isset($_SESSION['formVars']["ccType"])
                                         ? $_SESSION['formVars']["ccType"] : NULL;
     $_SESSION['formVars']["ccNumber"]         = isset($_SESSION['formVars']["ccNumber"])
                                         ? $_SESSION['formVars']["ccNumber"] : NULL;
     $_SESSION['formVars']["ccExpireDate"]     = isset($_SESSION['formVars']["ccExpireDate"])
                                         ? $_SESSION['formVars']["ccExpireDate"] : NULL;

     $_SESSION['formVars']["passwordOrig"]     = isset($_SESSION['formVars']["passwordOrig"])
                                         ? $_SESSION['formVars']["passwordOrig"] : NULL;

  if ($debug) {
    //echo "Query: " . $query . "<br><br>";
	echo "email_on_new_event: "
         . $_SESSION['formVars']["email_on_new_event"] . "<br><br>";
	echo "email_on_event_change: "
         . $_SESSION['formVars']["email_on_event_change"] . "<br><br>";
    echo '<br>';
    foreach($_POST as $varname => $value)
      echo $varname . ":" . $value."<br>";
    echo '<br>';
  }



  // start field validation routines

  // Validate the firstName
  if (empty($_SESSION['formVars']["firstName"]))
      // First name cannot be a null string
      $errors["firstName"] =
          "The first name field cannot be blank.";

  elseif (!preg_match("#^[a-z'&-/ ]*$#i", $_SESSION['formVars']["firstName"]))
      // First name permit "James & Leah" 
      $errors["firstName"] =
      "The first name can only contain alphabetic " .
         "characters or \"-\" or \"'\" or Ampersand \"&\"";

  elseif (strlen($_SESSION['formVars']["firstName"]) > 50)
      $errors["firstName"] =
      "The first name can be no longer than 50 " .
         "characters";


  // Validate the Lastname
  if (empty($_SESSION['formVars']["lastname"]))
      // the user's lastname cannot be a null string
      $errors["lastname"] =
          "The lastname field cannot be blank.";
  elseif (strlen($_SESSION['formVars']["lastname"]) > 50)
      $errors["lastname"] =
          "The lastname can be no longer than 50 " .
          "characters";


  // Validate the Address
  if (empty($_SESSION['formVars']["address1"]) &&
      empty($_SESSION['formVars']["address2"]) &&
      empty($_SESSION['formVars']["address3"]))
      // all the fields of the address cannot be null
      $errors["address"] =
          "You must supply at least one address line.";
  else
  {
      if (strlen($_SESSION['formVars']["address1"]) > 50)
      $errors["address1"] =
          "The address line 1 can be no longer " .
          "than 50 characters";
      if (strlen($_SESSION['formVars']["address2"]) > 50)
      $errors["address2"] =
          "The address line 2 can be no longer " .
          "than 50 characters";
      if (strlen($_SESSION['formVars']["address3"]) > 50)
      $errors["address3"] =
          "The address line 3 can be no longer " .
          "than 50 characters";
  }


  // Validate the user's Initial
  if (!empty($_SESSION['formVars']["initial"]) &&
      !preg_match("#^[a-z]{1}$#i", $_SESSION['formVars']["initial"]))
      // If there is a middle initial, it must be
      // one character in length
      $errors["initial"] =
         "The initial field must be empty or one " .
         "character in length.";


  // Validate the City
  if (empty($_SESSION['formVars']["city"]))
      // the user's city cannot be a null string
      $errors["city"] = "You must supply a city.";
  elseif (strlen($_SESSION['formVars']["city"]) > 20)
      $errors["city"] =
        "The city must be less than or equal to 20 characters";


  // Validate State - any string less than 21 characters
  if (strlen($_SESSION['formVars']["state"]) > 20)
      $errors["state"] =
         "The state can be no longer than 20 characters";


  // Validate Zipcode
  if (!preg_match("#^([0-9]{4,5}[- ]?[0-9]*)$#i", $_SESSION['formVars']["zipcode"]))
      $errors["zipcode"] =
         "The zipcode must be 4 or 5 digits in length. " .
         "zip+4 is optional";


  // Validate Country
  if (strlen($_SESSION['formVars']["country"]) > 20)
      $errors["country"] =
         "The country can be no longer than 20 characters";


  // Phone must have
  //  correct format
  $validPhoneExpr =
     "^([0-9]{2,3}[- ]?)?[0-9]{3}[- ]?[0-9]{4}$";

  if (empty($_SESSION['formVars']["phonemobile"]))
      $errors["phonemobile"] =
        "You must enter a contact phone number";
//  elseif (!empty($_SESSION['formVars']["phonemobile"]) &&
//      !ereg($validPhoneExpr, $_SESSION['formVars']["phonemobile"]))
//      $errors["phonemobile"] =
//        "The phone number must be 8 digits in length, " .
//        "with an optional 2 or 3 digit area code";

  // Validate sex field
  if (empty($_SESSION['formVars']["sex"]))
      $errors["sex"] =
        "You must indicate your sex";

/*
  if (!preg_match("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#i",
          $_SESSION['formVars']["dob"], $parts))
      // Check the format
      $errors["dob"] =
        "The date of birth is not a valid date in the " .
        "format MM/DD/YYYY";
*/



  // validate email if this is an INSERT
    // Check syntax
     $validEmailExpr =
         "/^[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*" .
         "@[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*$/i";

     if (empty($_SESSION['formVars']["email"]))
         // the user's email cannot be a null string
         $errors["email"] =
            "You must supply an email address.";

     elseif (!preg_match($validEmailExpr, $_SESSION['formVars']["email"]))
         // The email must match the above regular
         // expression
         $errors["email"] =
            "The email address must be in the " .
            "name@domain format.";

     elseif (strlen($_SESSION['formVars']["email"]) > 50)
         // The length cannot exceed 50 characters
         $errors["email"] =
            "The email address can be no longer than " .
            "50 characters.";

    // validate email2 if this is an INSERT
    // Check syntax
     $validEmailExpr =
         "/^[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*" .
         "@[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*$/i";

    if ((!empty($_SESSION['formVars']["email2"])) &&
         (!preg_match($validEmailExpr, $_SESSION['formVars']["email2"]))
       )
         // The email must match the above regular
         // expression
         $errors["email2"] =
            "The email address must be in the " .
            "name@domain format.";

     elseif (strlen($_SESSION['formVars']["email2"]) > 50)
         // The length cannot exceed 50 characters
         $errors["email2"] =
            "The email address can be no longer than " .
            "50 characters.";


//DisplayArry($errors);

//DisplayArry($_SESSION['formVars']);

  // Now the script has finished the validation,
  // check if there were any errors
  if (count($errors) > 0)
  {
    // There are errors.  Relocate back to the client form
    //$loc = "Location: http://" . $_SERVER['SERVER_NAME'] . "/admin/medit.php";
	$_SESSION['errors'] = $errors;
	//$_SESSION['formVars'] = $formVars;
    $loc = "Location: /members/medit.php?cust_id=" . $_SESSION['formVars']['cust_id'];
    header($loc);
    exit;
  }

//DisplayArry($errors);


  // If we made it here, then the data is valid

  if (!isset($connection))
  {
	mysqlconnect($connection);
  }

  // Reassemble the date of birth into database format
	//$dob = "";
	//if (isset($_SESSION['formVars']["dob"]))
      //$dob = MySqlDate($_SESSION['formVars']["dob"]);
  // need this for member age statistics!
  $_SESSION['formVars']["birth_month"] = quotesqldata($_SESSION['formVars']["birth_month"]);
  $_SESSION['formVars']["birthday"] = quotesqldata($_SESSION['formVars']["birthday"]);
  $_SESSION['formVars']["age"] = quotesqldata($_SESSION['formVars']["age"]);
  $birth_month = (is_numeric($_SESSION['formVars']["birth_month"])) ? $_SESSION['formVars']["birth_month"] : "00";
  $birthday = (is_numeric($_SESSION['formVars']["birthday"])) ? $_SESSION['formVars']["birthday"] : "00";
  $age = (is_numeric($_SESSION['formVars']["age"])) ? $_SESSION['formVars']["age"] : "35"; // GUESTIMATE!!!
  $birth_year = (date('Y',time()) - $age); // this is an APPROXIMATION/GUESTIMATE!!!!
  $date_of_birth = $birth_year . "-" . $birth_month . "-" . $birthday;
  //'-00-00';

/*
echo 'formVars[birth_month]: ' . $_SESSION['formVars']["birth_month"] . '<br>';
echo 'formVars[birthday]: ' . $_SESSION['formVars']["birthday"] . '<br>';
echo 'formVars[age]: ' . $_SESSION['formVars']["age"] . '<br>';
echo 'birth_month: '.$birth_month . '<br>';
echo 'birthday: ' . $birthday  . '<br>';
echo 'age: ' . $age . '<br>';
echo 'birthyear: ' . $birth_year . '<br>';
exit;
*/

  // quote all user input!
  $_SESSION['formVars']["lastname"] = quotesqldata($_SESSION['formVars']["lastname"]);
  $_SESSION['formVars']["firstName"] = quotesqldata(  $_SESSION['formVars']["firstName"]);
  $_SESSION['formVars']["initial"]   = quotesqldata(  $_SESSION['formVars']["initial"]  );
  $_SESSION['formVars']["title"]   = quotesqldata(  $_SESSION['formVars']["title"]  );
  $_SESSION['formVars']["address1"]   = quotesqldata(  $_SESSION['formVars']["address1"]  );
  $_SESSION['formVars']["address2"]   = quotesqldata(  $_SESSION['formVars']["address2"]  );
  $_SESSION['formVars']["address3"]   = quotesqldata(  $_SESSION['formVars']["address3"]  );
  $_SESSION['formVars']["city"]   = quotesqldata(  $_SESSION['formVars']["city"]  );
  $_SESSION['formVars']["state"]   = quotesqldata(  $_SESSION['formVars']["state"]  );
  $_SESSION['formVars']["zipcode"]   = quotesqldata(  $_SESSION['formVars']["zipcode"]  );
  $_SESSION['formVars']["country"]   = quotesqldata(  $_SESSION['formVars']["country"]  );
  $_SESSION['formVars']["phonehome"]   = quotesqldata(  $_SESSION['formVars']["phonehome"]  );
  $_SESSION['formVars']["phonework"]   = quotesqldata(  $_SESSION['formVars']["phonework"]  );
  $_SESSION['formVars']["phonemobile"]   = quotesqldata(  $_SESSION['formVars']["phonemobile"]  );
  $_SESSION['formVars']["phonemisc"]   = quotesqldata(  $_SESSION['formVars']["phonemisc"]  );
  $_SESSION['formVars']["sex"]   = quotesqldata(  $_SESSION['formVars']["sex"]  );
  $_SESSION['formVars']["email"]   = quotesqldata(  $_SESSION['formVars']["email"]  );
  $_SESSION['formVars']["email2"]   = quotesqldata(  $_SESSION['formVars']["email2"]  );
  //$_SESSION['formVars']["emailalias"]   = quotesqldata(  $_SESSION['formVars']["emailalias"]  );
  $_SESSION['formVars']["emailalias"]   = "";
  $dob  = quotesqldata(  $date_of_birth );
  $_SESSION['formVars']["email_on_event_change"]   = quotesqldata(  $_SESSION['formVars']["email_on_event_change"]  );
  $_SESSION['formVars']["email_on_new_event"]   = quotesqldata(  $_SESSION['formVars']["email_on_new_event"]  );
  $_SESSION['formVars']["email_reminder"]   = quotesqldata(  $_SESSION['formVars']["email_reminder"]  );

  if ($debug) {
	LogMsg('$_SESSION["formVars"]["email_on_new_event"]: '
         . $_SESSION['formVars']["email_on_new_event"]);
	LogMsg('$_SESSION["formVars"]["email_on_event_change"]: '
         . $_SESSION['formVars']["email_on_event_change"]);
  }

  $_SESSION['formVars']["memberstatus"]   = isset($_SESSION['formVars']["memberstatus"]) ?
                                   quotesqldata(  $_SESSION['formVars']["memberstatus"]) : NULL;
  $_SESSION['formVars']["pay_method"]   = isset($_SESSION['formVars']["pay_method"]) ?
                                 quotesqldata($_SESSION['formVars']["pay_method"]) : NULL;

  $_SESSION['formVars']["comments"]   = isset($_SESSION['formVars']["comments"]) ?
                              quotesqldata(  $_SESSION['formVars']["comments"]) : NULL;

  $_SESSION['formVars']["disp_email"]   = quotesqldata(  $_SESSION['formVars']["disp_email"]  );
  $_SESSION['formVars']["disp_title"]   = quotesqldata(  $_SESSION['formVars']["disp_title"]  );
  $_SESSION['formVars']["disp_lastname"]   = quotesqldata(  $_SESSION['formVars']["disp_lastname"]  );
  $_SESSION['formVars']["disp_address"]   = quotesqldata(  $_SESSION['formVars']["disp_address"]  );
  $_SESSION['formVars']["disp_address2"]   = quotesqldata(  $_SESSION['formVars']["disp_address2"]  );
  $_SESSION['formVars']["disp_address3"]   = quotesqldata(  $_SESSION['formVars']["disp_address3"]  );
  $_SESSION['formVars']["disp_city"]   = quotesqldata(  $_SESSION['formVars']["disp_city"]  );
  $_SESSION['formVars']["disp_state"]   = quotesqldata(  $_SESSION['formVars']["disp_state"]  );
  $_SESSION['formVars']["disp_zipcode"]   = quotesqldata(  $_SESSION['formVars']["disp_zipcode"]  );
  $_SESSION['formVars']["disp_country"]   = quotesqldata(  $_SESSION['formVars']["disp_country"]  );
  $_SESSION['formVars']["disp_phonehome"]   = quotesqldata(  $_SESSION['formVars']["disp_phonehome"]  );
  $_SESSION['formVars']["disp_phonemobile"]   = quotesqldata(  $_SESSION['formVars']["disp_phonemobile"]  );
  $_SESSION['formVars']["disp_phonework"]   = quotesqldata(  $_SESSION['formVars']["disp_phonework"]  );
  $_SESSION['formVars']["disp_phonemisc"]   = quotesqldata(  $_SESSION['formVars']["disp_phonemisc"]  );
  $_SESSION['formVars']["disp_email2"]   = quotesqldata(  $_SESSION['formVars']["disp_email2"]  );
  $_SESSION['formVars']["disp_sex"]   = quotesqldata(  $_SESSION['formVars']["disp_sex"]  );
  $_SESSION['formVars']["disp_date_birth"]   = quotesqldata(  $_SESSION['formVars']["disp_date_birth"]  );
  $_SESSION['formVars']["disp_emailalias"]   = quotesqldata(  $_SESSION['formVars']["disp_emailalias"]  );
  $cust_id   = quotesqldata(  $cust_id  );


   $summary = quotesqldata($_SESSION['formVars']["summary"]);
   $occupation = quotesqldata($_SESSION['formVars']["occupation"]);
   $details = quotesqldata($_SESSION['formVars']["details"]);

  $profile_display = DispMemberProfile($_SESSION['formVars'],$cust_id);

  if (trim($_SESSION['formVars']["email_reminder"]) == "None") {
     $_SESSION['formVars']["email_reminder"] = 'NULL';
  }

  $query = "UPDATE members SET "
              . "m_lastname = "
              . "'" . $_SESSION['formVars']["lastname"] . "', "
              . "m_firstname = "
              . "'" . $_SESSION['formVars']["firstName"] . "', "
              . "m_initial = "
              . "'" . $_SESSION['formVars']["initial"] . "', "
              . "m_title = "
              . "'" . $_SESSION['formVars']["title"] . "', "
              . "m_address1 = "
              . "'" . $_SESSION['formVars']["address1"] . "', "
              . "m_address2 = "
              . "'" . $_SESSION['formVars']["address2"] . "', "
              . "m_address3 = "
              . "'" . $_SESSION['formVars']["address3"] . "', "
              . "m_city = "
              . "'" . $_SESSION['formVars']["city"] . "', "
              . "m_state = "
              . "'" . $_SESSION['formVars']["state"] . "', "
              . "m_zipcode = "
              . "'" . $_SESSION['formVars']["zipcode"] . "', "
              . "m_country = "
              . "'" . $_SESSION['formVars']["country"] . "', "
              . "m_phonehome = "
              . "'" . $_SESSION['formVars']["phonehome"] . "', "
              . "m_phonework = "
              . "'" . $_SESSION['formVars']["phonework"] . "', "
              . "m_phonemobile = "
              . "'" . $_SESSION['formVars']["phonemobile"] . "', "
              . "m_phonemisc = "
              . "'" . $_SESSION['formVars']["phonemisc"] . "', "
              . "m_sex = "
              . "'" . $_SESSION['formVars']["sex"] . "', "
              . "m_email = "
              . "'" . $_SESSION['formVars']["email"] . "', "
              . "m_email2 = "
              . "'" . $_SESSION['formVars']["email2"] . "', "
              . "m_emailalias = "
              . "'" . $_SESSION['formVars']["emailalias"] . "', "
              . "m_date_birth = "
              . "'" . $date_of_birth . "', "

              . "m_email_on_event_change = "
              . (int) $_SESSION['formVars']["email_on_event_change"] . ", "

              . "m_email_on_new_event = "
              . (int) $_SESSION['formVars']["email_on_new_event"] . ", "

              . "m_email_reminder = "
              . (int) $_SESSION['formVars']["email_reminder"] . ", ";

              //. "m_email_reminder = "
              //. "" . NULL . ", ";


if ($AdminLevel) {
     $query .=  "m_memberstatus = "
              . "'" . $_SESSION['formVars']["memberstatus"] . "', "

              . "m_pay_method = "
              . "'" . $_SESSION['formVars']["pay_method"] . "', "

              . "m_comments = "
              . "'" . $_SESSION['formVars']["comments"] . "', ";
}

	$date_expiration = "";
	if (isset($_SESSION['formVars']["date_expiration"]))
		$date_expiration = quotesqldata(MySqlDate($_SESSION['formVars']["date_expiration"]));

	$new_auth_level = "";
	if (isset( $_SESSION['formVars']["auth_level"]))
   $new_auth_level  = quotesqldata($_SESSION['formVars']["auth_level"]);

  $summary = quotesqldata($_SESSION['formVars']["summary"]);
  $occupation = quotesqldata($_SESSION['formVars']["occupation"]);
  $details = quotesqldata($_SESSION['formVars']["details"]);
  $query .=    "m_disp_email = "
              . "'" . (int) $_SESSION['formVars']["disp_email"] . "', "
              . "m_disp_title = "
              . "'" . (int) $_SESSION['formVars']["disp_title"] . "', "
              . "m_disp_lastname = "
              . "'" . (int) $_SESSION['formVars']["disp_lastname"] . "', "
              . "m_disp_address = "
              . "'" . (int) $_SESSION['formVars']["disp_address"] . "', "
              . "m_disp_address2 = "
              . "'" . (int) $_SESSION['formVars']["disp_address2"] . "', "
              . "m_disp_address3 = "
              . "'" . (int) $_SESSION['formVars']["disp_address3"] . "', "
              . "m_disp_city = "
              . "'" . (int) $_SESSION['formVars']["disp_city"] . "', "
              . "m_disp_state = "
              . "'" . (int) $_SESSION['formVars']["disp_state"] . "', "
              . "m_disp_zipcode = "
              . "'" . (int) $_SESSION['formVars']["disp_zipcode"] . "', "
              . "m_disp_country = "
              . "'" . (int) $_SESSION['formVars']["disp_country"] . "', "
              . "m_disp_phonehome = "
              . "'" . (int) $_SESSION['formVars']["disp_phonehome"] . "', "
              . "m_disp_phonemobile = "
              . "'" . (int) $_SESSION['formVars']["disp_phonemobile"] . "', "
              . "m_disp_phonework = "
              . "'" . (int) $_SESSION['formVars']["disp_phonework"] . "', "
              . "m_disp_phonemisc = "
              . "'" . (int) $_SESSION['formVars']["disp_phonemisc"] . "', "
              . "m_disp_email2 = "
              . "'" . (int) $_SESSION['formVars']["disp_email2"] . "', "
              . "m_disp_sex = "
              . "'" . (int) $_SESSION['formVars']["disp_sex"] . "', "
              . "m_disp_date_birth = "
              . "'" . (int) $_SESSION['formVars']["disp_date_birth"] . "', "
              . "m_disp_emailalias = "
              . "'" . (int) $_SESSION['formVars']["disp_emailalias"] . "', "
              . "m_profile_display = "
              . "'" . (int) $profile_display . "', "
              . "p_summary= "
			  . "'" . $summary . "',"
			  . "p_details="
			  . "'" . $details . "',"
			  . "p_occupation="
			  . "'" . $occupation . "'";

	if ($AdminLevel)
	{
		$query .=
   		",u_date_expiration = "
		."'" . $date_expiration . "'"
		.",u_auth_level ="
		."'" . $new_auth_level . "'";
	}

	$query .= " WHERE cust_id = '" . $cust_id  . "'";

  if ($debug) {
    LogMsg("Query: " . $query . "<br><br>");
		LogMsg("email_on_new_event: "
         . $_SESSION['formVars']["email_on_new_event"] . "<br><br>");
		LogMsg("email_on_event_change: "
         . $_SESSION['formVars']["email_on_event_change"] . "<br><br>");
    //exit;
  }

  // Run the query on the members table
  if (!(@ mysqli_query ($connection, $query)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);

  // Clear the formVars so a future <form> is blank
  SessionUnregister("formVars");
  SessionUnregister("errors");

  // Now show the index page
  //if ($AdminLevel)
    //$loc = "Location: /admin/mlist.php";
  //else

  $loc = "Location: /members/mlist.php";
  header($loc);
  exit;
