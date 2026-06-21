<?php
// help user edit his/her personal data

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$errors = array();
if (array_key_exists('message', $_SESSION)) {
   $message = $_SESSION['message'];
}
if (array_key_exists('errors', $_SESSION)) {
   $errors = $_SESSION['errors'];
} else {
   $errors = array();
}
if (array_key_exists('photoerrors', $_SESSION)) {
   $photoerrors = $_SESSION['photoerrors'];
} else {
   $photoerrors = array();
}

// Show an error in a red font
function fieldError($fieldName, $errors)
{
   if (isset($errors[$fieldName]))
      echo "<font color=\"red\">" .
         $errors[$fieldName] .
         "</font><br>";
}

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing

if ((!SessionIsRegistered("loginUsername"))) {
   // Register a message to show the user
   $message = "Error: you are not logged in! ";
   SessionRegister("message", $message);

   //trigger_error("Error user not logged in (medit)!", E_USER_ERROR);

   // Register where they came from
   $referer = $_SERVER['PHP_SELF'];
   SessionRegister("referer", $referer);

   // redirect to the login page
   header("Location: /login.php");
   exit;
}

// does user have sufficient privilages to edit another users data?
$cust_id = !empty($_GET['cust_id']) ? clean($_GET['cust_id']) : NULL;
$loginUsername = LoginUsername();
$AuthLevel = $MemberInfo->GetMemberAuthLevel();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
$cid = getCustomerID($loginUsername);
if ((!$AdminLevel) || (empty($cust_id))) {
   // member cannot edit another members data without admin privilages!
   $cust_id = $cid;
}

if (isset($errors) && count($errors) > 0) {
   // obtain form data from $_SESSION variable
   $formVars = $_SESSION['formVars'];
} else if (!isset($errors) || empty($errors)) {
   // obtain form data from database
   mysqlconnect($connection);

   $query = "SELECT * FROM members
			   WHERE (cust_id='" . $cust_id . "');";

   if (!($result = @mysqli_query($connection, $query)))
      trigger_error(
         "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
         E_USER_ERROR
      );

   $row = mysqli_fetch_array($result);

   // Reset $formVars, since we're loading from
   // the customer table
   $formVars = array();

   // Reset the errors
   $errors = array();

   // Load all the form variables with customer data
   $formVars["cust_id"]        = $cust_id;
   $formVars["title"]          = $row["m_title"];
   $formVars["lastname"]       = $row["m_lastname"];
   $formVars["firstName"]      = $row["m_firstname"];
   $formVars["initial"]        = $row["m_initial"];
   $formVars["address1"]       = $row["m_address1"];
   $formVars["address2"]       = $row["m_address2"];
   $formVars["address3"]       = $row["m_address3"];
   $formVars["city"]           = $row["m_city"];
   $formVars["state"]          = $row["m_state"];
   $formVars["zipcode"]        = $row["m_zipcode"];
   $formVars["country"]        = $row["m_country"];
   $formVars["phonemobile"]    = $row["m_phonemobile"];
   $formVars["phonehome"]      = $row["m_phonehome"];
   $formVars["phonework"]      = $row["m_phonework"];
   $formVars["phonemisc"]      = $row["m_phonemisc"];
   $formVars["fax"]            = $row["m_fax"];
   $formVars["email"]          = $row["m_email"];
   $formVars["email2"]         = $row["m_email2"];
   $formVars["referral"]       = $row["m_referral"];
   $formVars["referral_detail"] = $row["m_referral_detail"];
   $formVars["date_joined"]    = $row["m_date_joined"];
   $formVars["sex"]            = $row["m_sex"];
   $formVars["memberstatus"]   = $row["m_memberstatus"];
   $formVars["pay_method"]     = $row["m_pay_method"];
   $formVars["comments"]       = $row["m_comments"];
   $formVars["emailalias"]     = $row["m_emailalias"];

   $formVars["dob"]            = HumanDates($row["m_date_birth"]);
   $formVars["birth_month"]    = substr($row["m_date_birth"], 5, 2);
   $formVars["birthday"]       = substr($row["m_date_birth"], 8, 2);
   $formVars["birth_year"]     = substr($row["m_date_birth"], 0, 4);

   $formVars["date_joined"]    = HumanDates($row["m_date_joined"]);

   // setup checkboxes
   $formVars["email_on_event_change"] = $row["m_email_on_event_change"];
   $formVars["email_on_new_event"] = $row["m_email_on_new_event"];
   $formVars["email_reminder"] = $row["m_email_reminder"];

   $formVars["disp_email"]     = $row["m_disp_email"];
   $formVars["disp_title"]     = $row["m_disp_title"];
   $formVars["disp_lastname"]  = $row["m_disp_lastname"];
   $formVars["disp_address"]   = $row["m_disp_address"];
   $formVars["disp_address2"]  = $row["m_disp_address2"];
   $formVars["disp_address3"]  = $row["m_disp_address3"];
   $formVars["disp_city"]      = $row["m_disp_city"];
   $formVars["disp_state"]     = $row["m_disp_state"];
   $formVars["disp_zipcode"]   = $row["m_disp_zipcode"];
   $formVars["disp_country"]   = $row["m_disp_country"];
   $formVars["disp_phonemobile"] = $row["m_disp_phonemobile"];
   $formVars["disp_phonehome"] = $row["m_disp_phonehome"];
   $formVars["disp_phonework"] = $row["m_disp_phonework"];
   $formVars["disp_phonemisc"] = $row["m_disp_phonemisc"];
   $formVars["disp_email2"]    = $row["m_disp_email2"];
   $formVars["disp_sex"]       = $row["m_disp_sex"];
   $formVars["disp_emailalias"] = $row["m_disp_emailalias"];
   $formVars["disp_date_birth"] = $row["m_disp_date_birth"];


   // load username and password data
   $formVars["username"]       = $row["user_name"];
   $formVars["password"]       = $row["password"];
   $formVars["passwordOrig"]   = $row["password"];
   $formVars["auth_level"]     = $row["u_auth_level"];
   $formVars["date_expiration"] = HumanDates($row["u_date_expiration"]);
   $formVars["date_last_login"]     = $row["u_date_last_login"];

   // load summary and details
   $formVars["summary"]       = $row["p_summary"];
   $formVars["occupation"]    = $row["p_occupation"];
   $formVars["details"]       = $row["p_details"];
}

// put check mark in check box?
$disp = array();
foreach ($formVars as $fieldname => $fieldvalue)
   if ((substr($fieldname, 0, 4) == 'disp') and
      ($fieldvalue == "1")
   )
      $disp[$fieldname] = " checked";
   else
      $disp[$fieldname] = "";

$disp["email_on_new_event"]  = ($formVars["email_on_new_event"] == "1")
   ? " checked " : "";
$disp["email_on_event_change"]  = ($formVars["email_on_event_change"] == "1")
   ? " checked " : "";

$script_url = "/members/medit-photo-post.php";


$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName      = GetParameter('ClubCompanyName');
$WebPageTitle = 'My Profile ' . $ClubCompanyName;
if ($AdminLevel) {
   $admin = true;
}
require('top.php');

?>


<div id="centercontent">
   <h3>Update Your Profile</h3>
   <?php
   if ((count($errors) > 0) || count($photoerrors) > 0) {
      echo "<h2><font color=\"red\">Errors Occurred. Please scroll down.</font></h2>";
      //print_r($errors);
      //exit;
   }
   if (isset($errors['main'])) {
      echo "<h2><font color=\"red\">" . $errors["main"] . "</font></h2>";
      //print_r($errors);
      //exit;
   }
   ?>

   <p>Share a little bit about yourself with other members, and manage your account right here. Items in <font color="red">red</font> are required.
      Please provide your cell phone number so that event leaders can contact you about an event if necessary. Information with a checkmark in the box will be shown only to other members. All other information is kept strictly confidential for your privacy.</p>

   <table>
      <tr>
         <td colspan=3>
            <hr align='left' color='#000000' SIZE='1' noshade>
         </td>
      </tr>

      <tr>
         <td>Password:</td>
         <td></td>
         <td>
            <a href="/members/passwdchng.php?cust_id=<?php echo $cust_id; ?>" name="passwdchng">Change Login Password</a>
      </tr>

      <tr>
         <td>Survey:</td>
         <td></td>
         <td>Tell us your <a href="/members/survey.php">preferences</a> for new events.</td>
      </tr>


      <tr>
         <td colspan=3>
            <hr align='left' color='#000000' SIZE='1' noshade>
         </td>
      </tr>

      <tr>
         <td colspan=3>
            <p><b>Upload Your Photo</b></p>
         </td>
      </tr>
      <tr>
         <td nowrap>Photo Type:</td>
         <td></td>
         <td>We accept only jpg, png, gif photo image files.</td>

      <tr>
         <td nowrap>Photo Size:</td>
         <td></td>
         <td>Photo file size must be less than 9 megabytes (9000000 bytes).</td>
      </tr>
      <tr>
         <td nowrap valign="bottom">Profile Photo:</td>
         <td></td>
         <td nowrap valign="bottom"><? echo fieldError("photo", $photoerrors); ?>
            <form enctype="multipart/form-data" action="/members/medit-photo-post.php" method="POST" name="photoform">
               <input type="hidden" name="MAX_FILE_SIZE" value="9000000">
               <input type="hidden" name="cust_id" value="<? echo $cust_id; ?>">
               File Name: <input name="toProcess" type=file>
               <input type=submit value="Upload">
            </form>
         </td>
      </tr>

      <tr>
         <td nowrap>Photo Info:</td>
         <td></td>
         <td><? echo isset($message) ? $message : NULL; ?></td>
      </tr>

      <tr>
         <td colspan=3>
            <hr align='left' color='#000000' SIZE='1' noshade>
         </td>
      </tr>

   </table>

   <form method="POST" action="/members/medit-post.php" name="memberdataform">
      <input type="hidden" name="cust_id" value="<?php echo $cust_id ?>">
      <table>

         <tr>
            <td nowrap>
               <font color="red">Email Reminder:</font>
            </td>
            <td></td>
            <td>Email me a reminder
               <select name="email_reminder">
                  <option <? if ($formVars["email_reminder"] == "None") echo "selected"; ?>>
                     None
                  <option <? if ($formVars["email_reminder"] == "0") echo "selected"; ?>>
                     0
                  <option <? if ($formVars["email_reminder"] == "1") echo "selected"; ?>>
                     1
                  <option <? if ($formVars["email_reminder"] == "2") echo "selected"; ?>>
                     2
                  <option <? if ($formVars["email_reminder"] == "3") echo "selected"; ?>>
                     3
                  <option <? if ($formVars["email_reminder"] == "4") echo "selected"; ?>>
                     4
                  <option <? if ($formVars["email_reminder"] == "5") echo "selected"; ?>>
                     5
                  <option <? if ($formVars["email_reminder"] == "6") echo "selected"; ?>>
                     6
                  <option <? if ($formVars["email_reminder"] == "7") echo "selected"; ?>>
                     7
               </select>
               days in advance of events I have signed up for.</td>
         </tr>

         <tr>
            <td nowrap>
               <font color="red">Email on New Event:</font>
            </td>
            <td><input type="checkbox" name="email_on_new_event" value="1" <?php echo $disp['email_on_new_event']; ?>></td>
            <td>Send me an email when a new event is posted to the website</td>
         </tr>

         <tr>
            <td nowrap>
               <font color="red">Email Event Changes:</font>
            </td>
            <td><input type="checkbox" name="email_on_event_change" value="1" <?php echo $disp['email_on_event_change']; ?>></td>
            <td>Email me if an event is changed</td>
         </tr>


         <tr>
            <td><a name="AdminStart"></a>Title:</td>
            <td></td>
            <td><select name="title">
                  <option <? if ($formVars["title"] == "Mr") echo "selected"; ?>>Mr
                  <option <? if ($formVars["title"] == "Ms") echo "selected"; ?>>Ms
                  <option <? if ($formVars["title"] == "Mrs") echo "selected"; ?>>Mrs
                  <option <? if ($formVars["title"] == "Dr") echo "selected"; ?>>Dr
               </select><br></td>
         </tr>

         <tr>
            <td>
               <font color="red">First name:</font>
            </td>
            <td></td>
            <td><? echo fieldError("firstName", $errors); ?>
               <input type="text" name="firstName" value="<? echo $formVars["firstName"]; ?>" size=50></td>
         </tr>

         <tr>
            <td>
               <font color="red">Lastname:</font>
            </td>
            <td><input type="hidden" name="disp_lastname" value="1"></td>
            <td><? echo fieldError("lastname", $errors); ?>
               <input type="text" name="lastname" value="<? echo $formVars["lastname"]; ?>" size=50></td>
         </tr>

         <tr>
            <td>Initial: </td>
            <td></td>
            <td><? echo fieldError("initial", $errors); ?>
               <input type="text" name="initial" value="<? echo $formVars["initial"]; ?>" size=2></td>
         </tr>

         <tr>
            <td colspan=3>
               <hr align='left' color='#000000' SIZE='1' noshade>
            </td>
         </tr>

         <?php
         $AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
         if ($AdminLevel) {
            //echo $AuthLevel;
         ?>
            <tr>
               <td>NOTICE</td>
               <td>&nbsp;</td>
               <td>ADMINISTRATION SETTINGS</td>
            </tr>

            <tr>
               <td>
                  <font color="red">Member Status: </font>
               </td>
               <td></td>
               <td><? echo fieldError("status", $errors); ?>
                  <select name="memberstatus">
                     <option <? if ($formVars["memberstatus"] == "Paid") echo "selected"; ?>>
                        Paid
                     <option <? if ($formVars["memberstatus"] == "Declined") echo "selected"; ?>>
                        Declined
                     <option <? if ($formVars["memberstatus"] == "Expired") echo "selected"; ?>>
                        Expired
                     <option <? if ($formVars["memberstatus"] == "Free") echo "selected"; ?>>
                        Free
                     <option <? if ($formVars["memberstatus"] == "Canceled") echo "selected"; ?>>
                        Canceled
                     <option <? if ($formVars["memberstatus"] == "NotPaid") echo "selected"; ?>>
                        NotPaid
                  </select></td>
            </tr>

            <tr>
               <td>
                  <font color="red">Payment Method: </font>
               </td>
               <td></td>
               <td><? echo fieldError("pay_method", $errors); ?>
                  <select name="pay_method">
                     <option <? if ($formVars["pay_method"] == "CreditCard") echo "selected"; ?>>
                        CreditCard
                     <option <? if ($formVars["pay_method"] == "Check") echo "selected"; ?>>
                        Check
                  </select></td>
            </tr>

            <tr>
               <td>
                  <font color="red">Auth Level: </font>
               </td>
               <td></td>
               <td><? echo fieldError("status", $errors); ?>
                  <select name="auth_level">
                     <option <? if ($formVars["auth_level"] == "Member") echo "selected"; ?>>
                        Member
                     <option <? if ($formVars["auth_level"] == "Leader") echo "selected"; ?>>
                        Leader
                     <option <? if ($formVars["auth_level"] == "Vendor") echo "selected"; ?>>
                        Vendor
                     <option <? if ($formVars["auth_level"] == "Admin") echo "selected"; ?>>
                        Admin

                        <?php
                        if ($MemberInfo->GetMemberAuthLevelRaw() == 'Maint') {
                           echo "<option ";
                           if ($formVars["auth_level"] == "Maint") echo "selected";
                           echo ">";
                           echo "Maint";
                        }
                        ?>

                  </select></td>
            </tr>

            <tr>
               <td>
                  <font color="red">Login Expire Date:</font>
               </td>
               <td></td>
               <td><? echo fieldError("date_expiration", $errors); ?>
                  <input type="text" name="date_expiration" value="<? echo $formVars["date_expiration"]; ?>" size=10 onfocus="showCalendar('',this,this,'','holder1',0,30,1)">
                  &nbsp;
                  &nbsp;(dd/mm/yyyy)</td>
            </tr>

            <tr>
               <td>
                  <font color="red">Comments:</font>
               </td>
               <td></td>
               <td><? echo fieldError("comments", $errors); ?>
                  <textarea name=comments rows=2 cols="40"><?php echo isset($formVars['comments']) ? $formVars['comments'] : NULL; ?></textarea></td>
            </tr>

            <tr>
               <td>Referral:</td>
               <td></td>
               <td>
                  <? echo isset($formVars["referral"]) ? $formVars["referral"] : NULL; ?></td>
            </tr>

            <tr>
               <td>Referral Detail:</td>
               <td></td>
               <td>
                  <? echo isset($formVars["referral_detail"]) ? $formVars["referral_detail"] : NULL; ?></td>
            </tr>


            <tr>
               <td>SwitchUser:</td>
               <td></td>
               <td>
                  <a href="/admin/switchuser.php?cust_id=<?php echo $cust_id; ?>"><?php echo 'Switch to User: ' . $formVars["username"] . '/' . $cust_id; ?></a></td>
            </tr>

            <tr>
               <td nowrap>Editors Id Number:</td>
               <td></td>
               <td nowrap><? echo $cid; ?></td>
            </tr>

            <tr>
               <td nowrap>Members Id Number:</td>
               <td></td>
               <td nowrap><? echo $cust_id; ?></td>
            </tr>

            <tr>
               <td>Username:</td>
               <td></td>
               <td>
                  <? echo isset($formVars["username"]) ? $formVars["username"] : NULL; ?></td>
            </tr>

            <tr>
               <td>Date Joined:</td>
               <td></td>
               <td>
                  <? echo isset($formVars["date_joined"]) ? $formVars["date_joined"] : NULL; ?></td>
            </tr>

            <tr>
               <td>Last login date:</td>
               <td></td>
               <td>
                  <? echo isset($formVars["date_last_login"]) ? $formVars["date_last_login"] : NULL; ?></td>
            </tr>

            <tr>
               <td colspan=3>
                  <input type="submit" value="Submit">

               </td>
            </tr>

            <tr>
               <td colspan=3>
                  <hr align='left' color='#000000' SIZE='1' noshade>
               </td>
            </tr>

         <?
         }
         ?>

         <tr>
            <td>Username:</td>
            <td></td>
            <td>
               <? echo isset($formVars["username"]) ? $formVars["username"] : NULL; ?></td>
         </tr>

         <tr>
            <td>Date Joined:</td>
            <td></td>
            <td>
               <? echo isset($formVars["date_joined"]) ? $formVars["date_joined"] : NULL; ?></td>
         </tr>

         <tr>
            <td>Date Membership Expires:</td>
            <td></td>
            <td>
               <? echo isset($formVars["date_expiration"]) ? $formVars["date_expiration"] : NULL; ?></td>
         </tr>

         <tr>
            <td>Last login date:</td>
            <td></td>
            <td>
               <? echo isset($formVars["date_last_login"]) ? $formVars["date_last_login"] : NULL; ?></td>
         </tr>

         <tr>
            <td></td>
            <td colspan=2><i>Click the check-box to display your info to other members</i></td>
         </tr>

         <tr>
            <td><font color="red">Mobile Phone: </font></td>
            <td><input type="checkbox" name="disp_phonemobile" value="1" <?php echo $disp['disp_phonemobile']; ?>></td>
            <td><? echo fieldError("phonemobile", $errors); ?>
               <input type="text" name="phonemobile" value="<? echo $formVars["phonemobile"]; ?>" size=15>(1-222-333-4444)</td>
         </tr>

         <tr>
            <td>Home Phone: </td>
            <td><input type="checkbox" name="disp_phonehome" value="1" <?php echo $disp['disp_phonehome']; ?>></td>
            <td><? echo fieldError("phonehome", $errors); ?>
               <input type="text" name="phonehome" value="<? echo $formVars["phonehome"]; ?>" size=15>(1-222-333-4444)</td>
         </tr>

         <tr>
            <td>Work Phone: </td>
            <td><input type="checkbox" name="disp_phonework" value="1" <?php echo $disp['disp_phonework']; ?>></td>
            <td><? echo fieldError("phonework", $errors); ?>
               <input type="text" name="phonework" value="<? echo $formVars["phonework"]; ?>" size=15>(1-222-333-4444)</td>
         </tr>

         <tr>
            <td>Misc Phone: </td>
            <td><input type="checkbox" name="disp_phonemisc" value="1" <?php echo $disp['disp_phonemisc']; ?>></td>
            <td><? echo fieldError("phonemisc", $errors); ?>
               <input type="text" name="phonemisc" value="<? echo $formVars["phonemisc"]; ?>" size=15>(1-222-333-4444)</td>
         </tr>


         <tr>
            <td>
               <font color="red">Email:</font>
            </td>
            <td><input type="checkbox" name="disp_email" value="1" <?php echo $disp['disp_email']; ?>></td>
            <td><? echo fieldError("email", $errors);
                  ?>
               <input type="text" name="email" value="<? echo $formVars["email"]; ?>" size=50 maxlength=50></td>
         </tr>


         <tr>
            <td>Alt Email:</td>
            <td><input type="checkbox" name="disp_email2" value="1" <?php echo $disp['disp_email2']; ?>></td>
            <td><? echo fieldError("email2", $errors); ?>
               <input type="text" name="email2" value="<? echo $formVars["email2"]; ?>" size=50 maxlength=50></td>
         </tr>

         <tr>
            <td>
               <font color="red">Sex: </font>
            </td>
            <td><input type="checkbox" name="disp_sex" value="1" <?php echo $disp['disp_sex']; ?>></td>
            <td><? echo fieldError("sex", $errors); ?>
               <select name="sex">
                  <option <? if ($formVars["sex"] == "") echo "selected"; ?>>

                  <option <? if ($formVars["sex"] == "Female") echo "selected"; ?>>
                     Female
                  <option <? if ($formVars["sex"] == "Male") echo "selected"; ?>>
                     Male
               </select></td>
         </tr>

         <?php /*
   <tr><td>RawAge:</td>
   <td><input type="checkbox" name="disp_email2"
   value="1"
   <?php echo $disp['disp_email2']; ?>
   ></td>
   <td><? echo fieldError("email2", $errors);
      echo $formVars["birth_year"] . ' / ' . $formVars["birth_month"] . ' / ' .  $formVars["birthday"]; ?></td>
   </tr>
      */
         ?>


         <tr>
            <td>Age:</td>
            <td></td>
            <td>
               <?php
               $age = "Unknown";
               if (isset($formVars["birth_year"]) && $formVars["birth_year"] <> "0000")
                  $age = date('Y', time()) - $formVars["birth_year"];
               //echo "Age: " . $age . "<br>";
               ?>
               <select name="age">
                  <option <?php if ($age == "Unknown") echo " selected ";
                           echo ' value="Unknown" '; ?>>Unknown</option>
                  <option <?php if ($age >= 20 && $age <= 24) echo " selected ";
                           echo ' value="22" '; ?>>20-24</option>
                  <option <?php if ($age >= 25 && $age <= 29) echo " selected ";
                           echo ' value="27" '; ?>>25-29</option>
                  <option <?php if ($age >= 30 && $age <= 34) echo " selected ";
                           echo ' value="32" '; ?>>30-34</option>
                  <option <?php if ($age >= 35 && $age <= 39) echo " selected ";
                           echo ' value="37" '; ?>>35-39</option>
                  <option <?php if ($age >= 40 && $age <= 44) echo " selected ";
                           echo ' value="42" '; ?>>40-44</option>
                  <option <?php if ($age >= 45 && $age <= 49) echo " selected ";
                           echo ' value="47" '; ?>>45-49</option>
                  <option <?php if ($age >= 50 && $age <= 54) echo " selected ";
                           echo ' value="52" '; ?>>50-54</option>
                  <option <?php if ($age >= 55 && $age <= 59) echo " selected ";
                           echo ' value="57" '; ?>>55-59</option>
                  <option <?php if ($age >= 60 && $age <= 64) echo " selected ";
                           echo ' value="62" '; ?>>60-64</option>
                  <option <?php if ($age >= 65) echo " selected ";
                           echo ' value="67" '; ?>>65++</option>
               </select> &nbsp; Your age will NOT be displayed to other members.
            </td>
         </tr>

         <tr>
            <td>Birthday:</td>
            <td><input type="checkbox" name="disp_date_birth" value="1" <?php echo $disp['disp_date_birth']; ?>></td>
            <td>
               <?php
               $birth_month = "Unknown";
               if (isset($formVars["birth_month"]))
                  $birth_month = $formVars["birth_month"];
               $birthday = "Unknown";
               if (isset($formVars["birthday"]))
                  $birthday = $formVars["birthday"];
               ?>
               <select name="birth_month">
                  <option <?php if ($birth_month == "Unknown") echo " selected";
                           echo ' value="Unknown" ' ?>>Unknown</option>
                  <option <?php if ($birth_month == "1") echo " selected";
                           echo ' value="1" '; ?>>Jan</option>
                  <option <?php if ($birth_month == "2") echo " selected";
                           echo ' value="2" ' ?>>Feb</option>
                  <option <?php if ($birth_month == "3") echo " selected";
                           echo ' value="3" ' ?>>Mar</option>
                  <option <?php if ($birth_month == "4") echo " selected";
                           echo ' value="4" ' ?>>Apr</option>
                  <option <?php if ($birth_month == "5") echo " selected";
                           echo ' value="5" ' ?>>May</option>
                  <option <?php if ($birth_month == "6") echo " selected";
                           echo ' value="6" ' ?>>Jun</option>
                  <option <?php if ($birth_month == "7") echo " selected";
                           echo ' value="7" ' ?>>Jul</option>
                  <option <?php if ($birth_month == "8") echo " selected";
                           echo ' value="8" ' ?>>Aug</option>
                  <option <?php if ($birth_month == "9") echo " selected";
                           echo ' value="9" ' ?>>Sep</option>
                  <option <?php if ($birth_month == "10") echo " selected";
                           echo ' value="10" ' ?>>Oct</option>
                  <option <?php if ($birth_month == "11") echo " selected";
                           echo ' value="11" ' ?>>Nov</option>
                  <option <?php if ($birth_month == "12") echo " selected";
                           echo ' value="12" ' ?>>Dec</option>
               </select> &nbsp;

               <select name="birthday">
                  <option <?php if ($birthday == "Unknown") echo "selected";
                           echo ' value="Unknown" '; ?>>Unknown</option>
                  <option <?php if ($birthday == "1") echo "selected";
                           echo ' value="1" '; ?>>1</option>
                  <option <?php if ($birthday == "2") echo "selected";
                           echo ' value="2" '; ?>>2</option>
                  <option <?php if ($birthday == "3") echo "selected";
                           echo ' value="3" '; ?>>3</option>
                  <option <?php if ($birthday == "4") echo "selected";
                           echo ' value="4" '; ?>>4</option>
                  <option <?php if ($birthday == "5") echo "selected";
                           echo ' value="5" '; ?>>5</option>
                  <option <?php if ($birthday == "6") echo "selected";
                           echo ' value="6" '; ?>>6</option>
                  <option <?php if ($birthday == "7") echo "selected";
                           echo ' value="7" '; ?>>7</option>
                  <option <?php if ($birthday == "8") echo "selected";
                           echo ' value="8" '; ?>>8</option>
                  <option <?php if ($birthday == "9") echo "selected";
                           echo ' value="9" '; ?>>9</option>
                  <option <?php if ($birthday == "10") echo "selected";
                           echo ' value="10" '; ?>>10</option>
                  <option <?php if ($birthday == "11") echo "selected";
                           echo ' value="11" '; ?>>11</option>
                  <option <?php if ($birthday == "12") echo "selected";
                           echo ' value="12" '; ?>>12</option>
                  <option <?php if ($birthday == "13") echo "selected";
                           echo ' value="13" '; ?>>13</option>
                  <option <?php if ($birthday == "14") echo "selected";
                           echo ' value="14" '; ?>>14</option>
                  <option <?php if ($birthday == "15") echo "selected";
                           echo ' value="15" '; ?>>15</option>
                  <option <?php if ($birthday == "16") echo "selected";
                           echo ' value="16" '; ?>>16</option>
                  <option <?php if ($birthday == "17") echo "selected";
                           echo ' value="17" '; ?>>17</option>
                  <option <?php if ($birthday == "18") echo "selected";
                           echo ' value="18" '; ?>>18</option>
                  <option <?php if ($birthday == "19") echo "selected";
                           echo ' value="19" '; ?>>19</option>
                  <option <?php if ($birthday == "20") echo "selected";
                           echo ' value="20" '; ?>>20</option>
                  <option <?php if ($birthday == "21") echo "selected";
                           echo ' value="21" '; ?>>21</option>
                  <option <?php if ($birthday == "22") echo "selected";
                           echo ' value="22" '; ?>>22</option>
                  <option <?php if ($birthday == "23") echo "selected";
                           echo ' value="23" '; ?>>23</option>
                  <option <?php if ($birthday == "24") echo "selected";
                           echo ' value="24" '; ?>>24</option>
                  <option <?php if ($birthday == "25") echo "selected";
                           echo ' value="25" '; ?>>25</option>
                  <option <?php if ($birthday == "26") echo "selected";
                           echo ' value="26" '; ?>>26</option>
                  <option <?php if ($birthday == "27") echo "selected";
                           echo ' value="27" '; ?>>27</option>
                  <option <?php if ($birthday == "28") echo "selected";
                           echo ' value="28" '; ?>>28</option>
                  <option <?php if ($birthday == "29") echo "selected";
                           echo ' value="29" '; ?>>29</option>
                  <option <?php if ($birthday == "30") echo "selected";
                           echo ' value="30" '; ?>>30</option>
                  <option <?php if ($birthday == "31") echo "selected";
                           echo ' value="31" '; ?>>31</option>
               </select> &nbsp; We celebrate birthdays!

            </td>
         </tr>



         <tr>
            <td colspan=3>
               <hr align='left' color='#000000' SIZE='1' noshade>
            </td>
         </tr>

         <tr>
            <td colspan=3><b>NOTE: This information will be shared with other members.</b></td>
         </tr>
         <tr>
            <td>Your Interests:</td>
            <td></td>
            <td><? echo fieldError("summary", $errors); ?>
               <input type="text" name="summary" value="<? echo $formVars["summary"]; ?>" size=50 maxlength=254></td>
         </tr>

         <tr>
            <td>Your Occupation:</td>
            <td></td>
            <td><? echo fieldError("occupation", $errors); ?>
               <input type="text" name="occupation" value="<? echo $formVars["occupation"]; ?>" size=50 maxlength=254></td>
         </tr>

         <tr>
            <td>More About You:</td>
            <td></td>
            <td><? echo fieldError("details", $errors); ?>
               <textarea name=details rows=20 cols="40"><?php echo isset($formVars['details']) ? $formVars['details'] : NULL; ?></textarea></td>
         </tr>

         <tr>
            <td colspan=3></td>
         </tr>

         <tr>
            <td>
               <font color="red">Address:</font>
            </td>
            <td><input type="checkbox" name="disp_address" value="1" <?php echo $disp['disp_address']; ?>></td>
            <td><? echo fieldError("address", $errors); ?>
               <? echo fieldError("address1", $errors); ?>
               <input type="text" name="address1" value="<? echo $formVars["address1"]; ?>" size=50></td>
         </tr>

         <tr>
            <td>Address2</td>
            <td><input type="checkbox" name="disp_address2" value="1" <?php echo $disp['disp_address2']; ?>></td>
            <td><? echo fieldError("address2", $errors); ?>
               <input type="text" name="address2" value="<? echo $formVars["address2"]; ?>" size=50></td>
         </tr>

         <tr>
            <td>Address3</td>
            <td><input type="checkbox" name="disp_address3" value="1" <?php echo $disp['disp_address3']; ?>></td>
            <td><? echo fieldError("address3", $errors); ?>
               <input type="text" name="address3" value="<? echo $formVars["address3"]; ?>" size=50></td>
         </tr>

         <tr>
            <td>
               <font color="red">City:</font>
            </td>
            <td><input type="checkbox" name="disp_city" value="1" <?php echo $disp['disp_city']; ?>></td>
            <td><? echo fieldError("city", $errors); ?>
               <input type="text" name="city" value="<? echo $formVars["city"]; ?>" size=20></td>
         </tr>

         <tr>
            <td>State: </td>
            <td><input type="checkbox" name="disp_state" value="1" <?php echo $disp['disp_state']; ?>></td>
            <td><? echo fieldError("state", $errors); ?>
               <input type="text" name="state" value="<? echo $formVars["state"]; ?>" size=20></td>
         </tr>

         <tr>
            <td>
               <font color="red">Zipcode:</font>
            </td>
            <td><input type="checkbox" name="disp_zipcode" value="1" <?php echo $disp['disp_zipcode']; ?>></td>
            <td><? echo fieldError("zipcode", $errors); ?>
               <input type="text" name="zipcode" value="<? echo $formVars["zipcode"]; ?>" size=10>
               <input type="hidden" name="country" value="United States">
            </td>
         </tr>

         <?php
         if ($AuthLevel == "Admin") {
         ?>

            <tr>
               <td nowrap>Editors Id Number:</td>
               <td></td>
               <td nowrap><? echo $cid; ?></td>
            </tr>

            <tr>
               <td nowrap>Members Id Number:</td>
               <td></td>
               <td nowrap><? echo $cust_id; ?></td>
            </tr>

            <tr>
               <td colspan=3></td>
            </tr>


         <?php
         }
         ?>

         <tr>
            <td><input type="submit" value="Submit"></td>
         </tr>
      </table>
   </form>
</div>

<?php

require('footer.php');

?>