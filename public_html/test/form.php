<html>

<head>
	<title>Google recaptcha v3 demo</title>
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
</head>

<body>
	<h1>Google reCAPTCHA Demo</h1>

	<form id="comment_form" action="form-post.php" method="post">
		<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
		<input type="hidden" name="action" value="validate_captcha">
		<input type="email" name="email" placeholder="Type your email" size="40"><br><br>
		<textarea name="comment" rows="8" cols="39"></textarea><br><br>
		<input type="submit" name="submit" value="Post comment"><br><br>
	</form>

</body>

</html>