<?php
// display list of events
// user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" .
      $errors[$fieldName] .
      "</font><br>";
}

// Is the user logged in and were there no errors from a previous
// validation?  

// Is the user logged in?
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
//$AuthLevel = AuthLevel($loginUsername);
//$AdminLevel = $AuthLevel == 'Admin';	
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $PHP_SELF;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}

$sessions = array();

$path = realpath(session_save_path());
$files = array_diff(scandir($path), array('.', '..'));

foreach ($files as $file)
{
  if (filesize($path.'/'.$file) > 0) {
      //$sessions[$file] = unserialize(file_get_contents($path . '/' . $file));
      $sessions[$file] = file_get_contents($path . '/' . $file);
  }
}

echo '<pre>';
print "path: ".$path."\n";
print_r($sessions);
echo '</pre>';


?>