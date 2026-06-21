<?php
// This script shows the user a customer <form>.
// It is used for INSERTing a new customer
// The script also shows error messages above widgets
// that contain erroneous data; errors are generated
// by join-post.php

//echo "Starting!<br>";


require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

if ($debug) {
    LogMsg('join.php starting...' . "<br>");
}

if (isset($_GET['invalid']) && clean($_GET['invalid'])) {
    // invalid data entry detected during join-post.php validation
    if (SessionIsRegistered('errors')) {
        $errors = $_SESSION['errors'];
    }
    if (SessionIsRegistered('formVars')) {
        $formVars = $_SESSION['formVars'];
    }
} else {
    // fresh start, reset $formVars & $errors
    $formVars = array();
    SessionUnRegister('formVars');
    // Reset the errors
    $errors = array();
    SessionUnRegister('errors');
    // indicate agreement to ACG membership policies
    $formVars['agreement'] = 'agree';
}

//echo "Obtaining post vars!<br>";
// get post
//foreach($_POST as $varname => $value)
//  	$formVars[$varname] = trim($value);

// Show an error in a red font
function fieldError($fieldName, $errors)
{
    if (isset($errors[$fieldName]))
        echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
}

$border = 0;
$FullTableWidth = "100%";

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Join ' . $ClubCompanyName;
$join = true;
//echo "Join: " . $join . "<br>";
require('top.php');

?>
<div align="left">
    <form method="POST" action="/join/join-post.php">
        <?php
        if (isset($errors) and count($errors) > 0) {
            echo "<h2><font color=\"red\">Errors Occurred. Please scroll down.</font></h2>";
            //print_r($errors);
            //exit;
        }
        ?>

        <h3>Join the <?php echo $ClubCompanyName; ?> </h3>

        <p>
            Our yearly initiation fee is only $3 a month (covers the website expenses),
            paid yearly.
        </p>

        <p>
            To join:</p>

        <p>
            1) Fill out the form below, then click the submit button.
        </p>

        <p>
            2) Complete your payment using PayPal, our secure online payment processor.</p>

        <p>
            3) Once we get your payment, you
            will be able to login using the username and password that you supplied
            on the form below. Please allow at least 24 hours for account activation.&nbsp;
            We will send you a welcome email when your account is ready!</p>

        <p>
            <b>Privacy Note:</b> your information will not
            be shared with third parties, other than ACG event leaders who
            may need to contact you with event change information. </p>
        </p>


        <table width="<?php echo $FullTableWidth; ?>" border="<?php echo $border; ?>">
            <col span="1" align="right">

            <tr>
                <td colspan=2>
                    <hr>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <font color="red">First name:</font>
                </td>
                <td><? echo fieldError("firstName", $errors); ?>
                    <input type="text" name="firstName" value="<?php echo isset($formVars['firstName']) ? $formVars['firstName'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Last name:</font>
                </td>
                <td><? echo fieldError("surname", $errors); ?>
                    <input type="text" name="surname" value="<?php echo isset($formVars['surname']) ? $formVars['surname'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td colspan=2>
                    <hr>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Address:</font>
                </td>
                <td><? echo fieldError("address", $errors); ?>
                    <? echo fieldError("address1", $errors); ?>
                    <input type="text" name="address1" value="<?php echo isset($formVars['address1']) ? $formVars['address1'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td></td>
                <td><? echo fieldError("address2", $errors); ?>
                    <input type="text" name="address2" value="<?php echo isset($formVars['address2']) ? $formVars['address2'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td></td>
                <td><? echo fieldError("address3", $errors); ?>
                    <input type="text" name="address3" value="<?php echo isset($formVars['address3']) ? $formVars['address3'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td>
                    <font color="red">City:</font>
                </td>
                <td><? echo fieldError("city", $errors); ?>
                    <input type="text" name="city" value="<?php echo isset($formVars['city']) ? $formVars['city'] : NULL; ?>" size=20></td>
            </tr>

            <tr>
                <td>
                    <font color="red">State: </font>
                </td>
                <td><? echo fieldError("state", $errors); ?>
                    <input type="text" name="state" value="<?php echo isset($formVars['state']) ? $formVars['state'] : NULL; ?>" size=20></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Zipcode:</font>
                </td>
                <td><? echo fieldError("zipcode", $errors); ?>
                    <input type="text" name="zipcode" value="<?php echo isset($formVars['zipcode']) ? $formVars['zipcode'] : NULL; ?>" size=10> (55555-4444)
                    <input type="hidden" name="country" value="United States">
                </td>
            </tr>


            <tr>
                <td colspan=2>
                    <hr>
                </td>
            </tr>

            <tr>
                <td>
                    <font color="red">Email:</font>
                </td>
                <td><? echo fieldError("email", $errors); ?>
                    <input type="text" name="email" value="<?php echo isset($formVars['email']) ? $formVars['email'] : NULL; ?>" size=50></td>
            </tr>

            <tr>
                <td>Telephone: </font>
                </td>
                <td><? echo fieldError("phone", $errors); ?>
                    <input type="text" name="phone" value="<?php echo isset($formVars['phone']) ? $formVars['phone'] : NULL; ?>" size=15> (333-333-4444)</td>
            </tr>

            <tr>
                <td>
                    <font color="red">Mobile Phone:
                </td>
                <td><? echo fieldError("phonemobile", $errors); ?>
                    <input type="text" name="phonemobile" value="<?php echo isset($formVars['phonemobile']) ? $formVars['phonemobile'] : NULL; ?>" size=15> (333-333-4444)&nbsp;How will we contact you if an event is canceled?</td>
            </tr>

            <tr>
                <td>
                    <font color="red">Sex:</font>
                </td>
                <td><? echo fieldError("sex", $errors);
                    if (isset($formVars["sex"]) && $formVars["sex"] == "female") { ?>
                        Female
                        <input type="radio" name="sex" value="female" checked>
                        Male
                        <input type="radio" name="sex" value="male">

                    <? } elseif (isset($formVars["sex"]) && $formVars["sex"] == "male") { ?>

                        Female
                        <input type="radio" name="sex" value="female">
                        Male
                        <input type="radio" name="sex" value="male" checked>

                    <? } else { ?>

                        Female
                        <input type="radio" name="sex" value="female">
                        Male
                        <input type="radio" name="sex" value="male">

                    <? } ?>
                </td>
            </tr>

            <tr>
                <td>Age:</td>
                <td>
                    <?php
                    $age = "Unknown";
                    if (isset($formVars["age"]))
                        $age = $formVars["age"];
                    ?>
                    <select name="age">
                        <option <?php if ($age == "Unknown") echo "selected";
                                echo ' value="Unknown" '; ?>>Unknown</option>
                        <option <?php if ($age == "22") echo "selected";
                                echo ' value="22" '; ?>>20-24</option>
                        <option <?php if ($age == "27") echo "selected";
                                echo ' value="27" '; ?>>25-29</option>
                        <option <?php if ($age == "32") echo "selected";
                                echo ' value="32" '; ?>>30-34</option>
                        <option <?php if ($age == "37") echo "selected";
                                echo ' value="37" '; ?>>35-39</option>
                        <option <?php if ($age == "42") echo "selected";
                                echo ' value="42" '; ?>>40-44</option>
                        <option <?php if ($age == "47") echo "selected";
                                echo ' value="47" '; ?>>45-49</option>
                        <option <?php if ($age == "52") echo "selected";
                                echo ' value="52" '; ?>>50-54</option>
                        <option <?php if ($age == "57") echo "selected";
                                echo ' value="57" '; ?>>55-59</option>
                        <option <?php if ($age == "62") echo "selected";
                                echo ' value="62" '; ?>>60-64</option>
                        <option <?php if ($age == "67") echo "selected";
                                echo ' value="67" '; ?>>65++</option>
                    </select> &nbsp; Please enter your age so we can calculate club demographics.
                </td>
            </tr>

            <tr>
                <td>Birthday:</td>
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
                        <option <?php if ($birth_month == "Unknown") echo "selected";
                                echo ' value="Unknown" '; ?>>Unknown</option>
                        <option <?php if ($birth_month == "1") echo "selected";
                                echo ' value="1" ';  ?>>Jan</option>
                        <option <?php if ($birth_month == "2") echo "selected";
                                echo ' value="2" '; ?>>Feb</option>
                        <option <?php if ($birth_month == "3") echo "selected";
                                echo ' value="3" '; ?>>Mar</option>
                        <option <?php if ($birth_month == "4") echo "selected";
                                echo ' value="4" '; ?>>Apr</option>
                        <option <?php if ($birth_month == "5") echo "selected";
                                echo ' value="5" '; ?>>May</option>
                        <option <?php if ($birth_month == "6") echo "selected";
                                echo ' value="6" '; ?>>Jun</option>
                        <option <?php if ($birth_month == "7") echo "selected";
                                echo ' value="7" '; ?>>Jul</option>
                        <option <?php if ($birth_month == "8") echo "selected";
                                echo ' value="8" '; ?>>Aug</option>
                        <option <?php if ($birth_month == "9") echo "selected";
                                echo ' value="9" '; ?>>Sep</option>
                        <option <?php if ($birth_month == "10") echo "selected";
                                echo ' value="10" '; ?>>Oct</option>
                        <option <?php if ($birth_month == "11") echo "selected";
                                echo ' value="11" '; ?>>Nov</option>
                        <option <?php if ($birth_month == "12") echo "selected";
                                echo ' value="12" '; ?>>Dec</option>
                    </select> &nbsp;

                    <select name="birthday">
                        <option <?php if ($birthday == "Unknown") echo "selected";
                                echo ' value="Unknown" ' ?>>Unknown</option>
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
                <td>
                    <font color="red">Referral:</font>
                </td>
                <td><? echo fieldError("referral", $errors); ?>
                    <select name="referral">
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == ""
                                )
                                    echo "selected"; ?>>
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Brochure"
                                )
                                    echo "selected"; ?>>
                            Brochure
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Friend"
                                )
                                    echo "selected"; ?>>
                            Friend
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Member"
                                )
                                    echo "selected"; ?>>
                            Member
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Online Search"
                                )
                                    echo "selected"; ?>>
                            Online Search
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Print Article"
                                )
                                    echo "selected"; ?>>
                            Print Article
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Print Advertisement"
                                )
                                    echo "selected"; ?>>
                            Print Advertisement
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Radio"
                                )
                                    echo "selected"; ?>>
                            Radio
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "TV"
                                )
                                    echo "selected"; ?>>
                            TV
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Flyer"
                                )
                                    echo "selected"; ?>>
                            Flyer
                        <option <? if (
                                    isset($formVars["referral"]) &&
                                    $formVars["referral"] == "Other"
                                )
                                    echo "selected"; ?>>
                            Other
                    </select>
                    How did you find out about the <?php echo $ClubCompanyName; ?>?
                </td>
            </tr>

            <tr>
                <td>Referral Detail:</font>
                </td>
                <td><? echo fieldError("referral_detail", $errors); ?>
                    <input type="text" name="referral_detail" value="<?php echo isset($formVars['referral_detail']) ? $formVars['referral_detail'] : NULL; ?>" size=80> If a friend referred you, please mention their name here.</td>
            </tr>
            <!--
   <tr><td>Discount Code: </td>
   <td><? echo fieldError("discountcode", $errors); ?>
      <input type="text" name="discountcode"
       value="<?php echo isset($formVars['discountcode']) ? $formVars['discountcode'] : NULL; ?>"
       size=20>&nbsp;Optional discount code</td>
   </tr>
-->
            <tr>
                <td colspan=2>
                    <hr>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Username:</font>
                </td>
                <td><? echo fieldError("username", $errors); ?>
                    <input type="text" name="username" value="<?php echo isset($formVars['username']) ? $formVars['username'] : NULL; ?>" size=30></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Password:</font>
                </td>
                <td><? echo fieldError("password", $errors); ?>
                    <input type="password" name="password" value="<?php echo isset($formVars['password']) ? $formVars['password'] : NULL; ?>" size=20> (Min 6 chars, max 20 chars)</td>
            </tr>

            <tr>
                <td colspan=2>
                    <hr>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <font color="red">Agreement:</font>
                </td>
                <td><? echo fieldError("agreement", $errors); ?>
                    <input type="checkbox" name="agreement" value="agree" <?php if (isset($formVars['agreement']))
                                                                                //echo " checked"; 
                                                                            ?>>
                    I understand and agree to the <?php echo $ClubCompanyName; ?>
                    <a href="/policies.php" target="new" name="policies">Policies</a>.

                </td>
            </tr>

            <tr>
                <td colspan=2>
                    <hr>
                </td>
                <td></td>
            </tr>


            <tr>
                <td><input type="submit" value="Submit"></td>
            </tr>
        </table>
    </form>


</div>
<?php
require('footer.php');
?>