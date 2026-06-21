<?php
/*
AdventureClub.Info, Inc.

*/

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


$ClubCompanyName    = GetParameter('ClubCompanyName');
//$ShortClubName 		  = GetParameter('ShortClubName');
//$EmailNoticesTo 		= GetParameter('EmailNoticesTo');
//$EmailNoticesFrom 	= GetParameter('EmailNoticesFrom');

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
// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in!";
  SessionRegister("message", $message);

  // Register where they came from
  if (isset($_GET['event_id']))
    $referer = $_SERVER['PHP_SELF'] . "?event_id=" . clean($_GET['event_id']);
  else
    $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: http://" . $_SERVER['SERVER_NAME']
    . "/login.php";
  header($loc);
  exit;
}

// Set table border value
$border = " border=0";

$errors = $_SESSION["errors"];
if (isset($errors)) {
  $ErrorString = "";
  foreach ($errors as $str) {
    $ErrorString .= $str;
  }
}

// provide link to correct event view page
$formVars = $_SESSION["formVars"];
$str = "";
$str .= "<a href=\"eview.php?event_id="
  . $formVars["event_id"]
  . "\">"
  . "Return to Event View"
  . "</a>";

$FileName = __FILE__;
$WebPageTitle = 'Event Leader - ' . $ClubCompanyName;
require('top.php');

?>
<div id="centercontent">
  <hr>
  <center>

    <?php
    if (isset($ErrorString)) {
      echo $ErrorString;
      echo "<br><br>";
      echo $str;
    } else
      echo "Sorry, We have a server problem.";
    ?>
  </center>
</div>

<?php
require('footer.php'); ?>