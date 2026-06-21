<?php $thisPage = "navpublic"; ?>
<?PHP
// navpublic.php
// user is NOT logged in
// display public menu system
?>
<div id="navcontainer">
	<ul id="navlist">

		<?php if ($_SERVER['REQUEST_URI'] == '/index.php' or ($thisPage == "home")) { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home</li>
		<?php } else { ?>
			<li><a href="/index.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home</a></li>
		<?php } ?>
		<?php if ($_SERVER['REQUEST_URI'] == '/faq.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FAQ</li>
		<?php } else { ?>
			<li><a href="/faq.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FAQ</a></li>
		<?php } ?>
		<?php if ($_SERVER['REQUEST_URI'] == '/elist-pub.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Events</li>
		<?php } else { ?>
			<li><a href="/elist-pub.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Events</a></li>

		<?php } ?>
		<li><a href="https://www.facebook.com/AdventureClubGainesville/photos" target="_blank">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facebook</a></li>

		<!--	
<?php if ($_SERVER['REQUEST_URI'] == '/links.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Links/Discounts</li>
<?php } else { ?>
<li><a href="/links.php">
        Links/Discounts</a></li>
<?php } ?>
-->

		<?php if ($_SERVER['REQUEST_URI'] == '/highfives.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;High Fives</li>
		<?php } else { ?>
			<li><a href="/communityservice.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Community Service</a></li>
		<?php } ?>
		<li><a href="<?php echo JoinURL(); ?>">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Join</a></li>
		<?php if ($_SERVER['REQUEST_URI'] == '/contact.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact Us</li>
		<?php } else { ?>
			<li><a href="/contact.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact Us</a></li>
		<?php } ?>

	</ul>
</div><br>

<!--[if !IE]><!-->
<p>Bookmark or Share Our Site: </p>
<a class="a2a_dd" href="https://www.addtoany.com/share_save?linkname=Adventure%20Club%20of%20Gainesville&amp;linkurl=https%3A%2F%2Fadventureclub.info%2F"><img src="https://static.addtoany.com/buttons/share_save_171_16.png" width="171" height="16" border="0" alt="Share/Bookmark" /></a>
<script type="text/javascript">
	a2a_linkname = "Adventure Club of Gainesville";
	a2a_linkurl = "https://adventureclub.info/";
</script>
<script type="text/javascript" src="https://static.addtoany.com/menu/page.js"></script>
<br /><br />
<!--<![endif]-->
<!--[if IE]>
Bookmark or Share Our Site: <p>
<a class="a2a_dd" href="http://www.addtoany.com/share_save?linkname=Adventure%20Club%20of%20Gainesville&amp;linkurl=http%3A%2F%2Fadventureclub.info%2F"><img src="http://static.addtoany.com/buttons/share_save_171_16.png" width="171" height="16" border="0" alt="Share/Bookmark"/></a><script type="text/javascript">a2a_linkname="Adventure Club of Gainesville";a2a_linkurl="http://adventureclub.info/";</script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>
<br />
<A HREF="#"
onClick="this.style.behavior='url(#default#homepage)';
this.setHomePage();"><img src="/images/makehome.gif" border="0" vspace="8"></A>
<br />
<![endif]-->