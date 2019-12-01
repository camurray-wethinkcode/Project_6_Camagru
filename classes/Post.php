<?php
include('Notify.php');
class Post {
	public static function createPost($postbody, $loggedInUserId, $profileUserId) {
			$loggedInUserId = LoggedIn::isLoggedIn();
			if (strlen($postbody) > 160 || strlen($postbody) < 1) {
				die('Incorrect Length Of Post, Must Be > 1 And < 160 Characters!');
			}
			$topics = self::getTopics($postbody);
			if ($loggedInUserId == $profileUserId) {
				if (count(Notify::createnotify($postbody)) != 0) {
					foreach(Notify::createnotify($postbody) as $key => $n) {
						$r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
						$s = $loggedInUserId;
						if ($r != 0) {
							DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
						}
					}
				}
				DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0, NULL, :topics)', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
			} else {
				die('Incorrect User!');
			}
	}

	public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {
			if (strlen($postbody) > 160) {
				die('Incorrect Length Of Post, Must Be < 160 Characters!');
			}
			$topics = self::getTopics($postbody);
			if ($loggedInUserId == $profileUserId) {
				if (count(Notify::createnotify($postbody)) != 0) {
					foreach(Notify::createnotify($postbody) as $key => $n) {
						$r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
						$s = $loggedInUserId;
						if ($r != 0) {
							DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
						}
					}
				}
				DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0, NULL, :topics)', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
				$postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY id DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
				return $postid;
			} else {
				die('Incorrect User!');
			}
	}

	public static function likePost($postId, $likerId) {
			if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
				DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
				DB::query('INSERT INTO post_likes VALUES (NULL, :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
				Notify::createnotify("", $postId);
			} else {
				echo 'Already Liked!';
				DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
				DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
			}
	}

	public static function getTopics($text) {
		$text = explode(" ", $text);
		$topics = "";
		$loopcount = 0;
		foreach($text as $word) {
			if (substr($word, 0, 1) == "#") {
				if ($loopcount > 0) {
					$topics .= ", ".substr($word, 0);
					$loopcount++;
				} else {
					$topics .= substr($word, 0);
					$loopcount++;
				}
			}
		}
		return $topics;
	}

	public static function link_add($text) {
		$text = explode(" ", $text);
		$newstring = "";
		foreach($text as $word) {
			if (substr($word, 0, 1) == "@") {
				$newstring .= "<a href='profile.php?username=".substr($word, 1)."?page=1'>".htmlspecialchars($word)." </a>";
			} else if (substr($word, 0, 1) == "#") {
				$newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";
			} else {
				$newstring .= htmlspecialchars($word)." ";
			}
		}
		return $newstring;
	}

	public static function displayPosts($userid, $username, $loggedInUserId) {
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
		$posts = "";
		foreach($dbposts as $p) {
			if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
				if ($p['postimg'] == NULL) {
					$posts .= self::link_add($p['body'])."
					<form action='profile.php?username=$username?page=1&postid=".$p['id']."' method='post'>
					<input type='submit' name='like' value='Like'>
					<span>".$p['likes']." likes</span>
					";
					if ($userid == $loggedInUserId) {
						$posts .= "<input type='submit' name='deletepost' value='x' />";
					}
					$posts .= "
					</form><hr /></br />";
				}
				else if ($p['postimg'] != NULL) {
					$posts .= "<img src='.".$p['postimg']."'>"."</br />".self::link_add($p['body'])."
					<form action='profile.php?username=$username?page=1&postid=".$p['id']."' method='post'>
					<input type='submit' name='like' value='Like'>
					<span>".$p['likes']." likes</span>
					";
					if ($userid == $loggedInUserId) {
						$posts .= "<input type='submit' name='deletepost' value='x' />";
					}
					$posts .= "
					</form><hr /></br />";
				}
			} else {
				if ($p['postimg'] == NULL) {
					$posts .= self::link_add($p['body'])."
					<form action='profile.php?username=$username?page=1&postid=".$p['id']."' method='post'>
					<input type='submit' name='unlike' value='Unike'>
					<span>".$p['likes']." likes</span>
					";
					if ($userid == $loggedInUserId) {
						$posts .= "<input type='submit' name='deletepost' value='x' />";
					}
					$posts .= "
					</form><hr /></br />";
				}
				else if ($p['postimg'] != NULL) {
					$posts .= "<img src='.".$p['postimg']."'>"."</br />".self::link_add($p['body'])."
					<form action='profile.php?username=$username?page=1&postid=".$p['id']."' method='post'>
					<input type='submit' name='unlike' value='Unlike'>
					<span>".$p['likes']." likes</span>
					";
					if ($userid == $loggedInUserId) {
						$posts .= "<input type='submit' name='deletepost' value='delete post' />";
					}
					$posts .= "
					</form><hr /></br />";
				}
			}
		}
		return $posts;
	}
}
?>
