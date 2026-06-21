<?
// confirm deletion of an expired members reservation record.

require('always.include.php');
//$debug = true;
//$debug = false;

////session_start();
//require('include.php');
//require('expired_membership.php');
//require("datacon.php"); 

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
//LogMsg('deletereserveYN.php:$loginUsername: '.$loginUsername);
//LogMsg('deletereserveYN.php:$AdminLevel: '.$AdminLevel);
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

$FileName = __FILE__;
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Admin Delete Expired Reservations ' . $ClubCompanyName;
$admin = true;
require('top.php');
?>

<?

$reserve_id = isset($_POST['reserve_id']) ? $_POST['reserve_id'] : "";
$reserve_id = trim($reserve_id);
//LogMsg('$reserve_id: '.$reserve_id);

// redirect to the elist.php page with bad reserve_id
if (empty($reserve_id)) {
  $loc = "Location: /members/elist.php";
  header($loc);
  exit;
}

//if (!($result = @ mysqli_query($connection, $query)))
$query = "select * from reserve where reserve_id = " . $reserve_id;
if (!$result = mysqli_query($connection, $query)) {
  trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );
}

$rowcount = mysqli_num_rows($result)
  or trigger_error(
    "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
    E_USER_ERROR
  );

if ($rowcount == 0) {
  echo "This record cannot be found!<br>";
  echo "reserve_id: " . $reserve_id . "<br>";
  //echo "number: " . $number . "<br>";
  //echo "printing rows:<br>";
  //while ($row = mysqli_fetch_assoc($result)) {
  //      echo "row " . $row["reserve_id"] . "<br>";
} elseif ($rowcount > 0) {
  $x = 0;

  //while ($row = mysqli_fetch_assoc($result)) {
  //      echo "row " . $row["reserve_id"] . "<br>";

?>

  <h3>Is this the Record that you want to Delete?</h3>
  <form name=formMaker method=post action="deletereserve.php">
    <table border=0 cellspacing=0 cellpadding=10>
      <?
      while ($x < $rowcount) {
      ?>
        <?php $row = mysqli_fetch_assoc($result); ?>
        <tr height=30>
          <? $reserve_id = $row['reserve_id']; ?>
          <td align=right><b>Reserve_id :</b></td>
          <td><? echo $reserve_id; ?></td>
        </tr>


        <tr height=30>
          <? $cust_id = $row['cust_id']; ?>
          <td align=right><b>Cust_id :</b></td>
          <td><? echo $cust_id; ?></td>
        </tr>


        <tr height=30>
          <? $event_id = $row['event_id']; ?>
          <td align=right><b>Event_id :</b></td>
          <td><? echo $event_id; ?></td>
        </tr>


        <tr height=30>
          <? $r_comments = $row['r_comments']; ?>
          <td align=right><b>R_comments :</b></td>
          <td><? echo $r_comments; ?></td>
        </tr>


    <?

        $x++;
      } // end while
    } // end if

    ?>

    </table>
    <input type=hidden name="reserve_id" value="<?php echo $reserve_id; ?>">
    <input type=submit name=Submit value="Delete this Record">
  </form>


  <?
  require('footer.php');
  ?>