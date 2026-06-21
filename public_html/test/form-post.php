<?php

if (isset($_POST['g-recaptcha-response'])) {
	$captcha = $_POST['g-recaptcha-response'];
	echo "captcha: " . $captcha . "</br>";
} else {
	$captcha = false;
}

if (!$captcha) {
	//Do something with error
	echo "Empty g-recaptcha-response!";
} else {
	$secret = '6Lc2iqkUAAAAAF_Yuy-htwPNEbDkgdc6EX-jONjj';
	$post_link = "https://www.google.com/recaptcha/api/siteverify?secret="
		. $secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
	$response = file_get_contents($post_link);
	echo "response: " . $response . "</br>";
	$json = json_decode($response);
	$success = $json->success;
	//LogMsg("SUCCESS: ".$success);
	if ($success == false) {
		//Do something with error
		echo "FAILED THE CAPTCHA TEST! captcha: " . $captcha;
	} else {
		//... The Captcha is valid you can continue with the rest of your code
		echo "PASSED THE CAPTCHA TEST!";
	}
}
