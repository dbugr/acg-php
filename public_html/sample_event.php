<?php
/*
AdventureClub.info
st
*/
  // display list of events
  // user can click on an event name to view details

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
$ClubCompanyName = GetParameter('ClubCompanyName');
$WebPageTitle = 'Sample Event - ' . $ClubCompanyName;
require('top.php');

$thisPage="service";
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td> 
      <p><font size="4" color="#008000"><b>Sample Event: How Our Members See It</b></font></p>
	
		<p>Once you are a member, you receive a username and password, so you 
        can login to our website and see details for all events.&nbsp; Here's an 
        example of what our members see (last names removed to protect members' 
        privacy):</p>
      <p>
                  <a href="<?php echo GetParameter('vd').'/join.php'; ?>">
                  <img border="0" src="images/btn_join_now.jpg" alt="Join Now" width="126" height="38"></a><font size="5">
                  </font><font size="4">...&nbsp;to become a member!</font></p>
      <p><img border="0" src="images/event.gif" width="627" height="2705"></p>
      <p><font size="4">Like What You See? </font></p>
      <p>
                  <a href="<?php echo GetParameter('vd').'/join.php'; ?>">
                  <img border="0" src="images/btn_join_now.jpg" alt="Join Now" width="126" height="38"></a></p>
	  
	  
 </td>
  </tr>
</table>

<?php 
require('footer.php');?>