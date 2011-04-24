<html>
	<head>
		<title><?=Settings::get("application.system.site.name")?> API</title>
		<style type="text/css">
			body {
				padding:20px;
				color:#444;
				background:#efefef;
			}
		</style>
	</head>
	<body>
		
		<h1><?=Settings::get("application.system.site.name")?> API</h1><hr />
		
		<h2>Interactive Actions</h2>
		<p>
			Each of these actions expect XML input sent to the server to process the action.  You can click on any of the URLs listed below to see an example of what XML I'm expecting to receive.
		</p>
		<ul>
			<li><a href="<?=ROOT_URL?>api/authenticate/true"><?=ROOT_URL?>api/authenticate/</a> checks to see if user exists, returns separate error messages for username not found, account locked, etc</li>
			<li><a href="<?=ROOT_URL?>api/register/true"><?=ROOT_URL?>api/register/</a> only call for new users, use authenticate if the user already exists. Will throw error if username is already</li>
			<li><a href="<?=ROOT_URL?>api/lock-account/true"><?=ROOT_URL?>api/lock-account/</a> call this after 3 failed login attempts, or whatever application logic dictates this (if used)</li>
			<li><a href="<?=ROOT_URL?>api/track/true"><?=ROOT_URL?>api/track/</a> call with an item_id, action_id, &amp; user_id to let me know which user performed what action on what item :)</li>
			<li><a href="<?=ROOT_URL?>api/reset-password/true"><?=ROOT_URL?>api/reset-password/</a> don't believe we are using this one, as we don't have passwords for the accounts</li>			
			<li><a href="<?=ROOT_URL?>api/mail/true"><?=ROOT_URL?>api/mail/</a> processes a contact form request (takes a username &amp; message currently)</li>
		</ul>
		
		<h2>Passive Actions</h2>
		<p>
			These are all passive, simply returning XML. "update" is for the adobe air update mechanism (application itself), the other three are for content versioning.
		</p>
		<ul>
			<li><a href="<?=ROOT_URL?>api/content/"><?=ROOT_URL?>api/content/</a></li>
			<li><a href="<?=ROOT_URL?>api/version/"><?=ROOT_URL?>api/version/</a></li>
			<li><a href="<?=ROOT_URL?>api/meta/"><?=ROOT_URL?>api/meta/</a></li>
			<li><a href="<?=ROOT_URL?>api/update/"><?=ROOT_URL?>api/update/</a></li>
		</ul>
	</body>
</html>