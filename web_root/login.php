<?php
/**
 * login.php 
 * Authenticate the user against JasperServer (hardcoded in this sample)
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


$errorMessage = "";

// Initialize the REST Class
$WSRest = new Pest(JS_WS_URL);
$WSRest->curl_opts[CURLOPT_HEADER] = true;

// Set the Jasperserver username and password to authenticate
// You can change this values to any valid JRS username and password
$myuserdata['username'] = 'jasperadmin'; // Or 'username|organization_id' for multitenant
$myuserdata['username'] = 'jasperadmin|organization_1';
$myuserdata['password'] = 'jasperadmin'; 

$restData = array(
  'j_username' => $myuserdata['username'],
  'j_password' => $myuserdata['password']
);

try 
{		    
	$body = $WSRest->post('login', $restData);  // Call JRS Web Services to authenticate the user
	$response = $WSRest->last_response;
	if ($response['meta']['http_code'] == '200') {
		// Respose code 200 -> All OK - See JasperServer WebServices Guide for a list of response codes
		
		// Register my own session values for later use
		session_register("username");
        session_register("password");
		session_register("userlevel");
        $_SESSION["username"]= $_POST['username'];
        $_SESSION["password"]= $_POST['password'];
		$_SESSION["userlevel"]= 'USER';
		
		/**
		 * The Rest API will send a cookie with the JasperServer JSession Identifier
		 * E.G.: Cookie: JSESSIONID=52E79BCEE51381DF32637EC69AD698AE; $Path=/jasperserver
		 * For the ifram to be able to skip the JRS login, we will extract that session identifier
		 * and place a Cookie on the user end where JRS expect that to be found.
		 */ 
				
		// Grab the JS Session ID and set the cookie in the right path so 
		// when I present an iFrame I can share be authenticated
		// /!\ IMPORTANT: For this to work JRS and this App have to run in the same domain 
		
		// Extract the JSESSIONID from the Rest response body.
		preg_match('/^Set-Cookie: (.*?)$/sm', $body, $cookie);
		preg_match('/=(.*?);/' , $cookie[1], $cookievalue);
		$expire_time = time() + (3600 * 3); // Match this expiration with your session and JRS session
		setcookie('JSESSIONID', $cookievalue[1], $expire_time, "/jasperserver-pro");
		
		// Redirect the user to the Iframe
        header("location: iframe.php");
        exit();
	} else {
		$errorMessage = "Unauthorized Code: " . $response['meta']['http_code'];
	}
	
	
} 
catch (Exception $e) 
{
    $errorMessage =  "Unauthorized Exception: " .  $e->getMessage() . "<br>";
}



$errorMessage = (!empty($errorMessage)) ? decorateError($errorMessage) : '';

// All the HTML is down below
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
					<p>
					 ERROR: </p> 
					 <p>
					<?php echo $errorMessage; ?>
					</p>

					<p class="small">Check login.php for a valid set of credentials and config.php to make sure that the paths are correct.</p>
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
