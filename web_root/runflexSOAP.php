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
 require_once('SOAPclient.php');
 
 session_start();

 // 1 Get the ReoportUnit ResourceDescriptor...
 $request = explode('.php',$_SERVER['REQUEST_URI']);
 $request = explode('?',$request[1]);

 $currentUri = $request[0]; // $_SERVER['QUERY_STRING'];

 $result = ws_get($currentUri);
 
 if (get_class($result) == 'SOAP_Fault')
 {
 	$errorMessage = $result->getFault()->faultstring;
 	echo "<error>SOAP FAULT</error>\n";
 	echo "<message>" . $errorMessage . "</message>\n";
 	exit();
 } else {
 	$folders = getResourceDescriptors($result);
 }
 
 if (count($folders) != 1 || $folders[0]['type'] != 'reportUnit')
 {
 	 echo "<error>Invalid RU ($currentUri)</error>";
 	 echo "<message>$result</message>"; 
 	 exit(); 
 }

 $reportUnit = $folders[0];
  
 $report_params = array();
 $multiselect_params = array();
 $output_params = array();
 
 $output_params[RUN_OUTPUT_FORMAT] = 'SWF';

 $result = ws_runReport($currentUri, $report_params,  $output_params, $attachments, $multiselect_params);

/*
 echo "<br><pre>";
 $urls = explode("&",$_SERVER['QUERY_STRING']);
 echo print_r($urls);
 echo print_r($report_params,true);
 echo "</pre>"; 
 echo "<hr><pre>";
 echo htmlentities($result);
 echo "</pre>";
 die(); 
*/
 
// 4. 
if (get_class($result) == 'SOAP_Fault')
 {
 	$errorMessage = $result->getFault()->faultstring;
 	echo "<error>SOAP FAULT</error>\n";
 	echo "<message>" . $errorMessage . "</message>\n";
 	exit();
 }
 
 
$operationResult = getOperationResult($result);
 
 if ($operationResult['returnCode'] != '0')
 {
  	echo "<error>Error executing the report</error>\n";
 	echo "<message>" . $operationResult['returnMessage'] . "</message>\n";	
 	exit();
 }
 
 if (is_array($attachments))
 {
	header ( 'Content-type: text/plain' );
	//header ( 'Content-Disposition: attachment; filename="report.jrpxml"');
	echo( $attachments["cid:report"]);
	exit(); 	
 } else {
 	echo "<notice>SOAP FAULT</notice>\n";
 	echo "<message>No Attachment Found</message>\n";
 }
?>
?>