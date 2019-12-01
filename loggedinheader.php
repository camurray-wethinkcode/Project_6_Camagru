<?php
ini_set("display_errors", true);
include_once('classes/DB.php');
include_once('classes/LoggedIn.php');
if (LoggedIn::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
} else {
	echo 'Not Logged In';
}
$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid));
$name = $username[0][0];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {margin: 0;}
ul.header {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: MistyRose;
}
ul.header li {float: left;}
ul.header li a {
  display: block;
  color: Indigo;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}
ul.header li a:hover:not(.active) {background-color: Fuchsia;}
ul.header li a.active {background-color: White;}
ul.header li.right {float: right;}
@media screen and (max-width: 600px) {
  ul.header li.right,
  ul.header li {float: none;}
}
</style>
</head>
<body>
<ul class="header">
<li><a class="active" href="index.php">Home</a></li>
<li><a href="logout.php">Logout</a></li>
<li><a href="my-account.php">My Account</a></li>
<li><a href="profile.php?username=<?php echo $name ?>?page=1">My Profile</a></li>
</ul>
<div style="padding:0 16px;">
<h2>Welcome Back! We Missed You!</h2>
<p>Please use the menu bar above to navigate around the website.</p>
<p>Clicking on the "Home" button at any time will bring you back here.</p>
<p>Clicking on the "Logout Button" at the bottom of each page at any time will log you out in one click.</p>
<p>Clicking on the "Logout" tab at any time will take you to the logout page.</p>
<p>Clicking on the "My Account" tab at any time will take you to your account page where you can change your profile picture, and account settings.</p>
<p>Clicking on the "My Profile" tab at any time will take you to your profile page where you can view and make posts, and have access to your notifications and messages.<p>
<h4>Have An Awesomely Epic Day!</h4>
</div>
</body>
</html>
