<?php
/* always.include.php */

$debug = true;
$debug = false;

# Include the Autoloader (see "Libraries" for install instructions)
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/secrets.php';
use Mailgun\Mailgun;


// Report all PHP errors (see changelog)
//error_reporting(E_ALL);

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);	

// Turn off all error reporting
error_reporting(0);

// was script run via command line? run via http?
define("CLI", !isset($_SERVER['HTTP_USER_AGENT']));

function MemberRetentionLoginUsername()
{
	return "MSSTORY";
}

function GetPath($index)
{
	static $path;

	$str = "";
	if (!isset($path)) {
		// get absolute path of current file
		$path['FILE_PATH'] = realpath(dirname(__FILE__));
		$path['DIR_BASE_NAME'] = basename(__DIR__);
		$dir_offset = "";
		if (strcmp($path['DIR_BASE_NAME'], 'public_html') != 0) {
			$dir_offset = '/..';
		}
		//const APPLICATION_PATH = realpath(FILE_PATH . $dir_offset);
		$path['APPLICATION_PATH'] = realpath($path['FILE_PATH'] . $dir_offset);
		$path['DOCROOT_PATH'] = $path['APPLICATION_PATH'] . '/public_html';
		$path['INCLUDE_PATH'] = $path['APPLICATION_PATH'] . '/include';
		$path['LIB_PATH'] = $path['APPLICATION_PATH'] . '/../lib';
		$path['CSS_PATH'] = $path['APPLICATION_PATH'] . '/../css';
		$path['JS_PATH'] = $path['APPLICATION_PATH'] . '/../js';
	}

	$str = $path[$index];
	return ($str);
}

function WebsiteVersionNumber()
{

	$VersionNumber = '3.1.0';
	return ($VersionNumber);
}

date_default_timezone_set("America/New_York");
// setup correct time zone
putenv('TZ=EST5EDT');
//ini_set('display_errors', 'On');

function GetSystemHostname()
{
	//$Hostname = `hostname` or 
	$Hostname = php_uname('n') or
		trigger_error('ERROR GetSystemHostname(): ', E_USER_ERROR);
	$Hostname = trim($Hostname);

	// docker container will return: php
	// non-docker will return name of host: FLEX, YOGA, etc
	return ($Hostname);
}


function IsDevelopment()
{
	// get application environment value
	$app_env = get_cfg_var('app_env');
	//print('app_env: '.$app_env."<br>");
	//$app_env = ini_get('app_env');
	//print('app_env: '.$app_env."<br>");
	//$app_env = getenv('app_env');
	//print('app_env: '.$app_env."<br>");
	if ($app_env == "prod") {
		$is_development = false;
	} elseif ($app_env == "dev") {
		$is_development = true;
	} else {
		$is_development = true;
		LogMsg('ERROR: unknown APP_ENV value: ' . $app_env);
		trigger_error('ERROR unknown APP_ENV value: ' . $app_env, E_USER_ERROR);
	}
	//LogMsg("APP_ENV: ".$app_env);
	//LogMsg("IsDevelopment: ".$app_env);

	return ($is_development);
}

// register a session
function SessionRegister($key, $value)
{
	$_SESSION[$key] = $value;
}

// unregister a session
function SessionUnregister($key)
{
	if (isset($_SESSION) && (array_key_exists($key, $_SESSION))) {
		$_SESSION[$key] = NULL;
		unset($_SESSION[$key]);
	}
}

// is the session registered?
function SessionIsRegistered($key)
{
	if (isset($_SESSION) && (array_key_exists($key, $_SESSION))) {
		$registered = true;
	} else {
		$registered = false;
	}
	return ($registered);
}


if (isset($debug) and $debug) {
	LogMsg("HTTP_HOST: " . $_SERVER['HTTP_HOST']);
	LogMsg("Hostname: " . $Hostname);
	LogMsg("APPLICATION_PATH: " . GetPath('APPLICATION_PATH'));
	LogMsg("gethostname: " . gethostname());
	//LogMsg("php_os: ". PHP_OS);
	LogMsg("php_uname: " . php_uname());
	LogMsg("IsDevelopment: " . IsDevelopment());
	//LogMsg("IsLaptop(): " . IsLaptop());
}


/*
// was script called with https ??
function IsHttps() {
	// new code added 6/25/2019 by Chuck

	$isSecure = false;
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$isSecure = true;
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		$isSecure = true;
	}
	$REQUEST_PROTOCOL = $isSecure ? 'https://' : 'http://';
	return($REQUEST_PROTOCOL);
}
*/

//==================================================
function mysqlconnect(&$connection)
{

	$aAuth = array(
		'dbhostname' => GetParameter("dbhostname"),
		'dbusername' => GetParameter("dbusername"),
		'dbpassword' => GetParameter("dbpassword"),
		'dbname' => GetParameter("dbname"),
	);
	//LogMsg('aAuth: '.print_r($aAuth,true));

	//LogMsg("Connecting to mysqli");
	$connection = @mysqli_connect(
		$aAuth['dbhostname'],
		$aAuth['dbusername'],
		$aAuth['dbpassword'],
		$aAuth['dbname']
	);
	
	if (!$connection) {
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	}

	if (mysqli_connect_errno()) {
		trigger_error("MySQL error: " 
			. mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	}

	//if (!mysqli_select_db($connection, $aAuth['dbname']))
	if (!mysqli_select_db($connection, $aAuth['dbname']))
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

	//LogMsg("Successfully connected to mysql!");
	return $connection;
}

//WARNING GLOBAL VARIABLE
mysqlconnect($connection);

function LoginUsername()
{
	if ( isset($_SESSION['loginUsername']) ) {
		return $_SESSION['loginUsername'];
	} else {
		return "";
	}
}

/*
function PhpIniSet()
{
	global $debug;

	$memory_limit = GetParameter('memory_limit');
	$retval = ini_set('memory_limit', $memory_limit);
	if (!$retval) {
		LogMsg("ini_set memory_limit failed!!!");
		LogMsg('current ini_get("memory_limit"): ' . ini_get('memory_limit'));
	}

	// adjust session garbage collection time
	$sess_gc_maxlifetime = GetParameter('session.gc_maxlifetime');
	$retval = ini_set('session.gc_maxlifetime', $sess_gc_maxlifetime);
	if (!$retval) {
		LogMsg("ini_set session.gc_maxlifetime failed!!!");
		LogMsg('current ini_get("session.gc_maxlifetime"): ' . ini_get('session.gc_maxlifetime'));
	}

	// change session save path to work around shared server timeouts problem
	$sess_save_path = GetParameter('session.save_path');
	$retval = ini_set('session.save_path', GetPath('APPLICATION_PATH') . $sess_save_path);
	if (!$retval) {
		ini_set('session.save_path', '/tmp');
	}

	if (isset($debug) && $debug) {
		LogMsg('ini_get(include_path): ' . ini_get('include_path'));
		LogMsg('ini_get(session.save_path): ' . ini_get('session.save_path'));
	}
}

// set php ini_set values
// must do this BEFORE calling session_start()!!
PhpIniSet();
*/

// if this is NOT a command line invocation....
if (!CLI) {
	// start the session!
	session_start();
}


//============================================================
function JoinURL()
{
	return GetParameter('vd') . "join.php";
}


//============================================================
function JoinPostURL()
{
	return GetParameter('vd') . "join-post.php";
}


function PublicDomainName($DomainName = null)
{
  if (isset($_SERVER['HTTP_HOST'])) {
    $http_host = $_SERVER['HTTP_HOST'];
	} else if (isset($domainname)) {
		$http_host = $DomainName;
	} else {
		$http_host = "adventureclub.info";
	}

	return($http_host);
}


//============================================================
function GetParameter($index)
{
	static $aParam;

	$DefaultDomainName = 'adventureclub.info';

	$aParam = array();

	$aParam['ClubCompanyName']		= 'Adventure Club of Gainesville';
	$aParam['ClubCode'] 			= "gnv";
	$aParam['MembershipFee']		= "$3";
	$aParam['ShortClubName']		= 'ACG';
	$aParam['ClubOwnerName']		= 'Nancy Henry';

	$aParam['GalleryURL'] = PublicDomainName($DefaultDomainName) . '/gallery3';
	//$aParam['GalleryRandomImageURL'] = PublicDomainName().'/gallery3/randimg';
	//$aParam['GalleryRandomImageURL'] = "http://www.adventureclub.info/gallery2/main.php?g2_view=core.DownloadItem&g2_itemId=38757&g2_serialNumber=2";

	$aParam['EnableCustomErrorHandler'] = true;
	//$aParam['EnableCustomErrorHandler'] = false;

	$aParam['ContactPhoneNumber1'] 	= "352-262-1162";
	$aParam['ContactPhoneNumber2'] 	= "";
	$aParam['FullContactName'] 			= "Nancy";
	$aParam['SnailMailAddress1'] 		= "7817 SW 95th Lane";
	$aParam['SnailMailAddress2'] 		= "Gainesville, Florida 32608";
	$aParam['SnailMailAddress3'] 		= "";

	$aParam['DeveloperEmailAddr'] 	= 'chuck.broker@gmail.com';
	$aParam['BounceEmailAddr'] 			= 'chuck.broker@gmail.com';
	//$aParam['BounceEmailAddr'] 			= 'sailfl@gmail.com';

	$aParam['BetaTestEmailAddr'] 		= 'mystardust@protonmail.com';
	//$aParam['BetaTestEmailAddr'] 	= 'chuck.broker@gmail.com';
	//$aParam['BetaTestEmailAddr'] 	= 'chucky@airpost.net';

	$aParam['EmailNoticesFrom'] 		= "donotreply@adventureclub.info"; //. PublicDomainName();
	$aParam['EmailNoticesTo'] 			= "chuck.broker@gmail.com,MsStory96@aol.com,sharonjulien@gmail.com";
	$aParam['SendNewEvents'] 				= true;
	$aParam['AllowNoComments'] 			= true;
	$aParam['eNewsletterNumDaysChecked'] = 4;

	$aParam['EmailHeaders'] =
	"From: " . $aParam['EmailNoticesFrom'] . "\n" .
	"Reply-To: " . $aParam['EmailNoticesFrom'] . "\n" .
	"Return-Path: " . $aParam['BounceEmailAddr'] . "\n" .
	//"Return-Path: " . $aParam['EmailNoticesFrom'] . "\n" .
	"X-Mailer: PHP/" . phpversion();

	$aParam['dbhostname'] 	= get_cfg_var('dbhostname') ?: 'localhost:3306'; 	// server name (overridable via php.ini)
	$aParam['dbusername'] 	= 'advclub'; 			// username 
	$aParam['dbpassword'] 	= 'sometingmon';		// password 
	$aParam['dbname'] 			= 'advclub';			// database name 

	// php.ini variables currently set in php.ini
	// bump session garbage collection time to 40 minutes * 60 seconds = 2400 seconds
	//$aParam['session.gc_maxlifetime'] = 2400;
	// change session save path to work around shared server timeouts problem
	//$aParam['session.save_path'] = GetPath('APPLICATION_PATH') . '/var/session';
	//$aParam['memory_limit'] = '128M';
	//$aParam['MaxExecutionTime'] = 180 * 60;		// maximum script execution time
	//$aParam['PearDBDriver'] 	= 'mdb2.php';		// which pear DB driver to use

	$aParam['Hostname'] = GetSystemHostname();

	if (IsDevelopment()) {
		// development pc: workstation or personal laptop
		$aParam['path'] 								= '/shared/httpd/acg/public_html/include/';
		$aParam['EmailsDeveloperMode'] 	= true;		// redirect emails to log file
		$aParam['EmailsBetaTestMode'] 	= false;	// redirect emails to log file
		$aParam['vd'] 									= '/join/';
		$aParam['home'] 								= "https://" . PublicDomainName("acg.loc") . "";
		//$aParam['home'] 								= "https://acg.loc/";
		$aParam['PhotosPath'] 					= "/shared/httpd/acg/var/photos";
		$aParam['LogFilePath'] 					= '/shared/httpd/acg/var/log';
		$aParam['LogFilename'] 					= '/advclub.log';
		$aParam['LogMsgPrint'] 					= "";		// null or "" to suppress override
		$aParam['LogNotices'] 					= false;	// instruct error_handler to log notices
		$aParam['JoinLogFilename']			= 'join-post.log';
		$aParam['ErrorWebPageWebUrl'] 	= 'http://' . PublicDomainName("acg.loc") . '/onerror.html';
		$aParam['OnErrorDispErrorWebPage'] 	= true;
		//$aParam['ErrorWebPageTitleText'] 	= 'Adventure Club System Error';
		//$aParam['ErrorWebPageContactPhoneNumber'] = '';
		//$aParam['MysqlDumpCommandPath'] = '/xampp/mysql/bin/mysqldump';
		//$aParam['TarGzCommandPath'] 	= 'C:\bin\UnxUtils\usr\local\wbin\tar';
		//$aParam['DBBackupFilePath'] 	= '/var/www/acg.keysolv.com/dbbackups';
	} else {	// must be production: 'lamp-s-1vcpu-1gb-nyc3-01':
		// production server, digitalocean.com
		$aParam['path'] 								= '/var/www/acg.keysolv.com/public_html/include/';
		$aParam['EmailsDeveloperMode'] 	= false;		// redirect emails to log file
		$aParam['EmailsBetaTestMode'] 	= false;	// redirect emails to log file
		$aParam['vd'] 									= '/join/';
		$aParam['home'] 								= "https://" . PublicDomainName($DefaultDomainName) . "";
		$aParam['PhotosPath'] 					= "/var/www/acg.keysolv.com/var/photos";
		$aParam['LogFilePath'] 					= '/var/www/acg.keysolv.com/var/log';
		$aParam['LogFilename'] 					= '/advclub.log';
		$aParam['LogMsgPrint'] 					= "";		// null or "" to suppress override
		$aParam['JoinLogFilename']			= 'join-post.log';
		$aParam['LogNotices'] 					= false;	// instruct error_handler to log notices
		$aParam['ErrorWebPageWebUrl'] 	= 'http://' . PublicDomainName($DefaultDomainName) . '/onerror.html';
		$aParam['OnErrorDispErrorWebPage'] 	= true;
		//$aParam['ErrorWebPageTitleText'] 	= 'Adventure Club System Error';
		//$aParam['ErrorWebPageContactPhoneNumber'] = '';
		//$aParam['MysqlDumpCommandPath'] = '/usr/bin/mysqldump';
		//$aParam['TarGzCommandPath'] 	= '/bin/tar';
		//$aParam['DBBackupFilePath'] 	= '/var/www/acg.keysolv.com/dbbackups';
	}

	if (array_key_exists($index, $aParam)) {
		$parameter = $aParam[$index];
	} else {
		$parameter = 'ERROR-' . $index . '-ARRAY-KEY-DOES-NOT-EXIST';
	}

	return ($parameter);
}



function EmailDeveloper($email_subject, $email_body)
{
	//global $email_from;

	$email_headers = 	GetParameter('EmailHeaders');
	$DeveloperEmailAddr = GetParameter('DeveloperEmailAddr');
	if (IsDevelopment()) {
		$s = 'FAKING developer email on laptop'
			. "\n"
			. '$DeveloperEmailAddr: ' . $DeveloperEmailAddr
			. "\n"
			. "subject: "
			. $email_subject
			. "\n"
			. "body: "
			. $email_body;
		LogMsg($s, false);
	} else {
		$MessageSent = mail($DeveloperEmailAddr, $email_subject, $email_body, $email_headers);
		LogMsg("EmailDeveloper: " . $MessageSent, false);
	}
}


//=============================================
// log some text to the log file with date/time stamp
// prototype: LogMsg("Text Msg",EchoToStdOut,Die/Exit,"ExtraTxtMsg");
function LogMsg($message, $print = false, $die = false, $sql = "")
{

	$prnt = GetParameter('LogMsgPrint');
	if (!empty($prnt)) {
		// override calling parameter with value in config.php
		$print = $prnt;
	}

	// use name of currently running script in log text
	$PhpSelf = $_SERVER['PHP_SELF'];
	//$PhpSelf = $_SERVER['SCRIPT_FILENAME'];
	$info = pathinfo($PhpSelf);
	// name of file that included this file
	$sScriptFileName = $info['basename'];
	//print_r($info);
	$sLogFileName = '/' . $info['filename'] . '.log';

	$LogFilePath = GetParameter('LogFilePath');
	$LogFilename = GetParameter('LogFilename');
	$LogFilePathName = $LogFilePath . $LogFilename;

	//if (isset($debug) && $debug) {
	//	LogMsg('$LogFilePath: '.$LogFilePath,false);
	//}

	// WARNING!!! GLOBAL VARIABLE
	if ((isset($_SESSION)) && (array_key_exists('loginUsername', $_SESSION))) {
		$loginUsername = LoginUsername();
	}

	$sTime = date('Y.m.d H:i:s', time());
	$message = $sTime
		. ' :'
		. $sScriptFileName
		. ": "
		. $message;
	if (!empty($sql)) {
		$message .= ' loginUsername: ' . $loginUsername;
		$message .= ' SQL: ' . $sql;
	}
	$message .= "\r\n";

	if (isset($print) && $print) {
		(!CLI) ? print "<pre>" : "";
		print $message;	//."\r\n";
		(!CLI) ? print "</pre>" : "";
	}
	if (!$handle = fopen($LogFilePathName, 'a+')) {
		echo "Cannot open file ($LogFilePathName)";
		exit;
	}
	if (fwrite($handle, $message) === FALSE) {
		echo "Cannot write to file ($LogFilePathName)";
		exit;
	}
	fclose($handle);

	if (!empty($die)) {
		//die('gotta quit');
		exit;		// if true, exit/die
	}

	//return($success);
}



//=============================================
// log some text to the database "log" table with date/time stamp
function LogMsgDb($message)
{
	global $connection;
	//echo 'LogMsg: '.$message.'<br>';

	$message = addslashes($message);

	$sTime = date('Y.m.d H:i:s', time());

	if ((isset($_SESSION)) && (array_key_exists('loginUsername', $_SESSION))) {
		$loginUsername = LoginUsername();
	}

	// use insert statement
	$sql = "INSERT INTO log ";
	$sql .= "(log_timestamp,";
	if (isset($loginUsername))
		$sql .= "log_loginUsername,";
	$sql .= "log_data) ";
	$sql .= "VALUES (";
	$sql .= "'{$sTime}',";
	if (isset($loginUsername))
		$sql .= "'{$loginUsername}',";
	$sql .= "'{$message}'";
	$sql .= ") ";

	//echo 'LogMsg sql: ' . $sql . '<br>';

	$result = @mysqli_query($connection, $sql);
	if (!($result)) {
		$success = false;
		trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
	} else {
		$success = true;
	}
	//echo "after query execution<br>";
	//echo "Success? ".$success . "\n<br>";


	return ($success);
}



function backTrace($context)
{
	// Get a backtrace of the function calls
	$trace = debug_backtrace();

	$calls = "\n<br><br>Backtrace:";

	// Start at 2 -- ignore this function (0) and the customHandler() (1)
	for ($x = 2; $x < count($trace); $x++) {
		$callNo = $x - 2;
		$calls .= "\n<br>  {$callNo}: {$trace[$x]["function"]} ";
		$calls .= "(line {$trace[$x]["line"]} in {$trace[$x]["file"]})";
	}


	if (isset($trace[2])) {
		$calls .= "\n<br>Variables in {$trace[2]["function"]} ():";
	} else {
		$calls .= "\n<br>Variables: " . '$trace[2]' . " NOT SET!";
	}

	// Use the $context to get variable information for the function
	// with the error
	foreach ($context as $name => $value) {
		if (!empty($value) && (!is_object($value)))
			if (is_array($value)) {
				$calls .= "\n<br>" . $name . " is " . print_r($value, true);
				//$calls .= "\n<br>  {$name} is {print_r($value,true)}";
			} else {
				$calls .= "\n<br>  {$name} is {$value}";
			} else
			$calls .= "\n<br>  {$name} is NULL";
	}
	return ($calls);
}


function ErrorHandlerCustom($number, $string, $file, $line, $context)
{
	global $hostName;
	//global $public_domain_name;

	$error_html_filename = 'error.html';
	$msg = "";
	$severity = "";

	$default = false;
	switch ($number) {
		case E_USER_ERROR:
			$msg .= "\nERROR on line {$line} in {$file}.";
			$severity = "ERROR";
			$stop = true;
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$msg .= "\nWARNING on line {$line} in {$file}.";
			$severity = "WARNING";
			$stop = true;
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			$msg .= "\nNOTICE on line {$line} in {$file}.";
			$severity = "NOTICE";
			$stop = false;
			break;
		default:
			$msg .= "UNHANDLED ERROR on line {$line} in {$file}.";
			$severity = "UNHANDLED ERROR";
			$stop = false;
			$default = true;
	}
	$msg .= "\n";
	//$msg .= "endmsg";
	$subject = $severity . ": \"{$string}\" (error #{$number}).";
	$body = $subject;
	$body .= "\n<br>" . date("YmdHis");
	$body .= "\n<br>" . $msg;
	$body .= '\n<br>';
	$error = $body;
	if (($number != E_NOTICE) && ($number != E_USER_NOTICE)) {
		$error .= backTrace($context);
	}
	if ((!CLI) && array_key_exists('REMOTE_ADDR', $_SERVER)) {
		$error .= "\n<br>Client IP: {$_SERVER["REMOTE_ADDR"]}";
	}
	$error .= "\n<br>==============================================\n<br>";
	// do not log default errors
	if (!$default) {
		LogMsg($error);
	}
	if ($stop == true) {
		// Throw away the buffer
		if (ob_get_level()) {
			ob_end_clean();
		}
		// log error
		// send email to software developer
		if (IsDevelopment()) {
			//echo $error;
			EmailDeveloper($subject, $body);
		} else {
			EmailDeveloper($subject, $body);
			//display error message to user
			DisplayErrorPage($error);
			//$loc = PublicDomainName()."/".$error_html_filename;
			//header("Location: " . $loc);
			//print "<pre>{$error}</pre>";
			exit();
		}
	}
}


if (GetParameter('EnableCustomErrorHandler')) {
	set_error_handler("ErrorHandlerCustom");
}


function DisplayErrorPage($error)
{

	$message = "";
	//loginUsername = LoginUsername();
	//LogMsg("loginUsername: " . $loginUsername);
	if (true) {
		//if (SessionIsRegistered("loginUsername")  and ($loginUsername == 'chuckles') ) {
		$message = $error;
	}

	$s = '<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">';
	$s .= '<html>';
	$s .= '<head>';
	$s .= '<title>Adventure Club System Error</title>';
	$s .= '</head>';
	$s .= '<body>';
	$s .= '<h1 style="color : red;">Adventure Club of Gainesville System Error</h1>';
	$s .= '<p>';
	$s .= 'An error occurred while responding to your request. The error has been ';
	$s .= 'recorded and the system administrator has been notified.';
	$s .= '<br />';
	$s .= '<br />';
	$s .= 'Sorry for the inconvenience, we will correct the error as soon as possible.';
	$s .= '<br />';
	$s .= '<br />';
	$s .= 'Please click here to return to the <a href="' . PublicDomainName() . '/">club home page</a>';
	$s .= '</p>';
	$s .= '<address>To contact the club owner: <a href="' . PublicDomainName() . '/contact.php">Contact</a></address>';
	$s .= '<hr>';
	$s .= $message;
	$s .= '</body>';
	$s .= '</html>';

	print $s;
}

/*
function RedirectToPrimaryDomainName ($FilePathName) {
    global $_SERVER;
    global $debug;

    if (!empty($_SERVER['HTTP_HOST'])) {
	if (strtolower($_SERVER['HTTP_HOST']) == 'www.actfl.com' or
		strtolower($_SERVER['HTTP_HOST']) == 'actfl.com') {
		// close session
		// redirect to 'www.adventureclub.info'

		session_destroy();
		// redirect to home page
		if (basename($FilePathName) == "index.php") {
			$loc = "Location: "
			    . 'http://'
			    . PublicDomainName()
			    . '/';
		} else {
			$loc = "Location: "
			    . 'http://'
			    . PublicDomainName()
			    . '/'
			    . basename($FilePathName);
		}

		if (isset($debug) and $debug) {
			LogMsg('Redirecting from www.actfl.com to '.PublicDomainName());
		}

		header($loc);
		exit;
	}
    }
}
*/

function DisplayLogin($MemberInfo)
{
	if (SessionIsRegistered("loginUsername")) {
		printf(
			'<b>Welcome, %s.</b>&nbsp; (%s)<br>',
			$MemberInfo->GetMemberFirstName(),
			$MemberInfo->GetMemberAuthLevelRaw()
		);
		echo '<img border="0" src="';
		echo '/images/lock.jpg" width="11" height="14">&nbsp;';
		echo '<a href="/logout.php">Logout</a><p>';
	} else {
		// display login form
		echo '<form action="/login.php" method="post" id=form1 name=form1>' . "\n";
		echo '<table width="100%" border="2" cellspacing="0" cellpadding="0" bgColor="#e1bc88">' . "\n";
		echo '<tr><td align="right"><font size="1">Username: </font>' . "\n";
		echo '<input id="loginUsername" name="loginUsername" value="" size="10" style="width: 135px; background-color: #dedede">';
		echo '</td></tr>' . "\n";

		echo '<tr><td align="right"><font size="1">Password: </font>' . "\n";
		echo '<input type="password" id="loginPassword" name="loginPassword" size="10" style="width: 135px; background-color: dedede">' . "\n";
		echo '</td></tr>' . "\n";

		echo '<tr>' . "\n";
		echo '<td align="right">' . "\n";
		echo '<input type="submit" name="Login" value="Login">' . "\n";
		echo '</td>' . "\n";
		echo '</tr>' . "\n";
		echo '</table>' . "\n";
		echo '</form>' . "\n";
	}
}


//		include.inc file
//         misc functions and defines for activity club

//-----------------------------------------------------------------------------------
function HumanDates($dateraw)
{

	// take raw mysql date string in dd/mm/yyyy format,
	// swap day/month, return result: mm/dd/yyyy
	$year = substr($dateraw, 0, 4);
	$month = substr($dateraw, 5, 2);
	$day = substr($dateraw, 8, 2);

	$dateswap = "";
	if (!empty($year) && !empty($month) && !empty($day)) {
		$dateswap =
			$month
			. "/"
			. $day
			. "/"
			. $year;
	} elseif (!empty($year) && !empty($month)) {
		$dateswap =
			$month
			. "/"
			. $year;;
	}

	return ($dateswap);
}

//-----------------------------------------------------------------------------------

function MySqlDate($dateraw)
{

	// take raw mysql date string in dd/mm/yyyy format,
	// swap day/month, return result: mm/dd/yyyy
	$month = substr($dateraw, 0, 2);
	$day = substr($dateraw, 3, 2);
	$year = substr($dateraw, 6, 4);

	if (!empty($year) && !empty($month) && !empty($day)) {
		$dateswap =
			$year
			. "-"
			. $month
			. "-"
			. $day;
	} else {
		$dateswap = "";
	}

	return ($dateswap);
}


function formatDate2($dt)
{

	// takes yyyy/mm/dd and swaps fields, resulting
	// in mm/dd/yyyy
	preg_match(
		"#^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$#",
		$dt,
		$parts
	);

	$datestr = $parts[2] . "/" . $parts[3] . "/" . $parts[1];  // month/day/year

	return ($datestr);
}



function formatDate($dt)
{

	// takes dd/mm/yyyy and swaps dd/mm, resulting
	// in mm/dd/yyyy
	preg_match(
		"#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#",
		$dt,
		$parts
	);

	$datestr = $parts[2] . "/" . $parts[1] . "/" . $parts[3];  // month/day/year

	return ($datestr);
}



// -------------------------------------------------
// Untaint user data for MySQL input
// i.e. strip back slashes and replace all single
// quotes with two single quotes
function quotesqldata($input)
{
	if (!ini_get('magic_quotes_gpc'))
		$input = addslashes($input);
	//$input = strtr($input,array('_' =>'\_','%' => '\%'));

	return ($input);
}

// -------------------------------------------------
// Untaint user data
function clean($input, $maxlength=null)
{
	if (!is_null($maxlength)) {
		$sanitized = substr($input, 0, $maxlength);
	}
	$sanitized = trim($input);
	//$sanitized = htmlentities($input, ENT_QUOTES, 'UTF-8');
	$sanitized = quotesqldata($input);
	
	return($sanitized);
}




// Display any messages that are set, and then
// clear the message
function showMessage()
{
	global $message;

	// Is there an error message to show the user?
	if (SessionIsRegistered("message")) {
		echo "<h3><font color=\"red\">$message</font></h3>";
		// Clear the error message
		$message = "";
		SessionUnregister("message");
	}
}

/*	 
   // -------------------------------------------------
   // Show whether the user is logged in or not
   function showLogin()
   {
      global $loginUsername;

      // Is the user logged in?
      if (SessionIsRegistered("loginUsername")) {
				$loginUsername = LoginUsername();
        return($loginUsername);
      } else { 
        return ("");
			}
   }
*/

// Show the user a login or logout button. Also, show them membership
// buttons as appropriate.
function loginButtons()
{
	if (SessionIsRegistered("loginUsername")) {
		echo "\n\t<td><input type=\"submit\" name=\"logout\" value=\"Logout\"></td>\n";
		echo "\n\t<td><input type=\"submit\" name=\"account\" value=\"Change Details\"></td>\n";
	} else {
		echo "\t<td><input type=\"submit\" name=\"login\" value=\"Login\"></td>\n";
		echo "\n\t<td><input type=\"submit\" name=\"account\" value=\"Become a Member\"></td>\n";
	}
}

// -------------------------------------------------
// Get the cust_id using loginUsername
function getCustomerID($loginUsername)
{
	global $connection;
	$ClubCode 			= GetParameter('ClubCode');

	$open = false;
	//echo "GetCustomerID 1<br>";
	if (!isset($loginUsername)) {
		//echo "GetCustomerID 2<br>";
		trigger_error(
			"Invalid LoginUsername (" . $loginUsername . ") in getCustomerID!",
			E_USER_ERROR
		);
		return ("");
	} else {


		//echo "GetCustomerID 4<br>";
		$query = "SELECT cust_id, user_name, m_club FROM members " .
			"WHERE ((user_name='" . $loginUsername . "') " .
			"AND (m_club='" . $ClubCode . "'))";

		if (($result = @mysqli_query($connection, $query)))
			$row = mysqli_fetch_array($result);
		else
			trigger_error("MySQL query error", E_USER_ERROR);

		//echo "GetCustomerID 5<br>";
		//@mysqli_close($connection);

		return ($row["cust_id"]);
	}
} // end function




function AuthLevel($loginUsername)
{
	global $connection;
	$ClubCode 			= GetParameter('ClubCode');

	// Is the user logged in and were there no errors from a previous
	// validation?  If so, look up the authenitication level
	// and return value to caller
	if (!SessionIsRegistered("loginUsername")) {
		return ("");
	} else {
		// We find the u_auth_level through the users table, using the
		// session variable holding their loginUsername.
		$query = "SELECT cust_id, user_name, u_auth_level
              FROM members
              WHERE user_name = \"$loginUsername\"
			  and cust_id like \"$ClubCode%\"";

		if (!($result = @mysqli_query($connection, $query))) {
			trigger_error("MySQL query error", E_USER_ERROR);
			return (NULL);
		} else
			$row = @mysqli_fetch_array($result);

		$result = NULL;
		if ($row) {
			if ($row["u_auth_level"] == "Maint") {
				$result = "Admin";
			} else {
				$result = $row["u_auth_level"];
			}
			return ($result);
		}
	}
}  // end function


// -------------------------------------------------
// given event_id, get the leader_id
function getLeaderID($event_id, $coleader)
{
	global $connection;

	if (!isset($event_id)) {
		trigger_error(
			"Invalid event_id (" . $event_id
				. ") in getLeaderID!",
			E_USER_ERROR
		);
		return ("");
	} else {

		// We find the cust_id through the users table, using the
		// session variable holding their loginUsername.

		if ($coleader) {
			// obtain co_leader_id
			$query = "SELECT event_id, co_leader_id
	                FROM events
	                WHERE event_id = '" . $event_id . "' ";

			if (($result = @mysqli_query($connection, $query)))
				$row = mysqli_fetch_array($result);
			else
				trigger_error("MySQL query error in getLeaderID", E_USER_ERROR);
			$result = $row["co_leader_id"];
		} else {
			// obtain leader id
			$query = "SELECT event_id, leader_id
			FROM events
			WHERE event_id = '" . $event_id . "' ";

			if (($result = @mysqli_query($connection, $query)))
				$row = mysqli_fetch_array($result);
			else
				trigger_error("MySQL query error in getLeaderID", E_USER_ERROR);
			$result = $row["leader_id"];
		}

		return ($result);
	}
} // end function


// -------------------------------------------------
// given event_id, get the login username
function getLoginUsername($cust_id)
{
	global $connection;

	if (!isset($cust_id)) {
		trigger_error(
			"Invalid cust_id (" . $cust_id
				. ") in getLoginUsername!",
			E_USER_ERROR
		);
		return ("");
	} else {

		// We find the username through the users table
		$query = "SELECT user_name
                FROM members
                WHERE cust_id = '" . $cust_id . "' ";

		if (($result = @mysqli_query($connection, $query)))
			$row = mysqli_fetch_array($result);
		else
			trigger_error("MySQL query error in getLoginUsername", E_USER_ERROR);

		return ($row["user_name"]);
	}
} // end function

function GetMemberNameFromDB($select)
{
	global $connection;
	$ClubCode 			= GetParameter('ClubCode');


	//echo "<br>loginUsername: ".$loginUsername;
	if (SessionIsRegistered("loginUsername")) {
		// get username and level
		$loginUsername = LoginUsername();
		$query = "SELECT m_firstname,m_lastname, cust_id 
		       FROM members 
					WHERE (user_name='" . $loginUsername . "') 
					AND	(cust_id like '" . $ClubCode . "%');";

		//LogMsg("GetMemberNameFromDB Query:".$query);
		// Open a connection to the DBMS
		// execute query
		$result = @mysqli_query($connection, $query);
		if (!$result) {
			LogMsg("Error Query in GetMemberNameFromDB returned zero results");
		} else {
			$row = @mysqli_fetch_array($result);
			$FirstName = $row['m_firstname'];
			$LastName = $row['m_lastname'];
		}
	}

	//echo "<br>FirstName: " . $FirstName;
	//echo "<br>LastName: " . $LastName;
	if ($select == "firstname")
		return ($FirstName);
	else
		return ($LastName);
}

//========================================
// get user data, store in object variables
class MemberAttrib
{

	var $aMemberRecord;

	//=======================
	function GetMemberAuthLevelRaw()
	{

		$AuthLevel = $this->aMemberRecord['u_auth_level'];
		return ($AuthLevel);
	} // end GetMemberAuthLevelRaw()


	//=======================
	function GetMemberAuthLevel()
	{
		//LogMsg('user_name: '.$this->aMemberRecord['user_name']);
		//LogMsg('this->aMemberRecord: '.$this->aMemberRecord['u_auth_level']);
		$AuthLevel = $this->aMemberRecord['u_auth_level'];
		if ($AuthLevel == 'Maint') {
			$AuthLevel = 'Admin';
		}
		return ($AuthLevel);
	} // end GetMemberAuthLevel()

	//=======================
	function GetMemberId()
	{

		$MemberId = $this->aMemberRecord['cust_id'];
		return ($MemberId);
	} // end GetMemberId()


	//=======================
	function GetMemberFirstName()
	{

		$MemberName = $this->aMemberRecord['m_firstname'];
		return ($MemberName);
	} // end function

	//=======================
	function GetMemberLastName()
	{

		$MemberName = $this->aMemberRecord['m_lastname'];
		return ($MemberName);
	} // end function


	//=======================
	function GetMemberRecord()
	{
		global $connection;
		$ClubCode 			= GetParameter('ClubCode');

		// get member/users data from database and store array in local variable
		//echo "Starting GetMemberRecord";
		//LogMsg('$_SESSION[loginUsername]: '.$_SESSION['loginUsername']);
		//LogMsg('session_loginUsername: '.$_SESSION['loginUsername']);
		if (isset($_SESSION['loginUsername'])) {
			$loginUsername = LoginUsername();
			// We find the u_auth_level through the users table, using the
			// session variable holding their loginUsername.
			$query = "SELECT *
				  FROM members
				  WHERE user_name = \"$loginUsername\"
				  and cust_id like \"$ClubCode%\"";
			//LogMsg('query: '.$query);

			if (!($result = @mysqli_query($connection, $query))) {
				trigger_error("MySQL query error", E_USER_ERROR);
			} else {
				$row = @mysqli_fetch_array($result);
			}
			$this->aMemberRecord = $row;
			//LogMsg('this->aMemberRecord: '.print_r($this->aMemberRecord,true));
		}
	}  // end GetMemberRecord()

} // end class MemberAttrib

// get database record for currently logged in member
// this will initialize the objects variables so that
// the internal functions will return correct results
$MemberInfo = new MemberAttrib;
$MemberInfo->GetMemberRecord();
//LogMsg('$MemberInfo->GetMemberFirstName(): '.$MemberInfo->GetMemberFirstName());
//LogMsg('$MemberInfo->GetMemberAuthLevelRaw(): '.$MemberInfo->GetMemberAuthLevelRaw());

function FormatMemberCompactName()
{

	$loginUsername = LoginUsername();
	//$cust_id = getCustomerID($loginUsername);
	$FirstName = GetMemberNameFromDB("firstname");
	$LastName = GetMemberNameFromDB("lastname");

	$FirstName = strtoupper(substr($FirstName, 0, 1)) .
		substr($FirstName, 1, strlen($FirstName) - 1);

	$CompactName = $FirstName . strtoupper(substr($LastName, 0, 1));

	return ($CompactName);
}


function MetaTags($filepath)
{
	global $ClubCompanyName;

	//echo "filepath: " . $filepath . "<br>";
	$filebasename = basename($filepath, ".php");
	//echo "filebasename: " . $filebasename . "<br>";

	$MetaTagString = "";
	switch ($filebasename) {
		case 'index':
			$MetaTagString = '<meta name="keywords" content="index, gainesville, adventure, club, florida, activities, indoor, outdoor, singles, singles, couples, all ages, hiking, biking, canoeing, kayaking, rock climbing, skydiving, hang gliding, wine tasting, dining, dancing" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . ', Florida �  A fun group where singles and couples of all ages go hiking, biking, canoeing, kayaking, rock climbing, skydiving, hang gliding, wine tasting, dining, dancing and more.">' . "\n";
			break;
		case 'faq':
			$MetaTagString = '<meta name="keywords" content="faq, gainesville, adventure, club, florida, events, member, leader, fun" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Get your questions about ' . $ClubCompanyName . ' answered here.">' . "\n";
			break;
		case 'leader':
			$MetaTagString = '<meta name="keywords" content="leader, gainesville, adventure, club, florida, event, faq, free, membership" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . 'Event Leader frequently asked questions FAQ answered here.">' . "\n";
			break;
		case 'join':
			$MetaTagString = '<meta name="keywords" content="join, gainesville, florida, adventure, club, membership, kayaking" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Join the ' . $ClubCompanyName . ', explore your life!">' . "\n";
			break;
		case 'elist-pub':
			$MetaTagString = '<meta name="keywords" content="calendar, gainesville, florida, adventure, club, frisbee " >' . "\n";
			$MetaTagString .= '<meta name="description" content="Calendar of Events for ' . $ClubCompanyName . '">' . "\n";
			break;
		case 'eview-pub':
			$MetaTagString = '<meta name="keywords" content="events, gainesville, florida, adventure, club, details, movies " >' . "\n";
			$MetaTagString .= '<meta name="description" content="Event details for ' . $ClubCompanyName . '">' . "\n";
			break;
		case 'shop':
			$MetaTagString = '<meta name="keywords" content="shop, gainesville, adventure, club, florida, store, purchase, t-shirts" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . ' Online Store">' . "\n";
			break;
		case 'underconstruction':
			$MetaTagString = '<meta name="keywords" content="construction, shop, gainesville, adventure, club, florida, store, purchase" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . ' Online Store">' . "\n";
			break;
		case 'contact':
			$MetaTagString = '<meta name="keywords" content="contact, gainesville, florida, adventure, club" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Comments, questions, suggestions for ' . $ClubCompanyName . '">' . "\n";
			break;
		case 'links':
			$MetaTagString = '<meta name="keywords" content="links, gainesville, adventure, club, florida" >' . "\n";
			$MetaTagString .= '<meta name="description" content="gainesville area resources and organizations providing discounts to ' . $ClubCompanyName . ' members">' . "\n";
			break;
		case 'login':
			$MetaTagString = '<meta name="keywords" content="login, gainesville, adventure, club, florida, friends" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Login to fun with the ' . $ClubCompanyName . '">' . "\n";
			break;
		case 'startclub':
			$MetaTagString = '<meta name="keywords" content="own, club, gainesville, adventure, club, florida, franchise, ownership" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Start your own Adventure Club">' . "\n";
			break;
		case 'subscribe':
			$MetaTagString = '<meta name="keywords" content="NewsLetter, gainesville, adventure, club, gainesville, florida, mailing, list, hiking, eNewsletter" >' . "\n";
			$MetaTagString .= '<meta name="description" content="Fill in this form to subscribe to the ' . $ClubCompanyName . ' newsletter mailing list">' . "\n";
			break;
		case 'policies':
			$MetaTagString = '<meta name="keywords" content="' . $ClubCompanyName . ', policies, members, events, leaders, florida" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . ' policies for members, leaders and events. Read the fine print here.">' . "\n";
			break;
		default:
			$MetaTagString = '<meta name="keywords" content="default, gainesville, adventure, club, florida, activities, indoor, outdoor, single, divorced" >' . "\n";
			$MetaTagString .= '<meta name="description" content="' . $ClubCompanyName . ' - - Recess for Adults! Home of indoor and outdoor fun for single, married, widowed and divorced people of all ages">' . "\n";
			break;
	}

	$MetaTagString .= '<meta http-equiv="Content-Language" content="en-us">' . "\n";
	$MetaTagString .=  '<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">' . "\n";
	$MetaTagString .= '<meta name="robots" content="index,follow">' . "\n";
	$MetaTagString .= '<meta name="author" content="' . $ClubCompanyName . '">' . "\n";
	//$MetaTagString .= '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >'."\n";

	//echo "MetaTagString: " . $MetaTagString . "<br>";

	return ($MetaTagString);
} // end function MetaTags

// creates and returns an anchor/link to a photo
function PhotoAnchorStr($series = "")
{
	//global $public_domain_name;
	//global $DirOffset;
	//global $laptop;

	$pic_id = array();
	$pic_id[0] = rand(1, 3);
	$pic_id[1] = rand(4, 6);
	$pic_id[2] = rand(7, 9);

	//#cccccc
	//#3399CC

	$pic = 0;
	$img_path = array();
	$i = 0;
	foreach ($pic_id as $pic) {
		switch ($pic) {
			case 1:
				$img_path[$i] = '/images/m1.jpg"  alt="hang gliding" ';
				break;
			case 2:
				$img_path[$i] = '/images/m2.jpg" alt="skydiving" ';
				break;
			case 3:
				$img_path[$i] = '/images/m3.jpg" alt="sailing" ';
				break;
			case 4:
				$img_path[$i] = '/images/m4.jpg" alt="Scuba Diving" ';
				break;
			case 5:
				$img_path[$i] = '/images/m5.jpg" alt="girls night" ';
				break;
			case 6:
				$img_path[$i] = '/images/m6.jpg" alt="horseback riding" ';
				break;
			case 7:
				$img_path[$i] = '/images/m7.jpg" alt="river tubing" ';
				break;
			case 8:
				$img_path[$i] = '/images/m8.jpg" alt="sky dive group" ';
				break;
			case 9:
				$img_path[$i] = '/images/m9.jpg" alt="ultralight aircraft" ';
				break;
			default:
				$img_path[$i] = '/images/m10.jpg" alt="Come join in the fun!" ';
		}
		$i += 1;
	}

	$s =""
	.'<table border="0" cellpadding="0" cellspacing="0" bordercolor="#111111" id="AutoNumber7" height="180">'
	.'<tr>'
	.'<td>'
	.'  <div id="randomImage">'
	.'	<center>'
	.'  <img src="'
	.	$img_path[0]
	. 	' width="164" height="164"  border="0">'
	.'  </div>'
	.'</td>'

	.'<td>'
	.'  <div id="randomImage">'
	.'  <img src="'
	.	$img_path[1]
	. 	' width="164" height="164"  border="0">'
	.'  </div>'
	.'</td>'

	.'<td>'
	.'  <div id="randomImage">'
	.'  <img src="'
	.	$img_path[2]
	. 	' width="164" height="164"  border="0">'
	.'  </div>'
	.'</td>'
	.'</tr>'
	.'</table>';
		
	return ($s);
}


function expired_membership($date_expiration)
{

	// if time() < u_date_expired return false (unexpired) membership
	// else return true expired membership
	if (!empty($date_expiration))
		$expiration_stamp = strtotime($date_expiration);
	else
		$expiration_stamp = strtotime('1970-01-01');
	$time_stamp = time();
	if ((float) $time_stamp < (float) $expiration_stamp)
		$expired = 0;
	else
		$expired = 1;

	//DisplayVariable("valid", $valid);

	return ($expired);
}

function GetMemberExpirationDates()
{
	global $connection;

	$query = "SELECT cust_id, u_date_expiration FROM members";
	//             WHERE user_name = '$loginUsername'";

	// Execute the query
	if (!($result = @mysqli_query($connection, $query))) {
		trigger_error(
			"MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
			E_USER_ERROR
		);
		return (0);
	}

	$MembershipExpirationDates = array();
	while ($u_row = @mysqli_fetch_array($result)) {
		$u_cust_id = $u_row['cust_id'];
		$MembershipExpirationDates[$u_cust_id] = $u_row['u_date_expiration'];
	}

	return ($MembershipExpirationDates);
}

if (isset($debug) and $debug) {
	LogMsg('END always.include.php');
}

// mail function prototype
// mail ($emailNoticesTo,$email_subject,$email_body,$email_from);
function OLDMailWrapper($email_to, $email_subject, $email_body, $email_from)
{

	$DeveloperEmailAddr = GetParameter('DeveloperEmailAddr');
	$BetaTestEmailAddr = GetParameter('BetaTestEmailAddr');
	$MessageSent = false;

	LogMsg('===============================================');
	LogMsg('Running MailWrapper(): '.$email_to.' | '
		.$email_subject.' | '.$email_from);
	if (GetParameter('EmailsDeveloperMode')) {
		if (IsDevelopment()) {
			// write email data to log file
			$s = 'EmailsDeveloperMode' . "\n"
					. "DeveloperEmailAddr: " . $DeveloperEmailAddr . "\n"
					. "Originalemail_toAddr: " . $email_to . "\n"
					. 'email_to: '.$email_to."\n"
					. 'email_subject: '.$email_subject."\n";
			LogMsg($s, false);
		} else {
			// send email to developer
			$MessageSent = mail($DeveloperEmailAddr, $email_subject, $email_body, $email_from);
			$s = 'EmailsDeveloperMode' . "\n"
					. "DeveloperEmailAddr: " . $DeveloperEmailAddr . "\n"
					. "Originalemail_toAddr: " . $email_to . "\n"
					. 'email_to: '.$email_to."\n"
					. 'email_subject: '.$email_subject."\n";
			LogMsg($s . '$MessageSent: ' . $MessageSent, false);
			if (!$MessageSent) {
				//mail('DeveloperEmailAddr','ERROR sending email'.$email_subject);	// testing
				LogMsg('FAILURE sending email ' . $email_subject . ' message NOT sent');
			}
		}
	} else if (GetParameter('EmailsBetaTestMode')) {
		// write email data to log file
		if (IsDevelopment()) {
			$s = 'EmailsBetaTestMode' . "\n"
				. "BetaTestEmailAddr: " . $BetaTestEmailAddr . "\n"
				. "Original email_to Addr: " . $email_to . "\n"
				. 'email_to: '.$email_to."\n"
				. 'email_subject: '.$email_subject."\n";
			LogMsg($s, false);
		} else {
			$BetaEmailTo = $DeveloperEmailAddr . ',' . $BetaTestEmailAddr;
			$MessageSent = mail($BetaEmailTo, $email_subject, $email_body, $email_from);
			$s = 'EmailsBetaTestMode' . "\n"
				. "BetaEmailTo: " . $BetaEmailTo . "\n"
				. "Original email_to Addr: " . $email_to . "\n"
				. 'email_to: '.$email_to."\n"
				. 'email_subject: '.$email_subject."\n";
			LogMsg($s . '$MessageSent: ' . $MessageSent, false);
			if (!$MessageSent) {
				//mail('DeveloperEmailAddr','ERROR sending email'.$email_subject);	// testing
				LogMsg('FAILURE sending email ' . $email_subject . ' message NOT sent');
			}
		}
	} else {
		LogMsg('sending PRODUCTION email $email_to: ' . $email_to 
			. '  email_subject: ' . $email_subject);
		$MessageSent = mail($email_to, $email_subject, $email_body, $email_from);
		if (!$MessageSent) {
			mail(
				GetParameter('DeveloperEmailAddr'),
				'ERROR sending email' . $email_subject,
				'ERROR sending email TO: ' . $email_to . '  SUBJECT: ' . $email_subject
			);
/*
			mail(
				GetParameter('EmailNoticesTo'),
				'ERROR sending email' . $email_subject,
				'ERROR sending email TO: ' . $email_to . '  SUBJECT: ' . $email_subject
			);
			LogMsg('ERROR sending email TO: ' . $email_to . '  SUBJECT: ' . $email_subject);
*/
		}
	}
	return ($MessageSent);
}


function MailWrapper($EmailNoticesTo,$email_subject,$email_body,$email_headers) {

    $email_to = $EmailNoticesTo;

    if (IsDevelopment()) {
        $s = 'EmailsDeveloperMode: email NOT sent' . "\n"
            . 'email_to: ' . $email_to . "\n"
            . 'email_subject: ' . $email_subject . "\n"
            . 'email_body: ' . $email_body . "\n";
        LogMsg($s, false);
        return null;
    }

    # Instantiate the client.
    //print("instantiating client\n");
    $mgClient = Mailgun::create(MAILGUN_API_KEY,
        'https://api.mailgun.net/v3/mg.adventureclub.info/messages');
    $domain = "mg.adventureclub.info";
    
    // inside the loop
    $params = array(
    'from'    => 'noreply@adventureclub.info <noreply@adventureclub.info>',
    'to'      => $email_to,
    //  'to'      => 'chuck.segal@proton.me',
    'subject' => $email_subject,
    'text'    => $email_body
    );
    //print_r($params);

    # Make the call to the client.
    $jsonResponse = $mgClient->messages()->send($domain, $params);
    //$MessageSent = mail($email_to, $email_subject, $email_body, $email_from);
    if (!$jsonResponse) {
        LogMsg('ERROR sending PRODUCTION email $email_to: ' . $email_to 
        . '  email_subject: ' . $email_subject 
        . ' JSON: '.print_r($jsonResponse,true));
    } else {
        LogMsg('Sent PRODUCTION email $email_to: ' . $email_to 
        . '  email_subject: ' . $email_subject
        . ' JSON: '.print_r($jsonResponse,true));
    }

    unset($mgClient);

    return($jsonResponse);
}

