<?php
// permit user to change event co-leader via pulldown members list

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

require('connect_mysqli_obj.php');
require('eventedit.php');


  // Is the user logged in?
  if (!SessionIsRegistered("loginUsername"))
  {
     // Register a message to show the user
     $message = "Error: you are not logged in! (elist)";
     SessionRegister("message",$message);

     // Register where they came from
     $referer = $_SERVER['PHP_SELF'];
     SessionRegister("referer",$referer);

     // redirect to the login page
     $loc = "Location: /index.php";
     header($loc);
     exit;
  }

LogMsg("eedit-co-leader.php");

//LogMsg("creating EventObj");
$EventObj = new EventEdit;

// get env variables
$sMode = $EventObj->ObtainPostEnvVar('mode');
$EventId = $EventObj->ObtainPostEnvVar('event_id');
$NewCoLeaderId = $EventObj->ObtainPostEnvVar('coleader_id');

//echo 'sMode: '.$sMode.'<br>';
//echo 'EventId: '.$EventId.'<br>';

// is the user the event leader, coleader or administrator?
// If not, display error message and bounce back to event page
//LogMsg("getting loginUsername");
$loginUsername = LoginUsername();
//LogMsg("loginUsername: ".$loginUsername);
//LogMsg("getting privileges");
$UserEditPrivilege = $EventObj->UserHasEditPrivilege($loginUsername,$EventId);
if (!$UserEditPrivilege)	{
    // redirect to the events list page
  	$loc = "Location: /members/eview.php?event_id=".$EventId;
    header($loc);
    exit;
}

// if a previous error screwed up the $EventId, correct it
//if ($EventId < 1) {
//	$EventId = 1;
//}

if ($sMode == 'update') {
   	// get env CoLeaderId variable
   	// sanitize CoLeaderId variable
   	// update the database with new CoLeaderId
    LogMsg("UpdateCoLeader");
   	$EventObj->UpdateCoLeader($EventId, $NewCoLeaderId);
	  $loc = '/members/eview.php?event_id='.$EventId;
	  header("Location: ".$loc);
} else {
    LogMsg("Display Change Event Co-Leader page");
    $EventName = $EventObj->GetEventName($EventId);
    $aCoLeader = $EventObj->GetCurrentCoLeader($EventId);
    $CoLeaderId = key($aCoLeader);
    $CoLeaderName = $aCoLeader[$CoLeaderId];

    $EventObj->GetAllCurrentMemberNames();

    $FileName = $_SERVER['PHP_SELF'];
    $ClubCompanyName	= GetParameter('ClubCompanyName');
    $WebPageTitle = 'Change Event CoLeader ' . $ClubCompanyName;
    require('top.php');

    echo '<h2>Change Event Co-Leader</h2>';
    echo '<p>Current Event Name: '.$EventName.'<br></p>';
    echo '<p>Current Event Co-Leader: '.$CoLeaderName.'<br></p>';

    echo $EventObj->DisplayCoLeaderSelectForm($EventId,$CoLeaderId);
    require('footer.php');
  }
