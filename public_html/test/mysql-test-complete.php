<?php
/* testmysql.php 12/17/2006 */


//$debug = true;
$debug = false;
$DirOffset = "../";  // relative offset to document root directory

//$RelOffset = (isset($DirOffset)) ? $DirOffset : "" ;
//$IncludesPath = dirname(__FILE__).'/'.$RelOffset.'includes/always.include.php';
$IncludesPath = '/shared/httpd/acg/public_html/include/always.include.php';
require ($IncludesPath);

// Connect to a session
////session_start();

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo '<html>';
echo '<body>';

echo "IncludesPath: " . $IncludesPath . "<br>";
echo '<p>Starting mysqltest.php script</p>';
//echo "loginUsername: " . $loginUsername . "<br>";

//$hostName = 'localhost';
//$ClubCode = 'acg';
//$databaseName = "adventrc";
//$username = "adventrc";
//$password = "sometingmon";

$aAuth = ConnectAuthArray();
echo '$aAuth[hostName]: ' 		. $aAuth['hostName'] 		. '<br>';
echo '$aAuth[username]: ' 		. $aAuth['username'] 		. '<br>';
echo '$aAuth[password]: ' 		. $aAuth['password'] 		. '<br>';
echo '$aAuth[databaseName] ' 	. $aAuth['databaseName'] 	. '<br>';
//echo '$aAuth[port] ' 			. $aAuth['port'] 			. '<br>';

echo "testmysql: connecting to database<br>";

$connection = mysqli_connect(
	$aAuth['hostName'],
	$aAuth['username'],
	$aAuth['password'],
	$aAuth['databaseName']
	);

if (mysqli_connect_errno($connection)){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}	

$query = "SELECT cust_id FROM members";
if (!($result = mysqli_query($connection,$query)))
	trigger_error("MySQL error: ". mysqli_errno() ." : ". mysqli_error(), E_USER_ERROR);

$numrows = mysqli_num_rows($result);
echo "mysqli query returned " . $numrows . " rows.";

$row = mysqli_fetch_array($result,MYSQLI_NUM);

foreach ($row as $key => $value) {
	$i = 0;
	print('$key: '.$key.'     $value: '.$value."<br>");
	$i++;
	if ($i >= 2) {
		break;
	}
}


echo 'THE END NUFF SAID!!!<BR>';
?>
</body>
</html>