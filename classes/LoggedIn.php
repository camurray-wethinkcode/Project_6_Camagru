<?php

class LoggedIn {
	public static function isLoggedIn() {
		if (isset($_COOKIE['CID'])) {
			if (DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])))) {
				$userid = DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])))[0]['user_id'];
				if (isset($_COOKIE['CID_'])) {
					return $userid;
				} else {
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
					DB::query('INSERT INTO login_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$userid));
					DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
					setcookie("CID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
					setcookie("CID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
					return $userid;
				}
			}
		}
		return false;
	}
}
?>
