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

$tokenIsValid = False;
if (LoggedIn::isLoggedIn()) {
	if (isset($_POST['changepassword'])) {
		if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
			die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
		}
		if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
			die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
		}
		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];
		$confirmnewpassword = $_POST['confirmnewpassword'];
		$userid = LoggedIn::isLoggedIn();
		if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])) {
			if ($newpassword == $confirmnewpassword) {
				if (strlen($newpassword) >= 6 && strlen($newpassword) <=60) {
					DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
					echo 'Password Changed Successfully!';
					DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
					setcookie('CID', '1', time()-3600);
					setcookie('CID_', '1', time()-3600);
					header( "refresh:5; url=login.php" );
				} else {
					echo 'Invalid password! Password length must be between 6 & 60 characters!';
				}
			} else {
				echo 'Passwords Don\'t Match!';
			}
		} else {
			echo 'Incorrect Current Password Entered!';
		}
		session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
	}
} else {
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id']) {
			$user_id = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			$tokenIsValid = True;
			if (isset($_POST['changepassword'])) {
				$newpassword = $_POST['newpassword'];
				$confirmnewpassword = $_POST['confirmnewpassword'];
				if ($newpassword == $confirmnewpassword) {
					if (strlen($newpassword) >= 6 && strlen($newpassword) <=60) {
						DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$user_id));
						echo 'Password Changed Successfully!';
						DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$user_id));
						DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
						setcookie('CID', '1', time()-3600);
						setcookie('CID_', '1', time()-3600);
						header( "refresh:5; url=login.php" );
					}
				} else {
					echo 'Passwords Don\'t Match!';
				}
			}
		} else {
			die('Token Invalid!');
		}
	} else {
	die('Not Logged In!');
	}
}
?>

<h1>Change Your Password</h1>
<form action="<?php if (!$tokenIsValid) { echo 'change-password.php'; } else { echo 'change-password.php?token='.$token.''; } ?>" method="post">
<?php if (!$tokenIsValid) { echo '<input type="password" name="oldpassword" value="" placeholder="Current Password ..."><p />'; } ?>
<input type="password" name="newpassword" value="" placeholder="New Password ..."><p />
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="password" name="confirmnewpassword" value="" placeholder="Confirm New Password ..."><p />
<input type="submit" name="changepassword" value="Change Password">
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
