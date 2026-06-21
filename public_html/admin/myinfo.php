<?

// use phpinfo() to display system values
// must be logged in and admin level to access this

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');


// Is the user logged in?
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';	
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';	
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';	
//$AuthLevel = AuthLevel($loginUsername);
//$AdminLevel = $AuthLevel == 'Admin';	
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel   )
{
   // Register a message to show the user
   $message = "Error: you are not logged in! (elist)";
   SessionRegister("message",$message);

   // Register where they came from
   $referer = $PHP_SELF;
   SessionRegister("referer",$referer);

   // redirect to the login page
   $loc = "Location: /index.php";
   header($loc);
   exit;
}


  echo "<HTML>\n";

  echo "<BODY BGCOLOR=#EEEEEE>\n";

  phpinfo();

  echo "</BODY>\n";
  echo "</HTML>\n";
