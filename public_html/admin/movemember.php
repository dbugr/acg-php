<?php

/*
 script to "move" a member from one club (the source club)
 to another club (the target club). Do this by:
 a) updating members.cust_id, for ex: 'gnv_369' to 'cfac_???'
 b) updating members.m_club to the club prefix, ex: 'cfac'
 c) updating events.leader_id
 d) updating events.co_leader_id
 e) updating reserve.cust_id

*/

// useful command line statements
// SELECT reserve_id, cust_id, event_id FROM reserve WHERE cust_id = "gnv_375";
// SELECT event_id, e_name FROM events WHERE event_id = "gnv_1607";
// SELECT cust_id, m_firstname, m_lastname FROM members WHERE cust_id = "gnv_375";

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');

$SrcClubCode = 'cfac';  // source club name
$DstClubCode = 'gnv'; // target club name

//$SrcClubCode = 'gnv';  // source club name
//$DstClubCode = 'cfac'; // target club name

// array of source club cust_id's to be moved
$aCustIds = array();
$aCustIds[] = 'cfac_387'; // Karen Piazza cfac_387
//$aCustIds[] = 'cfac_420'; // Stephanie Pressman gnv_346
// Kathy Radcliff Ocala 34480  gnv_369
//$aCustIds[] = 'gnv_369'; 	// Kathy Radcliff Ocala 34480  gnv_369
//$aCustIds[] = 'gnv_371';  // Deborah & Gordon White Apopka 32712 (SE of Ocala) gnv_371
//$aCustIds[] = 'gnv_343';	// Heather Jones Silver Springs 34488 2005-07-16 gnv_343
// WARNING! Howard has entries in BOTH the gnv and cfac sites!!!
//$aCustIds[] = 'gnv_374';	// Howard Winkler Summerfield 34491 (SE of Ocala) gnv_374




  ////// functions
  // Get Next member id from database 
  
function GetNextMemberIdFromDatabase ($ClubCode) 
	{
    global $connection;
    global $debug;
  
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

	echo 'SQL: ' . $query . '<br>';
	//if (!$debug) {
        if (!($result = @ mysqli_query($connection, $query)))
        {
          trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
        	exit;
      	}
      	$row = mysqli_fetch_array($result);
      	$cust_id = $row['cust_id'];
      	$cust_id += 1;
        $cust_id = $ClubCode . "_" . $cust_id;
        return $cust_id;
  	//}
}
  
function MvMembersTableCustId ($SrcCustId,$DstCustId,$DstClub) 
	{
    global $connection;
    global $debug;
  
	$query = "update members set cust_id = '"
		.$DstCustId
		."', m_club = '"
		.$DstClub
		."' where cust_id = '"
		.$SrcCustId
		."'";
	echo 'SQL: ' . $query . '<br>';
	if (!$debug) {
        if (!($result = @ mysqli_query($connection, $query)))
        {
          trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
        	exit;
      	}
    }

}

function MvEventsTableLeaderId ($SrcCustId,$DstCustId,$DstClub) 
	{
    global $connection;
    global $debug;
  
	$query = "update events set leader_id = '"
		.$DstCustId
		."' where leader_id = '"
		.$SrcCustId
		."'";
	echo 'SQL: ' . $query . '<br>';
	if (!$debug) {
        if (!($result = @ mysqli_query($connection, $query)))
        {
          trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
        	exit;
      	}
    }

	$query = "update events set co_leader_id = '"
		.$DstCustId
		."' where co_leader_id = '"
		.$SrcCustId
		."'";
	echo 'SQL: ' . $query . '<br>';
	if (!$debug) {
        if (!($result = @ mysqli_query($connection, $query)))
        {
          trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
        	exit;
      	}
    }

}

function MvReserveTableCustId ($SrcCustId,$DstCustId,$DstClub) 
	{
    global $connection;
    global $debug;
  
	$query = "update reserve set cust_id = '"
		.$DstCustId
		."' where cust_id = '"
		.$SrcCustId
		."'";
	echo 'SQL: ' . $query . '<br>';
	if (!$debug) {
        if (!($result = @ mysqli_query($connection, $query)))
        {
          trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection), E_USER_ERROR);
        	exit;
      	}
    }

}



// code starts here!

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Function List ' . $ClubCompanyName;
$admin = true;
require('top.php');

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing

// Is the user logged in?
$loginUsername = LoginUsername();
	$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';	
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel   )
{
     // Register a message to show the user
     $message = "Error: you are not logged in! (elist)";
     SessionRegister("message",$message);

     // Register where they came from
     $referer = __FILE__;
     SessionRegister("referer",$referer);

     // redirect to the login page
     $loc = "Location: /index.php";
     header($loc);
     exit;
}

		mysqlconnect($connection);

     // obtain member information from members table
     $cust_id = getCustomerID($loginUsername);

     if ($cust_id == NULL) {
        $message = "Error: Invalid Customer ID!\n";
        trigger_error("Error cust_id is NULL",E_USER_ERROR);
        exit;
     }

$DebugText = $debug ? "True" : "False";
echo "MOVING CLUB MEMBERS FROM $SrcClubCode TO $DstClubCode<br>";
echo 'Debug: ' . $DebugText . '<br>';
echo '<br>';
echo '<br>';
foreach ($aCustIds as $SrcCustId) {
    echo 'Processing SrcCustId: ' . $SrcCustId . '<br>';
    $DstCustId = GetNextMemberIdFromDatabase ($DstClubCode);
    echo 'DstCustId: ' . $DstCustId . '<br>';
    
    MvMembersTableCustId($SrcCustId,$DstCustId,$DstClubCode);
    MvEventsTableLeaderId($SrcCustId,$DstCustId,$DstClubCode); 
    MvReserveTableCustId($SrcCustId,$DstCustId,$DstClubCode); 

    //$DstCustId = $DstClubCode 
	//	. '_' 
	//	. substring($SrcCustId,pos('_',$SrcCustId),length($SrcCustId);
	echo '=============================<br><br>';
}


require('footer.php');


?>