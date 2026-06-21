<?php
// use php/apache system mkdir
// to create g2data dir with group=nobody

//$result = system("mkdir /home/adventrc/g2data");
//echo "System return value: ".$result."<br>";

//$result = system("chmod 755 /home/adventrc/g2data");
//echo "System return value: ".$result."<br>";

//$result = system("touch /home/adventrc/public_html/gallery2/config.php");
//echo "System return value: ".$result."<br>";

//$result = system("chmod 444 /home/adventrc/public_html/config.php");
//echo "System return value: ".$result."<br>";

//print "PHP runs under the user: [" . system('whoami') . "]<br>"; phpinfo();

//$result = system("touch /home/adventrc/public_html/gallery2/.htaccess");
//echo "System return value: ".$result."<br>";

//$result = system("chmod 666 /home/adventrc/public_html/gallery2/.htaccess");
//echo "System return value: ".$result."<br>";

$result = system("chmod 555 /home/adventrc/public_html/gallery2/.htaccess");
echo "System return value: ".$result."<br>";

?>