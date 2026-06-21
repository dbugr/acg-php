<?php

/* connect to mysql and select database */
/* handle errors */

require_once('always.include.php');

//==================================================
// mysqli object oriented connection
function connect_mysqli_obj(&$dbh) {
	LogMsg("db_connect_obj");

	$dbauth = array (
		'dbhostname' => GetParameter("dbhostname"),
		'dbusername' => GetParameter("dbusername"),
		'dbpassword' => GetParameter("dbpassword"),
		'dbname' => GetParameter("dbname"),
	);
	LogMsg('Connection parameters: '.print_r($dbauth,true));

	$dbh = new mysqli(
		//'localhost',
		$dbauth["dbhostname"],
		$dbauth["dbusername"],
		$dbauth["dbpassword"],
		$dbauth["dbname"]
		//'3306'
	);

	if (mysqli_connect_errno()) {
		LogMsg("mysqli connection failed!");
		trigger_error("MySQL connect error: ". $dbh->connect_error);
		die('ERROR connecting to mysql database: '.$dbh->connect_error.'<br>');
	}
	//LogMsg("mysqli connection succeeded!");
	return($dbh);
}


$dbh = connect_mysqli_obj($dbh);
$ClubCode			= GetParameter('ClubCode');
$CurrentDateTimeStamp = time();

$sql = "SELECT cust_id, m_firstname, m_lastname, u_date_expiration
FROM members
WHERE (m_club='" . $ClubCode . "')
AND ('" . $CurrentDateTimeStamp . "' < unix_timestamp(u_date_expiration))
ORDER BY m_firstname, m_lastname";
Print "Running sql: ".$sql."<br>";
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
    LogMsg('MySQL GetAllCurrentMemberNames Num Rows < zero ' . mysqli_error($dbh));
    //trigger_error(
    //  'MySQL GetAllCurrentMemberNames Num Rows < zero ' . mysqli_error($dbh),
    //  E_USER_ERROR
    //);
  } else {
    while ($row = $sth->fetch_row()) {
      //echo 'GetAllCurrentMemberNames: u_date_expiration: '.$row['u_date_expiration'].'<br>';
      //if ( (float)$CurrentDateTimeStamp < (float)strtotime($row['u_date_expiration']) ) {
      $cust_id = $row[0];
      print $row[0];
      //$this->aAllCurrentMemberNames[$cust_id] = $row[1] . ' ' . $row[2];
      //echo 'GetAllMemberNames: ' . $this->aAllCurrentMemberNames[$cust_id] . '<br>';
      //}
    }
  }
}



/*
// PDO connection
function db_connect(&$conn) {

	$aAuth = ConnectAuthArray();
        $ConnectString = 'mysql:'
                 ."host=".$aAuth['hostName']
		.";"
                 ."dbname=".$aAuth['databaseName'];
	$username = $aAuth['username'];
	$password = $aAuth['password'];

	try {
    		$conn = new PDO($ConnectString, $username, $password);
    		//$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    		// set the PDO error mode to exception
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		LogMsg("PDO connection successfull");
    	}
	catch(PDOException $e)
    	{
	    	LogMsg("PDO connection failed: " . $e->getMessage();
	        trigger_error("MySQL PDO connect error: ". $e->getMessage(),E_USER_ERROR);
    	}
}
*/
