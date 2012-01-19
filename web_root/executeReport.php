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
$reportUri = (isset($_POST['uri'])) ? htmlentities($_POST['uri']) : false;
$reportFormat = (isset($_POST['format'])) ? htmlentities($_POST['format']) : 'PDF';
$reportName = (isset($_POST['name'])) ? htmlentities($_POST['name']) : false;

if ($reportUri === false) {
	// invalid URI
	echo "Invalid Report";
	exit();
}

$_PageTitle = 'Welcome ' . $_SESSION["username"] ; 
$tabArray =  array();
$tabArray[99] = '<a href="#" class="active">Logged as: ' . $_SESSION["username"] . '</a>';
$_PageTabs = decoratePageTabs($tabArray, 99);

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
	$forceDownload = false;
	$filecount = 0;
	foreach ($reportMetadata->file as $reportFiles ) {
		$screen .= '<hr> Files: ' . $reportFiles . ' (' . $reportFiles['type']  . ') <br>';	
		$file[$filecount]['mime'] = (string) $reportFiles['type'];
		$file[$filecount]['request'] = 'report/' . $reportUUID . '?file=' . $reportFiles;
		//$reportfile = $WSRestRaw->get('report/' . $reportUUID . '?file=' . $reporFiles);
		$filecount++;
	}
	
	if (count($file) == 1 && $file[0]['mime'] != 'text/html') {
		// Just one file send it..
		$requestUrl = $file[0]['request'];
		try {
			$reportfile = $WSRestRaw->get($requestUrl);
			header('Content-Description: File Transfer');
		    header('Content-Type: ' . $file[0]['mime']);
		    header('Content-Disposition: attachment; filename = ' .$reportName . '.' .  end(explode('/', $file[0]['mime']))); //. basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    //header('Content-Length: ' . sizeof($reportfile));
		    ob_clean();
		    flush();
		    echo $reportfile;
		    exit;
		} catch (Exception $e) {
    		$screen .=  "Exception When requesting URL ". $requestUrl . ": <pre>" .  $e->getMessage() . "</pre>";
			$screen .=  "<hr><pre>" .  print_r($file, true). "</pre>";
		}

	} else {
		// More than one file tipicaly HTML request, just render the HTML
		$reportfile = $WSRestRaw->get('report/' . $reportUUID . '?file=report');
		$screen .= "<hr>" . $reportfile;
	}
	
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
   body { behavior:url("<?php echo WWW_ROOT; ?>js/csshover.htc"); }
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
	    	<?php echo $_SiteConfig['user_menu'] ?>
	    	</ul>			
		</div> 
		<div id="maincontent" class="span-24 last"> 
			<ul class="tabs">
			<?php echo $_PageTabs; ?>
			</ul> 
			<h3><?php echo $reportName; ?></h3>
			<form action="executeReport.php" method="POST">
     			<input type="hidden" name="uri" value="<?php echo $reportUri ?>">
     			<input type="hidden" name="name" value="<?php echo $reportName ?>">
			     Export format: <select name="format">
			         <option value="HTML">HTML</option>
			         <option value="PDF">PDF</option>
			         <option value="XLS">XLS</option>
			     </select>
			     
			     
			     
			     <input type="submit" value="Run the report">
		     </form>
			<?php echo $screen; ?>
   
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
