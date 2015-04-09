<?PHP

		require_once('class.phpmailer.php');

function mailReset($username, $email, $tokenHash) {
 $mail             = new PHPMailer(); // defaults to using php "mail()"

 $mail->IsSendmail(); // telling the class to use SendMail transport

 $body             = 'Someone has requested a password reset for the JokeSwaps.com account "' . $username . '", which is associated with your email address, if it was not you no action is required and you can ignore this email, the account will remain secure.' . 
 ' <BR />If you did request a password reset, please confirm by clicking <a href="http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . '">here</a>' . 
 ' or copy and pasting the following link into your browser: ' .
 'http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . 
 ' <BR /><BR />From the Friendly JokeSwaps Robot';

 $mail->AddReplyTo("admin@jokeswaps.com","JokeSwaps");

 $mail->SetFrom('robot@jokeswaps.com', 'JokeSwaps Robot');

 $mail->AddAddress($email, "JokeSwaps Parent");

 $mail->Subject    = "Password reset";

 $mail->AltBody    = 'Someone has requested a password reset for the JokeSwaps.com account "' . $username . '", which is associated with your email address, if it was not you no action is required and you can ignore this email, the account will remain secure.' . 
 '\r\nIf you did request a password reset, please confirm by copy and pasting the following link into your browser: ' .
 'http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . 
 '\r\n\r\nFrom the Friendly JokeSwaps Robot';

 $mail->MsgHTML($body);

 if(!$mail->Send()) {
   echo "Mailer Error: " . $mail->ErrorInfo;
 } else {
   echo "Message sent!";
 }
	
}

function mailVerify($username, $email, $tokenHash)
	
{
	$mail             = new PHPMailer(); // defaults to using php "mail()"

	$mail->IsSendmail(); // telling the class to use SendMail transport

	$body             = 'Someone has signed up to JokeSwaps.com using your email address, if it was not you no action is required and you can ignore this email.' . 
	' <BR /><BR />If you did sign up to JokeSwaps.com, please confirm your email address by clicking <a href="http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . '">here</a>' . 
	' or copy and pasting the following link into your browser: ' .
	'http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . 
	' <BR /><BR />From the Friendly JokeSwaps Robot';

	$mail->AddReplyTo("admin@jokeswaps.com","JokeSwaps");

	$mail->SetFrom('robot@jokeswaps.com', 'JokeSwaps Robot');

	$mail->AddAddress($email, "JokeSwaps Parent");

	$mail->Subject    = "Please confirm your email address";

	$mail->AltBody    = 'Someone has signed up to JokeSwaps.com using your email address, if it was not you no action is required and you can ignore this email.' . 
	'\r\nIf you did sign up to JokeSwaps.com, please confirm your email address by copy and pasting the following link into your browser: ' .
	'http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . 
	'\r\n\r\nFrom the Friendly JokeSwaps Robot';

	$mail->MsgHTML($body);

	if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  echo "Message sent!";
	}
	
}



?>