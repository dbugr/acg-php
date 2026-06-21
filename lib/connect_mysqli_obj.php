<?php

/* connect to mysql and select database */
/* handle errors */

require_once('always.include.php');

//==================================================
// mysqli object oriented connection
function connect_mysqli_obj(&$dbh) {
	//LogMsg("db_connect_obj");

	$dbauth = array (
		'dbhostname' => GetParameter("dbhostname"),
		'dbusername' => GetParameter("dbusername"),
		'dbpassword' => GetParameter("dbpassword"),
		'dbname' => GetParameter("dbname"),
	);
	
	//LogMsg('Connection parameters: '.print_r($dbauth,true));

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
