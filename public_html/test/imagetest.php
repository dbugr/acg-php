<?php

/* Image handler object/functions */
/* 12/18/2006 */

require('always.include.php');

//session_start();
$debug = true;
$debug = false;
require('include.php');
require('image.php');
require(PhpPearDir().'/DB.php');
require('dbconnect.php');


?>
<html>
<body>
<?php
	//echo 'debug: '.$debug.'<br>';
	if (isset($debug) && ($debug)) {
		LogMsg('Running images.php script');
		//LogMsg('WebsiteRootDir: ' . WebsiteRootDir());
	}
	$cust_id = $_GET['cust_id'];
    $Image = new Image;
	$Image->ImagesCopyFromDisk2Database(WebTmpDir());

	if (isset($debug) && ($debug)) {
	    LogMsg('END SCRIPT NUFF SAID!!!');
	}
?>
</body>
</html>