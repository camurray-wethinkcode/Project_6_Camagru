<?php
session_start(); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
$cstring = True;//this is to add true to the prototype below, get an error if done directly for some reason.
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));//this is to generate a 64 byte randomised token.
if (!isset($_SESSION['token'])) { //if session token doesn't exist we'll create one.
	$_SESSION['token'] = $token; //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
}
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/Mail.php');
if (isset($_POST['resetpassword'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	$cstrong = True;
	$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
	$email = $_POST['email'];
	$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
	DB::query('INSERT INTO password_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
	Mail::sendMail('Password Reset - Camagru', "<a href='http://localhost:8080/camagru/change-password.php?token=$token'>Reset your password by clicking on this link...</a>", $email);
	echo 'Email sent!';
	echo '<br />';
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
	header( "refresh:5; url=index.php" );
}
?>

<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="text" name="email" value="" placeholder="Email ..."><p />
<input type="submit" name="resetpassword" value="Reset Password">
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
