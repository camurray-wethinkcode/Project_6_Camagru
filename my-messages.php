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
	include('my-messagesheader.php');
} else {
	die('Not Logged In!');
}
if (isset($_GET['mid'])) {//mid is message id
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	$message = DB::query('SELECT * FROM messages WHERE id=:mid AND (receiver=:receiver OR sender=:sender)', array(':mid'=>$_GET['mid'], ':receiver'=>$userid, ':sender'=>$userid))[0];//this is to see the individual message
	$messages = DB::query('SELECT messages.*, users.username FROM messages, users WHERE receiver=:receiver OR sender=:sender AND users.id = messages.sender', array(':receiver'=>$userid, ':sender'=>$userid));//this is to access the username
	echo '<h1>View Message</h1><br><br>';
	echo htmlspecialchars($message['body']).'<hr />';
	foreach ($messages as $getusername) {
		if ($message['sender'] == $userid) {
			$id = $message['receiver']." from ".$getusername['username'];
			$idforheader = $message['receiver'];
		} else if ($message['receiver'] == $userid) {
			$id = $message['sender']." from ".$getusername['username'];
			$idforheader = $message['sender'];
		}
	}
	DB::query('UPDATE messages SET isread=1 WHERE id=:mid', array(':mid'=>$_GET['mid']));
?>

<h1>Send a Reply</h1>
<form action="send-message.php?receiver=<?php echo $idforheader; ?>" method="post">
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<textarea name="body" rows="8" cols="80"></textarea>
<input type="submit" name="send" value="Send Message">
</form>

<?php
} else {
?>

	<h1>My Messages</h1>

<?php
	$messages = DB::query('SELECT messages.*, users.username FROM messages, users WHERE receiver=:receiver OR sender=:sender AND users.id = messages.sender', array(':receiver'=>$userid, ':sender'=>$userid));//this is to access the username
	foreach ($messages as $message) {
		if (strlen($message['body']) > 10) {
			$m = substr($message['body'], 0, 10)."...";
		} else {
			$m = $message['body'];
		}
		if ($message['isread'] == 0 && (($message['sender'] == $userid) || ($message['receiver'] == $userid))) {
			echo "<a href='my-messages.php?mid=".$message['id']."'><strong>".$m."</strong></a> sent by ".$message['username'].'<hr />';
		} else {
			echo "<a href='my-messages.php?mid=".$message['id']."'>".$m."</a> sent by ".$message['username'].'<hr />';
		}
	}
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
}
?>

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
