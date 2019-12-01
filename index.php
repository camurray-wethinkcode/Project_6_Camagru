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
include('classes/Post.php');
include('classes/Comment.php');
$showTimeline = False;
if (LoggedIn::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
	$IsLoggedIn = True;
	include('loggedinheader.php');
	echo '<hr />';
	$showTimeline = True;
} else {
	include('loggedoutheader.php');
	$IsLoggedIn = False;
	echo '<hr />';
}
if (isset($_POST['like']) || isset($_POST['unlike'])) {
	Post::likePost($_GET['postid'], $userid);
}
if (isset($_POST['comment'])) {
	Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
}
if (isset($_POST['searchbox'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	$searchfor = explode(" ", $_POST['searchbox']);
	if (count($searchfor) == 1) {//this is so we don't have to have the entire name correct to be able to find a user (spelling)
		$searchfor = str_split($searchfor[0], 2);//str_split takes string and searchfor is an array of 1 because it was exploded so we use an index
	}
	$whereSQL = "";//only one where is allowed in our statement so this is a workaround for that so we can have 2
	$paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');
	for ($i = 0; $i < count($searchfor); $i++) {
		$whereSQL .= " OR username LIKE :u$i";
		$paramsarray[":u$i"] = $searchfor[$i];
	}
	$users = DB::query('SELECT users.username FROM users WHERE users.username LIKE :username '.$whereSQL.'', $paramsarray);
	if (count($users) != 0) {
		$usersFound = count($users);
		$loopcount = 0;
		if ($usersFound > 1) {
			echo $usersFound.' Users Found:';
		} else {
			echo $usersFound.' User Found:';
		}
		echo '<br><br>';
		echo '*click on the hyperlinked name to view the user*<br><br>';
		while ($loopcount < count($users)) {
			?>
			<a href="profile.php?username=<?php
			echo $users[$loopcount][0];?>?page=1"><?php
			print($users[$loopcount][0]);?></a>
			<?php
			echo '<br><br>';
			$loopcount++;
		}
	} else {
		echo '<br><br>';
		echo 'We could not locate any users matching your search criteria.<br><br>';
	}
	$whereSQL = "";//only one where is allowed in our statement so this is a workaround for that so we can have 2
	$paramsarray = array(':body'=>'%'.$_POST['searchbox'].'%');
	for ($i = 0; $i < count($searchfor); $i++) {
		if ($i % 2) {
			$whereSQL .= " OR body LIKE :p$i";
			$paramsarray[":p$i"] = $searchfor[$i];
		}
	}
	$posts = DB::query('SELECT posts.body FROM posts WHERE posts.body LIKE :body '.$whereSQL.'', $paramsarray);
	if (count($posts) != 0) {
		$postsFound = count($posts);
		$loopcount = 0;
		if ($postsFound > 1) {
			echo $postsFound.' Posts Found:';
		} else {
			echo $postsFound.' Post Found:';
		}
		echo '<br><br>';
		while ($loopcount < count($posts)) {
			$number = $loopcount + 1;
			echo 'Result '.$number.' :';
			echo '<br>';
			print($posts[$loopcount][0]);
			echo '<br>';
			$loopcount++;
		}
	} else {
		echo 'We could not match any posts containing your search criteria.<br><br>';
	}
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
}
?>

<?php echo "<br><br>"."<div align='center'>Please Use This Searchbox To Search For Users Or Posts:</div>"."<br>" ?>
<div align="center">
<form action="index.php" method="post">
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="text" name="searchbox" value="">
<input type="submit" name="search"  value="Search"><br><br><br><hr />
</form>
</div>

<?php
if (LoggedIn::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
	$followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, users.username FROM users, posts, followers WHERE posts.user_id = followers.user_id AND users.id = posts.user_id AND follower_id=:userid ORDER BY posts.likes DESC', array(':userid'=>$userid));
	echo 'Your Timeline: '.'<br><br>';
	foreach($followingposts as $post) {
		echo $post['body']." ~ ".$post['username'];
		echo "<form action='index.php?postid=".$post['id']."' method='post'>";
		if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
			echo "<input type='submit' name='like' value='Like'>";
		} else {
			echo "<input type='submit' name='unlike' value='Unlike'>";
		}
		echo "<span>".$post['likes']." likes</span><br>";
		Comment::displayComments($post['id']);
		echo "<form>
		<form action='index.php?postid=".$post['id']."' method='post'>
		<textarea name='commentbody' rows='3' cols='50'></textarea>
		<input type='submit' name='comment' value='Comment'>	
		</form>
		";
		echo "
		</br /><hr />";
	}
}
if (LoggedIn::IsLoggedIn()) {
?>

<br>
<div align="center">
<form action="index.php" method="post">
<input type="submit"  name="logoutbutton" value="Logout Button">
</form>
</div>

<?php
	if (isset($_POST['logoutbutton'])) {
		if (isset($_COOKIE['CID'])) {
			DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CID'])));
			echo 'Logged Out Successfully!';
		}
		setcookie('CID', '1', time()-3600);
		setcookie('CID_', '1', time()-3600);
		$secondsWait = 5;
		echo '<meta http-equiv="refresh" content="'.$secondsWait.'">';
	}
}
include('footer.php');
?>
