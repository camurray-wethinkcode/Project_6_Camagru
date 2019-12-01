<?php
ini_set("display_errors", true);
include('../classes/DB.php');
if (file_exists('databasesetup.sql')) {
	try {
		$pdo = new PDO('mysql:host=localhost;dbname=socialnetwork;charset=utf8', 'root', 'mWAWADA666');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->exec("CREATE DATABASE IF NOT EXISTS socialnetwork; USE socialnetwork;".file_get_contents("databasesetup.sql"));
		echo "Installed.";
	}
	catch (PDOException $e) {
		exit ($e->getMessage());
	}
}
include('footer.php');
?>
