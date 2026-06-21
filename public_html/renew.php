<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Renew Your Membership - ' . $ClubCompanyName;
require('top.php');

$thisPage = "renew";
?>

<div id="centercontent">


  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        <h3>
          <font size="5" color="#008000">Renew Your Adventure Club Membership!</font>
        </h3>
        <p>
          Click the <b>Subscribe</b> button below to renew, and check out
          using PayPal, a secure payment processor used worldwide:<p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
              <input type="hidden" name="cmd" value="_s-xclick">
              <input type="hidden" name="hosted_button_id" value="3YM7YSN5NZ7UA">
              <blockquote>
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" width="147" height="47">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
              </blockquote>
            </form>
            <ul>
              <li>We collect a website usage fee of $36 per year.&nbsp; </li>
              <li>Your subscription will automatically renew annually.&nbsp; </li>
              <li>To cancel, login to paypal and cancel your automatic payment.&nbsp; </li>
            </ul>
            <p>
              &nbsp;
      </td>
    </tr>
  </table>
</div>

<?php

require('footer.php');
?>