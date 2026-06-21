<?php
// This script shows the user a customer <form>.
// It is used for INSERTing a new customer
// The script also shows error messages above widgets
// that contain erroneous data; errors are generated
// by join-post.php

//require ('site.php');	
//require ( $path . 'ErrorHandler.inc');
//$error =& new ErrorHandler();
//$error->set_context('strict',TRUE);

// Connect to a session
require('always.include.php');
//session_start();
//require('include.php');
//require ('cc.php');

if (empty($errors)) {
    // Reset $formVars, since this is a fresh start
    $formVars = array();

    // indicate agreement to membership policies
    $formVars['agreement'] = 'agree';

    // Reset the errors
    $errors = array();
}

// get post      
foreach ($_SESSION['formVars'] as $varname => $value)
    $formVars[$varname] = trim($value);
foreach ($_SESSION['errors'] as $varname => $value)
    $errors[$varname] = trim($value);

// Show an error in a red font
function fieldError($fieldName, $errors)
{
    if (isset($errors[$fieldName]))
        echo "<font color=\"red\">" . $errors[$fieldName] . "</font><br>";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <title>Admin create new member</title>
    <meta name="description" content="Central Florida Activity Club hosts indoor and outdoor activities for all age groups.  We take the work out of having fun! ">
    <link rel=STYLESHEET href=" <?php echo GetParameter('vd'); ?>club.css" Type="text/css">
</head>

<body>
    <div id="centercontent">
        <hr>
        <form method="POST" <?php echo 'action="' . GetParameter('vd') . 'createmember-post.php"'; ?>>
            <?php
            if (count($errors) > 0) {
                echo "<h2><font color=\"red\">Errors Occurred. Please scroll down.</font></h2>";
            }
            ?>

            <b>Admin create new member</b>

            <table>
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
                        <input type="hidden" name="country" value="United States"></td>
                </tr>


                <tr>
                    <td colspan=2>
                        <hr>
                    </td>
                </tr>

                <tr>
                    <td>
                        <font color="red">Telephone: </font>
                    </td>
                    <td><? echo fieldError("phone", $errors); ?>
                        <input type="text" name="phone" value="<?php echo isset($formVars['phone']) ? $formVars['phone'] : NULL; ?>" size=15> (333-333-4444)</td>
                </tr>

                <tr>
                    <td>
                        <font color="red">Email:</font>
                    </td>
                    <td><? echo fieldError("email", $errors); ?>
                        <input type="text" name="email" value="<?php echo isset($formVars['email']) ? $formVars['email'] : NULL; ?>" size=50></td>
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
                                        $formVars["referral"] == "Flyer"
                                    )
                                        echo "selected"; ?>>
                                Flyer
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
                                        $formVars["referral"] == "Office Visit"
                                    )
                                        echo "selected"; ?>>
                                Office Visit
                            <option <? if (
                                        isset($formVars["referral"]) &&
                                        $formVars["referral"] == "Other"
                                    )
                                        echo "selected"; ?>>
                                Other
                        </select>
                        How did you find out about the Central Florida Activity Club?
                    </td>
                </tr>

                <tr>
                    <td>
                        <font color="red">Referral Detail:</font>
                    </td>
                    <td><? echo fieldError("referral_detail", $errors); ?>
                        <input type="text" name="referral_detail" value="<?php echo isset($formVars['referral_detail']) ? $formVars['referral_detail'] : NULL; ?>" size=80></td>
                </tr>

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
                        <input type="password" name="password" value="<?php echo isset($formVars['password']) ? $formVars['password'] : NULL; ?>" size=20> (Min 4 chars, max 20 chars)</td>
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
                                                                                    echo " checked"; ?>>
                        I understand and agree to the
                        <a href="/policies.php" target="new" name="policies">Policies</a>
                        and <a href="/faq.php" target="new" name="faq">Frequently Asked Questions</a>
                        of club membership
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
    <?php SSLBannerOnly("Admin create member"); ?>
</body>

</html>