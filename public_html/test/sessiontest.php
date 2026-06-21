<?php

$debug = true;
$debug = false;

require('always.include.php');

$retval = ini_set('session.save_path','/tmp/adventrc');
//$retval = ini_set('session.save_path','c:/xampp/tmp/adventrc');
print "RetVal: ".$retval."<br>";

//session_start();
//print "Requiring include.inc...<br>";
require('include.php');

//top($PHP_SELF,'FAQ ' . $ClubCompanyName);
$FileName = __FILE__;
$WebPageTitle = 'Session Test ' . $ClubCompanyName;
//print "FileName: " . $FileName . "<br>";
//print "WebPageTitle: " . $WebPageTitle . "<br>";
require('top.php');

?>

<h1>TEST SCRIPT TO INVESTIGATE SESSION TIMEOUTS</h1>

<p>
<?php

$s = session_name();
var_dump($s);
print "<br>";

$s = session_save_path();
var_dump($s);
print "<br>";

$s = ini_get('session.gc_maxlifetime');
var_dump($s);
print "<br>";

$s = session_id();
var_dump($s);
print "<br>";

$s = ini_get('session.id');
var_dump($s);
print "<br>";

//ini_set('session.gc_maxlifetime',30*60);



// change path to session files to increase session timeout
//$retval = session_save_path('/tmp/adventrc');


?>
</p>

<?php

include IncludesDir().'/footer.php';

?>