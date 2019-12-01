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
if (Loggedin::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
	include('send-messageheader.php');
} else {
	die('Not Logged In!');
}
if (isset($_POST['send'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if (DB::query('SELECT id FROM users WHERE id=:receiver', array(':receiver'=>$_GET['receiver']))) {
		DB::query("INSERT INTO messages VALUES (NULL, :body, :sender, :receiver, 0)", array(':body'=>$_POST['body'], ':sender'=>$userid, ':receiver'=>htmlspecialchars($_GET['receiver'])));
		echo "Message Sent!";
		header( "refresh:5; url=send-message.php" );
	} else {
		die('Invalid ID!');
	}
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
}
?>

<h1>Send a Message</h1>
<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']); ?>" method="post">
<textarea name="body" rows="8" cols="80"></textarea>
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="submit" name="send" value="Send Message">
</form>

<br>
<hr />
<br>
<div align="center">
<form action="index.php" method="post">
<input type="submit"  name="logoutbutton" value="Logout Button">
</form>
</div>
<br>
<br>

<?php
if (isset($_POST['logoutbutton'])) {
	if (isset($_COOKIE['CID'])) {
		DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
		echo 'Logged Out Successfully!';
	}
	setcookie('CID', '1', time()-3600);
	setcookie('CID_', '1', time()-3600);
	header( "refresh:5; url=index.php" );
}
include('footer.php');
?>
