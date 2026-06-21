<?php

print "<pre>";
print '$_SERVER[\'HTTP_USER_AGENT\']: '.$_SERVER['HTTP_USER_AGENT'];
print "\n";

// was script run via command line? run via http?
define("CLI", array_key_exists('HTTP_USER_AGENT',$_SERVER) && !isset($_SERVER['HTTP_USER_AGENT']));

print 'CLI: '.CLI."\n";

print "ALL DONE FOLKS!!\n";

print "</pre>";

?>