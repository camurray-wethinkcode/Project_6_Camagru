<?php
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/Mail.php');
require_once('classes/VerifyEmail.php');
if (isset($_GET['linktoken'])) {
	$linktoken = $_GET['linktoken'];
		if (DB::query('SELECT user_id FROM password_tokens WHERE token=:linktoken', array(':linktoken'=>sha1($linktoken)))[0]['user_id']) {
			$user_id = DB::query('SELECT user_id FROM password_tokens WHERE token=:linktoken', array(':linktoken'=>sha1($linktoken)))[0]['user_id'];
			$tokenIsValid = True;
			DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$user_id));
			echo 'Your email account has been verified! Thank you. You will be redirected to the login page shortly...';
			DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$user_id));
			DB::query('DELETE FROM login_tokens WHERE token=:linktoken', array(':linktoken'=>sha1($_COOKIE['CID'])));
			setcookie('CID', '1', time()-3600);
			setcookie('CID_', '1', time()-3600);
			header( "refresh:5; url=login.php" );
	} else {
		die('Link Token Invalid!');
	}
}
include('footer.php');
?>
