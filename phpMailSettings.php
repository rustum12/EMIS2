	<?php
			// include PHPMailer files (no Composer needed)
			require 'PHPMailer/src/Exception.php';
			require 'PHPMailer/src/PHPMailer.php';
			require 'PHPMailer/src/SMTP.php';
			$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
			
			try {
				// Server settings
				$mail->isSMTP();
				$mail->Host       = '';   // Gmail SMTP server
				$mail->SMTPAuth   = true;
				$mail->Username   = 'ra75277@gmail.com';  // your Gmail address
				$mail->Password   = 'dvoc wlcs etui wqup';    // Gmail App Password
				$mail->SMTPSecure = 'tls'; 
				$mail->Port       = 587;
				$server	=	'http://localhost/lms/';
				// Recipients
				$mail->setFrom($email, 'LMS System');
				$mail->addAddress($email); // the email entered in forgot password form
				$forgotPassword = isset($forgotPassword) && $forgotPassword ? $forgotPassword : false;
				$signUP = isset($signUP) && $signUP ? $signUP : false;
				if($forgotPassword){
				// Content
				$mail->isHTML(true);
				$mail->Subject = 'Password Reset Request';
				$mail->Body    = "Click <a href='{$server}reset_password.php?token=$token'>here</a> to reset your password.";
				$mail->send();
				 $message = "Password reset link sent to your email.";
				}
				elseif($signUP){
				// Content
				$mail->isHTML(true);
				$mail->Subject = 'Verify Email';
				$mail->Body    = "Click <a href='{$server}verify.php?token=$token'>here</a> to reset your password.";
				$mail->send();
				 $message = "Email Verification link sent to your email.";
				}
				
				
			
			} catch (Exception $e) {
			   $message = "Email could not be sent. Error: {$mail->ErrorInfo}";
			}
		
		
?>