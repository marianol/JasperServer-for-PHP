<?php
/**
 * login.php 
 * Show the login form and authenticate the user against JasperServer
 * I also use the JS Session ID sent from the REST Auth to set a cookie for the iframe integration
 * 
 *
 * @copyright Copyright (c) 2011
 * @author Mariano Luna
 * 
LICENSE AND COPYRIGHT NOTIFICATION
==================================

The Proof of Concept deliverable(s) are (c) 2011 Jaspersoft Corporation - All rights reserved. 
Jaspersoft grants to you a non-exclusive, non-transferable, royalty-free license to use the deliverable(s) pursuant to 
the applicable evaluation license agreement (or, if you later purchase a subscription, the applicable subscription 
agreement) relating to the Jaspersoft software product at issue. 

The Jaspersoft Sales department provides the Proof of Concept deliverable(s) "AS IS" and WITHOUT A WARRANTY OF ANY KIND. 
It is not covered by any Jaspersoft Support agreement or included in any Professional Services offering. 
At the discretion of the head of the Jaspersoft Professional Services team, support, maintenance and enhancements may be 
available for such deliverable(s) as "Time for Hire": http://www.jaspersoft.com/time-for-hire.

 */

require_once('config.php');
$_PageTitle = 'Login'; 

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$WSRest = new Pest(JS_WS_URL);
	$WSRest->curl_opts[CURLOPT_HEADER] = true;
	$myuserdata = explode("|", $_POST['username']);
	if ($_POST['username'] == 'jasperadmin') {
		$_POST['username'] = 'jasperadmin|organization_1';
	}
	$restData = array(
	  'j_username' => $_POST['username'],
	  'j_password' => $_POST['password']
	);
	
	try 
	{		    
		$body = $WSRest->post('login', $restData);
		$response = $WSRest->last_response;
		if ($response['meta']['http_code'] == '200') {
			// Respose code 200 -> All OK
			session_register("username");
	        session_register("password");
			session_register("userlevel");
	        $_SESSION["username"]= $_POST['username'];
	        $_SESSION["password"]= $_POST['password'];
			$_SESSION["userlevel"]= USER;
			
			//Cookie: JSESSIONID=52E79BCEE51381DF32637EC69AD698AE; $Path=/jasperserver
			// Extract the Cookie and save the string in my session for further requests.
			preg_match('/^Set-Cookie: (.*?)$/sm', $body, $cookie);
			$_SESSION["JSCookie"] = '$Version=0; ' . str_replace('Path', '$Path', $cookie[1]);
			
			// Grab the JS Session ID and set the cookie in the right path so 
			// when I present an iFrame I can share be authenticated
			// For this to work JS and the App have to run in the same domain 
			preg_match('/=(.*?);/' , $cookie[1], $cookievalue);
			setcookie('JSESSIONID', $cookievalue[1], time() + (3600 * 3), "/jasperserver-pro");
	        header("location: home.php");
	        exit();
		} else {
			$errorMessage = "Unauthorized Code: " . $response['meta']['http_code'];
		}
		
		
	} 
	catch (Exception $e) 
	{
	    $errorMessage =  "Unauthorized Exception: " .  $e->getMessage() . "<br>";
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
    	<h3>Welcome to MyReport</h3>
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
