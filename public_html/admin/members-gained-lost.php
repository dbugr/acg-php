<?php
/*
AdventureClub.info

calculate number of members gained/lost during
past month.

*/
// listing of member referral categories 
// with count of members per category

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');


function num_new_members($connection, $begin_date, $end_date)
{
  //global $ClubCode;
  $ClubCode      = GetParameter('ClubCode');


  //$new_members = array();        
  // get number of people who joined
  $query = "select m_date_joined 
            from members 
            where $begin_date <= m_date_joined 
            AND (members.m_club='$ClubCode') 
            and m_date_joined <= $end_date";
  //and m_memberstatus = 'Paid'";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $new_members = mysqli_num_rows($result);
  return ($new_members);
}

// get number of people who canceled/expired
//$query = "select cust_id, user_name, u_date_expiration
//   from users 
//   where $begin_date <= u_date_expiration 
//   and u_date_expiration <= $end_date";
function num_expired_members($connection, $begin_date, $end_date)
{
  //global $ClubCode;
  $ClubCode      = GetParameter('ClubCode');


  $query = "select members.cust_id, members.m_memberstatus, 
           members.user_name, members.u_date_expiration 
           from members
           where $begin_date <= members.u_date_expiration 
           AND (members.m_club='$ClubCode') 
           and members.u_date_expiration <= $end_date";

  //$expired_members = array();
  if (!($result = @mysqli_query($connection, $query))) {
    return 0;
  } else {
    $expired_members = mysqli_num_rows($result);
  }
  return ($expired_members);
}

function num_members($connection, $begin_date, $end_date)
{
  //global $ClubCode;
  $ClubCode      = GetParameter('ClubCode');


  // get number of people who are paid members
  $query = "select m_firstname,m_lastname 
            from members 
            where m_date_joined <= $begin_date
            AND (members.m_club='$ClubCode') 
            and $end_date <= u_date_expiration";
  //and m_memberstatus = 'Paid'";

  if (!($result = @mysqli_query($connection, $query)))
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );

  $paid_members = mysqli_num_rows($result);
  return ($paid_members);
}



// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
if (!SessionIsRegistered("loginUsername")  or !$AdminLevel) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (elist)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = __FILE__;
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: /index.php";
  header($loc);
  exit;
}



mysqlconnect($connection);

// obtain member information from members table
$cust_id = getCustomerID($loginUsername);

if ($cust_id == NULL) {
  $message = "Error: Invalid Customer ID!\n";
} else {

  $new_members = array();
  $expired_members = array();
  $days_in_month = array();
  $strings = array();

  $days_in_month[1] = '31';
  $days_in_month[2] = '28';
  $days_in_month[3] = '31';
  $days_in_month[4] = '30';
  $days_in_month[5] = '31';
  $days_in_month[6] = '30';
  $days_in_month[7] = '31';
  $days_in_month[8] = '31';
  $days_in_month[9] = '30';
  $days_in_month[10] = '31';
  $days_in_month[11] = '30';
  $days_in_month[12] = '31';

  $sum_of_gains = 0;
  $loop_counter = 0;

  $CurrentYear = date('Y', time());
  //$year = '2006';

  for ($year = '2003'; $year <= $CurrentYear; $year++) {
    foreach ($days_in_month as $month => $end_day) {
      $loop_counter++;
      $begin_date = "'" . $year . '-' . $month . '-01' . "'";
      $end_date   = "'" . $year . '-' . $month . '-' . $end_day . "'";

      //          $begin_date = "'2005-01-01'";
      //          $end_date   = "'2005-12-31'";
      $new_members[$month] = num_new_members(
        $connection,
        $begin_date,
        $end_date
      );
      $expired_members[$month] = num_expired_members(
        $connection,
        $begin_date,
        $end_date
      );
      $members_gained[$month] =
        $new_members[$month] - $expired_members[$month];
      $sum_of_gains += $members_gained[$month];
      $num_members[$month] = num_members(
        $connection,
        $begin_date,
        $end_date
      );

      $str = "<tr>"

        . "<td nowrap>"
        .   $month
        . "</td>"

        . "<td nowrap>"
        .   $begin_date
        . "</td>"

        . "<td nowrap>"
        .   $end_date
        . "</td>"

        . "<td nowrap>"
        .   $members_gained[$month]
        . "</td>"

        . "<td nowrap>"
        .   $new_members[$month]
        . "</td>"

        . "<td nowrap>"
        .   $expired_members[$month]
        . "</td>"

        . "<td nowrap>"
        .   $num_members[$month]
        . "</td>"

        . "</tr>";
      $strings[$year][] = $str;
    } // end, foreach
  } // end, for

  // format (stringify)  data
  $header_str = "<tr>"
    . "<td nowrap>"
    . "Month"
    . "</td>"
    . "<td>"
    . "Begin Date"
    . "</td>"
    . "<td>"
    . "End Date"
    . "</td>"
    . "<td>"
    . "Members Gained"
    . "</td>"
    . "<td>"
    . "Members New"
    . "</td>"
    . "<td>"
    . "Members Expired"
    . "</td>"
    . "<td>"
    . "Members"
    . "</td>"
    . "</tr>";
} // if

$average_monthly_gain_loss = $sum_of_gains / $loop_counter;

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Members Gained/Lost ' . $ClubCompanyName;
$admin = true;
require('top.php');


?>

<div id="centercontent2">
  <hr>
  <h3>NOTE: this report includes ALL new/expired members.
    Even if the member changed from paid to free/leader. The "Members" column includes ALL members, including "paid" and "leaders"</h3>

  <table border=1>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";

    $CurrentYear = date('Y', time());
    for ($year = '2003'; $year <= $CurrentYear; $year++) {
      foreach ($strings[$year] as $str) {
        echo $str . "\n";
      }
    }
    ?>
  </table>

  <p><br></p>

  <p>Average Monthly Gain/Loss:
    <?php printf('%3.2f', $average_monthly_gain_loss); ?></p>

  <p><br></p>
</div>

<?php
require('footer.php');
?>