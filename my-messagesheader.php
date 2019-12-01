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
<li><a href="send-message.php">Send A Message</a></li>
<li><a href="notify.php">Notifications</a></li>
</ul>
</div>
</body>
</html>
