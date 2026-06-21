<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


//echo 'mPhotoGet.php<br>';
if (!isset($_GET['cust_id'])) {
	// print photo unavailable on screen
	//LogMsg('$_GET not set!');
  Header('Content-Type: image/jpg');
	$FilePath = GetPath('DOCROOT_PATH') . "/photos/photounavailable.jpg";
	readfile($FilePath);
} else {
  //$cust_id = !empty($_GET['cust_id']) ? $_GET['cust_id'] : NULL;
  $cust_id = clean($_GET['cust_id']);
	$cust_id = quotesqldata($cust_id);
	//echo 'mPhotoGet.php sql: '.$sql.'<br>';

	$images_path = GetParameter('PhotosPath');
	$filepath = $images_path . '/' . $cust_id . '.jpg';
	//LogMsg("filepath: ".$filepath);
	if (file_exists($filepath)) {
		//LogMsg("Displaying image for cust_id: ".$cust_id);
		Header('Content-Type: image/jpg');
		readfile($filepath);
	} else {
		// print photo unavailable on screen
		$FilePath = GetPath('DOCROOT_PATH') . "/photos/photounavailable.jpg";
		//LogMsg('photo unavailable #2 FilePath: '.$FilePath);
		if (file_exists($FilePath)) {
			Header('Content-Type: image/jpg');
			if (ob_get_level()) {
				ob_clean();
				flush();
			}
			readfile($FilePath);
			exit;
		}
	}

	/*
	$query = "select * from image where image_id = '{$cust_id}'";
	// Execute the query
	if (!($result = @ mysqli_query($connection, $query))) {
        trigger_error("MySQL mPhotoGet query error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);
    } else {
		$NumRows = mysqli_num_rows($result);
		//$NumRows = $sth->numRows();
		//echo 'mPhotoGet.php NumRows: '.$NumRows.'<br>';
    	if ($NumRows > 0) {
			$row = mysqli_fetch_array($result);
	    	//$row = $sth->fetchRow(DB_FETCHMODE_ASSOC);
			//echo 'mPhotoGet.php row[image_size]: '.$row['image_size'].'<br>';
			//LogMsg('$NumRows: '.$NumRows);
			//LogMsg('$row["image"]'.$row["image"]);
	        Header('Content-Type: image/jpg');
        	print $row["image"];
        	//readfile("/home/adventrc/var/webtmp/gnv_78.jpg");
        } else {
			// print photo unavailable on screen
			$FilePath = $GetPath(DOCROOT_PATH) . "/images/photounavailable.jpg";
			//LogMsg('$FilePath: '.$FilePath);
			if (file_exists($FilePath)) {
				Header('Content-Type: image/jpg');
				if (ob_get_level()) {
					ob_clean();
					flush();
				}
				readfile($FilePath);
				exit;
			}
        }
  	}
	  */
}
