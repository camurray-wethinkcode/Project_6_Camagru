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
if (!LoggedIn::isLoggedIn())
	die('Not logged in!');
include('classes/Post.php');
include('classes/Image.php');
include_once('classes/Notify.php');
if (!isset($_GET['username'])) {
	if (!strstr($_GET['username'], 'page'))
		die ('User Not Found!');
}
include('profileheader.php');
$username = "";
$verified = False;
$isFollowing = False;
if (isset($_GET['username'])) {
	$username = $_GET['username'];
	if (strstr($username, "page")) {
	if (substr($_GET['username'], -2, 1) == '=')
		$i = substr($_GET['username'],-1);
	else
		$i = substr($_GET['username'], -2);
	$me = chop($_GET['username'], "page=".$i);
	$username = substr($me, 0, -1);
	}
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))[0]['username'];
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
		$verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$username))[0]['verified'];
		$followerid = LoggedIn::isLoggedIn();
		if (isset($_POST['follow'])) {
			if ($userid != $followerid) {
				if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
					if ($followerid == 1) {
						DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
					}
					DB::query('INSERT INTO followers VALUES (NULL, :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
				} else {
					echo 'Already Following User!';
				}
				$isFollowing = True;
			}
		}
		if (isset($_POST['unfollow'])) {
			if ($userid != $followerid) {
				if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
					if ($followerid == 3) {
						DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
					}
					DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
				}
				$isFollowing = False;
			}
		}
		if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
			$isFollowing = True;
		}
		if (isset($_POST['deletepost'])) {
			if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
				DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
				DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
				echo 'Post Deleted!';
			}
		}
		if (isset($_POST['post'])) {
			if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
				die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
			}
			if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
				die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
			}
			if ($_FILES['postimg']['size'] == 0) {
				Post::createPost($_POST['postbody'], LoggedIn::isLoggedIn(), $userid);
			} else {
				$postid = Post::createImgPost($_POST['postbody'], LoggedIn::isLoggedIn(), $userid);
				Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
			}
			session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
		}
		if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
			Post::likePost($_GET['postid'], $followerid);
		}
		$posts = Post::displayPosts($userid, $username, $followerid);
	} else {
		die('User Not Found!');
	}
}
?>

<h1><?php echo $username; ?>'s Profile <?php if ($verified) { echo ' - Verified'; }  ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
<?php
if ($userid != $followerid) {
	if ($isFollowing) {
		echo '<input type="submit" name="unfollow" value="Unfollow">';
	} else {
		echo '<input type="submit" name="follow" value="Follow">';
	}
}
?>
</form>

<form action="profile.php?username=<?php echo $username; ?>?page=1" method="post" enctype="multipart/form-data">
<textarea name="postbody" rows="8" cols="80"></textarea>
<br />Upload an image:
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="file" name="postimg">
<input type="submit" name="post" value="Post"><br><br><br>
<input type="submit" name="usewebcam" value="Take a Picture with Webcam"><br><br><hr /><br>
</form>

<?php
if (isset($_POST['usewebcam'])) {
?>
	<!-- Stream video via webcam -->
	<div class="video-wrap" align="center">
	<video id="video" align="center" autoplay></video>
	</div>
	<!-- Trigger canvas web API -->
	<div class="controller" align="center"><br><br>
	<button id="snap" align="center">Capture</button><br><br>
	</div>
	<!-- Webcam video snapshot -->
	<form action="" align="center" method="post">
	<input type="hidden" id="image" name="img">
	<br><br>
	<button onclick="save()" id="submit" name="upload_1">Upload Webcam Image</button><br><br>
	</form>
	<div class "row">
	<div class="column" align="right">
	<canvas align="right" id="canvas" width="640" height="480"></canvas>
	</div>
	<div id="side" class="column" align="left">
	<ul align="left">
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker1.jpeg')" src="images/sticker1.jpeg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker2.jpg')" src="images/sticker2.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker3.jpg')" src="images/sticker3.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker4.jpg')" src="images/sticker4.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker5.jpg')" src="images/sticker5.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker6.jpg')" src="images/sticker6.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker7.jpg')" src="images/sticker7.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker8.jpg')" src="images/sticker8.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker9.jpg')" src="images/sticker9.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker10.jpg')" src="images/sticker10.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker11.jpg')" src="images/sticker11.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker12.jpeg')" src="images/sticker12.jpeg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker13.jpg')" src="images/sticker13.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker14.jpg')" src="images/sticker14.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker15.jpg')" src="images/sticker15.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker16.jpg')" src="images/sticker16.jpg"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker17.png')" src="images/sticker17.png"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker18.png')" src="images/sticker18.png"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker19.png')" src="images/sticker19.png"></label></li>
	<li><label><img style="width: 100px;" onclick="merge(300, 120, './images/sticker20.jpg')" src="images/sticker20.jpg"></label></li>
	</ul>
	</div>
	</div>
	<script>
	'use strict';
	const video = document.getElementById('video');
	const canvas = document.getElementById('canvas');
	const snap = document.getElementById('snap');
	const errorMsgElement = document.getElementById('span#ErrorMsg');
	const constraints = {
		audio: false,
		video: {
			width: 640, height: 480
		}
	};
	// Access webcam
	async function init() {
	try {
		const stream = await navigator.mediaDevices.getUserMedia(constraints);
		handleSuccess(stream);
	}
	catch (e) {
		errorMsgElement.innerHTML = `navigator.getUserMedia.error:$({e.toString()})`;
		}
	}
	// Success
	function handleSuccess(stream) {
		window.stream = stream;
		video.srcObject = stream;
	}
	// Load init
	init();
	// Draw image
	var context = canvas.getContext('2d');
	snap.addEventListener("click", function() {
		context.drawImage(video, 0, 0, 640, 480);
		console.log(photo.value);
	});
	const photo = document.getElementById('image');
	function merge(x, y, img){
		var new_img = new Image();
		new_img.onload = function() {
			context.drawImage(new_img, x, y, 200, 300);
		}
		new_img.src = img;
	}
	function save() {
		photo.value = canvas.toDataURL();
	}
	</script>
<?php
}
	if (isset($_POST['upload_1'])) {
		$img = $_POST['img']; // Your data 'data:image/png;base64,AAAFBfj42Pj4';
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $newdata = mktime().'.jpeg';
        $file = 'uploads/'.mktime().'.jpeg';
		file_put_contents($file, $data);
		$file = '/uploads/'.mktime().'.jpeg';
		$text = "selfie";
		DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0, :file, NULL)', array(':postbody'=>$text, ':userid'=>$userid, ':file'=>$file));
		echo 'Your post selfie has been saved!'."<br><br>";
	}
?>

<div class="posts">
<?php
echo $posts;
?>
</div>

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
