<?php
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/LoggedIn.php');
include('classes/Post.php');
include('classes/Image.php');
include('classes/Notify.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Camagru</title>
	</head>
<body>
<!-- Stream video via webcam -->
	<div class="video-wrap" align="center">
	    <video id="video" autoplay align="center"></video>
	</div>
	<!-- Trigger canvas web API -->
	<div class="controller" align="center">
	    <button id="snap" class="button is-primary" align="center">Capture</button>
	</div>
	<!-- Webcam video snapshot -->
<div align="center">
	<canvas  align="center" id="canvas" width="640" height="480"></canvas>
</div>
	<div align="center" style="margin-left: 5px; margin-top: 10px;">
	<div class="dropdown is-hoverable">
	  <div class="dropdown-trigger">
	    <button class="button is-primary" aria-haspopup="true" aria-controls="dropdown-menu4">
					<form action="#" method="post" enctype="multipart/form-data" align="center">
						<table align='center'>
	<td><input id='selfie' type='hidden' name='selfie' value='' style="margin-left: -106px;"/></td>
	<td><input id='submitSelfie' class='button is-primary' onClick="download()" type='submit' name='submitSelfie' value='Upload Post' style='margin-left: 3px; margin-top: 0px; width: 120px; height: 50%; font-size: 10px; align:center'/></td>
						</table>
					</form>
	</div>
	</div>
	</div>
		<script>
		const video = document.getElementById('video');
		const canvas = document.getElementById('canvas');
		const snap = document.getElementById("snap");
		const errorMsgElement = document.querySelector('span#errorMsg');
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
		  } catch (e) {
		    errorMsgElement.innerHTML = `navigator.getUserMedia error:${e.toString()}`;
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
		});
		function download(){
        	var image = document.getElementById('canvas').toDataURL("uploads/jpeg");
			document.getElementById('selfie').value = image;
    }
</script>
<?php
if (isset($_POST['submitSelfie'])) {
	$postid = Post::createImgPost($_POST['postbody'], LoggedIn::isLoggedIn(), $userid);
				Image::uploadImage('selfie', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
		}
?>
	</body>
</html>
