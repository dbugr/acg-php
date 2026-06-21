<?php
/*
AdventureClub.Info, Inc.

*/
  // help user edit his/her personal data

  require('always.include.php');
  //$debug = true;
  //$debug = false;
  
  //session_start();
  //require('include.php');
  
  require('image.php');

//echo "medit-photo-post.php: running script<br>";

// Is the user logged in and were there no errors from a previous
// validation?  If so, look up the customer for editing
// Is the user logged in?
// Is the user logged in?

if ((!SessionIsRegistered("loginUsername")))
{
     // Register a message to show the user
     $message = "Error: you are not logged in or "
                . "do not have sufficient privilages to "
                . "view this information.";
     SessionRegister("message",$message);

     trigger_error("Error user not logged in (medit-adm)!", E_USER_ERROR);

     // Register where they came from
     $referer = $_SERVER['PHP_SELF'];
     SessionRegister("referer",$referer);

     // redirect to the login page
     header("Location: /login.php");
     exit;
}

$ApplicationPath = GetPath('APPLICATION_PATH');

// does user have sufficient privilages to edit another users data?
$cust_id = !empty($_GET['cust_id']) ? $_GET['cust_id'] : NULL;

$loginUsername = LoginUsername();
$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';	
$LeaderLevel = $MemberInfo->GetMemberAuthLevel() == 'Leader';	
$VendorLevel = $MemberInfo->GetMemberAuthLevel() == 'Vendor';	
$cid = getCustomerID($loginUsername);
if (!$AdminLevel) {
   	// member cannot edit another members data without admin privilages!
   	$cust_id = $cid;
}

function UpdateDB_m_profile_display_field ($cust_id) {
  global $connection;

	mysqlconnect($connection);

  	$query = "UPDATE members SET "
              . "m_profile_display = "
              . "'1' "
  			  . " WHERE cust_id = '" . $cust_id  . "'";

	if (isset($debug) && ($debug)) {
		LogMsg('query: ' . $query . '<br>');
	}

  // Run the query on the members table
  if (!( mysqli_query ($connection, $query)))
      trigger_error("MySQL error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);
}


function GetMimeType($filename)
{
    $mimetype = false;
    if(function_exists('exif_imagetype')) {
      $mimetype = exif_imagetype($filename);
    } elseif(function_exists('mime_content_type')) {
      $mimetype = mime_content_type($filename);
    }
    return $mimetype;
}



// CODE STARTS HERE
// Clear any errors from previous scripts
$errors = array();
SessionRegister("photoerrors",$errors);

if ($debug) {
  //LogMsg("medit-photo-post.php: uploading file<br>");
  //LogMsg('print_r($_FILES[])');
  //LogMsg(print_r($_FILES,true));
}
if (!is_uploaded_file($_FILES['toProcess']['tmp_name'])) {
  switch($_FILES['toProcess']['error']){
    case 0: //no error; possible file attack!
      $errors["photo"] = "There was a problem with your upload.";
      $_SESSION['photoerrors'] = $errors;
      trigger_error("Error file not uploaded, LoginUsername: " .
        $loginUsername, E_USER_ERROR);
      break;
    case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
      $errors["photo"] =  "The file you are trying to upload is too big (upload_max_filesize).";
      $_SESSION['photoerrors'] = $errors;
      break;
    case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
      $errors["photo"] =  "The file you are trying to upload is too big (MAX_FILE_SIZE).";
      $_SESSION['photoerrors'] = $errors;
      break;
    case 3: //uploaded file was only partially uploaded
      $errors["photo"] =  "The file you are trying to upload was only partially uploaded.";
      $_SESSION['photoerrors'] = $errors;
      break;
    case 4: //no file was uploaded
      $errors["photo"] =  "You must select an image for upload.";
      $_SESSION['photoerrors'] = $errors;
      break;
    default: //a default error, just in case!  :)
      $errors["photo"] =  "There was a problem with your upload.";
      $_SESSION['photoerrors'] = $errors;
      break;
  }
} else {
  $cust_id = clean($_POST['cust_id']);
  $path = $ApplicationPath . "/var/webtmp";

  $file_extension = "";
  $move_successful = 0; // pessimistic default
  
  // get details of the uploaded file
  $name     = $_FILES['toProcess']['name'];
  $type     = $_FILES['toProcess']['type'];
  $tmp_name = $_FILES['toProcess']['tmp_name'];
  $size     = $_FILES['toProcess']['size'];

  $detected_mime_type_integer = exif_imagetype($tmp_name);
  $detected_mime_type_text = image_type_to_mime_type($detected_mime_type_integer);
  $allowed_mime_types_integer = array(IMAGETYPE_JPEG);
  //$allowed_mime_types_integer = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
  //LogMsg('detected_mime_type_text: '.$detected_mime_type_text);

  if ( $error = !in_array($detected_mime_type_integer, $allowed_mime_types_integer) ) {
    $errors["photo"] = "we only accept .jpg files at this time. Sorry.";
  } else {
    $extensions = array( 'image/jpeg'=>'jpg');
    $file_extension = $extensions[$detected_mime_type_text];
    $NewFileName = $cust_id . '.' . $file_extension;
    // directory where the file will be moved to
    $PhotosPath = GetParameter('PhotosPath');
    $DestPath = $PhotosPath .'/'. $NewFileName;
    if(move_uploaded_file($tmp_name, $DestPath)) {
      $message ='File successfully uploaded.';
      $move_successful = 1;
    } else {
      $errors["photo"] = 'Error moving file to photos folder.';
      $move_successful = 0;
    }
  }
}
  /*
	// copy image file into database
	//echo "medit-photo-post.php: copying image file to database<br>";
  $photo = new ImageClass();
	if (!$filename = $photo->ImageLoadFromDisk($filespec)) {
		$errors['photo'] = "Error loading photo image file";
		$_SESSION['photoerrors'] = $errors;
	} else
		$move_successful = $photo->ImageSave2Database($cust_id);

    //$move_successful =
    //  move_uploaded_file($_FILES['toProcess']['tmp_name'],
    //     $path . "/" . $cust_id . $file_extension);
    // delete file from server
	//unlink($filespec) or die ("Can not delete file: $filespec<br>");
  }
}
*/

if (empty($errors) and $move_successful) {
  $message =
           'Upload successful: '
           .$_FILES['toProcess']['name']
           ."  Mime Type: "
           . $_FILES['toProcess']['type'];
  UpdateDB_m_profile_display_field($cust_id);
} elseif (empty($errors) and !$move_successful) {
  $message = "<font color='red'>"
             ."ERROR: "
             ."Error renaming file"
             ."</font>";
  //LogMsg($message);
} else {
  $message = "<font color='red'>"
             ."ERROR: "
             .$errors['photo']
             ."</font>";
  //LogMsg($message);
}
$_SESSION['message'] = $message;


// redirect to calling page
  $loc = "Location: /members/medit.php?cust_id=" . $cust_id;
  header($loc);
  exit;
