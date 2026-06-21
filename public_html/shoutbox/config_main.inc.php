<?
/*********************************************************************************************************
 This code is part of the ShoutBox software (www.gerd-tentler.de/tools/shoutbox), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

//////////////////////////////////////////////////////////////////////////////////////////////////////////
// This is the MAIN configuration file
//////////////////////////////////////////////////////////////////////////////////////////////////////////

//========================================================================================================
// Database settings
//========================================================================================================

  // leave these fields empty if you don't want to use a database:
  $db_server = "mysql";      // server name
  $db_user = "advclub";             // user name
  $db_pass = "sometingmon";                 // user password
  $db_name = "advclub";                 // database name

  // don't change unless you know what you're doing:
  $tbl_name = "Shoutbox";        // table name
  $fld_id = "ID";                // field name: ID
  $fld_timestamp = "Timestamp";  // field name: timestamp
  $fld_name = "Name";            // field name: name
  $fld_email = "EMail";          // field name: e-mail
  $fld_text = "Text";            // field name: text

//========================================================================================================
// Other settings
//========================================================================================================

  // shout-box language: de, en, fi, fr, it, nl, pl, sl, sv
  $language = "en";

  // administrator password (can delete entries; needs PHP >= 4.1.0)
  $adminPass = "gville";

  // shout-box folder (WEB path, e.g. /webtools/shoutbox)
  $boxFolder = "/shoutbox";

  // shout-box width (pixels)
  $boxWidth = 180;

  // shout-box height (pixels)
  $boxHeight = 300;

  // maximum entries in shout-box (higher values = more traffic!)
  $boxEntries = 80;

  // refresh shout-box every .. seconds (lower values = more traffic!)
  $boxRefresh = 1200;

  // input fields position (side or bottom)
  $inputsPosition = "bottom";

  // message order: ASC (new at bottom) or DESC (new on top)
  $messageOrder = "ASC";

  // maximum word length (0 = no limit)
  // NOTE: should be 0 for non-European languages (Asian, Arabic, etc.)
  $wordLength = 30;

  // maximum text length (0 = no limit)
  $textLength = 300;

  // adjust hour of server time (e.g. 1, 2, -1, -2, etc.)
  $timeOffset = 3;

  // allow URLs (true = yes, false = no)
  $allowURLs = true;

  // allow HTML tags (true = yes, false = no)
  $allowHTML = true;

  // allow UBB codes (true = yes, false = no)
  $allowUBBs = true;

  // enable message IDs against spam bots (true = yes, false = no; needs PHP >= 4.1.0)
  // NOTE: if enabled, you must start a session in your PHP script (see readme.txt)
  $enableIDs = false;

  // enable link check against spam bots (true = yes, false = no)
  // NOTE: works only if $allowURLs and/or $allowHTML is false
  $enableLinkCheck = false;

  // enable user agent check against spam bots (true = yes, false = no)
  // NOTE: if enabled, some browsers might be mistaken for spam bots
  $enableAgentCheck = false;

  // valid user agents; don't change unless you know what you're doing
  $agents = array("Mozilla", "Opera", "Lynx", "Mosaic", "amaya", "WebExplorer", "IBrowse", "iCab");

  // bad words
  $nonos = array("fuck", "asshole");

//========================================================================================================
?>
