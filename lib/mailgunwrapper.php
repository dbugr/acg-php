<?php

require_once "always.include.php";
require_once __DIR__ . '/secrets.php';

// mailgun send test email

error_reporting(E_ALL);

# Include the Autoloader (see "Libraries" for install instructions)
require_once '/home/chucky/.composer/vendor/autoload.php';
use Mailgun\Mailgun;
//use Amp\Http\Client\HttpClientBuilder;
//use Amp\Http\Client\Request;
//use Amp\Loop;


function mailgunWrapper($EmailNoticesTo,$email_subject,$email_body,$email_headers) {

    //LogMsg('sending PRODUCTION email $email_to: ' . $email_to 
    //    . '  email_subject: ' . $email_subject);

    $email_to = $EmailNoticesTo;

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


$EmailNoticesTo = 'chuck.segal@proton.me';
$email_subject = 'mgtest advclub php to chuck.segal@proton.me';
$email_body = 'mgtest advclub php to chuck.segal@proton.me';
$email_headers = '';

$jsonResponse = mailgunWrapper($EmailNoticesTo,$email_subject,$email_body,$email_headers);
print("jsonResponse: \n");
print_r($jsonResponse);
