<?php



function custom_mail($to, $subject, $email_message)
{
	$mailFrom = 'sales@logic-valley.com';
	$from = 'info@logic-valley.com';
	$mailto = $to;

	$site_title = 'Logic Valley';


	$header   = array();
	$header[] = "MIME-Version: 1.0";
	$header[] = "Content-type: text/html; charset=iso-8859-1";
	$header[] = "From: $site_title  <" . $from . ">";
	$header[] = "Reply-To: $site_title  <" . $from . ">";
	$header[] = "Subject: {$subject}";
	$header[] = "X-Mailer: PHP/" . phpversion();

	$bool = 1;

	//mail($to,$subject,$message);

	if(!mail($to,$subject,$email_message, implode("\r\n", $header)))
	 {
	 	print_r(error_get_last());
	 	$bool=0;
	 }	


	/*$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Mailer = "smtp";


	$mail->SMTPDebug  = 0;
	$mail->SMTPAuth   = TRUE;
	$mail->SMTPSecure = "ssl";
	$mail->Port       = 465;
	$mail->Host       = "s9.fcomet.com";
	$mail->Username   = "$mailFrom";
	$mail->Password   = "A8szCeq8QieP";


	$mail->IsHTML(true);
	$mail->AddAddress("$mailto", "recipient-name");
	$mail->SetFrom("$mailFrom", "$site_title");
	// $mail->AddReplyTo("reply-to-email@domain", "reply-to-name");
	// $mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
	$mail->Subject = "$subject";
	$content = "$message";


	$mail->MsgHTML($content);
	if (!$mail->Send()) {
		echo "Error while sending Email.";
		// var_dump($mail);
		return $bool = 0;
	} else {
		// echo "Email sent successfully";
		return $bool;
	}*/
}
custom_mail('aasifkhattak45@gmail.com', 'test email', 'test email');