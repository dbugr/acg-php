<?php
  // This script validates customer data entered into
  // join.php
  // If validation succeeds, it INSERTs or UPDATEs
  // a customer and redirect to the credit card form page; if it
  // fails, it creates error messages and redirects
  // back to join.php


require ('always.include.php');
//$debug = true;
//$debug = false;
//session_start();
//require ('include.php');

//if ($debug) {
	LogMsg('join-post.php Validating...'."<br>");
//}
  ////// functions
  // Get Next member id from database

$EmailNoticesTo		= GetParameter('EmailNoticesTo');
$EmailNoticesFrom	= GetParameter('EmailNoticesFrom');
$ClubCode			= GetParameter('ClubCode');


// go ahead and connect to the database
mysqlconnect($connection);
LogMsg('mysqlconnect succeeded... ');

function GetNextMemberIdFromDatabase ()
{
	global $connection;
	global $debug;
	$ClubCode			= GetParameter('ClubCode');



	//echo "Connecting to database...<br>";
	// go ahead and connect to the database
	mysqlconnect($connection);

  	$ClubCodeLength = strlen( $ClubCode);
    $query = "SELECT MAX(substring(cust_id, " . $ClubCodeLength
				." + 2 "
				.", "
				."14 "
				.") + 0 ) "
	            ."AS cust_id "
				."FROM members "
				."WHERE (cust_id LIKE '" . $ClubCode . "%');"
				;
    if (!($result = @ mysqli_query($connection,$query)))
    {
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
    	exit;
  	}
  	$row = mysqli_fetch_array($result);
  	$cust_id = $row['cust_id'];
  	$cust_id += 1;
    $cust_id = $ClubCode . "_" . $cust_id;
	
	//if ($debug) {
		LogMsg("join-post.php NextMemberId: " . $cust_id);
	//}
    return $cust_id;
} // end function GetNextMemberIdFromDatabase



	// Clear any errors that might have been
	// found previously
	$errors = array();

	$formVars = array();
	foreach($_POST as $varname => $value)
		$formVars[$varname] = clean($value);
	$s = print_r($formVars,true);
	LogMsg("formVars dump:");
	LogMsg("$s");

  	if ((array_key_exists('discountcode',$formVars)) && (strtolower($formVars['discountcode']) == "fna")) {
  		$ForceNewAccount = true;
	} else {
  		$ForceNewAccount = false;
  	}

    LogMsg("Obtained post vars...<br>");

  // Validate the firstName
  if ((!array_key_exists('firstName',$formVars)) || empty($formVars["firstName"]))
  	$errors["firstName"] = "The first name field cannot be blank.";
  else if (strlen($formVars["firstName"]) > 50)
    $errors["firstName"] = "The first name must be less than 50 characters";

  // Validate the Surname
  if ((!array_key_exists('surname',$formVars)) || empty($formVars["surname"]))
  	$errors["surname"] = "The last name cannot be blank.";
  else if (strlen($formVars["surname"]) > 50)
    $errors["surname"] = "The Last name must be less than 50 characters";

	  // Validate the user's Initial
  // If there is a middle initial, it must be one character in length
  if (!empty($formVars["initial"]) && !preg_match("^[a-z]{1}$", $formVars["initial"]))
  	$errors["initial"] = "The initial field must be empty or one character in length.";

  LogMsg('name validated... ');

  // Validate the Address
  if (empty($formVars["address1"]) &&
      empty($formVars["address2"]) &&
      empty($formVars["address3"]))
  {
    // all the fields of the address cannot be null
  	$errors["address"] = "You must supply at least one address line.";
	}
  else
  {
  	if (strlen($formVars["address1"]) > 50)
    	$errors["address1"] = "The address line 1 can be no longer than 50 characters";
    if (strlen($formVars["address2"]) > 50)
      $errors["address2"] = "The address line 2 can be no longer than 50 characters";
    if (strlen($formVars["address3"]) > 50)
      $errors["address3"] = "The address line 3 can be no longer than 50 characters";
  }

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

  LogMsg('start zip code validation... ');

  // Validate Zipcode
  if (empty($formVars["zipcode"]))
 		$errors["zipcode"] = "You must supply a zip code.";
  else if (!preg_match("/^([0-9]{4,5}[- ]?[0-9]*)$/", $formVars["zipcode"]))
    $errors["zipcode"] = "The zipcode must be 4 or 5 digits in length. " .
         "zip+4 is optional";

  LogMsg('finished zip code validation... ');

  // Validate Country
  if ((array_key_exists('country',$formVars)) && (strlen($formVars["country"]) > 20))
    $errors["country"] = "The country must be less than 21 characters";

  LogMsg('address validated... ');

	// Validate Home Phone
	if ( array_key_exists('phone', $formVars) && ( strlen($formVars["phone"]) > 15)  ) {
		$errors["phone"] = "Home phone number must be less than 15 characters";
	}
	
	// Validate Mobile Phone
  if ((!array_key_exists('phonemobile', $formVars)) && empty($formVars["phonemobile"])) {
    $errors["phonemobile"] = "You must enter a contact mobile phone number";
  } else if (strlen($formVars["phonemobile"]) > 15) {
		$errors["phonemobile"] = "Mobile phone number must be less than 15 characters";
	}
	
  // Validate sex field
  if ((!array_key_exists('sex',$formVars)) || empty($formVars["sex"]))
    $errors["sex"] = "You must indicate your sex";

  // validate email address field
  //$validEmailAddr = validEmail($email);

  // the user's email cannot be a null string
  if ((!array_key_exists('email',$formVars)) || empty($formVars["email"]))
  	$errors["email"] = "You must supply an email address.";
  else if (strlen($formVars["email"]) > 50)
    $errors["email"] = "The email address can be no longer than 50 characters.";
  //else if (!validEmailAddr)
    //$errors["email"] = "You must supply a valid email address.";

  LogMsg('phone, sex, email validated... ');

  // validate referral field
  if ((!array_key_exists('referral',$formVars)) || empty($formVars["referral"]))
  	$errors["referral"] = "You must supply a referral.";

  // Validate username - must be non-empty
  LogMsg("Validating username...");
  if ((!array_key_exists('username',$formVars)) || empty($formVars["username"]))
  	$errors["username"] = "You must supply a username";
  else
  {
    // Check if the username is already in use
    $query = "SELECT * FROM members
              WHERE user_name = '" . $formVars["username"] . "'";

    if (!($result = @ mysqli_query($connection,$query)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),E_USER_ERROR);

    // Is it taken?
    if (mysqli_num_rows($result) == 1)
    	$errors["username"] = "A customer already exists with this login name.";
  }

  // Validate password - between 6 and 20 characters
  if (((!array_key_exists('password',$formVars))) ||
	((strlen($formVars["password"]) < 6) || (strlen($formVars["password"]) > 20)))
  	$errors["password"] = "The password must be between 6 and 20 characters in length";

  // Validate discount code field
//  echo "validating discount code field...";
/*
  $formVars['recur_start'] = NumDaysFreeMembership();
  if ((array_key_exists('discountcode',$formVars)) && !empty($formVars["discountcode"]))
  {
    $query = "SELECT discount_code FROM ac_discountcode
              WHERE discount_code = '" .
              $formVars["discountcode"] . "'";

		if (!($result = @ mysqli_query($connection,$query)))
    	trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);

    // Is the discount code valid?
    if ((mysqli_num_rows($result) != 1) and (!$ForceNewAccount) ) {
      $errors["discountcode"] = "You supplied an invalid discount code!";
    } else
      $formVars['recur_start'] = NumDiscountDaysFreeMembership() +
                   NumDaysFreeMembership();

  }
*/

  // Validate agreement field
  if (empty($formVars["agreement"]) || ($formVars["agreement"] != "agree"))
  	$errors["agreement"] = "To become a member, you must agree to the policies and terms";

//  echo "Validating credit card fields...";

  // Validate Credit Card fields
	//if ((!$ForceNewAccount)) {
	//  ValidateCCFields($ccv = false);
	//}


//================================================
function LogQuery($query) {
	global $formVars;
	global $errors;
	global $ccReturnVars;
	
	// blank out credit card number
	$formVarsCopy = $formVars;
	$formVarsCopy['cc_num'] = 'xxxx-xxxx-xxxx-xxxx';
	
	// LOG THIS EVENT!
	$FormVarsString =
	date('m/d/Y H:i:s',time())
	. " "
	. '$formVars: '
	. print_r($formVarsCopy,true)
	. "\n"
	;

	$ErrorString = 
	date('m/d/Y H:i:s',time())
	. " "
	. '$errors: '
	. print_r($errors,true)
	. "\n"
	;

	//$ccReturnVarsString = 
	//date('m/d/Y H:i:s',time())
	//. " "
	//. '$ccReturnVars: '
	//. print_r($ccReturnVars,true)
	//. "\n"
	//;
		
	$fh = fopen($_SERVER['DOCUMENT_ROOT'] . '/..' . '/var/advclub.log','a');
	//fseek($fh,0,SEEK_END);
	fwrite($fh,$FormVarsString);
	fwrite($fh,$ErrorString);
	//fwrite($fh,$ccReturnVarsString);
	fflush($fh);
	fclose($fh);
}


// Now the script has finished the validation,
// check if there were any errors
if (count($errors) > 0)  {
	// There are errors. log this event
	//if ($debug) {
		LogMsg("join-post.php Form validation errors detected.");
		LogQuery($errors);
	//}

	// save errors and formVars 
	// to session variable for use by join.php
	$_SESSION['errors'] = $errors;
	$_SESSION['formVars'] = $formVars;

	//Relocate back to the join form
	//echo 'Relocate!!!...'."<br>";
	$loc = "Location: " . JoinURL()
	.'?invalid=true';
	header($loc);
	exit;
}


	//if ($debug) {
		LogMsg("join-post.php Successfully validated all form fields, no errors...");
	//}

  //if (!isset($formVars['cc_billaddr1']))
  //	$formVars['cc_billaddr1'] = $formVars['address1'];
  //if (!isset($formVars['cc_billcity']))
  //	$formVars['cc_billcity'] = $formVars['city'];
  //if (!isset($formVars['cc_billstate']))
  //	$formVars['cc_billstate'] = $formVars['state'];
  //if (!isset($formVars['cc_billzip']))
  //	$formVars['cc_billzip'] = $formVars['zipcode'];

// AVS processing
//	if (!$laptop)
//	if (!ProcessAVS())
//	{
//			$errors["cc_num"] = "Neither billing address nor Zip matches this credit card.";
//      header("Location: " . JoinURL() );
//      exit;
//	}


// notify admin staff that someone tried to join the club!
//  $formVars['cust_id'] = $cust_id;
//  if (empty($formVars["cc_billaddr1"]))
//	  $formVars['cc_billaddr1'] = $formVars['address1'];
//  if (empty($formVars["cc_billcity"]))
//  	$formVars['cc_billcity'] = $formVars['city'];
//  if (empty($formVars["cc_billstate"]))
//  	$formVars['cc_billstate'] = $formVars['state'];
//  if (empty($formVars["cc_billzip"]))
//  	$formVars['cc_billzip'] = $formVars['zipcode'];
//  if (!$laptop)
// 		SimpleMailCCInformation( $ClubCode . " AC Prospect", "");

	$cust_id = GetNextMemberIdFromDatabase ();

	//$formVars['cost'] = GetParameter('MembershipFee');
	//$formVars['chrg_amount'] = "1.00";

	//if (empty($formVars['cc_billaddr1']))
	//	$formVars['cc_billaddr1'] = $formVars['address1'];
	//if (empty($formVars['cc_billcity']))
	//	$formVars['cc_billcity'] = $formVars['city'];
	//if (empty($formVars['cc_billstate']))
	//	$formVars['cc_billstate'] = $formVars['state'];
	//if (empty($formVars['cc_billzip']))
	//	$formVars['cc_billzip'] = $formVars['zipcode'];

	// send cc info to cc gateway, process any returned errors
	//if ((!$laptop) & (!$ForceNewAccount)) {
		//if ($ClubCode == 'cfac') {
		//	ProcessCC();
		//}
		//else if ($ClubCode == 'gnv') {
			//ProcessCCMerchantPartnersGateway($join = true,$authonly=true);
		//}
	   	//else
	   	//	$errors['ClubCode'] = 'Unknown Club Code!';
	//}
  	//if (count($errors) > 0)
	//{
		// There are errors. log this event
		//LogQuery($query);
		//$email_subject = "AC Join CC DECLINED:  " . $formVars['firstName']
		//." ".$formVars['surname'];

		//GnvSimpleMailCCInformation ( $ClubCode, "", $email_subject, $laptop);

		// save $errors array to $_SESSION variable for use by join.php
		//$_SESSION['formVars'] = $formVars;
		//$_SESSION['errors'] = $errors;
		//$loc = "Location: " . JoinURL()
		//.'?invalid=true';
		//header($loc);
		//exit;
	//}

	//echo "Prep data for insert query...<br>";

	// set initial date of birth to thirty years old
	// need this for member age statistics!
	$birth_month = ($formVars["birth_month"] != "Unknown") ? $formVars["birth_month"] : "01";
	$birthday = ($formVars["birthday"] != "Unknown") ? $formVars["birthday"] : "01";
	$age = ($formVars["age"] != "Unknown") ? $formVars["age"] : "30";
	$age = $age + 2;
	$birth_year = (date('Y',time()) - $age); // this is APPROXIMATE!!!!
	$date_of_birth = $birth_year . "-" . $birth_month . "-" . $birthday;
	//'-00-00';


	// Use the first two characters of the
	// USERNAME as a salt for the password
	$formVars["username"] = strtolower( $formVars["username"]);
	$salt = substr($formVars["username"], 0, 2);

	// Create the encrypted password
	$stored_password = crypt($formVars["password"], $salt);

	// get next customer id
	$cust_id = GetNextMemberIdFromDatabase ();
	$formVars["cust_id"] = $cust_id;
	if ($formVars["sex"] == "male")
		$title = "Mr";
	else if ($formVars["sex"] == "female")
		$title = "Ms";
	else
	    $title = "";

	if ($ForceNewAccount) {
		// set member expiration date for one month in the future
		$days_in_future = 30;
  		$MemberStatus = "NotPaid";
  	} else {
		// set member expiration date for one year in the future
		$days_in_future = 30;
  		$MemberStatus = "NotPaid";
  	}
	$u_date_expiration = time() + ($days_in_future * 24 * 3600);
	$u_date_expiration = date('Y-m-d', $u_date_expiration);

	$u_date_last_login = date('Y-m-d',strtotime('1970-01-01 01:01:01') );
	//$u_date_last_login = date('Y-m-d','1970-01-01 01:01:01');

  	$query = "INSERT INTO members (" .
              "cust_id, " .
              "m_lastname, " .
              "m_firstname, " .
              "m_title, " .
              "m_address1, " .
              "m_address2, " .
              "m_address3, " .
              "m_city, " .
              "m_state, " .
              "m_zipcode, " .
              "m_country, " .
              "m_phonehome, " .
              "m_phonemobile, " .
              "m_sex, " .
              "m_email, " .
              "m_referral, " .
              "m_referral_detail, " .
              "m_discount_code, " .
              "m_date_birth, " .
              "m_date_joined, " .
              "m_memberstatus, " .
              "password,user_name, " .
			  "u_auth_level, " .
			  "u_date_expiration," .
			  "u_date_last_login," .
			  "m_profile_display," .
			  "m_disp_email," .
			  "m_disp_phonemobile," .
              "m_club" .
              ") " .
              " VALUES (" .
	      			"'" . $cust_id . "', " .
              "\"" . $formVars["surname"] . "\", " .
              "\"" . $formVars["firstName"] . "\", " .
              "\"" . $title . "\", " .
              "\"" . $formVars["address1"] . "\", " .
              "\"" . $formVars["address2"] . "\", " .
              "\"" . $formVars["address3"] . "\", " .
              "\"" . $formVars["city"] . "\", " .
              "\"" . $formVars["state"] . "\", " .
              "\"" . $formVars["zipcode"] . "\", " .
              "\"" . $formVars["country"] . "\", " .
              "\"" . $formVars["phone"] . "\", " .
              "\"" . $formVars["phonemobile"] . "\", " .
              "\"" . $formVars["sex"] . "\", " .
              "\"" . $formVars["email"] . "\", " .
              "\"" . $formVars["referral"] . "\", " .
              "\"" . $formVars["referral_detail"] . "\", " .
              "\"" . "" . "\", " . // discountcode
              "\"" . $date_of_birth . "\", " .
              "'" . date('Y-m-d H:i:s', time()) . "', " .
              //"\"Paid \", " .
              "\"" . $MemberStatus . "\", " .
              "'" . $stored_password . "', " .
              "'" . $formVars["username"] . "', " .
              "'Member'," .
			  "'" . $u_date_expiration . "'," .
			  "'" . $u_date_last_login . "'," .

              "'1'," .  // display member profile
              "'1'," .  // display email address
              "'1'," .  // display mobile phone number

              "'" . $ClubCode . "'" .
              ")";

	//if ($debug) {
		LogMsg("join-post.php query: " . $query);
	//}

	LogQuery($query);

	// Run the query on the customer table
	if (!(@ mysqli_query($connection,$query)))	{
		trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
	}

	// notify admin staff that someone joined the club!
	if ($ForceNewAccount) {
  		$email_subject = "AC Join Force New Account:  " . $formVars['firstName']." ". $formVars['surname'];
  	} else {
  		$email_subject = "AC Join New Account:  " . $formVars['firstName']." ". $formVars['surname'];
  	}

    //GnvSimpleMailCCInformation ( $ClubCode, "", $email_subject, $laptop);
	LogMsg('ACG NEW MEMBER: '.$email_subject);

	$email_headers = 	GetParameter('EmailHeaders');
	mailwrapper($EmailNoticesTo,$email_subject,
		"NOTICE! New ACG account created for: "
		."Name: ".$formVars['firstName']." ".$formVars['surname']."\n"
		."EMail: ".$formVars['email']."\n"
		."Phone: ".$formVars['phone']."\n"
		."Mobile Phone: ".$formVars['phonemobile']."\n"
		."The new members account will expire in 30 days. ". "\n"
		."Please adjust the account expiration date."
		,
		$email_headers);
	  
	// now email new member with welcome message
	$email_subject = "Welcome to the Adventure Club!";
	$email_body =
	$formVars['firstName']." ".$formVars['surname'].",\n"
	."\n"
	."Welcome to the Adventure Club group!\n"
	."\n"
	."http://www.adventureclub.info/\n"
	."\n"
	."Do you have any special interests? Events you would like \n"
	."to attend, such as rock climbing, bicycling, kayaking, \n"
	."dancing? The general rule of thumb is: if three people send \n"
	."contact us stating that they want something, we will \n"
	."do our best to make it happen.\n"
	."\n"
	."Click here to use our contact form:"."\n"
	."http://www.adventureclub.info/contact.php\n"
	."\n"
	."Hope to see you at a club event soon!\n"
	."Adventure Club Management\n"
	."Nancy Henry\n"
	."\n"
	."Adventure Club of Gainesville\n"
	."http://www.adventureclub.info\n"
	;

	// welcome the new member!
	$memberEmailAddr = $formVars['email'];
	mailwrapper($memberEmailAddr,$email_subject,$email_body,
	$email_headers);


$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Join-post ' . $ClubCompanyName;
$join = true;
//echo "Join: " . $join . "<br>";

	// reset data
	$formVars = array();
  $errors = array();
	SessionUnregister('errors');
	SessionUnregister('formVars');
	
	// redirect to the paypal.php script
	$loc = "Location: " . GetParameter('vd') . "paypal.php";
	//LogMsg('Redirecting to Paypal page: $loc: ',$loc);
	header($loc);
