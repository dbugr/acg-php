<?php
// permit user to change event co-leader via pulldown members list

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

require('eventedit.php');
require('connect_mysqli_obj.php');


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

$EventObj = new EventEdit;

// get env variables
$sMode = $EventObj->ObtainPostEnvVar('mode');
$EventId = $EventObj->ObtainPostEnvVar('event_id');
$NewLeaderId = $EventObj->ObtainPostEnvVar('leader_id');

//echo 'sMode: '.$sMode.'<br>';
//echo 'EventId: '.$EventId.'<br>';

// is the user the event leader, coleader or administrator?
// If not, display error message and bounce back to event page
$loginUsername = LoginUsername();
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
   	// get env LeaderId variable
   	// sanitize LeaderId variable
   	// update the database with new LeaderId
   	$EventObj->UpdateLeader($EventId, $NewLeaderId);
	$loc = '/members/eview.php?event_id='.$EventId;
	header("Location: ".$loc);
} else {
    $EventName = $EventObj->GetEventName($EventId);
    $aLeader = $EventObj->GetCurrentLeader($EventId);
    $LeaderId = key($aLeader);
    $LeaderName = $aLeader[$LeaderId];

    $EventObj->GetAllCurrentMemberNames();

    $FileName = $_SERVER['PHP_SELF'];
    $ClubCompanyName	= GetParameter('ClubCompanyName');
    $WebPageTitle = 'Change Event Leader ' . $ClubCompanyName;
    require('top.php');

    echo '<h2>Change Event Leader</h2>';
    echo '<p>Current Event Name: '.$EventName.'<br></p>';
    //echo '<p>Current Event Id: '.$EventId.'<br></p>';
    echo '<p>Current Event LeaderId: '.$LeaderId.'<br></p>';
    echo '<p>Current Event Leader: '.$LeaderName.'<br></p>';
    //echo '<p>'.print_r($aLeader).'</p>';

    echo $EventObj->DisplayLeaderSelectForm($EventId,$LeaderId);
    require('footer.php');
  }
