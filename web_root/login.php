<?php
/**
 * login.php 
 * Show the login form and authenticate the user against JasperServer
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

require_once('config.php');
$_PageTitle = 'Login'; 

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'];
   	$password = $_POST['password'];
    $result = ws_checkUsername($username, $password);
    if (get_class($result) == 'SOAP_Fault') {
        $errorMessage = $result->getFault()->faultstring;
    } else {
        session_register("username");
        session_register("password");
		session_register("userlevel");
        $_SESSION["username"]=$username;
        $_SESSION["password"]=$password;
		$_SESSION["userlevel"]= USER;
        header("location: home.php");
        exit();
    }
}

$errorMessage = (!empty($errorMessage)) ? decorateError($errorMessage) : '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo $_SiteConfig['site']['title'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/screen.css" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/print.css" type="text/css" media="print"> 
  <!--[if lt IE 8]>
    <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/ie.css" type="text/css" media="screen, projection">
  <![endif]-->
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection" />  
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/tabs/screen.css" type="text/css" media="screen,projection">
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/buttons/screen.css" type="text/css" media="screen,projection">
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/themes/default/helper.css" media="screen" rel="stylesheet" type="text/css" media="screen, projection" />
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/dropdown.limited.css" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/themes/default/default.css" media="screen" rel="stylesheet" type="text/css" />
  <!--[if lt IE 7]>
   <style type="text/css" media="screen">
   body { behavior:url("/js/csshover.htc"); }
  </style>
  <![endif]-->
  <link href="<?php echo WWW_ROOT; ?>css/style.css" media="screen, projection"  rel="stylesheet" type="text/css" />

</head>
<body >
	<div class="container">
		<div id="header" class="span-24 last">
			<h1><a href="<?php echo WWW_ROOT; ?>" title="Home"><?php echo $_SiteConfig['site']['name'] ?></a></h1>								
		</div> 
        <div id="subheader" class="span-24 last">
          <h3 class="alt"><?php echo $_PageTitle ?></h3>
        </div>
		<div id="mainmenu" class="span-24 last">
			<ul id="nav" class="dropdown dropdown-horizontal">	
	    	<?php echo $_SiteConfig['guest_menu'] ?>
	    	</ul>			
		</div> 
		<div id="maincontent" class="span-24 last"> 
			<ul class="tabs">
			<?php echo $_PageTabs; ?>
			</ul> 
    	<h3>Welcome to the JasperServer sample (PHP version)</h3>
		<div class="prepend-8 append-8 last">
		<div class="box">
		   <form name = 'login' action = '' method = 'POST'>
		          <h4><img class="left" src="<?php echo WWW_ROOT; ?>images/lock.gif" alt="LOCKED" width="29" height="31" /> Login  </h4>
					<p>Type in a JasperServer username and password (i.e. jasperadmin/jasperadmin)</p>
					<p><label for="username">Username:&nbsp;</label>
		            <input type = 'text' name = 'username' value="" />
		            </p>
					<p>
					<label for="password">Password:&nbsp;</label>
		            <input type = 'password' name='password' />
		            </p>
					<?php echo $errorMessage; ?>

		    <button type="submit" class="button positive right">
			  <img src="<?php echo WWW_ROOT; ?>css/blueprint/plugins/buttons/icons/tick.png" alt=""/> Login
			</button>
					<p class="small">You must have cookies enabled in your browser.</p>
		   </form>
		</div>
		</div>
   
		</div>
		<div id="footer" class="span-16"> 
			<!-- Footer Links -->
		</div> 
		<div class="alt span-7 last">
			<a href="http://www.jaspersoft.com">Jaspersoft.com</a>
		</div>
</div>
    </body>
</html>
