<?php
/**
 * repoflex.php 
 * Send repo list to Flex app
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2012
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