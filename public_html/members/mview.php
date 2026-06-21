<?php

$debug = true;
require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


//========================================================
// functions

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
// get list of photos
function GetPhotosListFromDB () {
 	$aFiles = array();
	//echo 'mPhotoGet.php connecting<br>';

  // Open a connection to the DBMS
	mysqlconnect($connection);
	//db_connect($dbh);

	$query = "select image_id,image_name from image";
	//$sql = "select image_id,image_name from image";
  //$sth = $dbh->query($sql);
  // Execute the query
  if (!($result = @ mysqli_query($connection, $query))) {
  //if (DB::isError($sth)) {
  	trigger_error("MySQL GetPhotosListFromDB query Error ". mysqli_errno($connection) . 
        	" : " . mysqli_error($connection), E_ERROR);
  } else {
		$NumRows = mysqli_num_rows($result);
		//$NumRows = $sth->numRows();
  	if ($NumRows > 0) {
			while ($row =  @ mysqli_fetch_array($result)) {
			//while ($row = $sth->fetchrow(DB_FETCHMODE_ASSOC)) {
				$image_id = $row['image_id'];
				$aFiles[$image_id] = $image_id;
				//echo 'image_name: ' . $row['image_name'] . '<br>';
			}
		}
 	}

	//foreach ($aFiles as $filename) {
	//	echo "My Filename: " . $filename . '<br>';
	//}

	return($aFiles);
}
*/

/*
//========================================================
// get member expiration dates
function GetMemberExpirationDates()
{
  global $username;
  global $password;
  global $hostName;
  global $databaseName;

  $query = "SELECT cust_id, u_date_expiration FROM members";

  // Open a connection to the DBMS
  mysqlconnect($connection);

  // Execute the query
  if (!($result = @mysqli_query($connection, $query))) {
    trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);
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
// determine date is expired
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
  return ($expired);
}
*/


//========================================================
//////////////// code starts here

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer info
// Is the user logged in?
if (!SessionIsRegistered("loginUsername")) {
  // Register a message to show the user
  $message = "Error: you are not logged in! (eview)";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to the login page
  $loc = "Location: http://" . $_SERVER['HTTP_HOST'] . "/login.php";
  header($loc);
  exit;
}

$ClubCompanyName    = GetParameter('ClubCompanyName');
$ClubCode            = GetParameter('ClubCode');


// get customer id
$loginUsername = LoginUsername();
$cust_id = !empty($_GET['cust_id']) ? clean($_GET['cust_id']) : NULL;
$cid = getCustomerID($loginUsername);
if (!isset($cust_id)) {
  $cust_id = $cid;
}
$cust_id = quotesqldata($cust_id);
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';

// Is the user a vendor?
if (isset($Vendor) && $Vendor) {
  // Register a message to show the user
  $message = "Error: Vendors cannot view member profile data.";
  SessionRegister("message", $message);

  // Register where they came from
  $referer = $_SERVER['PHP_SELF'];
  SessionRegister("referer", $referer);

  // redirect to events listing
  header("Location: /members/elist.php");
  exit;
}

// Open a connection to the DBMS
mysqlconnect($connection);

// obtain this members data
$query = "SELECT * FROM members WHERE (cust_id='" . $cust_id . "') " .
  " ORDER BY m_firstname, m_lastname";
if (!($result = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

// stringify member data
$strings = array();

$m_row = mysqli_fetch_array($result);
$i = 0;
foreach ($m_row as $key => $value) {
  if (($i % 2) > 0) {
    $str = "<tr><td nowrap>" . $key . ": </td><td>" . $value . "</td></tr>";
    $strings[$key] = $str;
  }
  $i++;
}

// get photo image height and width
// no error checking, the user might not have a photo!
/*
  $query = "SELECT image_id, image_width, image_height FROM image WHERE (image_id='" . $cust_id . "') ";
  $result = @ mysqli_query($connection, $query);
  $img_row = @ mysqli_fetch_array($result);
  $image_id = $img_row['image_id'];
  $image_width = $img_row['image_width'];
  $image_height = $img_row['image_height'];
  */
//LogMsg('mview.php'
//	.' # '.'image_id:'.$image_id
//	.' # '.'image_width:'.$image_width
//	.' # '.'image_height:'.$image_height
//	);

// get photo image height and width
// no error checking, the user might not have a photo!
$photos_path = GetParameter('PhotosPath');
$filepath = $photos_path . '/' . $cust_id . '.jpg';
$MaxWidth = 310;
if (!file_exists($filepath)) {
  $ImgAdjWidth = 0;
  $ImgAdjHeight = 0;
} else {
  list($image_width, $image_height) = getimagesize($filepath);
  if ($image_width > $MaxWidth) {
    // scale the image
    $ratio = $image_height / $image_width;
    $ImgAdjWidth = $MaxWidth;
    $ImgAdjHeight = $ratio * $MaxWidth;
  } else {
    $ImgAdjWidth = $image_width;
    $ImgAdjHeight = $image_height;
  }
}

$disp = array();

$disp['phonehome']   = $m_row['m_disp_phonehome']   ?
  "<b>Home:</b> " . $m_row['m_phonehome'] . "<br>\n" : NULL;
$disp['phonemobile'] = $m_row['m_disp_phonemobile'] ?
  "<b>Mobile:</b> " . $m_row['m_phonemobile'] . "<br>\n" : NULL;
$disp['phonework']   = $m_row['m_disp_phonework']   ?
  "<b>Work:</b> " . $m_row['m_phonework'] . "<br>\n" : NULL;
$disp['phonemisc']   = $m_row['m_disp_phonemisc']   ?
  "<b>Misc:</b> " . $m_row['m_phonemisc'] . "<br>\n" : NULL;

$disp['email']       = $m_row['m_disp_email']       ?
  "<b>Email:</b> " . $m_row['m_email'] . "<br>\n" : NULL;
$disp['email2']      = $m_row['m_disp_email2']      ?
  "<b>Email2:</b> " . $m_row['m_email2'] . "<br>\n" : NULL;

$disp['address1']    = $m_row['m_disp_address']     ?
  "<b>Address1:</b> " . $m_row['m_address1'] . "<br>\n" : NULL;
$disp['address2']    = $m_row['m_disp_address2']    ?
  "<b>Address2: </b>" . $m_row['m_address2'] . "<br>\n" : NULL;
$disp['address3']    = $m_row['m_disp_address3']    ?
  "<b>Address3:</b> " . $m_row['m_address3'] . "<br>\n" : NULL;

$disp['city']        = $m_row['m_disp_city']        ?
  "<b>City:</b> " . $m_row['m_city'] . "<br>\n" : NULL;
$disp['state']       = $m_row['m_disp_state']       ?
  "<b>State:</b>" . $m_row['m_state'] . "<br>\n" : NULL;
$disp['zipcode']     = $m_row['m_disp_zipcode']     ?
  "<b>Zip: </b>" . $m_row['m_zipcode'] . "<br>\n" : NULL;
$disp['country']     = $m_row['m_disp_country']     ?
  "<b>Country: </b>" . $m_row['m_country'] . "<br>\n" : NULL;

$disp['sex']         = $m_row['m_disp_sex']         ?
  "<b>Sex: </b>" . $m_row['m_sex'] . "<br>\n" : NULL;
// birth date display month/day ONLY!!!
$disp['date_birth']  = $m_row['m_disp_date_birth']  ?
  $m_row['m_date_birth'] : NULL;
$disp['date_birth']  = !empty($disp['date_birth']) ?
  "<b>Birthday: </b>" . substr($disp['date_birth'], 5, 2)
  . "/"
  . substr($disp['date_birth'], 8, 2) . " (Month/Day)"
  : NULL;


$disp['summary'] = isset($m_row['p_summary']) ? $m_row['p_summary'] : NULL;
$disp['occupation'] = isset($m_row['p_occupation']) ? $m_row['p_occupation'] : NULL;
$disp['details'] = isset($m_row['p_details']) ? $m_row['p_details'] : NULL;

// get data for all members
$query = "SELECT cust_id, m_firstname, m_lastname, m_profile_display "
  . " FROM members WHERE (m_club='" . $ClubCode . "')"
  . " ORDER BY m_firstname, m_lastname ";

if (!($members = @mysqli_query($connection, $query)))
  trigger_error("MySQL error: " . mysqli_errno($connection) . " : " . mysqli_error($connection), E_USER_ERROR);

$mlist_strings = array();
$loc = "/members/mview.php?cust_id=";

$PhotoFileList = GetPhotosListFromDisk();
//$PhotoFileList = GetPhotosListFromDB();
//$error->debug($PhotoFileList, '$PhotoFileList', __FILE__, __LINE__);
$MemberExpirationDates = GetMemberExpirationDates();

// create member list
while ($mlist_row = mysqli_fetch_array($members)) {
  $mlist_cust_id = $mlist_row["cust_id"];
  $begin_bold = (isset($PhotoFileList[$mlist_cust_id]) ? "<b>" : "");
  $end_bold = (isset($PhotoFileList[$mlist_cust_id]) ? "</b>" : "");
  if (!expired_membership($MemberExpirationDates[$mlist_cust_id])) {
    $begin_bold = (isset($PhotoFileList[$mlist_cust_id]) ? "<b>" : "");
    $end_bold = (isset($PhotoFileList[$mlist_cust_id]) ? "</b>" : "");

    $mlist_str = ""; // view member info

    // first name
    $mlist_str .= "";

    $begin_anchor = "";
    $end_anchor = "";
    if ($mlist_row['m_profile_display']) {
      // profile exists, so create link
      $begin_anchor .= "<a href=\"/members/mview.php?cust_id=" . $mlist_row["cust_id"] . "\">";
      $end_anchor = "</a>";
    }

    $mlist_str .= $begin_anchor . $begin_bold . $mlist_row["m_firstname"] . " " . $mlist_row["m_lastname"]
      // entries marked for display get an asterisk "*"
      //. ($mlist_row['m_profile_display'] ? " * " : "")
      . (isset($PhotoFileList[$mlist_cust_id]) ? "+" : "")
      . $end_bold;

    $mlist_str .= $end_anchor . "<br>\n";
    $mlist_strings[] = $mlist_str;
  }
}

$found_contact = 0;
if (
  $disp['email']
  or $disp['email2']
  or $disp['phonehome']
  or $disp['phonemobile']
  or $disp['phonework']
  or $disp['phonemisc']
  or $disp['sex']
  or $disp['date_birth']
) {
  $found_contact = 1;
}

$found_address = 0;
if (
  $disp['address1']
  or $disp['address2']
  or $disp['address3']
  or $disp['city']
  or $disp['state']
  or $disp['zipcode']
  or $disp['country']
) {
  $found_address = 1;
}

$name_string =
  //        "<center>"
  "<table width=100% border=0>"
  . "<tr>"
  . "<td>"
  . "<h3>"
  . $m_row['m_firstname'] . " " . $m_row['m_lastname']
  . "</h3>"
  . "</td>"
  . "</tr>"
  . "</table>";
//        ."</center>\n";

if ($found_contact) {
  $contact_string =
    //        "<center>\n"
    "<table width=100% border=0>\n"

    . "<tr>\n"
    . "  <td>\n"
    . $disp['email']
    . $disp['email2']
    . $disp['phonehome']
    . $disp['phonemobile']
    . $disp['phonework']
    . $disp['phonemisc']
    . "  </td>\n"
    . "</tr>\n"

    . "<tr>\n"
    . "  <td>\n"
    . $disp['sex']
    . $disp['date_birth']
    . "  </td>\n"
    . "</tr>\n"

    . "</table>\n";
}

if ($found_address) {
  $address_string = "<table width=100% border=0>\n"
    . "<tr>\n"
    . "  <td>\n"
    . $disp['address1']
    . $disp['address2']
    . $disp['address3']
    . $disp['city']
    . $disp['state']
    . $disp['zipcode']
    . $disp['country']
    . "  </td>\n"
    . "</tr>\n"

    . "</table>\n";
  //        ."</center>\n";

}


// display members photo
$photo_string = "<br />"
  . "<center>"
  . "<img src=\"/members/mphotoget.php?cust_id="
  . $cust_id
  . "\" alt=\"Photo Not Available\" "
  . "width=\"{$ImgAdjWidth}\" "
  . "height=\"{$ImgAdjHeight}\" "
  . ">"
  . "</center>"
  . "<br />";


/*
  // display members photo
  $photo_string = "<br />"
      ."<center>"
      ."<img src=\"/photos/".$cust_id.".jpg"
      ."\" alt=\"Photo Not Available\" "
      ."width=\"{$ImgAdjWidth}\" "
      ."height=\"{$ImgAdjHeight}\" "
      .">"
      ."</center>"
      ."<br />";
*/

$found_profile = 0;
if (((strlen($disp['summary'])
    || strlen($disp['details'])))
  || strlen($disp['occupation'])
) {
  $found_profile = 1;
  $profile_string = "<table width=100% border=0>\n"
    . "<tr>\n"
    . "  <td>\n"
    . "<b>Interests:</b> "
    . $disp['summary']
    . "  </td>\n"
    . "</tr>\n"

    . "<tr>\n"
    . "  <td>\n"
    . "<b>Occupation:</b> "

    . $disp['occupation']
    . "  </td>\n"
    . "</tr>\n"

    . "<tr>\n"
    . "  <td>\n"
    . "<b>Profile: </b>"
    . $disp['details']
    . "  </td>\n"
    . "</tr>\n"

    . "</table>\n";
}


$FileName = $_SERVER['PHP_SELF'];
$WebPageTitle = 'Member Profile - ' . $ClubCompanyName;
require('top.php');


?>

<div id="centercontent">
  <table border=0>
    <tr>
      <td nowrap valign=top>
        <?
        foreach ($mlist_strings as $m_link) {
          echo $m_link;
        }
        ?>
      </td>

      <td valign=top>
        <?
        echo isset($name_string)    ? $name_string : "";
        echo isset($contact_string) ? $contact_string : "";
        echo isset($profile_string) ? $profile_string : "";
        echo isset($address_string) ? $address_string : "";
        echo isset($photo_string) ? $photo_string : "";
        ?>
      </td>
    </tr>
    </td>
  </table>

  <b>+</b> = Member profile includes a photo
</div>

<?php

require('footer.php');
?>