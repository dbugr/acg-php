<?php

// event leaders home page


require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
    // Register a message to show the user
    $message = "Error: you are not logged in!";
    SessionRegister("message", $message);

    // Register where they came from
    $referer = $_SERVER['PHP_SELF'];
    SessionRegister("referer", $referer);

    // redirect to the login page
    $loc = "Location: /login.php";
    header($loc);
    exit;
}

$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Leader Stuff ' . $ClubCompanyName;
require('top.php');
?>

<?php
echo '<div align="left">';
echo '<table>';
echo '<tr><td><h3 align="left">Leader Stuff</h3></td></tr>';

echo '<tr><td>';
echo '<a href="/members/faq-leader.php">Leader FAQ (Frequently Asked Questions)</a><br>' . "\n";
echo '</tr></td>';

echo '<tr><td>';
echo '<a href="/members/leaders-event-stats.php">Leader Event Statistics</a><br>' . "\n";
echo '</td></tr>';

echo '<tr><td>';
echo '<a href="/members/member-preferences-rpt.php">
         Member Event Preferences Report</a><br>'
    . "\n";
echo '</td></tr>';

echo '<tr><td>';
echo '<a href="/members/birthdays.php">Members birthday list</a><br>' . "\n";
echo '</td></tr>';

echo '<tr>';
echo '<td>';
echo '<form method="POST" action="/members/eleader-report.php">';
echo '<input type="submit" value="My Events">';
echo '&nbsp;List all events for which you are a leader. Includes attendee contact info.';
echo '</form>';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
echo '<form method="POST" action="/members/eventcategories.php">';
echo '<input type="submit" value="Event Popularity">';
echo '&nbsp;Warning: takes about 60 seconds to run!';
echo '</form></td>';
echo '</tr>';

echo '</table></div>';


?>
    

<?php
require('footer.php');
?>

