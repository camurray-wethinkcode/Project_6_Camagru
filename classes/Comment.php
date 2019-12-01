<?php
class Comment {
	public static function createComment($commentBody, $postId, $userId) {
		if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
			die('Incorrect Length Of Post, Must Be > 1 And < 160 Characters!');
		}
		if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
			echo 'Invalid Post ID';
		} else {
			DB::query('INSERT INTO comments VALUES (NULL, :comment, :userid, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':postid'=>$postId));
		}
	}

	public static function displayComments($postId) {
		$comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id = users.id', array(':postid'=>$postId));
		foreach($comments as $comment) {
			echo $comment['comment']." ~ ".$comment['username'].'<br>';
		}
	}
}
?>
