<?php
ini_set("display_errors", true);
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
<li><a href="login.php">Login</a></li>
<li><a href="forgot-password.php">Forgot Password</a></li>
<li><a href="create-account.php">Create an Account</a></li>
</ul>
<div style="padding:0 16px;">
<h2>Welcome To Camagru!</h2>
<p>Camagru is the ultimate social media platform...</p>
<p>Here you can share pictures, send messages, share your thoughts through posts and like just about everything.</p>
<p>This platform is Instagram, Twitter and Facebook combined.</p>
<p>Join today and encourage your friends and family to sign up as well.</p>
<h4>We Notice You Are Not Logged In. Please Use The Menu To Login Or Create A New Account!</h4>
</div>
</body>
</html>
