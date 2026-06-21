<?php


//========================================================
// load/save/display Images
class ImageClass
{
	var $ImageBinary;
	var $ImageFilePath;
	var $file_type;
	var $file_size;
	var $file_name;
	var $image_width;
	var $image_height;
	var $image_attrib;

	//========================================================
	function ImageLoadFromDisk($filespec) {
		global $debug;
		$debug = true;
		$debug = false;

		//echo 'Function ImageLoadFromDisk!<br>';
		//echo 'ImageFilePath: ' . $this->ImageFilePath . '<br>';
        if (!file_exists($filespec)) {
        		$success = false;
        } else {
			//echo 'ImageLoadFromDisk Opening: ' . $filespec . '<br>';
			$info = array();
			$extension = substr(strrchr($filespec, "."), 1);
			$this->file_type = $extension;
			//$info = pathinfo($filespec);
			//$this->file_type = $info['extension'];
			$this->file_name = basename($filespec,'.jpg');
           	if (!($fh = fopen($filespec,'rb')))
		        trigger_error("ImageLoadFromDisk error opening file: ".$filespec,
	    		E_USER_ERROR);
           	$this->file_size = filesize($filespec);
           	$this->image_attrib = getimagesize($filespec);
           	$this->image_width = $this->image_attrib[0];
           	$this->image_height = $this->image_attrib[1];

	  		if (isset($debug) && ($debug)) {
  				LogMsg('ImageLoadFromDisk: '.$this->file_name.':'.$this->file_type.':'.$this->file_size);
				LogMsg('ImageLoadFromDisk'
			  	.' # '.'image_id:'.$this->file_name
			  	.' # '.'image_width:'.$this->image_width
			  	.' # '.'image_height:'.$this->image_height
			  	);

 	    	}

			//echo 'FileName: ' . $this->file_name . '<br>';
			//echo 'FileSize: ' . $this->file_size . '<br>';
			//echo 'FileType: ' . $this->file_type . '<br>';
           	$this->ImageBinary = fread($fh,filesize($filespec));
           	if (!fclose($fh))
		        trigger_error("ImageLoadFromDisk error closing file: ".$filespec,
	    		E_USER_ERROR);
           	$success = true;
        }
        if ($success)
			return($this->file_name);
		else
			return(false);
	}


	//========================================================
	function ImageDeleteAllFromDatabase() {

		echo 'ImageDeleteAllFromDatabase<br>';

		// Open a connection to the DBMS
		mysqlconnect($connection);

        $query = "delete from image";
        echo 'query: ' . $query . '<br>';

		// Execute the query
		if (!($result = @ mysqli_query($connection, $query))) {
			trigger_error("MySQL ImageDeleteAllFromDatabase error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);
 			return(0);
		}
	}


    //=====================================================
    function ImageRecordExists(&$dbh,$image_id) {

    	// does image record already exist in image table?
    	$FoundRecord = false;
 
		// Open a connection to the DBMS
		mysqlconnect($connection);

 		$query = "select image_id from image where image_id = '{$image_id}'";
    	//echo 'ImageRecordExists: sql: '.$sql.'<br>';
		// Execute the query
		if (!($result = @ mysqli_query($connection, $query))) {
			trigger_error("MySQL ImageRecordExists Query error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);
 			return(0);
		} else {
			$NumRows = mysqli_num_rows($result);
    		//echo 'ImageRecordExists: NumRows: '.$NumRows.'<br>';
        	if ($NumRows <= 0) {
    	     	$FoundRecord = false;
        	} else {
    			$FoundRecord = true;
    		}
      	}
    	return($FoundRecord);
    }

	//========================================================
	function ImageSave2Database($ImageName) {
		global $debug;

		//$debug = true;
		// Open a connection to the DBMS
		mysqlconnect($connection);

		$success = false; // pessimistic assume failure
		if (isset($ImageName))
			$ImageNameStr = $ImageName;
		else
			$ImageNameStr = $this->file_name;
		$image = addslashes($this->ImageBinary);
		$TimeDate = date('Y-m-d H:i:s', time());

		if ($this->ImageRecordExists($dbh,$ImageNameStr)) {
          	$sql = "UPDATE image "
                 . "SET "
                 . "image_id = '{$ImageNameStr}', "
                 . "image_type = '{$this->file_type}', "
                 . "image_size = '{$this->file_size}', "
                 . "image_name = '{$this->file_name}', "
                 . "image_date = '{$TimeDate}',"
                 . "image_width = '{$this->image_width}',"
                 . "image_height = '{$this->image_height}',"
                 . "image = '{$image}' "
                 . "WHERE image_id = '{$ImageNameStr}'";
        } else {
          	$sql = "INSERT INTO image ";
            $sql.= "(image_id, image_type, image, image_size, ";
            $sql.= "image_name, image_date, image_width, image_height) ";
    	    $sql.= "VALUES (";
          	$sql.= "'{$ImageNameStr}',";
          	$sql.= "'{$this->file_type}',";
          	$sql.= "'{$image}',";
          	$sql.= "'{$this->file_size}',";
            $sql.= "'{$this->file_name}',";
            $sql.= "'{$TimeDate}',";
            $sql.= "'{$this->image_width}',";
            $sql.= "'{$this->image_height}'";
            $sql.= ")";
		}

		if (isset($debug) && ($debug)) {
			LogMsg(
			'ImageSave2Database: '
			.'  ImageNameStr:'.$ImageNameStr
			.'  FileType:'.$this->file_type
			.'  FileSize:'.$this->file_size
			.'  FileName:'.$this->file_name
			.'  TimeDate:'.$TimeDate
			);
			LogMsg('ImageSave2Database'
			  	.' # '.'image_id:'.$this->file_name
			  	.' # '.'image_width:'.$this->image_width
			  	.' # '.'image_height:'.$this->image_height
			  	);

			//LogMsg('ImageSave2Database: SQL: '.$sql);
	    }

		$query = $sql;

		// Execute the query
		if (!($result = @ mysqli_query($connection, $query))) {
			trigger_error("MySQL ImageSave2Database Query error: ". mysqli_errno($connection) ." : ". mysqli_error($connection),
                       E_USER_ERROR);
 			return(0);
		} else {
			$success = true;
		}

		return($success);
	}


	//========================================================
	function ImageDisplay() {
		// write image to browser

	    echo "<center>";
      	echo '<img src="';
      	Header('Content-Type: image/jpg');
      	print $this->ImageBinary;
      	echo '"';
      	//echo " alt=\"Image Not Available\">";
      	echo "</center>";
      	echo"<br />";

	}

	//========================================================
    function ImagesCopyFromDisk2Database($FileDir) {

		$this->ImageDeleteAllFromDatabase();
      	$this->aFiles = array();
		$d = dir($FileDir) or die('ERROR php dir command: '.$php_errormsg);
		while ( (false !== ($filename = $d->read())) ) {
			$info = pathinfo($filename);
			$ext = $info['extension'];
			if ($ext == 'jpg') {
    	     	$filepath = $FileDir .'/'. $filename;
        	 	echo 'ImagesCopyFromDisk2Database FILENAME: ' . $filepath . '<br>';
        	 	echo 'ImageLoadFromDisk FILENAME: ' . $filepath . '<br>';
		    	if (!$filename=$this->ImageLoadFromDisk($filepath))
    				echo 'ERROR loading Image from disk!<br>';
        	 	echo 'ImageSave2Database FILENAME: ' . $filepath . '<br>';
    			if (!$this->ImageSave2Database($filename))
    				echo 'ERROR saving image to database<br>';
    			echo '<br>';
         	}
		}
		$d->close();
    }


	//========================================================
	function ListImagesInDatabase() {
		mysqlconnect($connection);

		$sql = "select * from image";
		if (!($result = @ mysqli_query($connection, $sql))) {
			trigger_error("MySQL ImageSave2Database Query error: ". mysqli_errno($connection) .
				" : ". mysqli_error($connection),E_USER_ERROR);
		}
		$number_of_images = mysqli_num_rows($result);
		echo 'number_of_images: '.$number_of_images ."<br>";		

		$aImages = array();
		while ($row = mysqli_fetch_array($result)) {
			//	while ($row = $sth->fetchrow(DB_FETCHMODE_ASSOC)) {
			$image_id = $row['image_id'];
			$aRow['image_type'] 	= $row['image_type'];
			$aRow['image'] 			= $row['image'];
			$aRow['image_size'] 	= $row['image_size'];
			$aRow['image_name'] 	= $row['image_name'];
			$aRow['image_date'] 	= $row['image_date'];
			$aRow['image_width'] 	= $row['image_width'];
			$aRow['image_height'] 	= $row['image_height'];
			$aImages[$image_id] 	= $aRow;
		}

		return($aImages);
	}


	//========================================================
	function ImageSaveToDisk($filename,$filepath,$image_binary) {
		//echo 'Writing image to disk: ' . $filespec .'<br>';
		$error = 0;		
		if ( !($fh = fopen($filepath,'wb') )) {
			print("ImageLoadFromDisk error opening file: ".$filepath.'<br>');
			trigger_error("ImageLoadFromDisk error opening file: ".$filepath,
			E_USER_ERROR);
			$error = 1;
		}

		if ( !fwrite($fh,$image_binary) ) {
			print("ImageLoadFromDisk error writing file: ".$filepath.'<br>');
			trigger_error("ImageLoadFromDisk error writing file: ".$filepath,
			E_USER_ERROR);
			$error = 1;
		}
		fclose($fh);

		return($error);
	}

	//========================================================
    function ImagesCopyFromDatabase2Disk($FileDir) {
		//$FileDir = '/devilbox/httpd/acg/var/images';
		$rows = $this->ListImagesInDatabase();
		$i = 0;
		foreach ($rows as $key => $value) {
			// create image file on disk
			$image_type = $value['image_type'];
			if (empty($image_type)) {
				$image_type = 'jpg';
			}
			$filename = $key . '.' . $image_type;
			$filepath = $FileDir .'/'. $filename;
			$image_binary = $value['image'];
			//echo 'ImagesCopyFromDatabase2Disk filepath: ' . $filepath . '<br>';
			//$image_binary = $value['image_size'];
			if ($error = $this->ImageSaveToDisk($filename,$filepath,$image_binary)) {
				echo 'ERROR writing image to disk! Filename: '.$filename.'<br>';
			} else {
				print 'Wrote image to disk file: '.$filepath.'/'.$filename.'<br>';
			}
			$i++;
			//if ($i >= 2) {
			//	exit;
			//}
		}
		echo 'ALL DONE, NUFF SAID!';
		/*
		$this->aFiles = array();
		$d = dir($FileDir) or die('ERROR php dir command: '); //.$php_errormsg);
		while ( (false !== ($filename = $d->read())) ) {
			$info = pathinfo($filename);
			$ext = $info[extension];
			if ($ext == 'jpg') {
    	     	$filepath = $FileDir .'/'. $filename;
        	 	echo 'ImagesCopyFromDatabase2Disk filename: ' . $filename . '<br>';
    			//echo '<br>';
         	}
		}
		$d->close();
		*/
    }


	function Image() {
		$this->ImageFilePath = ''; //WebTmpDir();
		//$this->ImageFilePath = WebsiteRootDir() . '/var/webtmp';
		$this->ImageBinary = '';
		//echo 'ImageFilePath: ' . $this->ImageFilePath . '<br>';
		//echo 'WebsiteRootDir: ' . WebsiteRootDir() . '<br>';
	}

} // end class Image
