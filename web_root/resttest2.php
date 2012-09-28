<?php
/**
 * executeReport.php Execute a report unit and diaplay output
 * 
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

if($_SESSION['userlevel'] < USER) {
	// Guest, please login.
	header('Location: ' . WWW_ROOT . 'login.php');
	exit();
} 

// Get the parameters
$reportUri = "/public/resttest";
$reportFormat = 'CSV';
$reportName = (isset($_POST['name'])) ? htmlentities($_POST['name']) : false;


$report_params = array();
$outputReports = array();

for ($reportnumber=0; $reportnumber < 25 ; $reportnumber++ ) { 
	$screen ='';

	$report_params['input'] = $reportnumber;

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
	$data .= '</resourceDescriptor>';
	// echo htmlentities($data) . "<hr>";
	
	$reportMetadata = $WSRest->put('report' . $reportUri . '?RUN_OUTPUT_FORMAT=' . $reportFormat , $data);
    $UUID = $reportMetadata->uuid;
    
    $outputReports[$reportnumber] = $UUID;
    } catch (Exception $e) 
        {
            $screen .=  "Exception: <pre>" .  $e->getMessage() . "</pre>";
        }



	
    try {    
    	//echo $reportnumber . " - " . $reportUUID . "<hr>";
    	$WSRestRaw = new Pest(JS_WS_URL);
    	$WSRestRaw->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;
    
    	// More than one file tipicaly HTML request, just render the HTML
    	$requestReport = 'report/' . $UUID . '?file=report';
    	$reportfile = $WSRestRaw->get($requestReport);  	
        $status = ($reportnumber == $reportfile) ? 'PASS' : 'FAIL';
    	$screen .=  "$reportnumber -- $reportfile --- $status - $requestReport <br />";
    	//$screen .= htmlentities($attachfile) . "<hr>";
    	
    } 
    catch (Exception $e) 
    {
        $screen .=  "Exception: <pre>" .  $e->getMessage() . "</pre>";
    }


echo $screen; 
ob_flush();
}
?>