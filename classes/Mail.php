<?php
ini_set("display_errors", true);
require_once('PHPMailer/PHPMailerAutoload.php');
class Mail {
	public static function sendMail($subject, $body, $address) {
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
//		$mail->Host = 'smtp.gmail.com';
//		$mail->Port = '465';
		$mail->Host = 'ssl://smtp.gmail.com:465';
		$mail->isHTML();
		$mail->Username = 'murraylydie@gmail.com';
		$mail->Password = 'Mwawada666#';
		$mail->SetFrom('murraylydie@gmail.com');
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($address);
		$mail->Send();
	}
}
?>
