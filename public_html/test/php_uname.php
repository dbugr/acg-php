<?php
// test output of various php os detection functions

$debug=true;

// was script run via command line? run via http?
define("CLI", !isset($_SERVER['HTTP_USER_AGENT']));

// global variables
$Hostname = GetSystemHostname();

function GetSystemHostname() {

	if(CLI) {
		//LogMsg('CLI: '.CLI);
		// linux hostname path
		$Hostname = `/bin/hostname.exe` or 
			trigger_error('ERROR GetSystemHostname(): ',E_USER_ERROR);
		// windows hostname path
		//$s = `c:/windows/system32/hostname.exe` or 
		//	trigger_error("ERROR GetSystemHostname(): ".$php_errormsg,E_USER_ERROR);
	} else {
		$Hostname = $_SERVER['HTTP_HOST'];
	}
	$Hostname = trim($Hostname);
	
	return($Hostname);
}

function IsLaptop () {

	$UName=php_uname();
	if(stripos($UName,'cruiser-pc') || stripos($UName,'cheetah')) {
		$laptop = true;
	} else {
		$laptop = false;
	}
	return($laptop);
}

if (isset($debug) && $debug) {
	LogMsg("HTTP_HOST: " . $_SERVER['HTTP_HOST']);
	LogMsg("Hostname: " . $Hostname);
	LogMsg("GetSystemHostname: " . GetSystemHostname());
	//LogMsg("php_os: ". PHP_OS);
	LogMsg("php_uname: ". php_uname());
	LogMsg("IsLaptop(): " . IsLaptop());
}


function LogMsg($msg) {

	print $msg . "<br/>";

}

