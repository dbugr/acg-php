<?php
// displays public web page header
require('always.include.php');

$a = array();
$a[] = rand(1,14);
$a[] = rand(1,14);
$a[] = rand(1,14);

$img1 = $a[0];
$img2 = 'a'.$a[1];
$img3 = 'b'.$a[2];

?>
<html>

<body>
	<table>
		<tr>
			<td>
				<div id="randomImage"><?php @readfile(PublicDomainName() . "/images/".$img1); ?>></div>
			</td>

			<td>
			<div id="randomImage"><?php @readfile(PublicDomainName() . "/images/".$img2); ?>></div>
			</td>

			<td>
			<div id="randomImage"><?php @readfile(PublicDomainName() . "/images/".$img3); ?>></div>
			</td>
		</tr>

	</table>
</body>
<html>