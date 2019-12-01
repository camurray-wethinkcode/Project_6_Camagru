<?php
session_start(); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
$cstring = True;//this is to add true to the prototype below, get an error if done directly for some reason.
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));//this is to generate a 64 byte randomised token.
if (!isset($_SESSION['token'])) { //if session token doesn't exist we'll create one.
	$_SESSION['token'] = $token; //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
}
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/LoggedIn.php');

if (!LoggedIn::isLoggedIn()) {
	die("Not logged in!");
}
if (isset($_POST['confirm'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if (isset($_POST['alldevices'])) {
		DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>LoggedIn::isLoggedIn()));
		echo 'Logged Out Of All Devices Successfully!';
		setcookie('CID', '1', time()-3600);
		setcookie('CID_', '1', time()-3600);
		session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
		header( "refresh:5; url=index.php" );
	} else {
		if (isset($_COOKIE['CID'])) {
			DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
			echo 'Logged Out Successfully!';
			setcookie('CID', '1', time()-3600);
			setcookie('CID_', '1', time()-3600);
			session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
			header( "refresh:5; url=index.php" );
		}
	}
}
?>

<h1>Logout of your Account?</h1>
<p>Are you sure you'd like to logout?</p>
<form action="logout.php" method="post">
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
<input type="submit" name="confirm" value="Confirm"><br><br>
</form>

<?php
include('footer.php');
?>
