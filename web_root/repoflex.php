<?php
/**
 * repoflex.php 
 * Send repo list to Flex app
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

$root = ($_POST['root'] != '') ? htmlentities($_POST['root']) : '/';
$message = '';
$WSRest = new PestXML(JS_WS_URL);
// Set auth Header
$WSRest->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;

foreach (explode('/', $root) as $key => $items) {
	$tempArray[] = $items;
	if ($item == '' and $key == 0) {
		$currentPathArray[] = 'Repository';
	} else {
		$currentPathArray[] = ucfirst($items);
		/* link = 'root=' . implode("/", $tempArray) */
	}
}
$currentPath = implode(" &raquo; ", $currentPathArray);

try 
{		    
	$resources = $WSRest->get('resources' . $root);
	//$response = $pest->post('login', $restData);
	
	//$screen .= "\n" . print_r($WSRest->last_response, true);
	$screen = '<repository>' . "\n";
	foreach ($resources->resourceDescriptor as $contents) {
		$screen .= '<item>' . "\n";
		$screen .= '<label>' . $contents->label . '</label>' . "\n";
		$screen .= '<type>' . $contents['wsType'] . '</type>' . "\n";
		$screen .= '<uri>' . $contents['uriString'] . '</uri>' . "\n";
		$screen .= '</item>' . "\n";
		
	}
	$screen .= '</repository>' . "\n";
	$message = "Success";
} 
catch (Pest_Unauthorized $e) {
	// Check for a 401 (login timed out)	
	$message = "Session Ended";
}
catch (Exception $e) 
{
    $message = $e->getMessage();
}

//$screen .= htmlentities(print_r($resources, true));
//start outputting the XML
$output = "<cookie>\n";
$output .= $_SESSION['JSCookie'];
$output .= "</cookie>\n";
$output .= "<currentpath>\n";
$output .= $currentPath;
$output .= "</currentpath>\n";

$output .= "<error>\n";
$output .= "None";
$output .= "</error>\n";
$output .= "<message>\n";
$output .= $message;
$output .= "</message>\n";
$output = $screen;
echo $output;
?>