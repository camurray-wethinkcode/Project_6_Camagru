<?php
class Image {
	public static function uploadImage($formname, $query, $params) {
		$file = $_FILES[$formname];
		$fileName = $_FILES[$formname]['name'];
		$fileTmpName = $_FILES[$formname]['tmp_name'];
		$fileSize = $_FILES[$formname]['size'];
		$fileError = $_FILES[$formname]['error'];
		$fileType = $_FILES[$formname]['type'];
		$fileExt = explode('.', $fileName);
		$fileActualExt = strtolower(end($fileExt));
		$allowed = array('jpg', 'jpeg', 'png', 'gif');
		if (in_array($fileActualExt, $allowed)) {
			if ($fileError === 0) {
				if ($fileSize < 1000000) {
					$fileNameNew = uniqid('', true).".".$fileActualExt;
					$fileDestination = 'uploads/'.$fileNameNew;
					move_uploaded_file($fileTmpName, $fileDestination);
					$preparams = array($formname=>'/'.$fileDestination);
					$params = $preparams + $params;
					DB::query($query, $params);
					//header("Location: my-account.php?uploadsuccess");
				} else {
					echo 'File Upload Limit of 1MB Exceeded!';
				}
			} else {
				echo 'An Error Occured, Please Re-upload File!';
			}
		} else {
			echo 'Please Upload .JPG/.JPEG/.PNG/.GIF Files Only!';
		}
	}
}
?>
