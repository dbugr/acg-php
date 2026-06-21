<?php
// Name - TailLog.bat
// Description - read last "n" lines of server
// log files and display on web page
// 8/30/2012 chuck segal

// ASSUMES the existance of tail.exe and tmp folders

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');


$LogFilePath = GetParameter('LogFilePath');
$JoinPostLog 		= $LogFilePath . '/join-post.log';

// linux
$OutputFilePath = GetParameter('LogFilePath') . '/' . 'logfiletail.txt';
//'/home/advclub/var/logfiletail.txt';
//$OutputFilePath = '/home/advclub/var/logfiletail.txt';

// windows
//$OutputFilePath = '/prj/livestats-prj/tmp';

$ApacheAccess 	= '/xampp/apache/logs/access.log';
$ApacheError 	= '/xampp/apache/logs/error.log';
$MysqlError 	= '/xampp/mysql/data/mysqli_error.log';

$ApacheLogPath 	= '\xampp\apache\logs';
$MysqlLogPath 	= '\xampp\mysql\data';
$FileSpec 		=  '*.log';

	// Is the user logged in and were there no errors from a previous
	// validation?  If so, look up the customer for editing
	// Is the user logged in?
	$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';	
	if (!SessionIsRegistered("loginUsername") || (!$AdminLevel) )	{
		// Register a message to show the user
		$message = "Error: you are not logged in!";
		SessionRegister("message",$message);

		// Register where they came from
		if (isset($_GET['event_id']))
			$referer = $_SERVER['PHP_SELF'] . "?event_id=" . clean($_GET['event_id']);
		else
			$referer = $_SERVER['PHP_SELF'];
		SessionRegister("referer",$referer);

		// redirect to the login page
		$loc = "Location: /login.php";
		LogMsg('REDIRECTING to $loc: '.$loc.'   $referer: '.$referer);
		header($loc);
		exit;
	}





// linux
function TailPath() { return('/usr/bin/tail ');};

// windows
//function TailPath() { return('/unxutils/usr/local/wbin/tail.exe ');};

function RunExecCommand ($command,$execute) {

	//LogMsg("Command: ".$command,true);
	//$dbpassword = GetParameter('dbpassword'); 		// database password
	//$command = str_replace('%secret%',$dbpassword,$command);
	//LogMsg("Command: ".$command,true);
	$ReturnCode = "";
	if ($execute) {
		//$output = array();
		//print "COMMAND IS: ".$command."\n\n";
		$LastLine = exec($command, $output, $ReturnCode);
		if ($ReturnCode) {
			print("ERROR: return code: $ReturnCode");
			print(" ERROR: LastLine: $LastLine\n");
		} else {
			//LogMsg("SUCCESS: return code: $ReturnCode",true);
			//LogMsg("SUCCESS: LastLine: $LastLine",true);
		}
	}
  
    return($output);
}


function GetTailLogFileContents($FQLogFilePathName,$lines,$OutputFilePath)
{

	$command = TailPath().' -n '.$lines.' '.$FQLogFilePathName." >$OutputFilePath";
	$ResultCode = RunExecCommand($command,true);
	$output = file_get_contents($OutputFilePath); 
	$output = $command."\n\n".$output."\n\n";
	//unlink("/tmp/phptailfile.txt");
	
	return($output);
}

function GetDirectoryListing($DirPath,$FileSpec){

	$OutputFilePath = '/prj/livestats-prj/tmp';

	$command = 'dir '.$DirPath.'\\'.$FileSpec." >$OutputFilePath/phpdirfile.txt";
	//$command = 'dir \xampp\apache\logs\*.log'." >$OutputFilePath/phpdirfile.txt";
	$ResultCode = RunExecCommand($command,true);
	$output = file_get_contents($OutputFilePath."/phpdirfile.txt"); 
	$output = $command."\n\n".$output."\n\n";
	//unlink("/tmp/phptailfile.txt");

	return($output);
}


//======================================================
// code execution starts here...

$lines = 2000;

print "<html><body>";
print "<pre>";

print GetTailLogFileContents($JoinPostLog,$lines,$OutputFilePath);

//print GetTailLogFileContents($ApacheAccess,$lines);
//print GetTailLogFileContents($ApacheError,$lines);
//print GetTailLogFileContents($MysqlError,$lines);

//print GetDirectoryListing($ApacheLogPath,$FileSpec);
//print GetDirectoryListing($MysqlLogPath,$FileSpec);

print "</pre>";
print "<body><html>";
