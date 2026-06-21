<?php
// scan adventure club database. 
// for each event
//   if event not canceled, not postponed
//    for each yes/maybe person signed up for the event
//       if member.m_email_reminder
//         if member.m_email_reminder == (event_date - today())
//            obtain members email address
//            send reminder email to member

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');


mysqlconnect($connection);


$query = "SELECT events.event_id, events.e_name, events.e_begindate, "
  . " events.e_status, reserve.event_id, reserve.r_attending "
  . " reserve.cust_id  "
  . " FROM events left join reserve "
  . " ON events.event_id = reserve.event_id "
  . " WHERE  "
  . " events.e_begindate >= "
  .  " now() and "
  .  " ((reserve.r_attending = 'Yes') or "
  .  " (reserve.r_attending = 'Maybe')) "
  . " ORDER BY events.e_begindate, events.e_begintime ";


if (!($result = @mysqli_query($connection, $query)))
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );

while ($e_row = mysqli_fetch_array($result)) {
  print_r($e_row);
}
