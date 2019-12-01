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
include('classes/Image.php');

if (LoggedIn::isLoggedIn()) {
	$userid = LoggedIn::isLoggedIn();
} else {
	die('Not Logged In');
}
include('my-accountheader.php');
if (isset($_POST['uploadprofileimg'])) {
	if (!isset($_POST['nocsrf'])) { //if there is no token in the form.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	if ($_POST['nocsrf'] != $_SESSION['token']) { //if there is some random value in the form and not our token value.
		die ("INVALID TOKEN!"); //this is to prevent cross site scripting CSRF attacks. Cookies are used for login verification.
	}
	Image::uploadImage('profileimg', "UPDATE users SET profileimg=:profileimg WHERE id=:userid", array(':userid'=>$userid));
	echo 'Your Profile Image Has Been Saved!';
	session_destroy(); //this is to prevent cross site scripting CSRF attacks. This is to destroy token.
}
?>

<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
Upload a profile image:
<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
<input type="file" name="profileimg">
<input type="submit" name="uploadprofileimg" value="Upload File Image"><br><br><hr /><br>
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
		$upload_dir = 'uploads/';
		$img = $_POST['img']; // Your data 'data:image/png;base64,AAAFBfj42Pj4';
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $newdata = mktime().'.jpeg';
        $file = $upload_dir.mktime().'.jpeg';
        file_put_contents($file, $data);
		DB::query("UPDATE users SET profileimg=:image WHERE id=:userid", array('image'=>$file, ':userid'=>$userid));
		echo 'Your profile image has been saved!';
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
