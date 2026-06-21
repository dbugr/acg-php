<?php
  // This script logs a user out and redirects 
  // to the home page

require('always.include.php');
//$debug = false;
//$debug = true;

//require('include.php');

  // Restore the session
  //session_start();

  // Is the user logged in?
  if (SessionIsRegistered("loginUsername")) {
     SessionUnregister("loginUsername");
     session_destroy();
  }
  else
  {
     // Register a message to show the user
     $message = "Error: you are not logged in!";
     SessionRegister("message",$message);
  }

  // Redirect the browser back to the home page
  $loc = "Location: /";
  header($loc);
