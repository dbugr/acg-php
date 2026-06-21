<table  width="<?php echo $HalfTableWidth; ?>" border="<?php echo $border; ?>">
  <tr>
    <th align=left>Yes</th>
    <td class="right"><?php echo $formVars["CurrentYesAttendees"]; ?></td>
    </tr>

      <?php
	foreach ($Yes as $row) {

	  $string= "<tr>"
		   . "<td>";
	
	  if ($row['custProfile']) {
	    $string .=
	           '<a href="/members/mview.php?cust_id='
	           .trim($row["cust_id"])
	           .'"' 
	           .'target="'
	           .'profiles'
	           .'">'
	           .trim($row["custName"])
	           .'</a>';
	  } else {
              $string .=
	           trim($row["custName"]);
          }
	        

          if (!empty($row['r_comments'])) 
            $string .= ":&nbsp;&nbsp;&nbsp;"
	        . stripslashes($row["r_comments"]);

          if ($row["r_num_guests"])
            $string=$string
            . " (+ "
            . $row["r_num_guests"]
            . " guests)";

          $string= $string
            . "</td></tr>";

	  echo $string;
        }
		
      ?>
		
    </table>
