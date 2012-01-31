<?php
/**
 * login.php 
 * Show the login form and authenticate the user against JasperServer
 * I also use the JS Session ID sent from the REST Auth to set a cookie for the iframe integration
 * 
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

require_once('config.php');

$errorMessage = "";


	$WSRest = new Pest(JS_WS_URL);
	$WSRest->curl_opts[CURLOPT_HEADER] = true;
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
	        $result = true;
		} else {
			$errorMessage = "Unauthorized Code: " . $response['meta']['http_code'];
			$result = false;
		}
		
		
	} 
	catch (Exception $e) 
	{
	    $errorMessage =  "Unauthorized Exception: " .  $e->getMessage() . "<br>";
	    $result = false;
	}

//start outputting the XML
$output = "<loginsuccess>";
if(!$result) {
		$output .= "no";		
	}else{
		$output .= "yes";	
	}
$output .= "</loginsuccess>";
$output .= "<username>";
$output .= $_POST['username'];		
$output .= "</username>";
//output all the XML
print ($output);
?>