<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Event Leader - ' . $ClubCompanyName;
require('top.php');

?>
<div id="centercontent">
  <h3>Become an Event Leader!</h3>

  Become an Event Leader!
  An Event Leader for Adventure Club of Gainesville might:
  <ul>
    <li>Be someone who just moved to the area</li>
    <li>Be a member who wants to lead events</li>
    <li>Be tired of TV or computers</li>
    <li>Be 9-to-5'ers or retirees who desire more from life</li>
    <li>Want a free membership with the club</li>
    <li>Have a special interest like GPS, astronomy, wine tasting, quilting, or hiking</li>
    <li>Feel like something's missing</li>
    <li>Like helping other folks have fun!</li>
  </ul>
  <p>
    As a leader you are eligible for bonuses:<br>
    To receive one month's free membership, just lead one event during a
    calendar month that is signed up for (on the ACG website) by seven
    or more current Adventure Club members (not member's guests). At the
    end of the month we will examine the Events lists and leaders who
    qualified will receive the next month's membership for free.
    And even better, if you, as a leader, lead a complex event
    during one calendar month wherein hours and hours of pre-event
    time are incurred, then you will receive the next two months free.
    (an example of a qualifying event would be: scalloping replete
    with boat rentals, caravanning, etc.)
  </p>

  <p>
    Think about it!
  </p>
  <ul>
    <li>Got a fishing boat and want to sponsor outings? </li>
    <li>Tired of attending dancing lessons alone? Looking for some dancing partners? </li>
    <li>Want to host game or movie night at your home, or invite others to attend premiere movies? </li>
    <li>Planning a weekend getaway, as close as Tarpon Springs or as far as New Orleans, and
      want some fun-loving people to come along? </li>
  </ul>

  <p>You get the idea!

    <p>
      <h3>Here is how to get started:</h3>
      <p>1. Call or <a href="contact.php">contact Nancy</a> and explain what you'd like to lead, your experience with that type of event, where and when you want to have it.
      </p>

      <p>2. If approved, you'll receive an email telling you how to post events. You'll have the privileges of Event Leader status on the site. <p>

          <p>If you need a break from leading, you can always go back to a fee-based membership.

            <p>Write to us. We'd love to hear from you!<br>
              -<?php echo GetParameter('FullContactName'); ?><?php
                                                              require('footer.php');
                                                              ?>