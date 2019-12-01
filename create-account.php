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
require_once('classes/VerifyEmail.php');
if (isset($_POST['createaccount'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];//email to check

	if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		if (strlen($username) >= 3 && strlen($username) <= 32) {
			if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
				$pattern = '/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/';
				if (strlen($password) >= 6 && strlen($password) <= 60) {
					if (preg_match($pattern, $password)) {
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))){
							$mail = new VerifyEmail();
							$mail->setStreamTimeoutWait(20);//set the timeout value on stream
							$mail->Debug = FALSE;//set debug output mode!!CHANGE TO FALSE IF NOT TETSING!!
							$mail->Debugoutput = 'html';//set debug output mode
							$mail->setEmailFrom('murraylydie@gmail.com');//set email address for SMTP request
							if ($mail->check($email) && (verifyEmail::validate($email))) {
								DB::query('INSERT INTO users VALUES (NULL, :username, :password, :email, \'0\', NULL)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
								$cstrong = True;
								$linktoken = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
								$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
								DB::query('INSERT INTO password_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($linktoken), ':user_id'=>$user_id));
								Mail::sendMail('Welcome to Camagru!', 'Your account has been created successfully! Please verify your account by '."<a href='http://localhost:8080/camagru/verify-account.php?linktoken=$linktoken'>clicking on this link...</a>", $email);
								echo 'New user created successfully!';
								header( "refresh:5; url=login.php" );	
							} else if (!verifyEmail::validate($email)) {
								echo 'Email &lt;'.$email.'&gt; doesn\'t seem to exist...';
							} else {
								echo 'Email &lt;'.$email.'&gt; is not a valid email!';
							}
						} else {
							echo 'Email already in use! Please use a different email!';
						}
					} else {
						echo 'Invalid email! Please enter a valid email address!';
					}
					} else {
						echo 'Please enter a complex password containing at least 1 uppercase letter, at least 1 lowercase letter, at least one number, and at least one special char!';	
					}} else {
					echo 'Invalid password! Password length must be between 6 & 60 characters!';
				}
			} else {
				echo 'Invalid username! Must contain a-z, A-Z, 0-9, _ only!';
			}
		} else {
			echo 'Invalid username! Must be between 3 & 32 characters!';
		}
	} else {
		echo 'User already exists! Please try logging in!';
	}
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
}
?>

<h1>Register</h1>
<form action="create-account.php" method="post">
<input type="text" name="username" value="" placeholder="Username ..."><p />
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="password" name="password" value="" placeholder="Password ..."><p />
<input type="email" name="email" value="" placeholder="me@somesite.com"><p />
<input type="submit" name="createaccount" value="Create Account">
</form>

<?php
include('footer.php');
?>
