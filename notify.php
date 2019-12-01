<?php
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/LoggedIn.php');
if (LoggedIn::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
	include('notificationsheader.php');
} else {
	echo 'Not Logged In';
}
echo "<h1>Notifications</h1>";
if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
	$notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));
	foreach($notifications as $n) {
		if ($n['type'] == 1) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			if ($n['extra'] == "") {
				echo "You got a notification!<hr />"; //user should never see this but just adding as an extra safety measure
			} else {
				$length = strlen($n['extra']) - 13;
				$content = substr($n['extra'], 16, $length);
				$trimmedcontent = substr($content, 0, -4);
				echo $senderName." mentioned you in a post! - ".$trimmedcontent."<hr />";
			}
		} else if ($n['type'] == 2) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			echo $senderName." liked your post!<hr />";
		}
	}
}
?>

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
