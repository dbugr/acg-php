
<table cellpadding="4" width="<?php echo $HalfTableWidth; ?>" border="<?php echo $border; ?>">
  <tr><th bgcolor="#3399cc">
       <font color=white>Will You Attend?</font><br><font color="#ccffff">
(
  <?php
    $MaxAttendees = $formVars['max_attendees'];
    if (empty($MaxAttendees))
      $MaxAttendees = 5; 
    if (empty($CurrentYesAttendees))
      $CurrentYesAttendees = 0;
    $CurrentYesAttendees = $formVars["CurrentYesAttendees"];
    $NumSpotsLeft = ($MaxAttendees - $CurrentYesAttendees);
    echo $MaxAttendees . " - " . $CurrentYesAttendees . " = ";
    echo $NumSpotsLeft;
  ?>
     spots left)      </font></th>
  </tr>
	<tr>
	<td>
    <form method="POST" action="/members/eview-post.php">
    <input type=hidden name=event_id value="<?php echo $formVars['event_id']; ?>">
    <table>

    <tr>
    <td colspan=3>
    I have read and understand the club <a href="/faq.php#ReservationEtiquette" target="_blank">Reservation Etiquette</a>
    </td>
    </tr>

    <tr>
    <td colspan=3>
    Please reserve <input type=text name=guests size=3 value="<?php if (isset($formVars['guests'])) echo $formVars['guests']; ?>"> guests.
    </td>
    </tr>

    <tr>
    <td colspan=3>
    <input type="checkbox" name="liabilitywaiver" value="accept">I accept the <a href="/policies.php" target='policies'><?PHP echo $ShortClubName; ?> Policies and Liability waiver</a>
    </td>
    </tr>

    <tr>
    <td colspan=3>
    Comments:<br>
    <textarea name=comments rows=6 cols="22"><?php if (isset($formVars['comments'])) echo $formVars['comments']; ?></textarea></td>
    </tr>

    <tr>
    <td><input type="submit" name=attending value="Yes"></td>
    <td><input type="submit" name=attending value="WaitingList"></td>
    <td><input type="submit" name=attending value="No"></td>
    </tr>

    </table>
    </form>
	</td>
	</tr>
</table>

<?php
  if (($formVars['pay4event'] == 'Enable') and
      ($formVars['attending'] == 'Yes'))
	{
?>
<table border="0" width=330>
  <tr>
    <td>
    <form method="POST" action="<?php echo Pay4EventURL($event_id); ?>" target="pay4event">
<?php
	$raw = "cust_id=" . $cust_id . "&" . 
		"event_id=" . $event_id . "&" .
		"pmt_descr=" . $formVars["name"] . "&" .
		"e_deposit=" . $formVars["e_deposit"] . "&" .
		"e_deposit_date=" . $formVars["e_deposit_date"] . "&" .
		"e_fullprice=" . $formVars["e_fullprice"] . "&" .
		"e_fullprice_date=" . $formVars["e_fullprice_date"] . "&" .
		"login=" . $loginUsername;
	$raw = urlencode( base64_encode( $raw));
	$hash = crypt($raw, "pay4event");
?>    
		<input type=hidden name=raw value="<?php echo $raw; ?> ">
		<input type=hidden name=hash value="<?php echo $hash; ?> ">
		<input type=submit value="Pay for Event with Credit Card">
		</form>
    </td>
  </tr>
</table>
<?php
  }
?>

<?php
/*
	if ((!isset($formVars["r_amount_paid"]) or ($formVars["r_amount_paid"] == 0)) and
		(isset($formVars["attending"])))
	{
    echo "<form method=\"POST\" action=\"/members/eview-remove-post.php\">";
    echo "<input type=hidden name=event_id value='" . $formVars['event_id'] . "'>";
    echo "<input type=hidden name='r_attending' value='No' >";
    echo "<table width=330 border=0>";
    echo "<tr><td><input type=\"submit\" value=\"RemoveMe\"> from this event.</td></tr>";
    echo "</table>";
    echo "</form>";
	}
*/
	?>
