<?php
// first html output; top of html
$styletag = '<style type="text/css">@import url("/styles.css");</style>';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<?php
	echo MetaTags($FileName);
	?>
	<title>
		<?php
		echo $WebPageTitle;
		?>
	</title>

	<?php
	echo $styletag;
	//<style type="text/css">@import url("/styles.css");</style>
	?>

	<script src="https://www.google.com/recaptcha/api.js?render=6Lc2iqkUAAAAAOAvarTVyDK4iMY3hvE4hOBTtbLv"></script>
	<script>
		grecaptcha.ready(function() {
			// do request for recaptcha token
			// response is promise with passed token
			grecaptcha.execute('6Lc2iqkUAAAAAOAvarTVyDK4iMY3hvE4hOBTtbLv', {
					action: 'validate_captcha'
				})
				.then(function(token) {
					// add token value to form
					document.getElementById('g-recaptcha-response').value = token;
				});
		});
	</script>
	<script>
		<!--
		function sendMailTo(name, company, domain) {
			locationstring = 'mai' + 'lto:' + name + '@' + company + '.' + domain;
			window.location.replace(locationstring);
		}
		-->
	</script>

	<link rel=stylesheet href="/include/xc2_default.css" type="text/css">
	<script language="javascript" src="/include/xc2_default.js"></script>
	<script language="javascript">
		xcDateFormat = "mm/dd/yyyy";
	</script>
	<script language="javascript" src="/include/xc2_inpage.js"></script>
	<script src="/include/slideshow.js" type="text/javascript"></script>
	<link rel="shortcut icon" href="/favicon.ico">
	<style type="text/css">
		img {
			border: none;
		}

		#randomImage {
			border: 2px solid white;
			margin: 0 20px 0 0;
			padding: 3px 3px 0 3px;
		}

		body {
			text-align: center;
		}
	</style>

	<!-- Matomo -->
	<script type="text/javascript">
		var _paq = window._paq || [];
		/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);
		(function() {
			var u = "//matomo.keysolv.com/matomo/";
			_paq.push(['setTrackerUrl', u + 'matomo.php']);
			_paq.push(['setSiteId', '1']);
			var d = document,
				g = d.createElement('script'),
				s = d.getElementsByTagName('script')[0];
			g.type = 'text/javascript';
			g.async = true;
			g.defer = true;
			g.src = u + 'matomo.js';
			s.parentNode.insertBefore(g, s);
		})();
	</script>
	<!-- End Matomo Code -->


</head>

<?php
if (isset($BackgroundDomain)) {
	// special exception for ~/public_html/cc-gnv/accepted.php and declined.php
	echo "<body background=\"https://" . SecureDomainName() . "/images/back1.jpg\">";
} else {
	echo "<body bgcolor=\"#4992D6\">";
	// echo "<body background="".$protocol.$domain."/images/back1.jpg/">";
}
//echo "Join: " . $join . "<br>";

// figure out which header file and navigation menu to use
if (isset($join)) {
	// new member registration/signup so use credit card header
	require('header-cc.php');
} else if (SessionIsRegistered('loginUsername')) {
	// user is logged in
	require('headermembers.php');
	//$firephp->log($MemberInfo->GetMemberFirstName(),"Top:GetMemberFirstName");
	$AdminLevel = $MemberInfo->GetMemberAuthLevel() == 'Admin';
	if (isset($admin) && $AdminLevel) {
		require('navadmin.php');
	} else {
		require('navmembers.php');
	}
	// user is logged in
	// and not handling a credit card transaction
	// then display shoutbox system
	//if (isset($EnableShoutbox))
	//	require(HomeDir().'/public_html/shoutbox/shoutbox.inc.php');
	/*
			<iframe src="<?php echo $RelOffset;?>tagbox/" width="185" height="400" frameborder="0" border="0"></iframe>
			*/
} else {
	// user not logged in, so display public header
	require('headerpublic.php');
	require('navpublic.php');
}

if (!isset($IndexPage)) {
	if (!isset($NoPhoto)) {
		echo '';
	}
	echo '</td>';
	echo '<td width="75%" valign="top">';
}
?>