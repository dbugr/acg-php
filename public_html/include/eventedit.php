<?php

// event edit co-leader objects / methods

class EventEdit
{

	var $CurrentEventId;
	var $CurrentEventLeaderId;
	var $CurrentEventLeaderName;
	var $CurrentEventCoLeaderId;
	var $CurrentEventCoLeaderName;
	var $aAllCurrentMemberNames;


	function ObtainPostEnvVar($sVarName)
	{

		//echo "ObtainPostEnvVar: " . $sVarName . "<br>";
		$sSanitizedValue = "";
		if (isset($_POST[$sVarName])) {
			$sValue = clean($_POST[$sVarName]);
			$sSanitizedValue = quotesqldata($sValue);
		}
		return ($sSanitizedValue);
	}


	function UserHasEditPrivilege($LoginUsername, $EventId)
	{
		global $MemberInfo;

		//LogMsg("UserHasEditPrivilege");
		$loginUsername = LoginUsername();
		$AuthLevel = AuthLevel($LoginUsername);
		$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
		$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
		$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';

		// obtain member information from members table
		$CustId = getCustomerID($LoginUsername);
		$LeaderId = getLeaderID($EventId, false);
		$aCoLeader = $this->GetCurrentCoLeader($EventId);
		$CoLeaderId = key($aCoLeader);

		// does the current user own the event
		$UserEditPrivilege = false;
		$UserEventLeader = false;
		$UserEventLeader = ($LeaderId == $CustId) ||
			($CoLeaderId == $CustId);
		$UserEditPrivilege = $UserEventLeader || $AdminLevel;

		//echo "UserHasEditPrivilege: CustId " . $CustId . "<br>";
		//echo "UserHasEditPrivilege: LoginUsername " . $LoginUsername . "<br>";
		//echo "UserHasEditPrivilege: AuthLevel: " . $AuthLevel . "<br>";
		//echo "UserHasEditPrivilege: AdminLevel: " . ($AdminLevel ? "true":"false") . "<br>";
		//echo "UserHasEditPrivilege: LeaderId: " . $LeaderId . "<br>";
		//echo "UserHasEditPrivilege: CoLeaderId: " . $CoLeaderId . "<br>";
		//echo "UserHasEditPrivilege: UserEventLeader: " . ($UserEventLeader ? "true" : "false") . "<br>";
		//echo "UserHasEditPrivilege: UserEditPrivilege: " . ($UserEditPrivilege ? "true" : "false") . "<br>";
		//exit;


		return ($UserEditPrivilege);
	}


	function GetEventName($EventId)
	{

		connect_mysqli_obj($dbh);

		$EventName = "";
		$sql = "select event_id, e_name from events where event_id = '{$EventId}'";
		//echo 'GetEventName: sql: '.$sql.'<br>';
		$sth = $dbh->query($sql);
		if (!$sth) {
			trigger_error(
				"MySQL GetEventName Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$NumRows = $sth->num_rows;
			if ($NumRows <= 0) {
				trigger_error(
					"MySQL GetEventName Query Error " . $sth->error,
					E_USER_ERROR
				);
			} else {
				$row = $sth->fetch_row();
				$EventName = $row[1];
				//echo 'GetEventName: ' . $EventName . '<br>';
			}
		}
		return ($EventName);
	}


	function UpdateLeader($EventId, $NewLeaderId)
	{

		//echo "UpdateCoLeader: " . "<br>";
		//echo "UpdateCoLeader: EventId: " .$EventId. "<br>";
		//echo "UpdateCoLeader: NewCoLeaderId: " .$NewCoLeaderId. "<br>";

		//echo 'UpdateCoLeader: updating database record<br>';
		connect_mysqli_obj($dbh);

		// use update statement
		$sql = "UPDATE events SET ";
		$sql .= "leader_id = '{$NewLeaderId}' ";
		$sql .= "where event_id = '{$EventId}'";

		//echo 'UpdateCoLeader: sql: ' . $sql . '<br>';

		$sth = $dbh->query($sql);
		if (!$sth) {
			$success = false;
			trigger_error(
				"MySQL UpdateCoLeader Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$success = true;
		}

		return ($success);
	}


	function UpdateCoLeader($EventId, $NewCoLeaderId)
	{

		//echo "UpdateCoLeader: " . "<br>";
		//echo "UpdateCoLeader: EventId: " .$EventId. "<br>";
		//echo "UpdateCoLeader: NewCoLeaderId: " .$NewCoLeaderId. "<br>";

		//echo 'UpdateCoLeader: updating database record<br>';
		connect_mysqli_obj($dbh);

		// use update statement
		$sql = "UPDATE events SET ";
		$sql .= "co_leader_id = '{$NewCoLeaderId}' ";
		$sql .= "where event_id = '{$EventId}'";

		//echo 'UpdateCoLeader: sql: ' . $sql . '<br>';

		$sth = $dbh->query($sql);
		if (!$sth) {
			$success = false;
			trigger_error(
				"MySQL UpdateCoLeader Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$success = true;
		}

		return ($success);
	}


	function GetAllCurrentMemberNames()
	{
		$ClubCode			= GetParameter('ClubCode');
		$dbh = "";

		//echo "GetAllCurrentMemberNames<br>";
		$this->aAllCurrentMemberNames = array();

		$dbh = connect_mysqli_obj($dbh);

		$CurrentDateTimeStamp = time();
		//echo 'GetAllCurrentMemberNames: CurrentDateTimeStamp: '.$CurrentDateTimeStamp.'<br>';

		$sql = "SELECT cust_id, m_firstname, m_lastname, u_date_expiration
		FROM members
		WHERE (m_club='" . $ClubCode . "')
		AND ('" . $CurrentDateTimeStamp . "' < unix_timestamp(u_date_expiration))
		ORDER BY m_firstname, m_lastname";
		//echo 'GetAllCurrentMemberNames: sql: '.$sql.'<br>';
		$sth = $dbh->query($sql);
		if (mysqli_error($dbh)) {
			trigger_error(
				"MySQL GetAllCurrentMemberNames Query Error " . mysqli_error($dbh),
				E_USER_ERROR
			);
		} else {
			$NumRows = $sth->num_rows;
			if ($NumRows <= 0) {
				trigger_error(
					'MySQL GetAllCurrentMemberNames Num Rows < zero ' . mysqli_error($dbh),
					E_USER_ERROR
				);
			} else {
				while ($row = $sth->fetch_row()) {
					//echo 'GetAllCurrentMemberNames: u_date_expiration: '.$row['u_date_expiration'].'<br>';
					//if ( (float)$CurrentDateTimeStamp < (float)strtotime($row['u_date_expiration']) ) {
					$cust_id = $row[0];
					$this->aAllCurrentMemberNames[$cust_id] = $row[1] . ' ' . $row[2];
					//echo 'GetAllMemberNames: ' . $this->aAllCurrentMemberNames[$cust_id] . '<br>';
					//}
				}
			}
		}

		//return($aAllCurrentMemberNames);
	}


	function DisplayLeaderSelectForm($EventId, $LeaderId)
	{

		//echo "DisplayLeaderSelectForm<br>";
		echo "<p>Please select the name of a club member who has agreed to become the leader for this event:</p><br>";

		$str  = '<form method="POST" action="/members/eedit-leader.php">' . "\n";
		$str .= '<input type=hidden name=event_id value="' . $EventId . '">' . "\n";
		//$str .='<input type=hidden name=leader_id value="'.$leader_id.'">'."\n";
		$str .= '<input type=hidden name=mode value="update">' . "\n";

		$str .= '<select name="leader_id">' . "\n";
		foreach ($this->aAllCurrentMemberNames as $varname => $value) {
			$str .= '<option ';
			if ($LeaderId == $varname) {
				$str .= "selected ";
			}
			$str .= 'value="';
			$str .= $varname;
			$str .= '">';
			$str .= $value;
			$str .= '</option>' . "\n";
		}
		$str .= '</select>' . "\n";
		$str .= '<br>' . "\n";
		$str .= '<BR>' . "\n";
		$str .= '<input type="submit" value="Submit">' . "\n";
		$str .= '</form>' . "\n";

		return ($str);
	}


	function DisplayCoLeaderSelectForm($EventId, $CoLeaderId)
	{

		//echo "DisplayCoLeaderSelectForm<br>";
		echo "<p>Please select the name of a club member who has agreed to help you lead this event (co-leader):</p><br>";

		$str  = '<form method="POST" action="/members/eedit-co-leader.php">' . "\n";
		$str .= '<input type=hidden name=event_id value="' . $EventId . '">' . "\n";
		//$str .='<input type=hidden name=coleader_id value="'.$coleader_id.'">'."\n";
		$str .= '<input type=hidden name=mode value="update">' . "\n";

		$str .= '<select name="coleader_id">' . "\n";
		foreach ($this->aAllCurrentMemberNames as $varname => $value) {
			$str .= '<option ';
			if ($CoLeaderId == $varname) {
				$str .= "selected ";
			}
			$str .= 'value="';
			$str .= $varname;
			$str .= '">';
			$str .= $value;
			$str .= '</option>' . "\n";
		}
		$str .= '</select>' . "\n";
		$str .= '<br>' . "\n";
		$str .= '<BR>' . "\n";
		$str .= '<input type="submit" value="Submit">' . "\n";
		$str .= '</form>' . "\n";

		return ($str);
	}

	function GetMemberName($cust_id)
	{
		//LogMsg('GetMemberName $cust_id: '.$cust_id);

		connect_mysqli_obj($dbh);

		$CoLeaderName = "";
		$sql = "select cust_id, m_firstname, m_lastname from members where cust_id = '{$cust_id}'";
		//echo 'GetMemberName: sql: '.$sql.'<br>';
		$sth = $dbh->query($sql);
		if (!$sth) {
			trigger_error(
				"MySQL GetMemberName Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$NumRows = $sth->num_rows;
			if ($NumRows <= 0) {
				trigger_error(
					"MySQL GetMemberName Query Error " . $sth->error,
					E_USER_ERROR
				);
			} else {
				$row = $sth->fetch_row();
				$CoLeaderName = $row[0] . ' ' . $row[1];
				//echo 'GetMemberName: ' . $CoLeaderName . '<br>';
			}
		}
		return ($CoLeaderName);
	}

	function GetCurrentLeader($event_id)
	{
		//LogMsg('GetCurrentLeader $event_id: '.$event_id);

		$aLeader = array();
		connect_mysqli_obj($dbh);

		$sql = "select leader_id from events where event_id = '{$event_id}'";
		//echo 'GetCurrentLeader: sql: '.$sql.'<br>';
		$sth = $dbh->query($sql);
		if (!$sth) {
			trigger_error(
				"MySQL GetCurrentLeader Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$NumRows = $sth->num_rows;
			if ($NumRows <= 0) {
				trigger_error(
					"MySQL GetCurrentLeader Query Error " . $sth->error,
					E_USER_ERROR
				);
			} else {
				while ($row = $sth->fetch_row()) {
					$LeaderId = $row[0];
					//echo 'CoLeaderId: ' . $CoLeaderId . '<br>';
				}
				//LogMsg('GetCurrentLeader $LeaderId: '.$LeaderId);
				$LeaderName = $this->GetMemberName($LeaderId);
				//LogMsg('GetCurrentLeader $LeaderName: '.$LeaderName);
			}
		}
		$aLeader["$LeaderId"] = $LeaderName;
		return ($aLeader);
	}



	function GetCurrentCoLeader($event_id)
	{
		//LogMsg('GetCurrentCoLeader $event_id: '.$event_id);
		$aCoLeader = array();
		//echo "GetCurrentCoLeader: db connecting<br>";
		connect_mysqli_obj($dbh);

		$sql = "select co_leader_id from events where event_id = '{$event_id}'";
		//echo 'GetCurrentCoLeader: sql: '.$sql.'<br>';
		//LogMsg("GetCurrentCoLeader sql: ".$sql);
		$sth = $dbh->query($sql);
		if (!$sth) {
			trigger_error(
				"MySQL GetCurrentCoLeader Query Error " . $sth->error,
				E_USER_ERROR
			);
		} else {
			$NumRows = $sth->num_rows;
			if ($NumRows <= 0) {
				trigger_error(
					"MySQL GetCurrentCoLeader Query Error " . $sth->error,
					E_USER_ERROR
				);
			} else {
				while ($row = $sth->fetch_row()) {
					//LogMsg("fetch_row: ".print_r($row,true));
					$CoLeaderId = $row[0];
					//echo 'CoLeaderId: ' . $CoLeaderId . '<br>';
				}
				//LogMsg('GetCurrentCoLeader $CoLeaderId: ',$CoLeaderId);
				$CoLeaderName = $this->GetMemberName($CoLeaderId);
				//LogMsg('GetCurrentCoLeader $CoLeaderName: '.$CoLeaderName);
			}
		}
		$sth->close();
		$aCoLeader["$CoLeaderId"] = $CoLeaderName;
		return ($aCoLeader);
	}
} // end class EventEdit
