<?php


require('always.include.php');
//$debug = true;
//$debug = false;

//session_start();
//require('include.php');

$FileName = __FILE__;
$ClubCompanyName	= GetParameter('ClubCompanyName');
$WebPageTitle = 'Our Town Article - ' . $ClubCompanyName;
require('top.php');

?>

<div id="centercontent">


  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
    <tr>
      <td width="100%">
        </embed>&nbsp;<h3>
          <font size="5">Adventure Club of Gainesville<br>
            Featured in <i>Our Town Magazine</i></font>
        </h3>
        <p>Click on the images below to read the article:<p>
            <a href="images/page_28.jpg">
              <img border="0" src="images/ourtown1.jpg" width="222" height="316"></a><a href="images/page_29.jpg"><img border="0" src="images/ourtown2.jpg" width="222" height="316"></a>&nbsp;&nbsp;
            <a href="images/page_30.jpg">
              <img border="0" src="images/ourtown3.jpg" width="222" height="316"></a>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;
              [<a href="images/page_28.jpg">page 1</a>]&nbsp;&nbsp;&nbsp;&nbsp; [<a href="images/page_29.jpg">page
                2</a>]&nbsp;&nbsp;&nbsp;&nbsp; [<a href="images/page_30.jpg">page 3</a>]<p>&nbsp;<p>&nbsp;
      </td>
    </tr>
  </table>
</div>

<?php

require('footer.php');

?>