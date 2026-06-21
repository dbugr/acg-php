<?php

require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');


$FileName = $_SERVER['PHP_SELF'];
$ClubCompanyName  = GetParameter('ClubCompanyName');
$WebPageTitle = 'Subscribe With PayPal to Register - ' . $ClubCompanyName;
require('top.php');

$thisPage = "paypal";
?>

<div id="centercontent">

  <table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        <h3>
          <font size="5" color="#008000">Please Subscribe to Complete Your
            Registration!</font>
        </h3>
        <p>
          Click the <b>Subscribe</b> button below to complete your registration, and check out
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
              <li>Paypal will automatically renew your subscription annually.&nbsp; </li>
              <li>To cancel, login to your Paypal account and cancel the automatic annual charges.&nbsp; </li>
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