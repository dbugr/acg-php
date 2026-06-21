<?php

/* write PHP user to web browser */

echo 'User PHP is running under: ' . get_current_user() . '<br>';

$FQFilePath = '/home/adventrc/var/webtmp/test.txt';

if (touch($FQFilePath)) {
	echo "SUCCESS creating/updating file<br>";
} else {
	echo "FAILURE creating/updating file<br>";
}

?>