<?php
/**
 * runflex.php 
 * Send JRPXML file to Flex app
 *
 *
 * @copyright Copyright (c) 2012
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

if($_SESSION['userlevel'] < USER) {
	// Guest, please login.
	header('Location: ' . WWW_ROOT . 'login.php');
	exit();
} 


//  Get the ReoportUnit 
$request = explode('.php',$_SERVER['REQUEST_URI']);
$request = explode('?',$request[1]);

$reportUri = $request[0]; // $_SERVER['QUERY_STRING'];
$reportFormat = 'SWF';
$reportName = 'FlexReport';

if ($reportUri === false) {
	// invalid URI
	echo "Invalid Report";
	exit();
}

$report_params = array();
$reportListParams = array();

// See if we have parameters
foreach ($_POST as $key => $value) {
	if (strncmp("PARAM_S_", $key,8) == 0) {
		if (!empty($value)) {
			$report_params[substr($key,8)] = $value;
		}
	}
	if (strncmp("PARAM_M_", $key,8) == 0) {
		$reportListParams[substr($key,8)] = $value;
	}
}

$WSRest = new PestXML(JS_WS_URL);
// Set auth Header
$WSRest->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;

try {
	// Prepare the resourceDescriptor
	$data = '<resourceDescriptor name="' . $reportName . '" wsType="reportUnit" uriString="' . $reportUri . '" isNew="false">' . "\n";	    
	if (!empty($report_params)) {
		foreach ($report_params as $name => $value) { 	
			$data .='<parameter name="' . $name . '" ><![CDATA[' . $value . ']]></parameter>' . "\n";
		}
	}

	if (!empty($reportListParams)) {
		foreach ($reportListParams as $name => $value) { // isListItem=\"true\" 	
			foreach ($value as $itemnum => $item) {
				$data .='<parameter name="' . $name . '" isListItem="true" ><![CDATA[' . $item . ']]></parameter>' . "\n";
			}
			
		}
	}	
	$data .= '</resourceDescriptor>';
	// echo print_r($_POST, true) . "<hr>" . htmlentities($data); die();
	$reportMetadata = $WSRest->put('report' . $reportUri . '?RUN_OUTPUT_FORMAT=' . $reportFormat , $data);

	$reportUUID = $reportMetadata->uuid;
	$WSRestRaw = new Pest(JS_WS_URL);
	$WSRestRaw->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;

	$file = array();
	$filecount = 0;
	foreach ($reportMetadata->file as $reportFiles ) {
		$file[$filecount]['mime'] = (string) $reportFiles['type'];
		$file[$filecount]['request'] = 'report/' . $reportUUID . '?file=' . $reportFiles;
		$filecount++;
	}
	
	if (count($file) == 1 && $file[0]['mime'] != 'text/html') {
		// Just one file send it..
		$requestUrl = $file[0]['request'];
		try {
			$reportfile = $WSRestRaw->get($requestUrl);

		} catch (Exception $e) {
    		$screen .=  "Exception When requesting URL ". $requestUrl . ": <pre>" .  $e->getMessage() . "</pre>";
			$screen .=  "<hr><pre>" .  print_r($file, true). "</pre>";
			echo $screen;
		}

	} else {
		// More than one file tipicaly HTML request, just render the HTML
		$reportfile = $WSRestRaw->get('report/' . $reportUUID . '?file=report');
		
	}
   
	// This replacement is to fix the display of fusion charts inside the Flash Viewer
	$reportfile = str_replace("http://localhost:8080/jasperserver-pro/fusion", "/fusion", $reportfile);
	
	header('Content-Type:  text/plain');
    ob_clean();
    flush();
    echo $reportfile;
    exit;
} 
catch (Pest_Unauthorized $e) {
	// Check for a 401 (login timed out)
	$WSRest = new Pest(JS_WS_URL);	
	$WSRest->curl_opts[CURLOPT_HEADER] = true;
	$restData = array(
	  'j_username' => $_SESSION['username'],
	  'j_password' => $_SESSION['password']
	);
	
    try {		    
		$body = $WSRest->post('login', $restData);
		$response = $WSRest->last_response;
		if ($response['meta']['http_code'] == '200') {
			// Respose code 200 -> All OK
			// Extract the Cookie for further requests.
			preg_match('/^Set-Cookie: (.*?)$/sm', $body, $cookie);
			//Cookie: $Version=0; JSESSIONID=52E79BCEE51381DF32637EC69AD698AE; $Path=/jasperserver
			$_SESSION["JSCookie"] = '$Version=0; ' . str_replace('Path', '$Path', $cookie[1]);
			// Reload this page.
	        header("location: home.php");
	        exit();
		} else {
			header("location: logout.php");
			exit();
		}
	} 
	catch (Exception $e) {
	   	header("location: logout.php");
		exit();
	}
}
catch (Exception $e) 
{
    $screen .=  "Exception: <pre>" .  $e->getMessage() . "</pre>";
}
//$currentReport = '';
//$screen .= htmlentities(print_r($resources, true));
?>