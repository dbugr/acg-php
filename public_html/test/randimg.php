<?php
// displays public web page header
require('/home/advclub/public_html/include/always.include.php');
?>
<html>
<body>
<table>
<tr>
	<td><div id="randomImage"><?php @readfile(GetParameter('GalleryURL')."/index.php/randimg");?>></div></td>

    <td><div id="randomImage"><?php @readfile(GetParameter('GalleryURL')."/index.php/randimg");	?></div></td>
    
	<td><div id="randomImage"><?php @readfile(GetParameter('GalleryURL')."/index.php/randimg"); ?></div></td>
</tr>

</table>
</body>
<html>