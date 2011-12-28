<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="/web_root/css/blueprint/screen.css" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="/web_root/css/blueprint/print.css" type="text/css" media="print"> 
  <!--[if lt IE 8]>
    <link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection">
  <![endif]-->
<title>Untitled Document</title>
</head>

<body style="background-color:#FFFFFF">
<div class="container">
<hr />
CONTENT
<hr />
<!-- START TEMPLATE CONTENT -->
<div class="prepend-8 append-8 last">
<div class="box">
   <form name = 'login' action = '{WWW_ROOT}public/login_process.php' method = 'POST'>
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" /> Login  </h4>
			<p><label for="username">Username:&nbsp;</label>
            <input type = 'text' name = 'username' value="{USERNAME}" />
            </p>
			<p>
			<label for="password">Password:&nbsp;</label>
            <input type = 'password' name = 'password' />
            </p>
			<div class="error {ERROR_CLASS}">{ERROR}</div>
			<p align="right">
			<a href="{WWW_ROOT}public/password_reset.php"><strong>Forgot your Password? &raquo;</strong></a>
			</p>
    <button type="submit" class="button positive right">
	  <img src="{WWW_ROOT}css/blueprint/plugins/buttons/icons/tick.png" alt=""/> Login
	</button>
			<p class="small">You must have cookies enabled in your browser.</p>
   </form>
</div>
</div>

<!-- END TEMPLATE CONTENT -->
<hr />
FORGOT_PASS
<hr />

<!-- START TEMPLATE FORGOT_PASS -->
<div class="prepend-8 append-8 last">
<div class="box">
   <form action="" method="POST" name="frmreset" id="frmreset">
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" />&nbsp;Password Recovery</h4>
			<div class="notice">
			 A new password will be generated for you and sent to the email address
associated with your account.
			</div>
			<p><label for="username">Email / Username:&nbsp;</label>
            <input name="username" type="text" id="username" tabindex="1" size="30" />
            </p>
			<div class="error {ERROR_CLASS}">{ERROR}</div>
    <button type="submit" class="button positive right">
	  <img src="{WWW_ROOT}css/blueprint/plugins/buttons/icons/tick.png" alt=""/> Recover your Password
	</button>
		<p>&nbsp;</p>

   </form>
</div>
</div>
<!-- END TEMPLATE FORGOT_PASS -->
<hr />
FORGOT_PASS_OK
<hr />
<!-- START TEMPLATE FORGOT_PASS_OK -->
<div class="prepend-8 append-8 last">
<div class="box">
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" />&nbsp;New Password Generated</h4>
			<div class="success">
			<p> Your new password has been generated and sent to the e-mail associated with your account.</p>
			<p>	You should receive the e-mail with your new credentials in a couple of minutes and proceed to login.</p>
			</div>
			  <a href="{WWW_ROOT}public/login.php">
				<img src="{WWW_ROOT}css/blueprint/plugins/buttons/icons/key.png" alt=""/> 
				Login Again &raquo;
			  </a>
</div>
</div>
<!-- END TEMPLATE FORGOT_PASS_OK -->  
<hr />
FORGOT_PASS_FAIL
<hr />
<!-- START TEMPLATE FORGOT_PASS_FAIL -->
<div class="prepend-8 append-8 last">
<div class="box">
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" />&nbsp;New Password Failure</h4>
			<div class="error">
			<p> There was an error sending you the e-mail with the new password, your password has not been changed.
			</p>
			<p><strong>Please contact the site administrator.</p>
			</div>
</div>
</div>
<!-- END TEMPLATE FORGOT_PASS_FAIL -->  
<hr />
USE_TOKEN
<hr />
<!-- START TEMPLATE USE_TOKEN -->
<div class="prepend-8 append-8 last">
<div class="box">
    <form name = 'use_token' action = '{WWW_ROOT}public/use_password_reset_token_pp.php' method = 'POST'>
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" />&nbsp;Recover Password</h4>
			<div class="notice">
			  Use the password Token you received in the e-mail you have received to change your password.
			</div>
			<p><label for="token">Reset Token:&nbsp;</label>
            <input type = 'text' name = 'token' value="{TOKEN}" />
            </p>
			<p>
			<label for="new_password">New Password:&nbsp;</label>
            <input type = 'password' name = 'new_password' />
            </p>
			<div class="error {ERROR_CLASS}">{ERROR}</div>
    <button type="submit" class="button positive right">
	  <img src="{WWW_ROOT}css/blueprint/plugins/buttons/icons/key.png" alt=""/> Change Password
	</button>
	<p>&nbsp;</p>
   </form>
</div>
</div>
<!-- END TEMPLATE USE_TOKEN -->
<hr />
LOGOUT
<hr />
<!-- START TEMPLATE LOGOUT -->
<div class="prepend-8 append-8 last">
<div class="box">
          <h4><img class="left" src="{WWW_ROOT}images/lock.gif" alt="LOCKED" width="29" height="31" />&nbsp;Session Ended</h4>
			<div class="success">
			<p>You have been logged out.</p>
			<p>Thanks for using {SITE_NAME}</p>
			</div>
			  <a href="{WWW_ROOT}public/login.php">
				<img src="{WWW_ROOT}css/blueprint/plugins/buttons/icons/key.png" alt=""/> 
				Login Again &raquo;
			  </a>
</div>
</div>
<!-- END TEMPLATE LOGOUT -->  

</div>

</body>
</html>
