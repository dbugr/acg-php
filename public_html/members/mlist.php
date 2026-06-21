<?php

// display list of members,
// display member selected data
// and profile summary, details and photo

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

// Show an error in a red font
function fieldError($fieldName, $errors)
{
  if (isset($errors[$fieldName]))
    echo "<font color=\"red\">" .
      $errors[$fieldName] .
      "</font><br>";
}


// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing

// Is the user logged in?
if ((!SessionIsRegistered("loginUsername"))) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (mList) or "
    . "do not have sufficient privileges to "
    . "view this information.";
  SessionRegister("message", $message);

  LogMsg("Error not logged in or insufficient privilages (mlist)!");
  //trigger_error("Error not logged in or insufficient privilages (mlist)!", E_USER_ERROR);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  header("Location: /login.php");
  exit;
}
$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';
if ($VendorLevel) {
  // Register a message to show the user
  $message = "Error: vendors cannot view member profiles.";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  header("Location: /members/elist.php");
  exit;
}
$ClubCode      = GetParameter('ClubCode');

/*
//========================================================
function GetMemberExpirationDates()
{
  $query = "SELECT cust_id, u_date_expiration FROM members";
  //             WHERE user_name = '$loginUsername'";
  // Open a connection to the DBMS
  mysqlconnect($connection);

  // Execute the query
  if (!($result = @mysqli_query($connection, $query))) {
    trigger_error(
      "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
      E_USER_ERROR
    );
    return (0);
  }

  $MembershipExpirationDates = array();
  while ($u_row = @mysqli_fetch_array($result)) {
    $u_cust_id = $u_row['cust_id'];
    $MembershipExpirationDates[$u_cust_id] = $u_row['u_date_expiration'];
  }

  return ($MembershipExpirationDates);
}

//========================================================
function expired_membership($date_expiration)
{
  // if time() < u_date_expired return false (unexpired) membership
  // else return true expired membership
  if (!empty($date_expiration))
    $expiration_stamp = strtotime($date_expiration);
  else
    $expiration_stamp = strtotime('1970-01-01');
  $time_stamp = time();
  if ((float) $time_stamp < (float) $expiration_stamp)
    $expired = 0;
  else
    $expired = 1;

  //DisplayVariable("valid", $valid);
  return ($expired);
}
*/


//========================================================
// get list of photos
function GetPhotosListFromDisk()
{

  $FileDir = GetParameter('PhotosPath');
  $aFiles = array();
  //echo 'mPhotoGet.php connecting<br>';
  $d = dir($FileDir) or die('ERROR php dir command: ' . $php_errormsg);
  while ((false !== ($FilePath = $d->read()))) {
    $info = pathinfo($FilePath);
    $filename = $info['filename'];
    $basename = $info['basename'];
    $aFiles[$filename] = $filename;
  }
  $d->close();

  //LogMsg('Displaying contents of $aFiles:');
  //LogMsg(print_r($aFiles,true));
  return ($aFiles);
}


/*
//========================================================
function getPhotosFileListDB () {
	//global $hostName;
	//global $username;
	//global $password;
	//global $databaseName;
	global $connection;


   	$aFiles = array();
	//echo 'mPhotoGet.php connecting<br>';
	
	//echo 'mPhotoGet.php querying<br>';
	//$cust_id = quotesqldata($cust_id);
	//$cust_id = $dbh->quote($cust_id);

	$query = "select image_id,image_name from image";
	LogMsg("getPhotosFileList query: " . $query);
	//echo 'mPhotoGet.php sql: '.$sql.'<br>';
        
  if (!($result = @ mysqli_query($connection, $query)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                  E_USER_ERROR);

	//$sth = $dbh->query($sql);
        //if (DB::isError($sth)) {
        //    trigger_error("MySQL GetPhotosFileList Query Error ". $sth->getMessage(),
        //	E_ERROR);
        //} else {
	$NumRows = mysqli_num_rows($result);
	//    $NumRows = $sth->numRows();
	if ($NumRows > 0) {
		while ($row = mysqli_fetch_array($result)) {
	//	while ($row = $sth->fetchrow(DB_FETCHMODE_ASSOC)) {
			$image_id = $row['image_id'];
			$aFiles[$image_id] = $image_id;
			//echo 'image_name: ' . $row['image_name'] . '<br>';
		}
	}
        //}

	//foreach ($aFiles as $filename) {
	//	echo "My Filename: " . $filename . '<br>';
	//}

	return($aFiles);
}
*/

// CODE STARTS HERE !!!!!
if (empty($errors)) {
  mysqlconnect($connection);

  $header_str = "<tr>"
    . "<td nowrap>"
    . "<b>Name</b>"
    . "</td>"
    . "<td>"
    . "<b>Email</b>"
    . "</td>"
    . "<td>"
    . "<b>Home Phone</b>"
    . "</td>"
    . "<td>"
    . "<b>Mobile Phone</b>"
    . "</td>";
  //."<td>"
  //."<b>Work Phone</b>"
  //."</td>"
  //."</tr>";

  $header_str2 = "<tr>"
    . "<td nowrap>"
    . "View Info"
    . "</td>"
    . "<td nowrap>"
    . "Edit Info"
    . "</td>"
    . "<td>"
    . "Chng Password"
    . "</td>"
    . "<td>"
    . "Login"
    . "</td>"
    . "<td>"
    . ""
    . "</td>"
    . "<td>"
    . ""
    . "</td>"
    . "<td>";
  //.""
  //."</td>"
  //."</tr>";

  // obtain member information from members table
  $cust_id = getCustomerID(LoginUsername());

  if ($cust_id == NULL) {
    $message = "Error: Invalid Customer ID!\n";
    trigger_error("Error cust_id is NULL", E_USER_ERROR);
    exit;
  } else {
    // obtain list of ALL members
    $query = "SELECT * FROM members WHERE (m_club='" . $ClubCode . "')"
      . " ORDER BY m_firstname, m_lastname;";

    if (!($result = @mysqli_query($connection, $query)))
      trigger_error(
        "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
        E_USER_ERROR
      );

    $MemberExpirationDates = GetMemberExpirationDates();

    $PhotoFileList = GetPhotosListFromDisk();
    //$PhotoFileList = getPhotosFileListDB();

    // obtain list of NEW members ONLY
    $PeriodInDays = 30;
    $StartDate = time() - ($PeriodInDays * 24 * 3600);
    $StartDate = date('Y-m-d', $StartDate);
    //$StartDate = '20060701';
    $queryNew = "SELECT * FROM members "
      . " WHERE (m_club='" . $ClubCode . "')"
      . " AND m_date_joined > '"
      . $StartDate
      . "'"
      . " ORDER BY m_firstname, m_lastname "
      . ";";

    if (!($resultNew = @mysqli_query($connection, $queryNew)))
      trigger_error(
        "MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection),
        E_USER_ERROR
      );

    //$error->debug($PhotoFileList, '$PhotoFileList', __FILE__, __LINE__);

    // obtain data for NEW members ONLY, format into array of strings
    while ($m_row_new = mysqli_fetch_array($resultNew)) {

      $cust_id = $m_row_new["cust_id"];
      if (!expired_membership($MemberExpirationDates[$cust_id])) {

        $begin_bold = (isset($PhotoFileList[$cust_id]) ? "<b>" : "");
        $end_bold = (isset($PhotoFileList[$cust_id]) ? "</b>" : "");

        $str = // view member info
          "<tr>";

        // first name
        $str .= "<td nowrap>";

        $begin_anchor = "";
        $end_anchor = "";
        if ($m_row_new['m_profile_display']) {
          // profile exists, so create link
          $begin_anchor .= "<a href=\"/members/mview.php?cust_id="
            . $m_row_new["cust_id"]
            . "\">";
          $end_anchor = "</a>";
        }
        $str .= $begin_anchor
          . $begin_bold
          . $m_row_new["m_firstname"]
          . " "
          . $m_row_new["m_lastname"]

          // entries marked for display get an asterisk "*"
          //. ($m_row['m_profile_display'] ? " * " : "")
          . (isset($PhotoFileList[$cust_id]) ? "+" : "")
          . $end_bold;

        $str .= $end_anchor
          . "</td>\n";

        // email
        $str .= "<td nowrap>";
        $str .= $m_row_new['m_disp_email'] ? $m_row_new["m_email"] : NULL;
        $str .= "</td>\n";

        // home phone
        $str .= "<td nowrap>";
        $str .= $m_row_new['m_disp_phonehome'] ? $m_row_new["m_phonehome"] : NULL;
        $str .= "</td>\n";

        // mobile phone
        $str .= "<td nowrap>";
        $str .= $m_row_new['m_disp_phonemobile'] ? $m_row_new["m_phonemobile"] : NULL;
        $str .= "</td>\n";

        // work phone
        //$str.="<td nowrap>";
        //$str.= $m_row['m_disp_phonework'] ? $m_row["m_phonework"] : NULL;
        //$str.="</td>\n";

        $str .= "</tr>";

        //echo $str . "<br>\n";
        $strings_new[$cust_id] = $str;
        //break;
      } // if (!expired)
    } // while (m_row)


    // obtain data for ALL current members, format into array of strings
    while ($m_row = mysqli_fetch_array($result)) {

      $cust_id = $m_row["cust_id"];
      if (!expired_membership($MemberExpirationDates[$cust_id])) {

        $begin_bold = (isset($PhotoFileList[$cust_id]) ? "<b>" : "");
        $end_bold = (isset($PhotoFileList[$cust_id]) ? "</b>" : "");

        $str = // view member info
          "<tr>";

        // first name
        $str .= "<td nowrap>";

        $begin_anchor = "";
        $end_anchor = "";
        if ($m_row['m_profile_display']) {
          // profile exists, so create link
          $begin_anchor .= "<a href=\"/members/mview.php?cust_id="
            . $m_row["cust_id"]
            . "\">";
          $end_anchor = "</a>";
        }
        $str .= $begin_anchor
          . $begin_bold
          . $m_row["m_firstname"]
          . " "
          . $m_row["m_lastname"]

          // entries marked for display get an asterisk "*"
          //. ($m_row['m_profile_display'] ? " * " : "")
          . (isset($PhotoFileList[$cust_id]) ? "+" : "")
          . $end_bold;

        $str .= $end_anchor
          . "</td>\n";

        // email
        $str .= "<td nowrap>";
        $str .= $m_row['m_disp_email'] ? $m_row["m_email"] : NULL;
        $str .= "</td>\n";

        // home phone
        $str .= "<td nowrap>";
        $str .= $m_row['m_disp_phonehome'] ? $m_row["m_phonehome"] : NULL;
        $str .= "</td>\n";

        // mobile phone
        $str .= "<td nowrap>";
        $str .= $m_row['m_disp_phonemobile'] ? $m_row["m_phonemobile"] : NULL;
        $str .= "</td>\n";

        // work phone
        //$str.="<td nowrap>";
        //$str.= $m_row['m_disp_phonework'] ? $m_row["m_phonework"] : NULL;
        //$str.="</td>\n";

        $str .= "</tr>";

        //echo $str . "<br>\n";
        $strings[$cust_id] = $str;
        //break;
      } // if (!expired)
    } // while (m_row)
  } // if (cust_id)
}


$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName    = GetParameter('ClubCompanyName');
$WebPageTitle = 'Member List ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">

  <?php
  if (!empty($strings_new)) {
    if (count($strings_new) > 0) {
      echo "<h3 align=\"left\">New Members</h3>";
      echo "<p align=\"left\">Members who joined during the last ";
      echo $PeriodInDays;
      echo " days</p>";
      echo "<table border=0 width=100%>";
      echo "<col span=\"1\" align=\"right\">";
      echo $header_str . "\n";
      foreach ($strings_new as $str) {
        echo $str . "\n";
      }
      echo "</td>";
      echo "</table>";
    }
  }
  ?>


  <h3 align="left">Current Member Directory</h3>
  <table border=0 width=100%>
    <col span="1" align="right">
    <?php
    echo $header_str . "\n";
    foreach ($strings as $str) {
      echo $str . "\n";
    }
    ?>
    </td>
  </table>

  <p>
    <b>+</b> = Member profile includes a photo


    <!--
<br><a href="http://validator.w3.org/check/referer"><img
     src="http://www.w3.org/Icons/valid-html401" height="31" width="88"
          align="right" border="0" alt="Valid HTML 4.01!"></a>
-->
</div>

<?php

require('footer.php');

?>