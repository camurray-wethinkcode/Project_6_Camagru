<?php
class Notify {
	public static function createnotify($text = "") {
		$text = explode(" ", $text);
		$notify = array();
		foreach($text as $word) {
			if (substr($word, 0, 1) == "@") {
				$notify[substr($word, 1)] = array("type"=>1, "extra"=>' { "postbody": "'.htmlentities(implode($text, " ")).'" } ');//htmlentities escapes double quotes in the post text as a security measure
			}
		}
		if (count($text) == 0 && $postid != 0) {
			$temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id FROM posts AS sender, post_likes WHERE posts.id = post_likes.post_id AND posts.id=:postid', array(':postid'=>$postid));
			$r = $temp[0]["receiver"];
			$s = $temp[0]["sender"];
			DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :extra)', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>""));
		}
		return $notify;
	}
}
?>
