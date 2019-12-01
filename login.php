<?php
session_start(); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
$cstring = True;//this is to add true to the prototype below, get an error if done directly for some reason.
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));//this is to generate a 64 byte randomised token.
if (!isset($_SESSION['token'])) { //if session token doesn't exist we'll create one.
	$_SESSION['token'] = $token; //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
}
ini_set("display_errors", true);
include('classes/DB.php');

if (isset($_POST['login'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))[0]['username'];
		$verifiedd = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$username))[0]['verified'];
		if (!$verifiedd)
			die ('Please confirm your account via the email link sent!');
		if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
			echo 'Logged in successfully!';
			$cstrong = True;
			$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
			DB::query('INSERT INTO login_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
			setcookie("CID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
			setcookie("CID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
			session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
			header( "refresh:5; url=index.php" );
		} else {
			echo 'Incorrect Password!';
			session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
			header( "refresh:5; url=login.php" );
		}
	} else {
		echo 'User not registered! Please register a new account!';
		session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
		header( "refresh:5; url=login.php" );
	}
}
?>

<h1>Login to your account</h1>
<form action="login.php" method="post">
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="text" name="username" value="" placeholder="Username ..."><p />
<input type="password" name="password" value="" placeholder="Password ..."><p />
<input type="submit" name="login" value="Login">
</form>

<?php
include('footer.php');
?>
