<?php $thisPage = "navmembers"; ?>
<?PHP
// navmembers.php
// user IS logged in?
// begin user IS logged in
// display members only menu system
?>
<?php
//$firephp->log($loginUsername,"Nav:loginUsername");
DisplayLogin($MemberInfo);
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
		<?php if ($_SERVER['REQUEST_URI'] == '/members/elist.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Events</li>
		<?php } else { ?>
			<li><a href="/members/elist.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Events</a></li>
		<?php } ?>
		<?php if ($_SERVER['REQUEST_URI'] == '/members/mlist.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Members</li>
		<?php } else { ?>
			<li><a href="/members/mlist.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Members</a></li>
		<?php } ?>
		<?php if ($_SERVER['REQUEST_URI'] == '/members/medit.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;My Profile</li>
		<?php } else { ?>
			<li><a href="/members/medit.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;My Profile</a></li>
		<?php } ?>

		<!--
	<?php if ($_SERVER['REQUEST_URI'] == '/members/servicestradebarter.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Services Trade/Barter</li>
<?php } else { ?>
<li><a href="/members/servicestradebarter.php">
        Services Trade/Barter</a></li>
<?php } ?>
-->


		<!--
	<?php if ($_SERVER['REQUEST_URI'] == '/members/fitnessexercise.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fitness &amp; Exercise</li>
<?php } else { ?>
<li><a href="/members/fitnessexercise.php">
        Fitness &amp; Exercise</a></li>
<?php } ?>
-->

		<!--
	<?php if ($_SERVER['REQUEST_URI'] == '/members/leaderstuff.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Leader Stuff</li>
<?php } else { ?>
<li><a href="/members/leaderstuff.php">
        Leader Stuff</a></li>
-->

	<?php } ?>
	<li><a href="https://www.facebook.com/AdventureClubGainesville/photos" target="_blank">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facebook</a></li>

	<?php if ($_SERVER['REQUEST_URI'] == '/highfives.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;High Fives</li>
	<?php } else { ?>
		<li><a href="/communityservice.php">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Community Service</a></li>
	<?php } ?>

	<!--
	<?php if ($_SERVER['REQUEST_URI'] == '/links.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Links/Discounts</li>
<?php } else { ?>
<li><a href="/links.php">
        Links/Discounts</a></li>
<?php } ?>
-->

	<?php if ($_SERVER['REQUEST_URI'] == '/contact.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact Us</li>
	<?php } else { ?>
		<li><a href="/contact.php">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact Us</a></li>
	<?php } ?>

	<?php
	$loginUsername = LoginUsername();
	$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
	if ($AdminLevel) {
	?>

		<?php if ($_SERVER['REQUEST_URI'] == '/admin/mlist.php') { ?><li class="navlinkoff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Admin</li>
		<?php } else { ?>
			<li><a href="/admin/index.php">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Admin</a></li>
		<?php } ?>
	<?php } ?>


	</ul>
</div><br>

<!--[if !IE]><!-->
<p>Bookmark or Share Our Site: </p>
<a class="a2a_dd" href="https://www.addtoany.com/share_save?linkname=Adventure%20Club%20of%20Gainesville&amp;linkurl=http%3A%2F%2Fadventureclub.info%2F"><img src="https://static.addtoany.com/buttons/share_save_171_16.png" width="171" height="16" border="0" alt="Share/Bookmark" /></a>
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