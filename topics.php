<?php
ini_set("display_errors", true);
include('classes/DB.php');
include('classes/LoggedIn.php');
include('classes/Post.php');
include('classes/Image.php');

if (isset($_GET['topic'])) {
	$variable = $_GET['topic'];
	if (DB::query("SELECT topics FROM posts WHERE topics LIKE '%#".$variable."%'", array())) {
		$posts = DB::query("SELECT * FROM posts WHERE topics LIKE '%#".$variable."%'", array());
		print_r($posts);
		foreach($posts as $post) {
			echo $post['body']."<br />";
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
