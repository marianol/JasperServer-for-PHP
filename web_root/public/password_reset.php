<?php
/**
 * guest/password_reset.php Allow user to recover a lost password.
 *
 * post to guest/password_reset.php
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

$message = '';

if (isset($_POST['username'])) {
	// Form Submitted Generate Pass Token

	$login =& get_login_manager();
	$email = htmlentities($_POST['username']);
	try {
		$token = $login->generatePasswordResetToken($_POST['username']);
		//echo $token;
	}
	catch (PNP_Login_No_User_Found_Exception $e) {
			// on invalid username or password
			$message = 'Invalid E-mail Address';
	}
	catch (PNP_Login_Invalid_Input_Exception $e) {
			$message = 'Invalid E-mail Address';
	}
	catch (PNP_Login_Back_Off_Exception $e) {
			// on invalid username or password
			$message = 'Too many consecutive attempts, please retry later'; //$e->getMessage();
	}

	if ($message == '') {
		//echo $token;
		// Mailer example
		include_once('PHPMailer/class.phpmailer.php');
		$tokenlink = $_SiteConfig['site']['url'] . '/public/use_password_reset_token.php?tkn=' . $token ;
		$mail             = new PHPMailer();

		$body             = 'Welcome ' . $username . ', <br>
							<p>
							You have requested a new ' . $_SiteConfig['site']['name'] . ' account password.
							</p>
							<p>Click in the following link to change your password: ' . $tokenlink . '</p>
							<p>Account information:<br>
							Username: <strong>' . $email . '</strong><br>
							Password Token: <strong>' . $token . '</strong><br>
							</p>
							<p>
							Thanks,
							</p>';

		//$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->Host       = "mail.worxteam.com"; // SMTP server
		$mail->IsHTML(true);
		$mail->From       = $_SiteConfig['site']['email'];
		$mail->FromName   = $_SiteConfig['site']['name'];

		$mail->Subject    = "Password Recovery from " . $_SiteConfig['site']['name'] ;
		$mail->Mailer	  = 'mail';
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

		$mail->MsgHTML($body);

		$mail->AddAddress( $email, "");
		$mail->AddBCC($_SiteConfig['site']['Administrator'], 'Administrator');
		// $mail->AddAttachment("images/phpmailer.gif");             // attachment
		echo $mail->ErrorInfo;
		if(!$mail->Send()) {
		  $message = "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  $message = "Message sent!";
		}
		
	} 
}

// Show Pass reset form
$template = array('content' => 'login.tpl#FORGOT_PASS');

$TPL = masterTemplate($template, true);

$TPL->assign("ERROR", $message);
if (empty($message)) {
		$TPL->assign("ERROR_CLASS", 'hide');
}

// We need to fill the previously entered username on error
$TPL->assign("USERNAME", '');

template_output($TPL);


?>