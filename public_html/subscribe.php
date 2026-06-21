<?php
// Subscribe to the newsletter

require('always.include.php');
$debug = true;
$debug = false;

//session_start();
//require('include.php');

// Register an error array - just in case!
if (!SessionIsRegistered("errors")) {
  $errors = array();
  SessionRegister("errors", $errors);
}

// Set up a $formVars array with the POST variables
// and register with the session.
if (!SessionIsRegistered("formVars")) {
  $formVars = array();
  SessionRegister("formVars", $formVars);
}

foreach ($_POST as $varname => $value)
  $formVars[$varname] = htmlentities(substr(trim($value), 0, 50), ENT_QUOTES, 'UTF-8');
foreach ($_GET as $varname => $value)
  $formVars[$varname] = htmlentities(substr(trim($value), 0, 50), ENT_QUOTES, 'UTF-8');

$email_address = "";
if (isset($formVars['email_address']))
  $email_address = $formVars['email_address'];
$mode = "validate";
if (isset($formVars['mode']))
  $mode = $formVars['mode'];

// minimal checks
if (($mode == "validate") &&
  (strpos($email_address, "@") > 0) &&
  (strpos($email_address, ".") > 0)
) {
  $mode = "finished";
  $email_body = "subscribe\n";

  // build subscribe-to address
  $emailNoticesTo = "news-subscribe-" .
    str_replace("@", "=", $email_address) .
    "@" . $email_domain_name;

  $email_from =
    "From: " . $emailNoticesFrom . "\n" .
    "Reply-To: " . $emailNoticesFrom . "\n" .
    "Return-Path: " . $emailNoticesFrom . "\n" .
    "X-Mailer: PHP/" . phpversion();

  mail($emailNoticesTo, "subscribe", $email_body, $email_from);
} else
  $mode = "validate";

$FileName = __FILE__;
$WebPageTitle = 'Subscribe to eNewsletter - ' . $ClubCompanyName;
require('top.php');


?>

<div id="centercontent">
  <h3>Subscribe to Our Free eNewsletter</h3>

  <?php
  if ($mode == "finished") {
    // leslie - this text occurs after pressing subscribe ...
  ?>

    An email has been sent to <?php echo $email_address; ?> with instructions on
    how to complete the newsletter subscription process.
    Please reply to the email to receive the newsletter beginning with the next issue. Hope
    to see you at an event soon!


  <?php
  } else {
    // leslie - edit any black text below ...
  ?>
    <form method=post action=http://www.ymlp.com/subscribe.php?adventureclub> <table border=0>
      <tr>
        <td colspan=2>Fill out your e-mail address <br> to receive our no obligation eNewsletter
          with information about fun activities!</td>
      </tr>
      <tr>
        <td><input type="text" name="YMLP0" size="20"></td>
        <td><input type="submit" value="Submit"></td>
      </tr>
      <tr>
        <td colspan=2>
          <input type="radio" name="action" value="subscribe" checked> Subscribe<input type="radio" name="action" value="unsubscribe"> Unsubscribe</td>
      </tr>
      </table>
    </form>

  <?php
  }
  ?>
</div>

<?php
require('footer.php');
?>